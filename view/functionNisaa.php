<?php
  require '../model/custom/authentification.php';
  require_once "../model/custom/vendor/autoload.php";
  use GuzzleHttp\Client;
  $client = new Client([
      // Base URI is used with relative requests
      'base_uri' => 'http://nisa.nisa.10.20.2.179.nip.io',
      'cookies'=>true,
  ]);
?>

<script type="text/JavaScript"> 
    
    function show_hide() {
      var x = document.getElementById("parag");
      if (x.style.display === "none") 
      {
        x.style.display = "block";
      } 
      else 
      {
        x.style.display = "none";
      }
    }
</script>
<?php 
    //show_hide() function is triggered on button click 
    echo "<button onClick='show_hide()'>Show/Hide</button>";
    //display this paragraph when button is clicked
    echo'<button id="parag" style="display:none;">Welcome to Codespeedy.</button>';
    authNisa('admin_nisa','Je suis 1 mot de passe.',$client);
    $response1 = authNisa('admin_nisa','Je suis 1 mot de passe.',$client);
    $body= $response1 ->getBody();
    //echo $body;
    

?>
