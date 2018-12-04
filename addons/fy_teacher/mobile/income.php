<?php
/*
 * 课程收入
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: http://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
 */

$pindex = max(1, intval($_GPC['page']));
$psize = 10;

$config = $this->module['config'];

$teacher = pdo_fetch("SELECT uid FROM " .tablename($this->table_teacher). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid, ':id'=>$_SESSION[$uniacid."_teacher_id"]));
if($op=='display'){
	$linkNav = array(
		'0'	=> array(
			'title'	=> '课程收入',
			'link'	=> $this->createMobileUrl('income'),
		),
	);

	$condition = " a.uniacid=:uniacid AND a.uid=:uid ";
	$params[':uniacid'] = $uniacid;
	$params[':uid'] = $teacher['uid'];
	if($_GPC['keyword'] != ''){
		$condition .= " AND (a.bookname LIKE :keyword OR a.ordersn LIKE :keyword) ";
		$params[':keyword'] = "%".trim($_GPC['keyword'])."%";
	}
	if($_GPC['starttime'] != ''){
		$condition .= " AND a.addtime >= :starttime ";
		$params[':starttime'] = strtotime($_GPC['starttime']);
	}
	if($_GPC['endtime'] != ''){
		$condition .= " AND a.addtime <= :endtime ";
		$params[':endtime'] = strtotime($_GPC['endtime'])+86399;
	}

	$list = pdo_fetchall("SELECT a.*,b.id AS orderid FROM " .tablename($this->table_teacher_income). " a LEFT JOIN " .tablename($this->table_order). " b ON a.ordersn=b.ordersn WHERE {$condition} ORDER BY a.id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_teacher_income). " a LEFT JOIN " .tablename($this->table_order). " b ON a.ordersn=b.ordersn WHERE {$condition}", $params);
	$pager = $this->pagination($total, $pindex, $psize);

}elseif($op=='cash'){
	$linkNav = array(
		'0'	=> array(
			'title'	=> '课程收入',
			'link'	=> $this->createMobileUrl('income'),
		),
		'1'	=> array(
			'title'	=> '课程收入提现',
			'link'	=> $this->createMobileUrl('income', array('op'=>'cash')),
		),
	);

	$cashListUrl = $this->createMobileUrl('income', array('op'=>'cashList'));

	$setting = pdo_fetch("SELECT manageopenid FROM " . tablename($this->table_setting) . " WHERE uniacid =:uniacid LIMIT 1", array(':uniacid' => $uniacid));
	$comsetting = $this->getComsetting();
	/* 提现方式 */
	$setting_cashway = unserialize($comsetting['cash_way']);
	/* 微课堂会员信息 */
	$member = pdo_fetch("SELECT * FROM " .tablename($this->table_member). " WHERE uniacid=:uniacid AND uid=:uid", array(':uniacid'=>$uniacid, ':uid'=>$teacher['uid']));

	$lastcashlog = pdo_fetch("SELECT pay_account FROM " .tablename($this->table_cashlog). " WHERE uniacid=:uniacid AND uid=:uid AND cash_way=3 ORDER BY id DESC LIMIT 1", array(':uniacid'=>$uniacid, ':uid'=>$teacher['uid']));

	if(checksubmit('submit')){
		$cash_way = intval($_GPC['cash_way']); //1.提现到余额 2.提现到微信钱包 3.支付宝
		$cash_num = floatval($_GPC['cash_num']);
		$pay_account = trim($_GPC['pay_account']);

		if(empty($cash_way)){
			message("暂无有效提现方式，请联系管理员", "", "error");
		}
		if($cash_way==3 && empty($pay_account)){
			message("请输入提现帐号", "", "error");
		}
		if($cash_num > $member['nopay_lesson']){
			message("您的可提现佣金额度为{$member['nopay_lesson']}元", "", "error");
		}
		if($cash_num < $comsetting['cash_lower_teacher']){
			message("当前系统最低提现额度为{$comsetting['cash_lower_teacher']}元", "", "error");
		}

		/**
		 * 减少会员可提现佣金和增加会员已提现佣金
		 */
		$upmember = array(
			'nopay_lesson'	=> $member['nopay_lesson'] - $cash_num,
			'pay_lesson'	=> $member['pay_lesson'] + $cash_num,
		);
		$succ = pdo_update($this->table_member, $upmember, array('id'=>$member['id']));

		if($succ){
			$cashlog = array(
				'uniacid'	  => $uniacid,
				'cash_way'	  => $cash_way, //1.提现到余额  2.提现到微信钱包  3.提现到支付宝
				'pay_account' => $pay_account,
				'uid'		  => $member['uid'],
				'openid'	  => $member['openid'],
				'cash_num'    => $cash_num,
				'lesson_type' => 2, /* 提现类型 1.分销佣金提现 2.课程收入提现 */
				'addtime'	  => time(),
			);
			if($cash_way==1){
				$cashlog['cash_type'] = 2; //提现到余额默认为自动到账
			}elseif($cash_way==3){
				$cashlog['cash_type'] = 1; //提现到支付宝默认为管理员审核
			}else{
				$cashlog['cash_type'] = $comsetting['cash_type'];
			}

			if($cash_way==1){/*  1.提现到余额 */
				load()->model('mc');
				$result = mc_credit_update($member['uid'], 'credit2', $cash_num, array('1'=>'微课堂讲师收入提现'));

				if($result){
					$cashlog['status']       = 1;
					$cashlog['disposetime']  = time();
					$cashlog['remark']		 = "";

					pdo_insert($this->table_cashlog, $cashlog);
					message("提现成功，佣金已发放到您的账户余额！", refresh, "success");
				}

			}elseif($cash_way==2 || $cash_way==3){/*  2.提现到微信钱包 3.提现到支付宝 */
				if($cashlog['cash_type']==1){ /* 提现方式为管理员审核 */
					$cashlog['status'] = 0;
					pdo_insert($this->table_cashlog, $cashlog);

					/* 模版消息通知管理员 */
					$tplmessage = pdo_fetch("SELECT newcash FROM " .tablename($this->table_tplmessage). " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));
					$manage = explode(",", $setting['manageopenid']);
					foreach($manage as $manageopenid){
						$sendneworder = array(
							'touser'      => $manageopenid,
							'template_id' => $tplmessage['newcash'],
							'url'         => "",
							'topcolor'    => "#7B68EE",
							'data'        => array(
								'first'=> array(
									'value' => urlencode("亲，您收到一条新的用户提现申请"),
									'color' => "#428BCA",
								),
								'keyword1'  => array(
									'value' => $member['nickname'],
									'color' => "#428BCA",
								),
								'keyword2'  => array(
									'value' => date('Y-m-d H:i', time()),
									'color' => "#428BCA",
								),
								'keyword3'  => array(
									'value' => urlencode($cash_num."元"),
									'color' => "#428BCA",
								),
								'keyword4'  => array(
									'value' => urlencode("微信零钱钱包"),
									'color' => "#428BCA",
								),
								'remark'	=> array(
									'value' => urlencode("详情请登录网站后台查看！"),
									'color' => "#222222",
								),
							)
						);
						$this->send_template_message(urldecode(json_encode($sendneworder)),$_W['acid']);
					}
					message("提交申请成功，请等待管理员审核！", refresh, "success");
				}elseif($comsetting['cash_type']==2){ /* 提现方式为自动提现到微信零钱钱包 */
					$post = array('total_amount'=>$cash_num, 'desc'=>'讲师申请课程收入提现');
					$fans = array('openid'=>$member['openid'], 'nickname'=>$member['nickname']);
					$result = $this->companyPay($post,$fans);

					if($result['result_code']=='SUCCESS'){
						$cashlog['status']           = 1;
						$cashlog['disposetime']      = strtotime($result['payment_time']);
						$cashlog['partner_trade_no'] = $result['partner_trade_no'];
						$cashlog['payment_no']	     = $result['payment_no'];
						$cashlog['remark']			 = "";

						pdo_insert($this->table_cashlog, $cashlog);
						message("提现成功，提现金额已发放到您的微信钱包！", refresh, "success");

					}else{
						/*回滚操作*/
						$rollback = array(
							'nopay_lesson'	=> $member['nopay_lesson'],
							'pay_lesson'	=> $member['pay_lesson'],
						);
						pdo_update($this->table_member, $rollback, array('id'=>$member['id']));
						
						message("提现失败，请联系管理员，公众号平台返回信息：".$result['err_code_des'], refresh, "error");
					}
				}
			}
		}
		
	}

}elseif($op=='cashList'){
	$linkNav = array(
		'0'	=> array(
			'title'	=> '课程收入',
			'link'	=> $this->createMobileUrl('income'),
		),
		'1'	=> array(
			'title'	=> '课程收入提现',
			'link'	=> $this->createMobileUrl('income', array('op'=>'cash')),
		),
		'2'	=> array(
			'title'	=> '课程收入提现记录',
			'link'	=> $this->createMobileUrl('income', array('op'=>'cashList')),
		),
	);

	$condition = " uniacid=:uniacid AND uid=:uid AND lesson_type=2 ";
	$params[':uniacid'] = $uniacid;
	$params[':uid'] = $teacher['uid'];

	$list = pdo_fetchall("SELECT * FROM " .tablename($this->table_cashlog). " WHERE {$condition} ORDER BY id DESC LIMIT " . ($pindex-1) * $psize . ',' . $psize, $params);
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_cashlog). " WHERE {$condition}", $params);
	$pager = $this->pagination($total, $pindex, $psize);

}


include $this->template('income');