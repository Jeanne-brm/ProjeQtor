<?php
require_once "vendor/autoload.php";
  
use GuzzleHttp\Client;
$jar = new \GuzzleHttp\Cookie\CookieJar;
  
$client = new Client([
    // Base URI is used with relative requests
    'base_uri' => 'http://nisa.nisa.10.20.2.179.nip.io',
    'cookies'=>true,
]);
  
$response = $client->request('POST', '/login_check', [
    'form_params' => [
        '_username' => 'admin_nisa',
        '_password' => 'Je suis 1 mot de passe.'
    ]
]);

$body= $response ->getBody();
//echo $body;
