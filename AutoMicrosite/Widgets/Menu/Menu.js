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
		//var thisWidget = this;
		this.divMenu = document.getElementById(this.widgetId +"menu");
		console.log(this.widgetId + ":onLoad");
		console.log(this.OpenAjax.getPropertyValue("buttons"));
		this.drawMenu();

		this.OpenAjax.hub.subscribe("AutoMicrosite.MenuClick", function(topic, data) {
				console.log("Menu received:");
				console.log(topic);
				console.log(data);
			});
	},

	/**
	 * Menu buttons change
	 */
	onChange: function() {
		console.log("Some property changed");
		console.log(this.OpenAjax.getPropertyValue("buttons"));
		this.drawMenu();
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
		var buttons = this.OpenAjax.getPropertyValue("buttons");
		this.divMenu.innerHTML = "";

		if (buttons == undefined || buttons.length == 0) {
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
				a.mashupAction = button.href;
				a.onclick = function(data) {
					thisWidget.buttonClick(data);
					return false;
				}.bind(a, button.href);
			}
			
			this.divMenu.appendChild(a);
		}
	}
};