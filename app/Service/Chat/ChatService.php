<?php

namespace App\Service\Chat;

use App\Enums\Cache\CacheKey;
use App\Enums\Chat\ChatRole;
use App\Enums\Chat\DialogType;
use App\Exceptions\Chat\ChatMessageException;
use App\Repository\Chat\ChatRepository;
use App\User;
use Carbon\Carbon;
use App\Models\Chat\MessagesModel;
use App\Traits\ArrayHelper;
use App\Models\Chat\DialogModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Service\NotificationsService;
use App\DTO\Chat\PrivateChatDTO;
use Illuminate\Support\Facades\{Cache, Gate, DB, Auth};

/**
 * Chat service class
 */
class ChatService
{
    use ArrayHelper;

    /** @var ChatRepository */
    protected $chatRepository;

    /**
     * ChatService constructor.
     * @param ChatRepository $chatRepository
     */
    function __construct(ChatRepository $chatRepository) {
        $this->chatRepository = $chatRepository;
    }

    /**
     * Dialog chat list service
     *
     * @param int $limit
     * @return Collection
     */
    public function chatList(int $limit = 0):Collection
    {
        $userDialogs = auth()->user()
            ->dialogs()
            ->with([
                'participants.user',
                'lastMessage.user'
            ])
            ->whereHas('messages')
            ->get();

        if ($limit) {
            $userDialogs = $userDialogs->take($limit);
        }

        foreach ($userDialogs as $dialog) {
            $lastMessage = $dialog->lastMessage;

            $dialog->id = $lastMessage->user_id;
            $dialog->name = $lastMessage->user->name;
            $dialog->avatar = $lastMessage->user->avatar;
            $dialog->created_at = $lastMessage->created_at;
            $dialog->isRead = $lastMessage->read;
            $dialog->isOnline = \Cache::get(CacheKey::USER_IS_ONLINE->value . $lastMessage->user_id);
            $dialog->difference = $lastMessage->created_at->diffForHumans();
            $dialog->text = \Str::limit($lastMessage->text, 50);
        }
        return $userDialogs->sortByDesc('created_at');
    }

    /**
     * Search service
     *
     * @param string $searchText
     * @return
     */
    public function search(string $searchText)
    {
        return $this->chatRepository->search($searchText);
    }

    /**
     * User dialog service
     *
     * @param int $dialogId
     * @param int $partnerId
     *
     * @return PrivateChatDTO
     */
    public function userDialog(int $dialogId, int $partnerId = 0): PrivateChatDTO
    {
        $dialog = $this->checkDialogAccess($dialogId);
        $dialogMessages = $this->chatRepository->getDialogMessages($dialogId);

        if ($dialogMessages->count()) {
            // Get id of the user we are talking to
            $partnerId = $dialog->participants
                ->firstOrFail(fn($user) => $user->user_id !== auth()->id())
                ->user_id;

            // Array of read messages
            $readMessages = [];

            foreach ($dialogMessages as $message) {
                $message->difference =
                    Carbon::createFromFormat('Y-m-d H:i:s', $message->created_at)->diffForHumans();

                $this->formatChatDate($message);

                if ($message->user_id !== auth()->id()) {
                    $readMessages[] = $message->message_id;
                }
            }

            // Update read messages
            MessagesModel::whereIn('message_id', $readMessages)
                ->update(['read' => true]);
            NotificationsService::userNotifications(auth()->id(), true);
        }

        return new PrivateChatDTO(
            dialogId: $dialogId,
            partnerId: $partnerId,
            messages: $dialogMessages
        );
    }

    /**
     * Get dialog Id or create new
     *
     * @param int        $userId
     * @param int        $dialogId
     * @param DialogType $dialogType
     *
     * @return int
     */
    public function getDialogId(
        int $userId,
        int $dialogId = 0,
        DialogType $dialogType = DialogType::PRIVATE
    ): int {

        if ($dialogId === 0) {
            $dialog = $this->chatRepository->getUserDialog($userId, $dialogType);
        } else {
            $dialog = DialogModel::where('dialog_id', $dialogId)->first();
        }

        if (empty($dialog) || is_null($dialog)) {

            $dialogId = DB::transaction(function () use ($userId, $dialogType) {
                $dialog = DialogModel::create([
                    'created_by' => auth()->id(),
                    'type' => $dialogType
                ]);

                $dialog->participants()->createMany([
                    ['user_id' => auth()->id(), 'role' => ChatRole::OWNER],
                    ['user_id' => $userId, 'role' => ChatRole::MEMBER],
                ]);

                return $dialog->dialog_id;
            });
        } else {
            $dialogId = $dialog->dialog_id;
        }

        return $dialogId;
    }

    /**
     * Formatting the text of the message composition
     *
     * @param \stdClass $obj
     *
     * @return void
     */
    private function formatChatDate(\stdClass $obj): void
    {
        $messageCreatedAt = Carbon::parse($obj->created_at);
        $obj->created_at = $messageCreatedAt->isToday()
            ? $messageCreatedAt->format('H:i')
            : $messageCreatedAt->format('d.m.Y H:i');
    }

    /**
     * @param int $dialogId
     * @return Model
     */
    private function checkDialogAccess(int $dialogId): Model
    {
        $dialogObject = DialogModel::findOrFail($dialogId);
        Gate::authorize('access', $dialogObject);
        return $dialogObject;
    }
}
