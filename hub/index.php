<?php

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Mashup</title>
<style type="text/css">
body {
	font-family: sans-serif;
	font-size: 1em;
	color: #000;
}
</style>
<script type="text/javascript" src="js/OpenAjaxManagedHub-all.js"></script>
<script type="text/javascript" src="js/dojo.js"></script>
<script type="text/javascript">
oaaLoaderConfig = {
		proxy: "proxy.php"
};
</script>
<script type="text/javascript" src="js/loader.js"></script>
<script type="text/javascript">
var myLoader, myHub, myWidget;

function onMHPublish(topic, data, publishContainer, subscribeContainer) {
	/* Callback for publish requests. This example approves all publish requests. */
	return true;
}

function onMHSubscribe(topic, container) {
	/* Callback for subscribe requests. This example approves all subscribe requests. */
	return true;
}

function onMHUnsubscribe(topic, container) {
	/* Callback for unsubscribe requests. This example approves all subscribe requests. */
	return true;
}

function onMHSecurityAlert(source, alertType) {
	/* Callback for security alerts */
}

function initHub() {
	// create new loader (+hub)
	myLoader = new OpenAjax.widget.Loader({ManagedHub: {
			onPublish: onMHPublish,
			onSubscribe: onMHSubscribe,
			onUnsubscribe: onMHUnsubscribe,
			onSecurityAlert: onMHSecurityAlert,
			scope: window
        }});
	myHub = myLoader.hub;

	var myWidget = myLoader.create({
		spec: "gadgets/helloworld/helloworld_oam.xml",
		target: document.getElementById("widget1"),
		onComplete: function(metadata) {
			console.log(metadata);
		},
		onError: function(error) {
			alert(error);
		}
	});
	console.log(myWidget);
}
</script>
</head>
<body onload="initHub();">
	<h1>Test Mashup!</h1>
	<div id="widget1"></div>
</body>
</html>