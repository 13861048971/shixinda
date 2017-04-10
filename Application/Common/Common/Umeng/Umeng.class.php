<?php
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidFilecast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidGroupcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidUnicast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidCustomizedcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSFilecast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSGroupcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSUnicast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSCustomizedcast.php');

class Umeng {
	protected $appkey           = NULL; 
	protected $appMasterSecret  = NULL;
	protected $timestamp        = NULL;
	protected $validation_token = NULL;

	function __construct($key, $secret) {
		$this->appkey = $key;
		$this->appMasterSecret = $secret;
		$this->timestamp = strval(time());
	}

	function sendAndroidBroadcast($data) {
		try {
			$brocast = new AndroidBroadcast();
			$brocast->setAppMasterSecret($this->appMasterSecret);
			$brocast->setPredefinedKeyValue("appkey",           $this->appkey);
			$brocast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			$brocast->setPredefinedKeyValue("ticker",           "Android broadcast ticker");
			$brocast->setPredefinedKeyValue("title",            $data['title']);
			$brocast->setPredefinedKeyValue("text",             $data['content']);
			$brocast->setPredefinedKeyValue("after_open",       "go_app");
			$brocast->setPredefinedKeyValue("description",       "系统消息");
			// Set 'production_mode' to 'false' if it's a test device. 
			// For how to register a test device, please see the developer doc.
			$brocast->setPredefinedKeyValue("production_mode", "false");
			
			// [optional]Set extra fields
			
			$brocast->setExtraField("data", json_encode($data, JSON_UNESCAPED_UNICODE));
			return $brocast->send();
		} catch (Exception $e) {
			\Think\Log::write(__FILE__ . __LINE__ ."Caught exception: " . $e->getMessage(),'Err');
		}
		return false;
	}

	function sendAndroidUnicast() {
		try {
			$unicast = new AndroidUnicast();
			$unicast->setAppMasterSecret($this->appMasterSecret);
			$unicast->setPredefinedKeyValue("appkey",           $this->appkey);
			$unicast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			// Set your device tokens here
			$unicast->setPredefinedKeyValue("device_tokens",    "xx"); 
			$unicast->setPredefinedKeyValue("ticker",           "Android unicast ticker");
			$unicast->setPredefinedKeyValue("title",            "Android unicast title");
			$unicast->setPredefinedKeyValue("text",             "Android unicast text");
			$unicast->setPredefinedKeyValue("after_open",       "go_app");
			// Set 'production_mode' to 'false' if it's a test device. 
			// For how to register a test device, please see the developer doc.
			$unicast->setPredefinedKeyValue("production_mode", "true");
			// Set extra fields
			$unicast->setExtraField("test", "helloworld");
			print("Sending unicast notification, please wait...\r\n");
			$unicast->send();
			print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
		}
	}

	function sendAndroidFilecast() {
		try {
			$filecast = new AndroidFilecast();
			$filecast->setAppMasterSecret($this->appMasterSecret);
			$filecast->setPredefinedKeyValue("appkey",           $this->appkey);
			$filecast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			$filecast->setPredefinedKeyValue("ticker",           "Android filecast ticker");
			$filecast->setPredefinedKeyValue("title",            "Android filecast title");
			$filecast->setPredefinedKeyValue("text",             "Android filecast text");
			$filecast->setPredefinedKeyValue("after_open",       "go_app");  //go to app
			print("Uploading file contents, please wait...\r\n");
			// Upload your device tokens, and use '\n' to split them if there are multiple tokens
			$filecast->uploadContents("aa"."\n"."bb");
			print("Sending filecast notification, please wait...\r\n");
			$filecast->send();
			print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
		}
	}

	/**
	 * 消息组播
	 * @param array $data
	 * @param array $arr  组播的标签数组 ['user_1234', '歌手'];
	 * @param bool
	 */
	function sendAndroidGroupcast($data, $arr) {
		try {
			/* 
		 	 *  Construct the filter condition:
		 	 *  "where": 
		 	 *	{
    	 	 *		"and": 
    	 	 *		[
      	 	 *			{"tag":"iostest"}
    	 	 *		]
		 	 *	}
		 	 */
			foreach($arr as $k=>$v){
				$arr[$k] = ['tag'=>$v];
			}
			$filter = [ 'where' => [ 'or' => $arr ] ];
					  
			$groupcast = new AndroidGroupcast();
			$groupcast->setAppMasterSecret($this->appMasterSecret);
			$groupcast->setPredefinedKeyValue("appkey",           $this->appkey);
			$groupcast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			// Set the filter condition
			$groupcast->setPredefinedKeyValue("filter",           $filter);
			$groupcast->setPredefinedKeyValue("ticker",           "Android groupcast ticker");
			$groupcast->setPredefinedKeyValue("title",            $data['title']);
			$groupcast->setPredefinedKeyValue("text",             $data['content']);
			$groupcast->setPredefinedKeyValue("after_open",       "go_activity");
			$groupcast->setPredefinedKeyValue("activity",         "com.sheyingapp.app.ui.HomeActivity");
			$groupcast->setPredefinedKeyValue("production_mode", "true");
			$groupcast->setExtraField("data", json_encode($data, JSON_UNESCAPED_UNICODE));
			$data = filter([$data], 'id,cate,cateName,user_id,node_id,content,link')[0];
			foreach($data as $k=>$v){
				$groupcast->setExtraField($k, $v);
			}
			return $groupcast->send();
		} catch (Exception $e) {
			\Think\Log::write(__FILE__ . __LINE__ ."Caught exception: " . $e->getMessage(),'Err');
		}
	}

	function sendAndroidCustomizedcast() {
		try {
			$customizedcast = new AndroidCustomizedcast();
			$customizedcast->setAppMasterSecret($this->appMasterSecret);
			$customizedcast->setPredefinedKeyValue("appkey",           $this->appkey);
			$customizedcast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			// Set your alias here, and use comma to split them if there are multiple alias.
			// And if you have many alias, you can also upload a file containing these alias, then 
			// use file_id to send customized notification.
			$customizedcast->setPredefinedKeyValue("alias",            "xx");
			// Set your alias_type here
			$customizedcast->setPredefinedKeyValue("alias_type",       "xx");
			$customizedcast->setPredefinedKeyValue("ticker",           "Android customizedcast ticker");
			$customizedcast->setPredefinedKeyValue("title",            "Android customizedcast title");
			$customizedcast->setPredefinedKeyValue("text",             "Android customizedcast text");
			$customizedcast->setPredefinedKeyValue("after_open",       "go_app");
			print("Sending customizedcast notification, please wait...\r\n");
			$customizedcast->send();
			print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
		}
	}

	function sendIOSBroadcast($data) {
		try {
			$brocast = new IOSBroadcast();
			$brocast->setAppMasterSecret($this->appMasterSecret);
			$brocast->setPredefinedKeyValue("appkey",           $this->appkey);
			$brocast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			$brocast->setPredefinedKeyValue("alert",             $data['content']);
			$brocast->setPredefinedKeyValue("description",       $data['cateName']);
			$brocast->setPredefinedKeyValue("production_mode", "true");
			$brocast->setCustomizedField("data", json_encode($data, JSON_UNESCAPED_UNICODE));
			$data = filter([$data], 'id,cate,cateName,user_id,node_id')[0];
			foreach($data as $k=>$v){
				$brocast->setCustomizedField($k, $v);
			}
			
			return $brocast->send();
		} catch (Exception $e) {
			\Think\Log::write(__FILE__ . __LINE__ ."Caught exception: " . $e->getMessage(),'Err');
		}
	}

	function sendIOSUnicast() {
		try {
			$unicast = new IOSUnicast();
			$unicast->setAppMasterSecret($this->appMasterSecret);
			$unicast->setPredefinedKeyValue("appkey",           $this->appkey);
			$unicast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			// Set your device tokens here
			$unicast->setPredefinedKeyValue("device_tokens",    "xx"); 
			$unicast->setPredefinedKeyValue("alert", "IOS 单播测试");
			$unicast->setPredefinedKeyValue("badge", 0);
			$unicast->setPredefinedKeyValue("sound", "chime");
			// Set 'production_mode' to 'true' if your app is under production mode
			$unicast->setPredefinedKeyValue("production_mode", "false");
			// Set customized fields
			$unicast->setCustomizedField("test", "helloworld");
			print("Sending unicast notification, please wait...\r\n");
			$unicast->send();
			print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
		}
	}

	function sendIOSFilecast() {
		try {
			$filecast = new IOSFilecast();
			$filecast->setAppMasterSecret($this->appMasterSecret);
			$filecast->setPredefinedKeyValue("appkey",           $this->appkey);
			$filecast->setPredefinedKeyValue("timestamp",        $this->timestamp);

			$filecast->setPredefinedKeyValue("alert", "IOS 文件播测试");
			$filecast->setPredefinedKeyValue("badge", 0);
			$filecast->setPredefinedKeyValue("sound", "chime");
			// Set 'production_mode' to 'true' if your app is under production mode
			$filecast->setPredefinedKeyValue("production_mode", "false");
			print("Uploading file contents, please wait...\r\n");
			// Upload your device tokens, and use '\n' to split them if there are multiple tokens
			$filecast->uploadContents("aa"."\n"."bb");
			print("Sending filecast notification, please wait...\r\n");
			$filecast->send();
			print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
		}
	}

	/**
	 * 消息组播
	 * @param array $data
	 * @param array $arr  组播的标签数组 ['user_1234', '歌手'];
	 * @param bool
	 */
	function sendIOSGroupcast($data, $arr) {
		try {
			/* 
		 	 *  Construct the filter condition:
		 	 *  "where": 
		 	 *	{
    	 	 *		"and": 
    	 	 *		[
      	 	 *			{"tag":"iostest"}
    	 	 *		]
		 	 *	}
		 	 */
			foreach($arr as $k=>$v){
				$arr[$k] = ['tag'=>$v];
			}
			$filter = [ 'where' => [ 'or' => $arr ] ];
			$groupcast = new IOSGroupcast();
			$groupcast->setAppMasterSecret($this->appMasterSecret);
			$groupcast->setPredefinedKeyValue("appkey",           $this->appkey);
			$groupcast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			$groupcast->setPredefinedKeyValue("filter",           $filter);
			$groupcast->setPredefinedKeyValue("alert",             $data['content']);
			$groupcast->setPredefinedKeyValue("description",       $data['cateName']);
			$groupcast->setPredefinedKeyValue("production_mode", "true");
			$groupcast->setCustomizedField("data", json_encode($data, JSON_UNESCAPED_UNICODE));
			$data = filter([$data], 'id,cate,cateName,user_id,node_id,content,link')[0];
			foreach($data as $k=>$v){
				$groupcast->setCustomizedField($k, $v);
			}
			return $groupcast->send();
		} catch (Exception $e) {
			\Think\Log::write(__FILE__ . __LINE__ ."Caught exception: " . $e->getMessage(),'Err');
		}
	}

	function sendIOSCustomizedcast() {
		try {
			$customizedcast = new IOSCustomizedcast();
			$customizedcast->setAppMasterSecret($this->appMasterSecret);
			$customizedcast->setPredefinedKeyValue("appkey",           $this->appkey);
			$customizedcast->setPredefinedKeyValue("timestamp",        $this->timestamp);

			// Set your alias here, and use comma to split them if there are multiple alias.
			// And if you have many alias, you can also upload a file containing these alias, then 
			// use file_id to send customized notification.
			$customizedcast->setPredefinedKeyValue("alias", "xx");
			// Set your alias_type here
			$customizedcast->setPredefinedKeyValue("alias_type", "xx");
			$customizedcast->setPredefinedKeyValue("alert", "IOS 个性化测试");
			$customizedcast->setPredefinedKeyValue("badge", 0);
			$customizedcast->setPredefinedKeyValue("sound", "chime");
			// Set 'production_mode' to 'true' if your app is under production mode
			$customizedcast->setPredefinedKeyValue("production_mode", "false");
			print("Sending customizedcast notification, please wait...\r\n");
			$customizedcast->send();
			print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
		}
	}
}

// Set your appkey and master secret here
// $demo = new Demo("your appkey", "your app master secret");
// $demo->sendAndroidUnicast();
/* these methods are all available, just fill in some fields and do the test
 * $demo->sendAndroidBroadcast();
 * $demo->sendAndroidFilecast();
 * $demo->sendAndroidGroupcast();
 * $demo->sendAndroidCustomizedcast();
 *
 * $demo->sendIOSBroadcast();
 * $demo->sendIOSUnicast();
 * $demo->sendIOSFilecast();
 * $demo->sendIOSGroupcast();
 * $demo->sendIOSCustomizedcast();
 */