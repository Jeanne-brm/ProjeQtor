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

/* ============================================================================
 * Stauts defines list stauts an activity or action can get in (lifecylce).
 */ 
require_once('_securityCheck.php');
class PokerSessionMain extends SqlElement {

  public $_sec_description;
  public $id;
  public $name;
  public $idPokerSessionType;
  public $idStatus;
  public $idProject;
  public $pokerSessionDate;
  public $_lib_from;
  public $pokerSessionStartTime;
  public $_lib_to;
  public $pokerSessionEndTime;
  public $_spe_startPokerSession;
  public $_spe_pausePokerSession;
  public $idResource;
  public $handled;
  public $handledDate;
  public $done;
  public $doneDate;
  public $idle;
  public $idleDate;
  public $_sec_Attendees;
  public $_Assignment=array();
  public $_sec_pokerItem;
  public $_spe_pokerItem;
  public $_sec_progress_left;
  public $PokerSessionPlanningElement;
  public $pokerSessionStartDateTime;
  public $pokerSessionEndDateTime;
  public $_sec_pokerVote;
  public $_spe_pokerVote;
  public $_sec_predecessor;
  public $_Dependency_Predecessor=array();
  public $_sec_successor;
  public $_Dependency_Successor=array();
  public $_sec_Link;
  public $_Link=array();
  public $_Attachment=array();
  public $_Note=array();
  public $_nbColMax=3;
  
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="5%" ># ${id}</th>
    <th field="name" width="15%">${name}</th>
    <th field="nameProject" width="15%">${idProject}</th>
    <th field="pokerSessionDate" width="10%" formatter="dateFormatter">${date}</th>
    <th field="nameResource" formatter="thumbName22" width="15%">${responsible}</th>
    <th field="colorNameStatus" width="15%" formatter="colorNameFormatter">${idStatus}</th>
    ';
  
  private static $_fieldsAttributes=array("idProject"=>"required",
  		"idStatus"=>"required",
  		"idPokerSessionType"=>"required",
  		"pokerSessionDate"=>"required, nobr",
  		"_lib_from"=>'nobr',
  		"pokerSessionStartTime"=>'nobr',
  		"_lib_to"=>'nobr',
  		"idResource"=>"required",
  		"handled"=>"readonly, nobr",
  		"done"=>"readonly, nobr",
  		"idle"=>"nobr",
  		"pokerSessionStartDateTime"=>"hidden",
  		"pokerSessionEndDateTime"=>"hidden",
  );
  
  private static $_colCaptionTransposition = array('idResource'=> 'responsible',
                                                  'attendees'=>'otherAttendees',
                                                  'pokerSessionStartDateTime'=>'pokerSessionStartTime',
                                                  'pokerSessionEndDateTime'=>'pokerSessionEndTime'
  );
  
  public function setAttributes() {
    if(!$this->id){
    	self::$_fieldsAttributes ['_button_startEndPokerSession'] = 'hidden';
    }
  }
  
   /** ==========================================================================
   * Constructor
   * @param $id the id of the object in the database (null if not stored yet)
   * @return void
   */ 
  function __construct($id = NULL, $withoutDependentObjects=false) {
    parent::__construct($id,$withoutDependentObjects);
  }

   /** ==========================================================================
   * Destructor
   * @return void
   */ 
  function __destruct() {
    parent::__destruct();
  }

// ============================================================================**********
// GET STATIC DATA FUNCTIONS
// ============================================================================**********
  protected function getStaticLayout() {
    return self::$_layout;
  }
  
  /** ==========================================================================
   * Return the specific fieldsAttributes
   * @return the fieldsAttributes
   */
  protected function getStaticFieldsAttributes() {
    return self::$_fieldsAttributes;
  }
  
  /** ============================================================================
   * Return the specific colCaptionTransposition
   * @return the colCaptionTransposition
   */
  protected function getStaticColCaptionTransposition($fld=null) {
  	return self::$_colCaptionTransposition;
  }
  
  // ============================================================================**********
  // GET VALIDATION SCRIPT
  // ============================================================================**********
  
  /** ==========================================================================
   * Return the validation sript for some fields
   * @return the validation javascript (for dojo framework)
   */
  public function getValidationScript($colName) {
  	$colScript = parent::getValidationScript($colName);
  
  	if ($colName=="idStatus") {
  		$colScript .= '<script type="dojo/connect" event="onChange" >';
  		$colScript .= htmlGetJsTable('Status', 'setIdleStatus', 'tabStatusIdle');
  		$colScript .= htmlGetJsTable('Status', 'setDoneStatus', 'tabStatusDone');
  		$colScript .= '  var setIdle=0;';
  		$colScript .= '  var filterStatusIdle=dojo.filter(tabStatusIdle, function(item){return item.id==dijit.byId("idStatus").value;});';
  		$colScript .= '  dojo.forEach(filterStatusIdle, function(item, i) {setIdle=item.setIdleStatus;});';
  		$colScript .= '  if (setIdle==1) {';
  		$colScript .= '    dijit.byId("idle").set("checked", true);';
  		$colScript .= '  } else {';
  		$colScript .= '    dijit.byId("idle").set("checked", false);';
  		$colScript .= '  }';
  		$colScript .= '  var setDone=0;';
  		$colScript .= '  var filterStatusDone=dojo.filter(tabStatusDone, function(item){return item.id==dijit.byId("idStatus").value;});';
  		$colScript .= '  dojo.forEach(filterStatusDone, function(item, i) {setDone=item.setDoneStatus;});';
  		$colScript .= '  if (setDone==1) {';
  		$colScript .= '    dijit.byId("done").set("checked", true);';
  		$colScript .= '  } else {';
  		$colScript .= '    dijit.byId("done").set("checked", false);';
  		$colScript .= '  }';
  		$colScript .= '  formChanged();';
  		$colScript .= '</script>';
  	} else if ($colName=="initialDueDate") {
  		$colScript .= '<script type="dojo/connect" event="onChange" >';
  		$colScript .= '  if (dijit.byId("actualDueDate").get("value")==null) { ';
  		$colScript .= '    dijit.byId("actualDueDate").set("value", this.value); ';
  		$colScript .= '  } ';
  		$colScript .= '  formChanged();';
  		$colScript .= '</script>';
  	} else if ($colName=="actualDueDate") {
  		$colScript .= '<script type="dojo/connect" event="onChange" >';
  		$colScript .= '  if (dijit.byId("initialDueDate").get("value")==null) { ';
  		$colScript .= '    dijit.byId("initialDueDate").set("value", this.value); ';
  		$colScript .= '  } ';
  		$colScript .= '  formChanged();';
  		$colScript .= '</script>';
  	} else     if ($colName=="idle") {
  		$colScript .= '<script type="dojo/connect" event="onChange" >';
  		$colScript .= '  if (this.checked) { ';
  		$colScript .= '    if (dijit.byId("idleDate").get("value")==null) {';
  		$colScript .= '      var curDate = new Date();';
  		$colScript .= '      dijit.byId("idleDate").set("value", curDate); ';
  		$colScript .= '    }';
  		$colScript .= '  } else {';
  		$colScript .= '    dijit.byId("idleDate").set("value", null); ';
  		$colScript .= '  } ';
  		$colScript .= '  formChanged();';
  		$colScript .= '</script>';
  	} else if ($colName=="done") {
  		$colScript .= '<script type="dojo/connect" event="onChange" >';
  		$colScript .= '  if (this.checked) { ';
  		$colScript .= '    if (dijit.byId("doneDate").get("value")==null) {';
  		$colScript .= '      var curDate = new Date();';
  		$colScript .= '      dijit.byId("doneDate").set("value", curDate); ';
  		$colScript .= '    }';
  		$colScript .= '  } else {';
  		$colScript .= '    dijit.byId("doneDate").set("value", null); ';
  		$colScript .= '    if (dijit.byId("idle").get("checked")) {';
  		$colScript .= '      dijit.byId("idle").set("checked", false);';
  		$colScript .= '    }';
  		$colScript .= '  } ';
  		$colScript .= '  formChanged();';
  		$colScript .= '</script>';
  	}
  	return $colScript;
  }
  
  public function save() {
    $result = null;
    $old = $this->getOld (false);
	$oldResource = $old->idResource;
	
	if (! $this->name) {
		$this->name=SqlList::getNameFromId('PokerSessionType',$this->idPokerSessionType) . " " . $this->pokerSessionDate;
	}
	$listTeam=array_map('strtolower',SqlList::getList('Team','name'));
	$listName=array_map('strtolower',SqlList::getList('Affectable'));
	$listUserName=array_map('strtolower',SqlList::getList('Affectable','userName'));
	$listInitials=array_map('strtolower',SqlList::getList('Affectable','initials'));
	$this->PokerSessionPlanningElement->idle=$this->idle;
	$this->PokerSessionPlanningElement->done=$this->done;
	$this->PokerSessionPlanningElement->validatedStartDate=$this->pokerSessionDate;
	$this->PokerSessionPlanningElement->validatedEndDate=$this->pokerSessionDate;
	if (! $this->PokerSessionPlanningElement->assignedWork) {
		$this->PokerSessionPlanningElement->plannedStartDate=$this->pokerSessionDate;
		$this->PokerSessionPlanningElement->plannedEndDate=$this->pokerSessionDate;
	}
	$this->pokerSessionStartDateTime=$this->pokerSessionDate.' '.$this->pokerSessionStartTime;
	$this->pokerSessionEndDateTime=$this->pokerSessionDate.' '.$this->pokerSessionEndTime;
	
    $result = parent::save ();
    if (! strpos ( $result, 'id="lastOperationStatus" value="OK"' )) {
      return $result;
    }
	
    if((Parameter::getGlobalParameter('autoSetAssignmentByResponsible')=="YES" and !SqlElement::isCopyInProgress()  and !$this->PokerSessionPlanningElement->isManualProgress) or RequestHandler::isCodeSet('selectedResource')){
    	$proj=new Project($this->idProject,true);
    	$type=new Type($proj->idProjectType);
    	$resource=$this->idResource;
    	if ($type->code!='ADM' and $resource and trim ( $resource ) != '' and ! trim ( $oldResource ) and stripos ( $result, 'id="lastOperationStatus" value="OK"' ) > 0) {
    		// Add assignment for responsible
    		$habil = SqlElement::getSingleSqlElementFromCriteria ( 'HabilitationOther', array(
    				'idProfile' => getSessionUser ()->getProfile ( $this->idProject ),
    				'scope' => 'assignmentEdit') );
    		if ($habil and $habil->rightAccess == 1) {
    			$ass = new Assignment ();
    			$crit = array('idResource' => $resource, 'refType' => 'PokerSession', 'refId' => $this->id);
    			$cpt=$ass->countSqlElementsFromCriteria($crit);
    			if ($cpt == 0) {
    				$ass->idProject = $this->idProject;
    				$ass->refType = 'PokerSession';
    				$ass->refId = $this->id;
    				$ass->idResource = $resource;
    				$ass->assignedWork = 0;
    				$ass->realWork = 0;
    				$ass->leftWork = 0;
    				$ass->plannedWork = 0;
    				$ass->notPlannedWork = 0;
    				$ass->rate = '100';
    				if ($this->PokerSessionPlanningElement->validatedWork and $this->PokerSessionPlanningElement->validatedWork>$this->PokerSessionPlanningElement->assignedWork) {
    					$ass->assignedWork=$this->PokerSessionPlanningElement->validatedWork-$this->PokerSessionPlanningElement->assignedWork;
    					$ass->leftWork=$ass->assignedWork;
    				}
    				$ass->save ();
    				$pokerM = new PokerResource();
    				$pokerM->idPokerSession = $this->id;
    				$pokerM->idResource = $resource;
    				$pokerM->save();
    			}
    		}
    	}
    }
    return $result;
  }
  
  public function drawSpecificItem($item) {
  global $print;
    $canUpdate=securityGetAccessRightYesNo('menuPokerSession', 'update', $this) == "YES";
    $result = "";
    $pItem = new PokerItem();
    $noItem = $pItem->countSqlElementsFromCriteria(array('idPokerSession'=>$this->id));
    if($item=="startPokerSession"){
    	if ($print or !$canUpdate or !$this->id or $this->idle or !$noItem) {
    		return "";
    	}
    	$name=(!$this->handled or $this->done)?i18n('pokerSessionStart'):i18n('pokerSessionStop');
    	$result .= '<tr><td valign="top" class="label"><label></label></td><td>';
    	$result .= '<button id="startPokerSession" dojoType="dijit.form.Button" showlabel="true"';
    	$result .= ' title="' . $name . '" class="roundedVisibleButton">';
    	$result .= '<span>' . $name. '</span>';
    	$result .=  '<script type="dojo/connect" event="onClick" args="evt">';
    	$result .= '   if (checkFormChangeInProgress()) {return false;}';
    	if(!$this->handled or $this->done){
    		$result .=  '  startPokerSession('.$this->id.');';
    	}else{
    		$result .=  '  stopPokerSession('.$this->id.');';
    	}
    	$result .= '</script>';
    	$result .= '</button>';
    	$result .= '</td></tr>';
    	return $result;
    }
    if($item=="pausePokerSession"){
    	if ($print or !$canUpdate or !$this->id or $this->idle or !$this->handled or !$noItem or $this->done) {
    		return "";
    	}
    	$name=i18n('pokerSessionPause');
    	$result .= '<tr><td valign="top" class="label"><label></label></td><td>';
    	$result .= '<button id="pausePokerSession" dojoType="dijit.form.Button" showlabel="true"';
    	$result .= ' title="' . $name . '" class="roundedVisibleButton">';
    	$result .= '<span>' . $name. '</span>';
    	$result .=  '<script type="dojo/connect" event="onClick" args="evt">';
    	$result .= '   if (checkFormChangeInProgress()) {return false;}';
  		$result .=  '  startPausePokerSession('.$this->id.');';
    	$result .= '</script>';
    	$result .= '</button>';
    	$result .= '</td></tr>';
    	return $result;
    }
    if($item=="pokerItem"){
    	drawPokerItem($this);
    }
    if($item=="pokerVote" and $this->handled){
    	drawPokerVote($this);
    }
  }
}?>