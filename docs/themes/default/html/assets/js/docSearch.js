var DocSearch = {
	currentIndex: 0,
	
	init: function () {
		$("#searchBox").bind("keyup", function(e){
			if (e.which === 40) {
				DocSearch.currentIndex = 0;
				DocSearch.selectItem();
			}
			else {
				DocSearch.filter();
				DocSearch.render();
			}
		});
		$("#searchResults li").live("keyup", function(e){
			switch (e.which) {
				case 38:
					// up arrow
					DocSearch.currentIndex--;
					DocSearch.selectItem();
					break;
				case 40:
					// down arrow
					DocSearch.currentIndex++;
					DocSearch.selectItem();
					break;
			}
		});
	},
	
	
	/**
	 * Holds the docs data
	 */
	data: [],
	/**
	 * Adds data to the docs search
	 */
	addData: function(data) {
		
		DocSearch.data = data;
		DocSearch.filter();
		DocSearch.render();
	},
	/**
	 * Filters the data
	 */
	filter: function() {
		var searchBox = $("#searchBox"), i, limit = DocSearch.data.length, item;
		
		for(i = 0; i < limit; i++) {
			item = DocSearch.data[i];
			if (searchBox.val() == "") {
				if (item.type == "class" || item.type == "interface" || item.type == "function") {
					item.visible = true;
				}
				else {
					item.visible = false;
				}
			}
			else {
				if (
					((item.type == "class" || item.type == "interface" || item.type == "function") || searchBox.val().indexOf(".") !== -1) &&
					item.label.toLowerCase().indexOf(searchBox.val().toLowerCase()) !== -1) {
					item.visible = true;
				}
				else {
					item.visible = false;
				}
			}
		}
	},
	/**
	 * Renders the data
	 */
	render: function() {
		var container = $("#searchResults"), item;
		container.html("");
		var i, limit = DocSearch.data.length;
		for(i = 0; i < limit; i++) {
			item = DocSearch.data[i];
			if (item.visible) {
				container.append("<li><a href='" + item.value + "' class='docItem'>" + item.label +"</a><p>" + item.introduction + "</p></li>");
			}
		}
	},
	
	selectItem: function () {
		var i, limit, results = $("#searchResults li");
		limit = results.length;
		if (DocSearch.currentIndex > limit) {
			DocSearch.currentIndex = 0;
		}
		else if (DocSearch.currentIndex < 0) {
			$("#searchBox").focus();
		}
		for (i = 0; i < limit; i++) {
			item = $(results[i]);
			if (i === DocSearch.currentIndex) {
				item.addClass("selected");
				item.find("a.docItem").focus();
			}
			else {
				item.removeClass("selected");
			}
		}
	}
	
};

DocSearch.init();
