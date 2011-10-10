(function($){
    $.ACalendarWidget = function(el, options){
        // To avoid scope issues, use 'base' instead of 'this'
        // to reference this class from internal events and functions.
        var base = this, calendar;

        // Access to jQuery and DOM versions of element
        base.$el = $(el);
        base.el = el;
		calendar = base.$el.fullCalendar;
        // Add a reverse reference to the DOM object
        base.$el.data("ACalendarWidget", base);

        base.init = function(){

            base.options = $.extend({},$.ACalendarWidget.defaultOptions, options);

        };
        /**
         * Gets the approve button wrapped in a jQuery selector
         */
        base.approveButton = function () {
            return $(base.$el.find(base.options.selectors.approve));
        };

		base.dayClick = function(date, allDay, jsEvent, view) {
			calendar("gotoDate",date);
			if (view.name == "month") {
				calendar("changeView","agendaDay");
			}
		};

		base.select = function(start, end, allDay) {
			var event = $.extend({}, base.options.newEvent), className = "event-" + (new Date).getTime();
			event.start = start;
			event.end = end;
			event.allDay = allDay;
			event.className = className;
			calendar("renderEvent", event);
			$("." + className).qtip({
				overwrite: false,
				content: {
					text: "<span class='loading'>Loading...</span>",
					ajax: {
						url: base.options.createUrl,
						data: event
					}
				},
				position: {
					my: 'bottom center',
					at: 'top center',
					adjust: {
						method: "flip"
					}
				},
				style: {
					classes: 'ui-tooltip-light ui-tooltip-shadow',
					tip: {
						border: 2,
						width: 44,
						height: 33
					}
				},
				show: {
					event: 'click',
					solo: true,
					ready: true,
					modal: {
						on: true
					}
				},
				hide: {
					event: 'unfocus'
				}
			});
		};
        // Run initializer
        base.init();
    };
    $.ACalendarWidget.defaultOptions = {
		newEvent: {
			title: "New Event"
		}

    };
	$.fn.ACalendarWidget = function(options){
        return this.each(function(){
            (new $.ACalendarWidget(this, options));

                   // HAVE YOUR PLUGIN DO STUFF HERE

                   // END DOING STUFF

        });
    };
})(jQuery);