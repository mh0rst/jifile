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


<script type="text/javascript">
function formatTimer(s) {
	time = '';
	m = h = d = 0;
	if(s >= 86400) {
		d = Math.floor(s/86400);
		s = (s%86400);
	}
	if(s >= 3600) {
		h = Math.floor(s/3600);
		s = (s%3600);
	}
	if(s >= 60) {
		m = Math.floor(s/60);
		s = (s%60);
	}
	s = Math.floor(s);

	if(d > 0) {
		time = d+'d ';
	}
	if(s < 10) {
		s = '0'+s;
	}
	if(m < 10) {
		m = '0'+m;
	}
	if(h < 10) {
		h = '0'+h;
	}
	time += h+':'+m+':'+s;
	return time;
}

function redirect() {
	//var _pw	= (window.opener == null) ? window.parent : window.opener;
	window.document.location = 'index.php?option=com_jifile&state=sync&time='+$('timerz').innerHTML;
}
//on dom ready...
jQuery(document).ready(function() {
	startTime = function(cc)
	{
		if(!cc) {
			cc = 0;
		}
		cc++;
		document.getElementById('timerz').innerHTML=formatTimer(cc);
		timer = setTimeout('startTime('+cc+')',1000);
	}
	function stoper() {
		clearTimeout(timer);
	}
	
	url = 'index.php?option=com_jifile&task=synchronize.synchronize';

	var ajax = new Request({
		url: url,
		onRequest: function() {
			jQuery('#result_synchronize').addClass('ajax-loading').html('<?php echo JText::_('JIFILE_SYNCHRONIZE_IN_PROGRESS') ?>...');
	        startTime(0);
		},
		onComplete: function() {
			stoper();
			jQuery('#result').addClass('success');
			img = '<img class="result_img" src="../administrator/components/com_jifile/images/success.gif" title="<?php echo JText::_('Success') ?>"/>';
			jQuery('#result_synchronize').removeClass('ajax-loading').html('<?php echo JText::_('JIFILE_SYNCHRONIZE_COMPLETED') ?>');
			jQuery('#result_synchronize').after(img);
			setTimeout('redirect()',2500);
		}
	});

	ajax.send();
});
</script>