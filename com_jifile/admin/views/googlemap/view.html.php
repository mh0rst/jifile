<?php
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );

class JifileViewGoogleMap extends JViewLegacy {
	
	function display($tpl = null) {
		$error = '';
		$tpl = JRequest::getVar('tmpl', null);
		
		$lat = JRequest::getVar('lat');
		$lng = JRequest::getVar('lng');
		$zoom = JRequest::getVar('zoom');
		
		$lat = (trim($lat) == '') ? '50' : $lat; 
		$lng = (trim($lng) == '') ? '-30' : $lng; 
		$zoom = ($lat == '50' || $lng == '-30') ? '2' : $zoom; 
		
		$this->assign('lat', $lat);
		$this->assign('lng', $lng);
		$this->assign('zoom', $zoom);
		$this->assignRef('error', $error);
		if(!is_null($tpl)) {
			jifilehelper::addJQuery(array('colorbox'));
			$doc = JFactory::getDocument();
			$doc->addScript( 'http://www.google.com/jsapi' );
		}
		parent::display($tpl);
	}	
}
