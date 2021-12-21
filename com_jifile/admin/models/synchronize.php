<?php
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link			http://jifile.isapp.it
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
require_once(JPATH_SITE.'/administrator/components/com_jifile/models/lucene.php');


class JifileModelSynchronize extends JifileModelLucene {
	
	function __construct() {		
		parent::__construct();
	}
	
	/**
	 * Synchronize table with index 
	 * @return bool
	 */
	public function synchronize() {
		// get instance of Documents Table
		$table = JTable::getInstance("Documents", "JifileTable");
		// get Lucene instance
		$lucene = $this->getIndex();
		set_time_limit(0);
		/**
		 * 1. get all document from Index
		 */
		$docs =& $lucene->getAllDocument(true);
		/**
		 * 2. insert this document in #_jifiledocuments if not exists
		 */
		if (!empty($docs)) {
			foreach ($docs as $i => $doc) {
				$keyid = $doc->getFieldValue('key');
				if (!empty($keyid)) {
					$delete = $lucene->isDeleted($i) ? 1 : 0;
					$table->insertDocuments($keyid, $delete);
				}
			}	
		}
		
		return true;		
	}	
}