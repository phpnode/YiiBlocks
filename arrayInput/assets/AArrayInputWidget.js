/*global $, jQuery, alert, clearInterval, clearTimeout, document, event, frames, history, Image, location, name, navigator, Option, parent, screen, setInterval, setTimeout, window, XMLHttpRequest */

(function($){
	$.arrayInputWidget = function(el, options){
		// To avoid scope issues, use 'base' instead of 'this'
		// to reference this class from internal events and functions.
		var base = this;

		// Access to jQuery and DOM versions of element
		base.$el = $(el);
		base.el = el;

		// Add a reverse reference to the DOM object
		base.$el.data("arrayInputWidget", base);

		base.init = function(){
			
			base.options = $.extend({},$.arrayInputWidget.defaultOptions, options);
			$(el).find("a.flattenArray").first().remove(); // can't flatten the base array
			$(el).find("a.add").live("click", base.addItem);
			$(el).find("a.delete").live("click", base.deleteItem);
			$(el).find("a.flattenArray").live("click", base.flattenArray);
			$(el).find("a.convertArray").live("click", base.convertArray);
			$(el).find("th." + base.options.keyClass + " input").live("keyup", base.changeKeys);
		};

		base.addItem = function(e){
			var table, row, prefix;
			table = $(this).parents("table." + base.options.tableClass).first();
			prefix = table.attr("rel");
			row = $("<tr></tr>");
			row.append("<th class='" + base.options.keyClass + "'><input type='text' /></th>");
			row.append("<td class='" + base.options.valueClass + "'><input type='text' name='" + prefix + "[]' /></td>");
			row.append("<th class='actions'><a href='#' class='icon delete iconOnly' title='Delete'>&nbsp;</a></th>");
			row.append("<th class='actions'><a href='#' class='icon convertArray iconOnly' title='Convert to array'>&nbsp;</a></th>");
			$(table).append(row);
			if (!$(table).find("td.empty").first().hasClass("hidden")) {
				$(table).find("td.empty").first().addClass("hidden");
			}
			e.preventDefault();
		};
		base.deleteItem = function(e){
			var table, row;
			table = $(this).parents("table." + base.options.tableClass).first();
			row = $(this).parents("tr").first();
			row.remove();
			
			if ($(table).find("tbody tr").length === 1) {
				$(table).find("td.empty").first().removeClass("hidden");
			}
			e.preventDefault();
		};
		
		base.flattenArray = function(e){
			var table, rows, values = [], element, td, tr;
			
			table = $(this).parents("table." + base.options.tableClass).first();
			td = table.parents("td").first();
			td.attr("colSpan", "1");
			
			tr = td.parents("tr").first().append("<th class='actions'><a href='#' class='icon convertArray iconOnly' title='Convert to array'>&nbsp;</a></th>");
			
			table.find("tbody tr").not(".empty").each(function() {
				var item = $(this).children("td." + base.options.valueClass).find("input");
				if (item.length !== -1 && item.val() !== undefined) {
					values.push(item.val());
				}
			});
			element = $("<input type='text' name='" + $(table).attr("rel") + "' />");
			element.val(values.join(","));
			table.replaceWith(element);
			
			
			e.preventDefault();
		};
		
		base.convertArray = function(e){
			var table, thead, tbody, row, rows = [], values = [], element, td, i, limit, hide = false;
			td = $(this).parents("tr").first().find("td." + base.options.valueClass).first();
			
			element = td.find("input").first();
			table = $("<table class='" + base.options.tableClass + "'></table>");
			table.attr("rel", element.attr("name"));
			thead = $("<thead></thead>");
			tbody = $("<tbody></tbody>");
			row = $("<tr></tr>");
			row.append("<th>Key</th>");
			row.append("<th>Value</th>");
			row.append("<th class='actions'><a href='#' class='icon add iconOnly' title='Add'>&nbsp;</a></th>");
			row.append("<th class='actions'><a href='#' class='icon flattenArray iconOnly' title='Flatten this array'>&nbsp;</a></th>");
			thead.append(row);
			if (element.val() === undefined) {
				values = [];
			}
			else {
				values = element.val().split(",");
			}
			
			limit = values.length;
			
			for (i = 0; i < limit; i++) {
				if (values[i].length > 0) {
					row = $("<tr></tr>");
					row.append("<th class='" + base.options.keyClass + "'><input type='text' /></th>");
					row.append("<td class='" + base.options.valueClass + "'><input type='text' name='" + element.attr("name") + "[]' value='" +  values[i] + "' /></td>");
					row.append("<th class='actions'><a href='#' class='icon delete iconOnly' title='Delete'>&nbsp;</a></th>");
					row.append("<th class='actions'><a href='#' class='icon convertArray iconOnly' title='Convert to array'>&nbsp;</a></th>");
					tbody.append(row);
					hide = true;
				}
			}
			tbody.append($("<tr class='empty'><td colspan='4' class='empty " + (hide ? "hidden" : "") + "'>Empty array</td></tr>"));
			table.append(thead);
			table.append(tbody);
			td.html(table);
			td.attr("colspan", 2);
			$(this).parents("th.actions").first().remove();
			e.preventDefault();
		};
		base.changeKeys = function (e) {
			var tr, parentTable, tables, inputs, path, pathParts, partCount;
			tr = $(this).parents("tr").first();
			inputs = tr.find("td." + base.options.valueClass).not(".empty").find("input");
			tables = tr.find("table." + base.options.tableClass);
			parentTable = tr.parents("table." + base.options.tableClass);
			path = parentTable.attr("rel") + "[" + $(this).val() + "]";
			pathParts = path.split(/\]\[|\[|\]/);
			pathParts.pop();
			partCount = pathParts.length;
			if (tables.length > 0) {
				tables.each(function () {
					var oldPath = $(this).attr("rel"), newPath, parts, i;
					parts = oldPath.split(/\]\[|\[|\]/);
					parts.pop();
					for (i = 0; i < partCount; i++) {
						parts[i] = pathParts[i];
					}
					newPath = parts.shift();
					for (i = 0; i < parts.length; i++) {
						newPath += "[" + parts[i] + "]";
					}
					$(this).attr("rel", newPath);
				});
				inputs.each(function () {
					$(this).attr("name", $(this).parents("table." + base.options.tableClass).attr("rel") + "[" + $(this).parents("tr").find("th." + base.options.keyClass).first().val() + "]");
				});
			}
			else {
				inputs.first().attr("name", path);
			}
			
		};
		// Run initializer
		base.init();
	};
	
	$.arrayInputWidget.defaultOptions = {
		tableClass: "array",
		keyClass: "array-key",
		valueClass: "array-value",
		multiClass: "array-multi"
	};

	$.fn.arrayInputWidget = function(options){
		return this.each(function(){
			(new $.arrayInputWidget(this, options));
		});
	};

})(jQuery);