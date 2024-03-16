<?php
/**
 *	Discord Webhook
 *	Function does not include "username" or "avatar_url" and relies on the setting configured on Discord.
 **/

function    sendtodiscord($url, $message) {
    $data = array("content" => $message);
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    return curl_exec($curl);
}
?>
