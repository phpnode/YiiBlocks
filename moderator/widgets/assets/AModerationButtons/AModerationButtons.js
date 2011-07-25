(function($){
    $.AModerationButtons = function(el, options){
        // To avoid scope issues, use 'base' instead of 'this'
        // to reference this class from internal events and functions.
        var base = this;

        // Access to jQuery and DOM versions of element
        base.$el = $(el);
        base.el = el;

        // Add a reverse reference to the DOM object
        base.$el.data("AModerationButtons", base);

        base.init = function(){
           
            base.options = $.extend({},$.AModerationButtons.defaultOptions, options);

            base.bindEvents();
        };
		/**
		 * Gets the approve button wrapped in a jQuery selector
		 */
		base.approveButton = function () {
			return $(base.$el.find(base.options.selectors.approve));
		};
		
		/**
		 * Gets the deny button wrapped in a jQuery selector
		 */
		base.denyButton = function () {
			return $(base.$el.find(base.options.selectors.deny));
		};
		
		/**
		 * Binds events to the moderation buttons
		 */
		base.bindEvents = function () {
			base.approveButton().bind("click", function(e) {
				$.ajax({
					url: base.approveButton().attr("href"),
					type: "POST",
					data: base.postData,
					success: base.handleResponse
				});
				e.preventDefault();
			});
			
			base.denyButton().bind("click", function(e) {
				$.ajax({
					url: base.denyButton().attr("href"),
					type: "POST",
					data: base.postData,
					success: base.handleResponse
				});
				e.preventDefault();
			});
		};
        /**
         * Handles the response from the server
         */
        base.handleResponse = function(res) {
        	if (res.status === "approved") {
        		base.approveButton().html("Approved");
        		base.denyButton().html("Deny");
        	}
        	else if (res.status === "denied") {
        		base.approveButton().html("Approve");
        		base.denyButton().html("Denied");
        	}
        	else {
        		alert("There was an error processing this request");
        	}
        };

        // Run initializer
        base.init();
    };

    $.AModerationButtons.defaultOptions = {
    	postData: {},
    	selectors: {
        	approve: 'a.button.approve',
        	deny: 'a.button.deny'
       }
    };

    $.fn.AModerationButtons = function(options){
        return this.each(function(){
            (new $.AModerationButtons(this, options));

                   // HAVE YOUR PLUGIN DO STUFF HERE

                   // END DOING STUFF

        });
    };

})(jQuery);