<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DateTimeZone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class ChatTestController extends Controller
{
    public function createChat(Request $request) {
        $request->validate([
            'name' => 'required | max:50',
            'phone' => 'required | size:10',
            'gender' => 'required'
        ]);

        $name = $request->name;
        $phone = $request->phone;
        $is_male = $request->gender == 1 ?? 0;

        $customer = new stdClass();
        $customer->uid = DB::table('customers_info')->insertGetId([
            'name' => $name,
            'phone' => $phone,
            'is_male' => $is_male
        ]);
        $customer->name = $name;
        $customer->phone = $phone;
        $customer->gender = $is_male;
        $customer->avatar_url = $is_male == 1 ? "/assets/image/male.jpg" : "/assets/image/female.jpg";

        $headers = [
            'Authorization: Bearer '.env('OPENAI_KEY', ''),
            'Content-Type: application/json; charset=utf-8',
            'OpenAI-Beta: assistants=v1',
        ];

        $apiUrl = 'https://api.openai.com/v1/threads';
        $thread = $this->makeRequest($apiUrl, "POST", $headers);
        if ($thread == null) return $this->responseMessage(0, 'Create thread failed');
        $thread = json_decode($thread);

        return $this->responseMessage(1, 'Create chat success', [
            "customer" => $customer,
            "thread_id" => $thread->id
        ]);
    }

    public function getMessage(Request $request) {
        $message = $request->message;

        $thread = $this->createThread();
        if ($thread == null) return abort(403, "Create thread failed");
        $thread = json_decode($thread);
        $thread->id = 'thread_peHiWIxB9aDbvyeEbqUgV5eh';

        $add_message = $this->addMessageToThread($thread->id, $message);
        if ($add_message == null) return abort(403, "Add message to thread failed");

        $run = $this->createRun($thread->id);
        if ($run == null) return abort(403, "Create run failed");
        
        $time = now('Asia/Ho_Chi_Minh');
        while (now('Asia/Ho_Chi_Minh')->diffInSeconds($time) < 90 && json_decode($run)->status != 'completed') {
            $run = $this->retrieveRun($thread->id, json_decode($run)->id);
            if ($run == null) return abort(403, "Retrieve run failed");
            // return $run;
            if (json_decode($run)->status == 'failed' || json_decode($run)->status == 'expired' || json_decode($run)->status == 'cancelled') {
                return abort(403, "Run status error");
            }
        }

        if (json_decode($run)->status != 'completed') return abort(403, "Run error");
        else {
            $message_list = $this->getMessageList($thread->id);
            if ($message_list == null) return abort(403, "Get message list failed");
        }
        
        $messages = [];
        foreach (json_decode($message_list)->data as $key => $message) {
            array_push($messages, $message->content[0]->text->value);
        }

        echo $thread->id.PHP_EOL;
        return response()->json([
            'latest_message' => json_decode($message_list)->data[0]->content[0]->text->value, 
            'message_list' => $messages
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function createThread() {
        $apiUrl = 'https://api.openai.com/v1/threads';

        $postData = [];

        $headers = [
            'Authorization: Bearer '.env('OPENAI_KEY', ''),
            'Content-Type: application/json; charset=utf-8',
            'OpenAI-Beta: assistants=v1',
        ];

        try {
            $ch = curl_init($apiUrl);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, true);

            if (!empty($postData)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            }

            $data = curl_exec($ch);

            if (curl_errno($ch)) {
                return null;
            }

            curl_close($ch);

            return $data;
        } catch (Exception $ex) {
            return null;
        }
    }

    private function addMessageToThread($thread_id, $message='') {
        if ($thread_id == null) return null;
        $apiUrl = 'https://api.openai.com/v1/threads/'.$thread_id.'/messages';

        $postData = [
            'role' => 'user',
            'content' => $message,
        ];

        $headers = [
            'Authorization: Bearer '.env('OPENAI_KEY', ''),
            'Content-Type: application/json; charset=utf-8',
            'OpenAI-Beta: assistants=v1',
        ];

        try {
            $ch = curl_init($apiUrl);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, true);

            if (!empty($postData)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            }

            $data = curl_exec($ch);

            if (curl_errno($ch)) {
                return null;
            }

            curl_close($ch);

            return $data;
        } catch (Exception $ex) {
            return null;
        }
    }

    private function createRun($thread_id) {
        if ($thread_id == null) return null;
        $apiUrl = 'https://api.openai.com/v1/threads/'.$thread_id.'/runs';

        $postData = [
            'assistant_id' => env('ASSISTANT_ID', '')
        ];

        $headers = [
            'Authorization: Bearer '.env('OPENAI_KEY', ''),
            'Content-Type: application/json; charset=utf-8',
            'OpenAI-Beta: assistants=v1',
        ];

        try {
            $ch = curl_init($apiUrl);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, true);

            if (!empty($postData)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            }

            $data = curl_exec($ch);

            if (curl_errno($ch)) {
                return null;
            }

            curl_close($ch);

            return $data;
        } catch (Exception $ex) {
            return null;
        }
    }

    private function retrieveRun($thread_id, $run_id) {
        if ($thread_id == null || $run_id == null) return null;
        $apiUrl = 'https://api.openai.com/v1/threads/'.$thread_id.'/runs/'.$run_id;

        $postData = [];

        $headers = [
            'Authorization: Bearer '.env('OPENAI_KEY', ''),
            'Content-Type: application/json; charset=utf-8',
            'OpenAI-Beta: assistants=v1',
        ];

        try {
            $ch = curl_init($apiUrl);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            // curl_setopt($ch, CURLOPT_POST, true);

            if (!empty($postData)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            }

            $data = curl_exec($ch);

            if (curl_errno($ch)) {
                return null;
            }

            curl_close($ch);

            return $data;
        } catch (Exception $ex) {
            return null;
        }
    }

    private function getMessageList($thread_id, $limit=20, $order='desc') {
        if ($thread_id == null) return null;
        $apiUrl = 'https://api.openai.com/v1/threads/'.$thread_id.'/messages?limit='.$limit.'&order='.$order;

        $postData = [];

        $headers = [
            'Authorization: Bearer '.env('OPENAI_KEY', ''),
            'Content-Type: application/json; charset=utf-8',
            'OpenAI-Beta: assistants=v1',
        ];

        try {
            $ch = curl_init($apiUrl);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            // curl_setopt($ch, CURLOPT_POST, true);

            if (!empty($postData)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            }

            $data = curl_exec($ch);

            if (curl_errno($ch)) {
                return null;
            }

            curl_close($ch);

            return $data;
        } catch (Exception $ex) {
            return null;
        }
    }
}
