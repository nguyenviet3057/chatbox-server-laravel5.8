<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use stdClass;
use ZipArchive;

class InstructionController extends Controller
{
    private $assistant_id;
    private $openai_key;
    private $except_tables;

    public function __construct() {
        $this->assistant_id = "asst_XJdELsXpLgGLPRom0w5H2d4z";
        $this->openai_key = "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";
        $this->except_tables = [
            'users',
            'password_resets',
            'migrations',
        ];
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

            $tables = collect(DB::select('SHOW TABLES'))->pluck('Tables_in_chatmodule')->toArray();

            $structure = [];

            $tables = array_diff($tables, $this->except_tables);

            foreach ($tables as $table) {
                $structure[$table] = [];
                $columns = Schema::getColumnListing($table);
                
                foreach ($columns as $column) {
                    array_push($structure[$table], $column);
                }            
            }

            return view('instruction.index', compact('faqs', 'instructions', 'structure'));
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

    function getDatabase(Request $request) {
        $request->validate([
            'table_name' => 'required'
        ]);

        $table_name = $request->table_name;

        try {
            $data = DB::table($table_name)->get();

            return response()->json([
                'status' => 1,
                'msg' => 'Get data from table ' . $table_name . ' successfully!',
                'data' => $data
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'status' => 0,
                'msg' => 'Error when collecting data',
            ]);
        }

        // $tables = collect(DB::select('SHOW TABLES'))->pluck('Tables_in_chatmodule')->toArray();
        // $file_structure = 'database_structure.sql';
        // $database_structure = '';
        // $database_data = [];
        // $zip_file = 'database.zip';

        // $zip = new ZipArchive;
        // $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // $tables = array_diff($tables, $this->except_tables);
        // foreach ($tables as $table) {
        //     $table_structure = DB::select("SHOW CREATE TABLE $table");
            
        //     $database_structure .= $table_structure[0]->{"Create Table"} . ";\n\n";
        // }
        // $zip->addFromString($file_structure, $database_structure);

        // foreach ($tables as $table) {
        //     $table_data = DB::table($table)->get();
        
        //     $database_data[$table] = $table_data;
        //     $zip->addFromString($table.'.json', json_encode($table_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        // }

        // $zip->close();

        // return response()->file($zip_file, ['Content-Disposition' => `inline; filename="$zip_file"`])->deleteFileAfterSend(true);
    }
}
