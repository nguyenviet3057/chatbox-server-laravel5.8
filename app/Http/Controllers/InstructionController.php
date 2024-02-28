<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class InstructionController extends Controller
{
    private $assistant_id;
    private $openai_key;

    public function __construct() {
        $this->assistant_id = "asst_XJdELsXpLgGLPRom0w5H2d4z";
        $this->openai_key = "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";
    }

    function index() {
        try {
            $faqs = DB::table('instructions')
                ->where('question', '!=', null)
                ->where('answer', '!=', null)
                ->get();
            $instructions = DB::table('instructions')
                ->where('instruction', '!=', null)
                ->get();

            return view('instruction.index', compact('faqs', 'instructions'));
        } catch (Exception $ex) {
            return abort(500);
        }
    }

    function faqAdd() {
        return view('instruction.faq.add');
    }

    function faqAddSubmit(Request $request) {
        $request->validate([
            'question' => 'required',
            'answer' => 'required'
        ]);

        $question = $request->question;
        $answer = $request->answer;

        try {
            DB::table('instructions')->insert([
                'question' => $question,
                'answer' => $answer,
                'user_id' => Auth::id()
            ]);
            if ($this->updateInstructionsAssistant()) return redirect()->route('faqs');
            else {
                return abort(500, "Add Q&A failed");
            }
        } catch (Exception $ex) {
            return abort(500);
        }
    }

    function faqEdit($id) {
        try {
            $data = DB::table('instructions')->where('user_id', Auth::id())->where('id', $id)->first();
            return view('instruction.faq.edit', compact('data'));
        } catch (Exception $ex) {
            return abort(500);
        }
    }

    function faqEditSubmit(Request $request) {
        $request->validate([
            'id' => 'required',
            'question' => 'required',
            'answer' => 'required'
        ]);

        $id = $request->id;
        $question = $request->question;
        $answer = $request->answer;

        try {
            DB::table('instructions')->where('user_id', Auth::id())->where('id', $id)->update([
                'question' => $question,
                'answer' => $answer
            ]);
            if ($this->updateInstructionsAssistant()) return redirect()->route('faqs');
            else {
                return abort(500, "Update Q&A failed");
            }
        } catch (Exception $ex) {
            return abort(500);
        }
    }

    function faqDeleteSubmit(Request $request) {
        $request->validate([
            'id' => 'required'
        ]);

        $id = $request->id;
        try {
            DB::table('instructions')->where('user_id', Auth::id())->delete($id);
            if ($this->updateInstructionsAssistant()) return $this->responseMessage(1, 'Delete Q&A success');
            else {
                $res = new stdClass();
                $res->status = 0;
                $res->msg = "Delete Q&A failed";
                $res->data = null;
        
                return response()->json($res, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        } catch (Exception $ex) {
            $res = new stdClass();
                $res->status = 0;
                $res->msg = "Delete Q&A failed";
                $res->data = null;
        
                return response()->json($res, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    function instructionAdd() {
        return view('instruction.add');
    }

    function instructionAddSubmit(Request $request) {
        $request->validate([
            'instruction' => 'required',
        ]);

        $instruction = $request->instruction;

        try {
            DB::table('instructions')->insert([
                'instruction' => $instruction,
                'user_id' => Auth::id()
            ]);
            if ($this->updateInstructionsAssistant()) return redirect()->route('instructions');
            else {
                return abort(500, "Add instruction failed");
            }
        } catch (Exception $ex) {
            return abort(500);
        }
    }

    function instructionEdit($id) {
        try {
            $data = DB::table('instructions')->where('user_id', Auth::id())->where('id', $id)->first();
            return view('instruction.edit', compact('data'));
        } catch (Exception $ex) {
            return abort(500);
        }
    }

    function instructionEditSubmit(Request $request) {
        $request->validate([
            'id' => 'required',
            'instruction' => 'required',
        ]);

        $id = $request->id;
        $instruction = $request->instruction;

        try {
            DB::table('instructions')->where('user_id', Auth::id())->where('id', $id)->update([
                'instruction' => $instruction,
            ]);
            if ($this->updateInstructionsAssistant()) return redirect()->route('instructions');
            else {
                return abort(500, "Update instruction failed");
            }
        } catch (Exception $ex) {
            return abort(500);
        }
    }

    function instructionDeleteSubmit(Request $request) {
        $request->validate([
            'id' => 'required'
        ]);

        $id = $request->id;
        try {
            DB::table('instructions')->where('user_id', Auth::id())->delete($id);
            if ($this->updateInstructionsAssistant()) return $this->responseMessage(1, 'Delete instruction success');
            else {
                $res = new stdClass();
                $res->status = 0;
                $res->msg = "Delete instruction failed";
                $res->data = null;
        
                return response()->json($res, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        } catch (Exception $ex) {
            $res = new stdClass();
            $res->status = 0;
            $res->msg = "Delete instruction failed";
            $res->data = null;
    
            return response()->json($res, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
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

    public function updateInstructionsAssistant() {
        $system_command = "";
        try {
            $instructions = DB::table('instructions')->where('user_id', Auth::id())
                                ->where('instruction', '!=', null)
                                ->get();
            foreach($instructions as $instruction) {
                $system_command .= "### ".$instruction->instruction."\n";
            }
            
            $faqs = DB::table('instructions')->where('user_id', Auth::id())
                                ->where('question', '!=', null)
                                ->where('answer', '!=', null)
                                ->get();
            if (sizeof($faqs) > 0) {
                $system_command .= "### Ask and answer following these Q&A pairs:\n";
                foreach($faqs as $faq) {
                    $system_command .= "* ".$faq->question.": ".$faq->answer."\n";
                }
            }

            if ($system_command != "") {
                $headers = [
                    'Authorization: Bearer '.$this->openai_key,
                    'Content-Type: application/json; charset=utf-8',
                    'OpenAI-Beta: assistants=v1',
                ];
        
                // Add message to thread
                $apiUrl = 'https://api.openai.com/v1/assistants/'.$this->assistant_id;
                $postData = [
                    'instructions' => $system_command,
                ];
                $add_message = $this->makeRequest($apiUrl, "POST", $headers, $postData);

                if ($add_message == null) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return false;
            }
        } catch (Exception $ex) {
            return false;
        }
    }
}