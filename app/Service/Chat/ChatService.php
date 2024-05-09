<?php

namespace App\Service\Chat;

use App\Enums\Profile\ProfileEnum;
use App\Repository\Chat\ChatRepository;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Notifications\SendMessageNotification;
use App\Models\Chat\MessagesModel;
use App\Traits\ArrayHelper;
use App\Models\Chat\DialogModel as DialogModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

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
                ->where('dialog', $chat->dialog_id)
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'DESC')
            ->first();

            if (is_null($lastMessage)) {
                unset($userDialogs[$k]);
                continue;
            }

            $dialogWithId = (Auth::user()->id == $chat->send) ? $chat->recive : $chat->send;
            $user = DB::table('users')
                ->select('users.id AS userId', 'users.name',  'users.avatar')
                ->where('users.id', $dialogWithId)
                ->first();

            $userDialogs[$k]->id = $user->userId;
            $userDialogs[$k]->name = $user->name;
            $userDialogs[$k]->avatar = $user->avatar;
            $userDialogs[$k]->text = $lastMessage->text;
            $userDialogs[$k]->created_at = $lastMessage->created_at;
            $userDialogs[$k]->isRead = $lastMessage->read;
            $userDialogs[$k]->difference =
                Carbon::createFromFormat('Y-m-d H:i:s', $lastMessage->created_at)->diffForHumans();

            ArrayHelper::noAvatar($userDialogs[$k]);

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

            $search->avatar = $userObj->avatar ?: ProfileEnum::NO_AVATAR;
            $search->id = $userObj->id;
            $search->name = $userObj->name;
        }
        return $searchResult;
    }

    /**
     * Send message service
     *
     * @param array $data
     * @return bool
     */
    public function message(array $data): bool
    {
        $messageId = $this->chatRepository->sendMessage(
            addslashes($data['message']),
            $this->getDialogId($data['dialogWithId'], $data['dialogId']),
            $data['dialogWithId']
        );

        if (!$messageId) {
            throw new \Exception('Message not send');
        }

        // Websocket
        $message = MessagesModel::where('message_id', $messageId)->firstOrFail();
        $user = User::select(['id', 'name', 'avatar'])
            ->whereId((auth()->id() === $message->recive ?: $message->send))
        ->firstOrFail();
        broadcast(new \App\Events\ChatMessageEvent($user, $message));

        return true;
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
        $message = addslashes($data['message']);

        $this->checkAccess($dialogId);
        $messageObj = MessagesModel::where('message_id', $messageId)->firstOrFail();
        $messageObj->text = $message;

        if (!$messageObj->save()) {
            throw new \Exception('Message not edited!');
        }

        return [
            'success' => true
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

        $this->checkAccess($dialogId);
        $messageObj = MessagesModel::query()->where('message_id', $messageId)->firstOrFail();
        $messageObj->delete();

        if (!$messageObj->save()) {
            throw new \Exception('Message not deleted!');
        }

        return [
            'success' => true
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

        $this->checkAccess($dialogId);
        $messageObj = MessagesModel::onlyTrashed()
            ->where('message_id', $messageId)
            ->firstOrFail();
        $messageObj->restore();

        if (!$messageObj->save()) {
            throw new \Exception('Message not recovered!');
        }

        return [
            'success' => true
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

        $currentUserAvatar = Auth::user()->avatar ?: ProfileEnum::NO_AVATAR;
        $dialogMessages = $this->chatRepository->getDialogMessages($dialogId);

        if ($dialogMessages->count()) {
            // Get id of the user we are talking to
            $anotherUserId = ($dialogMessages[0]->send == $currentUserId) ?
                $dialogMessages[0]->recive :
                $dialogMessages[0]->send;

            $anotherUserObj = User::where('id', $anotherUserId)->first();
            ArrayHelper::noAvatar($anotherUserObj);

            foreach ($dialogMessages as $dialog) {
                $dialog->text = str_ireplace(array("\r\n", "\r", "\n"), '<br/>&emsp;', $dialog->text);
                $dialog->difference =
                    Carbon::createFromFormat('Y-m-d H:i:s', $dialog->created_at)->diffForHumans();

                $this->formatChatDate($dialog);
                if ($dialog->send == $currentUserId) {
                    $dialog->name = Auth::user()->name;
                    $dialog->avatar = $currentUserAvatar;
                    $dialog->id = $currentUserId;
                } else {
                    $dialog->name = $anotherUserObj->name;
                    $dialog->avatar = $anotherUserObj->avatar;
                    $dialog->id = $anotherUserObj->id;
                }
            }
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
            $dialogId = DB::table('dialog')->insertGetId(
                [
                    'send' =>  Auth::user()->id,
                    'recive' => $userId
                ]
            );
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
    private function formatChatDate($obj, $currentDate = '')
    {
        $chatDate = Carbon::parse($obj->created_at);
        if (empty($currentDate)) {
            $currentDate = Carbon::now()->format('d.m.Y');
        }

        if ($currentDate == $chatDate->format('d.m.Y')) {
            $obj->created_at =  $chatDate->format('H:i');
        } else {
            $obj->created_at = $chatDate->format('d.m.Y H:i');
        }
    }

    /**
     * Check access to dialog
     *
     * @param  int $dialogId
     * @throws \Exception
     */
    private function checkAccess(int $dialogId)
    {
        $dialog = DialogModel::query()->where('dialog_id', $dialogId)->first();
        if (!$dialog->exists()) {
            throw new \Exception('Dialog not found!');
        }

        if ($dialog->send !== Auth::user()->id && $dialog->recive !== Auth::user()->id) {
            throw new \Exception('Not access to message edit!');
        }
    }
}
