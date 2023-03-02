<?php

namespace App\Models\Notation;

use App\Enums\Profile\ProfileEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ProfileModel;
use App\Models\Notation\VoteNotationModel;
use App\Models\Notation\NotationViewModel;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class NotationModel
 * @package App\Models\Notation
 */
class NotationModel extends Model
{
    use hasFactory;

    /** @var string  */
    protected $table = 'notations';
    /** @var string  */
    protected $primaryKey = 'notation_id';
    /** @var bool  */
    public $timestamps = false;

    /*protected $fillable = [
        'id_user', 'name_notation', 'text_notation','notation_add_date'
    ];*/

    /**
     * @param array $dataNotation
     * @return array
     */
    protected static function createNotation(Array $dataNotation)
    {

        if (Auth::check() && $dataNotation['method'] == 'add') {
            $notationId = DB::table('notations')->insertGetId([
                'id_user' =>  Auth::user()->id,
                'name_notation' =>  trim(addslashes($dataNotation['name_tema'])),
                'text_notation' =>  trim(addslashes($dataNotation['text_notation'])),
                'notation_add_date' =>  Carbon::now(),
                'notation_edit_date' => Carbon::now()
            ]);

            $expAdded = ProfileModel::expAdd('addNotation');
            return ['notationId' => $notationId, 'expAdded' => $expAdded];
        }
        else $notationId = false;
    }

    /**
     * @param int $notationId
     * @return \Illuminate\Support\Collection
     */
    protected function viewNotation(int $notationId)
    {

        $notation = DB::table('notations')
            ->select('notations.notation_id', 'notations.id_user',
                'notations.name_notation', 'notations.text_notation',
                'notations.rating','users.name', 'users.avatar',
                'notations.notation_add_date', 'np.path_photo')
            ->join('users', 'users.id', '=', 'notations.id_user')
            ->leftJoin('notation_photos AS np', 'np.notation_id', '=', 'notations.notation_id')
            ->where('notations.notation_id', '=', $notationId)
        ->get();

        if (Auth::check()) {
            $vote = DB::table('vote_notation')
                ->select('vote_notation_id')
                ->where('notation_id', '=', $notationId)
                ->where('id_user', '=', Auth::user()->id)
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

        $notationViews = DB::table('views_notation')
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
    protected function dataEditNotation(int $notationId)
    {

        $data = array();
        $data['notation'] = DB::table('notations')
            ->select('id_user','notation_id','category','name_notation','text_notation')
            ->where('notation_id', '=', $notationId)
        ->first();

        $data['notation_photos'] = DB::table('notation_photos')
            ->select('path_photo','notation_photo_id')
            ->where('notation_id', '=', $notationId)
        ->get();

        return $data;
    }

    /**
     * @param int $notationId
     * @param int $action
     * @return bool|int|null
     */
    protected function notationRating(int $notationId, int $action)
    {

        if (Auth::check()) {
            $checkRating = DB::table('vote_notation')
            ->select('vote_notation_id', 'vote')
                ->where('id_user', '=', Auth::user()->id)
                ->where('notation_id', '=', $notationId)
            ->first();

            $dbMove = null;
            if (empty($checkRating->vote_notation_id)) {
                $dbMove = DB::table('vote_notation')->insert(
                    array(
                        'id_user' => Auth::user()->id,
                        'notation_id' => $notationId,
                        'vote' => $action,
                        'vote_date' => Carbon::now()
                    )
                );
            } else {

                // checking for already set vote
                if ($checkRating->vote == 1 && $action == 1)
                    return false;

                if ($checkRating->vote == 0 && $action == 0)
                    return false;

                $dbMove = DB::table('vote_notation')
                    ->where('id_user', '=', Auth::user()->id)
                    ->where('notation_id', '=', $notationId)
                ->update([
                    'vote' => $action,
                    'vote_date' => Carbon::now()
                ]);
            }

            if($action) {
                $string = "SET `rating` =  `rating` + 1";
            }
            else  $string = "SET `rating` =  `rating` - 1";

            DB::statement("UPDATE `notations` {$string} WHERE `notation_id` =  {$notationId}");
            return $dbMove;
      }
      else return null;
    }

    /**
     * @param array $dataNotationEdit
     * @return bool
     */
    protected function notationEdit(Array $dataNotationEdit)
    {

        if (Auth::check()) {
            $upd = DB::table('notations')
            ->where('id_user', '=', Auth::user()->id)
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
    }

    /**
     * @param int $notationDelete
     * @return string[]
     */
    protected function notationDelete(int $notationDelete)
    {

        if (Auth::check())  {
            $notation = DB::table('notations')
                ->select('id_user','notation_id')
                ->where('notation_id', '=', $notationDelete)
            ->first();

            if($notation->id_user ===  Auth::user()->id) {

                $destroy = DB::table('notations')
                    ->where('notation_id', '=', $notationDelete)
                    ->where('id_user', '=',  Auth::user()->id)
                ->delete();

                if ($destroy) {

                    $data = [
                        'status'=>'1',
                        'msg'=>'success'
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
    }

    /**
     * @param array $photoData
     * @return string
     */
    protected function notationPhotoDelete(array $photoData)
    {

        $check_added_photo = DB::table('notation_photos')
            ->select('id_user', 'path_photo')
                ->where('notation_id', '=', $photoData['notation_id'])
                ->where('notation_photo_id', '=', $photoData['photo_id'])
            ->first();

        if($check_added_photo->id_user == Auth::user()->id) {
            $delete = DB::table('notation_photos')
                ->where('notation_id', '=', $photoData['notation_id'])
                ->where('notation_photo_id', '=', $photoData['photo_id'])
            ->delete();

            unlink(public_path($check_added_photo->path_photo));

            if($delete)
                return $check_added_photo->answer = 'success';
            else
                return $check_added_photo->answer = 'Ошибка удаления';
        } else {
            return $check_added_photo->answer = 'Не совпадает id пользователя';
        }
    }

    /**
     * @param $request
     * @return array
     */
    protected static function notationAddPhotos($request)
    {

        $paths = array();
        if ($request->hasFile('images')) {

            $files = $request->file('images');
            foreach($files as $file) {
                $imageName = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path("img/notation_photos/{$request->notation_id}"), $imageName);

                $ins =
                DB::table('notation_photos')->insert(
                    array('id_user' => Auth::user()->id,
                        'notation_id' => $request->notation_id,
                        'path_photo' => "img/notation_photos/{$request->notation_id}/{$imageName}",
                        'photo_edit_date' =>  Carbon::now())
                );

                $paths[] = $imageName;
            }
        }

        return $paths;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class, 'id', 'id_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function notationViews() {
        return $this->hasOne(NotationViewModel::class, 'notation_id', 'notation_id');
    }
}
