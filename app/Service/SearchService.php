<?php

namespace App\Service;

use App\Repository\SearchRepository;

class SearchService
{
    /** @var $searchRepository */
    protected $searchRepository;

    /**
     * SearchService constructor.
     * @param SearchRepository $searchRepository
     */
    function __construct(SearchRepository $searchRepository) {

        $this->searchRepository = $searchRepository;
    }

    /**
     * @param object $search
     * @return mixed
     */
    public function byUser(object $search) {

        return $this->searchRepository->searchByUser($search);
    }

    /**
     * @param object $search
     * @return mixed
     */
    public function byNotation(object $search) {

        return $this->searchRepository->searchByNotation($search);
    }
}
