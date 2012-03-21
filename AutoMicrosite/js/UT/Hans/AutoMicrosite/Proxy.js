/**
 * Proxy context for JS classes
 */
define([], function() {
	return function(method, context) {
		method.call(context);
	}
})