/**
 * Common javascript library.
 * 
 * @version $Id$
 * @package ARTIO Booking
 * @subpackage assets
 * @copyright Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link http://www.artio.net Official website
 */

var ACommon = {

	/**
	 * Get main edit form element.
	 * 
	 * @return element
	 */
	getForm : function() {
		return document.adminForm;
	},

	/**
	 * Setup time picker component. Create a time picker input box with image
	 * button to fetch time picker. Apend this elements as childs of given
	 * element. Run time picker setup function.
	 * 
	 * @param element
	 *            parent element to apend calendar items
	 * @param string
	 *            fieldName name of time picker form field
	 */
	createTimePicker : function(parent, fieldName, midnight) {
		// get new ID for time picker
		var id = ACommon.getNewId('timePicker#', 1);
		// generate items IDs
		var holderID = this.replaceSlash('timePickerHolder#', id);
		var fieldID = this.replaceSlash('timePicker#', id);
		var togglerID = this.replaceSlash('timePickerToggler#', id);
		if (midnight) {
			var div2 = new Element('div');
			var midnightInput = new Element('input', {
				'type'    : 'checkbox',
				'class'   : 'timePickerMidnight',
				'onclick' : 'timePickerMidnight(this)'
			});
			var midnightLabel = new Element('span');
			midnightLabel.set('text', LGMidnight);
		}
		var div1 = new Element('div', {'class': 'timePickerDiv'});
		var div3 = new Element('div', {'class': 'picker'});
		// create form field
		var fieldAnchor = new Element('input', {
			'id' : fieldID,
			'type' : 'text',
			'class' : 'timePicker',
			'value' : '',
			'name' : fieldName,
			'size' : 5
		});
		// create image button
		var togglerAnchor = new Element('img', {
			'id' : togglerID,
			'src' : timePickerToggler,
			'alt' : '',
			'class' : 'clock'
		});
		// create holder element
		var holderAnchor = new Element('div', {
			'id' : holderID,
			'class' : 'time_picker_div'
		});
		// append items as childs of given parent
		if (midnight) {
			div2.appendChild(midnightInput);
			div2.appendChild(midnightLabel);
			div1.appendChild(div2);
		}
		div3.appendChild(fieldAnchor);
		div3.appendChild(togglerAnchor);
		div3.appendChild(holderAnchor);
		div1.appendChild(div3);
		parent.appendChild(div1);
		// setup time picker
		var timePicker = new TimePicker(holderID, fieldID, togglerID, {
			format24 : true,
			imagesPath : timePickerImages
		});
		timePickers.push(timePicker);
	},

	openTimePicker : function(index) {
		timePickers[index].openTimePicker();
	},

	/**
	 * Setup calendar component. Create a calendar input box with image button
	 * to fetch calendar. Apend this elements as childs of given element. Run
	 * calendar setup function.
	 * 
	 * @param element
	 *            parent element to apend calendar items
	 * @param string
	 *            inputIDMask mask to create new calendar ID
	 * @param string
	 *            fieldName name of calendar form field
	 */
	createCalendar : function(parent, inputIDMask, fieldName) {
		// get new ID by calendar ID mask
		var id = ACommon.getNewId(inputIDMask, 1);
		// generate new ID for inputbox
		var inputId = this.replaceSlash(inputIDMask, id);
		// generate new ID for image button
		var imgId = inputId + '_img';
		// generate new ID for image eraser
		var eraId = inputId + '_era';
		// generate new ID for display area
		var daId = inputId + '_da';
		// create display area
		var daAnchor = new Element('span', {
			'id' : daId,
			'class' : 'calendar_da'
		});
		// create input box
		var inputAnchor = new Element('input', {
			'id' : inputId,
			'type' : 'hidden',
			'value' : '',
			'name' : fieldName

		});
		// create image button
		var imgAnchor = new Element('img', {
			'id' : imgId,
			'class' : 'calendar',
			'alt' : 'calendar',
			'src' : calendarHolder
		});
		// create image eraser
		var eraAnchor = new Element('img', {
			'id' : eraId,
			'class' : 'calendar_era',
			'alt' : 'erase',
			'src' : calendarEraser,
			'onclick' : 'ACommon.resetCalendar(\'' + inputId + '\')'
		});
		// append calendar items as childs of given parent
		parent.appendChild(daAnchor);
		parent.appendChild(inputAnchor);
		parent.appendChild(imgAnchor);
		parent.appendChild(eraAnchor);
		// setup calendar
		Calendar.setup({
			displayArea: daId,
			inputField : inputId,
			ifFormat: "%Y-%m-%d %H:%M:%S",
			daFormat : dateFormat,
			button : imgId,
			align : 'Tl',
			singleClick : true
		});
	},

	resetCalendar : function(id) {
		document.id(id + '_da').innerHTML = '';
		document.id(id).value = '';
	},
	
	/**
	 * Set ID of parent children elements by IDs mask and old and new value.
	 * 
	 * @param parent
	 *            ID
	 * @param replace
	 *            params to replace
	 * @param ids
	 *            masks
	 * @param from
	 *            old ID value
	 * @param to
	 *            new ID value
	 */
	setIds : function(parent, replaces, ids, from, to) {
		for ( var i = 0; i < ids.length; i++) {
			var sFrom = this.replaceSlash(ids[i], from);
			var eFrom = document.id(parent).getElementById(sFrom);
			var sTo = this.replaceSlash(ids[i], to);
			for ( var j = 0; j < replaces.length; j++) {
				eFrom.setAttribute(replaces[j], sTo);
			}
		}
	},

	/**
	 * Search new ID by mask from start value.
	 * 
	 * @param mask
	 *            ID
	 * @param start
	 *            search value
	 */
	getNewId : function(mask, start) {
		var id = start;
		while (true) {
			var el = document.getElementById(this.replaceSlash(mask, id));
			if (!el) {
				return id;
			}
			id++;
		}
	},

	/**
	 * Replace slash in string by added value.
	 * 
	 * @param string
	 *            where search and replace
	 * @param value
	 *            to replace
	 */
	replaceSlash : function(string, value) {
		return string.replace(/#/, value);
	},

	/**
	 * Save bookmark position into cookies.
	 */
	saveBookmark : function() {
		var items = document.id('tabone').getChildren();
		this.setCookie('startOffset', 0);
		for ( var i = 0; i < items.length; i++)
			if (items[i].className.match(/open/) != null)
				this.setCookie('startOffset', i);
	},

	setCookie : function(param, value) {
		try {
			/**
			 * Joomla! 1.6.x Mootools 1.3
			 */
			Cookie.write(param, value);
		} catch (e) {
			/**
			 * Joomla! 1.5.x Mootools 1.12
			 */
			Cookie.set(param, value);
		}
	},

	/**
	 * Remove table rows if checked checkbox on row begin.
	 * 
	 * @param parent
	 *            tbody ID
	 * @param name
	 *            of checkboxes
	 * @return boolean if true deleted all rows
	 */
	removeRows : function(parent, name, two) {
		var items = document.id(parent).getElements('input[name^=' + name + ']');
		var deleted = 0;
		for ( var i = 1; i < items.length; i++) {
			var item = document.id(items[i]);
			if (document.id(item).checked) {
				var cell = document.id(item).getParent();
				var row = document.id(cell).getParent();
				if (two == true)
					var row2 = row.getNext();
				var body = document.id(row).getParent();
				body.removeChild(row);
				if (two == true)
					body.removeChild(row2);
				deleted++;
			}
		}
		if (deleted == 0 && items.length > 1) {
			alert(LGErrSelectItems);
		}
		var deletedAll = (items.length - 1) == deleted;
		return deletedAll;
	},

	/**
	 * Set all parents elements of type input hidden.
	 */
	check : function(e) {
		var parent = document.id(e).getParent();
		var allAnchors = parent.getElements('input');
		for ( var i = 0; i < allAnchors.length; i++) {
			if (document.id(allAnchors[i]).getProperty('type') == 'hidden') {
				document.id(allAnchors[i]).setAttribute('value', e.checked ? '1' : '0');
				return;
			}
		}
		// checkbox in label
		parent.getPrevious().setAttribute('value', e.checked ? '1' : '0');
	},
	/**
	 * Set all parents elements of type input hidden.
	 */
	setCheck : function(e, value) {
		if (e.disabled != true) {
			var parent = document.id(e).getParent();
			var allAnchors = parent.getElements('input');
			for ( var i = 0; i < allAnchors.length; i++) {
				if (document.id(allAnchors[i]).getProperty('type') == 'hidden') {
					document.id(allAnchors[i]).setAttribute('value', value ? '1' : '0');
					e.checked = value;
				}
			}
		}
	},

	/**
	 * Select default calendar.
	 */
	calendarSelect : function(count) {
		for ( var j = 0; j < count; j++) {
			var def = document.getElementById('def' + j);
			var cal = document.getElementById('cal' + j);
			// var shi = document.getElementById('shi' + j);

			if (def.checked) {
				cal.checked = false;
				cal.disabled = true;
			} else
				cal.disabled = false;
			/*
			 * if (!cal.checked && !def.checked) { shi.checked = false;
			 * shi.disabled = true; } else shi.disabled = false;
			 */
		}
	},

	/**
	 * Valid multi form parameters (all must be filled). Except no empty string
	 * value or no zero value.
	 * 
	 * @param string
	 *            tagName type of form input
	 * @param string
	 *            regular expression for identify field name
	 * @param string
	 *            error message in alert window on error, if empty string alert
	 *            no use
	 * @param boolean
	 *            ignore first found (if use hidden row as mask)
	 * @return boolean true on valid, false on invalid
	 */
	validMultiParam : function(tagName, namePrefix, errorMsg, ignoreFirst) {
		if (tagName == 'textarea|input') {
			var items = new Array();
			var items1 = document.id(document).getElements(
					'textarea[name^=' + namePrefix + ']');
			var items2 = document.id(document).getElements(
					'input[name^=' + namePrefix + ']');
			for ( var i = 0; i < items1.length; i++) {
				if (items1[i].style.display != 'none') {
					items.push(items1[i]);
					items2[i].value = items1[i].value;
				} else if (items2[i].style.display != 'none') {
					items.push(items2[i]);
					items1[i].value = items2[i].value;
				}
			}
		} else {
			var items = document.id(document).getElements(
					tagName + '[name^=' + namePrefix + ']');
		}
		var start = ignoreFirst ? 1 : 0;
		for ( var i = start; i < items.length; i++) {
			var value = trim(items[i].value);
			if (!items[i].disabled
					&& items[i].style.display != 'none'
					&& ((tagName == 'input' || tagName == 'textarea') && value == '')
					|| (tagName == 'select' && (value == '0' || value == ''))) {
				if (errorMsg != '')
					alert(errorMsg);
				return false;
			}
		}
		return true;
	},

	/**
	 * Parse integer value from given string
	 * 
	 * @param string
	 *            value to test
	 * @return int integer value
	 */
	parseInt : function(value) {
		// value = trim(value);
		var newValue = '';
		var length = value.length;
		for ( var i = 0; i < length; i++) {
			var char = value.charAt(i);
			switch (char) {
			case '1':
			case '2':
			case '3':
			case '4':
			case '5':
			case '6':
			case '7':
			case '8':
			case '9':
				newValue += char;
			}
			if (char == '0' && (newValue != '' || length == 1)) {
				newValue += char;
			}
		}
		return newValue;
	},

	/**
	 * Parse float value from given string
	 * 
	 * @param string
	 *            value to test
	 * @return int float value
	 */
	parseFloat : function(value, allowNegative) {
		var newValue = '';
		var length = value.length;
		var haveDot = false;
		for ( var i = 0; i < length; i++) {
			var char = value.charAt(i);
			switch (char) {
			case '-':
				if (i == 0 && allowNegative == true)
					newValue += char;
				break;
			case "\n":
			case "\r\n":
				haveDot = false;
				newValue += char;
				break;
			case '.':
				if (newValue != '' && !haveDot)
					haveDot = true;
				else
					break;
			case '0':
				if (length > 1 && i == 0 && value.charAt(i + 1) != '.')
					break;
			case '1':
			case '2':
			case '3':
			case '4':
			case '5':
			case '6':
			case '7':
			case '8':
			case '9':
				newValue += char;
				break;
			}
		}
		return newValue;
	},

	/**
	 * Convert input value to integer.
	 * 
	 * @param el
	 *            element object
	 */
	toInt : function(el) {
		var oldValue = el.value;
		var newValue = this.parseInt(oldValue);
		if (oldValue.toString() != newValue.toString()) {
			el.value = newValue;
		}
	},
	
	toIntLimit : function(min, max, el) {
		ACommon.toInt(el);
		min = min.toInt();
		max = max.toInt();
		val = el.value.toInt();
		if (min != 0 && val < min)
			el.value = min;
		if (max != 0 && val > max)
			el.value = max;
	},

	/**
	 * Convert input value to float.
	 * 
	 * @param el
	 *            element object
	 */
	toFloat : function(el, allowNegative) {
		var oldValue = el.value;
		var newValue = this.parseFloat(oldValue, allowNegative);
		if (oldValue.toString() != newValue.toString()) {
			el.value = newValue;
		}
	},

	/**
	 * Open and close box with information text.
	 */
	info : function(element) {
		var children = document.id(element).getChildren();
		var text = children[1];
		if (document.id(text).style.display == 'none') {
			document.id(text).style.display = ''
		} else {
			document.id(text).style.display = 'none'
		}
	},

	/**
	 * Valid date or time interval from - to. Date from has to be higher or equal to date to.
	 * If one of fields is empty then function doesn't compare and return true.
	 * Input fields have to have value in database format to compare as strings.
	 * EQ: 2011-01-01 10:00:00
	 * 
	 * @param begin string ID of input field with date from value 
	 * @param end string ID of inptu field with date to value
	 * @param msg string error message for alert
	 * @param multi bool use multi param
	 * @param viceversa bool up/down viceversa (time range over midnight)
	 * @param vv string is viceversa option field name
	 * @param vvv string is viceversa option field value
	 * @param msg2 string error message for viceversa
	 * @returns {Boolean}
	 */
	validDateTimeInterval : function(from, to, msg, multi, viceversa, vv, vvv, msg2) {
		var froms = new Array();
		var tos = new Array();
		if (viceversa)
			var vvs = new Array();
		if (multi == true){
			var efroms = document.id(document).getElements('input[name^=' + from + ']');
			var etos = document.id(document).getElements('input[name^=' + to + ']');
			if (viceversa)
				var evvs = document.id(document).getElements('select[name^=' + vv + ']');
			var length = Math.min(efroms.length, etos.length);
			for (var i = 0; i < length; i++) {
				if (efroms[i].id.trim() && etos[i].id.trim()) {
					froms.push(efroms[i].id);
					tos.push(etos[i].id);
					if (viceversa)
						vvs.push(evvs[i]);
				}
			}
		} else {
			if (document.id(from) != null)
				froms.push(from);
			if (document.id(to) != null)
				tos.push(to);
			if (viceversa && document.id(viceversa) != null)
				vvs.push(viceversa);
		}
		for(var i = 0; i < froms.length; i++) {
			var from = document.id(froms[i]).value.trim();
			var to = document.id(tos[i]).value.trim();
			if (viceversa)
				var vv = vvs[i].value == vvv;
			if (to != '' && from != '') {
				if (viceversa && vv) { 
					if (to >= from) {
						alert(msg2);
						return false;
					}
				} else if (to < from && to != '00:00') {
					alert(msg);
					return false;
				}
			}
		}
		return true;
	},
	
	/**
	 * Get ID of new member of field group EQ title[0], title[1], title[2] etc
	 * @params string selector EQ input[name^=title] 
	 * @return int
	 */
	getNextId : function(selector)
	{
		var list = document.getElements(selector);
		var nextId = 0;
		for (var i = 0; i < list.length; i++) {
			var qualifyName = list[i].get('name').replace('[]', '[0]');
			var id = qualifyName.match(/[0-9]+/); // first match only
			nextId = Math.max(nextId, id);
		}
		nextId ++;
		return nextId;
	}
}
