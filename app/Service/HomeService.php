<?php

namespace App\Service;

use App\Repository\HomeRepository;

class HomeService
{
    protected $homeRepository;

    function __construct(HomeRepository $homeRepository) {

        $this->homeRepository = $homeRepository;
    }

    /**
     * Home service
     * @param int $limit
     * @return
     */
    public function notations() {

        return $this->homeRepository->takeNotations();
    }
}
