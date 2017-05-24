<?php
return array(
	//'配置项'=>'配置值'
	'SHOW_PAGE_TRACE'	=> false,
	'URL_ROUTER_ON'   => true,
	'URL_ROUTE_RULES'=> [
		'/^regist$/' 	=> 	'user/regist',
		'/^login$/' 	=> 	'user/login',
		'/^logout$/' 	=> 	'user/logout',
		'/^vercode$/' 	=> 	'user/getvercode',
		'/^phoList$/' 	=> 	'index/phoList',
		'/^phoDetail$/' => 'index/phoDetail',
		'/^taskList$/' 	=> 	'index/taskList',
		'/^mealList$/' 	=> 	'index/mealList',
		'/^mealDetail$/' 	=> 	'index/mealDetail',
		'/^taskDetail$/' 	=> 	'index/taskDetail',
		'/^regions$/' 	=> 	'index/regions',
		'/^citys$/' 	=> 	'index/citys',
		'/^category$/' 	=> 	'index/category',
		'/^about$/'  	=> 'index/about',
		'/^agreement$/'  => 'index/agreement',
		'/^feedback$/'  => 'index/feedback',
		'/^phoShare\/(\d+)$/'  => 'Index/phoShare?id=:1',
		'/^taskShare\/(\d+)$/'  => 'Index/taskShare?id=:1',
		'/^mealShare\/(\d+)$/'  => 'Index/mealShare?id=:1',
	],
);