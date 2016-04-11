/*!
 * Top Ten User 
 * History
 * 2015-Aug-10
 */

google.load('visualization', '1', {packages: ['corechart', 'bar']});

function topTenUser(sysHost, startDate, endDate) {

    console.log("TopTenUser: " + sysHost + startDate + endDate);
    var jsonChartData = $.ajax
        ({url: "include/topTenUser.php",
         data: "sysHost="+sysHost + "&startDate=" + startDate + "&endDate=" +endDate,
         dataType:"json", async: false
         }).responseText;

    var count = checkJsonData(jsonChartData);
    if (count != 0) {

        document.getElementById("top_user_div").style.visibility = 'visible';

        // Create our data table out of JSON data loaded from server.
        var chartData = new google.visualization.DataTable(jsonChartData);

        // Define Chart Options .
        var options = {title: 'Top Ten User',
            chartArea: {width: '80%', height:"70%", left: "auto" },
            hAxis: {title: 'Users'},
            legend:{position: 'top'},
            vAxis: {title: 'Count (log scale)',scaleType: 'log', format: 'short'},
            seriesType: 'bars'
        };

        // Instantiate and draw chart.
        var chart = new google.visualization.ComboChart(document.getElementById('top_user_div'));
        chart.draw(chartData, options);
    }
    if (count == 0){
        document.getElementById("top_user_div").style.visibility = 'hidden';
    }

}

function checkJsonData (jsonTableData) {
    var o = JSON.parse(jsonTableData); 
    return (o.rows.length);
}  
