<?php

namespace App\Repository;

use App\Models\Notation\NotationModel;
use App\User;

/**
 * Class SearchRepository
 * @package App\Repository
 */
class SearchRepository
{

    /**
     * @param object $query
     * @return mixed
     */
    public function searchByUser (object $query)
    {

        $result = User::select('name', 'id', 'avatar')
            ->where('name', 'LIKE', $query->search.'%')
            ->orderBy('name', 'ASC')
        ->paginate(10)
        ->onEachSide(1);

        $result->view = 1;
        $result->search = $query->search;
        return $result;
    }

    /**
     * @param object $query
     * @return mixed
     */
    public function searchByNotation (object $query)
    {

        $result = NotationModel::select('notation_id',
            'name_notation', 'rating', 'text_notation')
            ->where('name_notation', 'LIKE', $query->search.'%')
            ->orWhere('text_notation', 'LIKE', '%'.$query->search.'%')
            ->orderBy('notation_add_date', 'ASC')
        ->paginate(10)
        ->onEachSide(1);

        $result->view = 2;
        $result->search = $query->search;
        return $result;
    }
}
