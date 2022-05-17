<?PHP
/*** COPYRIGHT NOTICE *********************************************************
 *
 * Copyright 2009-2017 ProjeQtOr - Pascal BERNARD - support@projeqtor.org
 * Contributors : -
 *
 * This file is part of ProjeQtOr.
 * 
 * ProjeQtOr is free software: you can redistribute it and/or modify it under 
 * the terms of the GNU Affero General Public License as published by the Free 
 * Software Foundation, either version 3 of the License, or (at your option) 
 * any later version.
 * 
 * ProjeQtOr is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for 
 * more details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * ProjeQtOr. If not, see <http://www.gnu.org/licenses/>.
 *
 * You can get complete code of ProjeQtOr, other resource, help and information
 * about contributors at http://www.projeqtor.org 
 *     
 *** DO NOT REMOVE THIS NOTICE ************************************************/

/** ===========================================================================
 * Get the list of objects, in Json format, to display the grid list
 */
require_once "../tool/projeqtor.php"; 

$id=RequestHandler::getId('idPokerSession');
$pokerSession = new PokerSession($id);
if(!$pokerSession->handled){
  $pokerSession->handled = 1;
  $pokerSession->handledDate=date('Y-m-d H:i:s');
  $st = new Status($pokerSession->idStatus);
  if(!$st->setHandledStatus){
    $allowedStatusList=Workflow::getAllowedStatusListForObject($pokerSession);
    foreach ( $allowedStatusList as $st ) {
    	if ($st->setHandledStatus) {
    		$pokerSession->idStatus=$st->id;
    		break;
    	}
    }
  }
}else if($pokerSession->handled and !$pokerSession->done){
  $pokerSession->done = 1;
  $pokerSession->doneDate=date('Y-m-d H:i:s');
  $st = new Status($pokerSession->idStatus);
  if(!$st->setDoneStatus){
    $allowedStatusList=Workflow::getAllowedStatusListForObject($pokerSession);
    foreach ( $allowedStatusList as $st ) {
    	if ($st->setDoneStatus) {
    		$pokerSession->idStatus=$st->id;
    		break;
    	}
    }
  }
}else{
  $pokerSession->handled = 1;
  $pokerSession->handledDate=date('Y-m-d H:i:s');
  $pokerSession->done = 0;
  $pokerSession->doneDate = null;
  $allowedStatusList=Workflow::getAllowedStatusListForObject($pokerSession);
  foreach ( $allowedStatusList as $st ) {
	$pokerSession->idStatus=$st->id;
	break;
  }
}
$pokerSession->save();
?>