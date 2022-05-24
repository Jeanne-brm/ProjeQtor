<?php

require_once "../tool/projeqtor.php";
require_once "../tool/formatter.php";
scriptLog('   ->/view/functionNisa.php');
require '../model/custom/authentification.php';
require_once "../model/custom/vendor/autoload.php";
use GuzzleHttp\Client;
$client = new Client([
      // Base URI is used with relative requests
      'base_uri' => 'http://nisa.nisa.10.20.2.179.nip.io',
      'cookies'=>true,
  ]);
?>

<button data-dojo-type="dijit/form/Button" type="button">je devienne fou!
    <script type="dojo/on" data-dojo-event="click" data-dojo-args="evt">
        require(["dojo/dom"], function(dom){
            dom.byId("result2").innerHTML += dojo.byId("login").value;
        });
    </script>
</button>
<div id="result2"></div>

<script type="text/javascript" src="js/functionNisa.js?version=<?php echo $version.'.'.$build;?>" ></script>
<?php 
    echo "<button onClick='show_hide()'>Show/Hide</button>";
    echo'<button id="parag" style="display:none;">Welcome to Codespeedy.</button>';
    authNisa('admin_nisa','Je suis 1 mot de passe.',$client);
    $response1 = authNisa('admin_nisa','Je suis 1 mot de passe.',$client);
    $body= $response1 ->getBody();
    //echo $body;
?>

<button data-dojo-type="dijit/form/Button" type="button">je devienne fou!
    <script type="dojo/on" data-dojo-event="click" data-dojo-args="evt">
        authNisa(dojo.byId("login").value, dojo.byId("password").value,$client); 

    </script>
</button>

