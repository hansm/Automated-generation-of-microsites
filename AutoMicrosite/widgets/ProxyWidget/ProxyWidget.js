/**
 * Proxy widget JS
 */

dojo.require("dojo.io.script");
dojo.require("dojox.rpc.Service");
dojo.require("dojox.rpc.JsonRPC");

if (typeof(AutoMicrosite) == "undefined") {
	AutoMicrosite = {};
}
if (typeof(AutoMicrosite.Widget) == "undefined") {
	AutoMicrosite.Widget = {};
}

/**
 * Widget constructor
 */
AutoMicrosite.Widget.ProxyWidget = function() {
	this.widgetId = null;
};

AutoMicrosite.Widget.ProxyWidget.prototype = {

	/**
	 * Widget loaded
	 */
	onLoad: function() {
		this.widgetId = this.OpenAjax.getId();
		var thisWidget = this;

		this.OpenAjax.hub.subscribe("ee.stacc.transformer.hasfinished",
			this.onTransformerFinished.bind(this),
			null, null,
			{PageBus: { cache: true }}
		);
		this.OpenAjax.hub.subscribe(this.getTopic("input"), function (topic, data, subscriberData) {
				// this.onData.bind(this)
				thisWidget.onData(topic, data, subscriberData);
			}
			, null, null,
			{PageBus: { cache: true }}
		);
	},

	/**
	 * Proxy service root URL
	 */
	getProxyUrl: function() {
		return this.OpenAjax.getPropertyValue("proxy");
	},

	getWsdl: function() {
		return this.OpenAjax.getPropertyValue("wsdl");
	},

	getOperation: function() {
		return this.OpenAjax.getPropertyValue("operation");
	},

	getMappingsUrl: function() {
		return this.getProxyUrl() + "mapping?wsdl=" + this.getWsdl()
				+ "&operation=" + this.getOperation();
	},

	getSmdUrl: function() {
		return this.getProxyUrl() + "smd?wsdl=" + this.getWsdl()
				+ "&operation=" + this.getOperation();
	},

	getTopic: function(type) {
		var clearedWsdl = this.getWsdl().replace(/\W/g, '-');
		var topic = "ee.stacc.soapwidgetgenerator." + clearedWsdl + "."
						+ this.getOperation() + "." + type;
		return topic;
	},

	onData: function(topic, publisherData, subscriberData) {
		this.onSoapServiceData(topic, publisherData, subscriberData, this.getSmdUrl(), this.OpenAjax.hub, this.getOperation(), this.getTopic('output'));
	},

	publishMappingToTransformerWidget: function() {
		this.OpenAjax.hub.publish("ee.stacc.transformer.mapping.add.url",
			this.getMappingsUrl()
		);
	},

	onTransformerFinished: function(topic, publisherData, subscriberData) {
		this.publishMappingToTransformerWidget();
	},

	onSoapServiceData: function(topic, publisherData, subscriberData, smdUrl, hubClient, operation, outputTopic) {
		this.loadSmd(smdUrl, publisherData, hubClient, operation, outputTopic);
	},

	loadSmd: function(smdUrl, publisherData, hubClient, operation, outputTopic) {
		var thisWidget = this;
		var smdDeferred = dojo.io.script.get({
			url:	smdUrl,
			jsonp:	"callback"
		});
		smdDeferred.addCallback(function(result) {
			thisWidget.callService(result, publisherData, operation, hubClient, outputTopic);
		});
	},

	callService: function(smd, requestData, operation, hubClient, outputTopic) {
		var services = new dojox.rpc.Service(smd);
		// lets try to specify the request ID
		var d = new Date();
		services._requestId = d.valueOf();
		var deferred = services[operation](requestData);
		deferred.addCallback(function(result) {
			hubClient.publish(outputTopic, result);
		});
		deferred.addErrback(function () {
			alert("Error");
		});

		return deferred;
	}
};