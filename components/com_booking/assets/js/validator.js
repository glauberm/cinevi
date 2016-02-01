/**
 * Javascript values validator.
 * 
 * @version $Id$
 * @package ARTIO Booking
 * @subpackage assets
 * @copyright Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link http://www.artio.net Official website
 */

var AValidator = {

	/**
	 * Is integer.
	 * 
	 * @param value
	 */
	isInt : function(value) {
		regex = /^[1-9]\d*$/;
		return regex.test(value);
	}
}