<?php
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

// MTY - LEAVE SYSTEM
// RULES :
// x Don't take assignment for the LeaveProject if it's not visible by the connected user
//      Done in getLines
// x Can't imputate on an assignment that is in the leave project
//      Done in getLines


/**
 * ============================================================================
 * Project is the main object of the project managmement.
 * Almost all other objects are linked to a given project.
 */
require_once ('_securityCheck.php');

class ImputationLine {
  
  // List of fields that will be exposed in general user interface
  // public $id; // redefine $id to specify its visible place
  public $refType;
  public $refId;
  public $idProject;
  public $idAssignment;
  public $name;
  public $comment;
  public $wbs;
  public $wbsSortable;
  public $topId;
  public $validatedWork;
  public $assignedWork;
  public $plannedWork;
  public $realWork;
  public $leftWork;
  public $imputable;
  public $elementary;
  public $arrayWork;
  public $arrayPlannedWork;
  public $startDate;
  public $endDate;
  public $idle;
  public $locked;
  public $description;
  public $functionName;
  public $fromPool;

  /**
   * ==========================================================================
   * Constructor
   *
   * @param $id the
   *          id of the object in the database (null if not stored yet)
   * @return void
   */
  function __construct($id=NULL, $withoutDependentObjects=false) {
    $arrayWork=array();
  }

  /**
   * ==========================================================================
   * Return some lines for imputation purpose, including assignment and work
   *
   * @return void
   */
  function __destruct() {}

static function getLines($resourceId, $rangeType, $rangeValue, $showIdle, $showPlanned=true, $hideDone=false, $hideNotHandled=false, $displayOnlyCurrentWeekMeetings=false) {
    SqlElement::$_cachedQuery['Assignment']=array();
    SqlElement::$_cachedQuery['PlanningElement']=array();
    SqlElement::$_cachedQuery['WorkElement']=array();
    SqlElement::$_cachedQuery['Activity']=array();
    SqlElement::$_cachedQuery['Project']=array();
    
    $projectNoteStartedBeforValidatedDate=(Parameter::getGlobalParameter("notStartBeforeValidatedStartDate")=='YES')?:false;
    // Insert new lines for admin projects
    Assignment::insertAdministrativeLines($resourceId);
    
    // Initialize parameters
    if (Parameter::getGlobalParameter('displayOnlyHandled')=="YES") {
      $hideNotHandled=1;
    }
    $user=getSessionUser();
    // $user=new User($user->id);
    
    $result=array();
    if ($rangeType=='week') {
      $nbDays=7;
    }
    if ($rangeType=='day') {
    	$nbDays=1;
    }
    $startDate=self::getFirstDay($rangeType, $rangeValue);
    $plus=$nbDays-1;
    $endDate=date('Y-m-d', strtotime("+$plus days", strtotime($startDate)));
    
    // Get All assignments, including the ones from pools
    $ressList=Sql::fmtId($resourceId);
    if (Parameter::getGlobalParameter('displayPoolsOnImputation')!='NO') {
      $rta=new ResourceTeamAffectation();
      $rtaList=$rta->getSqlElementsFromCriteria(array('idResource'=>$resourceId));
      foreach ($rtaList as $rta) {
        if ($rta->idle) continue;
        if (($rta->startDate==null or $rta->startDate<=$endDate) and ($rta->endDate==null or $rta->endDate>=$startDate)) {
          $ressList.=','.Sql::fmtId($rta->idResourceTeam);
        }
      }
    }
    $critWhere="idResource in ($ressList)";
    if (!$showIdle) {
      $critWhere.=" and idle=0";
    }
// MTY - LEAVE SYSTEM
    if (isLeavesSystemActiv()) {
        // Don't take assignment for the LeaveProject if it's not visible by the connected user
        if ($resourceId != $user->id) {
            $theRes = new Resource($resourceId);
        } else {
            $theRes = $user;
        }
        $leaveProject = Project::getLeaveProject();
        if ($leaveProject!=null) {$leaveProjectId = $leaveProject->id;} else {$leaveProjectId=null;};
        if ($theRes->isEmployee==0 and $leaveProjectId!=null) {
                    $projPe=new ProjectPlanningElement();
                    $critWhere.=" and idProject NOT IN (Select ".$projPe->getDatabaseColumnName('idProject')." FROM ".$projPe->getDatabaseTableName()." WHERE ".$projPe->getDatabaseColumnName('validatedStartDate')." > '".$endDate."'";
                    $critWhere.= " and ".$projPe->getDatabaseColumnName('refType')." = 'Project' )";
                    $critWhere .= " and idProject <> ". $leaveProjectId;
        }else if ($projectNoteStartedBeforValidatedDate) {
          $projPe=new ProjectPlanningElement();
          $critWhere.=" and idProject NOT IN (Select ".$projPe->getDatabaseColumnName('idProject')." FROM ".$projPe->getDatabaseTableName()." WHERE ".$projPe->getDatabaseColumnName('validatedStartDate')." > '".$endDate."'";
          $critWhere.= " and ".$projPe->getDatabaseColumnName('refType')." = 'Project' )";
        }
    } else {
        $leaveProjectId=null;
        if($projectNoteStartedBeforValidatedDate){
          $projPe=new ProjectPlanningElement();
          $critWhere.=" and idProject NOT IN (Select ".$projPe->getDatabaseColumnName('idProject')." FROM ".$projPe->getDatabaseTableName()." WHERE ".$projPe->getDatabaseColumnName('validatedStartDate')." > '".$endDate."'";
          $critWhere.= " and ".$projPe->getDatabaseColumnName('refType')." = 'Project' )";
        }
    }
// MTY - LEAVE SYSTEM
    $ass=new Assignment();
    $assList=$ass->getSqlElementsFromCriteria(null, false, $critWhere, null, true, true);
    
    // Retrieve realwork and planned work entered for period
    $crit=array('idResource'=>$resourceId);
    $crit[$rangeType]=$rangeValue;
    $work=new Work();
    $workList=$work->getSqlElementsFromCriteria($crit, false, 'id asc', null, false, true);
    $plannedWork=new PlannedWork();
    if ($showPlanned) {
      $critWhere="idResource in ($ressList)";
      $critWhere.=" and $rangeType='$rangeValue'";
      $plannedWorkList=$plannedWork->getSqlElementsFromCriteria(null, false, $critWhere, null, false, true);
    }
    
    // Get acces restriction to hide projects dependong on access rights
    $profile=$user->getProfile(); // Default profile for user
    $listAccesRightsForImputation=$user->getAllSpecificRightsForProfiles('imputation');
    $listAllowedProfiles=array(); // List will contain all profiles with visibility to Others imputation
    if (isset($listAccesRightsForImputation['PRO'])) {
      $listAllowedProfiles+=$listAccesRightsForImputation['PRO'];
    }
    if (isset($listAccesRightsForImputation['ALL'])) {
      $listAllowedProfiles+=$listAccesRightsForImputation['ALL'];
    }
    $visibleProjects=array();
    foreach ($user->getSpecificAffectedProfiles() as $prj=>$prf) {
      if (in_array($prf, $listAllowedProfiles)) {
        $visibleProjects[$prj]=$prj;
      }
    }
    // ... and remove assignments not to be shown
    $accessRightRead=securityGetAccessRight('menuActivity', 'read');
    if ($user->id!=$resourceId and $accessRightRead!='ALL') {
      foreach ($assList as $id=>$ass) {
        if (!array_key_exists($ass->idProject, $visibleProjects)) {
          unset($assList[$id]);
        }
      }
    }
    
    // Hide some lines depending on user criteria selected on page
    if ($hideNotHandled or $hideDone or $displayOnlyCurrentWeekMeetings) {
      foreach ($assList as $id=>$ass) {
        if ($ass->refType and SqlElement::class_exists($ass->refType)) $refObj=new $ass->refType($ass->refId, true);
        if ($hideNotHandled and property_exists($refObj, 'handled') and !$refObj->handled) {
          unset($assList[$id]);
        }
        if ($hideDone and property_exists($refObj, 'done') and $refObj->done) {
          unset($assList[$id]);
        }
        if ($displayOnlyCurrentWeekMeetings and get_class($refObj)=='Meeting') {
          if ($refObj->meetingDate<$startDate or $refObj->meetingDate>$endDate) {
            unset($assList[$id]);
          }
        }
      }
    }
    // Check if assignment exists for each work (may be closed or not assigned: so make it appear)
    foreach ($workList as $work) {
      if ($work->idAssignment) {
        $found=false;
        // Look into assList
        if (isset($assList['#'.$work->idAssignment])) {
          $ass=$assList['#'.$work->idAssignment];
          $found=true;
        }
        if (!$found) {
          $ass=new Assignment($work->idAssignment);
          if ($ass->id) { // Assignment exists, but not retrieve : display but readonly
            $ass->_locked=true;
            $assList[$ass->id]=$ass;
          } else { // Assignment does not exist : this is an error case as $wor->idAssignment is set !!! SHOULD NOT BE SEEN
            /*
             * $id=$work->refType.'#'.$work->refId; if (! isset($assList[$id])) { // neo-assignment do not exist : insert one $ass->id=null; $ass->name='<span style="color:red;"><i>' . i18n('notAssignedWork') . ' (1)</i></span>'; if ($work->refType and $work->refId) { $ass->comment=i18n($work->refType) . ' #' . $work->refId; } else { $ass->comment='unexpected case : assignment #' . htmlEncode($work->idAssignment) . ' not found'; } $ass->realWork=$work->work; $ass->refType=$work->refType; $ass->refId=$work->refId; } else { // neo-assignment exists : add work (once again ,at this step this should not be displayed, it is an error case $ass=$assList[$id]; $ass->realWork+=$work->work; } $ass->_locked=true; $assList[$id]=$ass;
             */
          }
        }
        if ($work->idWorkElement) { // Check idWorkElement : if set, add new line for ticket, locked
          $acticityAss=$ass; // Save reference to parent activity
          $ass=new Assignment();
          $we=new WorkElement($work->idWorkElement, true);
          $ass->id=$acticityAss->id;
          $ass->name=$we->refName;
          ;
          $ass->refType=$we->refType;
          $ass->refId=$we->refId;
          $ass->realWork=$we->realWork;
          $ass->leftWork=$we->leftWork;
          $ass->_locked=true;
          $ass->_topRefType=$acticityAss->refType;
          $ass->_topRefId=$acticityAss->refId;
          $ass->_idWorkElement=$work->idWorkElement;
          $ass->isResourceTeam=0;
          $id=$work->refType.'#'.$work->refId.'#'.$work->idWorkElement;
          $assList[$id]=$ass;
        }
      } else { // Work->idAssignment not set (for tickets not linked to Activities for instance)
        $id=$work->refType.'#'.$work->refId;
        if (isset($assList[$id])) {
          $ass=$assList[$id];
        } else {
          $ass=new Assignment();
        }
        if ($work->refType) { // refType exist (Ticket is best case)
          $obj=new $work->refType($work->refId, true);
          if ($obj->name) {
            $obj->name=htmlEncode($obj->name);
          }
        } else { // refType does not exist : is should not happen (name displayed in red), key ot to avoid errors
          $obj=new Ticket();
          $obj->name='<span style="color:red;"><i>'.i18n('notAssignedWork').' (2)</i></span>';
          if (!$ass->comment) {
            $ass->comment='unexpected case : no reference object';
          }
          $ass->_locked=true;
        }
        // $ass->name=$id . " " . $obj->name;
        $ass->name=$obj->name;
        if (isset($obj->WorkElement)) {
          $ass->realWork=$obj->WorkElement->realWork;
          $ass->leftWork=$obj->WorkElement->leftWork;
        }
        $ass->id=null;
        $ass->refType=$work->refType;
        $ass->refId=$work->refId;
        if ($work->refType) {
          // $ass->comment=i18n($work->refType) . ' #' . $work->refId;
        }
        $assList[$id]=$ass;
      }
    }
    
    $notElementary=array();
    $cptNotAssigned=0;
    foreach ($assList as $idAss=>$ass) {
// MTY - LEAVE SYSTEM
      if (isLeavesSystemActiv()) {
        // Can't imputate on an assignment that is in the leave project
        if ($ass->idProject == $leaveProjectId && $leaveProjectId<>null) {
            $ass->_locked = true;
        }
      }
// MTY - LEAVE SYSTEM
      $elt=new ImputationLine();
      $elt->idle=$ass->idle;
      $elt->refType=$ass->refType;
      $elt->refId=$ass->refId;
      $elt->comment=$ass->comment;
      $elt->idProject=$ass->idProject;
      $elt->idAssignment=$ass->id;
      $elt->fromPool=$ass->isResourceTeam;
      $elt->assignedWork=$ass->assignedWork;
      $elt->plannedWork=$ass->plannedWork;
      $elt->realWork=$ass->realWork;
      $elt->leftWork=$ass->leftWork;
      $elt->arrayWork=array();
      if ($ass->isNotImputable) {
        $elt->imputable=false;
      }
      if (isset($ass->_locked)) $elt->locked=true;
      $elt->arrayPlannedWork=array();
      if (!$ass->idProject) {
        $elt->idProject=SqlList::getFieldFromId($ass->refType, $ass->refId, 'idProject');
      }
      if ($ass->idRole) {
        $elt->functionName=SqlList::getNameFromId('Role', $ass->idRole);
      }
      $crit=array('refType'=>$elt->refType, 'refId'=>$elt->refId);
      if (isset($ass->_topRefType) and isset($ass->_topRefId)) {
        $crit=array('refType'=>$ass->_topRefType, 'refId'=>$ass->_topRefId);
      }
      $plan=null;
      $manuPlan=false;
      if ($ass->id) {
        $plan=SqlElement::getSingleSqlElementFromCriteria('PlanningElement', $crit);
        //florent
          if($plan->refType=='Activity' and $plan->idPlanningMode=='23'){
            $manuPlan=true;
            $plannedWorkMan=new PlannedWork();
            $critWhere="idProject='".$plan->idProject."' and refType='".$plan->refType."'";
            $critWhere.=" and refId='$plan->refId' and workDate between'".$startDate."' and '".(($endDate<date('Y-m-d'))?$endDate:date('Y-m-d'))."'";
            $plannedManualWorkList=$plannedWorkMan->getSqlElementsFromCriteria(null, false, $critWhere, null, false, true);
            if(!$showPlanned){
              $critWhere="idResource in ($ressList)";
              $critWhere.=" and $rangeType='$rangeValue'";
              $plannedWorkList=$plannedWork->getSqlElementsFromCriteria(null, false, $critWhere, null, false, true);
            }
          }
        //
        if (! $plan->id and $plan->refType and SqlElement::class_exists($plan->refType) and $plan->refId) {
          // This is unconsistency that we'll try and fix, if planning element does not exist : save main item will recreate it
          $refType=$plan->refType;
          $peNameForRefObj=$refType."PlanningElement";
          $pmNameForRefObj="id".$refType."PlanningMode";
          $refObjFromPlan=new $refType($plan->refId);
          if ($refObjFromPlan->id) { // Assignment refers to existing item
            if (property_exists($refObjFromPlan,$peNameForRefObj)) {
            	$refObjFromPlan->$peNameForRefObj=new $peNameForRefObj();
              $refObjFromPlan->$peNameForRefObj->refType=$refType;
              $refObjFromPlan->$peNameForRefObj->refId=$plan->refId;
              if (property_exists($refObjFromPlan->$peNameForRefObj, $pmNameForRefObj) and !$refObjFromPlan->$peNameForRefObj->$pmNameForRefObj) {
                $planningModeList=SqlList::getList('PlanningMode','applyTo');
                foreach ($planningModeList as $pmId=>$pmApplyTo) {      
                  if ($pmApplyTo==$refType) {
                    $refObjFromPlan->$peNameForRefObj->$pmNameForRefObj=$pmId;
                    break;
                  }
                }
              }
            }
            $resultSaveObjFromPlan=$refObjFromPlan->save();
            traceLog("Assignment #$ass->id for resource #$ass->idResource refers to $refType #$plan->refId that does not have a planning element");
            traceLog("   Save $refType #$plan->refId to generate planning element.");
            traceLog("   Result = ".$resultSaveObjFromPlan);
            $plan=$refObjFromPlan->$peNameForRefObj;
          } else { // Assignment refers to no existing item : delete
            $resultDeleteInvalidAssignement=$ass->delete();
            traceLog("Assignment #$ass->id for resource #$ass->idResource refers to not existing item $refType #$plan->refId");
            traceLog("   Delete unconsistent assignment.");
            traceLog("   Result = ".$resultDeleteInvalidAssignement);
            continue;
          }
        }
      }
      if ($plan and $plan->id and isset($ass->_topRefType) and isset($ass->_topRefId)) {
        $elt->wbs=$plan->wbs.'.'.htmlEncode($elt->refType).'#'.$elt->refId;
        $elt->wbsSortable=$plan->wbsSortable.'.'.htmlEncode($elt->refType).'#'.$elt->refId;
        $elt->topId=$plan->id;
        $elt->elementary=$plan->elementary;
        $elt->startDate=null;
        $elt->endDate=null;
        $elt->elementary=1;
        if (!$ass->isNotImputable) {
          $elt->imputable=true;
        }
        if (isset($ass->_idWorkElement)) {
          $elt->_idWorkElement=$ass->_idWorkElement;
        }
        $elt->name='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$ass->name;
        $key=$plan->wbsSortable.' '.$ass->_topRefType.'#'.$ass->_topRefId;
        if (isset($result[$key])) {
          $result[$key]->elementary=0;
        } else {
          $notElementary[$key]=$key;
        }
        $elt->locked=true;
      } else if ($plan and $plan->id) {
        $elt->name=htmlEncode($plan->refName);
        $elt->wbs=$plan->wbs;
        $elt->wbsSortable=$plan->wbsSortable;
        $elt->topId=$plan->topId;
        $elt->elementary=$plan->elementary;
        $elt->startDate=($plan->realStartDate)?$plan->realStartDate:$plan->plannedStartDate;
        $elt->endDate=($plan->realEndDate)?$plan->realEndDate:$plan->plannedEndDate;
        if (!$ass->isNotImputable) {
          $elt->imputable=true;
        }
      } else {
        $cptNotAssigned+=1;
        if (isset($ass->name)) {
          $elt->name=$ass->name;
        } else {
          $elt->name='<span style="color:red;"><i>'.i18n('notAssignedWork').' (3)</i></span>';
          if ($ass->refType and $ass->refId) {
            $elt->comment=i18n($ass->refType).' #'.$ass->refId;
          } else {
            $elt->comment='unexpected case : no assignment name';
          }
        }
        $elt->wbs='0.'.$cptNotAssigned;
        $elt->wbsSortable='000.'.str_pad($cptNotAssigned, 3, "0", STR_PAD_LEFT);
        $elt->elementary=1;
        $elt->topId=null;
        if (!$ass->isNotImputable) {
          $elt->imputable=true;
        }
        $elt->idAssignment=null;
        $elt->locked=true;
      }
      // if ( ! ($user->id = $resourceId or $scopeCode!='ALL' or ($scopeCode='PRO' and array_key_exists($ass->idProject, $visibleProjects) ) ) ) {
      // $elt->locked=true;
      // }
      $key=$elt->wbsSortable.' '.htmlEncode($ass->refType).'#'.$ass->refId;
      if (array_key_exists($key, $result)) {
        $key.='/#'.$ass->id;
      }
      //florent
      if($manuPlan){
        foreach ($plannedManualWorkList as $work) {
          $critArray=array('idProject'=>$work->idProject,'month'=>$work->month);
          $validatedImp=SqlElement::getSingleSqlElementFromCriteria('ConsolidationValidation', $critArray);
          //$lockedImp=SqlElement::getSingleSqlElementFromCriteria('LockedImputation', $critArray);
          if($validatedImp->id!='' )continue;
          if (($work->idAssignment and $work->idAssignment==$elt->idAssignment ) or (!$work->idAssignment and $work->refType==$elt->refType and $work->refId==$elt->refId) or ($work->idAssignment and $work->idAssignment==$elt->idAssignment)) {
            $workDate=$work->workDate;
            $offset=dayDiffDates($startDate, $workDate)+1;
            $elt->arrayWork[$offset]=$work;
          }
        }
      }
      //
      // fetch all work stored in database for this assignment
      foreach ($workList as $work) {
        if (($work->idAssignment and $work->idAssignment==$elt->idAssignment and !$work->idWorkElement and !isset($elt->_idWorkElement)) or (!$work->idAssignment and $work->refType==$elt->refType and $work->refId==$elt->refId) or ($work->idAssignment and $work->idAssignment==$elt->idAssignment and $work->idWorkElement and isset($elt->_idWorkElement) and $elt->_idWorkElement==$work->idWorkElement)) {
          $workDate=$work->workDate;
          $offset=dayDiffDates($startDate, $workDate)+1;
          if (isset($elt->arrayWork[$offset])) {
            if($elt->arrayWork[$offset]->idLeave!=''){
              $elt->arrayWork[$offset]->work+=$work->work;
            }else{
              $work->delete();
            }
          } else {
            $elt->arrayWork[$offset]=$work;
          }
        }
      }
      // Fill arrayWork for days without an input
      for ($i=1; $i<=$nbDays; $i++) {
        if (!array_key_exists($i, $elt->arrayWork)) {
          $elt->arrayWork[$i]=new Work();
        }
      }
      if ($showPlanned or $manuPlan) {
        foreach ($plannedWorkList as $plannedWork) {
          if ($plannedWork->idAssignment==$elt->idAssignment) {
            $workDate=$plannedWork->workDate;
            $offset=dayDiffDates($startDate, $workDate)+1;
            $elt->arrayPlannedWork[$offset]=$plannedWork;
          }
        }
        // Fill arrayWork for days without an input
        for ($i=1; $i<=$nbDays; $i++) {
          if (!array_key_exists($i, $elt->arrayPlannedWork)) {
            $elt->arrayPlannedWork[$i]=new PlannedWork();
          }
        }
      }
      
      $result[$key]=$elt;
    }
    // If some not assigned work exists : add group line
    if ($cptNotAssigned>0) {
      $elt=new ImputationLine();
      $elt->idle=0;
      $elt->arrayWork=array();
      $elt->arrayPlannedWork=array();
      $elt->name=i18n('notAssignedWork');
      $elt->wbs=0;
      $elt->wbsSortable='000';
      $elt->elementary=false;
      $elt->imputable=false;
      $elt->refType='Imputation';
      for ($i=1; $i<=$nbDays; $i++) {
        if (!array_key_exists($i, $elt->arrayWork)) {
          $elt->arrayWork[$i]=new Work();
        }
      }
      $result['#']=$elt;
    }
    $act=new Activity();
    $accessRight=securityGetAccessRight($act->getMenuClass(), 'read');
    foreach ($result as $key=>$elt) {
      $result=self::getParent($elt, $result, true, $accessRight);
    }
    ksort($result);
    return $result;
  }
}
?>