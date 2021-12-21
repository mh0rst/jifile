<?php
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    1.0 Adapter_Search_Lucene_Document_ODS.php 2011-01-24 17:50:56
 */

/** Adatpter_Search_Lucene_Document_ODT */
require_once 'Adapter_Search_Lucene_Document_OpenOffice.php';

/**
 * Adapter per il recupero del contenuto dei file ODS
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class Adapter_Search_Lucene_Document_ODS extends Adapter_Search_Lucene_Document_OpenOffice 
{
	public function __construct() {
		parent::__construct();
	}	
}