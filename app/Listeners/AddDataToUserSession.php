<?php namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Session;

class AddDataToUserSession
{
  public function handle(Login $loginEvent)
  {
    if(!empty($loginEvent->user->avatar))
      Session::put('avatar', $loginEvent->user->avatar);
    else  Session::put('avatar', '/img/avatar/no_avatar.png');
  }
}