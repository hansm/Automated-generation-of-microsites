if (typeof(AutoMicrosite) == "undefined") {
	AutoMicrosite = {};
}
if (typeof(AutoMicrosite.Widget) == "undefined") {
	AutoMicrosite.Widget = {};
}

/**
 * Widget constructor
 */
AutoMicrosite.Widget.Summary = function() {
	this.widgetId = null;
};

AutoMicrosite.Widget.Summary.prototype = {

	onLoad: function() {
		this.widgetId = this.OpenAjax.getId();
		var thisWidget = this;

		this.OpenAjax.hub.subscribe("AutoMicrosite.Data.Summary", function(topic, receivedData) {
			var data = receivedData.data;
			var divContent = document.getElementById(thisWidget.widgetId + "content");
			divContent.innerHTML = "";

			var divSummary;
			for (var i = 0; i < data.length; i++) {
				divSummary = document.createElement("p");
				divSummary.appendChild(document.createTextNode(data[i].label +": "+ data[i].value))
				divContent.appendChild(divSummary);
			}
		});
	}
};