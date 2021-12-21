<?php
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link			http://jifile.isapp.it
*/

// No direct access
defined('_JEXEC') or die;

/**
 * @package		administrator
 * @subpackage	com_jifile
 */
class JifileTableAddon extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__jifileaddon', 'id', $db);
	}


	/**
	 * Overloaded bind function
	 *
	 * @param	array		Named array
	 * @return	null|string	null is operation was satisfactory, otherwise returns an error
	 * @since	1.6
	 */
	public function bind($array, $ignore = '')
	{
		return parent::bind($array, $ignore);
	}
	
	/**
	 * Return list of Addon for Admin Frontend
	 * @param array $filters [optional] 
	 * @param array $ordering [optional] 
	 * @param int $offset [optional] 
	 * @param int $limit [optional] 
	 * @return array
	 */	
	public function getAddon($filters = array(), $ordering = array(), $offset = 0, $limit = 0) {
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);	
		$query->select('*');
		$query->from('#__jifileaddon AS a');
		
		if (!empty($filters) && is_array($filters)) {
			foreach ($filters as $field => $value) {
				$operation = (empty($value['operation'])) ? "=" : $value['operation']; 
				
				// set field integer
				if ($value['type'] == "i") {
					$query->where("a.{$field} {$operation} ".(int) $value['value']);	
				} 
				// set field string
				if ($value['type'] == "s") {
					$valueString = $db->quote((string) $value['value'], true);
					$query->where("a.{$field} {$operation} ". $valueString ."");	
				} 
				// set field float
				if ($value['type'] == "f") {
					$query->where("a.{$field} {$operation} ".(float) $value['value']);	
				}		
			}
		}
		
		if (!empty($ordering) && is_array($ordering)) {
			foreach ($ordering as $field => $type) {
				$query->order($db->escape($field.' '.$type));	
			}
		}
		
		// Get the content 
		$db->setQuery($query, $offset, $limit);
		$rows = $db->loadAssocList();
		
		return $rows; 
	}
	
	public function find($wheres = array(), $selects = '*', $keyReturn = '')
	{
		// Get the JDatabaseQuery object
		$query = $this->_db->getQuery(true);
		
		foreach ($wheres as $col => $val)
		{
			$query->where($col . ' = ' . $this->_db->quote($val));
		}

		$query->select($selects);
		$query->from($this->_db->quoteName('#__jifileaddon'));
		//die(var_dump($query->__toString()));
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList($keyReturn);
	}

	/**
	 * Stores a Addon
	 *
	 * @param	boolean	True to update fields even if they are null.
	 * @return	boolean	True on success, false on failure.
	 * @since	1.6
	 */
	public function store($updateNulls = false)
	{	
		// Attempt to store the data.
		return parent::store($updateNulls);
	}

	/**
	 * Overloaded check function
	 *
	 * @return boolean
	 * @see JTable::check
	 * @since 1.5
	 */
	function check()
	{

//		if (JFilterInput::checkAttribute(array ('href', $this->webpage))) {
//			$this->setError(JText::_('COM_CONTACT_WARNING_PROVIDE_VALID_URL'));
//			return false;
//		}

		return true;
	}
}
