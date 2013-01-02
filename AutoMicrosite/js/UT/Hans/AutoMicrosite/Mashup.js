/**
 * Mashup handling class
 *
 * @author Hans
 */
define(["dojo/_base/declare", "dojo/dom", "dojo/dom-construct", "dojo/dom-style"
		, "dojo/window", "dojo/on", "dojo/query", "dojo/dom-attr"
		, "UT/Hans/AutoMicrosite/Size", "UT/Hans/AutoMicrosite/Log"
		, "UT/Hans/AutoMicrosite/WidgetLoader"]
	, function(declare, dom, domConstruct, domStyle, win, on, domQuery, domAttr, SizeHandler, log, Loader) {
	return declare(null, {

		/**
		 * Template placeholder itemtype
		 */
		TEMPLATE_PLACEHOLDER: "http://automicrosite.maesalu.com/TemplatePlaceholder",

		widgetData: [],

		widgetIdPrefix: "widget",

		divMashup: null,

		/**
		 * OpenAjax widget loader object
		 */
		openAjaxLoader: null,

		/**
		 * OpenAjax hub object
		 */
		hub: null,

		/**
		 * Widget objects
		 */
		widgets: [],

		/**
		 * Template placeholders for the widgets
		 */
		placeholders: [],

		/**
		 * Widget resize handler
		 */
		size: null,

		loader: null,

		loadingMessage: null,

		dataWidgets: [],

		visualWidgets: [],

		templateData: [],

		/**
		 * Constructor method
		 *
		 * @param string divMashupId ID of element where hub should be attached
		 */
		constructor: function(divMashupId, widgetData, templateData) {
			console.log("widgetData");
			console.log(widgetData);
			console.log("templateData");
			console.log(templateData);

			this.divMashup = dom.byId(divMashupId);
			this.widgetData = widgetData;
			this.templateData = templateData;

			// Create hub with loader object
			this.openAjaxLoader = new OpenAjax.widget.Loader({ManagedHub: {
				onPublish:			this.onPublish.bind(this),
				onSubscribe:		this.onSubscribe.bind(this),
				onUnsubscribe:		this.onUnsubscribe.bind(this),
				onSecurityAlert:	this.onSecurityAlert.bind(this),
				scope: window
			}});
			this.hub = this.openAjaxLoader.hub;

			// Find template placeholders
			this.placeholders = domQuery("[itemtype='"+ this.TEMPLATE_PLACEHOLDER +"']");
			console.log("Placeholders found: "+ this.placeholders.length);
			if (this.placeholders.length == 0) {
				this.handleError("No placeholders found on the template.");
				return;
			}

			// Load widgets
			this.size = null;
			this.dataWidgets = [];
			this.visualWidgets = [];
			this.loader = new Loader(this.openAjaxLoader, this.widgetData, this.placeholders, this.visualWidgetsLoaded.bind(this),
				this.allWidgetsLoaded.bind(this));

			// Loading mashup message
			var dimensions = win.getBox();
			this.loadingMessage = domConstruct.create("div", {
				id: "loadingMashup",
				innerHTML: "Loading mashup...",
				style: {
					background: "rgba(0, 0, 0, 0.5)",
					color: "#FFFFFF",
					position: "absolute",
					top: 0,
					left: 0,
					width: "100%",
					height: "100%",
					padding: "0",
					textAlign: "center",
					lineHeight: dimensions.h +"px",
					zIndex: 100000
				}
			}, document.body);
		},

		/**
		 * Handle error message
		 */
		handleError: function(errorMessage) {
			alert(errorMessage);
		},

		onPublish: function(topic, data, publishContainer, subscribeContainer) {
			// Listen on the MenuClick topic to track clicking on menu items
			if (topic == "AutoMicrosite.MenuClick") {
				this.size.menuClick(data);
			}
			return true;
		},

		onSubscribe: function(topic, container) {
			return true;
		},

		onUnsubscribe: function(topic, container) {
			return true;
		},

		/**
		* Security alert from OpenAjax hub
		*/
		onSecurityAlert: function(source, alertType) {
			// TODO: do something about it
			this.handleError(source +" "+ alertType);
		},

		/**
		 * Actions to perform once visual widgets have finished loading
		 */
		visualWidgetsLoaded: function(visualWidgets, dataWidgets) {
			this.size = new SizeHandler(this.widgetData, this.placeholders, visualWidgets);
			this.size.run();
		},

		allWidgetsLoaded: function() {
			this.loadingMessage.style.display = "none";
		},

		/**
		 * Load all widgets into mashup
		 */
		start: function() {
			try {
				this.loader.load();
			} catch (e) {
				this.handleError(e);
			}

/*
			for (var i in this.widgetData) {
				this.loadWidget(i, this.widgetData[i]);
			}

			// remove empty placeholders (optional)
			/*
			for (var k = 0; k < this.placeholders.length; k++) {
				var removeItem = true;
				for (var j in this.widgetData) {
					if (this.placeholders[k].getAttribute("itemid") == this.widgetData[j].placeholder) {
						removeItem = false;
						break;
					}
				}
				if (removeItem) {
					this.placeholders[k].parentNode.removeChild(this.placeholders[k]);
				}
			}
			*/
		}

	})
});