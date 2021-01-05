<?php
 
namespace App;
 
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
 
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
 
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name', 'phone', 'password','otp',
    ];
 
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public static function randomNumber($length_of_number) 
    { 
      
      $str_result = '0123456789'; 
      return substr(str_shuffle($str_result),  
                         0, $length_of_number); 
    } 

    public  static function SendOtp($otp,$number)
    {
          $description=urlencode($otp);
          $POSTdata = 'http://103.16.101.52:8080/sendsms/bulksms?username=nex-tigerpay&password=tiger&type=0&dlr=1&destination='.$number.'&source=TigrPy&message='.$description;
          $curlObj = curl_init();
          curl_setopt($curlObj, CURLOPT_URL, $POSTdata);
          curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
          $response = curl_exec($curlObj);
          return $response;
    }
}