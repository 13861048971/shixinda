<?php 
/**
 * 生成select表单控件
 **/
use Think\Controller;

class SelectWidget extends Controller{
	/**
	 * @param array $data #键有*name,*list,selected,nameKey,valueKey,padd1,paddText
	 */
	function index($data){
		if(!$data['name'] || !$data['list'] || !is_array($data['list'])){
			return;
		}

		$valueKey = $data['valueKey'] ? $data['valueKey'] : 'id';
		$nameKey  = $data['nameKey']  ? $data['nameKey']  : 'name';
		$str = '<select';
		$data['readonly'] && $str .= ' readonly ';
		$str .= ' name="'. $data['name'] .'" class="form-control">';

		if($data['padd1'] || $data['paddText']){
			!$data['paddText'] && $data['paddText'] = '请选择';
			$str .= '<option value="">-' .$data['paddText']. '-</option>';
		}
		foreach($data['list'] as $k=>$v){
		  if($data['group'] && 0 == $v['parent_id']){
			if(isset($pid)) {
				$str .= '</optgroup>';
			}
			$str.= '<optgroup label="' . $v[$nameKey] . '">';
			$pid = $v['id'];
			continue;
		  }
		
			if(is_array($v)){
				$value = isset($v[$valueKey]) ? $v[$valueKey] : $k;
				$selected = ((string)$value === $data['selected'] ? 'selected':'');
				!$selected && ((string)$value === $data['value']) && ($selected = 'selected');
				
				$str .= '<option value="'. $value .'" '. $selected .'>'. 
					$v[$nameKey] .'</option>';
				continue;
			}
			$selected = ((string)$k === (string)$data['selected']) ? 'selected':'';
			!$selected  && ((string)$k === $data['value']) && ($selected = 'selected');
			
			$str .= '<option value="'. $k .'" '. $selected .'>'. 
				$v .'</option>';
		}
		$data['group'] && $str .= '</optgroup>';
		$str .= '</select> ';
		
		if($data['return'])
			return $str;
		
		echo $str;
	}
	
	function select($data){
		return $this->index($data);
	}
	
	/**
	 * 生成radio
	 * @param array $data 选择按钮的信息
	 * 
	 */
	function radio($data){
		if(!$data['name'] || !$data['list'] || !is_array($data['list'])){
			return;
		}

		$valueKey = $data['valueKey'] ? $data['valueKey'] : 'value';
		$nameKey  = $data['nameKey']  ? $data['nameKey']  : 'name';
		if( !isset($data['checked']) && isset($data['selected']) )
            $data['checked'] = $data['selected']; 
		if(!$data['checked'])
		    $data['checked'] = '女';
		
		$str = '';
		foreach($data['list'] as $k=>$v){
			$str .= '<label class="radio-inline">';
			if(is_array($v)){
				$value = isset($v[$valueKey]) ? $v[$valueKey] : $k;
				$str .= '<input name="'. $data['name'] .'" type="radio" value="'. $value .'" '.
					($value==$data['checked']? 'checked':null) .'>'. 
					$v[$nameKey] .'</label>';
				continue;
			}
			
			$str .= '<input name="'. $data['name'] .'" type="radio" value="'. $k .'" '. 
				($k == $data['checked']? 'checked':null) .'>'. 
				$v . '</label>';
		}
		if($data['return']) return $str;
		echo $str;
	}
	
	/**
	 * 生成checkbox
	 */
	function checkbox($data){
		$str = '';
		$list = $data;
		$data['list'] && $list = $data['list'];
		
		if($data['list'] && $data['value']){
			$arr = $data['value'];
			!is_array($arr) && $arr = explode(',', $data['value']);
		}
		
		foreach($list as $k=>$v){
			$str .= '<label class="checkbox-inline">';
			$value = isset($v[$data['valueKey']]) ? $v[$data['valueKey']] : $v['value'];
			!$value && ($value = isset($v[$v['valueKey']]) ? $v[$v['valueKey']] : $v['value']);
			!$value && ($value =  $v['name']);
			!$value && ($value =  $k);
			$nameKey = $data['name']  ? $data['name']  : 'name';
			!$nameKey && ($nameKey = $v['nameKey']  ? $v['nameKey']  : 'name');
			$checked = ($v['checked'] || $v['selected']) ? 'checked':'';
			!$checked && in_array($value, $arr) && ($checked = 'checked');
			$str .= '<input name="'. $nameKey .'['. $k .']"  type="checkbox" value="'.
				$value .'" '. $checked .'>'. $v['name'] .'</label>';
		}
		if($data['return']) return $str;
		echo $str;
	}

	/**
	 * select多级分类选择下拉框
	 * 
	 */
	function postCate(){
	    $con = $_GET;
	    $con['pid'] = '0';
	    $postCateInfo = d('postCate')->getList($con);//分类表所有信息
	    $url = "/admin/user/postCateChildren/pid/";
	    $row = d('post')->where(['id'=>$con['id']])->find();;
	    $cateInfo = d('postCate')->getInfo((int)$row['post_cate_id']);
	    $rowId = (int)$row['post_cate_id'];
	    $cateList = d('post')->getPostCateList($rowId);//父级分类集合
	    $data = [
	        "url" => $url,
	        "name" =>'post_cate_id',
	        'cateName' =>$cateInfo['name'],//当前分类名称
	    ];
	    isset($postCateInfo)?$list = $postCateInfo:null;
	    isset($cateList)?$cateList:null;
	    $countnum = count($cateList);
	    $cate = [];
	    foreach ($list as $v){
	        $cate[] = $v;
	    }
	    foreach ($cateList as $ko=>$vo){
	        $cateList[$ko]['list'] = d('postCate')->getList(['pid'=>$vo['pid']]);
	        $cateList[$ko]['count'] = count($cateList[$ko]['list']);
	    }
	    
	    $cateList = array_reverse($cateList);//按键值逆向排序
	    $value = $data['cateName']?$data['cateName']:'请选择';
        $this->assign('data',$data);
        $this->assign('cate',$cate);
        $this->assign('cateList',$cateList);//有上级的信息列表
        
        $this->assign('list',$list);//无上级的信息列表
        $this->assign('value',$value);
        $this->assign('count',$countnum);//上级数量
        $this->display('Widget:Select:cateList');
	}
	/**
	 * 选择地区
	 */
	function region($data){
		$pid = (int)$data['parent_id'];
		$regMod = d('region');
		$regionId = $data['region_id'];
		//$regionId = 3408;
		$list = [[],[],[],[]];
		$list[0] = $regMod->getList(['parent_id' => $pid]);
		$list[1] = $regMod->getList(['parent_id' => $list[0][0]['id']]);
		if(!$regionId){
			$this->assign('list', $list);
			$this->display('Widget:Select:region');
			return ;
		}
		
		$ins = [1];
		$region = $regMod->getInfo($regionId);					//区
		if($region['parent_id']){
			$ins[] = $region['parent_id'];
			$parent = $regMod->getInfo($region['parent_id']);	//城市
			$parent['parent_id'] && ($ins[] = $parent['parent_id']);
			$con['parent_id'] = ['in', $ins];
			$list2 = $regMod->getList($con);
			$ins[] = $regionId;
			foreach($list2 as $v){
				if(in_array($v['id'], $ins))
					$v['selected'] = 1;
				
				$list[$v['region_type']][] = $v;
			}
		}

		$this->assign('list', $list);
		$this->display('Widget:Select:region');
	}
	
	/**
	 * 地区多选
	 */
	function regionMulti($data){
		$regMod = d('region');
		$list[0] = $regMod->getList(['parent_id' => 0]);
		$list[0][0]['selected'] = 1;
		$list[1] = $regMod->getList(['parent_id' => $list[0][0]['id']]);
		$list[2] = [];
		$list[3] = [];
		
		$selected = $data['selected'];
		if(is_array($selected)){
			$regions = $regMod->getList(['id'=>['in', $selected]]);
		}
		$this->assign('list', $list);
		$this->assign('selectedRegion', $regions);
		$this->display('Widget:Select:regionMulti');
	}

		
	/**
	 * @param array $data ['uid'=> , 'name'=>'user_id' 'require' => false] 
	 */
	function user($data = []){
		$uid = (int)$data['uid'];
		if($uid){
			$user= d('user')->getInfo($uid);
			$this->assign('user', $user);
		}
		
		!$data['name'] && $data['name'] = 'user_id';
		$this->assign('data', $data);
		$this->assign('require', $data['require']);
		$this->display('Widget:Select:user');
	}
	
	/**
	 * @param array $data ['ids'=>[],'require'=>false] 
	 */
	function goods($data = []){
		$ids = $data['ids'];
		if($ids && is_array($ids)){
			$con['id'] = ['in', $ids];
			$goodsList = d('goods')->getList($con);
			$this->assign('goodsList', $goodsList);
		}
		
		$this->display('Widget:Select:goods');
	}
	
	/**
	 * @param array $data ['ids'=>[],'require'=>false] 
	 */
	function goods2($data = []){
		$ids = explode(',', $data['value']);
		if($ids && is_array($ids)){
			$con['id'] = ['in', $ids];
			$goodsList = d('goods')->getList($con);
			$this->assign('goodsList', $goodsList);
		}
		
		$name = $data['name'] ? $data['name'] : 'ids';
		$this->assign('name', $name);
		$this->assign('ids', $ids);
		$this->display('Widget:Select:goods2');
	}
	
}

