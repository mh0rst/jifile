<?php
/**
 * JiFile
 * 
 * @category   Joomla1.6!
 * @package    com_jifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo 
 * @copyright  Copyright (C) 2011 isApp.it - All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    1.0
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Interface of JiFile Plugin
 *
 * @category   Joomla1.6!
 * @package    com_jifile
 * @subpackage helpers/interface
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright  Copyright (C) 2011 isApp.it - All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
interface JiFileLucenePluginInterface
{
	/**
	 * Set Toolbar
	 *  
	 * @return  
	 */
	public function setToolbar();
	
	/**
	 * Set Document
	 *  
	 * @return  
	 */
	public function setDocument();
	
	/**
	 * Get Filters
	 * 
	 * @return  
	 */
	public function printFilter();
		
	/**
	 * Print Action
	 * @param int $id Id of the Lucene Document
	 * @param obj $doc document of Lucene Search Document
	 * @return void
	 */
	public function printAction($id, $doc);
	
	/**
	 * Set Tag Hidden 
	 * 
	 * @return void
	 */
	public function setTagHidden();	
	
	/**
	 * Return filter for Lucene Documents 
	 * 
	 * @return array
	 */
	public function luceneFilter();
	
	/**
	 * Return list of filter name 
	 * 
	 * @return array
	 */
	public function getFiltersName();
}
?>