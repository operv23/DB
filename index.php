<?php
/**
 * Webhook for Time Bot- Facebook Messenger Bot
 */

//---------DB----------//

mysql://b3dfd00d3f8fc1:f2319eb0@us-cdbr-iron-east-04.cleardb.net/heroku_cf74a6558cae1bc?reconnect=true

    $url=parse_url(getenv("CLEARDB_DATABASE_URL"));

    $server = $url["us-cdbr-iron-east-04.cleardb.net"];
    $username = $url["b3dfd00d3f8fc1"];
    $password = $url["f2319eb0"];
    $db = substr($url["heroku_cf74a6558cae1bc"],1);

    mysqli_connect($server, $username, $password);


    mysqli_select_db($db);
    
    $query = mysql_query("SELECT * FROM db");
	$data = mysql_fetch_assoc($query);

//---------DB----------//


$access_token = "EAAEIpvTLTHcBAHmKn8dUf8Y4fY5QwUhyRBNaXHcxZC1t6zsUFoEi2ewO5hXomrlZCNgBo98RhqIAE31MoV1L682c7Qgik09rZBowIKvwbs4MXXfmHJgawpZBc81aRGI6SFh7VAb7ESBVwm9nErhpSDhqsetrNqL0PIFjZC99ZBACyU5cKwzk6ZA";
$verify_token = "Zaq1Xsw2";
$hub_verify_token = null;
if(isset($_REQUEST['hub_challenge'])) {
    $challenge = $_REQUEST['hub_challenge'];
    $hub_verify_token = $_REQUEST['hub_verify_token'];
}
if ($hub_verify_token === $verify_token) {
    echo $challenge;

}
$input = json_decode(file_get_contents('php://input'), true);
$sender = $input['entry'][0]['messaging'][0]['sender']['id'];
$message = $input['entry'][0]['messaging'][0]['message']['text'];
$message_to_reply = '';
/**
 * Some Basic rules to validate incoming messages
 */
if(preg_match('[time|current time|now]', strtolower($message))) {
    // Make request to Time API
    ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)');
    $result = file_get_contents("http://www.timeapi.org/utc/now?format=%25a%20%25b%20%25d%20%25I:%25M:%25S%20%25Y");
    if($result != '') {
        $message_to_reply = $result;
    }
} else {
	if ($input == $data['id']) { $message_to_reply = $data['value']; }else{
    $message_to_reply = 'Huh! what do you mean?';}
}
//API Url
$url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$access_token;
//Initiate cURL.
$ch = curl_init($url);
//The JSON data.
$jsonData = '{
    "recipient":{
        "id":"'.$sender.'"
    },
    "message":{
        "text":"'.$message_to_reply.'"
    }
}';
//Encode the array into JSON.
$jsonDataEncoded = $jsonData;
//Tell cURL that we want to send a POST request.
curl_setopt($ch, CURLOPT_POST, 1);
//Attach our encoded JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
//Execute the request
if(!empty($input['entry'][0]['messaging'][0]['message'])){
    $result = curl_exec($ch);
}