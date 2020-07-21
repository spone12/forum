<?php

namespace  App\Http\Model;

use Illuminate\Database\Eloquent\Model;
use App\User;

class SearchModel extends Model
{
    protected static function query_search_user($query)
    {
        $result = User::select('name', 'id', 'avatar')->where('name', 'LIKE', $query.'%')->get();
        return $result;
    }
}
