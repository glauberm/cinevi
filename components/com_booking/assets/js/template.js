/**
 * Template generating properties editing view. Functions: create new many types
 * property, new property add in table with output information for saving on
 * server, can edit existing property, delete existing property
 * 
 * @version $Id$
 * @package ARTIO Booking
 * @subpackage assets
 * @copyright Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link http://www.artio.net Official website
 */

var ATemplate = {

	/**
	 * Create window to choose new parameter type. Available are selectbox,
	 * textbox and radio buttons.
	 */
	add : function() {
		var html = '';
		html += '<form name="paramsWindow" class="property">';
		html += '  <div class="main">';
		html += '    <fieldset class="adminForm radio">';
		html += '      <legend>' + LGChoose + '</legend>';

		/* radio buttons to choose wanted type */
		html += '      <input type="radio" name="ctype" id="type-text" value="text" onChange="ATemplate.setType(this);" class="rcType inputRadio"/>';
		html += '      <label for="type-text">' + LGChooseTextBox + '</label>';
		html += '      <div class="clr">&nbsp;</div>';
		html += '      <input type="radio" name="ctype" id="type-textarea" value="textarea" onChange="ATemplate.setType(this);" class="rcType inputRadio"/>';
		html += '      <label for="type-textarea">' + LGChooseTextarea + '</label>';
		html += '      <div class="clr">&nbsp;</div>';
		html += '      <input type="radio" name="ctype" id="type-editor" value="editor" onChange="ATemplate.setType(this);" class="rcType inputRadio"/>';
		html += '      <label for="type-editor">' + LGChooseEditor + '</label>';
		html += '      <div class="clr">&nbsp;</div>';		
		html += '      <input type="radio" name="ctype" id="type-select-one" value="select-one" onChange="ATemplate.setType(this);" class="rcType inputRadio"/>';
		html += '      <label for="type-select-one">' + LGChooseSelectBox + '</label>';
		html += '      <div class="clr">&nbsp;</div>';
		html += '      <input type="radio" name="ctype" id="type-radio" value="radio" onChange="ATemplate.setType(this);" class="rcType inputRadio"/>';
		html += '      <label for="type-radio">' + LGChooseRadio + '</label>';
		html += '      <div class="clr">&nbsp;</div>';
		html += '      <input type="radio" name="ctype" id="type-checkbox" value="checkbox" onChange="ATemplate.setType(this);" class="rcType inputRadio"/>';
		html += '      <label for="type-checkbox">' + LGChooseCheckBox + '</label>';
		html += '      <div class="clr">&nbsp;</div>';

		/* toolbar to confirm choosed type or cancel operation */
		html += '      <div class="toolbarContainer">';
		html += '	     <table class="toolbar">';
		html += '          <tbody>';
		html += '            <tr>';

		/* create button */
		html += '              <td class="button">';
		html += '                <a class="toolbar" href="javascript:ATemplate.create();">';
		html += '                  <span class="icon-32-new icon-new" title="' + LGCreate + '">&nbsp;</span>';
		html += '                    ' + LGCreate;
		html += '                </a>';
		html += '              </td>';

		/* cancel button */
		html += '              <td class="button">';
		html += '                <a class="toolbar" href="javascript:ATemplate.close();">';
		html += '                  <span class="icon-32-cancel icon-delete" title="' + LGCancel + '">&nbsp;</span>';
		html += '                  ' + LGCancel;
		html += '                </a>';
		html += '              </td>';

		html += '            </tr>';
		html += '          </tbody>';
		html += '        </table>';
		html += '      </div>';
		html += '    </fieldset>';
		html += '  </div>';

		/* saving choosed type */
		html += '  <input type="hidden" name="type" value=""/>';
		html += '</form>';

		/* set window size */
		this.resize(260, 400);
		/* display window */
		this.display(html);
	},

	/**
	 * Set choosed property type to hidden create form parameter.
	 * 
	 * @param element
	 *            HTML element
	 */
	setType : function(element) {
		document.paramsWindow.type.value = element.value;
	},

	/**
	 * Get choosed property type from hidden create form parameter.
	 */
	getType : function() {
		return document.paramsWindow.type.value;
	},

	/**
	 * Event after click on create property button with test if user choose
	 * propert type. Open window to config property params.
	 */
	create : function() {
		var type = this.getType();
		if (!type) {
			alert(LGChoose);
		} else {
			this.config(0, type);
		}
	},

	/**
	 * Config existing or new property.
	 * 
	 * @param id
	 *            config element id
	 * @param type
	 *            type of element
	 */
	config : function(id, type) {
		var element = null;
		var stype = '';
		if (id != 0) { /* existing property */
			/* search property element */
			element = document.getElementById(this.getParamsId(id));
			if (element) { /* one item element - selectbox or textbox */
				stype = element.type;
			}
			if(stype == 'fieldset'){ /* more item element - radio buttons */
				/* get all elements by property name */
				var elements = document.getElementsByName(this.getElementName(id));
				if (elements) {
					var type = typeOf(elements);
					switch (type) {
					case 'array':
					case 'collection':
						if (elements.length > 0) {
							stype = elements[0].type;
						}
						break;
					default:
						stype = elements.type;
						break;
					}
				}
			}
			if (document.id('param_' + id + '_type')) {
				stype = document.id('param_' + id + '_type').value;
				if (stype == 'list')
					stype = 'select-one';
			}
		} else if (type != undefined) { /* new property */
			stype = type; /* type is given by method parameter */
		}
		var html = '';
		/* take editing page by property type */
		switch (stype) {
		case 'text': /* textbox */
			html = ATemplateText.render(element, id);
			break;
		case 'textarea': /* textarea */
			html = ATemplateTextarea.render(element, id);
			break;
		case 'editor': /* editor */
			html = ATemplateEditor.render(element, id);
			break;
		case 'select-one': /* selectbox */
			html = ATemplateSelectOne.render(element, id);
			break;
		case 'radio': /* radio buttons */
			html = ATemplateRadio.render(element, id);
			break;
		case 'checkbox': /* checkbox */
			html = ATemplateCheckbox.render(element, id);
			break;
		}
		/* render main window */
		this.render(id, html, stype);
	},

	/**
	 * Get property element id
	 * 
	 * @param id
	 */
	getParamsId : function(id) {
		return 'params_' + id;
	},

	/**
	 * Set property after editing.
	 * 
	 * @param id
	 * @return false to disable form submit
	 */
	set : function(id) {
		if (this.getEditTitle() == '') {
			alert(LGErrAddTitle);
			return;
		}
		/* search for editing element */
		var element = document.getElementById(this.getParamsId(id));
		if (element) { /* existing element - take type */
			var stype = element.type;
		} else { /* new element - take type from edit form */
			var stype = this.getType();
		}
		if (document.id('param_' + id + '_type')) {
			stype = document.id('param_' + id + '_type').value;
			if (stype == 'list')
				stype = 'select-one';
		}
		var output = null;
		switch (stype) {
		case 'text':/* textbox */
			output = id ? ATemplateText.set(element, id) : ATemplateText.build(this.getId(false, true));
			break;
		case 'textarea': /* textarea */
			output = id ? ATemplateTextarea.set(element, id) : ATemplateTextarea.build(this.getId(false, true));
			break;
		case 'editor': /* editor */
			output = id ? ATemplateEditor.set(element, id) : ATemplateEditor.build(this.getId(false, true));
			break;			
		case 'select-one': /* selectbox */
			output = id ? ATemplateSelectOne.set(element, id) : ATemplateSelectOne.build(this.getId(false, true));
			break;
		case 'radio': /* radio buttons */
			output = id ? ATemplateRadio.set(element, id) : ATemplateRadio.build(this.getId(false, true));
			break;
		case 'checkbox': /* checkbox */
			output = id ? ATemplateCheckbox.set(element, id) : ATemplateCheckbox.build(this.getId(false, true));
			break;
		}
		if (output == false) {
			return;
		}
		if (id) {/* existing - set next params */
			/* set property title */
			this.setTitle(id, this.getEditTitle());

			var icon = this.getSelectIcon();
			this.setIconThumbSrc(id, icon);
			this.setIconOrigSrc(id, icon);

			/*
			 * set hidden param with property text overwrite for saving on
			 * server
			 */
			this.setOutput(id, output, icon);
			/* set toolbar icons */

			/* get toolbar element */
			var toolbar = document.getElementById(this.getToolbarId(id));
			/* delete old searchable icon or empty div */
			this.removeSearchable(id, toolbar);
			/* delete old filterable icon or empty div */
			this.removeFilterable(id, toolbar);
			this.removeObjects(id, toolbar);
			this.removeObject(id, toolbar);
			/* set info icons */
			if (this.isSetSearchable()) {
				toolbar.appendChild(this.getImgSearchable(id));
			} else {
				this.addEmptyDiv(toolbar, this.getSearchableId(id));
			}
			if (this.isSetFilterable()) {
				toolbar.appendChild(this.getImgFilterable(id));
			} else {
				this.addEmptyDiv(toolbar, this.getFilterableId(id));
			}			
			if (this.isSetObjects()) {
				toolbar.appendChild(this.getImgObjects(id));
			} else {
				this.addEmptyDiv(toolbar, this.getObjectsId(id));
			}
			if (this.isSetObject()) {
				toolbar.appendChild(this.getImgObject(id));
			} else {
				this.addEmptyDiv(toolbar, this.getObjectId(id));
			}
		} else { /* new - create full row */
			this.build(this.getId(true, false), output);
		}
		/* close editing window */
		this.close();
	},

	/**
	 * Render editing window
	 * 
	 * @param id
	 *            ID of editing or new property(zero value)
	 * @param property
	 *            HTML content for editing concrete property type
	 * @param type
	 *            of property
	 */
	render : function(id, property, type) {
		/* get title if existing property */
		var title = this.getTitle(id);
		/* get sign if property have set searchable option */
		var searchable = this.getIconSearchable(id);
		searchable = (searchable && searchable.src) ? 'checked="checked"' : '';
		/* get sign if property have set filterable option */
		var filterable = this.getIconFilterable(id);
		filterable = (filterable && filterable.src) ? 'checked="checked"' : '';
		/* get sign if property have set display option */
		var objects = this.getIconObjects(id);
		objects = ((objects && objects.src) || objects == null) ? 'checked="checked"' : '';
		var object = this.getIconObject(id);
		object = ((object && object.src) || object == null) ? 'checked="checked"' : '';
		
		var html = '';
		html += '<form name="paramsWindow" class="property" onsubmit="return ATemplate.set(' + id + ');">';
		html += '  <div class="main">';

		/* field for set property title */
		html += '    <label for="title" class="bold">' + LGTitle + ':</label>';
		html += '    <input type="text" id="title" name="title" value="' + (title ? title.innerHTML : '') + '" size="30"/>';
		html += '    <div class="clr"></div>';

		/* concrete editing fields */
		html += property;

		/* fields for set if property is searchable and filterable */
		html += '    <input type="checkbox" id="searchable" name="searchable" value="1" class="rcType inputCheckbox" ' + searchable + '/>';
		html += '    <label for="searchable" class="bold fright">' + LGSearchable + '</label>';
		html += '    <input type="checkbox" id="filterable" name="filterable" value="1" class="rcType inputCheckbox" ' + filterable + '/>';
		html += '    <label for="filterable" class="bold fright">' + LGFilterable + '</label>';
		html += '    <input type="checkbox" id="objects" name="objects" value="1" class="rcType inputCheckbox" ' + objects + '/>';
		html += '    <label for="objects" class="bold fright">' + LGObjects + '</label>';
		html += '    <input type="checkbox" id="object" name="object" value="1" class="rcType inputCheckbox" ' + object + '/>';
		html += '    <label for="object" class="bold fright">' + LGObject + '</label>';

		html += '    <div class="icons">';
		html += '        <fieldset>';
		html += '            <legend>' + LGIcon + '</legend>';
		var setIcon = id ? this.getIconOrigSrc(id) : '';
		for ( var i = 0; i < TmpIconsThumbs.length; i++) {
			var iconThumb = TmpIconsThumbs[i];
			var iconOrig = TmpIconsReal[i];
			html += '            <img id="icon' + i + '" src="' + iconThumb + '" alt="" class="tmpIcon ' + (setIcon == iconOrig ? 'tmpIconSet' : '') + '" onclick="ATemplate.setIcon(' + i + ')" />';
		}
		html += '        </fieldset>';
		html += '    </div>';

		/* toolbar for set changes or cancel operation */
		html += '    <div class="toolbarContainer">';
		html += '	     <table class="toolbar">';
		html += '        <tbody>';
		html += '          <tr>';

		/* save button */
		html += '            <td class="button">';
		html += '              <a class="toolbar" href="javascript:ATemplate.set(' + id + ');">';
		html += '                <span class="icon-32-apply icon-new" title="' + LGApply + '">&nbsp;</span>';
		html += '                  ' + LGApply;
		html += '              </a>';
		html += '            </td>';

		/* cancel button */
		html += '            <td class="button">';
		html += '              <a class="toolbar" href="javascript:ATemplate.close();">';
		html += '                <span class="icon-32-cancel icon-cancel" title="' + LGCancel + '">&nbsp;</span>';
		html += '                  ' + LGCancel;
		html += '              </a>';
		html += '            </td>';
		html += '          </tr>';
		html += '        </tbody>';
		html += '      </table>';
		html += '    </div>';
		html += '  </div>';

		/* property type in hidden param */
		html += '  <input type="hidden" name="type" value="' + type + '"/>';
		html += '</form>';

		/* display window */
		this.display(html);
	},

	/**
	 * Get title of existing property
	 * 
	 * @param id
	 */
	getTitle : function(id) {
		return document.getElementById(this.getTitleId(id));
	},

	/**
	 * Get property title from editing form
	 */
	getEditTitle : function() {
		var value = document.paramsWindow.title.value;
		value = trim(value);
		return value;
	},

	/**
	 * Set property title on page
	 * 
	 * @param id
	 * @param value
	 */
	setTitle : function(id, value) {
		document.getElementById(this.getTitleId(id)).innerHTML = value;
	},

	/**
	 * Get element label id contained property title
	 * 
	 * @param id
	 */
	getTitleId : function(id) {
		return 'params_' + id + '-lbl';
	},

	/**
	 * Get element id of cell contained property element
	 * 
	 * @param id
	 */
	getValueCellId : function(id) {
		return 'params' + id + '-value';
	},

	/**
	 * Get property element name
	 * 
	 * @param id
	 */
	getElementName : function(id) {
		return 'params[' + id + ']';
	},

	/**
	 * Get property thumb icon id
	 * 
	 * $param id
	 */
	getIconThumbId : function(id) {
		return 'params' + id + '-icons';
	},

	/**
	 * Get property thumb icon img source.
	 * 
	 * @param id
	 */
	getIconThumbSrc : function(id) {
		return document.getElementById(this.getIconThumbId(id)).src;
	},

	/**
	 * Set property thumb icon img source.
	 * 
	 * @param id
	 * @param src
	 */
	setIconThumbSrc : function(id, icon) {
		var el = document.getElementById(this.getIconThumbId(id));
		var src = icon != -1 ? TmpIconsThumbs[icon] : '';
		el.src = src;
		el.style.display = src ? 'block' : 'none';
	},

	/**
	 * Get property icon orig id.
	 * 
	 * @param id
	 */
	getIconOrigId : function(id) {
		return 'params' + id + '-icons-orig';
	},

	/**
	 * Get property icon orig source.
	 * 
	 * @param id
	 */
	getIconOrigSrc : function(id) {
		return document.getElementById(this.getIconOrigId(id)).value;
	},

	/**
	 * Set property icon orig source.
	 * 
	 * @param id
	 * @param src
	 */
	setIconOrigSrc : function(id, icon) {
		document.getElementById(this.getIconOrigId(id)).value = icon != -1 ? TmpIconsReal[icon] : '';
	},

	/**
	 * Set output with property overwrite for saving on server into hidden param
	 * on page.
	 * 
	 * @param id
	 * @param output
	 * @param icon
	 */
	setOutput : function(id, output, icon) {
		/* get id of hidden param */
		var eid = this.getOutputId(id);
		/* search for existing */
		var hidden = document.getElementById(eid);
		if (!hidden) { /* no exist - new property */
			/* create new */
			hidden = document.createElement('input');
			hidden.type = 'hidden';
			hidden.id = eid;
			hidden.name = 'params-output[' + id + ']';
		}
		/* add output in format like: TITLE;SEARCHABLE;FILTERABLE;TYPE;OPTIONS */
		var value = new Array();
		value.push(ATemplate.safeString(this.getEditTitle()));
		value.push(this.isSetSearchable() ? '1' : '0');
		value.push(this.isSetFilterable() ? '1' : '0');
		value.push(this.isSetObjects() ? '1' : '0');
		value.push(this.isSetObject() ? '1' : '0');
		value.push(icon != -1 ? TmpIconsReal[icon] : '0');
		if (output != '') {
			value.push(output);
		}
		 
		value = value.join('|');
		/* set values */
		hidden.value = value;
		var adminForm = this.getAdminForm();
		/* add into main form */
		adminForm.appendChild(hidden);
	},

	/**
	 * Remove output if delete property.
	 * 
	 * @param id
	 */
	unsetOutput : function(id) {
		var eid = this.getOutputId(id);
		var hidden = document.getElementById(eid);
		if (hidden) {
			var adminForm = this.getAdminForm();
			/* remove hidden param from main form */
			adminForm.removeChild(hidden);
		}
	},

	/**
	 * Get output hidden param ID
	 */
	getOutputId : function(id) {
		return 'params' + id + '-output';
	},

	/**
	 * Get main form.
	 */
	getAdminForm : function() {
		return document.getElementById('adminForm');
	},

	/**
	 * Create new row with property in page table.
	 * 
	 * @param id
	 * @param property
	 *            new property element
	 */
	build : function(id, property) {
		/* new table row for property */
		var row = document.createElement('tr');
		row.id = this.getRowId(id);

		/* cell with checkbox */
		var cell1 = document.createElement('td');
		cell1.className = 'check';
		/* create checkbox and set params values */
		var input = document.createElement('input');
		input.type = 'checkbox';
		input.name = 'cid[]';
		input.id = this.getCheckId(id);
		input.value = id;
		input.className = 'inputCheckbox';
		/* add checkbox into cell */
		cell1.appendChild(input);
		/* add cell into row */
		row.appendChild(cell1);

		var cell5 = document.createElement('td'); // cell with drop & drag
        var dropAndDrag = document.createElement('span');  // drop & drag handler
        dropAndDrag.className = 'drop-and-drag';  
		cell5.appendChild(dropAndDrag); // apend handler into cell
        var ordering = document.createElement('input'); 
        ordering.type = 'hidden'; 
        ordering.name = 'params-ordering[' + id + ']'; // ordering hidden field
		cell5.appendChild(ordering); // append arrows and input into cell
		row.appendChild(cell5); // append cell into row
		
		/* cell with property label */
		var cell2 = document.createElement('td');
		cell2.className = 'label';

		/* property label element */
		var label = document.createElement('label');
		label.id = this.getTitleId(id);
		label.innerHTML = this.getEditTitle();

		/* add label into cell */
		cell2.appendChild(label);

		/* add cell into row */
		row.appendChild(cell2);

		/* cell contain property element */
		var cell3 = document.createElement('td');
		cell3.id = this.getValueCellId(id);

		/* if property have more elements (radio buttons) - is in array */
		if (typeOf(property[0]) == 'array') {
			/* add all property elements into cell */
			for ( var i = 0; i < property[0].length; i++) {
				cell3.appendChild(property[0][i]);
				this.appendNewLine(cell3);
			}
		} else { /* one element property */
			cell3.appendChild(property[0]);
			this.appendNewLine(cell3);
		}
		/* add cell with property element(s) into row */
		row.appendChild(cell3);

		/* cell contain property icon */
		var cell5 = document.createElement('td');
		
		var icon = this.getSelectIcon();
		
		var img = document.createElement('img');
		var hidden = document.createElement('hidden');
		
		img.id = this.getIconThumbId(id);
		
		cell5.appendChild(img);
		cell5.appendChild(hidden);
		
		hidden.setAttribute('name', 'params-icons-orig[]');
		hidden.id = this.getIconOrigId(id);
		
		if (icon != -1) {
			img.src = TmpIconsThumbs[icon];
			hidden.setAttribute('value', TmpIconsReal[icon]);
		} else {
			img.style.display = 'none';
		}
		
		/* add icon cell into row */
		row.appendChild(cell5);
		
		/* cell contain toolbar icons */
		var cell4 = document.createElement('td');
		cell4.id = this.getToolbarId(id);

		/* add image button to config property */
		cell4.appendChild(this.getImage(TmpImgConfig, LGConfig, 'ATemplate.config(' + id + ')', 'tool', 0));

		this.appendNewLine(cell4);

		/* add image button to remove property */
		cell4.appendChild(this.getImage(TmpImgTrash, LGTrash, 'ATemplate.trash(' + id + ',true)', 'tool', 0));

		/* if user sign property like searchable add info icon */
		if (this.isSetSearchable()) {
			cell4.appendChild(this.getImgSearchable(id));
		} else { /* if no add empty div */
			this.addEmptyDiv(cell4, this.getSearchableId(id));
		}

		/* if user sign property like filterable add info icon */
		if (this.isSetFilterable()) {
			cell4.appendChild(this.getImgFilterable(id));
		} else { /* if no add empty div */
			this.addEmptyDiv(cell4, this.getFilterableId(id));
		}
		
		if (this.isSetObjects()) {
			cell4.appendChild(this.getImgObjects(id));
		} else {
			this.addEmptyDiv(cell4, this.getObjectsId(id));
		}
		if (this.isSetObject()) {
			cell4.appendChild(this.getImgObject(id));
		} else {
			this.addEmptyDiv(cell4, this.getObjectId(id));
		}

		/* add toolbar cell into row */
		row.appendChild(cell4);

		/* save output data into main form */
		this.setOutput(id, property[1], icon);
		/* get property table */
		var paramlist = this.getParamList();
		/* add row into property table */
		paramlist.appendChild(row);
        bookingTemplateSortables.addItems(document.id(row));
        ATemplate.updateOrdering();
	},

	/**
	 * Add emty div into element
	 * 
	 * @param obj
	 *            parent element to add
	 * @param id
	 */
	addEmptyDiv : function(obj, id) {
		var div = document.createElement('div');
		div.className = 'emptyIcon';
		div.id = id;
		obj.appendChild(div);
	},

	/**
	 * Get id of property row
	 * 
	 * @param id
	 */
	getRowId : function(id) {
		return 'params' + id + '-row';
	},

	/**
	 * Get id of property checkbox
	 */
	getCheckId : function(id) {
		return 'params' + id + '-check';
	},

	/**
	 * Get properties table element
	 */
	getParamList : function() {
		return document.getElementById('paramlist');
	},

	/**
	 * Remove one or more properties
	 * 
	 * @param id
	 *            removed property id
	 * @param confirming
	 *            if before removing user must confirm operation by child window
	 */
	trash : function(id, confirming) {
		/* if user confirm operation or no need confirming */
		if ((confirming && confirm(LGAreYouSure)) || !confirming) {
			/* remove more elements if have check */
			if (id == 'all') {
				/* get all page properties */
				var cids = this.getCids();
				for ( var i = 0; i < cids.length; i++) {
					if (cids[i].checked) { /*
											 * if element checked go to trash
											 * function with id
											 */
						this.trash(cids[i].value, false);
					}
				}
			} else { /* in function is id of concrete property */
				/* get properties table */
				var paramlist = this.getParamList();
				/* get property row */
				var row = document.getElementById(this.getRowId(id));
				/* remove property row from properties table */
                var isEditor = tinymce.get(this.getParamsId(id));
                if (isEditor) {
                    isEditor.remove();
                }
				paramlist.removeChild(row);
				/* remove property output */
				this.unsetOutput(id);
			}
		}
	},

	/**
	 * Get image element with onclick event.
	 * 
	 * @param src
	 *            image source file (URL)
	 * @param alt
	 *            alternative text sign if source file no available
	 * @param onclick
	 *            onclick event function
	 * @param className
	 *            CSS style class name
	 * @param element
	 *            ID
	 */
	getImage : function(src, alt, onclick, className, id) {
		var img = document.createElement('img');
		img.src = src;
		img.alt = alt;
		/* if event function not empty add into element params */
		if (onclick != '') {
			img.setAttribute('onclick', onclick);
		}
		/* if id not empty add into element params */
		if (id) {
			img.id = id;
		}
		img.className = className;
		return img;
	},

	/**
	 * Get last property ID. On page is saved last used property ID. If create
	 * new property this ID is incremented and returned.
	 * 
	 * @param increment
	 *            if need ID increment before returning (to new property)
	 */
	getId : function(incrementExists, increment) {
		if (incrementExists) {
			TmpId++;
			return TmpId;
		} else {
			if (increment) {
				var value = TmpId;
				value++;
				return value;
			} else {
				return TmpId;
			}
		}
	},

	/**
	 * Get checkbox elements of all properties on page in array.
	 */
	getCids : function() {
		/* last property id */
		var id = this.getId(false, false);
		/* array to save checkbox elements */
		var output = new Array();
		for ( var i = 0; i <= id; i++) {
			/* get property checkbox element */
			var check = document.getElementById(this.getCheckId(i));
			if (check) { /* if exists add */
				output.push(check);
			}
		}
		return output;
	},

	/**
	 * Set window size.
	 * 
	 * @param width
	 *            in pixels
	 * @param height
	 *            in pixels
	 */
	resize : function(width, height) {
		SqueezeBox.options.size = {
			x : width,
			y : height
		};
	},

	/**
	 * Display window with text content.
	 * 
	 * @param content
	 *            text content of window (HTML code)
	 */
	display : function(content) {
		SqueezeBox.setContent('string', content);
	},

	/**
	 * Close window.
	 */
	close : function() {
		SqueezeBox.close();
	},

	/**
	 * Add new line into element HTML content.
	 * 
	 * @param obj
	 *            element to add
	 */
	appendNewLine : function(obj) {
		obj.innerHTML += "\n";
	},

	/**
	 * Get ID of searchable info icon or empty div on page.
	 * 
	 * @param id
	 */
	getSearchableId : function(id) {
		return 'icon-search-' + id;
	},

	/**
	 * Get element of searchable info icon or empty div.
	 * 
	 * @param id
	 */
	getIconSearchable : function(id) {
		var icon = document.getElementById(this.getSearchableId(id));
		return icon ? icon : null;
	},

	/**
	 * Get searchable info icon new IMG element.
	 * 
	 * @param id
	 */
	getImgSearchable : function(id) {
		return this.getImage(TmpImgSearch, LGSearchable, '', 'icon', this.getSearchableId(id));
	},

	/**
	 * Get info if user on editing window set property as searchable.
	 */
	isSetSearchable : function() {
		return document.paramsWindow.searchable.checked;
	},

	/**
	 * Remove searchable info icon or empty div from page.
	 * 
	 * @param id
	 * @param parent
	 */
	removeSearchable : function(id, parent) {
		var element = document.getElementById(this.getSearchableId(id));
		if (element) {
			parent.removeChild(element);
		}
	},

	/**
	 * Get ID of filterable info icon or empty div on page.
	 * 
	 * @param id
	 */
	getFilterableId : function(id) {
		return 'icon-filter-' + id;
	},

	/**
	 * Get element of filterable info icon or empty div.
	 * 
	 * @param id
	 */
	getIconFilterable : function(id) {
		var icon = document.getElementById(this.getFilterableId(id));
		return icon ? icon : null;
	},

	/**
	 * Get filterable info icon new IMG element.
	 * 
	 * @param id
	 */
	getImgFilterable : function(id) {
		return this.getImage(TmpImgFilter, LGFilterable, '', 'icon', this.getFilterableId(id));
	},

	/**
	 * Get info if user on editing window set property as filterable.
	 */
	isSetFilterable : function() {
		return document.paramsWindow.filterable.checked;
	},

	/**
	 * Remove filterable info icon or empty div from page.
	 * 
	 * @param id
	 * @param parent
	 */
	removeFilterable : function(id, parent) {
		var element = document.getElementById(this.getFilterableId(id));
		if (element) {
			parent.removeChild(element);
		}
	},
	
	/**
	 * Get ID of objects info icon or empty div on page.
	 * 
	 * @param id
	 */
	getObjectsId : function(id) {
		return 'icon-objects-' + id;
	},

	/**
	 * Get element of objects info icon or empty div.
	 * 
	 * @param id
	 */
	getIconObjects : function(id) {
		var icon = document.getElementById(this.getObjectsId(id));
		return icon ? icon : null;
	},

	/**
	 * Get display info icon new IMG element.
	 * 
	 * @param id
	 */
	getImgObjects : function(id) {
		return this.getImage(TmpImgObjects, LGObjects, '', 'icon', this.getObjectsId(id));
	},

	/**
	 * Get info if user on editing window set property as used on object's list.
	 */
	isSetObjects : function() {
		return document.paramsWindow.objects.checked;
	},

	/**
	 * Remove objects info icon or empty div from page.
	 * 
	 * @param id
	 * @param parent
	 */
	removeObjects : function(id, parent) {
		var element = document.getElementById(this.getObjectsId(id));
		if (element) {
			parent.removeChild(element);
		}
	},
	
	/**
	 * Get ID of object info icon or empty div on page.
	 * 
	 * @param id
	 */
	getObjectId : function(id) {
		return 'icon-object-' + id;
	},

	/**
	 * Get element of object info icon or empty div.
	 * 
	 * @param id
	 */
	getIconObject : function(id) {
		var icon = document.getElementById(this.getObjectId(id));
		return icon ? icon : null;
	},

	/**
	 * Get object info icon new IMG element.
	 * 
	 * @param id
	 */
	getImgObject : function(id) {
		return this.getImage(TmpImgObject, LGObject, '', 'icon', this.getObjectId(id));
	},

	/**
	 * Get info if user on editing window set property as used on object detail.
	 */
	isSetObject : function() {
		return document.paramsWindow.object.checked;
	},

	/**
	 * Remove object info icon or empty div from page.
	 * 
	 * @param id
	 * @param parent
	 */
	removeObject : function(id, parent) {
		var element = document.getElementById(this.getObjectId(id));
		if (element) {
			parent.removeChild(element);
		}
	},
	
	/**
	 * Get ID of toolbar property cell.
	 * 
	 * @param id
	 */
	getToolbarId : function(id) {
		return 'params' + id + '-toolbar';
	},

	/**
	 * Get HTML safe string.
	 * 
	 * @param string
	 */
	safeString : function(string) {
		string = string.replace(/'/gi, '&#39;');
		string = string.replace(/"/gi, '&quot;');
		string = string.replace(/\|/gi, '&#166;');
		return string;
	},

	/**
	 * Set property icon.
	 * 
	 * @param id
	 */
	setIcon : function(id) {
		for ( var i = 0; i < TmpIconsReal.length; i++) {
			var e = document.getElementById('icon' + i);
			if (e.className != this.getSelectIconClass() && id == i) {
				e.className = this.getSelectIconClass();
			} else {
				e.className = this.getUnselectIconClassname();
			}
		}
	},

	/**
	 * Get select icon.
	 */
	getSelectIcon : function() {
		for ( var i = 0; i < TmpIconsReal.length; i++) {
			if (document.getElementById('icon' + i).className == this.getSelectIconClass()) {
				return i;
			}
		}
		return -1;
	},

	/**
	 * Get select icon HTML class name.
	 */
	getSelectIconClass : function() {
		return this.getUnselectIconClassname() + ' tmpIconSet';
	},

	/**
	 * Get unselect icon HTML class name.
	 */
	getUnselectIconClassname : function() {
		return 'tmpIcon';
	},
    
    updateOrdering : function() {
        var ordering = document.getElements('input[name^=params-ordering]');
        for (var i = 0; i < ordering.length; i++) {
            ordering[i].value = i;
        }
    }
}

var bookingTemplateSortables;
window.addEvent('domready', function() {     
    bookingTemplateSortables = new Sortables(document.id('paramlist'), {
        onComplete: function() {
            ATemplate.updateOrdering();
        }
    });
} );