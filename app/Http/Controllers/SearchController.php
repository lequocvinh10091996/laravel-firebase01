<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use League\Csv\Writer;
use SplTempFileObject;
use League\Csv\CharsetConverter;
use ZipArchive;
use Illuminate\View\View;

class SearchController extends BaseController
{
    public function index() {
        $jsonTopic = array();
        $listTopic = $this->database->getReference('mst_topic')->getValue();

        if ($listTopic) {
            foreach($listTopic as $key => $value){
                $listTopic[$key]['tp_key'] = $key;
            }
            $jsonTopic = json_encode($listTopic);
        }
        
        $jsonTerminology = array();
        $listTerminology = $this->database->getReference('mst_translate_mean')->getValue();

        if ($listTerminology) {
            $jsonTerminology = json_encode($listTerminology);
        }
        return response([
            'error' => false,
            'data' => compact('listTopic', $jsonTopic, 'listTerminology', $jsonTerminology)
        ], 200);
    }

    public function query (Request $request) {
        $listTerminologyValue = array();
        $error = true;
        $json = "";
        if (!empty($request->topicData)) {
            $error = false;
//            $listSection = $this->database->getReference('mst_section')->getValue();
//            foreach ($listSection as $key => $value) {
//                if (in_array($value['tp_id'], $request->topicData)) {
//                    $listSectionQuery[] = $key;
//                }
//            }
            
            foreach ($request->topicData as $topic){
                $listSectionQuery[] = $this->database->getReference('mst_section')
                // order the reference's children by the values in the field 
                ->orderByChild('tp_id')
                // returns all persons being exactly 
                ->equalTo($topic)
                ->getValue();
            }
            //get key of section
            foreach( $listSectionQuery as $keys => $values){
                foreach(array_keys($values) as $key => $value){
                    $listSectionKey[] = $value;
                }
            }
            
            if (!empty($listSectionKey)) {
//                $listTerminology = $this->database->getReference('mst_translate_mean')->getValue();
//                foreach ($listTerminology as $key => $value) {
//                    if (in_array($value['sec_id'], $listSectionQuery)) {
//                        $listTerminologyQuery[] = $value;
//                    }
//                }
                foreach ($listSectionKey as $section) {
                    $listTerminologyQuery[] = $this->database->getReference('mst_translate_mean')
                    // order the reference's children by the values in the field 
                    ->orderByChild('sec_id')
                    // returns all persons being exactly 
                    ->equalTo($section)
                    ->getValue();
                }
                //get value of terminology
                foreach ($listTerminologyQuery as $keys => $values) {
                    foreach ($values as $key => $value) {
                        $listTerminologyValue[] = $value;
                    }
                }
            }
        }
        if(!empty($listTerminologyValue)){
            $json = json_encode($listTerminologyValue);
        }
        return response([
            'error' => $error,
            'data' => $json,
        ], 200);
    }
}

//Array ( [0] => Array ( [-LygJYfxJ5hGpQ29jqLM] => Array ( [sec_id] => -Lz4uN3-SVLVWfEHCODu [tm_english_translate] => [tm_example] => [tm_flag] => 1 [tm_insert_user] => [tm_japanese_higarana] => こうざふりかえめいさいしょ [tm_japanese_translate] => 口座振替明細書 [tm_vietnamese_translate] => Phiếu chi tiết chuyển khoản ) [-Lz4wVVr8etBviBVq8Wh] => Array ( [sec_id] => -Lz4uN3-SVLVWfEHCODu [tm_english_translate] => [tm_example] => [tm_flag] => 1 [tm_insert_user] => [tm_japanese_higarana] => たいしょうねんがつ [tm_japanese_translate] => 対象年月 [tm_vietnamese_translate] => Chỉ định ngày tháng đối tượng ) [-Lz4wlwq-Wxtw5cItidZ] => Array ( [sec_id] => -Lz4uN3-SVLVWfEHCODu [tm_english_translate] => [tm_example] => [tm_flag] => 1 [tm_insert_user] => [tm_japanese_higarana] => しはらいさき [tm_japanese_translate] => 支払先 [tm_vietnamese_translate] => Nơi chi trả ) [-Lyc_tXKh05A2-miNjgl] => Array ( [sec_id] => -Lz4uN3-SVLVWfEHCODu [tm_english_translate] => [tm_example] => [tm_flag] => 1 [tm_insert_user] => [tm_japanese_higarana] => にゅういんがい [tm_japanese_translate] => 入院外 [tm_vietnamese_translate] => Trong 1 tháng đến khám rồi vê, hoặc bác sĩ đến nhà khám. Nếu nói đơn giản thì đó là trường hợp sử dụng dịch vụ y tế mà không nhập viện. Dịch vụ châm cứu tại nhà và dịch vụ châm cứu khi đến viện, thì không có trường hợp nhập viện ) [-LyfsHTqZCJ85qBAGb8b] => Array ( [sec_id] => -Lz4uN3-SVLVWfEHCODu [tm_english_translate] => [tm_example] => [tm_flag] => 1 [tm_insert_user] => [tm_japanese_higarana] => こうざふりかえいらいしょ [tm_japanese_translate] => 口座振替依頼書 [tm_vietnamese_translate] => Phiếu yêu cầu chuyển khoản ) [-LycKX6SMMEfx_bRRIiz] => Array ( [sec_id] => -Lz4uN3-SVLVWfEHCODu [tm_english_translate] => [tm_example] => [tm_flag] => 1 [tm_insert_user] => [tm_japanese_higarana] => しはらいちらんひょう [tm_japanese_translate] => 支払一覧表 [tm_vietnamese_translate] => Bảng danh sách chi trả ) [-LycOK8koG7A8vFSEBkq] => Array ( [sec_id] => -Lz4uN3-SVLVWfEHCODu [tm_english_translate] => [tm_example] => [tm_flag] => 1 [tm_insert_user] => [tm_japanese_higarana] => ふりこみ [tm_japanese_translate] => 振込 [tm_vietnamese_translate] => Chuyển khoản ) ) )
