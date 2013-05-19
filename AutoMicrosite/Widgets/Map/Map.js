/**
 * Map widget JavaScript
 *
 * @author Hans
 */

if (typeof(AutoMicrosite) == "undefined") {
	AutoMicrosite = {};
}
if (typeof(AutoMicrosite.Widget) == "undefined") {
	AutoMicrosite.Widget = {};
}

/**
 * Widget constructor
 */
AutoMicrosite.Widget.Map = function() {
	this.data = [];
	this.matrix = [];
	this.columns = [];
	this.map = null;
	this.dataTable = null;
	this.widgetId = null;
	this.divBubble = null;
	this.divMenu = null;
};

AutoMicrosite.Widget.Map.prototype = {

	/**
	 * Widget loaded
	 */
	onLoad: function() {
		this.widgetId = this.OpenAjax.getId();
		var thisWidget = this;

		google.load("visualization", "1", {
			packages: ["geochart"],
			callback: function() {
				thisWidget.mapLoaded();
			}
		});

		this.OpenAjax.hub.subscribe("AutoMicrosite.LabourCost.Map.Data", function(topic, receivedData) {
			thisWidget.data = receivedData;
			thisWidget.processData(thisWidget.data);

			if (typeof(thisWidget.map) != "undefined") {
				thisWidget.drawMap();
			}
			thisWidget.drawMenu();
		});

		this.drawMenu();
	},

	/**
	 * Widget dimensions change
	 * @param object e
	 */
	onSizeChanged: function(e) {
		this.drawMap();
	},

	/**
	 * Process data into more acceptable format
	 *
	 * @param array data
	 */
	processData: function(data) {
		// Columns
		var columns = [];
		var i, j, values;
		for (i in data) {
			values = data[i].values;
			for (j in values) {
				if (columns.indexOf(values[j].year) < 0) {
					columns.push(values[j].year);
				}
			}
		}
		columns.sort();

		// Create full data matrix
		var m = [];
		var val, k;
		for (i in data) {
			m[i] = [];
			values = data[i].values;
			for (j = 0; j < columns.length; j++) {
				val = 0;
				for (k in values) {
					if (values[k].year === columns[j]) {
						val = values[k].value;
						break;
					}
				}
				m[i].push(val);
			}
		}
		this.matrix = m;
		this.columns = columns;
	},

	/**
	 * Finished loading Google Map Graph API
	 */
	mapLoaded: function() {
		var thisWidget = this;
		this.map = new google.visualization.GeoChart(document.getElementById(this.widgetId +"map"));

		this.drawMap();
		google.visualization.events.addListener(this.map, "select", function() {
			try {
				var row = thisWidget.map.getSelection()[0].row;
				var id = thisWidget.dataTable.getValue(row, 0);
				thisWidget.mapClick(id);
			} catch (e) {
				console.log(e);
			}
		});
	},

	/**
	 * Draw map widget
	 */
	drawMap: function(columnNumber) {
		if (!this.map) {
			return;
		}

		if (!columnNumber || !this.columns[columnNumber]) {
			columnNumber = 0;
		}
		var columnName = this.columns[columnNumber];

		var widgetDimensions = this.OpenAjax.getDimensions();
		var options = {
			width: widgetDimensions.width, height: widgetDimensions.height * 0.89,
			enableRegionInteractivity: true,
			region: 150
		};

		this.dataTable = new google.visualization.DataTable();
		this.dataTable.addColumn("string", "Country");
		this.dataTable.addColumn("number", "Salary");

		var dataRow, matrixRow;
		for (var i in this.data) {
			dataRow = this.data[i];
			matrixRow = this.matrix[i];
			this.dataTable.addRow([dataRow.country, matrixRow[columnNumber]]);
		}

		try {
			this.map.draw(this.dataTable, options);
		} catch (e) {
			console.log("Map error: "+ e);
		}

		this.publishSummary(columnNumber);
	},

	/**
	 * Publish summary of the selected data
	 */
	publishSummary: function(columnNumber) {
		var matrixRow;
		var objectsCount = 0, objectsSum = 0;
		for (var i in this.data) {
			matrixRow = this.matrix[i];
			if (matrixRow[columnNumber]) {
				objectsCount++;
				objectsSum += matrixRow[columnNumber];
			}
		}

		var data = [];
		data.push({label: "Number of '"+ this.columns[columnNumber] +"' objects", value: objectsCount});
		data.push({label: "Average value", value: Math.round((objectsSum / objectsCount) * 100) / 100});

		this.OpenAjax.hub.publish("AutoMicrosite.LabourCost.Summary", {data: data});
	},

	mapClick: function(id) {
		this.showBubble();

		for (var i in this.data) {
			if (this.data[i].country == id) {
				this.bubbleText(this.data[i]);
			}
		}
	},

	/**
	 * Show bubble with country info
	 */
	showBubble: function() {
		if (this.divBubble == null) {
			this.divBubble = document.createElement("div");
			this.divBubble.className = "bubble";
			this.divBubble.innerHTML = "Loading...";
			document.getElementById(this.widgetId +"map").appendChild(this.divBubble);
		}
		this.divBubble.style.display = "block";

		// TODO: closing
	},

	bubbleText: function(data) {
		if (this.divBubble == null) {
			return;
		}

		var thisWidget = this;
		this.divBubble.innerHTML = "";

		// close button
		var aClose = document.createElement("a");
		aClose.href = "#close";
		aClose.className = "close";
		aClose.title = "Close";
		aClose.innerHTML = "X";
		aClose.style.textDecoration = "none";
		aClose.onclick = function() {
			aClose.onclick = null;
			thisWidget.divBubble.style.display = "none";
			return false;
		};
		this.divBubble.appendChild(aClose);

		// data
		var p;
		p = document.createElement("div");
		p.style.fontWeight = "bold";
		p.appendChild(document.createTextNode(data.country));
		this.divBubble.appendChild(p);

		for (var i in data.values) {
			p = document.createElement("div");
			p.appendChild(
				document.createTextNode(data.values[i].year +": "
					+ (data.values[i].value ? data.values[i].value : "-"))
			);
			this.divBubble.appendChild(p);
		}
	},

	/**
	 * Draw menu for selecting year
	 */
	drawMenu: function() {
		var thisWidget = this;

		if (!this.divMenu) {
			this.divMenu = document.getElementById(this.widgetId + "mapMenu");
		}
		this.divMenu.innerHTML = "";

		if (!this.columns) {
			return;
		}

		var widgetDimensions = this.OpenAjax.getDimensions();
		var buttonHeight = widgetDimensions.height * 0.1;

		var a;
		for (var i in this.columns) {
			a = document.createElement("a");
			a.innerHTML = this.columns[i];
			a.href = "#"+ this.columns[i];
			a.columnNumber = i;
			a.style.lineHeight = buttonHeight + "px";
			a.onclick = function() {
				thisWidget.drawMap(this.columnNumber);
				return false;
			};
			this.divMenu.appendChild(a);
		}
	}

};