<?php

namespace App\Service\Chat;

use App\Repository\Chat\ChatRepository;

class ChatService
{
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
     * @return
     */
    public function chat(int $limit = 0) {

        return $this->chatRepository->getUserChats($limit);
    }

    /**
     * Dialog id service
     *
     * @param int $value
     * @return
     */
    public function dialogId(int $value) {

        return $this->chatRepository->getDialogId($value);
    }

    /**
     * Search service
     *
     * @param array $word
     * @return
     */
    public function search(array $word) {

        return $this->chatRepository->search(addslashes($word['word']));
    }

    /**
     * Send message service
     *
     * @param array $data
     * @return
     */
    public function message(array $data) {

        return $this->chatRepository->sendMessage(
            addslashes($data['message']), (int) $data['dialogId'], (int) $data['dialogWithId']
        );
    }

    /**
     * Edit message service
     *
     * @param array $data
     * @return
     */
    public function edit(array $data) {

        return $this->chatRepository->editMessage(
            addslashes($data['message']), (int) $data['dialogId'], (int) $data['messageId']
        );
    }

    /**
     * Delete message service
     *
     * @param array $data
     * @return
     */
    public function delete(array $data) {

        return $this->chatRepository->deleteMessage(
            (int) $data['dialogId'], (int) $data['messageId']
        );
    }

    /**
     * Delete message service
     *
     * @param array $data
     * @return
     */
    public function recover(array $data) {

        return $this->chatRepository->recoverMessage(
            (int) $data['dialogId'], (int) $data['messageId']
        );
    }

    /**
     * User dialog service
     *
     * @param int $dialogId
     * @param int $userMessageWithId
     * @return array
     */
    public function userDialog(int $dialogId, $userMessageWithId = 0): array {

        return $this->chatRepository->getUserDialog($dialogId, $userMessageWithId);
    }
}
