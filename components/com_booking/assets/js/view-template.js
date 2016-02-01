/**
 * Javascript for edit template form
 * 
 * @version $Id$
 * @package ARTIO Booking
 * @subpackage assets
 * @copyright Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link http://www.artio.net Official website
 */

var ViewTemplate = {
	/**
	 * Valid form before submit. Standard in Joomla! administration.
	 * 
	 * @param pressbutton
	 *            button selected in toolbar
	 */
	submitbutton : function(pressbutton) {
		var form = ACommon.getForm();
		switch (pressbutton) {
		case 'cancel':
		case 'copy':
			submitform(pressbutton);
			return;
		}
		if (trim(form.name.value) == '') {
			alert(LGErrAddTemplateName);
		} else {
			submitform(pressbutton);
		}
	}
}

try {
	/**
	 * Joomla! 1.6.x
	 */
	Joomla.submitbutton = function(pressbutton) {
		return ViewTemplate.submitbutton(pressbutton);
	}
} catch (e) {
	/**
	 * Joomla! 1.5.x
	 */
	function submitbutton(pressbutton) {
		return ViewTemplate.submitbutton(pressbutton);
	}
}