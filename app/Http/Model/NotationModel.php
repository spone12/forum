<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class NotationModel extends Model
{
    protected $table = 'notations';
    /*protected $fillable = [
        'id_user', 'name_notation', 'text_notation','notation_add_date'
    ];*/
    public $timestamps = false;

    protected static function ins_notation(Array $data_notation)
    {
        if (Auth::check()) 
        {
            $user = Auth::user()->id;

            $ins =  
            DB::table('notations')->insert(
                array('id_user' => $user, 
                    'name_notation' =>  trim(addslashes($data_notation['name_tema'])), 
                    'text_notation' =>  trim(addslashes($data_notation['text_notation'])),
                    'notation_add_date' =>  Carbon::now(),
                    'notation_edit_date' => Carbon::now())
            );
          
        }
        else $ins = false;

        return $ins;
        //return $ins;
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
                    ->select('notations.notation_id', 'notations.id_user',
                            'notations.name_notation', 'notations.text_notation',
                            'notations.rating','users.name', 'users.avatar',
                            'notations.notation_add_date','vote_notation.vote')
                    ->where('notations.notation_id', '=', $notation_id)
                    ->where('vote_notation.id_user', '=', Auth::user()->id)
                    ->get();
            }
            
        } 
        
        if(empty($notation))
        {
            $notation = DB::table('notations')
            ->join('users', 'users.id', '=', 'notations.id_user')
            ->join('notation_photos AS np', 'np.notation_id', '=', 'notations.notation_id')
            ->select('notations.notation_id', 'notations.id_user',
                    'notations.name_notation', 'notations.text_notation',
                    'notations.rating','users.name', 'users.avatar',
                    'notations.notation_add_date', 'np.path_photo')
            ->where('notations.notation_id', '=', $notation_id)->get();
        }

            if($notation)
            {
                $notation[0]->text_notation = str_ireplace(array("\r\n", "\r", "\n"), '<br/>&emsp;', $notation[0]->text_notation);
                
                if(is_null($notation[0]->avatar))
                    $notation[0]->avatar = 'img/avatar/no_avatar.png';

                return $notation;
            }
    
    }

    protected function edit_notation_access(int $notation_id)
    {
        $notation = DB::table('notations')
        ->select('id_user','notation_id','category','name_notation','text_notation')
        ->where('notation_id', '=', $notation_id)->first();

        return $notation;
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

        if(empty($check_rating->vote_notation_id))
        {
                $ins = DB::table('vote_notation')->insert(
                array('id_user' => Auth::user()->id, 
                    'notation_id' =>  (INT)$notation_id, 
                    'vote' => $action,
                    'vote_date' => Carbon::now())
                );

                if($action == 1)
                {
                    $string = "SET `rating` =  `rating` + 1";
                }
                else  $string = "SET `rating` =  `rating` - 1";

            $upd_notation = DB::statement("UPDATE `notations` {$string}
                            WHERE `notation_id` =  {$notation_id}");


                return $ins;
        }
        else 
        {
                if($check_rating->vote == 1 && $action == 1)
                    return 0;
                
                if($check_rating->vote == 0 && $action == 0)
                    return 0;
                
                $upd = DB::table('vote_notation')
                ->where('id_user', '=', Auth::user()->id)
                ->where('notation_id', '=', $notation_id)
                ->update(['vote' => $action,
                        'vote_date' => Carbon::now()]);
                
                if($action == 1)
                {
                    $string = "SET `rating` =  `rating` + 1";
                }
                else  $string = "SET `rating` =  `rating` - 1";

            $upd_notation = DB::statement("UPDATE `notations` {$string}
                            WHERE `notation_id` =  {$notation_id}");

                //$back['upd_not'] =  $upd_notation;
            // $back['upd'] =  $upd;
                return $upd;
        }
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
                        $data=[
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

    protected function notation_add_photo($request)
    {
        $paths = array();
        if($request->hasFile('images'))
        {
            $files = $request->file('images');

            foreach($files as $file)
            {
                //$imageName = time() . '.' . $image->getClientOriginalExtension();
                $imageName = $file->getClientOriginalName();
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
