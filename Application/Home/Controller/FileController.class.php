<?php
use Think\Controller;
// Vendor('Qiniu.Auth','','.class.php');
// Vendor('Qiniu.Zone','','.class.php');
// Vendor('Qiniu.Config','','.class.php');
// import('Vendor.Qiniu.Auth');
// import('Vendor.Qiniu.Storage.UploadManager');
Vendor('Vendor.Qiniu.GetToken','','.class.php');
/**
 * 上传文件接口
 */

class FileController extends PublicController {
    //获取七牛云的token
    function getQiNiuToken(){
        $auth = new \Vendor\Qiniu\GetToken(c('QINIUYUN')['accessKey'],c('QINIUYUN')['secrectKey']);
        
        $upToken = $auth->getUploadToken(c('QINIUYUN')['bucket'],$_POST['imageName']);
        return  ajaxReturn(0,'',['token'=>$upToken]);
    }
    
	function image(){
		header('Access-Control-Allow-Origin:*');
		$type = $_POST['type'];
		$typeArr = ['image', 'avatar', 'goods'];
		!in_array($type, $typeArr) && ($type = 'image');

        $output = array('error' => 1, 'info' => '', 'src' => '');
		$upload = new \Think\Upload();							// 实例化上传类
		$upload->maxSize   = 3145728 ;							// 设置附件上传大小
		$upload->exts      = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->rootPath  = 'Public/upload/'; 						// 设置附件上传根目录
		$upload->savePath  = $type . '/'; 							// 设置附件上传（子）目录
		// 上传文件 
		$info = $upload->upload();
		if(!$info) {
			$output['error'] = 1;
            $output['info'] = $upload->getError();
		}else{
			$images = [];
			foreach($info as $k=>$file){
				$src = '/'.$upload->rootPath . $file['savepath'] . $file['savename'];
				$output['file'] = $file;
				$output['error'] = 0;
				$output['src'] = $src;
				$output['image'] = $src;
				if('image' == $type){
					$output['image'] = AlbumModel::cutImage($src, '', '');
					$thumb = getImage($src);
				}
				$images[$k] = ['src'=>$src,'image'=>$src, 'thumb'=> $thumb];
			}
			
			$len = count($images);
			
			$len > 1 && ($images = [ 'list' => $images]);
			$len == 1 && ($images = current($images));
		}
		
		if($output['error']){
			return ajaxReturn2($output['error'], $output['info']);
		}
		
		return ajaxReturn2($output['error'], $output['info'], $images);
	}
	
	function image2(){
		$mod = d('Album');
		
		if(! ($src = $mod->upload() ) )
			return ajaxReturn2(1, $mod->getError());

		return ajaxReturn2(0,'',['src'=>$src]);
	}
	
		/**
     * 文件上传
     *
     * @return Response
     */
    public function upload(){
        header('Access-Control-Allow-Origin:*');
		
		$type = $_POST['type'];
		$typeArr = ['image', 'avatar', 'goods'];
		!in_array($type, $typeArr) && $type = 'image';

        $output = array('err' => 0, 'msg' => '', 'src' => '');
		$upload = new \Think\Upload();							// 实例化上传类
		$upload->maxSize   = 3145728 ;							// 设置附件上传大小
		$upload->exts      = array('jpg', 'gif', 'png', 'jpeg','');// 设置附件上传类型
		$upload->rootPath  = 'Public/upload/'; 						// 设置附件上传根目录
		$upload->savePath  = $type . '/'; 							// 设置附件上传（子）目录
		// 上传文件 
		$info = $upload->upload();
		if(!$info) {
			$output['err'] = 1;
            $output['msg'] = $upload->getError();;
		}else{
			$file = $info['file'];
			$src = '/'.$upload->rootPath . $file['savepath'] . $file['savename'];
			$output['file'] = $file;
			$output['src'] = $src;
			if('goods' == $type){
				$output['image'] = AlbumModel::cutImage($src, '', '');
				$output['thumb'] = getImage($src);
			}
		}

        echo json_encode($output);
    }

	
	/**
	 * 生成二维码
	 */
	public function qrcode(){
		$str = $_REQUEST['qrdata'];
		$logo = $_REQUEST['logo'];
		$mod = d('Album');
		if($filepath = $mod->qrcode($str, $logo)){
			return ajaxReturn(0, '', ['path' => $filepath]);
		}
		return ajaxReturn(0, $mod->getError());
	}
}