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
AutoMicrosite.OrganizationInfo.GoogleMaps = function() {
	this.widgetId = null;
};

AutoMicrosite.Widget.OrganizationInfo.prototype = {

	/**
	 * Widget loaded
	 */
	onLoad: function() {
		this.widgetId = this.OpenAjax.getId();

		this.OpenAjax.hub.subscribe("AutoMicrosite.Table.OrganizationData",
			(function(topic, receivedData) {
				this.fillTable(receivedData);
			}).bind(this)
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
				j.innerHTML = data[i];
			}
		}
	}

};