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
<div id="txt"></div>

<div id="toolbar-box">
	<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
	<div class="m">
			<div class="pagetitle icon-48-cpanel"><h2><?php echo JText::_( 'Indexing' ); ?></h2>
			<?php echo JText::_( 'INDEXING_NOTIFY' ); ?></div>
		<div class="clr"></div>
	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>

<div class="clr"></div>
<div id="element-box">
	<div class="t">
 		<div class="t">
			<div class="t"></div>
 		</div>
	</div>
	<div class="m" id="ifile">
		<div style="float: left; font-weight: bold;">
		File: <span id="numfile">0</span> / <span id="totfile">0</span>
		</div>
		<div style="float: right; margin-top: -20px;">
			<button id="stopAll" type="button">
				<?php echo JText::_( 'CANCEL_ALL' ); ?></button>
			<button onclick="jQuery.colorbox.close();" type="button">
				<?php echo JText::_( 'Close' ); ?></button>
		</div>
		<table class="adminform">
			<tbody>
			<tr><td>
				<div id="result"></div>
			</td></tr>
			</tbody>
		</table>
	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>