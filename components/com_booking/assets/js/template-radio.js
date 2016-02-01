/**
 * Generate property elements of type radio button with labels. Functions: -
 * create adding or editing window, create new element and set button and
 * labels, set existing element
 * 
 * @version $Id$
 * @package ARTIO Booking
 * @subpackage assets
 * @copyright Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link http://www.artio.net Official website
 */

var ATemplateRadio = {
	/* save old checked item */
	checked : '',
	/**
	 * Render editing window. Add textarea to set radio buttons values with info
	 * text for user.
	 * 
	 * @param element
	 *            (existing)
	 * @param id
	 */
	render : function(element, id) {
		var html = '';
		/* textarea element label */
		html += '<label for="options" class="bold">' + LGOptions + ':</label>';
		/* textarea for add or edit radio buttons values */
		html += '<textarea rows="10" cols="30" id="options" name="options">';
		/* get all existing radio buttons with property name */
		var elements = document.getElementsByName(ATemplate.getElementName(id));
		this.checked = '';
		for ( var i = 0; i < elements.length; i++) {
			/* get buttons values and add into textarea every on single line */
			if (elements[i].value != '') {
				html += elements[i].value + "\n";
				if (elements[i].getAttribute('checked') == 'checked'
						|| elements[i].checked) {
					this.checked = elements[i].value;
				}
			}
		}
		html += '</textarea>';
		/* info text for user */
		html += '<p>' + LGSelectOneInfo + '</p>';
		/* set window size */
		ATemplate.resize(300, 470);
		/* return editing HTML to render full window */
		return html;
	},

	/**
	 * Set existing property elements.
	 * 
	 * @param element
	 * @param id
	 */
	set : function(element, id) {
		/* load all elements */
		var elements = this.getElements(id);
		if (elements == false) {/* no add any option */
			return false;
		}
		/* get cell containing element */
		//var cell = document.getElementById(ATemplate.getValueCellId(id));
		var cell = document.getElementById(ATemplate.getValueCellId(id));
		/* remove all elements from cell */
		cell.innerHTML = '';
		/* add all new elements into cell */
		for ( var i = 0; i < elements.length; i++) {
			cell.appendChild(elements[i]);
			ATemplate.appendNewLine(cell);
		}
		return this.getOutput();
	},

	/**
	 * Create new property. Get all property elements - radio buttons and
	 * labels.
	 * 
	 * @param id
	 */
	build : function(id) {
		var build = new Array(2);
		build[0] = this.getElements(id);
		if (build[0] == false) {
			return false;
		}
		build[1] = this.getOutput();
		return build;
	},

	/**
	 * Get all property elements - radio buttons and labels.
	 * 
	 * @param id
	 */
	getElements : function(id) {
		var elements = Array();
		/* load user added values */
		var options = ATemplateSelectOne.loadOptions();
		if (options == false) {/* no add any options */
			return false;
		}
		options.unshift('');
		var anyToCheck = true;
		for ( var i = 0; i < options.length; i++) {
			var value = ATemplate.safeString(options[i]);
			if (value == this.checked) {
				anyToCheck = false;
				break;
			}
		}
		var html = '<fieldset id="' + ATemplate.getParamsId(id) + '" class="radio">';
		for ( var i = 0; i < options.length; i++) {
			var value = ATemplate.safeString(options[i]);
			var checked = ((value == this.checked) || ((this.checked == '' || anyToCheck) && value == ''));
			var displayNone = (value == '');
			var name = ATemplate.getElementName(id);
			var pid = 'params' + id + value;
			html += '<input type="radio" class="inputRadio" id="'
						+ pid
						+ '" name="'
						+ name
						+ '" value="'
						+ value
						+ '" '
						+ (checked ? 'checked="checked"' : '')
						+ ' '
						+ (displayNone ? 'style="display: none"' : '')
						+ '/>';
			if (value != '')
				html += '<label for="params' + id + i + '">' + options[i] + '</label>';
		}
		html += '</fieldset><input id="param_' + id + '_type" type="hidden" value="radio" name="param_' + id + '_type" />';
		return Array.from(Elements.from(html));
	},

	/**
	 * Get output for saving property on server.
	 */
	getOutput : function() {
		/* get added options */
		var options = ATemplateSelectOne.loadOptions();
		/* property type */
		var output = 'radio';
		for ( var i = 0; i < options.length; i++) {
			/* add option as safe string */
			output += '|' + ATemplate.safeString(options[i]);
		}
		return output;
	}
}