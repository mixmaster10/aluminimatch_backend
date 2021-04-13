<?php

namespace App\Traits;

trait PushTrait
{

    public function sendPush($type, $playerID, $title, $message, $data, $appUrl){
        $content = array(
            "en" => $message
        );
        $headings = array(
            "en" => $title
        );

        $Appearance = array(
            "small_icon" => "ic_stat_onesignal_default.png",
            "large_icon" => "ic_stat_onesignal_default.png",
        );

        $fields = array(
            'app_id' => "440fffbf-364b-425c-8b87-b1359f9426d8",
            'url' => $appUrl,
            'include_player_ids' => array($playerID),
            'data' => ['type'=>$type, 'data'=>$data],
            'contents' => $content,
            'headings' => $headings,
            'Appearance' => $Appearance,
            "ios_badgeType" => "Increase",
            "ios_badgeCount" => 1
        );
        if(isset($data['avatar'])){
            $fields['large_icon'] = $data['avatar'];
            $fields['small_icon'] = $data['avatar'];
        }

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic NmFmZDY4MGMtZGE0ZS00NmQ1LWJkY2EtNDczYzZmOTZhZjYy'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }


    public function sendMultiPush($type, $tokens, $title, $message, $data){
        $content = array(
            "en" => $message
        );
        $headings = array(
            "en" => $title
        );

        $Appearance = array(
            "small_icon" => "ic_stat_onesignal_default.png",
            "large_icon" => "ic_stat_onesignal_default.png"
        );

        $fields = array(
            'app_id' => "440fffbf-364b-425c-8b87-b1359f9426d8",
            'include_player_ids' => $tokens,
            'url' => $data->appUrl,
            'data' => array("type" => $type, "data"=>$data),
            'contents' => $content,
            'headings' => $headings,
            'Appearance' => $Appearance,
            "ios_badgeType" => "Increase",
            "ios_badgeCount" => 1
        );
        if(isset($data['avatar'])){
            $fields['large_icon'] = $data['avatar'];
            $fields['small_icon'] = $data['avatar'];
        }

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic NmFmZDY4MGMtZGE0ZS00NmQ1LWJkY2EtNDczYzZmOTZhZjYy'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);
//dd("work title  Notification".$response);
        return $response;
    }
}