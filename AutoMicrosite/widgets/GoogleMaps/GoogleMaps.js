/**
 * Google Maps API widget JavaScript
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
AutoMicrosite.Widget.GoogleMaps = function() {
	this.map = null;
	this.geocoder = null;
	this.widgetId = null;
};

AutoMicrosite.Widget.GoogleMaps.prototype = {

	/**
	 * Widget loaded
	 */
	onLoad: function() {
		this.widgetId = this.OpenAjax.getId();
		var thisWidget = this;
		
		google.load("maps", "3", {
			other_params: "sensor=false",
			callback: function() {
				thisWidget.apiLoaded();
			}
		});

		this.OpenAjax.hub.subscribe("AutoMicrosite.GoogleMaps.Data", function(topic, receivedData) {
			thisWidget.addMarker(receivedData);
		});
	},

	/**
	 * Maps API library has finished loading, draw map
	 */
	apiLoaded: function() {
		this.geocoder = new google.maps.Geocoder();
		var latlng = new google.maps.LatLng(26.7, 58.3);
		var mapOptions = {
			zoom: 8,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.HYBRID
		};
		this.map = new google.maps.Map(
			document.getElementById(this.widgetId + "map"),
			mapOptions
		);
	},
	
	/**
	 * Add marker to map and center to marker
	 */
	addMarker: function(data) {
		var thisWidget = this;
		// No map yet, so try again in a while
		if (!this.map) {
			setTimeout(function() {
				thisWidget.addMarker(data);
			}, 1000);
			return;
		}

		var geocoderRequest = {
			address: this.cleanValue(data.countryCode)
						+ ", " + this.cleanValue(data.city)
						+ " " + this.cleanValue(data.postalCode)
						+ ", " + this.cleanValue(data.street),
			region: this.cleanValue(data.countryCode)
		};

		this.geocoder.geocode(geocoderRequest, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				thisWidget.map.setCenter(results[0].geometry.location);
				var marker = new google.maps.Marker({
					map: thisWidget.map,
					position: results[0].geometry.location
				});
			} else {
				alert("Geocode not found: "+ status);
			}
        });
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