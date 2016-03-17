/*!
 * Chart for Compiler Trend
 * History
 * 2015-Aug-10
 */

google.load('visualization', '1', {packages: ['corechart']});

function compilerTrend(sysHost, startDate, endDate) {

    console.log("CompilerTrend: " + sysHost + startDate + endDate);
    var jsonBarChartData = $.ajax
        ({url: "include/compilerTrend.php",
         data: "sysHost="+sysHost + "&startDate=" + startDate + "&endDate=" + endDate,
         dataType:"json", async: false
         }).responseText;

    var count = checkJsonData(jsonBarChartData);

    if (count != 0) {

        document.getElementById("compTrend_div").style.visibility = 'visible';
        // Create our data table out of JSON data loaded from server.
        var barChartData = new google.visualization.DataTable(
                jsonBarChartData);

        // Define Chart Options .
        var options = {title: 'Compiler Trend',
            isStacked: true,
            chartArea: {width: '65%', height:"50%", left: "auto" },
            hAxis: {title: 'LinkProgram'},
            vAxis: {title: '#Instances Linked',format: 'short'}
        };


        // Instantiate and draw chart.
        var chart = new google.visualization.ColumnChart(document.getElementById('compTrend_div'));
        chart.draw(barChartData, options);

    }
    if (count ==0){
        document.getElementById("compTrend_div").style.visibility = 'hidden';
    }

}
function checkJsonData (jsonTableData) {
    var o = JSON.parse(jsonTableData);
    return (o.rows.length);
}

