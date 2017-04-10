/**
 * 统计中心
 */
require.config({ paths: { echarts: '/Public/js/lib/echarts' }});
/**
 * 饼图
 * @param HTMLElement element
 * @param Object opt #{title:'', unit:['x','y'], legend:['']}
 * @param Object seriesData 
 */
function pie(element, opt, seriesData){
	myChart = echarts.init(element);
	var option = {
		title : { text: opt.title},
		tooltip : {
			trigger: 'item',
			formatter: "{a} <br/>{b} : {c} ({d}%)"
		},
		legend: { data:opt.legend },
		series : [
			{
				name:opt.legend[0],
				type:'pie',
				radius : '90%',
				center: ['100%', '100%'],
				data:seriesData
			}
		]	 
	};
	myChart.setOption(option);
	return myChart;
}
 
(function count(){
	require(['echarts','echarts/chart/pie'], function (ec){
		var node = $('#region-chart');
		if(!node[0]) return;
		var str = node.find('textarea').val();
		var sData = $.parseJSON(str);
		console.log(sData);
		var opt = {title:'', legend:[node.attr('title')]};
		var option = {
			title : { text: opt.title},
			tooltip : {
				trigger: 'item',
				formatter: "{a} <br/>{b} : {c} ({d}%)"
			},
			legend: { data:opt.legend },
			series : [
				{
					name:opt.legend[0],
					type:'pie',
					radius : '60%',
					center: ['50%', '50%'],
					data:sData
				}
			]	 
		};
		ec.init(node[0]).setOption(option);	
	});

	require(['echarts','echarts/chart/line'], function (ec){
		var node = $('#order-chart');
		if(!node[0]) return;
		var str = node.find('textarea').val();
		var sData = $.parseJSON(str);
		var opt = {title:'订单趋势', legend:['金额','订单量']};
		console.log(sData);
		var option = {
			title : { text: opt.title},
			legend: { data:opt.legend },
			xAxis: { splitLine: {
            show: false
        },data: sData.x },
			yAxis: [{splitLine: {
            show: false
        }},{splitLine: {
            show: false
        }}],
			series : [
				{
					name: opt.legend[0],
					type: 'line',
					yAxisIndex:0,
					showSymbol: false,
					data:sData['total']
				},
				{
					name: opt.legend[1],
					type: 'line',
					yAxisIndex:1,
					showSymbol: false,
					data:sData['num']
				}
			]	 
		};
		ec.init(node[0]).setOption(option);	
	});

})();