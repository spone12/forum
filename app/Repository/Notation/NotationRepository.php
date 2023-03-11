<?php

namespace App\Repository\Notation;

use App\Enums\Profile\ProfileEnum;
use App\Models\Notation\VoteNotationModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    /**
     * @param int $notationId
     * @return \Illuminate\Support\Collection
     */
    public function view(int $notationId)
    {

        $notation = DB::table('notations')
            ->select('notations.notation_id', 'notations.user_id',
                'notations.name_notation', 'notations.text_notation',
                'notations.rating','users.name', 'users.avatar',
                'notations.notation_add_date', 'np.path_photo')
            ->join('users', 'users.id', '=', 'notations.user_id')
            ->leftJoin('notation_photo AS np', 'np.notation_id', '=', 'notations.notation_id')
            ->where('notations.notation_id', '=', $notationId)
        ->get();

        if (Auth::check()) {
            $vote = DB::table('vote_notation')
                ->select('vote_notation_id')
                ->where('notation_id', '=', $notationId)
                ->where('user_id', '=', Auth::user()->id)
                ->get();

            if ($vote->count()) {
                $notation[0]->vote = VoteNotationModel::where('vote_notation_id', '=', $vote[0]->vote_notation_id)
                    ->first()->vote;
            }
        }

        $notation[0]->text_notation = str_ireplace(array("\r\n", "\r", "\n"), '<br/>&emsp;', $notation[0]->text_notation);

        if (is_null($notation[0]->avatar)) {
            $notation[0]->avatar = ProfileEnum::NO_AVATAR;
        }

        $notationViews = DB::table('notation_views')
            ->select('counter_views','view_date')
            ->where('notation_id', '=', $notationId)
            ->orderBy('view_date')
            ->get();

        $list = array();
        $countViews = 0;

        foreach ($notationViews as $v) {
            $countViews += $v->counter_views;
            $list[] = array(
                'full_date' => date('d.m.Y', strtotime($v->view_date)),
                'sum_views' => $countViews,
                'value' => $v->counter_views
            );
        }
        $notation[0]->countViews = number_format($countViews, 0, '.', ',');
        $notation['graph'] = json_encode($list);

        return $notation;
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

        $upd = DB::table('notations')
            ->where('user_id', '=', Auth::user()->id)
            ->where('notation_id', '=', $dataNotationEdit['notation_id'])
        ->update([
            'name_notation' =>  $dataNotationEdit['name_tema'],
            'text_notation' =>  $dataNotationEdit['text_notation'],
            'notation_edit_date' => Carbon::now()
        ]);

        if ($upd) {
            return true;
        } else {
            return false;
        }
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
                    'photo_edit_date' =>  Carbon::now()
                ]);

                $paths[] = $imageName;
            }
        }
        return $paths;
    }

    /**
     * @param array $photoData
     * @return string
     */
    public function removePhoto(array $photoData)
    {
        $ownerPhotoCheck = DB::table('notation_photo')
            ->select('user_id', 'path_photo')
            ->where('notation_id', '=', $photoData['notation_id'])
            ->where('notation_photo_id', '=', $photoData['photo_id'])
        ->first();

        if($ownerPhotoCheck->user_id == Auth::user()->id) {
            $delete = DB::table('notation_photo')
                ->where('notation_id', '=', $photoData['notation_id'])
                ->where('notation_photo_id', '=', $photoData['photo_id'])
                ->delete();

            unlink(public_path($ownerPhotoCheck->path_photo));

            if ($delete) {
                return $ownerPhotoCheck->answer = 'success';
            } else {
                return $ownerPhotoCheck->answer = 'Ошибка удаления';
            }
        } else {
            return $ownerPhotoCheck->answer = 'Не совпадает id пользователя';
        }
    }
}
