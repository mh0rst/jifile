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
 * @version    1.0 Adapter_Search_Lucene_Document_JPG.php 2011-09-16 19:10:14
 */

/** Adapter_Search_Lucene_Document_JPEG */
require_once 'Adapter_Search_Lucene_Document_JPG.php';

/**
 * Adapter per il recupero del contenuto degli EXIF TAG dei file JPEG
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class Adapter_Search_Lucene_Document_JPEG extends Adapter_Search_Lucene_Document_JPG 
{
	public function __construct() {
		parent::__construct();
	}
}
?> 