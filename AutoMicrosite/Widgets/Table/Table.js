if (typeof(AutoMicrosite) == "undefined") {
	AutoMicrosite = {};
}
if (typeof(AutoMicrosite.Widget) == "undefined") {
	AutoMicrosite.Widget = {};
}

/**
	* Widget constructor
	*/
AutoMicrosite.Widget.Table = function() {
	this.tableData = [];
	this.table = null;
	this.widgetId = null;
};

/**
	* Add additional methods to widget
	*/
AutoMicrosite.Widget.Table.prototype = {

	/**
	 * Widget has finished loading
	 */
	onLoad: function() {
		this.widgetId = this.OpenAjax.getId();
		var thisWidget = this;

		this.table = document.getElementById(this.widgetId + "table");
		this.drawTable();

		this.OpenAjax.hub.subscribe("AutoMicrosite.LabourCost.Data", function(topic, receivedData) {
			thisWidget.tableData = receivedData;
			thisWidget.drawTable();
		});
	},

	/**
	 * Process data into simple array
	 */
	processData: function() {
		var data = {thead: [], tbody: []};

		if (typeof this.tableData[0] == "undefined") {
			throw "Invalid table data.";
		}

		data.thead = [this.tableData[0]];

		// read data into an array
		var row, column, value;
		for (var i = 1; i < this.tableData.length; i++) {
			row = [];

			for (var j = 0; j < this.tableData[i].length; j++) {
				row.push((this.tableData[i][j] == "undefined" || this.tableData[i][j] == null)
					? "-" : this.tableData[i][j]);
			}

			data.tbody.push(row);
		}

		return data;
	},

	/**
	 * Draw data in table
	 */
	drawTable: function() {
		// get table sections
		var thead = this.table.getElementsByTagName("thead")[0];
		var tbody = this.table.getElementsByTagName("tbody")[0];
		var tfoot = this.table.getElementsByTagName("tfoot")[0];

		// empty
		thead.innerHTML = '';
		tbody.innerHTML = '';
		tfoot.innerHTML = '';

		// no data
		if (this.tableData.length == 0) {
			var tr = document.createElement("tr");
			var td = document.createElement("td");
			td.appendChild(document.createTextNode("No data."));
			tr.appendChild(td);
			tbody.appendChild(tr);
			return;
		}

		var data = this.processData();

		this.drawTableSection(thead, data.thead);
		this.drawTableSection(tbody, data.tbody);

		this.table.parentNode.style.height = "100%";
	},

	/**
		* Draw section of table
		*/
	drawTableSection: function(element, data) {
		element.innerHTML = "";
		var tr, td;
		for (var i = 0; i < data.length; i++) {
			tr = document.createElement("tr");
			for (var j = 0; j < data[i].length; j++) {
				td = document.createElement("td");
				td.appendChild(document.createTextNode(data[i][j]));

				if (data[i][j] == "-") {
					td.className = "null";
				} else if (!isNaN(data[i][j])) {
					td.className = "number";
				}

				tr.appendChild(td);
			}
			element.appendChild(tr);
		}
	}

};