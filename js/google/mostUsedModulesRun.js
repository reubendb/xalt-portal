/*!
 * Column chart for Most Used Modules.
 * History
 * 2015-Aug-10
 */

google.load('visualization', '1', {packages: ['corechart', 'bar']});
google.setOnLoadCallback(mostUsedModulesRun);

function mostUsedModulesRun(param) {

	var syshost = param.value;
	var jsonBarChartData = $.ajax(
			{
			url: "include/mostUsedModulesRun.php",
			data: "sys="+syshost,
			dataType:"json", async: false
			}
			).responseText;
	
	// Create our data table out of JSON data loaded from server.
	var barChartData = new google.visualization.DataTable(
			jsonBarChartData);

	// Define Chart Options .
	var options = {
		title: 'Most Used Modules (Run Time)',
		       chartArea: {width: '70%'},
		       hAxis: {
			       title: 'Module',
			       minValue: 0
		       },
			vAxis: {title: 'Year'
			       }
		       };
	
	
	// Instantiate and draw chart.
	var chart = new google.visualization.ColumnChart(document.getElementById('run_module_div'));
	chart.draw(barChartData, options);
}

