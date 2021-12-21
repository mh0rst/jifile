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

class JifileViewFilesystem extends JViewLegacy {
	
	private $maxByte = 5242880;
	
	/**
	 * Recupera le informazioni dal model e le formatta per presentarle a video
	 *  
	 * @param object $tpl [optional]
	 * @return void
	 */
	function display($tpl = null) {

		if (jifilehelper::inDebug() && JRequest::getVar('tpl') == 'debug') {
			parent::display('debug');
			return false;
		}
		
		$this->setDocument();
		// recupero della option dalla request
		$option    = JRequest::getCMD('option');
		if (!JFactory::getUser()->authorise('core.filesystem', 'com_jifile'))
		{
			$this->setToolbar($option, false);
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$params = JComponentHelper::getParams( 'com_jifile' );
		$basepath = jifilehelper::getCorrectPath($params->get( 'base_path' ));
		$index_path = jifilehelper::getIndexPath();
		
		$model = $this->getModel();
		
		$errore = null;
		$rootApp = '';
		if(!$index_path) {
			$errore = JText::_('NO_PATH_CONFIGURED_FOR_THE_INDEX');
		} elseif (!jifilehelper::checkPathConfig($rootApp)) {
			$errore = JText::_('ERROR_PATH_FILE').': '.$rootApp;
		} elseif ($model->getError()) {
			$errore = $model->getError();
		}
		
		if($errore) {
			$this->setToolbar($option, false);
			$this->assign('error_pref', $errore.'. '.JText::_('BACK_CONTROL_PANEL'));
			parent::display($tpl);
			return false;
		}
		
		$dir = urldecode(JRequest::getVar('dir', null));
		
		// recupero dell'oggetto JApplication
		$app = JFactory::getApplication();
		// Array dei parametri di ricerca da presentare
		$listFilter = array();
		// Array dei parametri di ricerca inseriti dall'utente
		$filter = array();
		
		// Recupero dei filtri 
		// search filter
		$filterString = $app->getUserStateFromRequest( $option.'search', 'search', '', 'string' );
		$filterString = JString::strtolower( $filterString );
		// search extension
		$filterExt = $app->getUserStateFromRequest( $option.'filter_ext', 'filter_ext', '*', 'string' );
		$filterExt = JString::strtolower( $filterExt );
		// pagination start end limit
		$limitstart = $app->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'number' );
		$limit 		= $app->getUserStateFromRequest( $option.'limit', 'limit', 10, 'number' );
		// Filtri utente
		$filter['search'] = $filterString;
		$filter['ext'] = $filterExt;
		
		if((!is_null($dir) && strpos($dir, '..') !== false) || $dir == DS) {
			$dir = null;
		}
		
		$this->setToolbar($option, true, $dir);
		
		// recupero della lista dei file presenti
		// nella cartella settata nelle Preference
		$listfile = $model->getListfile($dir, $filter);
		
		// recupero delle estensioni dopo la creazione della lista dei file
		$ext = $model->getExtension();
		
		// creazione della select delle extension
		$options = array();
		$options[] = JHTML::_('select.option', '*', '- '.JText::_('SELECT_EXTENSION').' -');
		foreach ($ext as $key => $value) {
			$options[] = JHTML::_('select.option', $key, '.'.$value);
		}
		
		// valore di default (da recuperare dalla request)
		$default = ($filterExt == 'none') ? 'none' : $filterExt;
		$selectExt = JHTML::_('select.genericlist', $options, 'filter_ext', null, 'value', 'text', $default);
		
		// Filtri Presentazione 
		$listFilter['search'] = $filterString;
		$listFilter['ext'] = $selectExt;
		
		//creazione paginazione
		jimport('joomla.html.pagination');
	    $pageNav = new JPagination(count($listfile), $limitstart, $limit);
	     // slice out elements based on limits
	    $listfile = array_slice($listfile, $pageNav->limitstart, $pageNav->limit);
			
		$this->setExtensionsAllows();
		//$this->setInfoMessageExtensionAllows();
		$this->assign('dir', $dir);
		$this->assign('basepath', $basepath);
		$this->assign('listFilter', $listFilter);
		$this->assign('listfile', $listfile);
		$this->assign('pagination', $pageNav);
		
		parent::display($tpl);
	}
	
	private function setInfoMessageExtensionAllows() {
		$msg = sprintf(JText::_('JIFILE_EXTENSIONS_AUTOINDEX'), implode(',&nbsp;', $this->extensionsAllows));
		JFactory::getApplication()->enqueueMessage( $msg );
	}
	
	/**
	 * Setta le estensioni consentite
	 * @return void
	 */
	private function setExtensionsAllows() {
		require_once JPATH_IFILE_LIBRARY.'/ifile/servercheck/LuceneServerCheck.php';
		
		$check = LuceneServerCheck::getInstance();		
		$extensions = $check->getExtensionsAllowed();
		$this->assign('extensionsAllows', $extensions);
		
		return true;		
	}

	/**
	 * Recupera le infomazioni del singolo File e 
	 * l'assegna alla proprieta' $this->infofile
	 * 
	 * @param pathFile $file
	 * @return void
	 */
	function loadInfoFile($file) {
		$filesystem = $this->getModel();
		$FSinfofile = $filesystem->getInfofile($file);
		
		if($FSinfofile['mime'] != 'dir') {
			$fileincache = jifilehelper::fileInCache($file);
			if($FSinfofile['size'] > $this->maxByte && !$fileincache) {
				$FSinfofile['indexed'] = 'ajax';
			} else {
				$lucene = $this->getModel('lucene');
				$FSinfofile['indexed'] = jifilehelper::checkIndex($file, $lucene);
			}
			$FSinfofile['size'] = jifilehelper::getFormatSize($FSinfofile['size']);
		}
		if(JDEBUG) {
			echo '<div style="clear:both">'.$file.': '.(($fileincache) ? '<span class="ok">cache</span> - ' : '<span class="nook">no cache</span> - ').$FSinfofile['indexed'].'</div>';
		}
		
		$this->assign('infofile', $FSinfofile);
	}
	
	function setToolbar($option, $all = true, $dir = null) {
		$canDo = jifilehelper::getActions();
		// gestione TOOLBAR
		JToolBarHelper::title('JiFile [Filesystem]', 'logo');
		JToolBarHelper::help('JiFile Filesystem', '', 'http://www.isapp.it/documentazione-jifile/23-la-sezione-filesystem.html');
		JToolBarHelper::divider();
		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton( 'Link', 'cache', 'CLEAR_CACHE', 'index.php?option='.$option.'&task=clearCache&from=filesystem' );
		$bar->appendButton( 'Link', 'refresh', 'Refresh', 'index.php?option='.$option.'&task=filesystem.&dir='.urlencode($dir) );
		JToolBarHelper::divider();
		if ($all) {
			if ($canDo->get('core.index')) {
				if (AdapterForJoomlaVersion::getInstance()->is(AdapterForJoomlaVersion::JOOMLA_2X)) {
					$bar->appendButton( 'Link', 'indexes', 'Indexes', 'index.php?option=com_jifile&task=lucene.indexing&view=indexing&tmpl=component" onclick="jQuery.colorbox({ href: this.href, width: \'640px\', height: \'580px\' }); return false;' );
				} else {
					//JHtml::_('bootstrap.modal', 'indexesModal', array('remote'=> true));
					$title = JText::_('INDEXES');
					$dhtml = "<button data-toggle=\"modal\" data-target=\"#indexesModal\" class=\"btn btn-small btn-success\">
					<i class=\"icon-indexes\" title=\"$title\"></i>
					$title</button>";
					$bar->appendButton('Custom', $dhtml, 'indexes');
				}
			}
			JToolBarHelper::divider();
		}
		//JToolBarHelper::back('CONTROL_PANEL', 'index.php?option='.$option);
		$bar->appendButton( 'Link', 'home', 'CONTROL_PANEL', 'index.php?option='.$option );
		if ($canDo->get('core.index')) {
			$bar->appendButton( 'Link', 'search', 'Index', 'index.php?option='.$option.'&task=lucene.' );
		}
		JRequest::setVar('hidemainmenu', 1);
	}
	
	function setDocument() {
		$canDo = jifilehelper::getActions();
		$doc = JFactory::getDocument();
		JHtml::_('behavior.framework');
		$doc->addStyleSheet( '../administrator/components/com_jifile/css/ifile.css?'.JIFILEVER );
		if (AdapterForJoomlaVersion::getInstance()->is(AdapterForJoomlaVersion::JOOMLA_2X)) {
			$doc->addStyleSheet( '../administrator/components/com_jifile/css/style_j25.css?'.JIFILEVER);
		}
		$doc->addStyleSheet( '../administrator/components/com_jifile/css/filetype.css?'.JIFILEVER );
		jifilehelper::addJQuery(array('colorbox'));
		if ($canDo->get('core.index')) {
			$doc->addScript( '../administrator/components/com_jifile/js/filesystemList.js?'.JIFILEVER );
		}
	}
}
