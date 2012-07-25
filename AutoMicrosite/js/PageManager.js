/**
 * Widget page manager that handles page switching
 */

var PageManager = {

	/**
	 * Mashup pages
	 * @type Array
	 */
	pages: [],

	/**
	 * Get pages for menus
	 */
	getPages: function() {
		return PageManager.pages;
	},

	setPages: function(pages) {
		PageManager.pages = pages;
	},
	
	/**
	 * Start page manager
	 */
	init: function(hub) {
		function onClientSecurityAlert(source, alertType) {  /* Handle client-side security alerts */  }
		function onClientConnect(container) {        /* Called when client connects */   }
		function onClientDisconnect(container) {     /* Called when client disconnects */ }

		var container1 = new OpenAjax.hub.InlineContainer(hub , "client1", {
			Container: {
				onSecurityAlert: onClientSecurityAlert,
				onConnect:       onClientConnect,
				onDisconnect:    onClientDisconnect
			}
		});
			
		// Handle security alerts:
		function client1SecurityAlertHandler(source, alertType) { }

		// Callback called when a subscription receives data
		

		var hubClient = new OpenAjax.hub.InlineHubClient({
			HubClient: {
				onSecurityAlert: client1SecurityAlertHandler
			},
			InlineHubClient: {
				container: container1
			}
		});

		// Callback that is invoked when HubClient's attempt to connect
		// to the Managed Hub completes
		function clientConnect(hubClient1, success, error) {
			if (!success) {
				console.error("PageManager: hub connection failed. "+ error);
				return;
			}
			
			/* Call hubClient1.publish(...) to publish messages  */
			/* Call hubClient1.subscribe(...) to subscribe to message topics */
				
			hubClient1.subscribe("AutoMicrosite.SwitchPage", function onData(topic, data, subscriberData) {
				console.log(topic);
				console.log(data);
				PageManager.switchPage(data);
			});
		}

		hubClient.connect(clientConnect);
		
		PageManager.switchPage(PageManager.pages[0].href);
	},

	/**
	 * Switch mashup page
	 */
	switchPage: function(page) {
		console.log("switch page "+ page);
		var p;
		console.log(PageManager.pages);
		for (var i in PageManager.pages) {
			p = PageManager.pages[i];
			console.log(p);
			if (p.href == page) {
				document.getElementById("page-"+ p.href).style.display = "block";
			} else {
				document.getElementById("page-"+ p.href).style.display = "none";
			}
		}
	}

};