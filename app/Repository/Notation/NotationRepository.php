<?php

namespace App\Repository\Notation;

use App\Enums\Profile\ProfileEnum;
use App\Models\Notation\VoteNotationModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Class NotationRepository
 * @package App\Repository\Notation
 */
class NotationRepository
{

    /**
     * Create a notation
     *
     * @param array $dataNotation
     * @return int
     */
    public function create(Array $dataNotation)
    {

        return DB::table('notations')->insertGetId([
            'user_id' =>  Auth::user()->id,
            'name_notation' => trim(addslashes($dataNotation['notationName'])),
            'text_notation' => trim(addslashes($dataNotation['notationText'])),
            'notation_add_date' => Carbon::now(),
            'notation_edit_date' => Carbon::now()
        ]);
    }

    public function notationViewData(int $notationId)
    {
        return DB::table('notations')
            ->select('notations.notation_id', 'notations.user_id',
                'notations.name_notation', 'notations.text_notation',
                'notations.rating','users.name', 'users.avatar',
                'notations.notation_add_date', 'np.path_photo')
            ->join('users', 'users.id', '=', 'notations.user_id')
            ->leftJoin('notation_photo AS np', 'np.notation_id', '=', 'notations.notation_id')
            ->where('notations.notation_id', '=', $notationId)
        ->get();
    }

    public function voteNotation(int $notationId)
    {
        return DB::table('vote_notation')
            ->select('vote_notation_id')
            ->where('notation_id', '=', $notationId)
            ->where('user_id', '=', Auth::user()->id)
        ->get();

    }

    /**
     * @param int $notationId
     * @return array
     */
    public function getDataEdit(int $notationId)
    {

        $data['notation'] = DB::table('notations')
            ->select('user_id','notation_id','category','name_notation','text_notation')
            ->where('notation_id', '=', $notationId)
        ->first();

        $data['notation_photo'] = DB::table('notation_photo')
            ->select('path_photo','notation_photo_id')
            ->where('notation_id', '=', $notationId)
        ->get();

        return $data;
    }

    /**
     * @param array $dataNotationEdit
     * @return bool
     */
    public function edit(array $dataNotationEdit)
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

    /**
     * @param int $notationId
     * @param int $action
     * @return bool|int
     */
    public function changeRating(int $notationId, int $action)
    {

        $checkRating = DB::table('vote_notation')
            ->select('vote_notation_id', 'vote')
            ->where('user_id', '=', Auth::user()->id)
            ->where('notation_id', '=', $notationId)
        ->first();

        $dbMove = null;
        if (empty($checkRating->vote_notation_id)) {
            $dbMove = DB::table('vote_notation')->insert([
                'user_id' => Auth::user()->id,
                'notation_id' => $notationId,
                'vote' => $action,
                'vote_date' => Carbon::now()
            ]);
        } else {

            // checking for already set vote
            if ($checkRating->vote == 1 && $action == 1)
                return false;

            if ($checkRating->vote == 0 && $action == 0)
                return false;

            $dbMove = DB::table('vote_notation')
                ->where('user_id', '=', Auth::user()->id)
                ->where('notation_id', '=', $notationId)
            ->update([
                'vote' => $action,
                'vote_date' => Carbon::now()
            ]);
        }

        if($action) {
            $set = "SET `rating` = `rating` + 1";
        } else {
            $set = "SET `rating` = `rating` - 1";
        }

        DB::statement("UPDATE `notations` {$set} WHERE `notation_id` = {$notationId}");
        return $dbMove;
    }

    /**
     * @param int $notationDelete
     * @return string[]
     */
    public function delete(int $notationId)
    {

        $notation = DB::table('notations')
            ->select('user_id', 'notation_id')
            ->where('notation_id', '=', $notationId)
        ->first();

        if ($notation->user_id === Auth::user()->id) {

            $destroy = DB::table('notations')
                ->where('notation_id', '=', $notationId)
                ->where('user_id', '=',  Auth::user()->id)
            ->delete();

            if ($destroy) {

                $data = [
                    'status' => '1',
                    'msg' => 'success'
                ];
            } else {

                $data = [
                    'status' => '0',
                    'msg' => 'fail'
                ];
            }
            return $data;
        }
    }

    /**
     * @param $request
     * @return array
     */
    public function addPhoto($request)
    {
        $paths = array();
        if ($request->hasFile('images')) {

            $files = $request->file('images');
            foreach($files as $file) {
                $imageName = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path("img/notation_photo/{$request->notation_id}"), $imageName);

                DB::table('notation_photo')->insert([
                    'user_id' => Auth::user()->id,
                    'notation_id' => $request->notation_id,
                    'path_photo' => "img/notation_photo/{$request->notation_id}/{$imageName}",
                    'photo_edit_date' => Carbon::now()
                ]);

                $paths[] = $imageName;
            }
        }
        return $paths;
    }

    /**
     * @param array $photoData
     * @return bool
     * @throws \Exception
     */
    public function removePhoto(array $photoData)
    {

        $ownerPhotoCheck = DB::table('notation_photo')
            ->select('user_id', 'path_photo')
            ->where('notation_id', '=', $photoData['notationId'])
            ->where('notation_photo_id', '=', $photoData['photoId'])
        ->first();

        if (is_null($ownerPhotoCheck)) {
            throw new \Exception('Ошибка удаления: Данной фотографии не существует!');
        }

        if ($ownerPhotoCheck->user_id !== Auth::user()->id) {
            throw new \Exception('Ошибка удаления: нет доступа на удаление фотографии!');
        }

        $delete = File::delete(public_path($ownerPhotoCheck->path_photo));
        if ($delete) {
            return DB::table('notation_photo')
                ->where('notation_id', '=', $photoData['notationId'])
                ->where('notation_photo_id', '=', $photoData['photoId'])
            ->delete();
        } else {
            throw new \Exception('Ошибка удаления');
        }

        return false;
    }
}
