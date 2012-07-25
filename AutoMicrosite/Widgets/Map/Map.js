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
	this.map = null;
	this.columnNames = {};
	this.data = [];
	this.dataTable = null;
	this.widgetId = null;
	this.divBubble = null;
};

AutoMicrosite.Widget.Map.prototype = {

	/**
	 * Widget loaded
	 */
	onLoad: function() {
		this.widgetId = this.OpenAjax.getId();
		var thisWidget = this;

		google.load("visualization", "1", {
			"packages": ["geochart"],
			"callback": function() {
				thisWidget.mapLoaded();
			}
		});

		this.OpenAjax.hub.subscribe("AutoMicrosite.Data.2D", function(topic, receivedData) {
			var data = receivedData.data;
			thisWidget.columnNames = data[0];
			thisWidget.data = [];
			for (var i = 1; i < data.length; i++) {
				thisWidget.data.push(data[i]);
			}

			if (typeof(thisWidget.map) != "undefined") {
				thisWidget.drawMap();
			}
		});
		
		this.OpenAjax.hub.subscribe("AutoMicrosite.Data.Row", function(topic, receivedData) {
			thisWidget.bubbleText(receivedData.data);
		});
		
		this.drawMenu();
	},

	mapLoaded: function() {
		var thisWidget = this;
		this.map = new google.visualization.GeoChart(document.getElementById(this.widgetId +"map"));
			
		this.drawMap();
		google.visualization.events.addListener(this.map, "select", function() {
			try {
				var row = thisWidget.map.getSelection()[0].row
				var id = thisWidget.dataTable.getValue(row, 0);
				thisWidget.mapClick(id);
			} catch (e) {
				console.log(e);
			}
		});
	},

	drawMap: function() {
		var widgetDimensions = this.OpenAjax.getDimensions();
		var options = {
			width: widgetDimensions.width, height: widgetDimensions.height,
			enableRegionInteractivity: true
		};

		//var processedData = this.processData('2002')

		this.dataTable = new google.visualization.DataTable();
		this.dataTable.addColumn("string", this.columnNames.id);
		this.dataTable.addColumn("number", this.columnNames.value);

		for (var i = 0; i < this.data.length; i++) {
			this.dataTable.addRow([this.data[i].id, (this.data[i].value == null ? 0 : this.data[i].value)]);
		}

		try {
			this.map.draw(this.dataTable, options);
		} catch (e) {
			console.log("Map error: "+ e);
		}
	},
	
	mapClick: function(id) {
		console.log("Loading: "+ id);
		
		this.showBubble();
		
		// query for country full info
		this.OpenAjax.hub.publish("AutoMicrosite.Data.Select", {id: id});
	},
	
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
		aClose.onclick = function() {
			aClose.onclick = null;
			thisWidget.divBubble.style.display = "none";
			return false;
		};
		this.divBubble.appendChild(aClose);
		
		// data
		var p;
		for (var i in data) {
			p = document.createElement("div");
			p.appendChild(document.createTextNode(data[i].label +": "+ data[i].value));
			this.divBubble.appendChild(p);
		}
	},
	
	drawMenu: function() {
		var thisWidget = this;
		var divMenu = document.getElementById(this.widgetId +"mapMenu");
		var buttons = this.OpenAjax.getPropertyValue("buttons");
		divMenu.innerHTML = "";

		var a;
		for (var i in buttons) {
			a = document.createElement("a");
			a.innerHTML = buttons[i];
			a.href = "#"+ buttons[i];
			a.onclick = function() {
				thisWidget.requestData(this.innerHTML);
				return false;
			};
			divMenu.appendChild(a);
		}
	},
	
	requestData: function(column) {
		this.OpenAjax.hub.publish("AutoMicrosite.Data.Select", {column: column});
	}
	
};