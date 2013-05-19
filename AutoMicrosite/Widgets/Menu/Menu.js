/**
 * Menu widget JavaScript code
 */

// initiate objects
if (typeof(AutoMicrosite) == "undefined") {
	AutoMicrosite = {};
}
if (typeof(AutoMicrosite.Widget) == "undefined") {
	AutoMicrosite.Widget = {};
}

/**
* Widget constructor
*/
AutoMicrosite.Widget.Menu = function() {
	this.divMenu = null;
	this.widgetId = null;
};

AutoMicrosite.Widget.Menu.prototype = {

	/**
	 * Widget loaded
	 */
	onLoad: function() {
		this.widgetId = this.OpenAjax.getId();
		this.divMenu = document.getElementById(this.widgetId + "menu");
		this.drawMenu();
	},

	/**
	 * Menu buttons change
	 */
	onChange: function() {
		this.drawMenu();
	},

	/**
	 * Resize menu items
	 */
	onSizeChanged: function() {
		var menuHeight = this.divMenu.clientHeight;
		var menuFontSize = menuHeight / 2;
		var menuElements = this.divMenu.childNodes;
		for (var i = 0; i < menuElements.length; i++) {
			menuElements[i].style.lineHeight = menuHeight + "px";
			menuElements[i].style.fontSize = menuFontSize + "px";
		}
	},

	/**
	 * Click on a button in menu
	 */
	buttonClick: function(button) {
		this.OpenAjax.hub.publish("AutoMicrosite.MenuClick", button);
	},

	/**
	 * Draw menu buttons
	 */
	drawMenu: function() {
		var thisWidget = this;
		try {
			var buttons = this.OpenAjax.getPropertyValue("buttons");
		} catch (e) {
			console.error("Menu widget error: " + e);
			return;
		}

		this.divMenu.innerHTML = "";
		if (!buttons || buttons.length == 0) {
			return;
		}

		var button, a;
		for (var i in buttons) {
			button = buttons[i];

			a = document.createElement("a");
			a.innerHTML = button.label;

			if (typeof button.href == "string" && button.href.match(/^http:\/\//i)) {
				a.href = button.href;
				a.target = "_blank";
			} else if (typeof button.href == "object") {
				a.href = "#"+ button.href.widget;
				a.onclick = (function(data) {
					thisWidget.buttonClick(data);
					return false;
				}).bind(a, button.href);
			}

			this.divMenu.appendChild(a);
		}

		this.onSizeChanged();
	}
};