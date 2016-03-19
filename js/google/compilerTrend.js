function compilerTrend(sysHost, startDate, endDate){

    console.log("CompilerTrend: " + sysHost + startDate + endDate);
    var chartData = $.ajax
        ({url: "include/compilerTrend.php",
         data: "sysHost="+sysHost + "&startDate=" + startDate + "&endDate=" + endDate,
         dataType:"json", async: false
         }).responseText;
    var dataChart = chartData.split('#');
    var dc = JSON.parse(dataChart[1]);
    var dctg = dataChart[0].split(",");

    console.log(dc);
    console.log(dctg);

    var chart = new Highcharts.Chart(
            {chart: {renderTo: 'comp_div',defaultSeriesType: 'line'}
            ,title: {text: 'Compiler Trend Over Time'}
            ,xAxis: {categories: dctg}
            ,yAxis: {title: 
            {text: '#Instance'},plotLines: [{value: 0,width: 1,color: '#808080'}]
            }
            ,legend: {layout: 'vertical',align: 'right',verticalAlign: 'middle',borderWidth: 0}
            ,credits:{enabled: false}
            ,series: dc 
            });

}

