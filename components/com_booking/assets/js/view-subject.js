/**
 * Javascript for edit subject form
 * 
 * @version $Id$
 * @package ARTIO Booking
 * @subpackage assets
 * @copyright Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link http://www.artio.net Official website
 */

var EditSubject = {

	usePricing: 0,		
    sortables: {
        supplements: null
    },
		
	/**
	 * Reset subject hits counter
	 * 
	 * @param object
	 *            edit form element
	 * @return boolean false to disable form submit
	 */
	resetHits : function() {
		form = ACommon.getForm();
		if (confirm(LGAreYouSure)) {
			form.hits.value = 0;
			form.hits_disabled.value = 0;
		}
		return false;
	},

	/**
	 * Open dialog for save subject as new template
	 * 
	 * @param object
	 *            edit form element
	 * @return boolean false to disable form submit
	 */
	openSaveAsNewTemplate : function() {
		this.setSaveAsNewTemplate('open', 'saveAsNew');
	},

	/**
	 * Storno dialog for save subject as new template
	 * 
	 * @param object
	 *            edit form element
	 * @return boolean false to disable form submit
	 */
	closeSaveAsNewTemplate : function() {
		this.setSaveAsNewTemplate('close', '');
	},

	/**
	 * Set dialog for save subject as new template
	 * 
	 * @param object
	 *            edit form element
	 * @param string
	 *            way set type: 'open' .. open dialog, 'close' .. close dialog
	 * @return boolean false to disable form submit
	 */
	setSaveAsNewTemplate : function(way, value) {
		this.setTemplateTask(value);
		document.getElementById('saveAsNewTemplate').style.display = way == 'open' ? 'inline'
				: 'none';
		document.getElementById('templateName').style.display = way == 'open' ? 'none'
				: '';
		return false;
	},

	/**
	 * Return true if dialog for save subject as new template is open.
	 */
	isSetSaveAsNewTemplate : function() {
		return document.getElementById('saveAsNewTemplate').style.display == 'inline';
	},

	/**
	 * Open dialog for rename subject template
	 * 
	 * @param object
	 *            edit form element
	 * @return boolean false to disable form submit
	 */
	openRenameTemplate : function() {
		this.setRenameTemplate('open', 'rename');
	},

	/**
	 * Storno dialog for rename subject template
	 * 
	 * @param object
	 *            edit form element
	 * @return boolean false to disable form submit
	 */
	closeRenameTemplate : function() {
		this.setRenameTemplate('close', '');
	},

	/**
	 * Set dialog for save subject as new template
	 * 
	 * @param object
	 *            edit form element
	 * @param string
	 *            way set type: 'open' .. open dialog, 'close' .. close dialog
	 * @return boolean false to disable form submit
	 */
	setRenameTemplate : function(way, value) {
		this.setTemplateTask(value);
		document.getElementById('renameTemplate').style.display = way == 'open' ? 'inline'
				: 'none';
		document.getElementById('templateName').style.display = way == 'open' ? 'none'
				: '';
		return false;
	},

	/**
	 * Return true if dialog for rename subject is open.
	 */
	isSetRenameTemplate : function() {
		return document.getElementById('renameTemplate').style.display == 'inline';
	},

	/**
	 * Set text field content. Remove mask value.
	 * 
	 * @param object
	 *            edit form element
	 * @param string
	 *            mask text mask value
	 */
	setTemplateNameContent : function() {
		form = ACommon.getForm();
		if (form.new_template_name.value == TemplateNameMask) {
			form.new_template_name.value = '';
		}
	},

	/**
	 * Open dialog for change subject template
	 * 
	 * @param object
	 *            edit form element
	 * @return boolean false to disable form submit
	 */
	openChangeTemplate : function() {
		return this.setChangeTemplate('open', 'changeTemplate');
	},

	/**
	 * Close dialog for change subject template
	 * 
	 * @param object
	 *            edit form element
	 * @return boolean false to disable form submit
	 */
	closeChangeTemplate : function() {
		this.setChangeTemplate('close', '');
	},

	/**
	 * Set dialog for change subject template
	 * 
	 * @param object
	 *            edit form element
	 * @param string
	 *            way set type: 'open' .. open dialog, 'close' .. close dialog
	 * @return boolean false to disable form submit
	 */
	setChangeTemplate : function(way, value) {
		this.setTemplateTask(value);
		document.getElementById('changeTemplate').style.display = way == 'open' ? 'inline'
				: 'none';
		document.getElementById('templateName').style.display = way == 'open' ? 'none'
				: '';
		return false;
	},

	/**
	 * Open dialog for delete subject template
	 * 
	 * @param object
	 *            edit form element
	 * @return boolean false to disable form submit
	 */
	openDeleteTemplate : function() {
		this.setDeleteTemplate('open', 'deleteTemplate');
	},

	/**
	 * Close dialog for delete subject template
	 * 
	 * @param object
	 *            edit form element
	 * @return boolean false to disable form submit
	 */
	closeDeleteTemplate : function() {
		this.setDeleteTemplate('close', '');
	},

	/**
	 * Set dialog for delete subject template
	 * 
	 * @param object
	 *            edit form element
	 * @param string
	 *            way set type: 'open' .. open dialog, 'close' .. close dialog
	 * @return boolean false to disable form submit
	 */
	setDeleteTemplate : function(way, value) {
		this.setTemplateTask(value);
		document.getElementById('deleteTemplate').style.display = way == 'open' ? 'inline'
				: 'none';
		document.getElementById('templateName').style.display = way == 'open' ? 'none'
				: '';
		return false;
	},

	/**
	 * Submit form for delete template.
	 * 
	 * @param object
	 *            edit form element
	 * @return boolean false to disable form submit
	 */
	deleteTemplate : function() {
		form = ACommon.getForm();
		if (confirm(LGAreYouSure)) {
			submitform('deleteTemplate');
		}
		return false;
	},

	/**
	 * Submit form for change template.
	 * 
	 * @param object
	 *            edit form element
	 * @return boolean false to disable form submit
	 */
	changeTemplate : function() {
		form = ACommon.getForm();
		if (form.template.value == '0') {
			alert(LGErrAddSubjectTemplate);
		} else if (confirm(LGAreYouSure)) {
			submitform('changeTemplate');
		}
		return false;
	},

	/**
	 * Set template task input hidden value
	 * 
	 * @param object
	 *            edit form element
	 * @param value
	 *            to set
	 */
	setTemplateTask : function(value) {
		form = ACommon.getForm();
		form.templateTask.value = value;
	},

	/**
	 * Add rezervation type row.
	 */
	addRtype : function() {
		/* copy default mask row */
		var clone = document.id('rtype').clone().inject('rtypes');
		/* set as visible */
		clone.style.display = '';
		/* remove element ID - no duplicity */
		clone.removeProperty('id');
		/* get all chidrens as elements array */
		var children = clone.getChildren();
		/* append and setup time pickers */
		var element = clone.getElement('select[name^=rtype-type]');
		element.className = 'notify';
		this.setReservationType(element);
		this.setEmptyRtype(false);
	},

	/**
	 * Set visibilaty of empty row of reservation types with information about
	 * creating reservation types.
	 */
	setEmptyRtype : function(display) {
		document.getElementById('rtype-empty').style.display = display ? ''
				: 'none';
	},

	/**
	 * Prepare saved reservation type displaying.
	 */
	prepareReservationTypes : function(allEnable) {
		if (document.id('rtypes')) {
			var elements = document.id('rtypes').getElements('select[name^=rtype-type]');
			for ( var i = 1; i < elements.length; i++) {
				this.setReservationType(elements[i], allEnable);
			}
		}        
        var i = 0;
        document.getElements('*[name^=rtype-fix_from]').each(function(e) {            
            if (e.get('name') == 'rtype-fix_from[0][fix_from_start]')
                e.set('name', 'rtype-fix_from[' + i + '][fix_from_start]');
            if (e.get('name') == 'rtype-fix_from[0][]')
                e.set('name', 'rtype-fix_from[' + (i --) + '][]');
        });
	},

	/**
	 * Remove rezervation types rows.
	 */
	removeRtypes : function() {
		var deletedAll = ACommon.removeRows('rtypes', 'rcid');
		if (deletedAll) {
			this.setEmptyRtype(true);
		}
	},

	setReservationType : function(element, allEnable) {
		var cell = document.id(element).getParent();
		var row = cell.getParent();

		var title = document.id(row).getElement('input[name^=rtype-title]');
		var description = document.id(row)
				.getElement('textarea[name^=rtype-description]');
		var timeUnit = document.id(row).getElement('input[name^=rtype-time_unit]');
		var gapTime = document.id(row).getElement('input[name^=rtype-gap_time]');
		
		var min = document.id(row).getElement('input[name^=rtype-min]');
		var max = document.id(row).getElement('input[name^=rtype-max]');
		var fix = document.id(row).getElement('input[name^=rtype-fix]');
		var fixFrom = document.id(row).getElement('select[name^=rtype-fix_from]');
		var fixPast = document.id(row).getElement('input[name^=rtype-book_fix_past][type=checkbox]');
		
		var rtypeValue = allEnable ? '1' : element.value;

		var textDisabled = true;
		var timeDisabled = true;
		var limitDisabled = true;
		var className = 'notify';
		var fixFromDisabled = null;

		switch (rtypeValue) {
		case '1':
			/* hourly */
			textDisabled = false;
			timeDisabled = false;
			className = '';
			limitDisabled = false;
			fixFromDisabled = !allEnable;
			break;
		case '2':
			/* daily */
			textDisabled = false;
			className = '';
			limitDisabled = false;
			fixFromDisabled = false;
			break;
		}

		element.className = className;

		title.disabled = description.disabled = textDisabled;
		min.disabled = max.disabled = fix.disabled = fixFrom.disabled = limitDisabled;
		fixFrom.disabled = fixPast.disabled = fixFromDisabled;
		timeUnit.disabled = gapTime.disabled = timeDisabled;
		
		if (textDisabled)
			title.value = description.value = '';
		
		if (limitDisabled)
			min.value = max.value = fix.value = '';

		if (timeDisabled) 
			timeUnit.value = gapTime.value = '';
		
		if (fixFromDisabled) {
			fixFrom.value = 'any';
			fixPast.checked = false;
		}
	},
	
	/**
	 * disable/enable classic gap time
	 */
	setReservationGapType : function(element) {
		var cell = document.id(element).getParent();
		var row = cell.getParent();
		var timeUnit = document.id(row).getElement('input[name^=rtype-time_unit]');
		var gapTime = document.id(row).getElement('input[name^=rtype-gap_time]');
		
		if(element.checked)
		{
			gapTime.value = timeUnit.value;
			gapTime.disabled = true;
		}
		else
		{
			gapTime.disabled = false;
		}
	},

	/**
	 * Add price row.
	 */
	addPrice : function() {
		/* copy default mask row */
		var clone1 = document.id('price1').clone().inject('tprices');
		var clone2 = document.id('price2').clone().inject('tprices');
		/* set as visible */
		clone1.style.display = clone2.style.display = '';
		/* remove element ID - no duplicity */
		clone1.removeProperty('id');
		clone2.removeProperty('id');
		/* get all chidrens as elements array */
		var children = clone1.getChildren();
		/* append and setup calendars */
		var dates = clone1.getElements('td[class=date]');
		var divs1 = dates[0].getElements('div');
		var divs2 = dates[1].getElements('div');
		ACommon.createCalendar(divs1[0], 'priceDateUp#', 'price-date_up[]');
		ACommon.createCalendar(divs2[0], 'priceDateDown#', 'price-date_down[]');
		/* append and setup time pickers */
		ACommon.createTimePicker(divs1[2], 'price-time_up[]', true);
		ACommon.createTimePicker(divs2[2], 'price-time_down[]', true);
		/* prepare new row inputs */
		var element = clone1.getElement('select[name^=price-rezervation_type]');
		element.className = 'notify';
		this.setPriceReservationType(element);
		this.setEmptyPrice(false);
		clone1.getElement('input[name^=price-custom_color]').color = false; // this property disable field init with jscolor
		jscolor.init(); // init color picker
		
		var oid = ACommon.getNextId('input[name^=price-occupancy_price_modifier]');
		var pod = clone1.getElements('input[name^=price-occupancy_price_modifier]');
		
		for (var i = 0; i < pod.length; i++)
			pod[i].set('name', pod[i].get('name').replace('[]', '[' + oid + ']'));
	},

	/**
	 * Set visibilaty of empty row of prices with information about creating
	 * prices.
	 */
	setEmptyPrice : function(display) {
		document.getElementById('price-empty').style.display = display ? ''
				: 'none';
	},

	/**
	 * Prepare saved prices displaying.
	 */
	preparePrices : function(allEnable) {
		if (document.getElementById('tprices')) {
			var elements = document.id('tprices').getElements(
					'select[name^=price-rezervation_type]');
			for ( var i = 1; i < elements.length; i++) {
				this.setPriceReservationType(elements[i], allEnable);
			}
		}
	},

	/**
	 * Remove prices rows.
	 */
	removePrices : function() {
		var deletedAll = ACommon.removeRows('tprices', 'pcid', true);
		if (deletedAll) {
			this.setEmptyPrice(true);
		}
	},

	setPriceReservationType : function(element, allEnable) {
		var row = document.id(element).getParent('tr');
		var row2 = row.getNext();

		var value = row.getElement('input[name^=price-value]');
		var deposit = row.getElement('input[name^=price-deposit]');

		var rtype = document.getElementById('rtype-type' + element.value);

		var dateUp = row.getElement('input[name^=price-date_up]');
		var dateDown = row.getElement('input[name^=price-date_down]');

		var timeUp = row.getElement('input[name^=price-time_up]');
		var timeDown = row.getElement('input[name^=price-time_down]');
		
		var timeRange = row2.getElement('select[name^=price-time_range]');
		
		var headPiece = row2.getElement('input[name^=price-head_piece]');
		var tailPiece = row2.getElement('input[name^=price-tail_piece]');
		
		var timePickerMidnight = row.getElements('input[class=timePickerMidnight]');
		var timePickerDiv = row.getElements('div[class=picker]');

		var days = row2.getElements('input[name^=fake]');

		var rtypeValue = rtype ? rtype.value : false;

		if (allEnable) {
			rtypeValue = '1';
		}

		var className = 'notify';

		var priceDisabled = true;
		var dateDisabled = true;
		var timeDisabled = true;

		switch (rtypeValue) {
		case '1':
			/* hourly */
			priceDisabled = false;
			dateDisabled = false;
			timeDisabled = false;
			className = '';
			break;
		case '2':
			/* daily */
			priceDisabled = false;
			dateDisabled = false;
			className = '';
			break;
		}

		element.className = className;

		if (value)
			value.disabled = priceDisabled;
		if (deposit)
			deposit.disabled = priceDisabled;

		if (priceDisabled) {
			if (value)
				value.value = '';
			if (deposit)
				deposit.value = '';
		}

		dateUp.disabled = dateDisabled;
		dateDown.disabled = dateDisabled;

		if (dateDisabled) {
			dateUp.value = '';
			dateDown.value = '';
		}

		timeUp.disabled = timeDisabled;
		timeDown.disabled = timeDisabled;
		timeRange.disabled = timeDisabled;
		headPiece.disabled = timeDisabled;
		tailPiece.disabled = timeDisabled;

		if (timeDisabled) {
			timeUp.value = '';
			timeDown.value = '';
			headPiece.value = '';
			tailPiece.value = '';
			timePickerMidnight.each(function(e) {
				e.disabled = true;
				e.checked = false;
			});
			timePickerDiv.each(function(e) {
				e.show();
			});
		} else 
			timePickerMidnight.each(function(e) {
				e.disabled = false;
			});

		for ( var i = 0; i < days.length; i++) {
			if (!allEnable) {
				days[i].disabled = dateDisabled;
				ACommon.check(days[i]);
			}
		}
	},
	setPaymentExpirationType : function(element) {
		var row = document.id(element).getParent();
		var value = row.getElement('input[name^=price-cancel_time]');
		var value2 = row.getElement('select[name^=price-expiration_format]');

		if(document.id(element).getSelected().get("value") < 2)
		{
			if (value)
				value.disabled = true;
			if (value2)
				value2.disabled = true;
		}
		else
		{
			if (value)
				value.disabled = false;
			if (value2)
				value2.disabled = false;
		}
	},
	checkPriceRowDays : function(element) {
		var row = document.id(element).getParent().getParent();
		var fakes = row.getElements('input[name^=fake]');
		var check = this.fakesCheck(element);
		for ( var i = 0; i < fakes.length; i++) {
			ACommon.setCheck(fakes[i], check);
		}
		this.setCheck(element);
	},
	checkPriceColDays : function(element, day) {
		var fakes = this.getFakes();
		var check = this.fakesCheck(element);
		for ( var i = (6 + day); i < fakes.length; i += 7) {
			ACommon.setCheck(fakes[i], check);
		}
		this.setCheck(element);
	},
	checkPriceAllDays : function(element) {
		var fakes = this.getFakes();
		var check = this.fakesCheck(element);
		for ( var i = 7; i < fakes.length; i++) {
			ACommon.setCheck(fakes[i], check);
		}
		this.setCheck(element);
	},
	getFakes : function() {
		return document.id('tprices').getElements('input[name^=fake]');
	},
	fakesCheck : function(element) {
		return element.className == 'checkall';
	},
	setCheck : function(element) {
		element.className = ((element.className == 'checkall') ? 'uncheckall'
				: 'checkall');
	},
	setRLimit : function() {
		if (!document.id('rlimit_set').checked)
			document.id('rlimit_count').value = document.id('rlimit_days').value = '';
		document.id('rlimit_box').style.display = document.id('rlimit_set').checked ? 'block'
				: 'none';
	},	

	/**
	 * Add supplement row.
	 */
	addSupplement : function() {
		/* copy default mask row */
		var clone = document.id('supplement').clone().inject('tsupplements');
		/* set as visible */
		clone.style.display = '';
		/* remove element ID - no duplicity */
		clone.removeProperty('id');
		/* get all chidrens as elements array */
		var children = clone.getChildren();
		/* prepare new row inptus */
		var element = clone.getElement('select[name^=supplements-type]');
		element.className = 'notify';
		var ttl = document.id('tsupplements').getElements('input[name^=supplements-title]');
		var max = 0;
		for (var j = 0; j < ttl.length; j++) { 
			var match = ttl[j].name.match(/(\d+)/); // search fo array index in field name 
			if (match != null) { var int = match[0].toInt(); if (int > max) max = int; } // save index if is the highest
		}
		var next = max + 1; // new index
		var match = clone.innerHTML.match(/(supplements\-\w+)\[\]/g); // all supplement form fields names
		for (var j = 0; j < match.length; j++) clone.getElement('*[name^=' + match[j] + ']').name = match[j].replace(/\[\]/, '['+next+']'); // update index
		this.setEmptySupplement(false);
		this.setSupplementType(element);
		var empty = document.id('supplement-empty'); 
        document.id('tsupplements').removeChild(empty); 
        empty.inject('tsupplements'); // move empty row at the end (cut and inject again)
        // make new item sortable
        if (this.sortables.supplements instanceof Sortables) {
            this.sortables.supplements.addItems(clone);
        }        
	},

	/**
	 * Set visibilaty of empty row of supplements with information about
	 * creating supplements.
	 */
	setEmptySupplement : function(display) {
		document.id('supplement-empty').style.display = display ? ''
				: 'none';
	},

	/**
	 * Remove supplements rows.
	 */
	removeSupplements : function() {
		if (ACommon.removeRows('tsupplements', 'scid'))
			this.setEmptySupplement(true);
	},

	setSupplementType : function(element, allEnable) {
		var row = document.id(element).getParent().getParent();
		var title = row.getElement('input[name^=supplements-title]');
		var description = row
				.getElement('textarea[name^=supplements-description]');
		var empty = row.getElement('input[name^=fake]');
		var options = row.getElement('textarea[name^=supplements-options]');
		var paid = row.getElement('select[name^=supplements-paid]');
		var price = row.getElement('textarea[name^=supplements-price]');
		if (element.value == '0')
			paid.value = '0';
		supplementType = allEnable == true ? '1' : element.value;
		this.setSupplementPaid(paid, allEnable);
		switch (supplementType) {
		case '1':
			/* list */
			price.disabled = title.disabled = description.disabled = options.disabled = empty.disabled = paid.disabled = false;
			element.className = '';
			break;
		case '2':
		case '3':
			/* yes/no or permanent */
			title.disabled = description.disabled = empty.checked = paid.disabled = false;
			price.disabled = options.disabled = empty.disabled = true;
			options.value = element.className = '';
			break;
		default:
			/* no select */
			price.disabled = title.disabled = description.disabled = options.disabled = paid.disabled = empty.disabled = true;
			empty.checked = false;
			title.value = description.value = options.value = '';
			element.className = 'notify';
			break;
		}
	},

	/**
	 * Sel supplement price input box.
	 * 
	 * @param element
	 * @param submit
	 */
	setSupplementPaid : function(element, submit) {
		var col = document.id(element).getParent();
		var price1 = col.getPrevious().getElement('textarea[name^=supplements-price]');
		var label1 = price1.getParent();
		var price2 = col.getElement('input[name^=supplements-price]');
		var label2 = price2.getParent();
		switch (element.value) {
		case '0':
			// free
			price1.style.display = price2.style.display = label1.style.display  = label2.style.display = 'none';
			price1.value = price2.value = '';
			if (submit == true)
				price1.dispose();
			break;
		case '1':
			// one price
			price2.style.display = 'block';
			label2.style.display = 'inline-block';
			price1.value = '';
			price1.style.display = label1.style.display  = 'none';
			if (submit == true)
				price1.dispose();
			break;
		case '2':
			// more prices
			price1.style.display = 'block';
			label1.style.display = 'inline-block';
			price2.value = '';
			price2.style.display = label2.style.display =  'none';
			if (submit == true)
				price2.dispose();
			break;
		}
	},

	/**
	 * Prepare saved supplements displaying.
	 */
	prepareSupplements : function(allEnable) {
		var elements = document.id('tsupplements').getElements(
				'select[name^=supplements-type]');
		for ( var i = 0; i < elements.length; i++) {
			this.setSupplementType(elements[i], allEnable);
		}
	},

	/**
	 * Valid form before submit. Standard in Joomla! administration.
	 * 
	 * @param pressbutton
	 *            button selected in toolbar
	 */

	submitbutton : function(pressbutton) {
		switch (pressbutton) {
		case 'cancel':
		case 'copy':
			submitform(pressbutton);
			return;
		case 'apply':
			ACommon.saveBookmark();
			break;
		}
		if (!ACommon.validDateTimeInterval('publishUp', 'publishDown', LGErrPublishIntervalInvalid)) {
			return false;
		}
		if (!ACommon.validMultiParam('input', 'rtype-title',
				LGErrAddReservationTypesTitles, true)) {
			return false;
		}
		if (!ACommon.validMultiParam('select', 'rtype-type',
				LGErrSelectsDefaultTypesReservationTypes, true)) {
			return false;
		}
		if (!ACommon.validMultiParam('input', 'rtype-time_unit',
				LGErrAddTimeUnit, true)) {
			return false;
		}
		var expire = document.getElements('select[name^=price-expiration_setting]');
		var cancel = document.getElements('input[name^=price-cancel_time]');
		for (var i = 1; i < expire.length; i++)
			if (expire[i].value > 1 && (cancel[i].value.trim() == '0' || cancel[i].value.trim() == '')) {
				alert(LGErrAddCancelTime);
				return false;
			}
		if (!ACommon.validMultiParam('input', 'price-value',
				LGErrAddPricesValues, true)) {
			return false;
		}
		if (!ACommon.validMultiParam('select', 'price-rezervation_type',
				LGErrSelectPricesReservationTypes, true)) {
			return false;
		}
		if (!ACommon.validMultiParam('input', 'price-date_up',
				LGErrAddPricesDates, true)
				|| !ACommon.validMultiParam('input', 'price-date_down',
						LGErrAddPricesDates, true)) {
			return false;
		}
		if (!ACommon.validDateTimeInterval('price-date_up', 'price-date_down', LGErrPriceDateIntervalInvalid, true)) {
			return false;
		}
		if (!ACommon.validMultiParam('input', 'price-time_up',
				LGErrAddPricesTimes, true)
				|| !ACommon.validMultiParam('input', 'price-time_down',
						LGErrAddPricesTimes, true)) {
			return false;
		}
		if (!ACommon.validMultiParam('input', 'supplements-title',
				LGErrAddSupplementsTitles, true)) {
			return false;
		}
		if (!ACommon.validMultiParam('select', 'supplements-type',
				LGErrSelectSupplementsTypes, true)) {
			return false;
		}
		if (!ACommon.validMultiParam('textarea', 'supplements-options',
				LGErrSelectSupplementsOptions, true)) {
			return false;
		}
		if (!ACommon.validMultiParam('textarea|input', 'supplements-price',
				LGErrAddSupplementsPrice, true)) {
			return false;
		}
		var form = ACommon.getForm();
		var cid = document.getElementById('cid').value;
		if (trim(form.title.value) == '') {
			alert(LGErrAddSubjectTitle);
		} else if (trim(form.total_capacity.value) == ''
				|| !AValidator.isInt(form.total_capacity.value)) {
			alert(LGErrTotalCapacityNoNumeric);
		} else if (form.minimum_capacity.value.toInt() > form.total_capacity.value.toInt()) { // minimum capacity has to be less then maximum capacity 
			alert(LGErrMinimumCapacityInvalid);
		} 
		
		else if ((form.standard_occupancy_min.value.trim() != '' && form.standard_occupancy_max.value.trim() == '') || (form.standard_occupancy_min.value.trim() == '' && form.standard_occupancy_max.value.trim() != '') || (form.extra_occupancy_min.value.trim() != '' && form.extra_occupancy_max.value.trim() == '') || (form.extra_occupancy_min.value.trim() == '' && form.extra_occupancy_max.value.trim() != '')) { // miminum and maximum occupancy value has to be set
			alert(LGErrOccupancyIntervalInvalid);
		} 		
		
		else if ((form.standard_occupancy_min.value.toInt() > form.standard_occupancy_max.value.toInt()) || (form.extra_occupancy_min.value.toInt() > form.extra_occupancy_max.value.toInt())) {  // occupancy minimum has to be less then maximum
			alert(LGErrOccupancyMinimumInvalid);
		} 
		
		else if ((form.standard_occupancy_max.value.trim().toInt() > 0 && document.getElements('input[name^=otype-type][value=0]').length == 0) || form.extra_occupancy_max.value.trim().toInt() > 0 && document.getElements('input[name^=otype-type][value=1]').length == 0) { // occupancy has to have some type 
			alert(LGErrOccupancyMissingType);
		} 
		
		else if ((this.isSetSaveAsNewTemplate() && (trim(form.new_template_name.value) == '' || trim(form.new_template_name.value) == TemplateNameMask))
				|| ((this.isSetRenameTemplate() && trim(form.template_rename.value) == ''))) {
			alert(LGErrAddSubjectTemplate);
		} else if (!this.isSetSaveAsNewTemplate() && form.template.value == '0') {
			alert(LGErrAddSubjectTemplate);
		} else if (cid != '0' && cid == form.parent.value) {
			alert(LGErrSelfAsParent);
		} else if (document.id('rlimit_set').checked
				&& (document.id('rlimit_count').value == '' || document.id('rlimit_days').value == '')) {
			alert(LGErrAddRLimit);
		} else if (document.id('use_fix_shedule').checked && (trim(form.shedule_from.value) == '' || trim(form.shedule_to.value) == '')) { 
			alert(LGErrAddFixedScheduleFromTo);
		} else if (document.id('night_booking').checked && (trim(form.night_booking_from.value) == '' || trim(form.night_booking_to.value) == '')) { 
			alert(LGErrAddNightsBookingCheckInCheckOut);
		} else {
			this.preparePrices(true);
			this.prepareReservationTypes(true);
			this.prepareSupplements(true);
			this.prepareDiscountContainer();
			Joomla.submitform(pressbutton, document.getElementById('adminForm'));
		}
	},
	
	/**
	 * Prepare supplements ordering on item edit page.
	 */
	prepareSupplementsOrdering : function(){
		if (document.id('tsupplements')) {
			this.sortables.supplements = new Sortables(document.id('tsupplements'), {
                onComplete: function() {
                    document.getElements('#tsupplements *[name^=supplements-ordering]').each(function(e, i) {
                        e.value = i;
                    });
                },
                handle: '.drop-and-drag'
            });
        }
	},		
	
	prepareGoogleMaps : function() {
		document.id('google_maps').addEvent('change', function() { EditSubject.prepareGoogleMaps(); } );
		switch (document.id('google_maps').value){
			default:
			case 'off':
				document.id('google_maps_address').disabled = true;
				document.id('google_maps_display').disabled = true;
				document.id('google_maps_width').disabled = true;
				document.id('google_maps_heigth').disabled = true;
				document.id('google_maps_zoom').disabled = true;
				document.id('google_maps_code').disabled = true;
				break;
			case 'address':
				document.id('google_maps_address').disabled = false;
				document.id('google_maps_display').disabled = false;
				document.id('google_maps_width').disabled = false;
				document.id('google_maps_heigth').disabled = false;
				document.id('google_maps_zoom').disabled = false;
				document.id('google_maps_code').disabled = true;
				break;
			case 'code':
				document.id('google_maps_address').disabled = true;
				document.id('google_maps_display').disabled = false;
				document.id('google_maps_width').disabled = true;
				document.id('google_maps_heigth').disabled = true;
				document.id('google_maps_zoom').disabled = true;
				document.id('google_maps_code').disabled = false;
				break;
		}
	},
	
	addOccupancyType : function(e, v) {
		var id = ACommon.getNextId('input[name^=otype-title]');
		Elements.from('<span id="otype'+id+'"><input type="text" maxlength="100" size="10" value="" name="otype-title['+id+']"><input type="hidden" value="'+v+'" name="otype-type['+id+']"><a class="aIcon aIconUnpublish aIconInline" href="javascript:EditSubject.removeOccupancyType(\'otype'+id+'\')"></a></span>').inject(document.id(e), 'before');
	},
	
	removeOccupancyType : function(e) {
		document.id(e).destroy();
	},
	
	showOccupancy : function() {
		document.getElements('tr[class=occupancyrow]').each(function(e) {
			e.show();
		});
	},
	
	hideOccupancy : function() {
		document.id('standard_occupancy_min').set('value', '');
		document.id('standard_occupancy_max').set('value', '');
		document.id('extra_occupancy_min').set('value', '');
		document.id('extra_occupancy_max').set('value', '');
		document.getElements('span[id^=otype]').each(function(e) {
			document.id(e).destroy();
		});
		document.getElements('tr[class=occupancyrow]').each(function(e) {
			e.hide();
		});
	},
	
	supplementManualCapacity : function(e) {
		var r = e.getParent().getParent().getParent();
		var m = r.getElement('input[name^=supplements-capacity_multiply][value=2]');
		var s = r.getElement('div[class=supplementmanualcapacity]');
		m.checked ? s.show() : s.hide();
	},
	
	addDiscount : function(e) {
		if (document.id(e).getParent().getElement('tr[class=nonediscount]'))
			document.id(e).getParent().getElement('tr[class=nonediscount]').setStyle('display', 'none');
		document.id(e).getParent().getElements('tr[class=disrow]').each(function(e) {
			e.clone().inject(e.getParent('tbody')).setStyle('display', '').removeClass('disrow');
		});
		return false;
	},
	
	removeDiscount : function(e, next) {
		document.id(e).getParent('tr').setStyle('display', 'none');
		if (next == true)
			document.id(e).getParent('tr').getNext().setStyle('display', 'none');
		return false;
	},
	
	prepareDiscountContainer : function() {
		document.getElements('*[class~=discountContainer]').each(function(e) {
			var count = e.getElements('input[name^=dis_count]');
			var value = e.getElements('input[name^=dis_value]');
			var type = e.getElements('select[name^=dis_type]');
			var per = e.getElements('select[name^=dis_per]');
			var voldises = [];
			for (var i = 0; i < count.length; i ++)
				if (count[i].getParent().getParent().getStyle('display') != 'none') {
					if (per.length > 0)
						voldises.push({'count': count[i].value, 'value': value[i].value, 'type': type[i].value, 'per': per[i].value});
					else
						voldises.push({'count': count[i].value, 'value': value[i].value, 'type': type[i].value});
				}
			e.getElement('input[type=hidden]').value = JSON.encode(voldises);
		});
	}
};

try {
	/**
	 * Joomla! 1.6.x
	 */
	Joomla.submitbutton = function(pressbutton) {
		return EditSubject.submitbutton(pressbutton);
	}
} catch (e) {
	/**
	 * Joomla! 1.5.x
	 */
	function submitbutton(pressbutton) {
		return EditSubject.submitbutton(pressbutton);
	}
}

// startup events
window.addEvent('domready', function() {
	EditSubject.preparePrices();
	EditSubject.prepareReservationTypes();
	EditSubject.prepareSupplementsOrdering();
	if (document.id('hide_occupancy'))
		document.id('hide_occupancy').checked ? EditSubject.hideOccupancy() : EditSubject.showOccupancy();
});
