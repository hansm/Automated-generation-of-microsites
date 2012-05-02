if (typeof(AutoMicrosite) == "undefined") {
	AutoMicrosite = {};
}
if (typeof(AutoMicrosite.Manual) == "undefined") {
	AutoMicrosite.Manual = {};
}

AutoMicrosite.Manual.DataManager = function() {
	this.columns = [];
	this.data = [];
};

AutoMicrosite.Manual.DataManager.prototype = {

	onLoad: function() {
		var thisWidget = this;

		this.OpenAjax.hub.subscribe("AutoMicrosite.Data.Receive", function(topic, receivedData) {
			if (receivedData.length == 0) {
				console.log("No data to receive.");
				return;
			}
			
			thisWidget.columns = receivedData[0];
			
			thisWidget.data = [];
			for (var i = 1; i < receivedData.length; i++) {
				thisWidget.data.push(receivedData[i]);
			}
			
			console.log(thisWidget.columns);
			console.log(thisWidget.data);
			
			thisWidget.publishSummary();
			thisWidget.publishTable();
			thisWidget.publish2D(1);
		});
		
		this.OpenAjax.hub.subscribe("AutoMicrosite.Data.Select", function(topic, receivedData) {
			if (typeof(receivedData.id) != "undefined" && receivedData.id != null) {
				thisWidget.publishRow(receivedData.id);
			}
			if (typeof(receivedData.column) != "undefined" && receivedData.column != null) {
				thisWidget.publish2D(receivedData.column);
			}
		});
	},
	
	/**
	 * Publish data summary
	 */
	publishSummary: function() {
		var data = [];
		
		// TODO: actually do something useful here
		data.push({label: "Number of '"+ this.columns[0] +"' objects", value: this.data.length});
		data.push({label: "'"+ this.columns[1] +"' average", value: this.average(this.getColumnValues(1))});
		
		this.OpenAjax.hub.publish("AutoMicrosite.Data.Summary", {data: data});
	},
	
	/**
	 * Publish whole data table
	 */
	publishTable: function() {
		var data = [];
		data.push(this.columns);
		for (var i = 0; i < this.data.length; i++) {
			data.push(this.data[i]);
		}
		this.OpenAjax.hub.publish("AutoMicrosite.Data.Table", {data: data});
	},
	
	/**
	 * Publish 2 dimensional data (key with some other parameter)
	 */
	publish2D: function(column) {
		var data = [];
		data.push({id: this.columns[0], value: this.columns[column]});
		for (var i = 0; i < this.data.length; i++) {
			data.push({id: this.data[i][0], value: this.data[i][column]});
		}
		this.OpenAjax.hub.publish("AutoMicrosite.Data.2D", {data: data});
	},
	
	publishRow: function(id) {
		var row = null;
		for (var i = 0; i < this.data.length; i++) {
			if (this.data[i][0] == id) {
				row = this.data[i];
			}
		}
		
		var data = [];
		for (var i = 0; i < this.columns.length; i++) {
			data.push({label: this.columns[i], value: row[i]});
		}
		
		this.OpenAjax.hub.publish("AutoMicrosite.Data.Row", {data: data});
	},

	/**
	 * Return average of array
	 *
	 * @param a array with numbers
	 * @return float
	 */
	average: function(a) {
		var sum = 0;
		var count = 0;
		for (var i = 0; i < a.length; i++) {
			if (a[i] == null) {
				continue;
			}
			count++;
			sum += a[i];
		}
		if (count == 0) {
			return 0;
		} else {
			return Math.round((sum / count) * 100) / 100;
		}
	},
	
	/**
	 * Return array of values in a column
	 */
	getColumnValues: function(col) {
		var a = [];
		for (var i = 0; i < this.data.length; i++) {
			a.push(this.data[i][col]);
		}
		return a;
	}

};