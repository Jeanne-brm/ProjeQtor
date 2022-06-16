<?php 

    /*
      Récupération des imputations saisies sur ProjeQtor pour un envoi vers NISA
    */
    //require_once('ImputationLine.php');

    //Tableau des lignes de l'écran d'imputations
    $tab = ImputationLine::getLines($resourceId, $rangeType, $rangeValue, $showIdle, $showPlanned, $hideDone, $hideNotHandled, $displayOnlyCurrentWeekMeetings);
    //Array des imputations ProjeQtor
    $imputations = recupImputations($tab, $nbDays, $listLienProject, $startDate);

    //print("<pre>res \n" . print_r($imputations, true) . "</pre>");

    // Variables de sessions récupérées dans /custom/functionNisa.php (identifiant utilisateur NISA et liste des projets)
    if (isset($_SESSION['idNisa'])){
      $idNisa = $_SESSION['idNisa'];
      $listeProjetsNisa = $_SESSION['listeProjets']['projectsAttributable'];
    }
    //print("<pre>".print_r($listeProjetsNisa  ,true)."</pre>");

    $tests = array();
    $workloads = array();
    $format = array();
    $count = 0; //trouver meilleur moyen de compter si ça existe
    

    //Boucle sur tous les projets ProjeQtor, les compare à la liste des projets NISA et formattage de la requête à envoyer 
    if(isset($idNisa) && isset($listeProjetsNisa)){
      foreach ($imputations as $projets) {
        foreach ($listeProjetsNisa as $value) {
          if ($projets['Nom'] == $value['project_imput_code']&&$projets['Vide']=='false') {
            foreach ($projets['Days'] as $x => $val) {
              if ($val != 0) {
                $imput = array(
                  'workload_date' => $x,
                  'workload_workload' => $val,
                  'workload_depreciate' => false,
                  'project_id' => $value['project_id'],
                  'workload_changed' => 2,
                  'workload_week' => substr($rangeValue, 4, 2),
                  'workload_comment' => 'test',
                );
                $count += 1;
                array_push($workloads, $imput);
                $listeWorkloads['id'] = $value['project_id'];
                $listeWorkloads['workloads'] = $workloads;
              }
            }
            array_push($format, $listeWorkloads);
            $workloads = array();

            $requete = array(
              'user_id' => $idNisa,
              'totalObjects' => $count,
              'projects' =>
                $format
            );  
          }
        }
      }
      if(isset($requete)){
        print("<pre>" . print_r($requete, true) . "</pre>");
        $_SESSION['arr'] = $requete;
      }
    }

    function recupImputations($tab, $nbDays, $listLienProject, $startDate){
    $imputations = array();
    $date = $startDate;
    $isVide='true';

    foreach ($tab as $key => $line) {
      if ($line->refType == 'Project') {
        for ($i = 1; $i <= $nbDays; $i++) {
          $sumWork = ImputationLine::getAllWorkProjectDay($i, $listLienProject, $tab, $line->refId);
          //$sumWork=Work::displayImputation(ImputationLine::getAllWorkProjectDay($i, $listLienProject, $tab, $line->refId));
          //$imputations[$line->name][$line->refId][$curDate]=($sumWork);
          if($sumWork!=0){
            $isVide='false';
          }
          $imputations[$line->name]["id"] = $line->refId;
          $imputations[$line->name]["Nom"] = $line->name;
          $imputations[$line->name]["Vide"] = $isVide;
          $imputations[$line->name]["Days"][$date] = $sumWork;
          
          $date = date('Y-m-d', strtotime("+1 days", strtotime($date)));
        }
        $date = $startDate;
      }
      $isVide='true';
    }
    return $imputations;
  }

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

  
    