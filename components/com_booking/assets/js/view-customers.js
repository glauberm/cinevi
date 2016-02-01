/**
 * Javascript for list customers form
 * 
 * @version $Id$
 * @package ARTIO Booking
 * @subpackage assets
 * @copyright Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link http://www.artio.net Official website
 */

var ListCustomers = {
	
	/**
	 * Select customer from element window.
	 * 
	 * @param id
	 * @param name
	 * @return false to disable page submit
	 */
	select : function(id, name) {
		window.parent.document.getElementById('customer_id').value = id;
		window.parent.document.getElementById('customer_name').value = name;
		window.parent.SqueezeBox.close();
		return false;
	},
	
	/**
	 * Fill customer form.
	 */
	fillCustomerCard : function(id) {
		new Request({
		    url: customerRoute,
		    method: 'GET',
		    data: {'id' : id},
		    async: false,
		    onSuccess: function(data) {
		    	Object.each(JSON.decode(data), function(v, k) {
		    		var e = window.parent.document.getElement('*[name=' + k + ']');
		    		if (e)
		    			e.value = v;
		    		if (v instanceof Object && k == 'fields')
		    			Object.each(v, function(v, k) {
	    					var e = window.parent.document.getElement('*[name=' + k + ']');
	    					if (e) {
	    						if (e.type == 'radio') {
	    							e = window.parent.document.getElement('input[name=' + k + '][type=radio][value=' + v.rawvalue + ']');
	    							if (e) {
	    								e.set('checked', true);
	    								l = window.parent.document.getElement('label[for=' + e.id + ']');
	    								if (l)
	    									l.set('class', 'btn active btn-success');
	    							}
	    						} else
	    							e.value = v.value;
	    					}
		    			});
		    	});
		    }
		}).send();
	}
}