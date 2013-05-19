/**
 * Read "Hourly labour costs in Euros (European Union 1997-2008)" data and distribute
 */

if (typeof(AutoMicrosite) == "undefined") {
	AutoMicrosite = {};
}
if (typeof(AutoMicrosite.Widget) == "undefined") {
	AutoMicrosite.Widget = {};
}

AutoMicrosite.Widget.Data = function() {};

AutoMicrosite.Widget.Data.prototype.onLoad = function() {
	var thisWidget = this;

	$.get("Widgets/Data/data.txt", function(data) {
		data = data.split("\n");

		var newData = [];
		var dataRow;
		var dataValues;
		var value;

		for (var i = 0; i < data.length; i++) {
			dataRow = [];
			dataValues = $.map(data[i].split("\t"), $.trim);

			for (var j = 0; j < dataValues.length; j++) {
				value = dataValues[j] == ":" ? null : dataValues[j];
				if (value != null && !isNaN(value)) {
					value = parseFloat(value);
				}
				dataRow.push(value);
			}

			newData.push(dataRow);
		}

		// Parse data
		for (var i in data) {
			data[i] = $.map(data[i].split("\t"), $.trim);
		}

		var columns = data[0];
		var mapData = [];
		var row, values, dataRow, val;
		for (var i = 1; i < data.length; i++) {
			dataRow = data[i];
			values = [];
			for (var j = 1; j < dataRow.length; j++) {
				val = dataRow[j] == ":" ? null : dataRow[j];
				if (val != null && !isNaN(val)) {
					val = parseFloat(val);
				}
				values.push({
					year: columns[j],
					value: val
				});
			}
			mapData.push({
				country: dataRow[0],
				values: values
			});
		}

		thisWidget.OpenAjax.hub.publish("AutoMicrosite.LabourCost.Data", newData);
		thisWidget.OpenAjax.hub.publish("AutoMicrosite.LabourCost.Map.Data", mapData);
	}, "text");
};