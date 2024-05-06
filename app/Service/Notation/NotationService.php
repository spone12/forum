<?php

namespace App\Service\Notation;

use App\Enums\ExpEnum;
use App\Enums\Profile\ProfileEnum;
use App\Http\Requests\NotationPhotoRequest;
use App\Models\Notation\{NotationModel, NotationViewModel, VoteNotationModel, NotationPhotoModel};
use App\Models\DescriptionProfile;
use App\Repository\Notation\NotationRepository;
use App\Traits\ArrayHelper;
use Illuminate\Support\Facades\Auth;

/**
 * Class NotationService
 * @package App\Service\Notation
 */
class NotationService
{
    use ArrayHelper;

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
        if (!$notation) {
            throw new \Exception('Notation not found!');
        }
        $notation->photo = NotationPhotoModel::query()->where('notation_id', $notationId)->get('path_photo');

        if (Auth::check()) {
            $vote = NotationModel::query()
                ->find($notationId)
                ->notationsVote()
                ->where('user_id', '=', Auth::user()->id)
            ->first();

            if ($vote) {
                $notation->vote = $vote->value('vote');
            }
        }

        $notation->text_notation = str_ireplace(array("\r\n", "\r", "\n"), '<br/>&emsp;', $notation->text_notation);
        ArrayHelper::noAvatar($notation);

        $notationViews = NotationViewModel::query()
            ->select('counter_views', 'view_date')
            ->where('notation_id', '=', $notationId)
            ->orderBy('view_date')
        ->get();

        $list = [];
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
