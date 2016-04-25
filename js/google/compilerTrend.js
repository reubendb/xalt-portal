/*!
 * Compiler Trend 
 * */

function compilerTrend(sysHost, startDate, endDate){

    console.log("CompilerTrend: " + sysHost + startDate + endDate);
    var chartData = $.ajax
        ({url: "include/compilerTrend.php",
         data: "sysHost="+sysHost + "&startDate=" + startDate + "&endDate=" + endDate,
         dataType:"json", async: false
         }).responseText;
    
    // Get column header of dataseries 
    var dataChart = chartData.split('#');

    // Get column body of dataseries 
    var dc = JSON.parse(dataChart[1]);     // DataSeries
    var dctg = dataChart[0].split(",");    // DateTimeRange

    // call chart to render
    var chart = new Highcharts.Chart(
            {chart: {renderTo: 'comp_div',defaultSeriesType: 'line'}
            ,title: {text: 'Compiler Trend Over Time'}
            ,xAxis: {title:{text: 'DateTimeRange'}, categories: dctg}
            ,yAxis: {title: 
            {text: '#Instance'},plotLines: [{value: 0,width: 1,color: '#808080'}]
            }
            ,legend: {layout: 'vertical',align: 'right',verticalAlign: 'middle',borderWidth: 0}
            ,credits:{enabled: false}
            ,series: dc 
            });

}

