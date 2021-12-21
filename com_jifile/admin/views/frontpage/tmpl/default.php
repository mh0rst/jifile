<?php 
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined('_JEXEC') or die('Restricted access'); 
$jAdapter = AdapterForJoomlaVersion::getInstance();
?>

<?php echo $this->loadTemplate('j'.$jAdapter->getJoomlaVersion()); ?>

<div id="social" class="row-fluid">
	<div class="icon span1">
		<a href="https://www.facebook.com/isApp.it"  title="isApp.it on Facebook" target="_blank">
			<img alt="isApp.it on Facebook" src="components/com_jifile/images/social/facebook.png" />
		</a>
	</div>
	<div class="icon span1">
		<a href="https://twitter.com/isappit"  title="isApp.it on Twitter" target="_blank">
			<img alt="isApp.it on Twitter" src="components/com_jifile/images/social/twitter.png" />
		</a>
	</div>
	<div class="icon span1">
		<a href="https://plus.google.com/100143192850831824506/posts"  title="isApp.it on Google+" target="_blank">
			<img alt="isApp.it on Google+" src="components/com_jifile/images/social/google-plus.png" />
		</a>
	</div>
	<div class="icon span1">
		<a href="http://extensions.joomla.org/extensions/search-a-indexing/site-search/19038"  title="<?php echo JText::_('JIFILE_VOTE_JED') ?>" target="_blank">
			<img alt="<?php echo JText::_('JIFILE_VOTE_JED') ?>" src="components/com_jifile/images/social/vote_jed.png" />
		</a>
	</div>
</div>

<?php echo jifilehelper::getFirma(); ?>