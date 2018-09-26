<?php
/**
 * @package		Rich Snippets for jEvents
 * @author		www.joomdev.com
 * @author		Created on March 2016
 * @copyright	Copyright (C) 2009 - 2016 www.joomdev.com. All rights reserved.
 * @license		GNU GPL2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;


class PlgSystemJeventsnippet extends JPlugin
{
	
	public function onAfterDispatch()
	{
		
		// Check that we are in the site application.
		if (JFactory::getApplication()->isAdmin())
		{
			return true;
		}
		
		include_once(JPATH_BASE.'/components/com_jevents/libraries/datamodel.php');
		include_once(JPATH_BASE.'/administrator/components/com_jevents/libraries/config.php');
		$doc = JFactory::getDocument();
		$input = JFactory::getApplication()->input;
		$extension = $input->get('option', '', 'cmd');
		$cfg_	 = JEVConfig::getInstance();
		
		//echo "<pre>";print_r($input);echo "</pre>";
		if(($doc->getType() == 'html') && $extension == 'com_jevents'){
			$dataHeader_	=	array();
			$jeventtask_  = $input->get('task');
			$dataObject_  = new JEventsDataModel;
			
			switch($jeventtask_){
				case 'cat.listevents' :
			
					$limitstart_ = intval( JRequest::getVar('start',JRequest::getVar('limitstart',0)));
					$limit_ = intval(JFactory::getApplication()->getUserStateFromRequest( 'jevlistlimit','limit',$cfg_->get("com_calEventListRowsPpg",15)));
					
					$catids_ 	= JRequest::getVar('catids',"") ;
					$catids_ = explode("|",$catids_);
					$jeventData_ = $dataObject_->getCatData($catids_,$cfg_->get('com_showrepeats',0),$limit_,$limitstart_);
					
					if(!empty($jeventData_['rows'])){
						$total_ = count($jeventData_['rows']);						
						$a_ = 1;
						$b = $jeventData_['rows'];
						foreach($jeventData_['rows'] as $k_=>$v_){
								$date_			 				= (isset($v_->start_date)) ? date('Y-m-d',strtotime($v_->start_date)) : '';
								$time_ 			 				= (isset($v_->start_time)) ? date('h:i',strtotime($v_->start_time)) : '';
								$rowlink_ 						= $v_->viewDetailLink($v_->yup(), $v_->mup(), $v_->dup(), false);
								$decs_                          = strip_tags($v_->description);
								$contact                        = $v_->contact;
								
								$name_							= 	(isset($v_->_title)) ? $v_->_title : '' ;
								$startDate_					 	= 	$v_->startrepeat;
								$url_					 		= 	JURI::Root().JRoute::_($rowlink_);
								
								$location_					 	= 	(isset($v_->_location)) ? $v_->_location : '' ;		
								$content_	= '';
								$content_ .= ' 
								{
								  "@context": "http://schema.org",
								  "@type": "Event",
								  "name": "'.$name_.'",
								  "startDate" : "'.$date_.'",
								  "description" : "'.$decs_.'",
								  "url" : "'.$url_.'",
								  "location" : {
									"@type" : "Place",
									"name":"'.$contact.'",
									"address" : "'.$location_.'"
								  }
								} 
								';
								if($total_ != $a_){
									$content_ .= '</script>
									<script type="application/ld+json">';
								}
								
								$doc->addScriptDeclaration($content_,'application/ld+json');
								$a_ ++;
						}
					}
						
				break;
				
				case 'year.listevents' :
				
					$limitstart_ = intval( JRequest::getVar('start',JRequest::getVar('limitstart',0)));
					$limit_ = intval(JFactory::getApplication()->getUserStateFromRequest( 'jevlistlimit','limit',$cfg_->get("com_calEventListRowsPpg",15)));
					
					$year_  = $input->get('year');
					$jeventData_ = $dataObject_->getCatData($year_,$limit_,$limitstart_);
					
					if(!empty($jeventData_['rows'])){
						$total_ = count($jeventData_['rows']);						
						$a_ = 1;
						foreach($jeventData_['rows'] as $k_=>$v_){
							
								$rowlink_ 						= $v_->viewDetailLink($v_->yup(), $v_->mup(), $v_->dup(), false);
								$name_							= 	(isset($v_->_title)) ? $v_->_title : '' ;
								$startDate_ 					= 	$v_->startrepeat;
								$url_					 		= 	JURI::Root().JRoute::_($rowlink_);
								$decs = strip_tags($v_->description);
								$contact                        = $v_->contact;
								
								$location_						= 	(isset($v_->_location)) ? $v_->_location : '' ;	
								
								$content_ = ' 
								{
									"@context": "http://schema.org",
									  "@type": "Event",
									  "name": "'.$name_.'",
									  "startDate" : "'.$date_.'",
									  "description" : "'.$decs.'",
									  "url" : "'.$url_.'",
									  "location" : {
										"@type" : "Place",
										"name" : "'.$contact.'",
										"address" : "'.$location_.'"
								  }
								} 
								';
								if($total_ != $a_){
									$content_ .= '</script>
									<script type="application/ld+json">';
								}
								
								$doc->addScriptDeclaration($content_,'application/ld+json');
								$a_ ++;
						}
					}
				break;
				
				case 'month.calendar' :
				
					$limitstart_ = intval( JRequest::getVar('start',JRequest::getVar('limitstart',0)));
					$limit_ = intval(JFactory::getApplication()->getUserStateFromRequest( 'jevlistlimit','limit',$cfg_->get("com_calEventListRowsPpg",15)));
					$year_  		= $input->get('year');
					$month_  		= $input->get('month');
					$day_  			= $input->get('day');
					$startDate_		= $year_.'-'.$month_.'-'.$day_;		
					$jeventData_ 	= $dataObject_->getCatData($startDate_,$startDate_,$limit_,$limitstart_);
					
					if(!empty($jeventData_['rows'])){
						$total_ = count($jeventData_['rows']);						
						$a_ = 1;
						foreach($jeventData_['rows'] as $k_=>$v_){
							
								$rowlink_ 					= $v_->viewDetailLink($v_->yup(), $v_->mup(), $v_->dup(), false);
								
								$name_						= 	(isset($v_->_title)) ? $v_->_title : '' ;
								$startDate_					= 	$v_->startrepeat;
								$url_						= 	JURI::Root().JRoute::_($rowlink_);
								$decs = strip_tags($v_->description);
								$contact = $v_->contact;
								$location_					= 	(isset($v_->_location)) ? $v_->_location : '' ;

								$content_ = ' 
								{
								  "@context": "http://schema.org",
								  "@type": "Event",
								  "name": "'.$name_.'",
								  "startDate" : "'.$date_.'",
								  "description" : "'.$decs.'",
								  "url" : "'.$url_.'",
								  "location" : {
									"@type" : "Place",
									"name" : "'.$contact.'",
									"address" : "'.$location_.'"
								  }
								} 
								';
								if($total_ != $a_){
									$content_ .= '</script>
									<script type="application/ld+json">';
								}
								
								$doc->addScriptDeclaration($content_,'application/ld+json');
								$a_ ++;		
						}
					}
					
				break;
				
				case 'week.listevents' :
				
					$dataHeader2_ = array();
					$limitstart_ = intval( JRequest::getVar('start',JRequest::getVar('limitstart',0)));
					$limit_ = intval(JFactory::getApplication()->getUserStateFromRequest( 'jevlistlimit','limit',$cfg_->get("com_calEventListRowsPpg",15)));
					$year_  		= $input->get('year');
					$month_  		= $input->get('month');
					$day_  			= $input->get('day');
					$startDate_		= $year_.'-'.$month_.'-'.$day_;		
					$jeventData_ 	= $dataObject_->getWeekData($year_,$month_,$day_);
					
					if(!empty($jeventData_['days'])){
						$i = 1;
						foreach($jeventData_['days'] as $d=>$dv){
							if(!empty($dv['rows'])){
								$total_ = count($dv['rows']);						
								$a_ = 1;
								foreach($dv['rows'] as $k_=>$v_){
									$rowlink_ 						= $v_->viewDetailLink($v_->yup(), $v_->mup(), $v_->dup(), false);
									$name_							= 	(isset($v_->_title)) ? $v_->_title : '' ;
									$startDate_						= 	$v_->startrepeat;
									$url_							= 	JURI::Root().JRoute::_($rowlink_);									
									$location_						= 	(isset($v_->_location)) ? $v_->_location : '' ;	
									$decs = strip_tags($v_->description);
									$contact = $v_->contact;
									$contact = 
									$content_ = ' 
									{
										  "@context": "http://schema.org",
										  "@type": "Event",
										  "name": "'.$name_.'",
										  "startDate" : "'.$date_.'",
										  "description" : "'.$decs.'",
										  "url" : "'.$url_.'",
										  "location" : {
											"@type" : "Place",
											"name" : "'.$contact.'",
											"address" : "'.$location_.'"
									  }
									} 
									';
									if($total_ != $a_){
										$content_ .= '</script>
										<script type="application/ld+json">';
									}
									
									$doc->addScriptDeclaration($content_,'application/ld+json');
									$a_ ++;		
								}
							
							}
							
						}
						
					}
					$dataHeader_	=	array_values($dataHeader_);
				break;
				
				case 'icalrepeat.detail' :
					$dataHeader_	= array();
					$evid_  		= $input->get('evid');
					$jevtype_   	= 'icaldb';
					$year_  		= $input->get('year');
					$month_  		= $input->get('month');
					$day_  			= $input->get('day');
					$uid_  			= $uid = urldecode((JRequest::getVar( 'uid', "" )));
					$jeventData_	= $dataObject_->getEventData($evid_,$jevtype_,$year_, $month_,$day_,$uid_);				
					
					
					$date_			 = (isset($jeventData_['row']->start_date)) ? date('Y-m-d',strtotime($jeventData_['row']->start_date)) : '';
					$time_ 			 = (isset($jeventData_['row']->start_time)) ? date('h:i',strtotime($jeventData_['row']->start_time)) : '';
					
					if(!empty($jeventData_)){
						$rowlink_ 					= $jeventData_['row']->viewDetailLink($jeventData_['row']->yup(), $jeventData_['row']->mup(), $jeventData_['row']->dup(), false);
						
						$name_						= 	(isset($jeventData_['row']->_title)) ? $jeventData_['row']->_title : '' ;
						$startDate_ 				= 	$date_.'T'.$time_;
						$url_						= 	JURI::Root().JRoute::_($rowlink_);
						$decs = strip_tags($jeventData_['row']->description);
						$contact = $jeventData_['row']->contact;
						$location_ 					= 	(isset($jeventData_['row']->_location)) ? $jeventData_['row']->_location : '' ;
						
						$content_ = ' 
						{
						  "@context": "http://schema.org",
						  "@type": "Event",
						  "name": "'.$name_.'",
						  "startDate" : "'.$startDate_.'",
						  "description" : "'.$decs.'",
						  "url" : "'.$url_.'",
						  "location" : {
							"@type" : "Place",
							"name" : "'.$contact.'",
							"address" :{
								"location" :"'.$location_.'"
							}
						  }
						} 
						';
						$doc->addScriptDeclaration($content_,'application/ld+json');
						
					}				
				break;
				
				default :
					return true;
				break;
			}
			
				
			return true;
		}else{
			return true;
		}
	
	}
}
