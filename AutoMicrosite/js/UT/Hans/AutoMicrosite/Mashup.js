/**
 * Mashup handling class
 *
 * @author Hans
 */
define(["dojo/_base/declare", "dojo/dom", "dojo/dom-construct", "dojo/dom-style"
		, "dojo/window", "dojo/on", "dojo/query", "dojo/dom-attr"
		, "UT/Hans/AutoMicrosite/Size"
		, "UT/Hans/AutoMicrosite/WidgetLoad"
	    , "UT/Hans/AutoMicrosite/Curtain"
		, "UT/Hans/AutoMicrosite/Navigation"]
	, function(declare, dom, domConstruct, domStyle, win, on, domQuery, domAttr
				, SizeHandler, Loader, Curtain, Navigation) {
	return declare(null, {

		/**
		 * Template placeholder itemtype
		 */
		TEMPLATE_PLACEHOLDER: "http://deepweb.ut.ee/TemplatePlaceholder",

		/**
		 * Widget data received from server-side
		 */
		widgetData: [],

		/**
		 * Mashup DOM element
		 */
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
		 * Template placeholder DOM elements for the widgets
		 */
		placeholders: [],

		/**
		 * Curtain handler
		 */
		curtain: null,

		/**
		 * Widget resize handler
		 */
		size: null,

		loader: null,

		/**
		 * Navigation handler object
		 */
		navigation: null,

		dataWidgets: [],

		visualWidgets: [],

		/**
		 * Constructor method
		 *
		 * @param string divMashupId ID of element where hub should be attached
		 */
		constructor: function(divMashupId, widgetData) {
			this.divMashup = dom.byId(divMashupId);
			this.widgetData = widgetData;

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
			if (this.placeholders.length == 0) {
				this.handleError("No placeholders found on the template.");
				return;
			}

			// Curtain handler
			this.curtain = new Curtain();

			// Load widgets
			this.size = null;
			this.navigation = null;
			this.dataWidgets = [];
			this.visualWidgets = [];
			this.loader = new Loader(
				this.openAjaxLoader,
				this.widgetData,
				this.placeholders
			);
			this.setUpMenuManager();
		},

		/**
		 * Load all widgets into mashup
		 */
		start: function() {
			this.curtain.enable();
			try {
				this.loader.load(
					this.visualWidgetsLoaded.bind(this),
					this.allWidgetsLoaded.bind(this)
				);
			} catch (e) {
				this.handleError(e);
			}
		},

		/**
		 * Handle error message
		 */
		handleError: function(errorMessage) {
			alert(errorMessage);
		},

		onPublish: function(topic, data, publishContainer, subscribeContainer) {
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
			this.handleError("Security alert: "+ source +" "+ alertType);
		},

		/**
		 * Actions to perform once visual widgets have finished loading
		 */
		visualWidgetsLoaded: function(visualWidgets, dataWidgets) {
			this.size = new SizeHandler(visualWidgets, this.placeholders);
			this.navigation = new Navigation(visualWidgets, this.size);
			this.navigation.build();
			this.size.run();

			// Re-calculate sizes when page is resized
			window.onresize = function() {
				this.size.run();
			}.bind(this);
		},

		/**
		 * All widgets have finished loading
		 */
		allWidgetsLoaded: function() {
			this.curtain.disable();
		},

		/**
		 * Monitor AutoMicrosite.MenuClick topic
		 */
		setUpMenuManager: function() {
			this.hub.subscribe("AutoMicrosite.MenuClick", function(topic, data) {
				this.navigation.clickMenu(data, this.size);
			}, this);
		}

	});
});