<?php

namespace App\Repository\Notation;

use Carbon\Carbon;
use Illuminate\Support\Facades\{Auth, DB};

/**
 * Class NotationRepository
 *
 * @package App\Repository\Notation
 */
class NotationRepository
{

    /**
     * Create a notation
     *
     * @param  array $dataNotation
     * @return int
     */
    public function create(Array $dataNotation)
    {
        return DB::table('notations')->insertGetId(
            [
                'user_id' =>  Auth::user()->id,
                'name_notation' => trim(addslashes($dataNotation['notationName'])),
                'text_notation' => trim(addslashes($dataNotation['notationText'])),
                'notation_add_date' => Carbon::now(),
                'notation_edit_date' => Carbon::now()
            ]
        );
    }

    public function notationViewData(int $notationId)
    {
        return DB::table('notations')
            ->select(
                'notations.notation_id', 'notations.user_id',
                'notations.name_notation', 'notations.text_notation',
                'notations.rating', 'users.name', 'users.avatar',
                'notations.notation_add_date'
            )
            ->join('users', 'users.id', '=', 'notations.user_id')
            ->where('notations.notation_id', '=', $notationId)
            ->first();
    }

    /**
     * @param  int $notationId
     * @return array
     */
    public function getDataEdit(int $notationId):array
    {
        $data['notation'] = DB::table('notations')
            ->select('user_id', 'notation_id', 'category', 'name_notation', 'text_notation')
            ->where('notation_id', '=', $notationId)
            ->first();

        $data['notation_photo'] = DB::table('notation_photo')
            ->select('path_photo', 'notation_photo_id')
            ->where('notation_id', '=', $notationId)
            ->get();

        return $data;
    }

    /**
     * @param  array $dataNotationEdit
     * @return bool
     */
    public function update(array $dataNotationEdit)
    {
        return DB::table('notations')
            ->where('user_id', '=', Auth::user()->id)
            ->where('notation_id', '=', $dataNotationEdit['notationId'])
            ->update([
                'name_notation' => $dataNotationEdit['notationName'],
                'text_notation' => $dataNotationEdit['notationText'],
                'notation_edit_date' => Carbon::now()
            ]);
    }
}
