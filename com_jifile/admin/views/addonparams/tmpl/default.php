<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_search
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
 defined('_JEXEC') or die('Restricted access');
 
 // Load tooltip behavior
 JHtml::_('behavior.tooltip');
// $listOrder     = $this->escape($this->state->get('list.ordering'));
// $listDirn      = $this->escape($this->state->get('list.direction'));
 
 $function = JRequest::getCmd('function', 'jSelectBook');
?>
<form action="index.php?option=com_jifile" method="post" name="adminForm" id="adminForm">
<!-- da fare  -->
<div>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</div>
</form>
