/**
 * Generate property element of type selectbox. Functions: - create adding or editing window, create new element and set added options, set existing
 * element
 * 
 * @version $Id$
 * @package ARTIO Booking
 * @subpackage assets
 * @copyright Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link http://www.artio.net Official website
 */

var ATemplateSelectOne = {
	/* save old selected item */
	selected : '',
	/**
	 * Render editing. Add textarea to add options values and fill with existing.
	 * 
	 * @param element existing element to edit
	 * @param id
	 */
	render : function(element, id) {
		var options = null;
		/* if existing element get own options */
		if (element) {
			var options = element.options;
		}
		var html = '';
		/* textarea and label to add or edit options */
		html += '<label for="options" class="bold">' + LGOptions + ':</label>';
		html += '<textarea rows="10" cols="30" id="options" name="options">';
		/* if have existing options add into textarea, every on one line */
		if (options) {
			for ( var i = 0; i < options.length; i++) {
				html += options[i].innerHTML + "\n";
			}
		}
		html += '</textarea>';
		/* info for user */
		html += '<p>' + LGSelectOneInfo + '</p>';
		/* set window size */
		ATemplate.resize(300, 470);
		/* return html to render edit window */
		return html;
	},

	/**
	 * Set options values to existing element.
	 * 
	 * @param element
	 * @param id
	 */
	set : function(element, id) {
		if (this.loadOptions() == false) {
			return false;
		}
		/* remove old options */
		this.removeOptions(element);
		/* add new and return output for save property on server */
		return this.addOptions(id, element);
	},

	/**
	 * Create new selectbox element and fill by user added options.
	 * 
	 * @param id
	 */
	build : function(id) {
		/* create element */

		var className = 'inputbox';
		var name = ATemplate.getElementName(id);
		//check if element exist - collision
		/*while(document.getElementsByName(name).length)
			name = ATemplate.getElementName(id = (ATemplate.getId(true, true)-1));*/
		var pid = ATemplate.getParamsId(id);

		try { /* internet explorer */
			element = document.createElement('<select name="' + name + '" class="' + className + '" id="' + pid + '"/>');
		} catch (err) { /* other browsers */
			var element = document.createElement('select');

			/* set params */
			element.id = pid;
			element.className = className;
			element.name = name;
		}

		/* add options into element and get output for saving on server */
		var output = this.addOptions(id, element);
		if (output == false) {
			return false;
		}

		/* build output for generate new property row on page */
		var build = new Array(2);
		build[0] = element;
		build[1] = output;
		return build;
	},

	/**
	 * Add optons into element and get output for saving property on server.
	 * 
	 * @param id
	 * @param element to add
	 */
	addOptions : function(id, element) {
		/* type of property */
		var output = 'list';
		/* load added options */
		var options = this.loadOptions();
		if (options == false) {
			return false;
		}
		for ( var i = 0; i < options.length; i++) {
			/* get options value like safe string */
			var evalue = ATemplate.safeString(options[i]);
			/* create option */
			var option = document.createElement('option');
			/* set option values */
			option.text = options[i];
			option.value = evalue;
			if (option.value == this.selected) {
				option.setAttribute('selected', 'selected');
			}
			/* add into selectbox */
			element.options.add(option);
			/* add value into output */
			output += '|' + evalue;
		}
		return output;
	},

	/**
	 * Remove all options from element.
	 * 
	 * @param element
	 */
	removeOptions : function(element) {
		var options = element.options;
		this.selected = element.options[element.selectedIndex].value;
		for ( var i = options.length - 1; i > -1; i--) {
			element.remove(i);
		}
	},

	/**
	 * Load user added options from textarea on editing page.
	 */
	loadOptions : function() {
		var aOptions = new Array();
		/* textarea element content */
		options = document.paramsWindow.options.value;
		/* clean data */
		option = trim(options);
		/* split every line like one array item */
		options = options.split("\n");
		for ( var i = 0; i < options.length; i++) {
			/* clean string value */
			var value = trim(options[i]);
			if (value != '') { /* if not emty add */
				aOptions.push(value);
			}
		}
		if ((aOptions.length < 2)) {
			alert(LGErrAddOptions);
			return false;
		}
		return aOptions;
	}
}