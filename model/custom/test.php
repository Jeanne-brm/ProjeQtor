<?php
require_once "vendor/autoload.php";
  
use GuzzleHttp\Client;
$jar = new \GuzzleHttp\Cookie\CookieJar;
  
$client = new Client([
    // Base URI is used with relative requests
    'base_uri' => 'https://nisa-ids.coexya.eu',
    'cookies'=>true,
]);
  
$response = $client->request('POST', '/login_check', [
    'form_params' => [
        '_username' => 'jbrom',
        '_password' => 'GaufreGaufre91'
    ]
]);

$body= $response ->getBody();
//echo $body;


$response1 = $client->request('GET', '/api/projectsattributable');

$projets = $response1 -> getBody();
//print_r($projets);
echo $projets;

$requestBody= array (
    'user_id' => '129',
    'totalObjects' => 1,
    'projects' => 
    array (
      0 => 
      array (
        'id' => 191,
        'workloads' => 
        array (
          0 => 
          array (
            'workload_date' => '2022-05-09',
            'workload_workload' => 0.25,
            'workload_depreciate' => false,
            'project_id' => 18,
            'workload_changed' => 2,
            'workload_week' => 19,
            'workload_comment' => 'test',
          ),
        ),
      ),
    ),
);




$response = $client->request('POST', '/api/users/workloads/create', [
    'json' => $requestBody
]);

