<?php

namespace App\Http\Controllers\Chat\Dialog;

use App\Enums\ResponseCodeEnum;
use App\Http\Controllers\Controller;
use App\Service\Chat\Dialog\DialogCommandService;
use App\Service\Chat\Dialog\DialogQueryService;
use App\Service\Chat\Messages\MessageQueryService;
use App\User;
use Illuminate\Http\Request;

/**
 * Class ChatController
 *
 * @package App\Http\Controllers
 */
class DialogController extends Controller
{
    /** @var DialogQueryService $dialogQueryService */
    protected $dialogQueryService;

    /** @var DialogCommandService $dialogCommandService */
    protected $dialogCommandService;

    /**
     * ChatController constructor.
     *
     * @param DialogQueryService $dialogService
     */
    function __construct(DialogQueryService $dialogQueryService, DialogCommandService $dialogCommandService)
    {
        $this->dialogQueryService = $dialogQueryService;
        $this->dialogCommandService = $dialogCommandService;
    }

    /**
     * Controller: Getting a list of user dialogs
     *
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|
     * \Illuminate\Foundation\Application
     */
    #[Route('/chat/', methods: ['GET'])]
    protected function dialogList()
    {
        return view('menu.Chat.chat', [
            'dialogList' => $this->dialogQueryService->dialogList()
        ]);
    }

    /**
     * Open a dialogue with user
     *
     * @param int $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    #[Route('/chat/open/{userId}', methods: ['GET'])]
    public function open(int $userId)
    {
        if (!User::whereId($userId)->exists()) {
            abort(ResponseCodeEnum::NOT_FOUND);
        }
        $currentUserId = auth()->id();

        // Checking the existing dialogue
        $dialog = $this->dialogQueryService->getPrivateDialog($currentUserId, $userId);

        if (!$dialog) {
            $dialogId = $this->dialogCommandService
                ->createDialogBetweenUsers($currentUserId, $userId);
        } else {
            $dialogId = $dialog->dialog_id;
        }

        return redirect()->route('dialog', ['dialogId' => $dialogId]);
    }

    /**
     * Controller get dialog messages
     *
     * @param int $dialogId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|never
     */
    #[Route('/chat/dialog/{dialogId}', methods: ['GET'])]
    protected function getDialogMessages(int $dialogId)
    {
        try {
            $dialog = app(MessageQueryService::class)->getDialogMessages($dialogId);

            return view('menu.Chat.chatLS', [
                'dialogObj'    => $dialog->messages,
                'dialogId'     => $dialog->dialogId,
                'lastDialogs'  => $this->dialogQueryService->dialogList(5)
            ]);
        } catch (\Throwable) {
            return abort(ResponseCodeEnum::NOT_FOUND);
        }
    }
}
