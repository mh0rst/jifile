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

require_once JPATH_IFILE_LIBRARY.'/ifile/IFileConfig.php';

/**
 * La sezione di configurazione serve solo alla configurazione della libreria IFile.
 * Tutte le configurazioni associate alla componente JiFile dovranno essere
 * gestite nella sezione "OPZIONI". 
 * 
 */
class JifileViewConfig extends JViewLegacy {
	
	function display($tpl = null) {
		$option = JRequest::getCmd('option');
		$this->setToolbar($option);
		$this->setDocument();
		
		if (!JFactory::getUser()->authorise('core.admin', 'com_jifile'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$model = $this->getModel();
		$reqXml = JRequest::getVar('xml', array());

		$config = $this->get('Config');
		$xmlValue = array(); 

		//$rootApp = $config->getConfig('root-application');
		//$xmlValue['root-application'] = $this->setValue('root-application', ($rootApp) ? $rootApp : JPATH_SITE.DS);
		$xmlValue['root-application'] = $this->setValue('root-application', JPATH_SITE.DS);
		
		//$xmlValue['table-name'] = $this->setValue('table-name', $config->getConfig('table-name'));
		$xmlValue['timelimit'] = $this->setValue('timelimit', $config->getConfig('timelimit'));
		$xmlValue['memorylimit'] = $this->setValue('memorylimit', $config->getConfig('memorylimit'));;
		
		$default = $this->setValue('duplicate', $config->getConfig('duplicate'));
		$listItemid[] 	= JHTML::_('select.option',  '1', JText::_( 'jYes' ) );
		$listItemid[] 	= JHTML::_('select.option',  '0', JText::_( 'jNo' ) );
		$xmlValue['duplicate'] = JHTML::_('select.genericlist',  $listItemid, 'xml[duplicate]', '', 'value', 'text', (is_null($default) ? 0 : $default));
		unset($listItemid);
		
		$default = $this->setValue('encoding', $config->getConfig('encoding'));
		$listEncoding[] 	= JHTML::_('select.option',  '', '' );
		$listEncoding[] 	= JHTML::_('select.option',  'UTF-8', JText::_( 'UTF-8' ) );			//ASCII compatible multi-byte 8-bit Unicode
		$listEncoding[] 	= JHTML::_('select.option',  'ASCII', JText::_( 'ASCII' ) );
		$listEncoding[] 	= JHTML::_('select.option',  'ISO-8859-1', JText::_( 'ISO8859-1' ) ); 	//Western European, Latin-1
		$listEncoding[] 	= JHTML::_('select.option',  'ISO-8859-15', JText::_( 'ISO8859-15' ) );	//Western European, Latin-9
		$listEncoding[] 	= JHTML::_('select.option',  'CP1256', JText::_( 'CP1256' ) );			
		$listEncoding[] 	= JHTML::_('select.option',  'Windows-1252', JText::_( 'Windows-1252' ) );	//Windows specific charset for Western European
		$xmlValue['encoding'] = JHTML::_('select.genericlist',  $listEncoding, 'xml[encoding]', '', 'value', 'text', $default);
		
		// DOC to Text
		$doctotxt			= $config->getConfig('doctotxt');
		$defaultType 	 	= (isset($reqXml['doctotxt']['attributes']['type'])) ? $reqXml['doctotxt']['attributes']['type'] : $doctotxt['type'];
		$defaultEncoding 	= (isset($reqXml['doctotxt']['attributes']['encoding'])) ? $reqXml['doctotxt']['attributes']['encoding'] : $doctotxt['encoding'];
		$listDocToTxt[] 	= JHTML::_('select.option',  '', '' );
		$listDocToTxt[] 	= JHTML::_('select.option',  'PHP', JText::_( 'PHP' ) );			
		$listDocToTxt[] 	= JHTML::_('select.option',  'COM', JText::_( 'COM' ) );
		$listDocToTxt[] 	= JHTML::_('select.option',  'ANTIWORD', JText::_( 'ANTIWORD' ) );
		$xmlValue['doctotxt']['attributes']['type'] 	  = JHTML::_('select.genericlist',  $listDocToTxt, 'xml[doctotxt][attributes][type]', '', 'value', 'text', $defaultType); 
		$xmlValue['doctotxt']['attributes']['encoding'] = (isset($reqXml['doctotxt']['attributes']['encoding'])) ? $reqXml['doctotxt']['attributes']['encoding'] : $defaultEncoding;  
		
		// XPDF
		$pdftotext  = $config->getXpdf('pdftotext');
		$pdfinfo 	= $config->getXpdf('pdfinfo');
		$xmlValue['xpdf'] = array();
		$xmlValue['xpdf']['pdftotext']['executable'] = (isset($reqXml['xpdf']['pdftotext']['executable'])) ? $reqXml['xpdf']['pdftotext']['executable'] : ((!empty($pdftotext['executable'])) ? $pdftotext['executable'] : "") ;
		$xmlValue['xpdf']['pdftotext']['xpdfrc'] = (isset($reqXml['xpdf']['pdftotext']['xpdfrc'])) ? $reqXml['xpdf']['pdftotext']['xpdfrc'] : ((!empty($pdftotext['xpdfrc'])) ? $pdftotext['xpdfrc'] : "") ;
		$xmlValue['xpdf']['pdfinfo']['executable'] = (isset($reqXml['xpdf']['pdfinfo']['executable'])) ? $reqXml['xpdf']['pdfinfo']['executable'] : ((!empty($pdfinfo['executable'])) ? $pdfinfo['executable'] : "") ;
		$xmlValue['xpdf']['pdfinfo']['xpdfrc'] = (isset($reqXml['xpdf']['pdfinfo']['xpdfrc'])) ? $reqXml['xpdf']['pdfinfo']['xpdfrc'] : ((!empty($pdfinfo['xpdfrc'])) ? $pdfinfo['xpdfrc'] : "") ;
		
		// Analyzer
		$default = (isset($reqXml['analyzer']['type']['default'])) ? $reqXml['analyzer']['type']['default'] : ($config->getConfig('xml-custom-analyzer') ? ' ' : $config->getConfig('analyzer'));
		$listItemid[] 	= JHTML::_('select.option',  '', '' );
		$listItemid[] 	= JHTML::_('select.option',  'Text', JText::_( 'Text' ) );
		$listItemid[] 	= JHTML::_('select.option',  'TextNum', JText::_( 'TextNum' ) );
		$listItemid[] 	= JHTML::_('select.option',  'Text_CaseInsensitive', JText::_( 'Text_CaseInsensitive' ) );
		$listItemid[] 	= JHTML::_('select.option',  'TextNum_CaseInsensitive', JText::_( 'TextNum_CaseInsensitive' ) );
		$listItemid[] 	= JHTML::_('select.option',  'Utf8', JText::_( 'Utf8' ) );
		$listItemid[] 	= JHTML::_('select.option',  'Utf8Num', JText::_( 'Utf8Num' ) );
		$listItemid[] 	= JHTML::_('select.option',  'Utf8_CaseInsensitive', JText::_( 'Utf8_CaseInsensitive' ) );
		$listItemid[] 	= JHTML::_('select.option',  'Utf8Num_CaseInsensitive', JText::_( 'Utf8Num_CaseInsensitive' ) );
		$listItemid[] 	= JHTML::_('select.option',  ' ', JText::_( 'Custom-default' ) );
		$xmlValue['analyzer'] = JHTML::_('select.genericlist',  $listItemid, 'xml[analyzer][type][default]', '', 'value', 'text', $default);
		unset($listItemid);
		
		if(isset($reqXml['analyzer']['type']['custom-default']['@value']) || isset($reqXml['analyzer']['type']['custom-default']['attributes']['class'])) {
			$xmlValue['analyzer_path'] = $reqXml['analyzer']['type']['custom-default']['@value'];
			$xmlValue['analyzer_class'] = $reqXml['analyzer']['type']['custom-default']['attributes']['class'];
		} elseif ($custom_analyzer = $config->getConfig('xml-custom-analyzer')) {
			$xmlValue['analyzer_path'] = $custom_analyzer['file'];
			$xmlValue['analyzer_class'] = $custom_analyzer['class'];
		}
		
		$xmlValue['stop-words'] = (isset($reqXml['analyzer']['filters']['stop-words'])) ? $reqXml['analyzer']['filters']['stop-words'] : $config->getConfig('stop-words');
		
		$default = (isset($reqXml['analyzer']['filters']['short-words'])) ? $reqXml['analyzer']['filters']['short-words'] : $config->getConfig('short-words');
		$listItemid[] 	= JHTML::_('select.option',  '', '' );
		$listItemid[] 	= JHTML::_('select.option',  2, 2 );
		$listItemid[] 	= JHTML::_('select.option',  3, 3 );
		$listItemid[] 	= JHTML::_('select.option',  4, 4 );
		$xmlValue['short-words'] = JHTML::_('select.genericlist',  $listItemid, 'xml[analyzer][filters][short-words]', '', 'value', 'text', $default);
		unset($listItemid);
		
		if(isset($reqXml['analyzer']['filters']['custom-filters']['filter'])) {
			$xmlValue['filters'] = $reqXml['analyzer']['filters']['custom-filters']['filter'];
		} else {
			$xmlValue['filters'] = $config->getConfig('xml-filters');
		}
		
		$listItemid[] = JHTML::_('select.option',  'Keyword', 'Keyword');
		$listItemid[] = JHTML::_('select.option',  'UnIndexed', 'UnIndexed');
		$listItemid[] = JHTML::_('select.option',  'Binary', 'Binary');
		$listItemid[] = JHTML::_('select.option',  'Text', 'Text');
		$listItemid[] = JHTML::_('select.option',  'UnStored', 'UnStored');
		
		$fields = (isset($reqXml['zend-document']['fields']['field'])) ? $reqXml['zend-document']['fields']['field'] : $config->getConfig('zend-document-fields');
		// fields da non cambiare il Type
		$notChangeFields = array("name", "path", "filename", "extensionfile");
		$i = 0;
		foreach ($fields as $key => $field) {				
			
			// non deve modificare il Type dei seguenti fileds
			// per una gestione corretta dei nomi dei file e della ricerca
			if (in_array($key, $notChangeFields)) {continue;}
					
			if (isset($reqXml['zend-document']['fields']['field'][$i]['attributes']['name'])) {
				$key = $reqXml['zend-document']['fields']['field'][$i]['attributes']['name'];
			}
			
			$encoding = isset($field['encoding']) ? $field['encoding'] : '';
			$defaultType = (isset($reqXml['zend-document']['fields']['field'][$i]['attributes']['type'])) ? $reqXml['zend-document']['fields']['field'][$i]['attributes']['type'] : $field['type'];
			$defaultEnco = (isset($reqXml['zend-document']['fields']['field'][$i]['attributes']['encoding'])) ? $reqXml['zend-document']['fields']['field'][$i]['attributes']['encoding'] : $encoding;
			$xmlValue['zend-document'][$key]['type'] = JHTML::_('select.genericlist',  $listItemid, 'xml[zend-document][fields][field]['.$i.'][attributes][type]', '', 'value', 'text', $defaultType, 'xmltype'.$key);
			$xmlValue['zend-document'][$key]['encoding'] = JHTML::_('select.genericlist',  $listEncoding, 'xml[zend-document][fields][field]['.$i.'][attributes][encoding]', '', 'value', 'text', $defaultEnco, 'xmlencoding'.$key);
			$xmlValue['zend-document'][$key]['id'] = $i;
			$i++;
		}
		unset($listItemid);
		$params = JComponentHelper::getParams('com_jifile');
		
		// server 
		$server = $config->getConfig("server");
		$serverbit = $server['bit'];		
		$default = (isset($reqXml['server']['attributes']['bit'])) ? $reqXml['server']['attributes']['bit'] : $serverbit; 
		$listItemid[] 	= JHTML::_('select.option',  64, '64bit' );
		$listItemid[] 	= JHTML::_('select.option',  32, '32bit' );
		$xmlValue['serverBit'] = JHTML::_('select.radiolist',  $listItemid, 'xml[server][attributes][bit]', '', 'value', 'text', $default);
		
		// @TODO
		// Addon:
		// chiamata al metodo per la definizione della $xmlValue o dei parametri
		// $plugin->setXmlValue($this, $xmlValue);
		
		$this->assignRef('xmlValue', $xmlValue);
		
		parent::display($tpl);
	}
	
	function setValue($name, $default = null) {
		$reqXml = JRequest::getVar('xml', array());
		if (isset($reqXml[$name]) && $reqXml[$name] !== null) {
			return $reqXml[$name];
		}
		return $default;
	}
	
	function setToolbar($option) {
		$canDo = jifilehelper::getActions();
		$bar = JToolBar::getInstance('toolbar');
		
		JToolBarHelper::title( 'JiFile ['.JText::_( 'IFILE_CONFIGURATION').']', 'logo' );
		
		if ($canDo->get('core.admin')) {
			JToolBarHelper::save( 'config.save' );
			JToolBarHelper::apply('config.apply');
		}
		JToolBarHelper::divider();
		JToolBarHelper::help('JiFle Configuration', '', 'http://www.isapp.it/documentazione-jifile/17-configurare-jifile.html');
		JToolBarHelper::divider();
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_jifile');
		}
		//JToolBarHelper::back('CONTROL_PANEL', 'index.php?option='.$option);
		$bar->appendButton( 'Link', 'home', 'CONTROL_PANEL', 'index.php?option='.$option );
	}
	
	function setDocument() {
		$doc = JFactory::getDocument();
		JHtml::_('behavior.framework', true);
		$doc->addStyleSheet( '../administrator/components/com_jifile/css/ifile.css?'.JIFILEVER );
		$doc->addScript( '../administrator/components/com_jifile/js/config.js?'.JIFILEVER );
		jifilehelper::addJQuery(array('colorbox'));
		$doc->addScriptDeclaration ( "
									jQuery(document).ready(function($) {								    	
										jQuery('#xpdfrcfile').colorbox({width: '850px', height: '400px', iframe: true});										
								    })" );
	}
}