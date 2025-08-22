<?php

namespace App\Service\Chat;

use App\Exceptions\Chat\ChatMessageException;
use App\Repository\Chat\ChatRepository;
use App\User;
use Carbon\Carbon;
use App\Models\Chat\MessagesModel;
use App\Traits\ArrayHelper;
use App\Models\Chat\DialogModel as DialogModel;
use Illuminate\Support\Collection;
use App\Service\NotificationsService;
use Illuminate\Support\Facades\{Gate, DB, Auth};

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
     * Chat service
     *
     * @param int $limit
     * @return Collection
     */
    public function chat(int $limit = 0):Collection
    {
        $userDialogs = DB::table('dialog')
            ->select('dialog_id', 'send', 'recive')
            ->where(
                function ($query) {
                    $query->where('dialog.send', Auth::user()->id)
                        ->orWhere('dialog.recive', Auth::user()->id);
                }
            )
            ->get();

        if ($limit) {
            $userDialogs = $userDialogs->take($limit);
        }

        foreach ($userDialogs as $k => $chat) {
            $lastMessage = MessagesModel::query()
                ->where('dialog_id', $chat->dialog_id)
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'DESC')
            ->first();

            if (is_null($lastMessage)) {
                unset($userDialogs[$k]);
                continue;
            }

            $dialogWithId = (Auth::user()->id == $chat->send) ? $chat->recive : $chat->send;
            $user = User::query()
                ->select(['users.id AS userId', 'name', 'avatar'])
                    ->whereId($dialogWithId)
                ->first();

            $userDialogs[$k]->id = $user->userId;
            $userDialogs[$k]->name = $user->name;
            $userDialogs[$k]->avatar = $user->avatar;
            $userDialogs[$k]->text = $lastMessage->text;
            $userDialogs[$k]->created_at = $lastMessage->created_at;
            $userDialogs[$k]->isRead = $lastMessage->read;
            $userDialogs[$k]->isOnline = \Cache::get('is_online.' . $user->userId);
            $userDialogs[$k]->difference =
                Carbon::createFromFormat('Y-m-d H:i:s', $lastMessage->created_at)->diffForHumans();

            if (strlen($userDialogs[$k]->text) >= 50) {
                $chat->text = str_ireplace(['</div>'], '<br>', $chat->text);
                $userDialogs[$k]->text = Str::limit(strip_tags($chat->text, '<br>'), 50);
            }
        }
        return $userDialogs->sortByDesc('created_at');
    }

    /**
     * Search service
     *
     * @param array $word
     * @return
     */
    public function search(array $word)
    {
        $searchResult = $this->chatRepository->search(
            addslashes($word['word'])
        );
        foreach ($searchResult as $search) {
            $search->text = str_ireplace(array("\r\n", "\r", "\n"), '<br/>&emsp;', $search->text);
            $userObj = User::where('id', $search->send)->first();
            $search->id = $userObj->id;
            $search->name = $userObj->name;
            $search->avatar = $userObj->avatar;
        }
        return $searchResult;
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

        $messageId = $this->chatRepository->sendMessage(
            $message,
            $this->getDialogId($dialogWithId, $dialogId),
            $dialogWithId
        );

        if (!$messageId) {
            throw new ChatMessageException('Message was not sent!');
        }

        // Websocket
        $message = MessagesModel::where('message_id', $messageId)->firstOrFail();
        $user = User::select(['id', 'name', 'avatar'])
            ->whereId((auth()->id() === $message->recive ?: $message->send))
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

        $dialog = DialogModel::find($dialogId);
        Gate::authorize('access', $dialog);

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

        $dialog = DialogModel::find($dialogId);
        Gate::authorize('access', $dialog);

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

        $dialog = DialogModel::find($dialogId);
        Gate::authorize('access', $dialog);

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
     * @param int $userMessageWithId
     * @return array
     */
    public function userDialog(int $dialogId, $userMessageWithId = 0): array
    {
        $currentUserId = Auth::user()->id;
        $dialogCheck = DialogModel::where('dialog_id', $dialogId);

        if (!$dialogCheck->exists()
            || ($dialogCheck->first()->send != $currentUserId && $dialogCheck->first()->recive != $currentUserId)
        ) {
            throw new \Exception('Chat not exist');
        }

        $currentUserAvatar = Auth::user()->avatar;
        $dialogMessages = $this->chatRepository->getDialogMessages($dialogId);

        if ($dialogMessages->count()) {
            // Get id of the user we are talking to
            $anotherUserId = ($dialogMessages[0]->send == $currentUserId) ?
                $dialogMessages[0]->recive :
                $dialogMessages[0]->send;

            $anotherUserObj = User::where('id', $anotherUserId)->first();

            // Array of read messages
            $readedMessages = [];
            foreach ($dialogMessages as $message) {
                $message->text = str_ireplace(array("\r\n", "\r", "\n"), '<br/>&emsp;', $message->text);
                $message->difference =
                    Carbon::createFromFormat('Y-m-d H:i:s', $message->created_at)->diffForHumans();

                $this->formatChatDate($message);
                if ($message->send == $currentUserId) {
                    $message->name = Auth::user()->name;
                    $message->avatar = $currentUserAvatar;
                    $message->id = $currentUserId;
                } else {
                    $readedMessages[] = $message->message_id;
                    $message->name = $anotherUserObj->name;
                    $message->avatar = $anotherUserObj->avatar;
                    $message->id = $anotherUserObj->id;
                }
            }

            // Update read messages
            MessagesModel::whereIn('message_id', $readedMessages)
                ->update(['read' => true]);
            NotificationsService::userNotifications($currentUserId, true);

        } else {
            $anotherUserId = $userMessageWithId;
        }

        return [
            'dialogMessages' => $dialogMessages,
            'dialogId' => $dialogId,
            'recive' => $anotherUserId
        ];
    }

    /**
     * Get dialog Id or create new
     *
     * @param  $userId   int
     * @param  $dialogId int
     * @return int
     */
    public function getDialogId(int $userId, int $dialogId = 0): int
    {
        if ($dialogId === 0) {
            $dialogExist = $this->chatRepository->getUserDialog($userId);
        } else {
            $dialogExist = DialogModel::where('dialog_id', $dialogId)->first();
        }

        if (empty($dialogExist) || is_null($dialogExist)) {
            $dialogId = DB::table('dialog')->insertGetId([
                'send' =>  Auth::user()->id,
                'created_by' =>  Auth::user()->id,
                'recive' => $userId
            ]);
        } else {
            $dialogId = $dialogExist->dialog_id;
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

        if ($currentDate == $chatDate->format('d.m.Y')) {
            $obj->created_at = $chatDate->format('H:i');
        } else {
            $obj->created_at = $chatDate->format('d.m.Y H:i');
        }
    }
}
