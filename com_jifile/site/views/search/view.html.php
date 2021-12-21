<?php
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/

defined('_JEXEC') or die;

require_once(JPATH_ADMINISTRATOR.'/components/com_jifile/models/lucene.php');
// @TODO
// da creare una propria classe dato che Joomla! 
// eliminerà le componenti nelle prossime versioni
require_once JPATH_ADMINISTRATOR . '/components/com_search/helpers/search.php';

class JifileViewSearch extends JViewLegacy
{
	function display($tpl = null)
	{
		$option = JRequest::getCmd('option');
		// Initialise some variables
		$app	= JFactory::getApplication();
		$pathway = $app->getPathway();
		$uri	= JFactory::getURI();

		$error	= null;
		$rows	= null;
		$results= null;
		$total	= 0;

		// Get some data from the model
		//$areas	= $this->get('areas');
		$state		= $this->get('state');
		$searchword = $state->get('keyword');
		$params = $app->getParams();
		$menus	= $app->getMenu();
		$menu	= $menus->getActive();

		// because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object($menu)) {
			$menu_params = new JRegistry;
			$menu_params->loadString($menu->params);
			if (!$menu_params->get('page_title')) {
				$params->set('page_title',	JText::_('COM_JIFILE_SEARCH'));
			}
		}
		else {
			$params->set('page_title',	JText::_('COM_JIFILE_SEARCH'));
		}

		$title = $params->get('page_title');
		
		if ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if ($params->get('menu-meta_description'))
		{
			$this->document->setDescription($params->get('menu-meta_description'));
		}

		if ($params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $params->get('menu-meta_keywords'));
		}

		if ($params->get('robots'))
		{
			$this->document->setMetadata('robots', $params->get('robots'));
		}

		// @TODO
		// si dovrebbe pensare ad un metodo per ordinare alcuni field dell'indice 
		// solo per quelli più significativi
		// si può pensare a 
		// built select lists
		/*$orders = array();
		$orders[] = JHtml::_('select.option',  'newest', JText::_('COM_SEARCH_NEWEST_FIRST'));
		$orders[] = JHtml::_('select.option',  'oldest', JText::_('COM_SEARCH_OLDEST_FIRST'));
		$orders[] = JHtml::_('select.option',  'popular', JText::_('COM_SEARCH_MOST_POPULAR'));
		$orders[] = JHtml::_('select.option',  'alpha', JText::_('COM_SEARCH_ALPHABETICAL'));
		$orders[] = JHtml::_('select.option',  'category', JText::_('JCATEGORY'));
		*/

		/*$lists = array();
		$lists['ordering'] = JHtml::_('select.genericlist', $orders, 'ordering', 'class="inputbox"', 'value', 'text', $state->get('ordering'));
		*/
		$searchphrases		= array();
		$searchphrases[]	= JHtml::_('select.option',  'all', JText::_('COM_JIFILE_ALL_WORDS'));
		$searchphrases[]	= JHtml::_('select.option',  'any', JText::_('COM_JIFILE_ANY_WORDS'));
		$searchphrases[]	= JHtml::_('select.option',  'exact', JText::_('COM_JIFILE_EXACT_PHRASE'));
		$lists['searchphrase' ]= JHtml::_('select.radiolist',  $searchphrases, 'searchphrase', '', 'value', 'text', $state->get('match'));

		// log the search
		// @TODO 
		// creazione di un sistema di LOG delle ricerche come per per la search see:
		//SearchHelper::logSearch($searchword);

		//limit searchword
		/*$lang = JFactory::getLanguage();
		$upper_limit = $lang->getUpperLimitSearchWord();
		$lower_limit = $lang->getLowerLimitSearchWord();
		if (SearchHelper::limitSearchWord($searchword)) {
			$error = JText::sprintf('COM_SEARCH_ERROR_SEARCH_MESSAGE', $lower_limit, $upper_limit);
		}
		*/
		//sanatise searchword
		/*if (SearchHelper::santiseSearchWord($searchword, $state->get('match'))) {
			$error = JText::_('COM_SEARCH_ERROR_IGNOREKEYWORD');
		}*/

		/*
		if (!$searchword && count(JRequest::get('post'))) {
			//$error = JText::_('COM_SEARCH_ERROR_ENTERKEYWORD');
		}
		*/
		
		$model = JModelLegacy::getInstance('JifileModelLucene');
		// setta la parola da ricercare nello state
		$state->set('keyword', $searchword);
		if ($error == null) {
			
			// pagination start end limit
			$limitstart = $app->getUserStateFromRequest( $option.'limitstart_lucene', 'limitstart', 0, 'number' );
			$limit 		= $app->getUserStateFromRequest( $option.'limit_lucene', 'limit', 10, 'number' );
			$tot = 0;
			
			$lucene = null;
			//gestione ricerca
			if(!empty($searchword)) {
				
				$filterField = $filter_order = $filter_order_Dir = null;
				$luceneSearch = $model->search($searchword, $filterField, $state->get('match'), $filter_order, $filter_order_Dir);
				$luceneSearch = $this->getDocuments($luceneSearch);
				
				$tot = count($luceneSearch);
				
				if($limit != 0) {
					$lucene = array_slice($luceneSearch, $limitstart, $limit, true);
				} else {
					$lucene = $luceneSearch;
				}
					
				unset($luceneSearch);
			}
			
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($tot, $limitstart, $limit);
			
			$results	= $lucene;
			$total		= $tot;
			$pagination	= $pageNav;

			require_once JPATH_SITE . '/components/com_content/helpers/route.php';			
		}
		

		// Check for layout override
		$active = JFactory::getApplication()->getMenu()->getActive();
		if (isset($active->query['layout'])) {
			$this->setLayout($active->query['layout']);
		}

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->assignRef('pagination',  $pagination);
		$this->assignRef('results',		$results);
		$this->assignRef('lists',		$lists);
		$this->assignRef('params',		$params);

		//$this->ordering = $state->get('ordering');
		$this->searchword = $searchword;
		$this->origkeyword = $state->get('origkeyword');
		$this->searchphrase = $state->get('match');
		$this->state = $state;

		$this->total = $total;
		$this->error = $error;
		$this->action = $uri;
		
		$this->setDocument();

		parent::display($tpl);
	}
	
	/**
	 * Return the text with highlight
	 * 
	 * @param object $row
	 * @param string $serachword
	 * @param object $state
	 * 
	 * @return object
	 */
	function highlightText($row, $searchword, $state) {
		
		// se è settato esatto
		if ($state->get('match') == 'exact') {
			$searchwords = array($searchword);
			$needle = $searchword;
		}
		else {
			$searchworda = preg_replace('#\xE3\x80\x80#s', ' ', $searchword);
			$searchwords = preg_split("/\s+/u", $searchworda);
			$needle = $searchwords[0];
		}
		
		$row = SearchHelper::prepareSearchContent($row, $needle);
		$searchwords = array_unique($searchwords);
		$searchRegex = '#(';
		$x = 0;
		
		foreach ($searchwords as $k => $hlword)
		{
			$searchRegex .= ($x == 0 ? '' : '|');
			$searchRegex .= preg_quote($hlword, '#');
			$x++;
		}
		$searchRegex .= ')#iu';
		
		$row = preg_replace($searchRegex, '<span class="highlight">\0</span>', $row);
		
		/*
		$result = &$results[$i];
		if ($result->created) {
			$created = JHtml::_('date', $result->created, JText::_('DATE_FORMAT_LC3'));
		}
		else {
			$created = '';
		}
		
			$result->text		= JHtml::_('content.prepare', $result->text, '', 'com_jifile.search');
			$result->created	= $created;
			$result->count		= $i + 1;
		}
		*/
		
		return $row;
		
	} 
	
	function getDocuments($lucene) {
		$result = array();
		if(empty($lucene)) {
			return $result;
		}
		
		// get JiFile Plugin Add-on Search
		$plugins = $this->getSearchPluginInstance();
		// initilize array filter
		$arrayFilter = array();
		// call onContentSearch() method from plugin
		if (!empty($plugins)) {
			foreach ($plugins as $plugin) {
				$arrayFilter = $arrayFilter + $plugin->onContentSearch();
			}
		}
	
		foreach ($lucene as $hit) {
			
			$doc = $hit->getDocument();
			// get key form document
			$key = $doc->getFieldValue('key');
			
			if (!empty($arrayFilter)) {
				if (array_key_exists($key, $arrayFilter)) {
					continue;
				}
			}
			
			$result[$hit->id]['score'] = $hit->score;
			$result[$hit->id]['doc'] = $doc;
			//$result[$hit->id]['doc'] = jifilehelper::luceneDocToArray($doc, false);
		}
		return $result;
	}
	
	/**
	 * Get Plugins Instance
	 * Return registry whit instance of Lucene Plugins
	 * @return array
	 */
	public function getSearchPluginInstance() {
		$registry = array();
	
		require_once(JPATH_ADMINISTRATOR.'/components/com_jifile/helpers/interface/jifilepluginfactory.php');
	
		$filter = array();
		$filter['context'] = array('type' => 's', 'value' => 'plugin');
		$filter['published'] = array('type' => 'i', 'value' => 1);
		$filter['type'] = array('type' => 'i', 'value' => 0);
		$filter['plugin'] = array('type' => 's', 'value' => 'search');
		$order = array();
		$order['ordering'] = 'asc';
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_jifile/tables');
		$tableAddon = JTable::getInstance('Addon', 'JifileTable');
		$plugins = $tableAddon->getAddon($filter, $order);
		$jifilefactory = JiFilePluginFactory::getInstance();
	
		// create instance of the Search Plugins
		if (!empty($plugins)) {
			foreach ($plugins as $plugin) {
				//$registry[] =& $jifilefactory->getJifilePlugin($plugin);
				$registry[] =& $jifilefactory->getJifileAddon($plugin);
			}
		}
	
		return $registry;
	}
	
	function setDocument() {
		$doc = JFactory::getDocument();
		$doc->addStyleSheet( 'components/com_jifile/css/style.css?'.JIFILEVER );
		$doc->addStyleSheet( 'administrator/components/com_jifile/css/filetype.css?'.JIFILEVER );
	}
}
