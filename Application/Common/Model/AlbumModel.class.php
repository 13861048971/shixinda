<?php
use Think\Model;
/**
 * 相册
 */
class AlbumModel extends BaseModel {
	public $cacheKey  = 'album_';
	public $typeArr = [0 => '任务', 1 => '套餐'];
	
	protected $_validate;
	
	function __construct(){
		parent::__construct();
		
		$this->_validate = [
			['path', 'require', '缺少图片地址!']
		];
	}
	
	/**
	 * 编辑or添加
	 */
	function edit($data, $id=null){
		if($id){
			$return  = $this->data($data)->where('id=' . (int)$id)->save();
			if(false === $return){
				$this->lastError = '修改失败!';
				return false;
			}
			return $id;
		}
		!$data['user_id'] && $data['user_id'] = $this->user['id'];
		if(!$this->create($data)) 
			return false;
		if(!($id = $this->add()))
			return $this->setError('添加失败!');
		return $id;
	}
	
	/**
	 * 添加或者编辑相册
	 * @param array $imageArr
	 * @param int 	$typeId
	 * @param int	$type
	 * @param int	$default 默认的图片
 	 */
	public function editAlbum($imageArr, $typeId, $default = 0, $type = 0){
		$setDefault = 0;
		foreach($imageArr as $k=>$v){
			$data = ['type' => $type, 'type_id' => $typeId, 
				'update_time' => time(), 'user_id'=>$this->user['id']];
			
			foreach($v as $k2 => $v2){
				if(!$v2) continue;
				$row = ['path' => $v2, 'rank' => $k2,'default'=>0 ];
				if(!$setDefault && $k2 == $default)
					($row['default'] = 1) && ($setDefault = 1);

				//添加
				if($k == 0){
					$addRows[] = $row + $data + ['add_time'=>time()];
					continue;
				}
				$editIds[] = $k;
				$row = $row + $data + ['id'=>$k];
				$this->data($row)->save();
			}
		}
		if($editIds){
			$con = ['id'=>['not in', $editIds], 
				'type'=>$type, 'type_id' => $typeId ];
			$this->where($con)->delete();
		}
		$addRows && $this->addAll($addRows);
		return true;
	}
	
	
	public function getInfo($id){
		$v = $this->find($id);
		if(!$v) return;
		$v['addTime'] 	 = local_date($v['add_time']);
		$v['updateTime'] = local_date($v['update_time']);
		$v['typeName']	 = $this->typeArr[$v['type']];
		$v['thumb'] 	 = getImage($v['path']);
		$v['medium'] 	 = getImage($v['path'], 2);
		!is_file(ROOT_PATH . $v['thumb']) && $v['thumb'] = $v['path'];
		$v['image'] = $v['path'];
		$v['thumb2'] = ROOT_PATH . $v['thumb'];
		return $v;
	}
	
	/**
	 * 
	 * @param array $con      类型,默认是产品分类
	 * @return array
	 **/
	public function getList($con = null, $limit = 20){
		$list = $this->field('id')->where($con)->limit($limit)->order('add_time desc')->select();		
		foreach($list as $k=>$v){
			$list[$k] = $this->getInfo($v['id']);
		}
		return $list;
	}

	/**
	 * 生成二维码
	 * 
	 */
	public function qrcode($qrdata, $logo = null){
		vendor("phpqrcode.phpqrcode");
		$dir = '/Public/qrcode/' . date('Y-m-d') .'/';
		$str = trim($qrdata);
		$logofile = ROOT_PATH . trim($logo);
		if(!$str){
			return $this->setError('缺少二维码内容!');
		}	
		if(!is_dir(ROOT_PATH . $dir) && !mkdir(ROOT_PATH . $dir, 0777)){
			return $this->setError('二维码目录没有权限!');
		}
		
		$filepath = $dir . time() . rand(100, 999) . '.png';
		$qr_eclevel = 'H';	//容错级别 
		$picsize = 8;		//生成图片大小
		QRcode::png($str, ROOT_PATH . $filepath, $qr_eclevel, $picsize, 2);
		
		is_file($logofile) && self::qrLogo(ROOT_PATH . $filepath, $logofile);
		return $filepath;
	}
	
	/**
	 * @string
	 */
	public function upload($type = 'image'){
		$arr = ['image','avatar'];
		!in_array($type) &&  $type = 'image';
		$dir = '/Public/upload/'. $type .'/' . date('Y-m-d') .'/';

		if(!is_dir(ROOT_PATH . $dir) && !mkdir(ROOT_PATH . $dir, 0777)){
			return $this->setError('目录没有权限!');
		}
		
		$streamData = isset($GLOBALS['HTTP_RAW_POST_DATA'])? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
		empty($streamData) && ($streamData = file_get_contents('php://input'));  
		if(!$streamData) 
			return $this->setError('没有收到文件!');
		
		$picType = self::pictype( substr($streamData ,0 , 5 ) );
		if(!$picType)
			return $this->setError('只能上传jpg,png,gif图片!');
		$filepath = $dir . time() . rand(100, 999) . '.'.$picType;
		$ret = file_put_contents(ROOT_PATH . $filepath, $streamData, true);
		if(!$ret){
			echo $streamData;
			echo $filepath;
			return $this->setError('保存文件失败!');
		}
		return $filepath;  
	}
	
	//图片类型
	static function pictype( $header )  
	{ 
		if ( $header { 0 }. $header { 1 }== "\x89\x50" )
			 return 'png' ; 
		 
		if( $header { 0 }. $header { 1 } == "\xff\xd8" )
			 return 'jpg' ; //jpeg 
		 
		if( $header { 0 }. $header { 1 }. $header { 2 } == "\x47\x49\x46" ){
			 if( $header { 4 } == "\x37" ) 
				 return 'gif' ; //gif89
			 if( $header { 4 } == "\x39" ) 
				 return 'gif' ; //gif89
		}
		return false;
	} 
	
	//生成带logo 的二维码
	static function qrLogo($qrPng, $logo = null){
		if (!$logo) {
			return ;
		} 
		$QR = imagecreatefromstring(file_get_contents($qrPng)); 
		$logo = imagecreatefromstring(file_get_contents($logo)); 
		$QR_width = imagesx($QR);//二维码图片宽度 
		$QR_height = imagesy($QR);//二维码图片高度 
		$logo_width = imagesx($logo);//logo图片宽度 
		$logo_height = imagesy($logo);//logo图片高度 
		// $logo_qr_width = $QR_width / 5; 
		// $scale = $logo_width/$logo_qr_width; 
		// $logo_qr_height = $logo_height/$scale; 
		$from_width = ($QR_width - $logo_width) / 2;
		$from_height = ($QR_height - $logo_height) / 2;
		//重新组合图片并调整大小 
		imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_width,  
		$logo_height, $logo_width, $logo_height); 
		//输出图片 
		imagepng($QR, $qrPng); 
		return true;
	}

		
	/**
	 * 切图
	 * @param string $src
	 * @return bool
	 */
	static function cutImage($src){
		$info = d('config')->getInfo('image')['value'];
		$image = new \Think\Image(\Think\Image::IMAGE_GD, ROOT_PATH . $src); // GD库
		$type = $image->type();
		
		//中图
		if( ($wh = explode('*', $info['medium'])) && $wh[0] && $wh[1]){
			$imagePath = getImage(ROOT_PATH . $src, 2);
			//$image->thumb($wh[0], $wh[1],\Think\Image::IMAGE_THUMB_CENTER)->save($imagePath);
			$image->thumb($wh[0], $wh[1])->save($imagePath);
		}
		
		//缩略图
		if( ($wh = explode('*', $info['thumb'])) && $wh[0] && $wh[1]){
			$imagePath = getImage(ROOT_PATH . $src);
			$image->thumb($wh[0], $wh[1])->save($imagePath);
		}
		
		//水印
		if(($water = $info['water']) ||  $info['waterText'] ){
			$image = new \Think\Image(\Think\Image::IMAGE_GD, ROOT_PATH . $src);
			if(is_file(ROOT_PATH . $water))
				$image->water(ROOT_PATH . $water, \Think\Image::IMAGE_WATER_NORTHWEST,50);

			if($text = $info['waterText']){
				$font = CORE_PATH . 'Verify/ttfs/2.ttf';
				$image->text($text, $font, 30, '#eeeeee', \Think\Image::IMAGE_WATER_SOUTHEAST, -20);
			}
			$image->save(ROOT_PATH . $src);
		}
		return true;
	}

	public function getPageList($con, $field='id', $order="id desc", $perNum = 15){
		
		$data = parent::getPageList($con, $field, $order, $perNum);
		foreach($data['list'] as $k=>$v){
			$v = $this->getInfo($v['id']);
			
			$data['list'][$k] =  $v;
		}
		
		return $data;
	}
}