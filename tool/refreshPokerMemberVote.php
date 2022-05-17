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
$result="";
$id=RequestHandler::getId('idItem');
$idPokerSession=RequestHandler::getId('idPokerSession');
$list = RequestHandler::getValue('itemList');
$itemList=explode(',',$list);
$user = getSessionUser();

$obj = new PokerSession($idPokerSession);
$pos = array_search($id, $itemList);
if($pos < 0)$pos=0;
$previous = ($pos>0)?true:false;
$lenght = count($itemList)-1;
if($lenght < 0)$lenght=0;
$next = ($pos<$lenght)?true:false;

$pokerComplexity = new PokerComplexity();
$pokerComplexityList = $pokerComplexity->getSqlElementsFromCriteria(array('idle'=>'0'), null, null, "sortOrder ASC");
$pokerItem = new PokerItem($id);
$pokerMember = new PokerResource();
$pokerMember = $pokerMember->getSingleSqlElementFromCriteria('PokerResource', array('idPokerSession'=>$idPokerSession, 'idResource'=>$user->id));
$pokerMemberList = $pokerMember->getSqlElementsFromCriteria(array('idPokerSession'=>$idPokerSession));
$pokerVote = PokerVote::getSingleSqlElementFromCriteria('PokerVote', array('idPokerSession'=>$idPokerSession, 'idResource'=>$user->id, 'idPokerItem'=>$pokerItem->id));
$pokerVoteList = SqlList::getListWithCrit('pokerVote', array('idPokerSession'=>$idPokerSession, 'idPokerItem'=>$pokerItem->id), 'value');
sort($pokerVoteList);
$lowVote = (isset($pokerVoteList[0]))?$pokerVoteList[0]:1;
$highVote = (isset($pokerVoteList[count($pokerVoteList)-1]))?$pokerVoteList[count($pokerVoteList)-1]:99;

?>