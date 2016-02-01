/**
 * Javascript for list subject form
 * 
 * @version $Id$
 * @package ARTIO Booking
 * @subpackage assets
 * @copyright Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link http://www.artio.net Official website
 */

var ListSubjects = {

	/**
	 * Set template from param when create new subject
	 * 
	 * @param element
	 *            templates select box
	 */
	setTemplate : function(element) {
		document.adminForm.template.value = element.value;
	},

	/**
	 * Select subject from element window.
	 * 
	 * @param id
	 * @param name
	 * @return false to disable page submit
	 */
	select : function(id, name, alias, input) {
		try {
			subject_id = 'subject_id';
			subject_name = 'subject_name';
			subject_title = 'subject_title';
			
			if (input){ //multi-dimensional input
				subject_id = input;
				matches = input.match(/^items\[([^\]]+)\]\[[^\]]+\]$/);
				if (matches){
					subject_name = 'items['+matches[1]+']['+subject_name+']';
					subject_title  = 'items['+matches[1]+']['+subject_title+']';
				}
			}

			window.parent.document.getElementById(subject_id).value = id+ ':' + alias;
			window.parent.document.getElementById(subject_name).value = name;
			window.parent.document.getElementById(subject_title).value = name;
			
		} catch (e) {
		}
		window.parent.SqueezeBox.close();
		return false;
	}
}