<?php
/**
* plg_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link			http://jifile.isapp.it
*/
// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Categories Search plugin
 *
 * @package		Joomla
 * @subpackage	Search
 * @since		1.6
 */
class plgSearchJifile extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * @return array An array of search areas
	 */
	function onContentSearchAreas()
	{
		$areaName = $this->params->def('area_name', 'Documents');
		$areas = array(
		'jifile' => $areaName
		);
		return $areas;
	}

	/**
	 * JiFile Search method
	 *
	 * The sql must return the following fields that are
	 * used in a common display routine: href, title, section, created, text,
	 * browsernav
	 * @param string Target search string
	 * @param string mathcing option, exact|any|all
	 * @param string ordering option, newest|oldest|popular|alpha|category
	 * @param mixed An array if restricted to areas, null if search all
	 */
	function onContentSearch($text, $phrase='any', $ordering='', $areas=null)
	{
		
		if (is_array($areas)) {
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas()))) {
				return array();
			}
		}

		$limit = $this->params->def('search_limit', 50);

		$text = trim($text);
		if ($text == '') {
			return array();
		}
		
		// Adapter for Joomla version
		require_once JPATH_ADMINISTRATOR.'/components/com_jifile/helpers/adapter/adapterforjoomlaversion.php';
		$jAdapter = AdapterForJoomlaVersion::getInstance();
		require_once(JPATH_ADMINISTRATOR.'/components/com_jifile/models/lucene.php');
		$model = JModelLegacy::getInstance('JifileModelLucene');

		if($model->getError()) {
			return array();
		}
		
		/*
		 * $orderting : value 
		 * newest
		 * oldest
		 * popular
		 * alpha
		 * category
		 */
		switch ($ordering)
		{
			case 'alpha':
				$order 	   = 'name';
				$order_dir = 'asc';
				break;
			// order by date:
			// isn't possible because the date is a string
			// with the format mm/dd/yyyy
// 			case 'oldest':
// 				$order 	   = 'created';
// 				$order_dir = 'asc';
// 				break;
// 			case 'newest':
// 				$order 	   = 'created';
// 				$order_dir = 'desc';
// 				break;
			default:
				$order 	   = null;
				$order_dir = null;
				break;
		}
		
		
		// @todo
		// rivedere il processo di ricerca 
		// in questa versione si lascia la ricerca con la search di un solo
		// token alla volta, mentre verranno implementati "Moduli" e 
		// componenti di front-end per la gestione della ricerca avanzata
		// ($search, $field = null, $searchphrase = 'any', $order = null, $order_dir = null, $limit = false) {
		$lucene = $model->search($text, null, $phrase, $order, $order_dir, $limit);
		
		if (empty($lucene)) {
			return array();
		}

		$app = JFactory::getApplication();
		$su = ($app->isAdmin()) ? '../' : '';
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
		
		$rows = array();
		foreach ($lucene as $docs) {
			$doc = $docs->getDocument();
			// get key form document
			$key = $doc->getFieldValue('key');
			
			// filter document
			if (!empty($arrayFilter)) {
				if (array_key_exists($key, $arrayFilter)) {
					continue;
				}	
			}
			
			$pathD = jifilehelper::getCorrectFilename($doc->getFieldValue('path'));
			
			if(!$pathD) {
				continue;
			}
			try {
				$created = $doc->getFieldValue('created');
			} catch (Exception $e) {
				$created = null;
			}
			$pathD=str_replace("\\","/", $pathD);
			$created = strtotime($created) ? $created : null;
			try {
				$text = $doc->getFieldValue('body');
			} catch (Exception $e) {
				$text = $doc->getFieldValue('introtext').'...';
			}
			
			$extension = strtoupper($doc->getFieldValue("extensionfile"));
			
			$key = base64_encode($key);
			// get title
			$title = jifilehelper::encodingCharset($doc->getFieldValue('name'));
			
			/**
			 * @TODO
			 * Andrebbe rivista la chiamata alla componente di frontend
			 * Per la visualizzazione dei documenti in modo dinamico
			 * in funzione del tipo di estensione.
			 */
			$row = array('title' => $title, 
						'href' => 'index.php?option=com_jifile&task=download&filename='.JFilterOutput::stringURLSafe($title).'&key='.$key, 
						'created' => $created, 
						'browsernav' => 0, 
						'section' => 'JiFile / '.$extension,
						'text' => $text);
			$rows[] = JArrayHelper::toObject($row);
		}

		return $rows;
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
				$registry[] = $jifilefactory->getJifileAddon($plugin);
			}	
		} 
		
		return $registry;
	}
}