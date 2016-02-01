/**
 * @package		 ARTIO Booking
 * @subpackage  modules
 * @copyright	 Copyright (C) 2014 ARTIO s.r.o.. All rights reserved.
 * @author 		 ARTIO s.r.o., http://www.artio.net
 * @link         http://www.artio.net Official website
 */

jQuery(document).ready(function() {
    Calendar.setup({
        inputField: "booking_arrival_date",
        displayArea: "booking_arrival_date_da",
        ifFormat: MOD_BOOKING_CHECK_AVAILABILITY_IFFORMAT,
        daFormat: MOD_BOOKING_CHECK_AVAILABILITY_DAFORMAT,
        button: "booking_arrival_date_img",
        align: "Tl",
        singleClick: true,
        firstDay: MOD_BOOKING_CHECK_AVAILABILITY_FIRSTDAY,
	weekNumbers: false,
        disableFunc: function(date) {	    
            var now = new Date();
	    var btns = jQuery('.calendar thead .headrow .button');
            if (btns[1] !== undefined) {
		if (date.getFullYear() === now.getFullYear() && date.getMonth() === now.getMonth()) {
	    		jQuery(btns[1]).children().css('visibility', 'hidden');
		} else {
	    		jQuery(btns[1]).children().css('visibility', '');
		}
	    }

            var year = date.getFullYear();
            var month = date.getMonth() + 1;
            if (month < 10) {
                month = '0' + month;
            }
            var day = date.getDate();
            if (day < 10) {
                day = '0' + day;
            }

            var mdate = year + '-' + month + '-' + day;
            if (MOD_BOOKING_CHECK_AVAILABILITY_DATA[mdate] === undefined) {
                jQuery.ajax({
                    url: MOD_BOOKING_CHECK_AVAILABILITY_BASE,
                    async: false,
                    data: {option: 'com_booking', controller: 'subject', task: 'getMonthData', year: year, month: month}
                }).done(function(data) {
                    MOD_BOOKING_CHECK_AVAILABILITY_DATA = jQuery.parseJSON(data);
                });
            }
            try {
                return !MOD_BOOKING_CHECK_AVAILABILITY_DATA[mdate][0];
            } catch (e) {
                return false;
            }
        },
        onUpdate: function(cal) {
            var date = cal.date.print(cal.params.ifFormat)
            cal.params.displayArea.value = cal.date.print(cal.params.daFormat);
            
            var data = MOD_BOOKING_CHECK_AVAILABILITY_DATA[date];
            
            document.bookingCheckAvailability.pre_from.value = date;
            document.bookingCheckAvailability.pre_to.value = data[1];
            
            var form = jQuery(document.bookingCheckAvailability);
            form.attr('action', data[2]); // path as form action
            
            var query = data[3]; // URL query data
            var param, input;
            for (param in query) { // append query into form - form has GET method
                input = jQuery('form[name=bookingCheckAvailability] input[type=hidden][name=' + param + ']'); // param already exists?
                if (input.length === 0) { // no - create new one
                    input = jQuery('<input type="hidden" name="' + param + '" />');
                    form.append(input);
                }
                input.val(query[param]);
            }
            
            jQuery('#checkAvailability').attr('disabled', false);
        }
    });
});
