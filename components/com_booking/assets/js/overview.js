/**
 * @package        ARTIO Booking
 * @subpackage		views
 * @copyright	  	Copyright (C) 2014 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

var BookingOverview = {
    /**
     * First hour of time lines.
     * @type String
     */
    firstHour: '',
    /**
     * Last hour of time lines.
     * @type String
     */
    lastHour: '',
    /**
     * URL to update overview by Ajax.
     * @type String
     */
    navigateURL: '',
    /**
     * Current date to show overview.
     * @type String
     */
    currentDate: '',
    /**
     * Show single week.
     * @type Boolean
     */
    singleWeek: false,
    /**
     * Render reservations time lines.
     */
    init: function() {
        var first, last, up, down, reservations, beginH, beginM, endH, endM, hours, start, offset, length;

        first = BookingOverview.firstHour.toInt() - 1;
        last = BookingOverview.lastHour.toInt() - first - 1;

        up = new Date();
        down = new Date();

        document.getElements('#bookingOverview .reservation').reverse().each(function(e) { // process all reservations

            up.parse(e.getProperty('up')); // reservation begin datetime
            down.parse(e.getProperty('down')); // reservation end datetime
            
            if (BookingOverview.singleWeek) {
                BookingOverview.currentDate = e.getParent().getProperty('date');
            }

            if (up.format('%Y-%m-%d') < BookingOverview.currentDate || up.format('%H:%M:%S') < BookingOverview.firstHour) {
                // the reservation begins under the time line
                beginH = 1;
                beginM = 0;
            } else {
                beginH = up.get('hr').toInt() - first;
                beginM = (up.get('min').toInt() / 60);
            }

            if (down.format('%Y-%m-%d') > BookingOverview.currentDate || down.format('%H:%M:%S') > BookingOverview.lastHour) {
                // the reservation ends over the time line
                endH = last;
                endM = 2;
            } else {
                endH = (down.get('hr').toInt() === 0 ? last : (down.get('hr').toInt() - first));
                endM = (down.get('min').toInt() / 60);
            }

            hours = e.getParent().getParent().getParent().getChildren(); // the hour list in the time line

            if (hours[endH] !== undefined && hours[beginH] !== undefined) {
                start = hours[1].getPosition().x; // the first hour in the time line
                offset = (hours[beginH].getPosition().x + hours[beginH].getSize().x * beginM); // the reservation begin hour
                length = (hours[endH].getPosition().x + hours[endH].getSize().x * endM); // the reservation end hour

                e.setStyle('left', Math.round(offset - start)); // shift to the begin hour
                e.setStyle('width', Math.round(length - offset)); // extend for hours interval
            } else {
                e.dispose(); // the reservation is completely out of the time line
            }
        });

        document.getElements('#bookingOverview .reservation').each(function(e) { // shift time lines to the top
            if (e.getParent().getChildren().length > 1) {
                e.setStyle('margin-top', e.getParent().getParent().getParent().getPosition().y - e.getPosition().y);
            }
        });
    },
    /**
     * Update overview by Ajax.
     * 
     * @param String date new overview date
     * @param Int id switch to antoher item family
     */
    navigate: function(date, id) {
        new Request.HTML({
            url: BookingOverview.navigateURL,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                responseElements.each(function(e) {
                    if (e.id === 'bookingOverview') {
                        document.id('bookingOverview').set('html', e.get('html'));
                    }
                });
                eval(responseJavaScript);
                jQuery(document).ready(function(){
                    jQuery('.hasTooltip').tooltip({"html": true,"container": "body"});
                });
            }
        }).get({date: date, id: id, r: Math.random(10, 1000)});
    }
};