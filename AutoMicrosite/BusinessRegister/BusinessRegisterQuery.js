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
		this.query();
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
		var serviceUrl = this.OpenAjax.rewriteURI("http://automicrosite.maesalu.com/BusinessRegister/BusinessRegisterQuery.php");
		$.post(serviceUrl, {name: name}, function(data) {
			if (data.error && data.error > 0) {
				console.log(data.message);
				alert("Error:\n" + data.message);
				return;
			}
			for (var i in data.businesses) {
				thisWidget.OpenAjax.hub.publish("AutoMicrosite.BusinessRegister.QueryResponse",
					data.businesses[i]);
			}
		}, "json");
	}

};