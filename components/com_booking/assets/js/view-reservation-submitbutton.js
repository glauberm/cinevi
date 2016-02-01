/**
 * Javascript for edit reservation form.
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

var ViewReservation = {

	submitbutton : function(pressbutton) {
		var form = ACommon.getForm();
		switch (pressbutton) {
		case 'save':
		case 'apply':
			break;
		default:
			submitform(pressbutton);
			return;
		}
		for ( var i = 0; i < rfields.length; i++) {
			var field = rfields[i];
			var el = document.getElementById(field.name);

			if (el) {
				var value = trim(el.value);
				if (value == '' || value == '0') {
					alert(field.msg);
					return;
				}
				if (el.name == 'email' && !isEmail(value)) {
					alert(LGErrAddReservationValidEmail);
					return;
				}
			} else { // radio button yes/no
                var yes = document.id(field.name + '-yes');
                var no = document.id(field.name + '-no');
                if (yes && no && !yes.checked && !no.checked) {
                    alert(field.msg);
					return;
                }
            }
		}
		if (form.capacity != undefined) {
			if (trim(form.capacity.value) == '') {
				alert(LGErrAddReservationCapacity);
				return;
			} else if (!AValidator.isInt(form.capacity.value)) {
				alert(LGErrReservationCapacityMustBeInteger);
				return;
			}
		}
		if (form.subject != undefined
				&& (trim(form.subject.value) == '' || trim(form.subject.value) == '0')) {
			alert(LGErrAddReservationSubject);
			return;
		}
		if (form.subject_title != undefined
				&& trim(form.subject_title.value) == '') {
			alert(LGErrAddReservationSubjectTitle);
			return;
		}
		if (form.from != undefined && trim(form.from.value) == '') {
			alert(LGErrAddReservationFrom);
			return;
		}
		if (!ACommon.validDateTimeInterval('from', 'to', LGErrReservationInvalidInterval)) {
			return;
		}
		if (form.to != undefined && trim(form.to.value) == '') {
			alert(LGErrAddReservationTo);
			return;
		}
		if (form.price != undefined && trim(form.price.value) == '') {
			alert(LGErrAddReservationPrice);
			return;
		}
		if (form.captcha != undefined && trim(form.captcha.value) == '') {
			alert(LGErrAddCaptcha);
			return;
		}
		if (form.rtype != undefined && trim(form.rtype.value) == '') {
			alert(LGErrAddRType);
			return;
		}
		if (form.accept_terms_of_contract != undefined && !form.accept_terms_of_contract.checked) {
			alert(LGErrAcceptContract);
			return;
		}
		if (form.accept_terms_of_privacy != undefined && !form.accept_terms_of_privacy.checked) {
			alert(LGErrAcceptPrivacy);
			return;
		}
		submitform(pressbutton);
	},
    
    /**
     * Change bookable item of a reservation item.
     * 
     * @param int id
     */
    openChangeItem : function(id) {
        new Request({
            url: URLRoot,
            method: 'get',
            data: {'option': 'com_booking', 'controller': 'reservation', 'task': 'getChangeableItems', 'id': id, 'r': Math.random(1, 10000)},
            async: false,
            onSuccess: function(response) {
                response = JSON.parse(response);
                if (response.code === 1) {
                    document.id('changeItemSelect' + id).set('html', response.html);
                    document.id('openChangeItem' + id).hide();
                    document.id('closeChangeItem' + id).show('inline-block');
                    document.id('itemTitle' + id).hide();
                } else {
                    alert(response.html);
                }
                
            }
        }).send();
    },
    
    /**
     * Show button to save reservation when user selects an item to change.
     * 
     * @param int id reservation item id
     */
    selectChangeItem : function(id) {
        document.id('subject' + id).value === '0' ? document.id('changeItem' + id).hide() : document.id('changeItem' + id).show('inline-block');
    },
    
    /**
     * Close change an item dialog.
     * 
     * @param int id reservation item id
     */
    closeChangeItem : function(id) {
        document.id('changeItemSelect' + id).set('html', '');
        document.id('openChangeItem' + id).show('inline-block');
        document.id('changeItem' + id).hide();
        document.id('closeChangeItem' + id).hide();
        document.id('itemTitle' + id).show();
    },
    
    /**
     * Do item changing.
     * 
     * @param int id reservation item id
     */
    changeItem : function(id) {
        if (id !== 0) {
            var data = document.id('subject' + id).value;
        } else {
            id = document.getElement('[name=changed_reservation_item_id]').value;
            var occupancy = [];
            document.getElements('*[name^=occupancy]').each(function(e) {
                var match = e.name.match(/occupancy\[[0-9]+\]\[([0-9]+)\]/);
                occupancy[match[1].toInt()] = e.value;
            });
            var data = JSON.encode({'id': document.getElement('[name=subject[0]]').value, 'boxIds': document.getElement('[name=boxIds[0]]').value.split(','), 'occupancy': occupancy});
        }
        if (data !== '0') {
            new Request({
                url: URLRoot,
                method: 'post',
                data: {'option': 'com_booking', 'controller': 'reservation', 'task': 'changeItem', 'changed_reservation_item_id': id, 'data': data, 'r': Math.random(1, 10000)},
                async: false,
                onSuccess: function(response) {
                    response = JSON.parse(response);
                    if (response.code === 1) {
                        if (window.parent.SqueezeBox) {
                            window.parent.location.reload();
                            window.parent.SqueezeBox.close();
                        } else {
                            window.location.reload();
                        }
                    } else {
                        alert(response.html);
                    }
                }
            }).send();
        }
    },
    
    /**
     * Open popup window to change reservation.
     * 
     * @param int subjectId current bookable item id
     * @param int itemId reservation item id
     * @param int rType reservation type
     */
    openChangeDate : function(subjectId, itemId, rType) {
        var url = new URI(URLRoot);
        url.setData({'option': 'com_booking', 'view': 'subject', 'id': subjectId, 'changed_reservation_item_id': itemId, 'tmpl': 'component', 'calendar': (rType === 1 ? 'weekly' : 'monthly'), /*'layout': (rType === 1 ? 'default_calendar_weekly' : 'default_calendar_monthly'),*/ 'mode': 'change'});
        SqueezeBox.open(url.toString(), {'handler': 'iframe', 'size': {'x': '600', 'y': '450'}});
    },
    
    /**
     * Change bookable item during reservation changing.
     * 
     * @param int subjectId new bookable item id
     */
    reloadChangeItem : function(subjectId) {
        var url = new URI(URLRoot);
        window.location = url.setData({'option': 'com_booking', 'view': 'subject', 'id': subjectId, 'changed_reservation_item_id': document.getElement('[name=changed_reservation_item_id]').value, 'tmpl': 'component', 'mode': 'change'}).toString();
    },
    
    /**
     * Open block to add more customer names
     */
    addMoreNames : function() {
        document.id('addMoreNames').show();
        document.id('addMoreButton').hide();
    },

    /**
     * Add next field for customer name
     */
    addNextName : function() {
        Elements.from('<input type="text" name="more_names[]" value="" />').inject(document.id('addNextButton'), 'before');
    },

    /**
     * Hide block to add more customer names and delete names fields
     */
    hideAddMoreNames : function() {
        document.id('addMoreNames').hide();
        document.getElements('#addMoreNames input[name^=more_names]').each(function(e) {
           e.value = '';
        });
        document.id('addMoreButton').show();
    }
}

try {
	/**
	 * Joomla! 1.6.x
	 */
	Joomla.submitbutton = function(pressbutton) {
		return ViewReservation.submitbutton(pressbutton);
	}
} catch (e) {}
/**
 * Joomla! 1.5.x
 */
function submitbutton(pressbutton) {
	return ViewReservation.submitbutton(pressbutton);
}

function refreshReservation() {
	document.id('adminForm').task.value = 'refresh';
	document.id('adminForm').controller.value = '';
	new Request({
	    url: 'index.php?option=com_booking&view=reservation&layout=form&ajaxForItems=1',
	    method: 'post',
	    data: document.id('adminForm').toQueryString(),
	    async: false,
	    onSuccess: function(responseText) {
	    	var data = JSON.decode(responseText);
	        document.id('reservedItems').set('html', data['items']);
	        document.id('reservationTotal').set('html', data['total']);
	        document.id('adminForm').controller.value = 'reservation';
	    }
	}).send();
	return false;
}

function removeReservationItem(id, rid) {
    if (confirm(LGAreYouSure))
		new Request({
			url: 'index.php?option=com_booking&task=reservation.remove_item_db&tmpl=component',
			method: 'get',
			data: {'id' : id, 'rid': rid},
			async: false,
			onSuccess: function(responseText) {
				if (responseText == '1') {
					if (document.id('reservationItem'+id))
						document.id('reservationItem'+id).dispose();
					if (document.id('reservationItemSupplements'+id))
						document.id('reservationItemSupplements'+id).dispose();
				} else {
					alert(LGErrDisallowRemoveOnlyItem);
				}
			}
		}).send();
}