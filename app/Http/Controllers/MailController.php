<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Arr;

class MailController extends Controller
{
    public static function auth($user){
        $details = [
            'username'=>$user['username'],
            'email'=>$user['email'],
            'link'=>env('APP_URL').'/'.$user['email'].'/'.$user['username'].'/'.$user['id'].'/'.$user['email_otp'],
        ];
        Mail::to($user['email'])->send(new SendMail($details,'welcome'));
        return $details;
    }
    public function email(){
        $details = [
            'username'=>'arwani',
            'email'=>'arwanimaulana89@gmail.com',
        ];
        Mail::to($details['email'])->send(new SendMail($details,'change_password'));
        return $details;

        Mail::send("mail.forgot", $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
            ->subject("Laravel Test Mail");
            $message->from("kelompok1clover@gmail.com","email");
        });
        // $to_name = 'aronei';
        // $to_email = 'arwanimaulana89@gmail.com';
        // $data = array("name"=>"Ogbonna Vitalis(sender_name)", "body" => "A test mail");
        // Mail::send("emails.mail", $data, function($message) use ($to_name, $to_email) {
        // $message->to($to_email, $to_name)
        // ->subject("Laravel Test Mail");
        // $message->from("kelompok1clover@gmail.com","email");
        // });
    }
    public static function change_password($user){
        $details = [
            'username'=>$user['username'],
            'email'=>$user['email'],
        ];
        Mail::to($user['email'])->send(new SendMail($details,'change_password'));
        return "mail sended";
    }
    public static function forgot_password($user,$otp){
        $details = [
            'username'=>$user['username'],
            'email'=>$user['email'],
            'otp'=>$otp
        ];
        Mail::to($user['email'])->send(new SendMail($details,'forgot_password'));
        return "mail sended";
    }

    public static function mobileSendEmail($user)
    {
        $details = [
            'username'=>$user['username'],
            'email'=>$user['email'],
            'otp'=>$user['email_otp']
        ];
        Mail::to($user['email'])->send(new SendMail($details,'forgot_password'));
        return "mail sended";
    }
}
