/*!
 * Bar chart for TotalSUs  usage on Darter
 * History
 * 2015-Aug-10
 */

google.load('visualization', '1', {packages: ['corechart']});

function totalSus(sysHost, startDate, endDate) {

    console.log("totalSus: " + sysHost + startDate + endDate);
    var jsonBarChartData = $.ajax
        ({url: "include/totalSus.php",
         data: "sysHost="+sysHost + "&startDate=" + startDate + "&endDate=" + endDate,
         dataType:"json", async: false
         }).responseText;

    var count = checkJsonData(jsonBarChartData);
    if (count != 0) {

        document.getElementById("total_su_div").style.visibility = 'visible';
        // Create our data table out of JSON data loaded from server.
        var barChartData = new google.visualization.DataTable(
                jsonBarChartData);

        // Define Chart Options .
        var options = {title: 'SUs Charged: Total',
            chartArea: {width: '50%', height:"50%", left: "auto" },
            hAxis: {title: 'Month'},
            vAxis: {title: 'TotalSUs',format: 'short',minValue: 0}
        };


        // Instantiate and draw chart.
        var chart = new google.visualization.ColumnChart(document.getElementById('total_su_div'));
        chart.draw(barChartData, options);

    }
    if (count ==0){
        document.getElementById("total_su_div").style.visibility = 'hidden';
    }

}
function checkJsonData (jsonTableData) {
    var o = JSON.parse(jsonTableData);
    return (o.rows.length);
}

