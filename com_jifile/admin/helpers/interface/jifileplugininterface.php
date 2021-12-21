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
interface JiFilePluginInterface
{
	/**
	 * Content Search
	 * 
	 * @return 
	 */
	public function onContentSearch();
	 
	 /**  
	  * @TODO
	  * Inserire anche i metodi che potrebbero servire 
	  * al processo di indicizzazione dei documenti
	  * primaIndicizzazione()
	  * dopoIndicizzazione()
	  */
}
?>