/**
 * Javascript for reservations list.
 * 
 * @version $Id$
 * @package ARTIO Booking
 * @subpackage assets
 * @copyright Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link http://www.artio.net Official website
 */

var ViewReservations = {

	/**
	 * Submit page to storno reservations.
	 */
	task : function(task) {
		var form = ACommon.getForm();
		form.task.value = task;
		form.submit();
	}
}