<?php

namespace App\Http\Controllers;
set_time_limit(180);

use Exception;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use stdClass;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function responseMessage($status=0, $message='Default error', $data=null) {
        $response = new stdClass();
        $response->status = $status;
        $response->message = $message;
        $response->data = $data;

        return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    public function makeRequest($url, $method="GET", $headers=[], $data=null) {
        try {
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_TIMEOUT_MS, 180000);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            if (strtoupper($method)=="POST") {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data ?? []));
            }

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                return null;
            }

            curl_close($ch);

            return $response;
        } catch (Exception $ex) {
            return null;
        }
    }

    public function uploadImage(Request $request) {
        return $request;
    }
}
