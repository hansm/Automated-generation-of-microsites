<?php

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Manual Mashup</title>
<style type="text/css">
body {
	font-family: sans-serif;
	font-size: 1em;
	color: #000;
	margin: 0;
	padding: 0;
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
var myLoader, myHub;

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
}
</script>
</head>
<body onload="initHub();">
</body>
</html>