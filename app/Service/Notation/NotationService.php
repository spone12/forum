<?php

namespace App\Service\Notation;

use App\Enums\ExpEnum;
use App\Enums\Profile\ProfileEnum;
use App\Http\Requests\NotationPhotoRequest;
use App\Models\Notation\NotationViewModel;
use App\Models\Notation\VoteNotationModel;
use App\Models\DescriptionProfile;
use App\Repository\Notation\NotationRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class NotationService
 * @package App\Service\Notation
 */
class NotationService
{
    /** @var NotationRepository */
    protected $notationRepository;

    /**
     * NotationService constructor
     * @param NotationRepository $notationRepository
     */
    function __construct(NotationRepository $notationRepository) {
        $this->notationRepository = $notationRepository;
    }

    /**
     * Create notation
     *
     * @param $data
     * @return array
     */
    public function create($data)
    {
        $notationId = $this->notationRepository->create($data);
        $expAdded = DescriptionProfile::expAdd(ExpEnum::NOTATION_ADD);

        return [
            'notationId' => $notationId,
            'expAdded' => sprintf(trans('app.expAdd'), $expAdded)
        ];
    }

    /**
     * Display notation by id
     *
     * @param int $notationId
     * @return mixed
     */
    public function view(int $notationId)
    {

        NotationViewModel::addViewNotation($notationId);
        $notation = $this->notationRepository->notationViewData($notationId);

        if (Auth::check()) {
            $vote = $this->notationRepository->voteNotation($notationId);
            if ($vote->count()) {
                $notation->vote = VoteNotationModel::where('vote_notation_id', '=', $vote->vote_notation_id)
                    ->first()->vote;
            }
        }

        $notation->text_notation = str_ireplace(array("\r\n", "\r", "\n"), '<br/>&emsp;', $notation->text_notation);

        if (is_null($notation->avatar)) {
            $notation->avatar = ProfileEnum::NO_AVATAR;
        }

        $notationViews = NotationViewModel::query()
            ->select('counter_views', 'view_date')
            ->where('notation_id', '=', $notationId)
            ->orderBy('view_date')
        ->get();

        $list = array();
        $countViews = 0;

        foreach ($notationViews as $v) {
            $countViews += $v->counter_views;
            $list[] = array(
                'full_date' => date('d.m.Y', strtotime($v->view_date)),
                'sum_views' => $countViews,
                'value' => $v->counter_views
            );
        }
        $notation->countViews = number_format($countViews, 0, '.', ',');
        $notation->graph = json_encode($list);

        return $notation;
    }

    /**
     * Get data edit notation
     *
     * @param int $notationId
     * @return array
     */
    public function getDataEdit(int $notationId)
    {
        return $this->notationRepository->getDataEdit($notationId);
    }

    /**
     * Edit notation by input data
     *
     * @param array $input
     * @return bool
     */
    public function update(array $input)
    {
        $edit = $this->notationRepository->update($input);
        if (!$edit) {
            throw new \Exception('Notation has not been changed');
        }
        return true;
    }

    /**
     * Change notation rating
     *
     * @param int $notationId
     * @param int $action
     * @return bool|int
     */
    public function changeRating(int $notationId, int $action)
    {
        return $this->notationRepository->changeRating($notationId, $action);
    }

    /**
     * Delete notation by id
     *
     * @param int $notationId
     * @return mixed
     */
    public function delete(int $notationId)
    {
        return $this->notationRepository->delete($notationId);
    }

    /**
     * Add photo to notation
     *
     * @param NotationPhotoRequest $request
     * @return mixed
     */
    public function addPhoto(NotationPhotoRequest $request)
    {
        return $this->notationRepository->addPhoto($request);
    }

    /**
     * Delete photo from notation
     *
     * @param array $photoData
     * @return string
     */
    public function removePhoto(array $photoData)
    {
        return $this->notationRepository->removePhotoCheck($photoData);
    }
}
