/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
window.addEvent('domready', function() {
	$('xmlanalyzertypedefault').addEvent('change',function(event) {
		if(this.value == ' ') {
			if($('custom-default_path')) {
				$('custom-default_path').removeProperty('disabled');
				$('custom-default_class').removeProperty('disabled');
			} else {
				//this[this.selectedIndex].setAttribute('disabled', '');
				labelPath = new Element('label', {"class": 'custom', "for": 'custom-default_path', "style": 'padding: 0 20px 0 0'});
				labelPath.innerHTML = 'Path';
				inputPath = new Element('input', {type: 'text', 
												id: 'custom-default_path', 
												"class": 'inputbox custom', 
												maxlength: 255, 
												size: 50,
												name: 'xml[analyzer][type][custom-default][@value]'});
				labelClass = new Element('label', {"class": 'custom', "for": 'custom-default_class', style: 'padding: 0 20px'});
				labelClass.innerHTML = 'Class';
				inputClass = new Element('input', {type: 'text', 
												id: 'custom-default_class', 
												"class": 'inputbox custom', 
												maxlength: 255, 
												size: 50,
												name: 'xml[analyzer][type][custom-default][attributes][class]'});
				$('custom').adopt(new Element('br'));
				$('custom').adopt(labelPath);
				$('custom').adopt(inputPath);
				$('custom').adopt(labelClass);
				$('custom').adopt(inputClass);
			}
		} else if($('custom-default_path')) {
			$('custom-default_path').setAttribute('disabled', '');
			$('custom-default_class').setAttribute('disabled', '');
		}
	});
	var totFilter = $(document.adminForm).getElements('.filters').length;
	$('addFilter').addEvent('click',function(event) {
		
		labelPath = new Element('label', {"for": 'path'+totFilter, style: 'padding: 0 20px 0 0'});
		labelPath.innerHTML = '('+(totFilter+1)+') Path';
		inputPath = new Element('input', {type: 'text', 
										id: 'path'+totFilter, 
										"class": 'inputbox filters', 
										maxlength: 255, 
										size: 50,
										name: 'xml[analyzer][filters][custom-filters][filter]['+totFilter+'][@value]'});
		labelClass = new Element('label', {"class": 'custom', "for": 'class'+totFilter, style: 'padding: 0 20px'});
		labelClass.innerHTML = 'Class';
		inputClass = new Element('input', {type: 'text', 
										id: 'class'+totFilter, 
										"class": 'inputbox', 
										maxlength: 255, 
										size: 50,
										name: 'xml[analyzer][filters][custom-filters][filter]['+totFilter+'][attributes][class]'});
		
		totFilter++;
		
		p = new Element('p');
		$(p).adopt(labelPath);
		$(p).adopt(inputPath);
		$(p).adopt(labelClass);
		$(p).adopt(inputClass);
		$('filters').adopt(p);
	});
});