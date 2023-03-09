<?php

namespace App\Service\Notation;

use App\Http\Requests\NotationPhotoRequest;
use App\Models\Notation\NotationViewModel;
use App\Repository\Notation\NotationRepository;

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

        $create = $this->notationRepository->create($data);
        return $create;
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
        $view = $this->notationRepository->view($notationId);
        return $view;
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
    public function edit(array $input)
    {

        $edit = $this->notationRepository->edit($input);
        return $edit;
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

        $delete = $this->notationRepository->removePhoto($photoData);
        return $delete;
    }
}
