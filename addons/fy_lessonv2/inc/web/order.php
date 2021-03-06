<?php
/**
 * 课程订单管理
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
 */

load()->model('mc');

/* 订单状态列表 */
$typeStatus = new TypeStatus();
$orderStatusList = $typeStatus->orderStatus();

if($operation == 'display') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;

	$condition = " a.uniacid=:uniacid AND a.is_delete=:is_delete ";
	$params[':uniacid'] = $uniacid;
	$params[':is_delete'] = intval($_GPC['is_delete']);;

	if (!empty($_GPC['keyword'])) {
		$condition .= " AND (a.ordersn LIKE :keyword OR a.bookname LIKE :keyword) ";
		$params[':keyword'] = "%".$_GPC['keyword']."%";
	}
	if ($_GPC['status']!='') {
		$condition .= " AND a.status=:status ";
		$params[':status'] = $_GPC['status'];
	}
	if ($_GPC['is_verify']!='') {
		$condition .= " AND a.is_verify=:is_verify ";
		$params[':is_verify'] = $_GPC['is_verify'];
	}
	if ($_GPC['validity']==2) {
		$condition .= " AND validity<:validity AND validity>0 AND status>0";
		$params[':validity'] = time();
	}
	if (!empty($_GPC['nickname'])) {
		$condition .= " AND ((b.nickname LIKE :nickname) OR (b.realname LIKE :nickname) OR (b.mobile LIKE :nickname)) ";
		$params[':nickname'] = "%".$_GPC['nickname']."%";
	}
	if (!empty($_GPC['time']['start'])) {
		$starttime = strtotime($_GPC['time']['start']);
		$endtime = strtotime($_GPC['time']['end']);
		$endtime = !empty($endtime) ? $endtime + 86399 : 0;
		if (!empty($starttime)) {
			$condition .= " AND a.addtime>=:starttime ";
			$params[':starttime'] = $starttime;
		}
		if (!empty($endtime)) {
			$condition .= " AND a.addtime<=:endtime ";
			$params[':endtime'] = $endtime;
		}
	}

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' .tablename($this->table_order). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE {$condition}", $params);

	if(!$_GPC['export']){
		$list = pdo_fetchall("SELECT a.*,b.nickname,b.realname,b.mobile FROM " .tablename($this->table_order). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE {$condition} ORDER BY a.id desc, a.addtime DESC LIMIT " .($pindex - 1) * $psize. ',' . $psize, $params);
		foreach($list as $k=>$v){
			$vipNumber = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_member_vip). " WHERE uid=:uid AND validity > :validity", array(':uid'=>$v['uid'], ':validity'=>time()));
			$list[$k]['vip'] = $vipNumber>0 ? 1 : 0;
		}

		$pager = pagination($total, $pindex, $psize);

		/* 黑名单 */
		$blacklist = pdo_fetchall("SELECT uid FROM ".tablename($this->table_blacklist)." WHERE uniacid = :uniacid", array(':uniacid' => $uniacid));
		foreach($blacklist as $item){
			$blackids[] = $item['uid'];
		}

		if($_GPC['status']=='-1'){
			$filename = "已取消课程订单";
		}elseif($_GPC['status']=='0'){
			$filename = "未支付课程订单";
		}elseif($_GPC['status']=='1'){
			$filename = "已付款课程订单";
		}elseif($_GPC['status']=='2'){
			$filename = "已评价课程订单";
		}else{
			$filename = "全部课程订单";
		}

	}else{
		set_time_limit(180);
		$psize = 10000;
		$max = ceil($total/$psize);
		$random = random(4);

		for($i=1; $i<=$max; $i++){
			$outputlist = pdo_fetchall("SELECT a.*,b.nickname,b.realname,b.mobile,b.msn,b.occupation,b.company,b.graduateschool,b.grade,b.address FROM " .tablename($this->table_order). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE {$condition} ORDER BY a.id desc, a.addtime DESC LIMIT " . ($i - 1) * $psize . ',' . $psize, $params);

			$j = 0;
			$verify_user = array();
			foreach ($outputlist as $key => $value) {
				$arr[$j]['ordersn']         = "'".$value['ordersn'];
				$arr[$j]['nickname']		= preg_replace('#[^\x{4e00}-\x{9fa5}A-Za-z0-9]#u','',$value['nickname']);
				$arr[$j]['realname']        = $value['realname'];
				$arr[$j]['mobile']          = $value['mobile'];
				$arr[$j]['spec_name']		= $value['spec_name'];
				if($value['lesson_type']==0){
					$arr[$j]['lesson_type_name'] = "普通课程";
				}elseif($value['lesson_type']==1){
					$arr[$j]['lesson_type_name'] = "报名课程";
				}
				$arr[$j]['bookname']        = $value['bookname'];
				$arr[$j]['price']           = $value['price'];
				$arr[$j]['integral']        = $value['integral'];
				if($value['paytype'] == 'credit'){
					$arr[$j]['paytype'] = "余额支付";
				}elseif($value['paytype'] == 'wechat'){
					$arr[$j]['paytype'] = "微信支付";
				}elseif($value['paytype'] == 'alipay'){
					$arr[$j]['paytype'] = "支付宝支付";
				}elseif($value['paytype'] == 'offline'){
					$arr[$j]['paytype'] = "线下支付";
				}elseif($value['paytype'] == 'admin'){
					$arr[$j]['paytype'] = "后台支付";
				}elseif($value['paytype'] == 'wxapp'){
					$arr[$j]['paytype'] = "微信小程序";
				}elseif($value['paytype'] == '' && $value['status']>0){
					$arr[$j]['paytype'] = "已支付";
				}else{
					$arr[$j]['paytype'] = "无";
				}
				$arr[$j]['commission1']     = $value['commission1'];
				$arr[$j]['commission2']     = $value['commission2'];
				$arr[$j]['commission3']     = $value['commission3'];
				$arr[$j]['teacher_income']  = $value['teacher_income'];
				$arr[$j]['income_amount']	= round($value['price']*$value['teacher_income']*0.01,2);
				
				if($value['status'] == '-2'){
					$arr[$j]['status'] = "已退款";
				}elseif($value['status'] == '-1'){
					$arr[$j]['status'] = "已取消";
				}elseif($value['status'] == '0'){
					$arr[$j]['status'] = "未支付";
				}elseif($value['status'] == '1'){
					$arr[$j]['status'] = "已付款";
				}elseif($value['status'] == '2'){
					$arr[$j]['status'] = "已评价";
				}
				$arr[$j]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
				if($value['lesson_type']==1){
					$arr[$j]['verify_name'] = $value['is_verify']==1 ? '已核销' : '未核销';
				}else{
					$arr[$j]['verify_name'] ='';
				}

				$verify_info = json_decode($value['verify_info'], true);
				$arr[$j]['verify_uid'] = $verify_info['verify_uid'];
				if($verify_info['verify_uid'] && empty($verify_user[$verify_info['verify_uid']])){
					$verify_user = pdo_get($this->table_mc_members, array('uid'=>$verify_info['verify_uid']), array('nickname'));
					$verify_user[$verify_info['verify_uid']] = $verify_user['nickname'];
				}
				$arr[$j]['verify_nickname'] = $verify_user[$verify_info['verify_uid']];
				$arr[$j]['verify_time'] = $verify_info['verify_time'] ? date('Y-m-d H:i:s', $verify_info['verify_time']) : '';

				$appoint_info = json_decode($value['appoint_info'], true);
				foreach($appoint_info as $key=>$item){
					$arr[$j][$key] = $item;
				}
				$j++;
			}

			$title =  array('订单编号','昵称','姓名','手机号码','规格名称','课程类型','课程名称','课程价格(元)','获赠积分','付款方式','一级佣金(元)','二级佣金(元)','三级佣金(元)','讲师分成(%)','讲师收入(元)','订单状态','下单时间','核销状态','核销员uid','核销员昵称','核销时间','报名课程信息');
			$filename = '课程订单'.$random.$uniacid.'-'.$i;

			$phpexcel = new FyLessonv2PHPExcel();
			$savetype = $max>1 ? 1 : 0;
			$phpexcel->exportTable($title, $arr, $filename, $savetype);
			unset($arr);

			$filenameArr[] = $filename.'-'.date('Ymd').'.xls';
		}

		/* 打包下载 */
		$filepath = '../data/excel/';
		$pack = $filepath.'课程订单'.$random.$uniacid.'-'.date('Ymd').'.zip';
		$zip = new ZipArchive();

		if($zip->open($pack, ZipArchive::CREATE)=== TRUE){
			foreach($filenameArr as $file){
				if(file_exists($filepath.$file)){
					$zip->addFile($filepath.$file);
				}else{
					exit('无法打开文件，或者文件创建失败');
				}
			}
			$zip->close();
		}

		header('Content-Type:text/html;charset=utf-8');
		header('Content-disposition:attachment;filename=课程订单'.$random.$uniacid.'-'.date('Ymd').'.zip');
		$filesize = filesize($pack);
		readfile($pack);
		header('Content-length:'.$filesize);

		$files = glob($filepath.'*');
		foreach($files as $file) {
			if(strstr($file, "课程订单{$random}{$uniacid}-")){
				unlink($file);
			}
		}
	}

}elseif ($operation == 'detail') {
	$id = intval($_GPC['id']);
	$order = pdo_fetch("SELECT a.*,b.nickname,b.realname,b.mobile,b.msn,b.occupation,b.company,b.graduateschool,b.grade,b.address,b.avatar FROM " .tablename($this->table_order). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE a.uniacid=:uniacid AND a.id=:id", array(':uniacid'=>$uniacid,':id'=>$id));
	if (empty($order)) {
		message('该订单不存在或已被删除!');
	}

	/* 报名课程信息 */
	$appoint_info = json_decode($order['appoint_info'], true);
	$verify_info = json_decode($order['verify_info'], true);
	if($verify_info['verify_uid']>0){
		$verify_user = pdo_get($this->table_mc_members, array('uid'=>$verify_info['verify_uid']), array('nickname'));
	}

	if($order['paytype']=='wechat' || $order['paytype']=='wxapp'){
		$wechatPay = $this->getWechatPayNo($order['id']);
		$wechatPay['transaction'] = unserialize($wechatPay['tag']);
	}
	
	if(empty($order['avatar'])){
		$avatar = MODULE_URL."template/mobile/images/default_avatar.jpg";
	}else{
		$inc = strstr($order['avatar'], "http://") || strstr($order['avatar'], "https://");
		$avatar = $inc ? $order['avatar'] : $_W['attachurl'].$order['avatar'];
	}

	$evaluate = pdo_fetch("SELECT content FROM " .tablename($this->table_evaluate). " WHERE uniacid=:uniacid AND orderid=:orderid", array(':uniacid'=>$uniacid,':orderid'=>$order['id']));

	if($order['member1']>0){
		$member1 = pdo_fetch("SELECT nickname,avatar FROM " .tablename($this->table_mc_members). " WHERE uid=:uid", array(':uid'=>$order['member1']));
		if(empty($member1['avatar'])){
			$avatar1 = MODULE_URL."template/mobile/images/default_avatar.jpg";
		}else{
			$avatar1 = strstr($member1['avatar'], "http://") ? $member1['avatar'] : $_W['attachurl'].$member1['avatar'];
		}
	}
	if($order['member2']>0){
		$member2 = pdo_fetch("SELECT nickname,avatar FROM " .tablename($this->table_mc_members). " WHERE uid=:uid", array(':uid'=>$order['member2']));
		if(empty($member2['avatar'])){
			$avatar2 = MODULE_URL."template/mobile/images/default_avatar.jpg";
		}else{
			$avatar2 = strstr($member2['avatar'], "http://") ? $member2['avatar'] : $_W['attachurl'].$member2['avatar'];
		}
	}
	if($order['member3']>0){
		$member3 = pdo_fetch("SELECT nickname,avatar FROM " .tablename($this->table_mc_members). " WHERE uid=:uid", array(':uid'=>$order['member3']));
		if(empty($member3['avatar'])){
			$avatar3 = MODULE_URL."template/mobile/images/default_avatar.jpg";
		}else{
			$avatar3 = strstr($member3['avatar'], "http://") ? $member3['avatar'] : $_W['attachurl'].$member3['avatar'];
		}
	}

	if($_GPC['submit_type']=='validity'){
		$validity = strtotime($_GPC['validity']);

		if($validity != $order['validity']){
			pdo_update($this->table_order, array('validity'=>$validity), array('id'=>$id));
			message("更新成功", $this->createWebUrl('order', array('op'=>'detail','id'=>$id)), "success");
		}
	}

}elseif($operation == 'confirmpay') {
	$today = strtotime("today");
	$orderid = $_GPC['orderid'];
	$order = pdo_fetch("SELECT * FROM " .tablename($this->table_order). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid,':id'=>$orderid));
	if (empty($order)) {
		message('该订单不存在或已被删除!');
	}
	$lessonmember = pdo_fetch("SELECT * FROM " . tablename($this->table_member) . " WHERE uniacid=:uniacid AND uid=:uid", array(':uniacid'=>$uniacid,':uid'=>$order['uid']));
	
	$data = array(
		'status'   => 1,
		'paytime'  => time(),
		'paytype'  => 'offline',
		'validity' => $order['validity']>0 ? time()+86400*$order['validity'] : 0,
	);

	if(pdo_update($this->table_order, $data, array('id'=>$orderid))){
		/* 增加课程购买人数 */
		$this->updateLessonNumber($order, $setting);

		/* 统计数据表 */
		$this->staticAmount($uniacid, 2, $order['price']);
		
		/* 判断分销员状态变化 */
		$this->checkAgentStatus($lessonmember, $comsetting, $order['price']);

		/* 一级佣金 */
        if ($order['member1'] > 0 && $order['commission1'] > 0) {
        	$this->sendCommissionToUser($uniacid, $order['member1'], $lessonmember['nickname'], 2, $setting, $order, $order['commission1'], 1);
        }
		
		/* 二级佣金 */
        if ($order['member2'] > 0 && $order['commission2'] > 0) {
            $this->sendCommissionToUser($uniacid, $order['member2'], $lessonmember['nickname'], 2, $setting, $order, $order['commission2'], 2);
        }
		
		/* 三级佣金 */
        if ($order['member3'] > 0 && $order['commission3'] > 0) {
            $this->sendCommissionToUser($uniacid, $order['member3'], $lessonmember['nickname'], 2, $setting, $order, $order['commission3'], 3);
        }

		/* 讲师分成 */
        if ($order['price'] > 0 && $order['teacher_income'] > 0) {
            $this->sendCommissionToTeacher($uniacid, $order, $setting);
        }
		
		/* 购买成功模版消息通知用户 */
		$this->sendMessageToUser($uniacid, $setting, $order, 2, $validity="");
		
        /* 赠送积分操作 */
        if ($order['integral'] > 0) {
        	$this->handleUserIntegral($order['ordersn'], $order['uid'], $order['integral']);
        }

		$this->addSysLog($_W['uid'], $_W['username'], 3, "课程订单", "更改订单编号:{$order['ordersn']}的课程状态为已付款");
		message("确认付款成功", $_GPC['refurl'], "success");
	}

}elseif($operation == 'recycle') {
	$ids = explode(",", $_GPC['ids']);
	foreach($ids as $id){
		pdo_update($this->table_order, array('is_delete'=>1), array('uniacid'=>$uniacid,'id' => $id));
	}

	if(!empty($_GPC['ids'])){
		$this->addSysLog($_W['uid'], $_W['username'], 2, "课程订单", "批量加入回收站，课程订单ID:{$_GPC['ids']}");
	}

	$data = array(
		'code' => 0,
		'msg'  => '批量加入回收站成功'
	);
	$this->resultJson($data);

}elseif($operation == 'delAll') {
	$ids = explode(",", $_GPC['ids']);
	foreach($ids as $id){
		pdo_delete($this->table_order, array('id'=>$id));
	}

	if(!empty($_GPC['ids'])){
		$this->addSysLog($_W['uid'], $_W['username'], 2, "课程订单", "批量删除课程订单ID:{$_GPC['ids']}");
	}

	$data = array(
		'code' => 0,
		'msg'  => '批量永久删除成功'
	);
	$this->resultJson($data);

}elseif($operation == 'black') {
	$id = $_GPC['id'];/* 订单id */
	$order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id AND uniacid=:uniacid  LIMIT 1", array(':id' => $id, ':uniacid' => $uniacid));

	if (empty($order)) {
		message('数据不存在!');
	}

	$data = array(
		'uniacid'   => $uniacid,
		'uid'    => $order['uid'],
		'addtime'   => time()
	);

	$blacker = pdo_fetch("SELECT * FROM " . tablename($this->table_blacklist) . " WHERE uid=:uid AND uniacid=:uniacid  LIMIT 1", array(':uid' => $order['uid'], ':uniacid' => $uniacid));

	if (!empty($blacker)) {
		$res = pdo_delete($this->table_blacklist, array('uniacid'=>$uniacid, 'uid'=>$order['uid']));
		if($res){
			$this->addSysLog($_W['uid'], $_W['username'], 2, "课程订单->黑名单", "把uid:{$order['uid']}的用户移出黑名单");
		}
		message('移出黑名单成功！!', $_GPC['refurl'], 'success');
	}else{
		$res = pdo_insert($this->table_blacklist, $data);
		if($res){
			$this->addSysLog($_W['uid'], $_W['username'], 2, "课程订单->黑名单", "把uid:{$order['uid']}的用户加入黑名单");
		}
		message('加入黑名单成功！', $_GPC['refurl'], 'success');
	}

}elseif($op=='createOrder'){

	if(checksubmit('submit')){
		$lessonid = intval($_GPC['lessonid']);
		$price = floatval($_GPC['price']);
		$teacher_income = intval($_GPC['teacher_income']);
		$validity = intval($_GPC['validity']);
		$uid = intval($_GPC['uid']);
		$income_switch = intval($_GPC['income_switch']);
		$sale_switch = intval($_GPC['sale_switch']);

		if(empty($lessonid)){
			message("请选择课程");
		}
		if(empty($uid)){
			message("请选择用户");
		}
		$lesson = pdo_fetch("SELECT id,bookname,price,teacherid,teacher_income,integral,commission,stock,buynum FROM " .tablename($this->table_lesson_parent). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid, ':id'=>$lessonid));
		if(empty($lesson)){
			message("指定课程不存在，请重新选择");
		}

		$member = pdo_fetch("SELECT * FROM " .tablename($this->table_member). " WHERE uid=:uid", array(':uid'=>$uid));
		if(empty($member)){
			message("指定用户不存在，请重新选择");
		}

		$order = array(
			'acid'	   => $_W['acid'],
			'uniacid'  => $uniacid,
			'ordersn'  => date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8),
			'uid'	   => $member['uid'],
			'lessonid' => $lesson['id'],
			'bookname' => $lesson['bookname'],
			'marketprice' => $lesson['price'],
			'price'	   => $price,
			'spec_day' => $validity,
			'teacherid'=> $lesson['teacherid'],
			'teacher_income' => $teacher_income,
			'integral' => $lesson['integral'],
			'paytype'  => 'admin',
			'paytime'  => time(),
			'validity' => $validity>0 ? time()+$validity*86400 : 0,
			'status'   => 1,
			'addtime'  => time(),
		);
		/* 检查当前分销功能是否开启且课程价格大于0 */
		if ($comsetting['is_sale'] == 1 && $price > 0 && $sale_switch==1) {
			$order['commission1'] = 0;
			$order['commission2'] = 0;
			$order['commission3'] = 0;

			if ($comsetting['self_sale'] == 1) {
				/* 开启分销内购，一级佣金为购买者本人 */
				$order['member1'] = $uid;
				$order['member2'] = $this->getParentid($uid);
				$order['member3'] = $this->getParentid($order['member2']);
			} else {
				/* 关闭分销内购 */
				$order['member1'] = $this->getParentid($uid);
				$order['member2'] = $this->getParentid($order['member1']);
				$order['member3'] = $this->getParentid($order['member2']);
			}

			$lessoncom = unserialize($lesson['commission']); /* 本课程佣金比例 */
			$settingcom = unserialize($comsetting['commission']); /* 全局佣金比例 */
			if ($order['member1'] > 0) {
				$order['commission1'] = $this->getAgentCommission1($lessoncom['commission1'], $settingcom['commission1'], $price, $order['member1']);
			}
			
			if ($order['member2'] > 0 && in_array($comsetting['level'], array('2', '3'))) {
				$order['commission2'] = $this->getAgentCommission2($lessoncom['commission2'], $settingcom['commission2'], $price, $order['member2']);
			}
			
			if ($order['member3'] > 0 && $comsetting['level'] == 3) {
				$order['commission3'] = $this->getAgentCommission3($lessoncom['commission3'], $settingcom['commission3'], $price, $order['member3']);
			}
		}

		if(pdo_insert($this->table_order, $order)){
			$orderid = pdo_insertid();
			/* 增加课程购买人数 */
			$this->updateLessonNumber($order, $setting);
			
			/* 统计数据表 */
			$this->staticAmount($uniacid, 2, $order['price']);
			
			/* 判断分销员状态变化 */
			$this->checkAgentStatus($member, $setting);

			/* 一级佣金 */
	        if ($order['member1'] > 0 && $order['commission1'] > 0) {
	        	$this->sendCommissionToUser($uniacid, $order['member1'], $member['nickname'], 2, $setting, $order, $order['commission1'], 1);
	        }
			
			/* 二级佣金 */
	        if ($order['member2'] > 0 && $order['commission2'] > 0) {
	            $this->sendCommissionToUser($uniacid, $order['member2'], $member['nickname'], 2, $setting, $order, $order['commission2'], 2);
	        }
			
			/* 三级佣金 */
	        if ($order['member3'] > 0 && $order['commission3'] > 0) {
	            $this->sendCommissionToUser($uniacid, $order['member3'], $member['nickname'], 2, $setting, $order, $order['commission3'], 3);
	        }

			/* 讲师分成 */
			if ($price > 0 && $order['teacher_income'] > 0 && $income_switch==1) {
				$this->sendCommissionToTeacher($uniacid, $order, $setting);
			}
			
			/* 购买成功模版消息通知用户 */
			$this->sendMessageToUser($uniacid, $setting, $order, 2, $validity="");

			/* 赠送积分操作 */
			if ($order['integral'] > 0) {
				$this->handleUserIntegral($order['ordersn'], $order['uid'], $order['integral']);
			}

			message("创建课程订单成功", $this->createWebUrl('order'), "success");
		}
	}

}elseif($op=='couponCode'){
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$time = time();

	$condition = " uniacid = :uniacid ";
	$params['uniacid'] = $uniacid;
	if (!empty($_GPC['ordersn'])) {
		$condition .= " AND ordersn LIKE :ordersn ";
		$params[':ordersn'] = "%{$_GPC[ordersn]}%";
	}
	if (!empty($_GPC['keyword'])) {
		$condition .= " AND (password LIKE :pwd OR amount=:keyword OR card_id=:keyword OR password=:keyword) ";
		$params[':pwd'] = "{$_GPC['keyword']}%";
		$params[':keyword'] = trim($_GPC['keyword']);
	}
	if ($_GPC['is_use'] != '') {
		if($_GPC['is_use']==0){
			$condition .= " AND is_use = :is_use AND validity > :validity ";
			$params[':is_use'] = 0;
			$params[':validity'] = $time;
		}elseif($_GPC['is_use']==1){
			$condition .= " AND is_use = :is_use ";
			$params[':is_use'] = $_GPC['is_use'];
		}elseif($_GPC['is_use']==-1){
			$condition .= " AND is_use = :is_use AND validity < :validity ";
			$params[':is_use'] = 0;
			$params[':validity'] = $time;
		}
	}
	if (!empty($_GPC['time']['start'])) {
		$starttime = strtotime($_GPC['time']['start']);
		$endtime = strtotime($_GPC['time']['end']);
		$endtime = !empty($endtime) ? $endtime + 86399 : 0;
		if (!empty($starttime)) {
			$condition .= " AND use_time >= :starttime ";
			$params[':starttime'] = $starttime;
		}
		if (!empty($endtime)) {
			$condition .= " AND use_time < :endtime ";
			$params[':endtime'] = $endtime;
		}
	}

	$list = pdo_fetchall("SELECT * FROM " .tablename($this->table_coupon). " WHERE {$condition} ORDER BY card_id DESC LIMIT " .($pindex - 1) * $psize. ',' . $psize, $params);
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' .tablename($this->table_coupon). " WHERE {$condition}", $params);
	$pager = pagination($total, $pindex, $psize);

	if($_GPC['export']==1){
		$outputlist = pdo_fetchall("SELECT * FROM " .tablename($this->table_coupon). " WHERE {$condition} ORDER BY card_id DESC", $params);

		foreach ($outputlist as $key => $value) {
			$arr[$key]['card_id']		= $value['card_id'];
			$arr[$key]['password']	= $value['password'];
			$arr[$key]['amount']		= $value['amount'];
			$arr[$key]['conditions']	= "订单满".$value['conditions']."元可用";
			$arr[$key]['validity']	= date('Y-m-d H:i:s',$value['validity']);
			if($value['is_use']==1){
				$status = "已使用";
			}elseif($value['is_use']==0 && $value['validity']>time()){
				$status = "未使用";
			}elseif($value['is_use']==0 && $value['validity']<time()){
				$status = "已过期";
			}
			$arr[$key]['is_use']	  = $status;
			$arr[$key]['nickname']    = $value['nickname'];
			$arr[$key]['ordersn']     = $value['ordersn'];
			$arr[$key]['use_time']    = $value['use_time']?date('Y-m-d H:i:s', $value['use_time']):'';
			$arr[$key]['addtime']     = date('Y-m-d H:i:s', $value['addtime']);
		}

		$title = array('编号', '密钥', '面值', '使用条件', '有效期', '状态', '使用者', '订单号', '使用时间', '添加时间');
		$phpexcel = new FyLessonv2PHPExcel();
		$phpexcel->exportTable($title, $arr, '课程优惠码');
	}

}elseif($op=='addCouponCode'){
	if(checksubmit()){
		$prefix = trim($_GPC['prefix']);
		$number = intval($_GPC['number']);
		$amount = intval($_GPC['amount']);
		$conditions = floatval($_GPC['conditions']);
		$validity = strtotime($_GPC['validity']);

		if(strlen($prefix) != 2){
			message("请输入优惠码的两位前缀", "", "error");
		}
		if($number < 1){
			message("请输入正确的优惠码数量", "", "error");
		}
		if($number > 500){
			message("单次生成优惠码不要超过500张", "", "error");
		}
		if($amount < 1){
			message("请输入正确的优惠码面值", "", "error");
		}
		if($validity < time()){
			message("有效期必须大于当前时间", "", "error");
		}

		set_time_limit(120);
		ob_end_clean();
		ob_implicit_flush(true);
		str_pad(" ", 256);

		$total = 0;
		for($i=1;$i<=$number;$i++){
			$seek=mt_rand(0,9999).mt_rand(0,9999).mt_rand(0,9999).mt_rand(0,9999);
			$start=mt_rand(0,16);
			$str=strtoupper(substr(md5($seek),$start,16));
			$str=str_replace("O",chr(mt_rand(65,78)),$str);
			$str=str_replace("0",chr(mt_rand(65,78)),$str);

			$couponData = array(
				'uniacid'	=> $uniacid,
				'password'	=> $prefix.$str,
				'amount'	=> $amount,
				'validity'	=> $validity,
				'conditions'=> $conditions,
				'addtime'   => time()
			);
			if(pdo_insert($this->table_coupon, $couponData)){
				$total++;
			}
		}

		if($total){
			$this->addSysLog($_W['uid'], $_W['username'], 1, "课程订单->课程优惠码", "成功生成{$total}个面值为{$amount}元的优惠码");
		}
		message("成功生成{$total}张优惠码", $this->createWebUrl('order', array('op'=>'couponCode')), "success");
	}

}elseif($op=='delCoupon'){
	$card_id = $_GPC['id'];
	$card = pdo_fetch("SELECT password FROM " .tablename($this->table_coupon). " WHERE uniacid=:uniacid AND card_id=:card_id LIMIT 1", array(':uniacid'=>$uniacid, ':card_id'=>$card_id));
	if(empty($card)){
		message("该课程优惠码不存在或已被删除", "", "error");
	}
	$res = pdo_delete($this->table_coupon, array('uniacid'=>$uniacid,'card_id' => $card_id));
	if($res){
		$this->addSysLog($_W['uid'], $_W['username'], 2, "课程优惠码", "删除密钥为:{$card['password']}的课程优惠码");
	}

	echo "<script>alert('删除成功！');location.href='".$this->createWebUrl('order', array('op' => 'couponCode', 'page' => $_GPC['page']))."';</script>";

}elseif($op=='delAllCoupon'){
	$ids = $_GPC['ids'];
	if(!empty($ids) && is_array($ids)){
		$total = 0;
		$coupon = "";
		foreach($ids as $id){
			$coupon .= $this->getCouponPwd($id).",";
			if(pdo_delete($this->table_coupon, array('uniacid'=>$uniacid,'card_id' => $id))){
				$total++;
			}
		}

		$coupon = trim($coupon, ",");
		$this->addSysLog($_W['uid'], $_W['username'], 2, "课程优惠码", "批量删除{$total}个课程优惠码,[{$coupon}]");
		message("批量删除成功", $this->createWebUrl('order', array('op'=>'couponCode')), "success");
	}else{
		message("未选中任何课程优惠码", "", "error");
	}
}

if(!in_array($op , array('getLesson','getMember'))){
	include $this->template('web/order');
}


?>