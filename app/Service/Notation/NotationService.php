<?php

namespace App\Service\Notation;

use App\Enums\{ExpEnum, NotationEnum};
use App\Http\Requests\NotationPhotoRequest;
use App\Models\Notation\{NotationModel, NotationViewModel, NotationPhotoModel, VoteNotationModel};
use App\Models\DescriptionProfile;
use App\Repository\Notation\NotationRepository;
use App\Traits\ArrayHelper;
use Illuminate\Support\Facades\{Auth, DB, Storage};
use Illuminate\Support\Collection;


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
     * Get notation data by id
     *
     * @param int $notationId
     * @return object
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
                $notation->vote = $vote->vote;
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
     * @return bool
     */
    public function changeRating(int $notationId, int $action):bool
    {
        $notation = NotationModel::query()->find($notationId);
        if (!$notation) {
            throw new \Exception('Deletion error: no notation with the specified identifier found!');
        }

        $voteObj = VoteNotationModel::query()
            ->select('vote_notation_id', 'vote')
                ->where('user_id', '=', Auth::user()->id)
                ->where('notation_id', '=', $notationId)
            ->first();

        $dbMove = false;
        if (!$voteObj) {
            $dbMove = DB::table('vote_notation')->insert([
                'user_id' => Auth::user()->id,
                'notation_id' => $notationId,
                'vote' => $action,
                'vote_date' => now()
            ]);
        } else {

            // Checking for already set vote
            if ($voteObj->vote === 1 && $action === 1) {
                return false;
            }

            if ($voteObj->vote === 0 && $action === 0) {
                return false;
            }

            $dbMove = $voteObj->update([
                'vote' => $action,
                'vote_date' => now()
            ]);
        }

        if ($action) {
            $notation->increment('rating');
        } else {
            $notation->decrement('rating');
        }

        return $dbMove;
    }

    /**
     * Delete notation by id
     *
     * @param int $notationId
     * @return bool
     */
    public function delete(int $notationId):bool
    {
        $notation = NotationModel::query()
            ->select('user_id', 'notation_id')
                ->where('notation_id', '=', $notationId)
            ->first();

        if (!$notation) {
            throw new \Exception('Deletion error: no notation with the specified identifier found!');
        }

        if ($notation->user_id !== Auth::user()->id) {
            throw new \Exception('Deletion error: no access to delete notation!');
        }

        $currentNotationPhotos = NotationPhotoModel::query()
            ->select('notation_photo_id', 'path_photo')
                ->where('notation_id', '=', $notationId)
            ->get();

        foreach ($currentNotationPhotos as $photo) {
            $removeData = collect([
                'path' => $photo->path_photo,
                'notationId' => $notationId,
                'photoId' => $photo->notation_photo_id
            ]);
            $this->removePhoto($removeData);
        }

        return DB::table('notations')
            ->where('notation_id', '=', $notationId)
            ->where('user_id', '=', Auth::user()->id)
            ->delete();
    }

    /**
     * Add photo to notation
     *
     * @param NotationPhotoRequest $request
     * @return array
     */
    public function addPhoto(NotationPhotoRequest $request):array
    {
        $paths = [];
        if (!$request->hasFile('images')) {
            throw new \Exception('Error, file(s) not found');
        }

        $files = $request->file('images');
        foreach ($files as $file) {
            $path = Storage::disk('public')->putFile(
                NotationEnum::PHOTO_PATH . $request->notation_id, $file
            );

            DB::table('notation_photo')->insert([
                'user_id' => Auth::user()->id,
                'notation_id' => $request->notation_id,
                'path_photo' => $path,
                'photo_edit_date' => now()
            ]);
            $paths[] = $path;
        }

        if (empty($paths)) {
            throw new \Exception('File(s) not uploaded');
        }

         return $paths;
    }

    /**
     * Delete photo from notation
     *
     * @param array $photoData
     * @return bool
     */
    public function removePhotoService(array $photoData):bool
    {
        $photoObj = NotationPhotoModel::query()
            ->select('user_id', 'path_photo')
                ->where('notation_id', '=', $photoData['notationId'])
                ->where('notation_photo_id', '=', $photoData['photoId'])
            ->first();

        if (!$photoObj) {
            throw new \Exception('Deletion error: This photo does not exist!');
        }

        if ($photoObj->user_id !== Auth::user()->id) {
            throw new \Exception('Deletion error: no access to delete photo!');
        }

        $removeData = collect([
            'path' => $photoObj->path_photo,
            'notationId' => $photoData['notationId'],
            'photoId' => $photoData['photoId']
        ]);

        return $this->removePhoto($removeData);
    }

     /**
     * Delete photo from disk and DB
     *
     * @param Collection $removeData
     * @return bool
     */
    private function removePhoto(Collection $removeData):bool
    {
        $delete = Storage::disk('public')->delete(
            $removeData->get('path')
        );

        if ($delete) {
            return NotationPhotoModel::query()
                ->where('notation_id', '=', $removeData->get('notationId'))
                ->where('notation_photo_id', '=', $removeData->get('photoId'))
            ->delete();
        } else {
            throw new \Exception('Photo deletion error ');
        }
    }
}
