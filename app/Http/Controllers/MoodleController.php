<?php

namespace App\Http\Controllers;

use \Curl\Curl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MoodleController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        //$this->middleware(['permission:moodle.login']);
    }

    /** Login to Moodle via token */
    public function moodlelogin()
    {

        $token = Config::where('name', 'moodle_token')->first()->value;
        $domainname = Config::where('name', 'moodle_url')->first()->value;

        $email = backpack_user()->email;
        $firstname = backpack_user()->firstname;
        $lastname = backpack_user()->lastname;

        function getloginurl($email, $firstname, $lastname, $token, $domainname)
        {

            $functionname = 'auth_userkey_request_login_url';

            $param = [
                'user' => [
                    'email' => $email,
                    'firstname' => $firstname, // You will not need this parameter, if you are not creating/updating users
                    'lastname'  => $lastname, // You will not need this parameter, if you are not creating/updating users
                    'username'  => $email, 
                ]
            ];

            $serverurl = $domainname . '/webservice/rest/server.php' . '?wstoken=' . $token . '&wsfunction=' . $functionname . '&moodlewsrestformat=json';

            $curl = new Curl;

            try {
                $resp = $curl->post($serverurl, $param);
                $resp = json_decode($resp->response);
                
                if ($resp && !empty($resp->loginurl)) {
                    $loginurl = $resp->loginurl;
                }
            } catch (Exception $ex) {
                return false;
            }

            if (!isset($loginurl)) {
                return false;
            }

            return $loginurl;
        }

        Log::info(backpack_user()->id . ' generated a Moodle login token');


        return redirect(getloginurl($email, $firstname, $lastname, $token, $domainname));

    }

}
