<?php
require_once "../../model/custom/vendor/autoload.php";
use GuzzleHttp\Client;
$client=new Client([
    // Base URI is used with relative requests
    'base_uri' => 'http://nisa.nisa.10.20.2.179.nip.io',
    'cookies'=>true,
]);

$passNisa=$_POST['passNisa'];
$loginNisa=$_POST['loginNisa'];


echo $loginNisa;
echo $passNisa;
authNisa($loginNisa, $passNisa, $client);

$response1 = $client->request('GET', '/api/projectsattributable');

$projets = $response1 -> getBody();
print_r($projets);
echo $projets;

$requestBody= array (
    'user_id' => '1',
    'totalObjects' => 1,
    'projects' => 
    array (
      0 => 
      array (
        'id' => 18,
        'workloads' => 
        array (
          0 => 
          array (
            'workload_date' => '2022-05-25',
            'workload_workload' => 0.5,
            'workload_depreciate' => false,
            'project_id' => 18,
            'workload_changed' => 2,
            'workload_week' => 21,
            'workload_comment' => 'test',
          ),
        ),
      ),
    ),
);




$response = $client->request('POST', '/api/users/workloads/create', [
    'json' => $requestBody
]);

print_r($requestBody);

function hey(){
  echo 'hi';
}

function authNisa($loginNisa, $passNisa, $client){
    $response = $client->request('POST', '/login_check', [
        'form_params' => [
            '_username' => $loginNisa,
            '_password' => $passNisa
        ]
    ]);

    $body= $response ->getBody();
    echo $body;
    echo 'oui';
}


?>
