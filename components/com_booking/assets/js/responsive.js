document.id(window).addEvent('domready', function() {
	BookingResponsive();
});

function BookingResponsive() {
	/* 
	 * Function set width and height for necessary elements
	 * and then uses nicescroll (jQuery) library for touch
	 * scrolling through days in week.	 
	 * 
	 */
	
	// prepare necessary styles
	var weekDaysNo = 0;
	var weekDaysWidth = 0;
	var maxWeekDaysHeight = 0;
	if (document.id('weekDays')) {
		document.id('weekDays').getChildren().each(function(el){
			if (el.hasClass('boxesDay')) {
				weekDaysNo++;
				weekDaysWidth += el.getSize().x + 10;
				el.id = 'weekDay-'+weekDaysNo;
				if (el.getSize().y > maxWeekDaysHeight) {
					maxWeekDaysHeight = el.getSize().y;
				}
			}
		});
		
		document.id('weekDays').setStyle('min-width', weekDaysWidth);
		//document.id('weekDays').setStyle('height', maxWeekDaysHeight);
		document.id('weekDaysScroller').setStyle('height', maxWeekDaysHeight);
		
		if ((typeof(window.jQuery)!="undefined") && document.id("weekDaysScroller")) {
			(function($){
				var nicesx = $("#weekDaysScroller").niceScroll("#weekDays",{touchbehavior:true,cursorcolor:"#FF00FF",cursoropacitymax:0.6,cursorwidth:24,usetransition:true,hwacceleration:true,autohidemode:"hidden"});
			})(jQuery);
		} else {
			alert(LGJQueryIsNotLoaded);
		}
	}
	
	/* 
	 * Function uses nicescroll (jQuery) library for touch
	 * scrolling through image in photogallery.	 
	 * 
	 */ 
	
	if ((typeof(window.jQuery)!="undefined")) {
		if (document.id("photogallery")) {
			(function($){
				var nicesx = $("#photogallery").niceScroll("#photogallery-images",{touchbehavior:true,cursorcolor:"#FF00FF",cursoropacitymax:0.6,cursorwidth:24,usetransition:true,hwacceleration:true,autohidemode:"hidden"});
			})(jQuery);
		}
	} else {
		alert(LGJQueryIsNotLoaded);
	}
}