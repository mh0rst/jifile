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

class JifileViewLucene extends JViewLegacy {
	
	/**
	 * Plugin Registry for Lucene  
	 * @var array
	 */
	protected $pluginLucene = null;
	
	public function __construct() {
		// get PluginLucene
		$this->pluginLucene = jifilehelper::getPluginLuceneInstance();
		
		parent::__construct();
	}
	
	function display($tpl = null) {
		$option = JRequest::getCmd('option');
		
		// add document to templates
		$this->setDocument();
		// verify authorise
		if (!JFactory::getUser()->authorise('core.index', 'com_jifile'))
		{
			$this->setToolbar($option, false);
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$index_path = $this->get('Index_path');
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
		
		$params = JComponentHelper::getParams( 'com_jifile' );
		$basepath = jifilehelper::getCorrectPath($params->get( 'base_path' ));
		$realpath = realpath(JPATH_SITE.DS.$basepath);
		$basepath = str_replace(JPATH_SITE.DS, '', $realpath);
		
		// recupero dell'oggetto JApplication
		$app = JFactory::getApplication();

		$this->setToolbar($option);
		
		// Recupero dei filtri 
		// search filter
		$filterString = $app->getUserStateFromRequest( $option.'search_lucene', 'search', '', 'string' );
		
		$filterField 		 = $app->getUserStateFromRequest( $option.'filter_field_lucene', 'filter_field', '', 'string' );
		$filterSearchphrases = $app->getUserStateFromRequest( $option.'filter_searchphrases_lucene', 'filter_searchphrases', '', 'string' );
		
		$filter_order		= $app->getUserStateFromRequest( $option.'filter_order',	'filter_order',		null,	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $option.'filter_order_Dir','filter_order_Dir',	null,	'word' );
		
		// pagination start end limit
		$limitstart = $app->getUserStateFromRequest( $option.'limitstart_lucene', 'limitstart', 0, 'number' );
		$limit 		= $app->getUserStateFromRequest( $option.'limit_lucene', 'limit', 10, 'number' );

		if($limit == 0 || JRequest::getVar('invioform', false)) {
			$app->setUserState($option.'limitstart_lucene', 0);
			JRequest::setVar('limitstart', 0);
			$limitstart = 0;
		}
		
		$index = $model->getIndex();
		$fieldNames = $index->getFieldNames(true);
		
		$filter['search'] 		= $filterString;
		$filter['fields'] 		= $this->getFilterFields($fieldNames, $filterField);
		$filter['searchphrase'] = $this->getFilterSearchphrases($filterSearchphrases);
		$filter['order'] 		= $filter_order;
		$filter['order_Dir'] 	= $filter_order_Dir;
		
		//report index
		$count = $index->count(); //Totale file inseriti
		$numDocs = $index->numDocs(); //Totale file indicizzati
		$numDelete = $count-$numDocs; //Totale file eliminati
		$optimize = !$index->hasDeletions(); //Ottimizzazione
		
		//gestione ricerca + cache
		if(!empty($filter['search'])) {
			// se il campo ricerca e' pieno
			$cacheId = md5($index_path.$filterField.$filter['search'].$filterSearchphrases.$filter_order.$filter_order_Dir);
			if(!($luceneSearch = jifilehelper::getCache($cacheId))) {
				if(JDEBUG) {echo 'NO CACHE<br/>';}
				//no cache eseguo ricerca e set cache
				$luceneSearch = $model->search($filter['search'], $filterField, $filterSearchphrases, $filter_order, $filter_order_Dir);
				$luceneSearch = $this->getDocuments($luceneSearch);
				jifilehelper::setCache($cacheId, $luceneSearch, array('lucene'));
			}
			$tot = count($luceneSearch);
			$cacheId = md5("PAG_$cacheId.$tot.$limitstart.$limit");
			if(!($lucene = jifilehelper::getCache($cacheId))) {
				if(JDEBUG) {echo 'NO CACHE<br/>';}
				if($limit != 0) {
					$lucene = array_slice($luceneSearch, $limitstart, $limit, true);
				} else {
					$lucene = $luceneSearch;
				}
				jifilehelper::setCache($cacheId, $lucene, array('lucene'));
			}
			unset($luceneSearch);
		} else {
			//no ricerca tutti i record
			$tot = $count;
			
			$cacheId = md5("ALL_$index_path.$tot.$limitstart.$limit");
			
			if(!($lucene = jifilehelper::getCache($cacheId))) {
				if(JDEBUG) {echo 'NO CACHE<br/>';}
				$lucene = $this->getAllDocuments($index, $tot, $limitstart, $limit);
				jifilehelper::setCache($cacheId, $lucene, array('lucene'));
			}
		}
		
		$fieldView = array('name', 'key');
		if(!empty($filterField)) {
			array_push($fieldView, $filterField);
		}
				
		jimport('joomla.html.pagination');
	    $pageNav = new JPagination($tot, $limitstart, $limit);
		
		$arrayLuceneFilter = array();
		// Invoke luceneFilter() method from plugin
		if (!empty($this->pluginLucene)) {
			foreach ($this->pluginLucene as $plugin) {
				$arrayLuceneFilter = $arrayLuceneFilter + $plugin->luceneFilter();	
			}	
		}
		
		$this->assign('luceneFilters', $arrayLuceneFilter);
		$this->assign('lucene', $lucene);
		$this->assign('fieldNames', $fieldNames);
		$this->assign('fieldView', $fieldView);
		$this->assign('count', $count);
		$this->assign('numDocs', $numDocs);
		$this->assign('numDelete', $numDelete);
		$this->assign('optimize', $optimize);
		$this->assign('listFilter', $filter);
		$this->assign('pagination', $pageNav);
		$this->assign('basepath', $basepath);
				
		parent::display($tpl);
	}
	
	function getDocuments($lucene) {
		$result = array();
		if(empty($lucene)) {
			return $result;
		}

		foreach ($lucene as $hit) {
			$result[$hit->id]['score'] = $hit->score;
			$result[$hit->id]['doc'] = $hit->getDocument();
		}
		return $result;
	}
	
	function getAllDocuments($index, $tot, $limitstart, $limit) {
		$result = array();

		$limit = ($limit == 0 || $limit>$tot) ? $tot : $limit;
		for ($i = $limitstart, $j=0; $i < $tot && $j < $limit; $i++, $j++) {
			if ($index->isDeleted($i)) {
				$result[$i]['isDeleted'] = true;
			}
			$result[$i]['doc'] = $index->getDocument($i);
		}

		return $result;
	}
	
	function getFilterFields($fieldNames, $filterField = '') {
		$options = array();
		$options[] = JHTML::_('select.option', '', '- '.JText::_('ALL_FIELD').' -');
		foreach ($fieldNames as $key => $value) {
			$options[] = JHTML::_('select.option', $key, $value);
		}
		
		// valore di default (da recuperare dalla request)
		$selectField = JHTML::_('select.genericlist', $options, 'filter_field', 'class="inputbox"', 'value', 'text', $filterField);
		
		return $selectField;
	}
	
	function getFilterSearchphrases($filterSearchphrases) {
		$searchphrases 		= array();
		$searchphrases[] 	= JHTML::_('select.option',  'any', JText::_( 'ANY_WORDS' ) );
		$searchphrases[] 	= JHTML::_('select.option',  'all', JText::_( 'ALL_WORDS' ) );
		$searchphrases[] 	= JHTML::_('select.option',  'exact', JText::_( 'EXACT_PHRASE' ) );
		$searchphrases[] 	= JHTML::_('select.option',  'wildcard', JText::_( 'PARTIAL_PHRASE' ) );
		$selectSearchphrases = JHTML::_('select.genericlist',  $searchphrases, 'filter_searchphrases', '', 'value', 'text', $filterSearchphrases);
		
		return $selectSearchphrases;
	}
	
	function setToolbar($option, $all = true) {
		$canDo = jifilehelper::getActions();
		
		JToolBarHelper::title('JiFile ['.JText::_('index').']', 'logo');
		
		// Invoke setToolbar() method from plugin
		if (!empty($this->pluginLucene)) {
			foreach ($this->pluginLucene as $plugin) {
				$plugin->setToolbar();	
			}	
		}
		
		JToolBarHelper::help('JiFle Configuration', '', 'http://www.isapp.it/documentazione-jifile/24-lindice-di-jifile.html');
		JToolBarHelper::divider();
		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton( 'Link', 'cache', 'CLEAR_CACHE', 'index.php?option='.$option.'&task=clearCache&from=lucene' );
		JToolBarHelper::divider();
		if($all) {
			
			if (AdapterForJoomlaVersion::getInstance()->is(AdapterForJoomlaVersion::JOOMLA_2X)) {
				$bar->appendButton( 'Link', 'optimize','Optimize', 'index.php?option=com_jifile&task=lucene.startoptimize&view=optimize&tmpl=component" onclick="jQuery.colorbox({ href: this.href, width: \'500px\', height: \'400px\' }); return false;' );
			} else {
				$title = JText::_('OPTIMIZE');
				$dhtml = "<a class=\"btn btn-small\" id=\"btnOptimize\" href=\"index.php?option=com_jifile&amp;task=lucene.startoptimize&amp;view=optimize&amp;tmpl=component\">
				<span class=\"icon-lightning\"></span>$title</a>";
				$bar->appendButton('Custom', $dhtml, 'optimize');
			}
			JToolBarHelper::deleteList(JText::_('ARE_YOU_SURE_TO_DELETE_THE_SELECTED_FILES').'?', 'lucene.delete', 'DELETE');
			$bar->appendButton( 'Confirm', JText::_('ARE_YOU_SURE_TO_DELETE_ALL_FILES').'?', 'delete_all', 'DELETE_ALL', 'lucene.deleteAll', false);
			JToolBarHelper::divider();
		}
		//JToolBarHelper::back('CONTROL_PANEL', 'index.php?option='.$option);
		$bar->appendButton( 'Link', 'home', 'CONTROL_PANEL', 'index.php?option='.$option );
		if ($canDo->get('core.filesystem')) {
			$bar->appendButton( 'Link', 'filesystem', 'Filesystem', 'index.php?option='.$option.'&task=filesystem.' );
		}
		JRequest::setVar('hidemainmenu', 1);
	}
	
	function setDocument() {
		$doc = JFactory::getDocument();
		$doc->addStyleSheet( '../administrator/components/com_jifile/css/ifile.css?'.JIFILEVER );
		jifilehelper::addJQuery(array('colorbox'));
		$doc->addScriptDeclaration ( "
									jQuery(document).ready(function($) {
								    	jQuery('a[rel*=modalx]').colorbox({maxWidth: '800px'});
										jQuery('#btnOptimize').colorbox({width: '500px', height: '400px'});
								    })" );
									
		// Invoke setDocument() method from plugin
		if (!empty($this->pluginLucene)) {
			foreach ($this->pluginLucene as $plugin) {
				$plugin->setDocument();	
			}	
		}
	}
}
