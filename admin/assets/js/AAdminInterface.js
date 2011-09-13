var AAdminInterface = (function(){
	var base = {
		elements: {

			content: '#content',
			container: '#main',
			sidebar: {
				selector: '#sidebar',
				elements: {
					collapseButton: {
						selector: "#sidebar a.collapsible",
						cookieName: "AdminSidebarCollapsed",
						init: function() {
							var cookieVal = jQuery.cookie(base.elements.sidebar.elements.collapseButton.cookieName);
							if (cookieVal) {
								jQuery(base.elements.sidebar.selector).addClass("collapsed");
								base.elements.sidebar.elements.collapseButton.updateMenuLinks();
							}

						},
						updateMenuLinks: function() {
							jQuery(base.elements.sidebar.selector + " a.icon").each(function(){

								if ($(this).attr("title") === undefined) {
									$(this).attr("title",$(this).html());
								}
							});
						},
						events: {
							click: function(e) {
								var sidebar = jQuery(base.elements.sidebar.selector);
								if (sidebar.hasClass("collapsed")) {
									// delete the cookie
									jQuery.cookie(base.elements.sidebar.elements.collapseButton.cookieName, null,{
										expires: 7,
										path: "/"
									});
								}
								else {
									jQuery.cookie(base.elements.sidebar.elements.collapseButton.cookieName, true, {
										expires: 7,
										path: "/"
									});
									base.elements.sidebar.elements.collapseButton.updateMenuLinks();
								}
								sidebar.toggleClass("collapsed");
								e.preventDefault();
							}
						}
					}
				}
			}

		},
		processElement: function(element) {
			if (typeof element !== "object") {
				return;
			}
			if (element.events !== undefined) {
				var eventName;
				for(eventName in element.events) {
					if (element.events.hasOwnProperty(eventName)) {
						$(element.selector).bind(eventName, element.events[eventName]);
					}
				}
			}
			if (element.elements !== undefined) {
				var childName;
				for(childName in element.elements) {
					if (element.elements.hasOwnProperty(childName)) {
						base.processElement(element.elements[childName]);
					}
				}
			}
			if (element.init !== undefined) {
				element.init();
			}
		},
		run: function() {
			base.processElement(base);
		}
	};

	return base;
}());