<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

class GoogleSheetController extends BaseController
{
    public function index() {
        $service = new Google_Service_Script($this->client);

        $scriptId = 'M8oKK7ixJUmCUAywpTmOM31fLgBYhn53P';

// Create an execution request object.
        $request = new Google_Service_Script_ExecutionRequest();
        $request->setFunction('getValueOfSheet');
//print_r($request);die;
        try {
            // Make the API request.
            $response = $service->scripts->run($scriptId, $request);

            if ($response->getError()) {
                // The API executed, but the script returned an error.
                // Extract the first (and only) set of error details. The values of this
                // object are the script's 'errorMessage' and 'errorType', and an array of
                // stack trace elements.
                $error = $response->getError()['details'][0];
                printf("Script error message: %s\n", $error['errorMessage']);

                if (array_key_exists('scriptStackTraceElements', $error)) {
                    // There may not be a stack
                    // 
                    // trace if the script didn't start executing.
                    print "Script error stacktrace:\n";
                    foreach ($error['scriptStackTraceElements'] as $trace) {
                        printf("\t%s: %d\n", $trace['function'], $trace['lineNumber']);
                    }
                }
            } else {
                // The structure of the result will depend upon what the Apps Script
                // function returns. Here, the function returns an Apps Script Object
                // with String keys and values, and so the result is treated as a
                // PHP array (folderSet).
                $resp = $response->getResponse();
                if ($resp) {
                    print_r($resp);die;
                }
            }
        } catch (Exception $e) {
            // The API encountered a problem before the script started executing.
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    public function store(Request $request) {
        $error = true;
        $json = null;
        $key = null;
        $data = array(
            'tp_vietnamese' => $request->tp_vietnamese,
            'tp_japanese' => $request->tp_japanese,
            'tp_description' => $request->tp_description,
            'tp_flag' => 1,
        );
        //insert
        $topicId = $this->database->getReference('mst_topic')->push($data)->getKey();
        //get data
        if ($topicId) {
            $currentConfigType = $this->database->getReference('mst_topic/' . $topicId)->getValue();
            $error = false;
            $json = json_encode($currentConfigType);
            $key = $topicId;
        }
        return response([
            'error' => $error,
            'data' => $json,
            'key' => $key
        ], 200);
    }

    public function update(Request $request) {
        $error = true;
        $json = 'Not update, something wrong !';
        $keyTopic = isset($request->keyTopic) ? $request->keyTopic : NULL;
        if (!$keyTopic) {
            return response([
                'error' => true,
                'data' => 'Key topic not exist !'
            ], 200);
        }
        $data = array(
            'tp_vietnamese' => $request->tp_vietnamese,
            'tp_japanese' => $request->tp_japanese,
            'tp_description' => $request->tp_description,
            'acc_flag' => 1,
        );
        //check key exist
        $reference = $this->database->getReference('mst_topic')->getValue();
        $keyExist = false;
        if (isset($reference[$keyTopic])) {
            $keyExist = true;
        }
        if ($keyExist) {
            //update
            $this->database->getReference('mst_topic/' . $keyTopic)->update($data);
            //get data
            $currentConfigType = $this->database->getReference('mst_topic/' . $keyTopic)->getValue();
            $error = false;
            $json = json_encode($currentConfigType);
        }
        return response([
            'error' => $error,
            'data' => $json
        ], 200);
    }

    public function delete(Request $request) {
        $keyTopic = isset($request->keyTopic) ? $request->keyTopic : NULL;
        if (!$keyTopic) {
            return response([
                'error' => true,
                'data' => 'Key topic not exist !'
            ], 200);
        }
        //delete
        $this->database->getReference('mst_topic/' . $keyTopic)->remove();
        return response([
            'error' => false,
        ], 200);
    }
}
