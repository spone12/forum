<?php namespace App\Listeners;

use App\Enums\Profile\ProfileEnum;
use Illuminate\Auth\Events\Login;
use Session;

class AddDataToUserSession
{
  public function handle(Login $loginEvent)
  {

      $avatarUrl = !empty($loginEvent->user->avatar) ? $loginEvent->user->avatar : ProfileEnum::NO_AVATAR;
      Session::put('avatar', $avatarUrl);
  }
}
