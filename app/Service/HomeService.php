<?php

namespace App\Service;

use App\Repository\HomeRepository;

class HomeService
{
    /** @var HomeRepository */
    protected $homeRepository;

    /**
     * HomeService constructor.
     * @param HomeRepository $homeRepository
     */
    function __construct(HomeRepository $homeRepository) {

        $this->homeRepository = $homeRepository;
    }

    /**
     * Home service
     *
     * @return
     */
    public function notations() {

        return $this->homeRepository->takeNotations();
    }
}
