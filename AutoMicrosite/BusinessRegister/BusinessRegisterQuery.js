/**
 * Business register query widget logic
 */

if (typeof(AutoMicrosite) == "undefined") {
	AutoMicrosite = {};
}

AutoMicrosite.BusinessRegisterQuery = function() {};

AutoMicrosite.BusinessRegisterQuery.prototype = {

	/**
	 * Widget loaded
	 */
	onLoad: function() {
		var thisWidget = this;
		setTimeout(function() {
			thisWidget.query();
		}, 2000); // Delay the execution a bit
	},
	
	/**
	 * Widget property changed
	 */
	onChange: function() {
		this.query();
	},

	/**
	 * Query server for registry code
	 */
	query: function() {
		var name = this.OpenAjax.getPropertyValue("name");
		if (!name) return;
		var thisWidget = this;
		var serviceUrl = this.OpenAjax.rewriteURI("http://deepweb.ut.ee/automicrosite/BusinessRegister/BusinessRegisterQuery.php");
		$.post(serviceUrl, {name: name}, function(data) {
			if (data.error && data.error > 0) {
				console.log(data.message);
				alert("Error:\n" + data.message);
				return;
			}
			for (var i in data.businesses) {
				data.businesses[i].registrationCountryCode = "EE";
				data.businesses[i].isLegalEntity = "true";
				thisWidget.OpenAjax.hub.publish("AutoMicrosite.BusinessRegister.QueryResponse",
					data.businesses[i]);
			}
		}, "json");
	}

};