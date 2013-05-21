/**
 * Organization dept information table
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
AutoMicrosite.Widget.OrganizationDeptInfo = function() {
	this.widgetId = null;
};

AutoMicrosite.Widget.OrganizationDeptInfo.prototype = {

	/**
	 * Widget loaded
	 */
	onLoad: function() {
		var thisWidget = this;
		this.widgetId = this.OpenAjax.getId();
		this.OpenAjax.hub.subscribe("AutoMicrosite.Table.OrganizationData.Dept",
			function (topic, receivedData) {
				thisWidget.fillTable(receivedData);
			}
		);
	},
	
	/**
	 * Fill table with data
	 */
	fillTable: function(data) {
		var j;
		for (var i in data) {
			j = document.getElementById(this.widgetId + i);
			if (j) {
				j.innerHTML = this.cleanValue(data[i]);
			}
		}
	},

	/**
	 * Remove proxy artifacts from the data
	 */
	cleanValue: function(value) {
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