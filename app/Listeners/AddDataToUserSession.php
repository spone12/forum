<?php namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Session;

class AddDataToUserSession
{
  public function handle(Login $loginEvent)
  {
    Session::put('avatar', $loginEvent->user->avatar);
  }
}