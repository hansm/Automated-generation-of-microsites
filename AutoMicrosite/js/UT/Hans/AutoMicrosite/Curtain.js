/**
 * Loading mashup screen
 *
 * @author Hans
 */
define(["dojo/_base/declare", "dojo/dom", "dojo/dom-construct", "dojo/dom-style"
		, "dojo/window"]
	, function(declare, dom, domConstruct, domStyle, win) {
	return declare(null, {

		/**
		 * Curtain DOM element
		 */
		div: null,

		constructor: function() {
			var dimensions = win.getBox();
			this.div = domConstruct.create("div", {
				id: "mashupCurtain",
				innerHTML: "Loading...",
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
					zIndex: 100000,
					display: "none"
				}
			}, document.body);
		},

		/**
		 * Show curtain
		 */
		enable: function() {
			domStyle.set(this.div, {
				display: "block"
			});
		},

		/**
		 * Hide curtain
		 */
		disable: function() {
			domStyle.set(this.div, {
				display: "none"
			});
		}

	});
});