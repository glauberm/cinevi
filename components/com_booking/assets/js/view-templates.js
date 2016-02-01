/**
 * Javascript for list template form
 * 
 * @version $Id$
 * @package ARTIO Booking
 * @subpackage assets
 * @copyright Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link http://www.artio.net Official website
 */

var ListTemplates = {

	/**
	 * Set template from param when create new template
	 * 
	 * @param element
	 *            templates select box
	 */
	setTemplate : function(element) {
		document.adminForm.template.value = element.value;
	},

	/**
	 * Select template from element window.
	 * 
	 * @param id
	 * @param name
	 * @return false to disable page submit
	 */
	select : function(id, name, alias, input) {
		try {
			template_id = 'template_id';
			template_name = 'template_name';
			template_title = 'template_title';
			
			if (input){ //multi-dimensional input
				template_id = input;
				matches = input.match(/^items\[([^\]]+)\]\[[^\]]+\]$/);
				if (matches){
					template_name = 'items['+matches[1]+']['+template_name+']';
					template_title  = 'items['+matches[1]+']['+template_title+']';
				}
			}

			window.parent.document.getElementById(template_id).value = id+ ':' + alias;
			window.parent.document.getElementById(template_name).value = name;
			window.parent.document.getElementById(template_title).value = name;
			
		} catch (e) {
		}
		window.parent.SqueezeBox.close();
		return false;
	}
}

window.addEvent('domready', function(){
	if ($$('#list .templatesList .template .bookit .button')) {
		$$('#list .templatesList .template .bookit .button').addEvent('click', function() {
			document.id('list').setAttribute('action', this.href);
			document.id('list').submit();
			return false;
		});
	}
	if ($$('#list .templatesList .template h2 a')) {
		$$('#list .templatesList .template h2 a').addEvent('click', function() {
			document.id('list').setAttribute('action', this.href);
			document.id('list').submit();
			return false;
		});
	}
});



