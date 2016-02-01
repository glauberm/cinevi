/**
 * Javascript for edit customer form.
 * 
 * @version $Id$
 * @package ARTIO Booking
 * @subpackage assets
 * @copyright Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link http://www.artio.net Official website
 */

/**
 * Valid form before submit. Standard in Joomla! administration.
 * 
 * @param pressbutton
 *            button selected in toolbar
 */
var rfields = new Array();

var ViewCustomerSubmit = {
	submitbutton : function(pressbutton) {
		if (document.id('member-profile'))
			var form = document.id('member-profile'); // front-end
		else
			var form = document.id('adminForm'); // back-end
		switch (pressbutton) {
		case 'save':
		case 'apply':
			break;
		default:
			submitform(pressbutton);
			return false;
		}
		for ( var i = 0; i < rfields.length; i++) {
			var field = rfields[i];
			var el = document.getElementById(field.name);
			if (el) {
				var value = trim(el.value);
				if (value == '' || value == '0') {
					alert(field.msg);
					return false;
				}
				if (el.name == 'email' && !isEmail(value)) {
					alert(LGErrAddReservationValidEmail);
					return false;
				}
			}
		}
		
		if (document.id('select_new_user'))
			var selectNewUser = document.id('select_new_user').checked;
		else
			var selectNewUser = null;
		
		if (document.id('select_existing_user'))
			var selectExistingUser = document.id('select_existing_user').checked;
		else
			var selectExistingUser = null;
		
		if (selectNewUser && form.username != undefined && trim(form.username.value) == '') {
			alert(LGErrAddCustomerUsername);
			return false;
		}
		if ((selectNewUser == true || selectNewUser == null) && form.email != undefined) {
			if (trim(form.email.value) == '') {
				alert(LGErrAddCustomerEmail);
				return false;
			}
			if (!isEmail(form.email.value)) {
				alert(LGErrAddValidCustomerEmail);
				return false;
			}
		}
		if (selectNewUser && form.password != undefined && form.password2 != undefined) {
			if (trim(form.password.value) == ''
				|| trim(form.password2.value) == '') {
				alert(LGErrAddPassword);
				return false;
			}
			if (form.password.value != form.password2.value) {
				alert(LGErrPasswordDoNotMatch);
				return false;
			}
		}
                var user = document.getElement('input[name=user]');
                var userId = user ? user.value.trim() : '';
		if (selectExistingUser && userId === '') {
			alert(LGErrSelectExistingUser);
			return false;
		}
		if (selectExistingUser == false && selectNewUser == false) {
			alert(LGErrSelectExistingOrAddNewUser);
			return false;
		}
		if (document.id('adminForm')) {
			if (form.firstname.value.trim() == '') {
				alert(LGErrAddCustomerFirstName );
				return false;
			}
			if (form.surname.value.trim() == '') {
				alert(LGErrAddCustomerSurname  );
				return false;
			}
		}
		submitform(pressbutton);
	}
}

try {
	/**
	 * Joomla! 1.6.x
	 */
	Joomla.submitbutton = function(pressbutton) {
		return ViewCustomerSubmit.submitbutton(pressbutton);
	}
} catch (e) {
	/**
	 * Joomla! 1.5.x
	 */
	function submitbutton(pressbutton) {
		return ViewCustomerSubmit.submitbutton(pressbutton);
	}
}