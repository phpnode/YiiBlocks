(function($){
    $.AVoteButtons = function(el, options){
        // To avoid scope issues, use 'base' instead of 'this'
        // to reference this class from internal events and functions.
        var base = this;

        // Access to jQuery and DOM versions of element
        base.$el = $(el);
        base.el = el;

        // Add a reverse reference to the DOM object
        base.$el.data("AVoteButtons", base);

        base.init = function(){
           
            base.options = $.extend({},$.AVoteButtons.defaultOptions, options);

            base.bindEvents();
        };
		/**
		 * Gets the upvote button wrapped in a jQuery selector
		 */
		base.upvoteButton = function () {
			return $(base.$el.find(base.options.selectors.upvote));
		};
		
		/**
		 * Gets the downvote button wrapped in a jQuery selector
		 */
		base.downvoteButton = function () {
			return $(base.$el.find(base.options.selectors.downvote));
		};
		
		/**
		 * Binds events to the moderation buttons
		 */
		base.bindEvents = function () {
			base.upvoteButton().bind("click", function(e) {
				$.ajax({
					url: base.upvoteButton().attr("href"),
					type: "POST",
					data: base.postData,
					success: base.handleResponse
				});
				e.preventDefault();
			});
			
			base.downvoteButton().bind("click", function(e) {
				$.ajax({
					url: base.downvoteButton().attr("href"),
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
        	var summary = base.options.labels.summary;
        	summary = summary.replace(/{\w+}/g, function (toReplace) {
        		items = {
        			"{score}": res.score,
        			"{upvotes}": res.totalUpvotes,
        			"{downvotes}": res.totalDownvotes
        		};
        		return items[toReplace];
        	});
        	base.$el.find(base.options.selectors.summary).html(summary);
        	if (res.status == "upvoted") {
        		base.upvoteButton().html(base.options.labels.upvoted);
        		base.downvoteButton().html(base.options.labels.downvote);
        	}
        	else if (res.status == "downvoted") {
        		base.upvoteButton().html(base.options.labels.upvote);
        		base.downvoteButton().html(base.options.labels.downvoted);
        	}
        	else {
        		base.upvoteButton().html(base.options.labels.upvote);
        		base.downvoteButton().html(base.options.labels.downvote);
        	}
        };

        // Run initializer
        base.init();
    };

    $.AVoteButtons.defaultOptions = {
    	postData: {},
    	selectors: {
        	upvote: 'a.button.upvote',
        	downvote: 'a.button.downvote',
        	summary: 'span.score'
      	},
      	labels: {
      		upvote: 'Upvote',
      		downvote: 'Downvote',
      		upvoted: 'Upvoted',
      		downvoted: 'Downvoted',
      		summary: '<span class="score" title="{upvotes} liked it, {downvotes} didn\'t like it">{score}</span>'
      	}
    };

    $.fn.AVoteButtons = function(options){
        return this.each(function(){
            (new $.AVoteButtons(this, options));

                   // HAVE YOUR PLUGIN DO STUFF HERE

                   // END DOING STUFF

        });
    };

})(jQuery);