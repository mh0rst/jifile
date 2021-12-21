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
class JifileTableDocuments extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__jifiledocuments', 'keyid', $db);
	}


	/**
	 * Overloaded bind function
	 *
	 * @param	array Named array
	 * @return	null|string	null is operation was satisfactory, otherwise returns an error
	 * @since	1.6
	 */
	public function bind($array, $ignore = '')
	{
		return parent::bind($array, $ignore);
	}
	
	/**
	 * Return all document
	 * @param array $wheres [optional]
	 * @param string $selects [optional]
	 * @param string $keyReturn [optional]
	 * @return 
	 */
	public function find($wheres = array(), $selects = '*', $keyReturn = 'keyid')
	{
		if (!($wheres instanceof JDatabaseQueryMySQLi)) {
			// Get the JDatabaseQuery object
			$query = $this->_db->getQuery(true);
			
			foreach ($wheres as $col => $val)
			{
				$cond = '=';
				if (is_array($val)) {
					$cond = $val['cond'];
					$val = $val['val'];
				}
				$query->where($col . $cond . $this->_db->quote($val));
			}
		} else {
			$query = $wheres;
		}
	
		$query->select($selects);
		$query->from($this->_db->quoteName($this->getTableName()));
		//die(var_dump($query->__toString()));
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList($keyReturn);
	}

	/**
	 * Stores a Documents
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
		return true;
	}
	
	/**
	 * Insert keyid in table if not exists
	 * @param string $keyid 
	 * @return bool
	 */	
	public function insertDocuments($keyid, $delete = 0) {
		// Create a new query object.
		$db		= $this->getDbo();
		
		$table = $db->quoteName($this->getTableName());		
		$keyid = $db->quote($keyid);
		
		$query = "INSERT INTO {$table} (keyid, {$db->quoteName('delete')})
					SELECT {$keyid}, {$delete} FROM dual
					WHERE NOT EXISTS (
					    SELECT keyid FROM {$table} WHERE keyid = {$keyid}
					) LIMIT 1";
		$db->setQuery($query);			
		
		$db->query();
		if ($db->getErrorNum())
		{
			echo JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $db->getErrorNum(), $db->getErrorMsg()).'<br />';
			return false;
		}	
		
		return true;
	}
	
	/**
	 * Update delete fields in table
	 * @param string $keyid 
	 * @return bool
	 */	
	public function updateDocument($setFields, $filters = false ) {
		// Create a new query object.
		$db		= $this->getDbo();
		$query  = $this->_db->getQuery(true);
		
		$table = $db->quoteName($this->getTableName());
		$query->update($table);
		
		if (!empty($setFields) && is_array($setFields)) {
			foreach ($setFields as $field => $value) {
				$setField = "";
				$valueSetField = $this->setTypeField($value['value'], $value['type']);
				$setField = $db->quoteName($field) ." = ".$valueSetField;
				$query->set($setField);						
			}
		} else {
			$e =  new JException(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', get_class($this), "Update Params Not Found"));
			$this->setError($e);
			return false;
		}
		
		if (!empty($filters) && is_array($filters)) {
			foreach ($filters as $field => $value) {
				
				$operation = (empty($value['operation'])) ? "=" : $value['operation'];				
				
				$fieldValue = $this->setTypeField($value['value'], $value['type']);
				
				if (!is_array($value['value'])) {
					$query->where("{$field} {$operation} {$fieldValue}");	
				} else {
					$where = (empty($value['where'])) ? " AND " : $value['where'];
					switch ($operation) {
						case 'in':
							$fieldValue = "(".implode(",", $fieldValue).")";
							$query->where("{$field} {$operation} {$fieldValue}");	
						break;
						default:							
							$fieldValue = $field . " {$operation} "  . implode( " {$where} " . $field . " {$operation} ", $fieldValue);
							$query->where($fieldValue);
						break;
					}
				}
			}
		}				
		// set query
		$db->setQuery($query);			
		// execute query
		$db->query();
		// get error
		if ($db->getErrorNum())
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;						
		}	
		
		return true;
	}
	
	function setTypeField($values, $type) {
		// Create a new query object.
		$db		= $this->getDbo();
		
		if (!is_array($values)) {
			$fieldValue = "";
			// set field integer
			if ($type == "i") {
				$fieldValue = (int) $values;	
			} 
			// set field string
			if ($type == "s") {
				$fieldValue = $db->quote((string) $values, true);
			} 
			// set field float
			if ($type == "f") {
				$fieldValue = (float) $values;	
			}	
		} else {
			$fieldValue = array();
			foreach ($values as $value) {
				$fieldValue[] = $this->setTypeField($value, $type);
			} 	
		}
		
		return $fieldValue;	
	}
	
	/**
	 * Update delete fields in table
	 * @param string $keyid 
	 * @return bool
	 */	
	public function updateDeleteDocuments($keyid) {
		// Create a new query object.
		$db		= $this->getDbo();
		
		$table = $db->quoteName($this->getTableName());	
		$fieldDelete = $db->quoteName('delete');	
		$keyid = $db->quote($keyid);
		
		$query = "UPDATE {$table} SET {$fieldDelete} = 1 WHERE keyid = {$keyid} LIMIT 1";
		$db->setQuery($query);			
		
		$db->query();
		if ($db->getErrorNum())
		{
			echo JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $db->getErrorNum(), $db->getErrorMsg()).'<br />';
			return false;
		}	
		
		return true;
	}
	
	/**
	 * Update Delete AllDocument in table
	 * @param string  
	 * @return bool
	 */	
	public function updateDeleteAllDocuments() {
		// Create a new query object.
		$db		= $this->getDbo();
		
		$table = $db->quoteName($this->getTableName());	
		$query = "UPDATE {$table} SET {$fieldDelete} = 1 ";
		$db->setQuery($query);			
		
		$db->query();
		if ($db->getErrorNum())
		{
			echo JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $db->getErrorNum(), $db->getErrorMsg()).'<br />';
			return false;
		}	
		
		return true;
	}
	
	/**
	 * Delete AllDocument in table
	 * @param string  
	 * @return bool
	 */	
	public function deleteAllDocuments() {
		// Create a new query object.
		$db		= $this->getDbo();
		
		$table = $db->quoteName($this->getTableName());	
		$fieldDelete = $db->quoteName('delete');	
		
		$query = "DELETE FROM {$table} WHERE {$fieldDelete} = 1";
		$db->setQuery($query);			
		
		$db->query();
		if ($db->getErrorNum())
		{
			echo JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $db->getErrorNum(), $db->getErrorMsg()).'<br />';
			return false;
		}	
		
		return true;
	}
	
	/**
	 * Truncate table
	 * @param string  
	 * @return bool
	 */	
	public function truncateDocuments() {
		// Create a new query object.
		$db		= $this->getDbo();
		
		$table = $db->quoteName($this->getTableName());	
		$query = "TRUNCATE TABLE {$table}";
		$db->setQuery($query);			
		
		$db->query();
		if ($db->getErrorNum())
		{
			echo JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $db->getErrorNum(), $db->getErrorMsg()).'<br />';
			return false;
		}	
		
		return true;
	}
	
}
