/**
 * Generate property element of type checkbox. Functions: - create adding or editing window, create new element, set existing element
 * 
 * @version $Id$
 * @package ARTIO Booking
 * @subpackage assets
 * @copyright Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link http://www.artio.net Official website
 */

var ATemplateCheckbox = {
	/**
	 * Render creating or editing window.
	 * 
	 * @param element (existing)
	 * @param id (not use)
	 */
	render : function(element, id) {
		/* only resize window */
		ATemplate.resize(300, 250);
		return '';
	},

	/**
	 * Set exiting - only return output value.
	 * 
	 * @param element
	 * @param id
	 */
	set : function(element, id) {
		return this.getOutput();
	},

	/**
	 * Create new element and get server output.
	 * 
	 * @param id
	 */
	build : function(id) {
		var name = ATemplate.getElementName(id);
		//check if element exist - collision
		/*while(document.getElementsByName(name).length)
			name = ATemplate.getElementName(id = (ATemplate.getId(true, true)-1));*/
		var className = 'text_area';
		var pid = ATemplate.getParamsId(id);

		var elements = new Array(2);

		var hidden = document.createElement('input');
		hidden.type = 'hidden';
		hidden.name = name;
		hidden.value = '0';

		elements[0] = hidden;

		try { /* internet explorer */
			var element = document.createElement('<input type="checkbox" name="' + name + '" id="' + pid + '" class="' + className + ' inputCheckbox" value="1"/>');
		} catch (err) { /* other browsers */
			/* create new input element */
			var element = document.createElement('input');

			/* set element params */
			element.type = 'checkbox';
			element.name = name;
			element.id = pid;
			element.className = className;
			element.value = '1';
		}
		elements[1] = element;

		/* generate output for main building */
		var build = new Array(2);
		build[0] = elements;
		build[1] = this.getOutput();
		return build;
	},

	/**
	 * Get property output to save on server.
	 */
	getOutput : function() {
		return 'checkbox';
	}
}