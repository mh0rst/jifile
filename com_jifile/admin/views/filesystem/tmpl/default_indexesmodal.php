<?php 
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined('_JEXEC') or die('Restricted access');  
?>

<?php 
$modalParams = array();
$modalParams['title'] = JText::_( 'Indexing' );
$modalParams['url'] = 'index.php?option=com_jifile&task=lucene.indexing&view=indexing&tmpl=component';
$modalParams['width'] = '640px';
$modalParams['height'] = '580px';
$modalParams['remote'] = 'true';
echo JHtml::_('bootstrap.renderModal', 'indexesModal', $modalParams);