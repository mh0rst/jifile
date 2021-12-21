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
<blockquote>
	<p style="font-size: 12px"><?php echo JText::_( 'INDEXING_NOTIFY' ); ?></p>
	<small>isApp.it Team</small>
</blockquote>
<div class="row">
	<div class="pull-left">
		File: <span class="badge badge-info"><span id="numfile">0</span> / <span id="totfile">0</span></span>
	</div>
	<button id="stopAll" class="btn btn-warning pull-right" type="submit">
		<?php echo JText::_('CANCEL_ALL'); ?>
	</button>
</div>
<div class="progress progress-striped active hide" id="indexingProgress">
  <div class="bar" style="width: 0%;"></div>
</div>
<div class="row-fluid" id="ifile">
	<div class="span12" id="result_j3">
		<div id="result"></div>
	</div>
</div>