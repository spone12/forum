<?php

namespace  App\Http\Model;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Http\Model\NotationModel;

class SearchModel extends Model
{
    protected static function search_by_user($query)
    {
        $result = User::select('name', 'id', 'avatar')->where('name', 'LIKE', $query->search.'%')
                        ->orderBy('name', 'ASC')->get();
        return $result;
    }

    protected static function search_by_notation($query)
    {
        $result = NotationModel::select('notation_id', 'name_notation', 'rating')
            ->where('name_notation', 'LIKE', $query->search.'%')
            ->orWhere('text_notation', 'LIKE', '%'.$query->search.'%')
                        ->orderBy('notation_add_date', 'ASC')->get();
        return $result;
    }
}
