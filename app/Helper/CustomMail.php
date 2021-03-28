<?php

namespace App\Helper;
use Mail;

class CustomMail
{
    public static function sendInvite($email, $subject, $message)
    {
      Mail::queue('emails.welcome', ['key' => 'value'], function($message)
      {
          $message->to('arvindpdh73@gmail.com', 'John Smith')->subject('Welcome!');
      });
    }
}
