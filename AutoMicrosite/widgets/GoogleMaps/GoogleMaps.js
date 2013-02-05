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



		this.OpenAjax.hub.subscribe("AutoMicrosite.GoogleMaps",
			(function(topic, receivedData) {
				this.addMarker(receivedData);
			}).bind(this)
		);
	},
	
	apiLoaded: function() {
		this.geocoder = new google.maps.Geocoder();
		var latlng = new google.maps.LatLng(26.7, 58.3);
		var mapOptions = {
			zoom: 8,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.HYBRID
		}
		this.map = new google.maps.Map(
					document.getElementById(this.widgetId +"map"), mapOptions);
	},
	
	/**
	 * Add marker to map and center to marker
	 */
	addMarker: function(data) {
		if (!this.map) {
			// TODO: handle this situation
			return;
		}
		
		var geocoderRequest = {
			"address": data.countryCode +", "+ data.city +" "+ data.postalCode +", "+ data.street,
			"region": data.countryCode
		};
		this.geocoder.geocode(geocoderRequest, (function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				this.map.setCenter(results[0].geometry.location);
				var marker = new google.maps.Marker({
					map: map,
					position: results[0].geometry.location
				});
			} else {
				alert("Geocode not found: "+ status);
			}
        }).bind(this));
	}

};