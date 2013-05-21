/**
 * Organization information table
 * 
 * @author Hans
 */

if (typeof(AutoMicrosite) == "undefined") {
	AutoMicrosite = {};
}
if (typeof(AutoMicrosite.Widget) == "undefined") {
	AutoMicrosite.Widget = {};
}

/**
 * Widget constructor
 */
AutoMicrosite.Widget.OrganizationInfo = function() {
	this.widgetId = null;
};

AutoMicrosite.Widget.OrganizationInfo.prototype = {

	/**
	 * Widget loaded
	 */
	onLoad: function() {
		this.widgetId = this.OpenAjax.getId();
		var thisWidget = this;

		this.OpenAjax.hub.subscribe("AutoMicrosite.Table.OrganizationData",
			function(topic, receivedData) {
				thisWidget.fillTable(receivedData);
			}
		);
	},
	
	/**
	 * Fill table with data
	 */
	fillTable: function(data) {
		var j, value;
		for (var i in data) {
			j = document.getElementById(this.widgetId + i);
			if (j) {
				value = data[i];
				if (i == "address") {
					value = this.cleanValue(value.street)
								+ ", " + this.cleanValue(value.postalCode) + " " + this.cleanValue(value.cityName)
								+ ", " + this.cleanValue(value.countryCode);
				} else {
					value = this.cleanValue(value);
				}
				j.innerHTML = value;
			}
		}
	},

	/**
	 * Remove proxy artifacts from the data
	 */
	cleanValue: function(value) {
		console.error(value);
		if (typeof(value) == "string" && value.substr(0, 1) == "{") {
			try {
				var jsObject = $.parseJSON(value);
				if (jsObject && jsObject["_value_"]) {
					value = jsObject["_value_"];
				}
			} catch (e) { }
		}
		return value;
	}

};