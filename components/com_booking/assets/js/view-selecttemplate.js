/**
 * Javascript for select template dialog
 * 
 * @version $Id$
 * @package ARTIO Booking
 * @subpackage assets
 * @copyright Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link http://www.artio.net Official website
 */

var SelectTemplate = {
	/**
	 * Control if user select any template and if success submit form.
	 * 
	 * @return boolean
	 */
	select : function() {
		for ( var i = 0; i < templatesCount; i++) {
			var radio = document.getElementById('template' + i);
			if (radio && radio.checked) {
				window.parent.document.adminForm.template.value = radio.value;
				this.newTmp();
				return true;
			}
		}
		alert(LGSelectTemplate);
		return;
	},
	/**
	 * Submit form without validation if user choose create new template.
	 */
	newTmp : function() {
		window.parent.document.adminForm.task.value = 'add';
		window.parent.document.adminForm.submit();
		this.cancel();
	},
	/**
	 * Close select dialog
	 */
	cancel : function() {
		window.parent.SqueezeBox.close();
	}
}