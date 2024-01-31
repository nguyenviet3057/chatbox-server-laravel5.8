<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DateTimeZone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class ChatController extends Controller
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
        $customer->id = str_pad(strval(DB::table('customers_info')->insertGetId([
            'name' => $name,
            'phone' => $phone,
            'is_male' => $is_male
        ])), 6, "0", STR_PAD_LEFT);
        $customer->name = $name;
        $customer->phone = $phone;
        $customer->gender = $is_male;
        $customer->avatar_url = $is_male == 1 ? "https://cdn.gokisoft.com/uploads/stores/97/2024/01/563698090.jpg" : "https://cdn.gokisoft.com/uploads/stores/97/2024/01/18298846.jpg";

        $system = new stdClass();
        $system->id = "000097";
        $system->name = "Hỗ trợ khách hàng";
        $system->phone = "0123456789";
        $system->avatar_url = "https://cdn.gokisoft.com/uploads/stores/97/2024/01/583143640.jpg";

        // $headers = [
        //     'Authorization: Bearer '.env('OPENAI_KEY', ''),
        //     'Content-Type: application/json; charset=utf-8',
        //     'OpenAI-Beta: assistants=v1',
        // ];

        // // Create thread
        // $apiUrl = 'https://api.openai.com/v1/threads';
        // $thread = $this->makeRequest($apiUrl, "POST", $headers);
        // if ($thread == null) return $this->responseMessage(0, 'Create thread failed');
        // $thread = json_decode($thread);

        return $this->responseMessage(1, 'Create chat success', [
            "customer" => $customer,
            "system" => $system,
            // "thread_id" => $thread->id
            "thread_id" => "NO BOT YET"
        ]);
    }

    public function chatBot(Request $request) {
        $request->validate([
            'message' => 'required',
            'thread_id' => 'required'
        ]);

        $message = $request->message;
        $thread_id = $request->thread_id;

        $limit = 20;
        $order = "desc";

        $headers = [
            'Authorization: Bearer '.env('OPENAI_KEY', ''),
            'Content-Type: application/json; charset=utf-8',
            'OpenAI-Beta: assistants=v1',
        ];

        // Add message to thread
        $apiUrl = 'https://api.openai.com/v1/threads/'.$thread_id.'/messages';
        $postData = [
            'role' => 'user',
            'content' => $message,
        ];
        $add_message = $this->makeRequest($apiUrl, "POST", $headers, $postData);
        if ($add_message == null) return $this->responseMessage(0, 'Add message to thread failed');

        // Create run
        $apiUrl = 'https://api.openai.com/v1/threads/'.$thread_id.'/runs';
        $postData = [
            'assistant_id' => env('ASSISTANT_ID', '')
        ];
        $run = $this->makeRequest($apiUrl, "POST", $headers, $postData);
        if ($run == null) return $this->responseMessage(0, 'Create run failed');
        $run_id = json_decode($run)->id;
        
        // Retrieve run
        $time = now('Asia/Ho_Chi_Minh');
        while (now('Asia/Ho_Chi_Minh')->diffInSeconds($time) < 90 && json_decode($run)->status != 'completed') {
            $apiUrl = 'https://api.openai.com/v1/threads/'.$thread_id.'/runs/'.$run_id;
            $run = $this->makeRequest($apiUrl, "GET", $headers);
            if ($run == null) return $this->responseMessage(0, 'Retrieve run failed');
            $run_id = json_decode($run)->id;

            if (json_decode($run)->status == 'failed' || json_decode($run)->status == 'expired' || json_decode($run)->status == 'cancelled') {
                return $this->responseMessage(0, 'Run status error');
            }
        }

        if (json_decode($run)->status != 'completed') return $this->responseMessage(0, 'Run error');
        else {
            // Get message list
            $apiUrl = 'https://api.openai.com/v1/threads/'.$thread_id.'/messages?limit='.$limit.'&order='.$order;
            $message_list = $this->makeRequest($apiUrl, "GET", $headers);
            if ($message_list == null) return $this->responseMessage(0, 'Get message list failed');
        }
        
        $messages = [];
        foreach (json_decode($message_list)->data as $key => $message) {
            array_push($messages, $message->content[0]->text->value);
        }

        return $this->responseMessage(1, 'Create chat success', [
            'latest_message' => json_decode($message_list)->data[0]->content[0]->text->value, 
            'message_list' => $messages
        ]);
    }

    function index() {
        $user = Auth::user();
        $user_data = new stdClass();
        // $user_data->id = $user->id;
        $user_data->id = "000097";
        // $user_data->name = $user->name;
        $user_data->name = "Hỗ trợ khách hàng";
        $user_data->email = $user->email;
        $user_data->phone = "88886666";
        $user_data->gender = 1;
        $user_data->avatarUrl = "/assets/image/system.jpg";

        return view('chat.index', compact("user_data"));
    }
}
