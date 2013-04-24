/**
 * Handle widget navigation
 *
 * @author Hans
 */
define(["dojo/_base/declare", "dojo/dom", "dojo/dom-construct", "dojo/dom-style"
		, "dojo/window"]
	, function(declare, dom, domConstruct, domStyle, win) {
	return declare(null, {

		/**
		 * Visual widgets
		 */
        widgets: null,

		/**
		 * Size handler object
		 */
		size: null,

		/**
		 * @param object visualWidgets visual widgets in the mashup
		 * @param UT/Hans/AutoMicrosite/Size size mashup size handler
		 */
		constructor: function(visualWidgets, size) {
            this.widgets = visualWidgets;
			this.size = size;
		},

		/**
		 * Build navigation
		 */
		build: function() {
			console.log("Building navigation");
			var menuWidget = this.getMenuWidget();
			if (!menuWidget) return;

			var widget, menuItems;

			// Hide widgets that require a separate page
			for (var i = 0; i < this.widgets.length; i++) {
				widget = this.widgets[i];
				if (!widget.separatePage) continue;

				// Disable widget
				widget.enabled = false;
				domStyle.set(widget.div, {
					display: "none"
				});

				// Add to menu
				menuItems = menuWidget.openAjax.OpenAjax.getPropertyValue("buttons");
				if (!menuItems) {
					var titleElement = document.getElementsByTagName('title')[0];
					menuItems = [{
						label: (titleElement && titleElement.innerHTML ?
									titleElement.innerHTML : 'Home'),
						href: {widget: null, placeholder: widget.placeholder}
					}];
				}

				menuItems.push({
					label: (widget.title ? widget.title : "Widget " + (i + 1)),
					href: {widget: widget.id, placeholder: widget.placeholder}
				});

				menuWidget.openAjax.OpenAjax.setPropertyValue("buttons", menuItems);
			}

			// TODO: if all widgets in a placeholder are "separatePage" then show the first one
		},

		/**
		 * Get menu widget
		 */
		getMenuWidget: function() {
			for (var i = 0; i < this.widgets.length; i++) {
				if (this.widgets[i].isMenuWidget) {
					return this.widgets[i];
				}
			}

			return null;
		},

		/**
		 * Click event on menu widget
		 */
		clickMenu: function(data) {
			//
			this.size.run();
		}

	});
});