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
                        ->orderBy('name', 'ASC')->paginate(10)->onEachSide(1);

        $result->view = 1;
        $result->search = $query->search;
        return $result;
    }

    protected static function search_by_notation($query)
    {
        $result = NotationModel::select('notation_id', 'name_notation', 'rating','text_notation')
            ->where('name_notation', 'LIKE', $query->search.'%')
            ->orWhere('text_notation', 'LIKE', '%'.$query->search.'%')
                        ->orderBy('notation_add_date', 'ASC')->paginate(10)->onEachSide(1);

        $result->view = 2;
        $result->search = $query->search;
        return $result;
    }
}
