<?php
/**
 * @subpackage	com_jifile
 * @author		Antonio Di Girolamo & Giampaolo Losito
 * @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link		http://jifile.isapp.it
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

/**
 * Book form field class
 */
class JFormFieldModal_Addon extends JFormField
{
	/**
	 * field type
	 * @var string
	 */
	protected $type = 'Modal_Book';

	/**
	 * Method to get the field input markup
	 */
	protected function getInput()
	{
		// Load modal behavior
		JHtml::_('behavior.modal', 'a.modal');
	
		// Build the script
		$script = array();
		$script[] = '    function jSelectBook_'.$this->id.'(id, title, object) {';
		$script[] = '        document.id("'.$this->id.'_id").value = id;';
		$script[] = '        document.id("'.$this->id.'_name").value = title;';
		$script[] = '        SqueezeBox.close();';
		$script[] = '    }';
	
		// Add to document head
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
	
		// Setup variables for display
		$html = array();
		$link = 'index.php?option=com_jifile&amp;view=addonparams&amp;layout=modal'.
				'&amp;tmpl=component&amp;function=jSelectBook_'.$this->id;
		
		$title = JText::_('COM_JIFILE_FIELD_SELECT_ADDON');
	
		// The current book input field
		$html[] = '<div class="fltlft">';
		$html[] = '  <input type="text" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" size="20" />';
		$html[] = '</div>';
	
		// The book select button
		$html[] = '<div class="button2-left">';
		$html[] = '  <div class="blank">';
		$html[] = '    <a class="modal" title="'.JText::_('COM_JIFILE_SELECT_ADDON').'" href="'.$link.
		'" rel="{handler: \'iframe\', size: {x:800, y:450}}">'.
		JText::_('COM_JIFILE_BUTTON_SELECT_ADDON').'</a>';
		$html[] = '  </div>';
		$html[] = '</div>';
	
		// The active book id field
		if (0 == (int)$this->value) {
			$value = '';
		} else {
			$value = (int)$this->value;
		}
	
		// class='required' for client side validation
		$class = '';
		if ($this->required) {
			$class = ' class="required modal-value"';
		}
	
		$html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';
	
		return implode("\n", $html);
	}
}