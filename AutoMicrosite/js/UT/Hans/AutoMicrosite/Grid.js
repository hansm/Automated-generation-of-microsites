/**
 * Grid for attaching widgets
 */
define(["dojo/_base/declare", "dojo/dom", "dojo/dom-construct", "dojo/dom-style"
	, "dojo/window", "dojo/on", "dojo/query"]
	, function(declare, dom, domConstruct, domStyle, win, on, domQuery) {
	return declare(null, {
		
		/**
		 * HTML objects
		 */
		div: {
			top: {
				left: null,
				center: null,
				right: null
			},
			center: {
				left: null,
				center: null,
				right: null
			},
			bottom: {
				left: null,
				center: null,
				right: null
			}
		},
		
		/**
		 * Grid is attached to this element
		 */
		attachTo: null,
		
		/**
		 * Create new grid
		 */
		constructor: function(attachTo) {
			this.attachTo = attachTo;
			
			domConstruct.empty(this.attachTo);
			
			// top line
			var divTopLine = domConstruct.create("div", {
				"class": "line top"
			}, this.attachTo);
			
			this.div.top.left = domConstruct.create("div", {
				"class": "left"
			}, divTopLine);
			this.div.top.center = domConstruct.create("div", {
				"class": "center"
			}, divTopLine);
			this.div.top.right = domConstruct.create("div", {
				"class": "right"
			}, divTopLine);
			
			// middle line
			var divMiddleLine = domConstruct.create("div", {
				"class": "line middle"
			}, this.attachTo);
			
			this.div.center.left = domConstruct.create("div", {
				"class": "left"
			}, divMiddleLine);
			this.div.center.center = domConstruct.create("div", {
				"class": "center"
			}, divMiddleLine);
			this.div.center.right = domConstruct.create("div", {
				"class": "right"
			}, divMiddleLine);
			
			// bottom line
			var divBottomLine = domConstruct.create("div", {
				"class": "line bottom"
			}, this.attachTo);

			this.div.bottom.left = domConstruct.create("div", {
				"class": "left"
			}, divBottomLine);
			this.div.bottom.center = domConstruct.create("div", {
				"class": "center"
			}, divBottomLine);
			this.div.bottom.right = domConstruct.create("div", {
				"class": "right"
			}, divBottomLine);
			
			/*
			var here = this;
			on(window, "resize", function() {
				here.windowResize();
			});*/
			
			// hdie all
			for (var i in this.div) {
				for (var j in this.div[i]) {
					this.div[i][j].style.display = "none";
				}
			}
		},
		
		/*
		optimize: function() {
			for (var i in this.div) {
				for (var j in this.div[i]) {
					if (!this.div[i][j].hasChildNodes()) {
						this.div[i][j].style.display = "none";
					}
				}
			}
			
			// TODO: remove unused blocks
			// TODO: resize widgets / grid blocks
			this.windowResize();
		},*/
		
		/**
		 * Set dimensions for a block
		 */
		setDimensions: function(block, width, height) {
			var div = this.getBlock(block.vertical, block.horizontal)
			if (width == 0 || height == 0) {
				div.style.display = "none";
				return;
			}
			domStyle.set(div, {
				width: width,
				height: height,
				display: "block",
				
				border: "1px solid grey"
			});
		},
		
		getBlock: function(verticalPosition, horizontalPosition) {
			return this.div[verticalPosition][horizontalPosition];
		}
		
	});
});