/**
 * SOAP access key distribution
 * 
 * @author Hans
 */

if (typeof(AutoMicrosite) == "undefined") {
	AutoMicrosite = {};
}
if (typeof(AutoMicrosite.Widget) == "undefined") {
	AutoMicrosite.Widget = {};
}

AutoMicrosite.Widget.Key = function() { };

AutoMicrosite.Widget.Key.prototype = {

	onLoad: function() {
		var thisWidget = this;
		this.OpenAjax.hub.subscribe("AutoMicrosite.BusinessRegister.QueryResponse", function(topic, receivedData) {
			thisWidget.publishKey();
		});
		this.publishKey();
	},

	publishKey: function() {
		var key = this.OpenAjax.getPropertyValue("key");
		this.OpenAjax.hub.publish("AutoMicrosite.SoapKey", {key: key});
	}

};