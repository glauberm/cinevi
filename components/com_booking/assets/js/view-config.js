/**
 * Javascript for configuration form
 * 
 * @version $Id$
 * @package ARTIO Booking
 * @subpackage assets
 * @copyright Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link http://www.artio.net Official website
 */

var ViewConfig = {

	/**
	 * Valid form before submit. Standard in Joomla! administration.
	 * 
	 * @param pressbutton
	 *            button selected in toolbar
	 */
	submitbutton : function(pressbutton) {
		switch (pressbutton) {
		case 'apply':
			// save last select bookmark into cookies
			ACommon.saveBookmark();
			break;
		}
		submitform(pressbutton);
	},

	/**
	 * Some options have child options. This child options are selectable only
	 * if masters options are switch on. This function disable child options if
	 * masters are switch off. This function set events for masters - if masters
	 * go to switch on then child options are enabled.
	 * 
	 * @param boolean
	 *            setEvents if true then function set events for masters - run
	 *            only once during initialise
	 */
	setEvents : function(setEvents) {

  	// show names (mine/all)
  	this.setDisabled('jform_display_mine_only0', 'jform_display_who_reserve0');
  	this.setDisabled('jform_display_mine_only1', 'jform_display_who_reserve0');

		// bookmark main
		this.setDisabled('jform_date_long', 'jform_date_type0');
		this.setDisabled('jform_date_normal', 'jform_date_type0');
		this.setDisabled('jform_date_day', 'jform_date_type0');
		this.setDisabled('jform_date_day_short', 'jform_date_type0');
		this.setDisabled('jform_time', 'jform_date_type0');
		this.setDisabled('jform_address_format_custom', 'jform_address_format0');
		
		document.getElements('*[id^=jform_date_long_]').each(function (e) {
			ViewConfig.setDisabled(e.id, 'jform_date_type0');
		});
		
		document.getElements('*[id^=jform_date_normal_]').each(function (e) {
			ViewConfig.setDisabled(e.id, 'jform_date_type0');
		});
		
		document.getElements('*[id^=jform_date_day_]').each(function (e) {
			ViewConfig.setDisabled(e.id, 'jform_date_type0');
		});
		
		document.getElements('*[id^=jform_date_day_short_]').each(function (e) {
			ViewConfig.setDisabled(e.id, 'jform_date_type0');
		});
		
		document.getElements('*[id^=jform_time_]').each(function (e) {
			ViewConfig.setDisabled(e.id, 'jform_date_type0');
		});

		/* bookmark objects */

		// thumbnails
		this.setDisabled('jform_display_thumbs_subjects_list_width',
				'jform_display_thumbs_subjects_list0');
		this.setDisabled('jform_display_thumbs_subjects_list_height',
				'jform_display_thumbs_subjects_list0');

		// introtext
		this.setDisabled('jform_display_readmore_subjects_list_length',
				'jform_display_readmore_subjects_list0');

		// pagination
		if (document.id('jform_subjects_pagination0').checked || document.id('jform_subjects_pagination1').checked) {
			document.id('jform_subjects_pagination_start').setProperty('disabled', false);
			document.id('jform_subjects_pagination_selector0').setProperty('disabled', false);
			document.id('jform_subjects_pagination_selector1').setProperty('disabled', false);
		}
		if (document.id('jform_subjects_pagination2').checked) {
			document.id('jform_subjects_pagination_start').setProperty('disabled', true);
			document.id('jform_subjects_pagination_selector0').setProperty('disabled', true);
			document.id('jform_subjects_pagination_selector1').setProperty('disabled', true);
		}

		// monthly calendars
		this.setDisabled('jform_subjects_calendar_skin',
				'jform_subjects_calendar0');
		this.setDisabled('jform_subjects_calendar_start',
				'jform_subjects_calendar0');
		this.setDisabled('jform_subjects_calendar_deep',
				'jform_subjects_calendar0');
		// weekly calendars
		this.setDisabled('jform_subjects_week_deep', 'jform_subjects_week0');

		/* bookmark object */

		// main image
		this.setDisabled('jform_display_thumbs_subject_detail_width',
				'jform_display_image_subject_detail0');
		this.setDisabled('jform_display_thumbs_subject_detail_height',
				'jform_display_image_subject_detail0');
		// images gallery
		this.setDisabled('jform_display_gallery_subject_position',
				'jform_display_gallery_subject_detail0');
		this.setDisabled('jform_display_gallery_subject_style',
				'jform_display_gallery_subject_detail0');
		this.setDisabled('jform_gallery_slideshow_duration',
				'jform_display_gallery_subject_detail0');
		this.setDisabled('jform_gallery_slideshow_shift',
				'jform_display_gallery_subject_detail0');
		this.setDisabled('jform_display_gallery_thumbs_subject_detail_width',
				'jform_display_gallery_subject_detail0');
		this.setDisabled('jform_display_gallery_thumbs_subject_detail_height',
				'jform_display_gallery_subject_detail0');
		this.setDisabled('jform_display_gallery_preview_subject_detail_width',
				'jform_display_gallery_subject_detail0');
		this.setDisabled('jform_display_gallery_preview_subject_detail_height',
				'jform_display_gallery_subject_detail0');

		/* gallery slideshow options */
		if (document.id('jform_display_gallery_subject_detail1').checked)
			document.id('jform_gallery_slideshow_duration').setProperty('disabled', document.id('jform_display_gallery_subject_style').value != 'slideshow');
		if (document.id('jform_display_gallery_subject_detail1').checked)
			document.id('jform_gallery_slideshow_shift').setProperty('disabled', document.id('jform_display_gallery_subject_style').value != 'slideshow');

		/* bookmark prices */
		if (document.id('jform_thousand_separator0').checked)
			document.id('jform_thousand_separator_char').setProperty('disabled', true);
		if (document.id('jform_thousand_separator1').checked)
			document.id('jform_thousand_separator_char').setProperty('disabled', true);
		if (document.id('jform_thousand_separator2').checked)
			document.id('jform_thousand_separator_char').setProperty('disabled', true);
		if (document.id('jform_thousand_separator3').checked)
			document.id('jform_thousand_separator_char').setProperty('disabled', true);
		if (document.id('jform_thousand_separator4').checked)
			document.id('jform_thousand_separator_char').setProperty('disabled', false);
		
		document.id('jform_main_currency').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_last_zero0').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_last_zero1').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_decimals').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_decimals_point0').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_decimals_point1').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_thousand_separator0').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_thousand_separator1').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_thousand_separator2').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_thousand_separator3').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_thousand_separator4').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_thousand_separator_char').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_price_format').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_online_payment_expiration_time').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_b2b_tax0').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_b2b_tax1').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_show_total_price0').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_show_total_price1').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_show_payment_status').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_show_unit_price').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_show_deposit_price').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_show_price_excluding_tax').setProperty('disabled', document.id('jform_using_prices').value == '0');
		document.id('jform_show_tax').setProperty('disabled', document.id('jform_using_prices').value == '0');
		
		document.id('jform_show_deposit_price').setProperty('disabled', document.id('jform_using_prices').value != '2');

		document.id('jform_redirection_after_reservation_menu_item').setProperty('disabled', document.id('jform_redirection_after_reservation').value != '5');
		document.id('jform_redirection_after_reservation_custom_url').setProperty('disabled', document.id('jform_redirection_after_reservation').value != '6');
		
		// set events for all masters
		if (setEvents) {
			Array.each(document.id('component-form').getElements('*[class~=masterChild]'), function(child, index) {
				Array.each(document.id('component-form').getElements('label[for=' + child.id + ']|label[for=' + child.id + '-lbl]'), function(label, index) {
					label.setStyle('min-width', '135px')
					label.setStyle('padding-left', '15px')
					label.setStyle('width', '135px')
				});
			});
			 
			var masters = new Array('jform_display_who_reserve0', 'jform_display_who_reserve1',
					'jform_date_type0', 'jform_date_type1',
					'jform_quick_navigator0', 'jform_quick_navigator1',
					'jform_subjects_pagination0', 'jform_subjects_pagination1', 'jform_subjects_pagination2',
					'jform_display_thumbs_subjects_list0',
					'jform_display_thumbs_subjects_list1',
					'jform_display_image_subject_detail0',
					'jform_display_image_subject_detail1',
					'jform_display_gallery_subject_detail0',
					'jform_display_gallery_subject_detail1',
					'jform_thousand_separator0',
					'jform_thousand_separator1', 
					'jform_thousand_separator2',
					'jform_thousand_separator3',
					'jform_thousand_separator4',
					'jform_subjects_calendar0',
					'jform_subjects_calendar1', 'jform_subjects_week0',
					'jform_subjects_week1',
					'jform_display_readmore_subjects_list0',
					'jform_display_readmore_subjects_list1',
					'jform_using_prices',
					'jform_display_gallery_subject_style',
					'jform_redirection_after_reservation',
					'jform_address_format0',
					'jform_address_format1');
                    
			masters.each(function(m) {
				try {
					document.id(m).addEvent('click', function() {
						ViewConfig.setEvents(false);
					});
                    jQuery(document).ready(function () {
                        jQuery('select#' + m).chosen().change(function() {
                            ViewConfig.setEvents(false);
                        });
                    });
				} catch(e) {}
			});
		}
	},

	/**
	 * Disable child option if master is switch off.
	 * 
	 * @param string
	 *            child ID of child option
	 * @param string
	 *            master ID of master option
	 */
	setDisabled : function(child, master) {
		document.id(child).setProperty('disabled', document.id(master).checked);
	},
    
    /**
     * Build complete date format from parts.
     * 
     * @param id master field
     */
    buildDateFormat : function(id) {	
        document.id(id).value = '';   
        document.id(id).getParent().getElements('select[id^=' + id + '_],input[id^=' + id + '_]').each(function(e) { // search format parts
            if (e.get('tag') == 'input' && e.value == '') { // empty separator
                document.id(id).value += ' '; // space at least
            } else {
                document.id(id).value += e.value; // concat all format elements into complete string
            }
        });
    }
};

try {
	/**
	 * Joomla! 1.6.x
	 */
	Joomla.submitbutton = function(pressbutton) {
		return ViewConfig.submitbutton(pressbutton);
	}
} catch (e) {
	/**
	 * Joomla! 1.5.x
	 */
	function submitbutton(pressbutton) {
		return ViewConfig.submitbutton(pressbutton);
	}
}