<?php
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
jimport('joomla.filesystem.file');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'jifilehelper.php');

class JifileModelFilesystem extends JModelLegacy {
	
	/**
	 * Path base definito nelle "Preference"
	 * 
	 * @var $basepath
	 */
	private $basepath = null;
	
	/**
	 * Array delle estensioni
	 * 
	 * @var $ext
	 */
	private $ext = array();
	
	/**
	 * Array file da ignorare
	 * @var $ignoreFile
	 */
	private $ignoreFile = false;
	
	function __construct($config) {
		$params = JComponentHelper::getParams( 'com_jifile' );
		$this->basepath = jifilehelper::getCorrectPath($params->get( 'base_path' ));
		if ($this->basepath == '\\' || $this->basepath == '/') {
			$this->basepath = '';
		}
		
		$ignoreFile = $params->get( 'ignoreFile', array() );
		// check the glob pattern to BRACE
		// @see http://php.net/manual/en/function.glob.php
		if (preg_match('/^\{.*\}$/i', $ignoreFile)) {
			$this->ignoreFile = $ignoreFile;
		}
		
		if($this->checkZend()) {
			jifilehelper::refreshCache();
		}
		parent::__construct($config);
	}
	
	function checkZend() {
		
		require_once JPATH_IFILE_LIBRARY.'/ifile/servercheck/LuceneServerCheck.php';
		
		$serverCheck = LuceneServerCheck::getInstance();
		$serverCheck->serverCheck();
		$reportServerCheck = $serverCheck->getReportCheck();
		$reportCheck = $reportServerCheck['Zend Framework']['Lucene'];
		
		if (!$reportCheck->getCheck()) {
			$this->setError(JText::_('Zend_Framework_is_not_installed'));
			return false;
		}
		return true;
	}
	
	function download() {
		$name = JRequest::getVar('filename');
		$filename =	realpath(jifilehelper::getCorrectFilename($name, true));

		if(!is_file($filename)) {
			JFactory::getApplication()->enqueueMessage(JText::_('FILE_NOT_FOUND'), 'error');
		} else {
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
	        header("Content-Type: "    . jifilehelper::getMimetype(JFile::getExt($name)) );			// MIME type
	        header("Content-Length: "  . $size);
	        
	        echo file_get_contents($filename);
	        jexit();
		}
	}
	
	function deleteFile($file) {
		if(!JFile::delete($file)) {
			//ImportHelper::log('Errore file move(getAttachments): '.$path);
			return false;
		}
		return true;
	}
	
	/**
	 * Ritorna la lista dei file 
	 * 
	 * @param path $dir [optional]
	 * @param string $search [optional]
	 * @return array
	 */
	function getListfile($dir = null, $filter = array(), $noback = false, $ricorsiva = false) { 

		if(empty($this->basepath)) {
			//return array();
		}
		$path = (is_null($dir) || strpos($dir, '..') !== false) ? JPATH_SITE.DS.$this->basepath : JPATH_SITE.DS.$this->basepath.$dir;
		$listfile = $listdir = array();
		
		if (is_dir($path)) {
		    if ($dh = opendir($path)) {
		    	
		    	// use GLOB BRACE
		    	$globFiles = array();
		    	if ($this->ignoreFile !== false) {
		    		$globFiles = (array_map(function ($a) {return basename($a);}, glob($path.$this->ignoreFile, GLOB_BRACE)));
		    	}
		    	
		        while (($file = readdir($dh)) !== false) {
		        	// if($file == '.' || ($file == '..' && (empty($dir) || $noback)) || in_array($file, $this->ignoreFile)) {
		        	// if($file == '.' || ($file == '..' && (empty($dir) || $noback)) || in_array(".".strtolower(JFile::getExt(basename($file))), $this->ignoreFile)) {
		        	if($file == '.' || ($file == '..' && (empty($dir) || $noback)) || in_array($file, $globFiles)) {
		        		continue;
		        	}
		        	$filename = $path . $file;
					
		        	if(is_dir($filename)) {
		        		if ($ricorsiva) { 
		        			$listFileDir = $this->getListfile(jifilehelper::retrievePath($filename, true).DS, $filter, $noback, $ricorsiva);
		        			$listfile = array_merge($listfile, $listFileDir);
		        		} else { 
		        			$listdir[] = $filename.DS;
		        		}
		        	} elseif(empty($filter) || jifilehelper::getRegexSearch(basename($filename), $filter['search'], $filter['ext'])) {
		        		$listfile[] = $filename;
		        	}
		        	if(!is_dir($filename)) {
		        		$ext = strtolower(JFile::getExt(basename($filename)));
		        		if(!empty($ext)) {
		        			$this->ext[$ext] = $ext;
		        		}
		        	}
		        }
		        closedir($dh);
		    }
		}
		natcasesort ($listfile);
		return array_merge($listdir,$listfile);
	}
	
	/**
	 * Ritorna l'array delle estensioni dei file presenti nella directory
	 * 
	 * @return array
	 */ 
	function getExtension() {
		return $this->ext;
	}
	
	function getInfofile($file) {
		jimport('joomla.utilities.date');
		$isdir 					= is_dir($file);
		$infofile['filename'] 	= $file;
		$infofile['name'] 		= basename($file);
		$infofile['name'] 		.= ($isdir) ? DS : '';
		$infofile['nameview']	= jifilehelper::encodingCharset($infofile['name']);
		$infofile['size'] 		= ($isdir) ? null : filesize($file);
		$data = new JDate(filemtime($file));
		$infofile['date'] 		= $data->format('d M, Y H:m:s', 1); 
		$infofile['ext'] 		= ($isdir) ? '' : JFile::getExt($file);
		$infofile['mime'] 		= ($isdir) ? 'dir' : jifilehelper::getMimetype($infofile['ext']);
		
		return $infofile; 
	}
}