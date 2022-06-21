<?php
session_start();
require_once "../../model/custom/vendor/autoload.php";
//require_once "../../model/ImputationLine.php";

use GuzzleHttp\Client;

$client=new Client([
    // Base URI is used with relative requests
    'base_uri' => 'http://nisa.k8s.ren-dev2.lan',
    'cookies'=>true,
]);

$passNisa=$_POST['passNisa'];
$loginNisa=$_POST['loginNisa'];


authNisa($loginNisa, $passNisa, $client);
$response1 = $client->request('GET', '/api/projectsattributable');
$projets = $response1 -> getBody();

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

$requestupdate= array (
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
if(isset($_SESSION['arr'])){
  $response = $client->request('POST', '/api/users/workloads/create', [
      'json' => $requestBody
  ]);
  //print("<pre>".print_r($_SESSION['arr']  ,true)."</pre>");
}
if(isset($_SESSION['update'])){
  $response = $client->request('PATCH', '/api/users/workloads/update', [
      'json' => $_SESSION['update']
  ]);
  print("<pre>".print_r($_SESSION['update']  ,true)."</pre>");
}
//$response = $client->request('POST', '/api/users/workloads/create', [
//  'json' => $requestBody
//]);



//$idNisa='';
$idNisa=recupId($client, $loginNisa);
$_SESSION['idNisa']=$idNisa;
$_SESSION['listeProjets']=recupListeProjet($client);
echo 'idNisa = '.$idNisa;


$response_workloads = $client->request('GET', 'api/users/'.$idNisa.'/workloads/2022-06-20/dates/2022-06-26');
$body_workloads =$response_workloads->getBody();
$array_workloads=json_decode($body_workloads,true);
$_SESSION['workloadsNisa']=$array_workloads['workloads'];
print("<pre>".print_r($array_workloads['workloads']  ,true)."</pre>");

//print("<pre>".print_r($requestBody  ,true)."</pre>");


function recupId($client, $loginNisa){
  $response = $client->request('GET', '/api/users');
  $body = $response ->getBody();
  $json= json_decode($body,true);
  if(isset($json['users'])){
    if (is_array($json['users']) || is_object($json['users'])){
      foreach ($json['users'] as $value){
        //echo "nom : ". $value['username'] . " et id : " . $value['id'] ."\n";
        if ($value['username']==$loginNisa){
          $result = $value['id'];
        }
      }
    }
  }
  else {
    $result='json undefined'; //à gérer
  }
  return $result;
}

function recupListeProjet($client){
  $response = $client->request('GET', '/api/projectsattributable');
  $body = $response ->getBody();
  $json= json_decode($body,true);
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
}

  
?>
