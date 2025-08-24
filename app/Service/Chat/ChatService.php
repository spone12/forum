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
     * Send message service
     *
     * @param array $data
     * @return array
     */
    public function send(array $data): array
    {
        $dialogWithId = (int)$data['dialogWithId'];
        $dialogId = (int)$data['dialogId'];
        $message = trim($data['message']);

        $this->checkDialogAccess($dialogId);

        $messageId = $this->chatRepository->sendMessage(
            $message,
            $this->getDialogId($dialogWithId, $dialogId)
        );

        if (!$messageId) {
            throw new ChatMessageException('Message was not sent!');
        }

        // Websocket
        $message = MessagesModel::where('message_id', $messageId)->firstOrFail();
        $user = User::select(['id', 'name', 'avatar'])
            ->whereId(auth()->id())
        ->firstOrFail();
        broadcast(new \App\Events\ChatMessageEvent($user, $message));

        return [
            'id' => $messageId,
            'created_at' => $message->created_at
        ];
    }

    /**
     * Edit message in dialog
     *
     * @param array $data
     * @return array
     */
    public function edit(array $data):array
    {
        $dialogId = (int) $data['dialogId'];
        $messageId = (int) $data['messageId'];
        $message = trim($data['message']);

        $this->checkDialogAccess($dialogId);

        $messageObj = MessagesModel::where('message_id', $messageId)
            ->where('dialog_id', $dialogId)
            ->firstOrFail();
        $messageObj->text = $message;

        if (!$messageObj->save()) {
            throw new ChatMessageException('The message has not been changed!');
        }

        return [
            'id' => $messageId,
            'updated_at' => $messageObj->updated_at
        ];
    }

    /**
     * Delete message in dialog
     *
     * @param array $data
     * @return array
     */
    public function delete(array $data):array
    {
        $dialogId = (int) $data['dialogId'];
        $messageId = (int) $data['messageId'];

        $this->checkDialogAccess($dialogId);

        $messageObj = MessagesModel::query()
            ->where('message_id', $messageId)
            ->where('dialog_id', $dialogId)
            ->firstOrFail();
        $messageObj->delete();

        if (!$messageObj->save()) {
            throw new ChatMessageException('The message was not deleted!');
        }

        return [
            'id' => $messageId,
            'deleted_at' => $messageObj->deleted_at
        ];
    }

    /**
     * Recover message in dialog
     *
     * @param array $data
     * @return
     */
    public function recover(array $data):array
    {
        $dialogId = (int) $data['dialogId'];
        $messageId = (int) $data['messageId'];

        $this->checkDialogAccess($dialogId);

        $messageObj = MessagesModel::onlyTrashed()
            ->where('message_id', $messageId)
            ->where('dialog_id', $dialogId)
            ->firstOrFail();
        $messageObj->restore();

        if (!$messageObj->save()) {
            throw new ChatMessageException('The message was not restored!');
        }

        return [
            'id' => $messageId,
            'updated_at' => $messageObj->updated_at
        ];
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
     * Formate create date message value
     *
     * @param  $obj
     * @param  $currentDate string
     * @return void
     */
    private function formatChatDate($obj, $currentDate = ''): void
    {
        $chatDate = Carbon::parse($obj->created_at);
        if (empty($currentDate)) {
            $currentDate = Carbon::now()->format('d.m.Y');
        }

        if ($currentDate === $chatDate->format('d.m.Y')) {
            $obj->created_at = $chatDate->format('H:i');
        } else {
            $obj->created_at = $chatDate->format('d.m.Y H:i');
        }
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
