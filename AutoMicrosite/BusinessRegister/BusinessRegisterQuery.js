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
		$.getJSON("BusinessRegisterQuery.php", {name: name}, function(data) {
			if (data.error && data.error > 0) {
				// TODO: error
				return;
			}
			for (var i in data.businesses) {
				this.OpenAjax.hub.publish("AutoMicrosite.BusinessRegister.QueryResponse", data.businesses[i]);
			}
		});
	}

};