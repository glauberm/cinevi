/**
 * Generate property element of type textarea. Functions: - create adding or editing window, create new element, set existing element
 * 
 * @version $Id$
 * @package ARTIO Booking
 * @subpackage assets
 * @copyright Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link http://www.artio.net Official website
 */

var ATemplateTextarea = {
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
		var build = new Array(2);
		build[0] = Array.from(Elements.from('<textarea name="' + ATemplate.getElementName(id) + '" id="' + ATemplate.getParamsId(id) + '"class="text_area"></textarea>'));
		build[1] = this.getOutput();
		return build;
	},

	/**
	 * Get property output to save on server.
	 */
	getOutput : function() {
		return 'textarea';
	}
}