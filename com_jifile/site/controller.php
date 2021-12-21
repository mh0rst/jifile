<?php
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/

defined('_JEXEC') or die;
/** see JFile */
//jimport( 'joomla.filesystem.file' );

class JifileController extends JControllerLegacy
{
	/*
	function execute( $task ){
	
		switch ($task) {
			case 'download':
				$this->download();
				break;
			default:
				$this->display();
				break;
		}
	}
	*/
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('view', 'search'); // force it to be the search view

		//$safeurlparams = array('catid'=>'INT', 'id'=>'INT', 'cid'=>'ARRAY', 'year'=>'INT', 'month'=>'INT', 'limit'=>'UINT', 'limitstart'=>'UINT',
		//	'showall'=>'INT', 'return'=>'BASE64', 'filter'=>'STRING', 'filter_order'=>'CMD', 'filter_order_Dir'=>'CMD', 'filter-search'=>'STRING', 'print'=>'BOOLEAN', 'lang'=>'CMD');

		$safeurlparams = array('filename'=>'STRING');
		
		parent::display($cachable, $safeurlparams);

		return $this;
	}
	
	function search()
	{
		// slashes cause errors, <> get stripped anyway later on. # causes problems.
		$badchars = array('#', '>', '<', '\\');
		$searchword = trim(str_replace($badchars, '', JRequest::getString('searchword', null, 'post')));
		//die(var_dump($searchword));
		// if searchword enclosed in double quotes, strip quotes and do exact match
		if (substr($searchword, 0, 1) == '"' && substr($searchword, -1) == '"') {
			$post['searchword'] = substr($searchword, 1, -1);
			JRequest::setVar('searchphrase', 'exact');
		}
		else {
			$post['searchword'] = $searchword;
		}
		$post['ordering']	= JRequest::getWord('ordering', null, 'post');
		$post['searchphrase']	= JRequest::getWord('searchphrase', 'all', 'post');
		$post['limit']  = JRequest::getUInt('limit', null, 'post');
		if ($post['limit'] === null) unset($post['limit']);
	
		// set Itemid id for links from menu
		$app	= JFactory::getApplication();
		$menu	= $app->getMenu();
		$items	= $menu->getItems('link', 'index.php?option=com_jifile&view=search');
	
		if(isset($items[0])) {
			$post['Itemid'] = $items[0]->id;
		} elseif (JRequest::getInt('Itemid') > 0) { //use Itemid from requesting page only if there is no existing menu
			$post['Itemid'] = JRequest::getInt('Itemid');
		}
	
		unset($post['task']);
		unset($post['submit']);
	
		$uri = JURI::getInstance();
		$uri->setQuery($post);
		$uri->setVar('option', 'com_jifile');
	
	
		$this->setRedirect(JRoute::_('index.php'.$uri->toString(array('query', 'fragment')), false));
	}
	
	public function download() {
		$name = JRequest::getVar('key');
		$jAdapter = AdapterForJoomlaVersion::getInstance();
		// get Key
		$key  = base64_decode($name);
		
		require_once(JPATH_ADMINISTRATOR.'/components/com_jifile/models/lucene.php');
		// instance Lucene Model		
		$model = $jAdapter->getJModel('JifileModelLucene');
		
		if($model->getError()) {
			$this->jifileRedirect();
		}
		
		// gel lucene document
		$lucene = $model->getDocumentFromKey($key);
		
		if (empty($lucene[0])) {
			$this->jifileRedirect();
		}
		// get Document
		$doc = $lucene[0]->getDocument();
		
		if (empty($doc)) {
			$this->jifileRedirect();
		}
		
		// get path file
		$filename =	realpath(jifilehelper::getCorrectFilename($doc->getFieldValue('path'), true));

		if (!$filename) {
			$this->jifileRedirect();
		}
		
		$name = basename($filename);
		$size = filesize($filename);
		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Expires: 0");
		
		header("Content-Transfer-Encoding: binary");
		header('Content-Disposition: attachment;'
				. ' filename="' . $name . '";'
				. ' modification-date="' . filemtime($filename) . '";'
				. ' size=' . $size .';'
		); //RFC2183
		// On some Joomla! Platform the JFile:getExt is not present
		//header("Content-Type: "    . jifilehelper::getMimetype(JFile::getExt($name)) );			// MIME type
		header("Content-Type: "    . jifilehelper::getMimetype($doc->getFieldValue('extensionfile')) );			// MIME type
		header("Content-Length: "  . $size);
		
		echo file_get_contents($filename);
		jexit();
	}
	
	public function jifileRedirect() {
		$app = JFactory::getApplication();
		// if empty then redirect in home page 
		$referer = (empty($_SERVER['HTTP_REFERER'])) ? 'index.php' : $_SERVER['HTTP_REFERER'] ;
		$msg  = JText::_('JIFILE_DOWNLOAD_FAILED');
		$app->redirect($referer, $msg, $msgType='error');
		jexit();
	}
}
