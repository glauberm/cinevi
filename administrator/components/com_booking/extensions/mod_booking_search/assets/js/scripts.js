/**
 * Extended search module.
 * 
 * @package ARTIO Booking
 * @subpackage modules
 * @copyright Copyright (C) 2012 ARTIO s.r.o.. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 * @link http://www.artio.net Official website
 */

window.addEvent('domready', function() {
	// button submit search form
	document.id('bookingSearchSubmit').addEvent('click', function() {
        var from = document.id('bookingSearchDateFrom');
        var to = document.id('bookingSearchDateTo');
		if (from.value != from.getProperty('placeholder') && from.value != '' && to.value != to.getProperty('placeholder') && to.value != '' && to.value < from.value) {
			return alert(LGInvalidDateRange);
        }
		document.id('bookingSearch').mySubmit();
	});
    document.id('bookingSearch').mySubmit = function() {
        var from = document.id('bookingSearchDateFrom');
        var to = document.id('bookingSearchDateTo');
        if (from.value == from.getProperty('placeholder')) {
            from.value = '';
        }
        if (to.value == to.getProperty('placeholder')) {
            to.value = '';        
        }
        document.id('bookingSearch').submit();
    }
	// button reset search form
	if (document.id('bookingSearchReset')) {
		document.id('bookingSearchReset').addEvent('click', function() {
            document.id('bookingSearch').getElements('select').each(function(e) {
               e.value = 0;
            });
            document.id('bookingSearch').getElements('input').each(function(e) {
               e.value = '';
               e.checked = false;
            });            
            document.id('bookingSearch').mySubmit();
		});
	}
	if (document.id('bookingSearchCapacity')) {
		document.id('bookingSearchCapacity').addEvent('keyup', function() {
			ACommon.toInt(this); // allow only integer as capacity
		});
		document.id('bookingSearchCapacity').addEvent('change', function() {
			document.id('bookingSearch').mySubmit();
		});
	}
	if (document.id('bookingSearchPriceFrom')) {
		document.id('bookingSearchPriceFrom').addEvent('keyup', function() {
			ACommon.toFloat(this); // allow only float as date range from
		});
		document.id('bookingSearchPriceFrom').addEvent('change', function() {
			document.id('bookingSearch').mySubmit();
		});
	}
	if (document.id('bookingSearchPriceTo')) {
		document.id('bookingSearchPriceTo').addEvent('keyup', function() {
			ACommon.toFloat(this); // allow only float as date range to
		});
		document.id('bookingSearchPriceTo').addEvent('change', function() {
			document.id('bookingSearch').mySubmit();
		});
	}
	if (document.id('template_area')) {
		document.id('template_area').addEvent('change', function() {
			document.id('bookingSearch').mySubmit();
		});
	}
	if (document.id('bookingSearchLogin')){
		document.id('bookingSearchLogin').addEvent('click', function() {
			window.location = document.id('bookingSearchLogin').getAttribute('rel');
		});
	}
});