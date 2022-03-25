<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Model\ProfileModel;


class NotationModel extends Model
{
    protected $table = 'notations';

    /*protected $fillable = [
        'id_user', 'name_notation', 'text_notation','notation_add_date'
    ];*/

    public $timestamps = false;

    protected static function createNotation(Array $dataNotation)
    {
        if (Auth::check() && $dataNotation['method'] == 'add') 
        {
            $notationId = DB::table('notations')->insertGetId(
                [
                    'id_user' =>  Auth::user()->id,
                    'name_notation' =>  trim(addslashes($dataNotation['name_tema'])), 
                    'text_notation' =>  trim(addslashes($dataNotation['text_notation'])),
                    'notation_add_date' =>  Carbon::now(),
                    'notation_edit_date' => Carbon::now()
                ]
            );

            $expAdded = ProfileModel::expAdd('addNotation');

            return ['notationId' => $notationId, 'expAdded' => $expAdded];
        }
        else $notationId = false;
    }

    protected function view_notation(int $notation_id)
    {
        if (Auth::check())
        {
            $is_vote = DB::table('vote_notation')
            ->select('vote_notation_id')
            ->where('notation_id', '=', $notation_id)
            ->where('id_user', '=', Auth::user()->id)->get();
            
            if(!empty($is_vote[0]->vote_notation_id))
            {
                $notation = DB::table('notations')
                    ->join('users', 'users.id', '=', 'notations.id_user')
                    ->join('vote_notation', 'vote_notation.notation_id', '=', 'notations.notation_id')
                    ->leftJoin('notation_photos AS np', 'np.notation_id', '=', 'notations.notation_id')
                    ->select('notations.notation_id', 'notations.id_user',
                            'notations.name_notation', 'notations.text_notation',
                            'notations.rating','users.name', 'users.avatar',
                            'notations.notation_add_date','vote_notation.vote','np.path_photo')
                    ->where('notations.notation_id', '=', $notation_id)
                    ->where('vote_notation.id_user', '=', Auth::user()->id)
                ->get();
            }
        } 
        
        if(empty($notation))
        {
            $notation = DB::table('notations')
            ->select('notations.notation_id', 'notations.id_user',
                    'notations.name_notation', 'notations.text_notation',
                    'notations.rating','users.name', 'users.avatar',
                    'notations.notation_add_date', 'np.path_photo')
            ->join('users', 'users.id', '=', 'notations.id_user')
            ->leftJoin('notation_photos AS np', 'np.notation_id', '=', 'notations.notation_id')
            ->where('notations.notation_id', '=', $notation_id)->get();
        }

            if($notation)
            {
                $notation[0]->text_notation = str_ireplace(array("\r\n", "\r", "\n"), '<br/>&emsp;', $notation[0]->text_notation);
                
                if(is_null($notation[0]->avatar))
                    $notation[0]->avatar = 'img/avatar/no_avatar.png';

                    $notation_views = DB::table('views_notation')
                        ->select('counter_views','view_date')
                         ->where('notation_id', '=', $notation_id)
                            ->orderBy('view_date')
                        ->get();

                        $list = array();
                        $sum_v = 0;

                        foreach ($notation_views as $v) 
                        {
                            $sum_v += $v->counter_views;
                            $list[] = array('full_date' => date('d.m.Y', strtotime($v->view_date)),
                                            'sum_views' => $sum_v,
                                            'value' => $v->counter_views);
        
                        }
                        $notation['graph'] = json_encode($list);
             
                return $notation;
            }
    
    }

    protected function data_edit_notation(int $notation_id)
    {
        $data = array();
        $data['notation'] = DB::table('notations')
                            ->select('id_user','notation_id','category','name_notation','text_notation')
                            ->where('notation_id', '=', $notation_id)->first();

        $data['notation_photos'] = DB::table('notation_photos')
                                    ->select('path_photo','notation_photo_id')
                                    ->where('notation_id', '=', $notation_id)->get();

        return $data;
    }

    protected function notation_rating(int $notation_id, int $action)
    {
        if (Auth::check())
        {
            $check_rating = DB::table('vote_notation')
                            ->select('vote_notation_id', 'vote')
                                ->where('id_user', '=', Auth::user()->id)
                                ->where('notation_id', '=', $notation_id)
                            ->first();

            $dbMove = null;
            if(empty($check_rating->vote_notation_id))
            {
                $dbMove = DB::table('vote_notation')->insert(
                    array(
                        'id_user' => Auth::user()->id, 
                        'notation_id' =>  (INT)$notation_id, 
                        'vote' => $action,
                        'vote_date' => Carbon::now()
                    )
                );
            }
            else 
            {
                if($check_rating->vote == 1 && $action == 1)
                    return 0;
                
                if($check_rating->vote == 0 && $action == 0)
                    return 0;
                
                $dbMove = DB::table('vote_notation')
                    ->where('id_user', '=', Auth::user()->id)
                    ->where('notation_id', '=', $notation_id)
                ->update([
                            'vote' => $action,
                            'vote_date' => Carbon::now()
                        ]);
            }

            if($action) {
                $string = "SET `rating` =  `rating` + 1";
            }
            else  $string = "SET `rating` =  `rating` - 1";

            DB::statement("UPDATE `notations` {$string} WHERE `notation_id` =  {$notation_id}");

            return $dbMove;
      }
      else return null;
       
    }

    protected function notation_edit(Array $data_notation_edit)
    {
        if (Auth::check())
        {
            $upd = DB::table('notations')
            ->where('id_user', '=', Auth::user()->id)
            ->where('notation_id', '=', $data_notation_edit['notation_id'])
            ->update(['name_notation' =>  $data_notation_edit['name_tema'], 
                      'text_notation' =>  $data_notation_edit['text_notation'],
                      'notation_edit_date' => Carbon::now()]);

            if($upd)
                return true;
            else return false;
        }   
       
    }

    protected function notation_delete(int $notation_delete)
    {
        if (Auth::check())
        {
            $notation = DB::table('notations')
            ->select('id_user','notation_id')
            ->where('notation_id', '=', $notation_delete)->first();

            if($notation->id_user ===  Auth::user()->id)
            {
                $destroy = DB::table('notations')
                ->where('notation_id', '=', $notation_delete)
                ->where('id_user', '=',  Auth::user()->id)->delete();

                    if ($destroy)
                    {
                        $data =
                                [
                                    'status'=>'1',
                                    'msg'=>'success'
                                ];

                    }else{

                        $data=[
                            'status'=>'0',
                            'msg'=>'fail'
                        ];

                    }
                    return $data;
            }
        }   
    }

    protected function notationPhotoDelete(array $photo_data)
    {
        $check_added_photo = DB::table('notation_photos')
            ->select('id_user', 'path_photo')
                ->where('notation_id', '=', $photo_data['notation_id'])
                ->where('notation_photo_id', '=', $photo_data['photo_id'])
            ->first();

            if($check_added_photo->id_user == Auth::user()->id)
            {
                $delete = DB::table('notation_photos')
                    ->where('notation_id', '=', $photo_data['notation_id'])
                    ->where('notation_photo_id', '=', $photo_data['photo_id'])
                ->delete();

                unlink(public_path($check_added_photo->path_photo));

                if($delete)
                    return $check_added_photo->answer = 'success';
                else
                    return $check_added_photo->answer = 'Ошибка удаления';
            }
            else 
            {    
                return $check_added_photo->answer = 'Не совпадает id пользователя';
            }
    }

    protected static function notationAddPhotos($request)
    {
        $paths = array();
        if($request->hasFile('images'))
        {
            $files = $request->file('images');

            foreach($files as $file)
            {
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
}
