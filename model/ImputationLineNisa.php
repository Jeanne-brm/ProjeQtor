<?php 

    /*
      Récupération des imputations saisies sur ProjeQtor pour un envoi vers NISA
    */
    //require_once('ImputationLine.php');

    //Tableau des lignes de l'écran d'imputations
    $tab = ImputationLine::getLines($resourceId, $rangeType, $rangeValue, $showIdle, $showPlanned, $hideDone, $hideNotHandled, $displayOnlyCurrentWeekMeetings);
    //Array des imputations ProjeQtor
    $imputations = recupImputations($tab, $nbDays, $listLienProject, $startDate);

    print("<pre>res \n" . print_r($imputations, true) . "</pre>");

    // Variables de sessions récupérées dans /custom/functionNisa.php (identifiant utilisateur NISA et liste des projets)
    if (isset($_SESSION['idNisa'])){
      $idNisa = $_SESSION['idNisa'];
      $listeProjetsNisa = $_SESSION['listeProjets']['projectsAttributable'];
    }
    //print("<pre>".print_r($listeProjetsNisa  ,true)."</pre>");

    if (isset($_SESSION['workloadsNisa'])){
      $workloadsNisa=$_SESSION['workloadsNisa'];
      print("<pre>" . print_r($workloadsNisa, true) . "</pre>");
    }

    foreach($imputations as $projets){
      foreach($workloadsNisa as $work){
       
        //print("<pre>" . print_r($work, true) . "</pre>");
        
      }
    }

    $tests = array();
    $workloads = array();
    $format = array();
    $count = 0; //trouver meilleur moyen de compter si ça existe
    $update=array();
    $tesst=array();
    

    //Boucle sur tous les projets ProjeQtor, les compare à la liste des projets NISA et formattage de la requête à envoyer 
    if(isset($idNisa) && isset($listeProjetsNisa)){
      foreach ($imputations as $projets) {
        foreach ($listeProjetsNisa as $value) {
          //if ($projets['Nom'] == $value['project_imput_code']&&$projets['Vide']=='false') {
          if ($projets['Nom'] == $value['project_imput_code']) {

            print_r($projets['Nom'] .'='. $value['project_imput_code']);
            foreach ($projets['Days'] as $x => $val) {
              if ($val != 0) {
                echo "oui";
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
                echo $count;
                array_push($workloads, $imput);
                print_r($imput);
                $listeWorkloads['id'] = $value['project_id'];
                $listeWorkloads['workloads'] = $workloads;
                //print("<pre>ici" . print_r($listeWorkloads, true) . "</pre>");
              } else{
                $date=new DateTime($x);

                
                //print("<pre>oui" . print_r($tesst, true) . "</pre>");

                //foreach($workloadsNisa as $work){

                  //$work['workload_workload']=0;
                  //$work['workload_changed']=3;
                 // $work["workload_comment"]=" ";
                  //print("<pre>ici" . print_r($work, true) . "</pre>");
                  //array_push($workloadsNisa,$work);
                  //1print("<pre>oui" . print_r($workloadsNisa, true) . "</pre>");


                //}
                foreach($workloadsNisa as $work){
                  //print("<pre>" . print_r($work, true) . "</pre>");
                  //var_dump($work);
                  //var_dump($date->format('Y-m-d\TH:i:sP'));

                  if ($work['project_id']=$value['project_id']&&$work['workload_workload']!=0 && $date->format('Y-m-d\TH:i:sP')==$work['workload_date'] ){
                    print_r($work['project_id']);
                    //print("<pre>work" . print_r($work, true) . "</pre>");
                    //print("<pre>value" . print_r($value, true) . "</pre>");

                    $tes=array(
                      'workload_id'=>$work['workload_id'],
                      'workload_date'=>$work['workload_date'],
                      'workload_workload'=>0,
                      'workload_depreciate'=>false,
                      'project_id'=>$value['project_id'],
                      'workload_valid'=>false,
                      'workload_week'=>$work['workload_week'],
                      "workload_changed"=> 3,
                      "workload_comment"=> " ",
                    );
                    array_push($update,$tes);
                  }
                  $listeUpdate=$update;



                }
              }
            }
            if(isset($listeWorkloads)){
              array_push($format, $listeWorkloads);
            }

            $workloads = array();
            $update=array();

            if(isset($listeUpdate)){
              $requete_update = array(
                'user_id' => $idNisa,
                'totalObjects' => count($listeUpdate),
                'workloads' =>
                  $listeUpdate
              );
            }

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
      if(isset($requete_update)){
        print("<pre>requeteupdate" . print_r($requete_update, true) . "</pre>");
        $_SESSION['update'] = $requete_update;
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

  
    