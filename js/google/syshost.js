/*!
 * Pull out all syshost
 * History
 * 2015-Aug-10
 */

function drawBasic() {

	var jsonBarChartData = $.ajax(
			{
			url: "include/moduleFile.php",
			dataType:"json", async: false
			}
			).responseText;
	
	// Create our data table out of JSON data loaded from server.
	var barChartData = new google.visualization.DataTable(
			jsonBarChartData);

	// Define Chart Options .
	var options = {
		title: 'SUs Charged: Total',
		chartArea: {width: '60%'},
		hAxis: {
			title: 'Month',
			minValue: 0
		},
		vAxis: {
			title: 'TotalSUs'
		       }};
	
	
	// Instantiate and draw chart.
	var chart = new google.visualization.BarChart(document.getElementById('module_run_div'));
	chart.draw(barChartData, options);

}
