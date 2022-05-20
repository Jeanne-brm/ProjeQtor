<?php
require_once "vendor/autoload.php";
use GuzzleHttp\Client;
$client = new Client([
    // Base URI is used with relative requests
    'base_uri' => 'http://nisa.nisa.10.20.2.179.nip.io',
    'cookies'=>true,
]);

function authNisa($loginNisa, $passNisa, $client){
    $response = $client->request('POST', '/login_check', [
        'form_params' => [
            '_username' => $loginNisa,
            '_password' => $passNisa
        ]
    ]);
    return $response;
}


//$response1 = authNisa('admin_nisa','Je suis 1 mot de passe.',$client);
//$body= $response1 ->getBody();
//echo $body;
echo PHP_VERSION_ID;
