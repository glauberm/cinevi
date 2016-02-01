/**
 * @version $Id$
 * @package ARTIO Booking
 * @subpackage assets
 * @copyright Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link http://www.artio.net Official website
 */

var Calendars = {
	unBookAbleInterval: false,
	multi : false, // multiple reservations
	item_id : 0,
	operation : 0, // next operation check in/out
	checkIn : '', // last set check in value
	checkOut : '', // last set check out value 
	firstDay : '', 
	lastDay : '',
	dayLength : 86400,
	dateBegin : 0,
	dateEnd : 0,
	view : '',
	select : false,
	boxes : new Array(),
	onlyOnePrice : false,
	cartPopup : true, // open popup cart or go directly to the checkout
	enabledResponsive : false,
	highlightBoxes : true, // highlight boxes on hover
	min: 0, // Minimum reservation limit allowed in reservation type
	max: 0, // Maxinum reservation limit allowed in reservation type
	fix: 0, // Fixed limit length
	currentYear : '', currentMonth : '', nextYear : '',	nextMonth : '',	calendarType : '',
	nightBooking: false,
	week: ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'],
    mode: '',
	init : function(multi) {
		this.multi = multi;
		var form = this.getForm();
		this.operation = form !== undefined ? parseInt(form.operation.value) : 1;
		switch (this.operation) {
		default:
		case CheckOpIn:
			this.setOperation(CheckOpIn);
			break;
		case CheckOpOut:
			this.setOperation(CheckOpOut);
			break;
		}
		
		var items = document.getElements('input[name^=boxIds]');
		
		for (var i = 0; i < items.length; i++) {
			var boxIds = items[i];
			if (boxIds.value != '') {
				var boxes = boxIds.value.split(','); //in form are ids with time offset, not idShorts for HTML
				this.checkIn = this.getBoxShortId(boxes[0]);
				this.checkOut = this.getBoxShortId(boxes[boxes.length - 1]);
				var checkInBox = this.getBox(this.checkIn);
				var checkOutBox = this.getBox(this.checkOut);
				if (checkInBox && checkOutBox) {
					// save limit length constrictions from first box as integer value
					this.min = parseInt(checkInBox.min);
					this.max = parseInt(checkInBox.max);
					this.fix = parseInt(checkInBox.fix);
					var interval1 = this.getBoxInterval(checkInBox);
					var interval2 = this.getBoxInterval(checkOutBox);
					this.checkBoxLimit(interval1, interval2);
					if (this.fix > 1) // nove check out box for fixed interval
						var checkOutBox = this.getBox(this.getMoveOnBox(checkInBox, this.fix - (Calendars.nightBooking ? 2 : 1)));
					this.setBookFrom(checkInBox.fromDate, checkInBox.fromDisplay);
					this.setBookTo(checkOutBox.toDate, checkOutBox.toDisplay);
				}
			}
		}
	},
	/**
	 * Event after calendar box clicking.
	 * Control interval validity and higlighted engaged boxes in calendar.
	 * @param id box ID
	 * @returns {Boolean}
	 */
	setCheckBox : function(id) {
		// box in calendar clicked by user
		var box = this.getBox(id);
		var interval = this.getBoxInterval(box);
		
		var atomicFixedInterval = this.checkAtomicInterval(box);
		if (atomicFixedInterval) {
			var box = this.getBox(atomicFixedInterval[0]);
			var interval = this.getBoxInterval(box);
		}
		
		Calendars.item_id = box.item_id;
		$$('#objectid').set('value',box.object);
		switch (this.operation) {
		default:
		case CheckOpIn:
			if (box.rtype == '2' && box.fix > 1 && box.fixFrom.length != 7 && !box.fixFrom.contains(box.dayWeek)) { // box isn't start day of fixed from interval
				var error = '';
				for (var i = 0; i < box.fixFrom.lengh; i++) {
					switch(box.fixFrom[i]) {
						case 'mon': error += LGFixFromMon;
						case 'tue': error += LGFixFromTue;
						case 'wed': error += LGFixFromWed;
						case 'thu': error += LGFixFromThu;
						case 'fri': error += LGFixFromFri;
						case 'sat': error += LGFixFromSat;
						case 'sun': error += LGFixFromSun;
					}
				}
				return this.setCheckInfo(error, 'error');
			}
			if (!this.testBoxLimit(interval[0]+interval[1], interval[2], interval[3],
					box.boxes)) {
				this.setCheckInfo(LGNoContinuousInterval, 'error');
				return false;
			}
			this.checkIn = this.getBoxId(interval);
			this.checkOut = this.getBoxId(interval, true);
			var checkInBox = this.getBox(this.checkIn);
			var checkOutBox = this.getBox(this.checkOut);
			this.setBookFrom(checkInBox.fromDate, checkInBox.fromDisplay);
			if (checkOutBox != undefined) {
				if (this.fix > 1 && Calendars.nightBooking) {
					checkOutBox = this.getPrevBox(checkOutBox.idShort);
				}
				this.setBookTo(checkOutBox.toDate, checkOutBox.toDisplay);
			} else if (this.calendarType == 'monthly') { // check out is over - shift to next month
				this.checkBoxLimit(interval, interval);				
				return this.monthNavigation(this.nextMonth, this.nextYear);
			}
			this.checkBoxLimit(interval, interval);
			// save limit length constrictions from first box as integer value
			this.min = parseInt(checkInBox.min);
			this.max = parseInt(checkInBox.max);
			this.fix = parseInt(checkInBox.fix);
			if (this.fix > 1){
				// fixed limit check in/out limit by one click 
				this.setOperation(CheckOpIn);
				// fixed limit is stronger then min/max limit, both are ignored
				this.min = this.max = this.fix;
			} else
				// without fixed limit customer has to select check out
				this.setOperation(CheckOpOut);
			break;
		case CheckOpOut:
			var interval1 = this.getBoxInterval(this.getBox(this.checkIn));
			var interval2 = this.getBoxInterval(box);
			if (this.checkIn == '')
				this.setCheckInfo(LGSelectCheckIn, 'error');
			else if (this.onlyOnePrice && (interval1[1] != interval2[1] || interval1[0] != interval2[0])) //not same price (if set), or not same rtype
				this.setCheckInfo(LGSelectRealInterval, 'error');
			else if (!this.testBoxLimit(interval1[0]+interval1[1], interval1[2],
					interval2[3], box.boxes) || !this.isContinuos(this.getBox(this.checkIn), box))
				this.setCheckInfo(LGNoContinuousInterval, 'error');
			else {
				this.checkOut = this.getBoxId(interval2, true);
				var lastBox = Calendars.nightBooking && box.rtype == '2' ? this.getPrevBox(this.checkOut) : this.getBox(this.checkOut);
				this.setBookTo(lastBox.toDate, lastBox.toDisplay);
				this.checkBoxLimit(interval1, interval2);
				this.setOperation(CheckOpNext);
				if (! this.isInLimit()) {
					this.resetCheckOut();
					this.setOperation(CheckOpOut, false);
					this.checkBoxLimit(interval1, interval1);
				}
			}
			return false;
		}
	},
	getBoxInterval : function(box) { //returns: 0: res type, 1: res price, 2: starting id2, 3: ending id2
		var parts = box.idShort.match(/^(box-\d+-)(\d+-)(\d+)$/); 
		return new Array(parts[1], parts[2], parts[3], box.boxes == '0' ? parts[3]
				: (parseInt(parts[3]) + parseInt(box.boxes) - 1));
	},
	/**
	 * Move on box for given value. 
	 * @param object box to move
	 * @param int value of moving
	 * @return box
	 */
	getMoveOnBox : function(box, move) {
		var parts = box.idShort.match(/^(box-\d+-\d+-)(\d+)$/);
		return parts[1] + (parts[2].toInt() + move.toInt()).toString();
	},
	getBoxId : function(interval, useSecond) { //gets box id of interval
		return interval[0]+interval[1] + (useSecond == true ? interval[3] : interval[2]);
	},
	getDayNum : function(id) {
		return parseInt(id.replace('day', ''));
	},
	getBoxNum : function(id) {
		return parseInt(id.replace('box', ''));
	},
	getDay : function(i) {
		return document.getElementById('day' + i);
	},
	getBox : function(id) {
		for ( var i = 0; i < this.boxes.length; i++)
			if (this.boxes[i] && this.boxes[i].idShort == id)
				return this.boxes[i];
	},
	/**
	 * Get previous box of current box
	 * @param String id
	 * @return Object
	 */
	getPrevBox : function(id) {
		for ( var i = 0; i < this.boxes.length; i++)
			if (this.boxes[i]) {
				if (this.boxes[i].idShort == id)
					return prev;
				var prev = this.boxes[i];
			}
	},
	getBoxShortId: function (id) {
		for ( var i = 0; i < this.boxes.length; i++)
			if (this.boxes[i] && this.boxes[i].id == id)
				return this.boxes[i].idShort;
	},
	setBookFrom : function(value, display) {
		if (!this.multi) { // with multiple reservations we do not show check in/out dialog
			var form = this.getForm();
                        if (form.iFrom)
                            form.iFrom.value = display;
		}
	},
	setBookTo : function(value, display) {
		if (!this.multi) { // with multiple reservations we do not show check in/out dialog
			var form = this.getForm();
                        if (form.iTo)
                            form.iTo.value = display;
		}
	},
	testLimit : function(start, stop) {
		for ( var i = start; i <= stop; i += this.dayLength) {
			var day = this.getDay(i);
			if (!day) {
				return false;
			}
		}
		return true;
	},
	checkLimit : function(start, stop) {
		for ( var i = this.firstDay; i <= this.lastDay; i += this.dayLength) {
			var day = this.getDay(i);
			if (day) {
				day.className = ((i >= start) && (i <= stop)) ? 'day actual selected'
						: 'day actual';
			}
		}
	},
	testBoxLimit : function(prefix, from, to, boxes) { //test if interval is continuous and boxes fits
		for ( var i = from; i <= to; i++) {
			if (this.onlyOnePrice) { // allowed only one price timeline 
				if (!document.id(prefix.concat(i))) return false; // next book isn't the same price as previous
			}
		}
		var diff = (parseInt(to) - parseInt(from) + 1) / parseInt(boxes);
		return Math.round(diff) == diff;
	},
    
    /**
     * Check if an interval is continuos.
     * 
     * @param {Object} begin interval begin box
     * @param {Object} end interval end box
     * @returns {Boolean}
     */
    isContinuos : function(begin, end) {
        if (!(Calendars.multi || Calendars.onlyOnePrice)) {
            var toEnds = new Array();
            Calendars.boxes.each(function(box) {
                toEnds.push(box.toEnd.toInt()); // extract list of all bookable boxes
            });          
            for (var i = end.toEnd.toInt(); i < begin.toEnd.toInt(); i++) {
                if (toEnds.indexOf(i) === -1) { // check if the interval has for every unit bookable box
                    return false;
                }
            }
        }
        return true;
    },
	
	/**
	 * Highlight selected reservation interval in calendar and store in the form.
	 * @param from
	 * @param to
	 */
	checkBoxLimit : function(from, to) {
		var boxIds = new Array();
		var begin = end = -1;
		for (var i = 0; i < this.boxes.length; i++) { // process all boxes in calendar
			if (this.boxes[i]) { // some box can be disabled
				var box = document.id(this.boxes[i].idShort);
				if (!this.multi) { 
					box.className = this.cleanSelected(box.className); // first unselect everything (do not with multiple reservations)
					box.selected = false;
				}
				if (from != null && to != null) { // search boxes available for selected interval
					var interval = this.getBoxInterval(this.boxes[i]);
					if (interval[1] == from[1] && interval[2] == from[2])
						begin = i; // begin box of selected interval is in the same price and reservation type as box
					if (interval[1] == to[1] && interval[2] == to[3])
						end = i; // end box of selected interval is in the same price and reservation type as box
				}
			}
		}

		// group boxes by day
		var dayGroup = new Array();
		var currentDayBoxes = new Array();
		var currentDay = this.boxes[begin] ? this.boxes[begin].fromDate : null;
		
		for (var i = begin; i <= end; i++) // process boxes in selected interval
			if (this.boxes[i]) {
				if (this.boxes[i].fromDate != currentDay) { // first box of the next day
					dayGroup.push(currentDayBoxes); // push day into days
					currentDayBoxes = new Array(); // start the next day
				}
				currentDayBoxes.push(i); // push box into current day
				currentDay = this.boxes[i].fromDate; // switch to the box day
			}
		if (currentDayBoxes.length > 0) // push the last day into days if non empty
			dayGroup.push(currentDayBoxes);
		
		var item_id = 0;
		
		for (var i = 0; i < dayGroup.length; i++) { // process grouped days
			
			var id = null;
			
			dayGroup[i].each(function(g) { // process day boxes
				var int = Calendars.getBoxInterval(Calendars.boxes[g]);
				if (!id && ((int[0] == from[0] && int[1] == from[1]) || (int[0] == to[0] && int[1] == to[1]))) // the same rtype or the same price
					id = g;
			});
			
			if (!id && !this.onlyOnePrice) // or booking over pricing periods
				dayGroup[i].each(function(g) { // process day boxes
					if (!id) {
						id = g; // try next box 
						boxIds.each(function(b) { // check if the box time is already engaged
							var uts = b.match(/box\-(\d+)\-(\d+)\-(\d+)\-(\d+)/);
							if (uts[3] < Calendars.boxes[id].toUts && uts[4] > Calendars.boxes[id].fromUts)
								id = null; // some other box is already engaging the box time
						});
					}
				});

			if (id == null)
				continue;
			
			var box = document.id(this.boxes[id].idShort);
			if (this.multi && box.selected) { // in multi mode unselect already selected box
						box.className = this.cleanSelected(box.className); // unhighlight (select) box
						box.selected = false;
			} else { // select non selected box
						box.className = this.addSelected(box.className); // highlight (select) box
						box.selected = true;
						if (this.highlightBoxes && (this.latestHighlighted instanceof Array))
							this.latestHighlighted.erase(this.boxes[id].idShort); // disable unhighlighting 
					}
			boxIds.push(this.boxes[id].id);
			item_id = this.boxes[id].item_id;
		}
		
		var form = this.getForm();
		
		if (this.multi) { // with multiple reservations try to concat different selected fragment together
			
			document.getElements('input[name^=boxIds]').each(function(e) { // reset form to join intervals again
				e.dispose();
			});
			
			document.getElements('input[name^=subject]').each(function(e) { // reset form to join intervals again
				e.dispose();
			});
			
			var selected = new Array();
			for (var i = 0; i < this.boxes.length; i++) // process again all boxes in calendar to group all selected
				if (this.boxes[i] && document.id(this.boxes[i].idShort).selected)
					selected.push(i);
			
			var boxIdsNumber = totalSelected = 0;
            
			while (selected.length > 0) { // process until every selected boxes are grouped
                totalSelected = selected.length;                
				boxIds = new Array(); // start new group
				boxIds.push(selected[0]);
				latest = this.boxes[selected[0]]
				selected.erase(selected[0]); // first item is grouped
                
				for (var i = 0; i < selected.length; i++) { // check if some of remaining process includes current group
					
					box = this.boxes[selected[i]];
					
					var sameGroup = true; // with multiple pricing mode do not check the group
					if (this.onlyOnePrice)
						sameGroup = box.priceId == latest.priceId && box.rtypeId == latest.rtypeId; // next box is in the same group
					
					if (box.rtype == '1' && (latest.toEnd - box.toEnd) == 1)
						var atomic = true;
					else if (box.rtype == '2' && ((this.week.indexOf(box.dayWeek) - this.week.indexOf(latest.dayWeek)) == 1 || (this.week.indexOf(box.dayWeek) - this.week.indexOf(latest.dayWeek)) == -6) && (latest.toEnd - box.toEnd) == 1)
						var atomic = true;
                    else
						var atomic = false;
                    
					if (sameGroup && atomic && !latest.noContinue// next box continues without break in the same group
							&& (this.boxes[boxIds[0]].fix == 0 || boxIds.length < this.boxes[boxIds[0]].fix || (this.boxes[boxIds[0]].fixMultiply == 1 && totalSelected % this.boxes[boxIds[0]].fix == 0))) { // there is no fix limit or fix limit is not full yet or selected interval contains more completed fixed limits
						boxIds.push(selected[i]);
						latest = box;
					}
				}
				
				if (boxIds.length) {
					// control allowed fixed limit 
					if (this.boxes[boxIds[0]].fix != 0 && boxIds.length != this.boxes[boxIds[0]].fix && this.boxes[boxIds[0]].fixMultiply == 0 && boxIds.length % this.boxes[boxIds[0]].fix != 0)
						this.setCheckInfo(LGFixedLimitError.replace('%s', this.boxes[boxIds[0]].fix), 'error');
					// control minimum allowed limit
					else if (this.boxes[boxIds[0]].min != 0 && boxIds.length < this.boxes[boxIds[0]].min)
						this.setCheckInfo(LGMinimumLimitUnderflow.replace('%s', this.boxes[boxIds[0]].min), 'error');
					// control maximum allowed limit
					else if (this.boxes[boxIds[0]].max != 0 && boxIds.length > this.boxes[boxIds[0]].max)
						this.setCheckInfo(LGMaximumLimitOverflow.replace('%s', this.boxes[boxIds[0]].max), 'error');
					else { // is in limit register reservation item
						document.id(form).adopt(new Element('input', {'type': 'hidden', 'name': 'subject[' + (boxIdsNumber) + ']', 'value': this.boxes[boxIds[0]].item_id}));
						var hBoxIds = new Array(); // data for hidden form field
						for (var i = 0; i < boxIds.length; i++) // group is complete - prepare to write into the form
							hBoxIds[i] = this.boxes[boxIds[i]].id; // get native box id
						document.id(form).adopt(new Element('input', {'type': 'hidden', 'name': 'boxIds[' + (boxIdsNumber ++) + ']', 'value': hBoxIds.join(',')}));
					}
					for (var i = 0; i < boxIds.length; i++)
						selected.erase(boxIds[i]); // erase grouped to avoid duplicity 
				}
			}
			
		} else {
            if (document.getElements('input[name^=boxIds]')[0]) {
                document.getElements('input[name^=boxIds]')[0].value = boxIds.join(','); // store selected boxes in the form
                document.getElements('input[name^=subject]')[0].value = item_id;
            }
		}
		Calendars.showTotal();
	},
	
	/**
	 * Send whole reservation form to the server through AJAX to ge total price. 
	 */
	showTotal : function() {
		if ($('total')) {
			var form = document.id(this.getForm()).clone(); // copy of reservation form
			// remove navigation parameters
			document.id(form.task).dispose(); 
			document.id(form.controller).dispose();
			document.id(form.Itemid).dispose();
			new Request({
			    url: juri + '?option=com_booking&controller=reservation&task=gettotal',
			    method: 'post',
			    data: document.id(form).toQueryString(),
			    onSuccess: function(responseText) {
			    	responseText = JSON.decode(responseText);
			    	if (responseText['status'] == 'FAIL') {
			    		Calendars.unBookAbleInterval = true;
			    		if (responseText['error'] != '') {
			    			document.id('total').set('html', '<span class="unBookAbleInterval">' + responseText['error'] + '</span>');
			    		} else {
			    			document.id('total').set('html', '<span class="unBookAbleInterval">' + LGUnBookAbleInterval + '</span>');
			    		}
			    	} else if(responseText['status'] == 'OK') { 
			    		Calendars.unBookAbleInterval = false;
			    		document.id('total').set('html', responseText['total'] == '' ? '&nbsp;' : responseText['total']); // show total price on page
                        Calendars.setCheckInfo('', '');
			    	}
			    }
			}).send();
		}
	},
	
	/**
	 * Show occupancy selectors for each capacity item
	 */
	showOccupancy : function() {
		for (var i = 1; i <= document.id('capacity').options.length; i ++) // procces each capacity item
			if (document.id('capacity' + i + 'occupancy')) // occupancy section appears
				// show or hide with tween efect
				document.id('capacity' + i + 'occupancy').tween('height', i <= document.id('capacity').value ? document.id('capacity1occupancy').getSize().y : 0).setStyle('overflow', i <= document.id('capacity').value ? '' : 'hidden');
	},
	
	setOperation : function(operation, msg) {
		if (!this.multi) { // with multiple reservations we do not show check in/out dialog
			var selectCheckInDay = document.getElementById('selectCheckInDay');
			var selectCheckOutDay = document.getElementById('selectCheckOutDay');
                        if (!selectCheckInDay || !selectCheckOutDay)
                            return;
			var className1 = 'checkButton checkButtonActive';
			var className2 = 'checkButton checkButtonUnactive';
			this.operation = operation;
			var form = this.getForm();
			form.operation.value = operation;
			switch (this.operation) {
			case CheckOpIn:
			case CheckOpNext:
                                selectCheckInDay.className = className1;
                                selectCheckOutDay.className = className2;
				var checkInfo = this.operation == CheckOpIn ? LGSelectCheckIn
						: LGSelectCheckNext;
				break;
			case CheckOpOut:
                                selectCheckInDay.className = className2;
                                selectCheckOutDay.className = className1;
				var checkInfo = LGSelectCheckOut;
				break;
			}
			if (msg != false)
				this.setCheckInfo(checkInfo, 'message');
		}
	},
	setCheckInfo : function(value, type) {
		var checkInfo = document.getElementById('checkInfo');
		checkInfo.innerHTML = value;
		switch (type) {
		case 'message':
			checkInfo.className = 'checkInfo checkInfoMessage';
			break;
		case 'notice':
			checkInfo.className = 'checkInfo checkInfoNotice';
			break;
		case 'error':
			checkInfo.className = 'checkInfo checkInfoError';
			break;
		}
	},
	setRType : function(value) {
		/*
		 * var form = this.getForm(); form.rtype.value = value;
		 */
	},
	getForm : function() {
		var form = document.bookSetting;
		return form;
	},
	monthNavigation : function(month, year) {
		if (year == undefined) {
			var parts = month.split(',');
			month = parts[0];
			year = parts[1];
		}
		var form = this.getForm();
		form.month.value = month;
		form.year.value = year;
		Calendars.requestNavigation();
	},
	weekNavigation : function(week, year) {
		if (year == undefined) {
			var parts = week.split(',');
			week = parts[0];
			year = parts[1];
		}
		var form = this.getForm();
		form.week.value = week;
		form.year.value = year;
		Calendars.requestNavigation();
	},
	dayNavigation : function(day, month, year) {
		if (month == undefined) {
			var parts = day.split('-');
			year = parts[0];
			month = parts[1];
			day = parts[2];
		}
		var form = this.getForm();
		form.month.value = month;
		form.year.value = year;
		form.day.value = day;
		Calendars.requestNavigation();
	},
	/**
	 * Calendar pagination via ajax
	 */
	requestNavigation : function(url) {
        if (url == undefined) {
            url = new URI(window.location);
            url = url.setData('calendar', null).setData('pre_from', null).setData('pre_to', null).toString();
        }
		new Request.HTML({
		    url: url,
		    method: 'post',
                    evalScripts: false,
		    data: document.id(Calendars.getForm()).toQueryString() + '&tmpl=component&ajax=1',
		    onRequest: function() {},
		    onSuccess: function(responseTree, responseElements, responseText, responseJavaScript) {                        

                        Calendars.evalJs = responseJavaScript.trim();

		    	var marks = responseText.match(/<!--AJAX_[A-Za-z]+-->/g); // search marks aka <!--AJAX_formFoot-->
		    	for (var i = 0; i < marks.length; i++) {
			    	
		    		var bmk = marks[i]; // begin mark aka <!--AJAX_formFoot-->
			    	var emk = marks[i].replace('<!--', '<!--/'); // end mark aka <!--/AJAX_formFoot-->

			    	var id = marks[i].match(/<!--AJAX_([A-Za-z]+)-->/).pop(); // updated element id from mark aka formFoot
			    	
			    	document.id(id).set('html', responseText.substr((responseText.indexOf(bmk) + bmk.length), (responseText.indexOf(emk) - responseText.indexOf(bmk) - bmk.length))); // update element html
		    	}	
		    	
		    	Calendars.boxes = new Array();                        
                        
                        var ajaxEvalBegin = responseText.indexOf('AJAX_EVAL_BEGIN');
                        var ajaxEvalEnd = responseText.indexOf('AJAX_EVAL_END');
                        if (ajaxEvalBegin > -1 && ajaxEvalEnd > -1)                            
                            eval(responseText.substr((ajaxEvalBegin + 15), (ajaxEvalEnd - ajaxEvalBegin - 15)));                                     
                        else if (responseJavaScript.trim())
                            eval(responseJavaScript);                            
		    	
		    	// initialise JTooltips again after page reload 
                try {
                    com_booking_tooltip();
                    jQuery('.hasTip, .hasTooltip').tooltip({"html": true,"container": "body"});
                } catch(e) {
                    if (Tips != undefined) {
                        // remove all old tips
                        $$('.tip-wrap').each(function(el) {
                            el.dispose();
                        });
                        // re-init tips
                        $$('.hasTip').each(function(el) {
                            var title = el.get('title');
                            if (title) {
                                var parts = title.split('::', 2);
                                el.store('tip:title', parts[0]);
                                el.store('tip:text', parts[1]);
                            }
                        });
                        var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false});
                    }
                }
                // initialise SqueezeBox again after page reload 
                if (SqueezeBox != undefined) {
                    //SqueezeBox.initialize({});
                    SqueezeBox.assign($$('a.modal'), {
                        parse: 'rel'
                    });
                }
		    	Calendars.init(Calendars.multi);
		    	// init responsive design again
		    	try { BookingResponsive(); } catch (e) {}
		    },
		    onFailure: function() {}
		}).send();
	},
	reset : function() {
                this.resetCheckIn();
                this.resetCheckOut();
                this.checkLimit(0, 0);
                this.checkBoxLimit(null, null);                
		this.setOperation(CheckOpIn);
		var form = this.getForm();
        try {
            document.getElements('input[name^=boxIds]')[0].value = '';
        } catch(e) {}
	},
	resetCheckIn : function() {
		this.setBookFrom('', '');
		this.checkIn = '';
	},
	resetCheckOut : function() {
		this.setBookTo('', '');
		this.checkOut = '';
	},
	/**
	 * Control reservation validity and submit.
	 */
	bookIt : function() {
		
		if (document.id('capacity') && document.id('capacity').options && document.id('capacity').options.length) { // capacity selector appears
			for (var i = 1; i <= document.id('capacity').value; i ++) { // check each capacity item
				var occupancy = document.getElements('select[id^=capacity' + i + 'occupancy]'); // check occupancy selectors for capacity item
				if (occupancy.length) { // occupancy selectors appears
					var total = 0;
					for (var j = 0; j < occupancy.length; j ++) // count selected occupancy total 
						total += occupancy[j].value.toInt();
					if (total == 0) // nothing selected
						return this.setCheckInfo(LGSelectOccupancy, 'error');
				}
			}
		}
		
		if (Calendars.unBookAbleInterval)
			return;
		
		// test if check in/out are set
		var boxIds = document.getElements('input[name^=boxIds]');
		if (this.getForm().ctype.value != 'period' && ! this.multi) { // non multi mode, non period mode
			if (this.checkIn == '' || this.checkOut == '') // check in or check out empty
				return this.setCheckInfo(LGSelectCheckInAndCheckOut, 'error');
			// test if interval is in allowed limit
			else if (! this.isInLimit())
				return;
		} else if (this.multi && boxIds.length == 0) // multi mode and non interval selected
			return this.setCheckInfo(LGSelectCheckInAndCheckOut, 'error');
		
		if (Calendars.nightBooking) // night mode, at least two boxes required
			for (var i = 0; i < boxIds.length; i ++)
				if (boxIds[i].value.split(',').length < 2)
					return this.setCheckInfo(LGSelectNightInterval, 'error');
		
		// everything OK - complete form and submit
		var form = this.getForm();
		
		form.controller.value = 'reservation';
		form.task.value = this.cartPopup ? 'add' : 'add_checkout'; // go to popup cart or directly to the checkout
		Calendars.view = form.view.value;
		form.view.value = '';
		
        if (Calendars.mode === 'change') {
            return ViewReservation.changeItem(0);
        }
        
		if (!this.cartPopup) // go directly to the checkout
			return document.bookSetting.submit();
		
		//open modal window
		
		suppements = document.id(document.body).getElements('[name^="supplements"]');
		
		iframeName = 'frame_add'; //name of iframe to sent form 
		SqueezeBox.$events['close'] = [];  //http://forum.joomla.org/viewtopic.php?p=2507364
		SqueezeBox.$events['open'] = []; 
		SqueezeBox.initialize();	
		if (this.enabledResponsive) {
			SqueezeBox.open("",{handler: 'iframe', size: {x: 440, y: 300 + suppements.length*20}, iframeOptions: {name: iframeName}, iframePreload: false});
		} else {
		SqueezeBox.open("",{handler: 'iframe', size: {x: 600, y: 300 + suppements.length*20}, iframeOptions: {name: iframeName}, iframePreload: false}); 
		}
		SqueezeBox.asset.name = iframeName;
		
		if (typeof SqueezeBox.asset != "undefined" && SqueezeBox.asset.name==iframeName){			
			SqueezeBox.addEvent('open',function(content){ //when modal is opened, submit form
				document.bookSetting.target="";
				document.bookSetting.tmpl.value="";
				
				if (SqueezeBox.asset.name==iframeName){ //if modal have proper name, point form into it //TODO: add loading image
					//submit form to iframe
					document.bookSetting.target=iframeName;
					document.bookSetting.tmpl.value="component";
					document.bookSetting.submit(); 
					//restore form back to original
					document.bookSetting.controller.value = '';
					document.bookSetting.view.value = Calendars.view;
					document.bookSetting.task.value = 'display';
					document.bookSetting.target="";
					document.bookSetting.tmpl.value="";
				}
				else //modal failed to load, submit to current window
					document.bookSetting.submit();
			});
		} else { //modal failed to load, submit to current window
			document.bookSetting.submit();
		}
	},
	
	sleep : function(ms)
	{
		var dt = new Date();
		dt.setTime(dt.getTime() + ms);
		while (new Date().getTime() < dt.getTime());
	}, 
	
	/**
	 * Check if interval length overflows or underflows allowed limit.
	 * Make error message.
	 * @return boolean
	 */
	isInLimit : function() {
		var form = this.getForm();
		// selected interval units count
		var length = document.getElements('input[name^=boxIds]')[0].value.split(',').length;
		// test if interval underflows minimum limitation
		if (length < this.min) {
			this.setCheckInfo(LGMinimumLimitUnderflow.replace('%s', this.min), 'error');
			return false;
		}
		// test if interval overflows maximum limitation
		if (this.max != 0 && length > this.max) {
			this.setCheckInfo(LGMaximumLimitOverflow.replace('%s', this.max), 'error');
			return false;
		}
		return true;
	},
	unhighlightInterval : function(id) {
		if (this.highlightBoxes) // not used in mobile devices
			if (this.latestHighlighted instanceof Array)
				this.latestHighlighted.each(function(id) {
					document.id(id).className = Calendars.cleanSelected(document.id(id).className);
				});
	},
	highlightInterval : function(id) {
		if (this.highlightBoxes) { // not used in mobile devices
			var box = this.getBox(id);
			var atomicFixedInterval = this.checkAtomicInterval(box, true);
			if (atomicFixedInterval) { // box is member of some atomic fixed interval
				this.latestHighlighted = atomicFixedInterval;
				atomicFixedInterval.each(function(boxId) {
					document.id(boxId).className = Calendars.addSelected(document.id(boxId).className);
				});
			}
		}
	},
	cleanSelected : function(className) {
		return className.replace(/selected/g, '').trim();
	},
	addSelected : function(className) {
		return trim(className + ' selected');
	},
	checkAtomicInterval : function(box, ignoreSelected) {
	    
		if (document.id(box.idShort).selected && ignoreSelected == true)
			return null;
		
		if (box.rtype == '2' && box.fix > 1 && box.fixFrom.length != 7 && !box.fixFrom.contains(box.dayWeek))
			return null; // it is not valid begin of fixed daily interval with fixed from option
		
		if (box.fix > 1) { // box is member of some fixed interval
			var interval = this.getBoxInterval(box);
			
			// analyse previous boxes
			var begin = interval[2]; // start with current box
			do {} while(document.id(interval[0] + interval[1] + (begin --))); // go forward until box is the same family
			begin += 2; // ignore last two decrements
			
			// analyse next boxes
			var end = interval[2]; // start with current box
			do {} while(document.id(interval[0] + interval[1] + (end ++))); // go backward until box is the same family
			end -= 2; // ignore last two increments
			
			var fullSpace = end - begin + 1; // full space for fixed interval
			var tailSpace = end - interval[2].toInt() + 1; // space for fixed interval after current box
			
			if (fullSpace >= box.fix && document.id(box.idShort).selected) { // box is already selected - check if there is some fixed interval already selected
				for (var i = interval[2]; i >= begin; i --) {
					if (document.id(interval[0] + interval[1] + i).selected)
						var sbegin = i;
					else
						break;
				}
				
				for (var i = interval[2]; i <= end; i ++) {
					if (document.id(interval[0] + interval[1] + i).selected)
						var send = i;
					else
						break;
				}
				
				if ((send - sbegin + 1) == box.fix) {
					begin = sbegin;
					end = send;
					fullSpace = box.fix;
				}
			} else if (fullSpace > box.fix) { // full space is larger then fixed interval
				if (tailSpace < box.fix) // space after current box is not enough
					begin = end - box.fix.toInt() + 1; // move begin at the latest position
				else if (tailSpace >= box.fix) // space after current is enough
					begin = interval[2].toInt(); // move begin at the current box position
				end = begin + box.fix.toInt() - 1; // compute end from begin
				fullSpace = box.fix;
			}
		
			if (fullSpace == box.fix) { // there is atomic fixed interval
				
				if (ignoreSelected)
					for (var i = begin; i <= end; i ++) // check if some part of interval is selected
						if (document.id(interval[0] + interval[1] + i).selected)
							return null;
				
				var atomicFixedInterval = new Array();
				for (var i = begin; i <= end; i++)
					atomicFixedInterval.push(interval[0] + interval[1] + i);
				return atomicFixedInterval;
			}
		} else 
			return [box.idShort];
	}	
}
function disallowDate(date) {
	if (Calendars.dateBegin == '0' && Calendars.dateEnd == '0')
		return false;
	var year = date.getFullYear().toString();
	var month = date.getMonth() + 1;
	if (month < 10)
		month = '0' + month.toString();
	var day = date.getDate();
	if (day < 10)
		day = '0' + day.toString();
	var current = parseInt(year + month + day);
	return Calendars.dateBegin > current || Calendars.dateEnd < current;
}
function onSelectDate(calendar, date) {
	if (calendar.dateClicked)
		Calendars.dayNavigation(date);
}
/**
 * Function calls Month/Year Picker after change month.
 */
function __doPostBack(id) {
	Calendars.monthNavigation(document.id(id).value.replace(/\-/, ',').replace(/^0/,'')); 
}