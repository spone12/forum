<?php

namespace App\Service\Notation;

use App\Enums\ExpEnum;
use App\Enums\Profile\ProfileEnum;
use App\Http\Requests\NotationPhotoRequest;
use App\Models\Notation\NotationViewModel;
use App\Models\Notation\VoteNotationModel;
use App\Models\ProfileModel;
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
        $expAdded = ProfileModel::expAdd(ExpEnum::NOTATION_ADD);
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
                $notation[0]->vote = VoteNotationModel::where('vote_notation_id', '=', $vote[0]->vote_notation_id)
                    ->first()->vote;
            }
        }

        $notation[0]->text_notation = str_ireplace(array("\r\n", "\r", "\n"), '<br/>&emsp;', $notation[0]->text_notation);

        if (is_null($notation[0]->avatar)) {
            $notation[0]->avatar = ProfileEnum::NO_AVATAR;
        }

        $notationViews = NotationViewModel::select('counter_views', 'view_date')
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
        $notation[0]->countViews = number_format($countViews, 0, '.', ',');
        $notation['graph'] = json_encode($list);

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

        $editData = $this->notationRepository->getDataEdit($notationId);
        return $editData;
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

        $change = $this->notationRepository->changeRating($notationId, $action);
        return $change;
    }

    /**
     * Delete notation by id
     *
     * @param int $notationId
     * @return mixed
     */
    public function delete(int $notationId)
    {

        $delete = $this->notationRepository->delete($notationId);
        return $delete;
    }

    /**
     * Add photo to notation
     *
     * @param NotationPhotoRequest $request
     * @return mixed
     */
    public function addPhoto(NotationPhotoRequest $request)
    {

        $photoPath = $this->notationRepository->addPhoto($request);
        return $photoPath;
    }

    /**
     * Delete photo from notation
     *
     * @param array $photoData
     * @return string
     */
    public function removePhoto(array $photoData)
    {

        $delete = $this->notationRepository->removePhotoCheck($photoData);
        return $delete;
    }
}
