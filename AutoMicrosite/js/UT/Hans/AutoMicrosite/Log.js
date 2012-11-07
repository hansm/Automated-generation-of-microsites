/**
 * Simple console logging module
 */
define([], function() {
		return function(type, message) {
			if (typeof console != "undefined" && console && console.log) {
				if (typeof message == "object" || typeof message == "array") {
					if (message.toString) {
						message = message.toString();
					} else {
						var newMessage = "";
						for (var i in message) {
							newMessage += i +"="+ message[i] +"; ";
						}
						message = newMessage;
					}
				}
				console.log(type +": "+ message);
			}
		}
	}
);