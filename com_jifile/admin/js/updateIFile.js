/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
function updateIFile() {
	this.url = 'index.php?option=com_jifile&task=updateifile';
	this.string = new Array();
	
	this.loadString = function(string) {
		this.string = string;
	};
    this.getString = function(key) {
    	if(this.string[key]) 
    		return this.string[key];
    	return false;
    };
	this.onComplete = function(data) {

		jQuery('#updateIFile .progress').remove();
		
		if(data.result == true) {
			jQuery('#system-message-container').remove();
			jQuery('#updateIFile').addClass('alert alert-success success').html(data.message);
			jQuery('#gotoJIfile').show();
			
			var timer = 5;
			
			var IntervalId = setInterval(function(){
				jQuery('#gotoJIfile .ifile_update_timer').addClass('badge').html(timer);
				timer--;
				if(timer == 0) {
					clearInterval(IntervalId);
					window.location.href = "index.php?option=com_jifile";
					return true;
				}
			},1000);
			
		} else {
			jQuery('#updateIFile').addClass('error').html(data.message);
		}
	};
	this.onFails = function(XMLHttpRequest, textStatus, errorThrown) {
		console.log('XMLHttpRequest: '+XMLHttpRequest);
		console.log('textStatus: '+textStatus);
		console.log('errorThrown: '+errorThrown);
		jQuery('#updateIFile').addClass('error').html(objVer.getString('error_ifile'));
	};
}

jQuery(document).ready(function() {
	startUpdateIFile = function() {
		if(objVer.getString('error_ifile') == false) {
			setTimeout('startUpdateIFile', 200);
			return false;
		}
		
		jQuery.ajax({
			url: objVer.url,
			success: function(response) { objVer.onComplete(response) },
			error: function(XMLHttpRequest, textStatus, errorThrown) { objVer.onFails(XMLHttpRequest, textStatus, errorThrown) },
			dataType: 'json'
		});
	};
	
	startUpdateIFile();
});