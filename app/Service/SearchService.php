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

        $searchResult = $this->searchRepository->searchByUser($search);
        $searchResult->view = 1;
        $searchResult->search = $search->search;
        return $searchResult;
    }

    /**
     * @param object $search
     * @return mixed
     */
    public function byNotation(object $search) {

        $searchResult = $this->searchRepository->searchByNotation($search);
        $searchResult->view = 2;
        $searchResult->search = $search->search;
        return $searchResult;
    }
}
