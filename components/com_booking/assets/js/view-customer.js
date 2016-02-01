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

var ViewCustomer = {

	/**
	 * Submit page to display customers reservations list.
	 */
	displayReservations : function() {
		var form = ACommon.getForm();
		form.controller.value = 'reservation';
		form.task.value = '';
		form.submit();
	},
	
	selectExistingUser : function() {
		document.id('user1').setStyle('display', '');
		document.id('user2').setStyle('display', 'none');
		document.id('user3').setStyle('display', 'none');
		document.id('user4').setStyle('display', 'none');
		document.id('user5').setStyle('display', 'none');
	},
	
	selectNewUser : function() {
		document.id('user1').setStyle('display', 'none');
		document.id('user2').setStyle('display', '');
		document.id('user3').setStyle('display', '');
		document.id('user4').setStyle('display', '');
		document.id('user5').setStyle('display', '');
	}
}