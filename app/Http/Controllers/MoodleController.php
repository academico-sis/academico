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

        $token = env('MOODLE_TOKEN');
        $domainname = env('MOODLE_URL');

        $email = backpack_user()->email;

        function getloginurl($email, $token, $domainname)
        {

            $functionname = 'auth_userkey_request_login_url';

            $param = [
                'user' => [
                    'email' => $email
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


        return redirect(getloginurl($email, $token, $domainname));

    }

}
