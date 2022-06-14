<?php
session_start();
require_once "../../model/custom/vendor/autoload.php";
use GuzzleHttp\Client;
$client=new Client([
    // Base URI is used with relative requests
    'base_uri' => 'http://nisa.k8s.ren-dev2.lan',
    'cookies'=>true,
]);

$passNisa=$_POST['passNisa'];
$loginNisa=$_POST['loginNisa'];


//echo $loginNisa;
//echo $passNisa;
authNisa($loginNisa, $passNisa, $client);

$response1 = $client->request('GET', '/api/projectsattributable');

$projets = $response1 -> getBody();
//print_r($projets);
//echo $projets;

$requestBody= array (
    'user_id' => '1',
    'totalObjects' => 1,
    'projects' => 
    array (
      0 => 
      array (
        'id' => 11,
        'workloads' => 
        array (
          0 => 
          array (
            'workload_date' => '2022-06-06',
            'workload_workload' => 0,
            'workload_depreciate' => false,
            'project_id' => 11,
            'workload_changed' => 2,
            'workload_week' => 21,
            'workload_comment' => 'test',
          ),
        ),
      ),
    ),
);

//création workload
$response = $client->request('POST', '/api/users/workloads/create', [
    'json' => $_SESSION['arr']
]);


$idNisa;
//session_start();
$_SESSION['idNisa']=recupId($client, $loginNisa);
//echo $idNisa;
//var_dump($_SESSION['ProjeQtOr_projector']);
//print("<pre>".print_r($_SESSION)."</pre>");


$_SESSION['listeProjets']=recupListeProjet($client);
//var_dump($_SESSION);
print("<pre>".print_r($_SESSION['arr']  ,true)."</pre>");
print("<pre>".print_r($requestBody  ,true)."</pre>");


function recupId($client, $loginNisa){
  $response = $client->request('GET', '/api/users');
  $body = $response ->getBody();
  $json= json_decode($body,true);
  foreach ($json['users'] as $value){
    //echo "nom : ". $value['username'] . " et id : " . $value['id'] ."\n";
    if ($value['username']==$loginNisa){
      $idNisa = $value['id'];
    }
  }
  return $idNisa;
}

function recupListeProjet($client){
  $response = $client->request('GET', '/api/projectsattributable');
  $body = $response ->getBody();
  $json= json_decode($body,true);
  //echo $json;
  return $json;
}

function authNisa($loginNisa, $passNisa, $client){
    $response = $client->request('POST', '/login_check', [
        'form_params' => [
            '_username' => $loginNisa,
            '_password' => $passNisa
        ]
    ]);

    $body= $response ->getBody();
    //echo $body;
}

  
?>
