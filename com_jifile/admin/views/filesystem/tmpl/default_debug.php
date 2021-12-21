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

<script type="text/javascript">
_pw = (window.opener == null) ? window.parent : window.opener
_pw.startCheck(<?php echo JRequest::getVar('id') ?>, true);
</script>