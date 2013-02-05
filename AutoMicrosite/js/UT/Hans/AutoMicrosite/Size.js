/**
 * Widget resizing component. Keeps the widgets appropriate for screen size.
 *
 * @author Hans
 */
define(["dojo/_base/declare", "dojo/dom", "dojo/dom-construct", "dojo/dom-style"
		, "dojo/window", "dojo/on", "UT/Hans/AutoMicrosite/Log"
		, "dojo/query", "dojo/dom-geometry"
		, "dojo/NodeList-traverse"]
	, function(declare, dom, domConstruct, domStyle, win, on, log, query, domGeom, nodeListTraverse) {
	return declare(null, {
		
		/**
		 * Widget data
		 */
		data: [],
		
		/**
		 * Widget placeholder elements
		 */
		placeholders: [],
		
		/**
		 * Visual widgets' OpenAjax widget objects
		 */
		visualWidgets: [],
		
		/**
		 * Constructor method
		 *
		 * @param string divMashupId ID of element where hub should be attached
		 */
		constructor: function(widgetData, placeholders, visualWidgets) {
			log("Size", "constructor");

			this.data = widgetData;
			this.placeholders = placeholders;
			this.visualWidgets = visualWidgets;
console.log("placeholders");
console.log(placeholders);
console.log(widgetData);
console.log(visualWidgets);
			log("Window dimensions", win.getBox());
		},
		
		/**
		 * Run widget resize
		 */
		run: function() {
			for (var i = 0; i < this.placeholders.length; i++) {
				this.processPlaceholder(this.placeholders[i]);
			}
			/*
			for (var i in this.visualWidgets) {
				this.calculateWidgetDimensions(this.visualWidgets[i]);
			}*/
		},
		
		getWidgetsInPlaceholder: function(placeholder) {
			var widgets = [];
			for (var i = 0; i < this.data.length; i++) {
				if (this.data[i].placeholder == placeholder) {
					widgets.push(this.data[i]);
				}
			}
			return widgets;
		},
		
		forEach: function(array, callback) {
			for (var i in array) {
				callback(array[i]);
			}
		},
		
		/**
		 * Process all placeholders
		 */
		processPlaceholder: function(placeholder) {
			//return ;
			var placeholderId = placeholder.getAttribute("itemid");
			
			var widgets = 0;
			var row;
			for (var i in this.data) {
				row = this.data[i];
				if (row.placeholder != placeholderId) continue;
				
				// Find widget manager element
				console.log(row);
				var widgetManager = row.openAjax;
				
				// TODO: some fancier logic, so it would be possible to have several widgets on the same page
				if (widgets === 0) {
					showWidget = widgetManager;
				}
				//widgetManager.OpenAjax._rootElement.style.display = "none";
				
				if (row.enabled) {
					this.calculateWidgetDimensions(row);
				}
				widgets++;
			}
		/*
			if (showWidget) {
				showWidget.OpenAjax._rootElement.style.display = "block";
				this.calculateWidgetDimensions(showWidget);
			}*/
 /*
			if (widgets > 1) {
				var menuWidget = this.getMenuWidget();
				if (menuWidget) {
					log("processPlaceholder", "Writing buttons property.");
					this.getMenuWidget().OpenAjax.setPropertyValue("buttons", menuItems);
				}
			}*/
		},
			
			
			
		calculateWidgetDimensions: function(widget) {
			var widgetManager = widget.openAjax;
			console.log("calculateWidgetDimensions");
			console.log(widgetManager);
			//widgetManager.OpenAjax._rootElement.style.width = win.getBox().w +"px";
			
			var placeholderWidgets = this.getWidgetsInPlaceholder(widget.placeholder);
			this.forEach(placeholderWidgets, function(w) {
				w.div.style.display = "none";
			});

			var widgetRootElement = query.NodeList();
			widgetRootElement.push(widgetManager.OpenAjax._rootElement);
			//widgetManager.OpenAjax._rootElement.style.display = "none";
			console.log(widgetRootElement.parent()[0]);
			var placeholderDimensions = domGeom.getContentBox(widgetRootElement.parent()[0]);
			//widgetManager.OpenAjax._rootElement.style.display = "block";
console.log(placeholderDimensions);

			this.forEach(placeholderWidgets, function(w) {
				if (w.enabled) {
					w.div.style.display = "block";
				}
			});

			// TODO: shouldn't it be 'requestSizeChange'?
			widgetManager.OpenAjax.adjustDimensions({
				width: placeholderDimensions.w,
				height: placeholderDimensions.h
			});

			//console.log(placeholderDimensions);
			//console.log(widgetManager.OpenAjax.getDimensions());
			//console.log(widgetManager.OpenAjax.getAvailableDimensions());
		},
			
		getMenuWidget: function() {
			// TODO: this info should probably come from server side, in case menu is used
			for (var i = 0; i < this.visualWidgets.length; i++) {
				if (this.visualWidgets[i].divMenu) { // TODO: really-really bad way to find menu
					return this.visualWidgets[i];
				}
			}
		},
				
		menuClick: function(widgetId) {
			console.log("opening widget "+ widgetId);
			
		}

	});
});