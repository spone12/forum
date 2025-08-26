<?php

namespace App\Http\Controllers\Chat\Dialog;

use App\Http\Controllers\Controller;
use App\Service\Chat\Dialog\DialogService;
use App\Service\Chat\Messages\MessageQueryService;
use Illuminate\Http\Request;

/**
 * Class ChatController
 *
 * @package App\Http\Controllers
 */
class DialogController extends Controller
{
    /**
     * @var DialogService
     */
    protected $dialogService;

    /**
     * ChatController constructor.
     *
     * @param DialogService $dialogService
     */
    function __construct(DialogService $dialogService)
    {
        $this->dialogService = $dialogService;
    }

    /**
     * Controller user chats
     *
     * @return \Illuminate\Contracts\Foundation\Application|
     * \Illuminate\Contracts\View\Factory|
     * \Illuminate\Contracts\View\View
     */
    protected function dialogList()
    {
        return view('menu.Chat.chat', [
            'userChats' => $this->dialogService->dialogList()
        ]);
    }

    /**
     * Controller current user dialogs
     *
     * @param  int     $value   - mixed (dialogId or userId)
     * @param  Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    protected function getDialogMessages(Request $request, int $value)
    {
        try {
            $dialogId = $request->get('fromProfile') ?
                $this->dialogService->getDialogId($value) :
                $value;
            $userDialog = app(MessageQueryService::class)->getDialogMessages($dialogId, $value);

            return view('menu.Chat.chatLS', [
                'dialogWithId' => $userDialog->partnerId,
                'dialogObj'    => $userDialog->messages,
                'dialogId'     => $userDialog->dialogId,
                'lastDialogs'  => $this->dialogService->dialogList(5)
            ]);
        } catch (\Throwable $exception) {
            return redirect()
                ->route('chat')
                ->with('errors', collect($exception->getMessage()));
        }
    }
}
