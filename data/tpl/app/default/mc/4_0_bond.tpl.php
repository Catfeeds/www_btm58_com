<?php defined('IN_IA') or exit('Access Denied');?><?php  define('MUI', true);?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php  if($do == 'mobile') { ?>
	<?php  if($op == 'index') { ?>
	<div class="mui-content mc-tel-bonded">
		<div class="mui-text-center">
			<img src="<?php  echo $_W['siteroot'];?>app/resource/images/bonded-tel.png">
			<div class="mui-b">你的手机号:<?php  echo $profile['mobile'];?></div>
		</div>
		<div class="mui-content-padded">
			<a class="mui-btn mui-btn-success mui-btn-block" href="<?php  echo url('mc/bond/mobile', array('op' => 'mobilechange'))?>">更换手机号</a>
		</div>
	</div>
	<?php  } ?>
	<?php  if($op == 'mobilechange') { ?>
	<div class="mui-content">
		<div class="sendcode">
			<div class="mui-content-padded mui-text-muted">请输入手机号,以收取验证码</div>
			<div class="mui-input-group mui-mt15">
				<div class="mui-input-row">
					<label class="mui-label-icon"><i class="fa fa-user"></i></label>
					<input name="mobile" type="text" class="js-mobile-val" value="" placeholder="手机号"/>
				</div>
			</div>
			<div class="mui-content-padded mui-text-center">
				<button class="mui-btn mui-btn-success mui-btn-block js-check-mobile" uniacid="<?php  echo $_W['uniacid'];?>">获取验证码</button>
			</div>
		</div>
		<div style="display:none;" class="codeverify">
			<div class="mui-content-padded mui-text-muted">您的手机号<span class="mui-text-success js-code"></span>会收到一条含有6位数字验证码的短息</div>
			<div class="mui-input-group mui-mt15">
				<div class="mui-input-row">
					<label class="mui-label-icon"><i class="fa fa-key"></i></label>
					<input name="code" type="text" placeholder="验证码"/>
				</div>
				<?php  if(!empty($reregister)) { ?>
				<div class="mui-input-row">
					<label class="mui-label-icon"><i class="fa fa-lock"></i></label>
					<input type="password" value="" name="password" placeholder="请输入您的新密码"/>
				</div>
				<div class="mui-input-row">
					<label class="mui-label-icon"><i class="fa fa-lock"></i></label>
					<input type="password" value="" name="repassword" placeholder="请再次输入您的新密码"/>
				</div>
				<?php  } ?>
			</div>
			<div class="mui-content-padded mui-text-center">
				<button class="mui-btn mui-btn-success mui-btn-block js-bond">确认</button>
				<div class="mui-mt15 mui-text-center">
					<span class="mui-text-muted js-timer">

					</span>
				</div>
			</div>
		</div>
		
	</div>
	<script>
		$(function(){
			$(document).on('input propertychange', '.js-mobile-val', function(){
				var mobile_value = $(this).val();
				if (mobile_value.length == '11') {
					$.post("<?php  echo url('auth/login/mobile_exist')?>", {'mobile' : mobile_value}, function(data) {
						data = $.parseJSON(data);
						if (data.message.errno == '1') {
							$('.js-check-mobile').removeClass('send-code');
							util.toast('手机号已被绑定', '', 'error');
							return;
						} else if (data.message.errno == '2'){
							$('.js-check-mobile').addClass('send-code');
						}
					});
				} else {
					$('.js-check-mobile').removeClass('send-code');
				}
			});
			$(document).on('click', '.send-code', function(){
				var username = $('input[name=mobile]').val();
				var oldmobile = '<?php  echo $_W['member']['mobile'];?>';
				if (username == oldmobile) {
					util.toast('无法更改为当前手机号', '', 'error');
					return;
				}
				$('.js-code').text(username);
				option = {
					'btnElement' : $('.send-code'),
					'showElement' : $('.js-timer'),
					'btnTips' : '<a class="send-code">重新获取验证码</a>',
					'successCallback' : function(ret, message){
						if (ret == '0') {
							util.toast(message);
							$('.sendcode').hide();
							$('.codeverify').show();
						} else {
							util.toast(message);
							$('.sendcode').show();
							$('.codeverify').hide();
							return;
						}
					}
				};
				util.sendCode(username, option);
			});
			$('.js-bond').click(function() {
				var code = $('.codeverify input[name="code"]').val();
				var mobile = $('.sendcode input[name="mobile"]').val();
				var password = $('.codeverify input[name="password"]').val();
				var repassword = $('.codeverify input[name="repassword"]').val();
				if (!code) {
					util.toast('请填写验证码', '', 'error');
					return;
				}
				if (!mobile) {
					util.toast('请填写手机号', '', 'error');
					return;
				}
				if(!/^1[3|4|5|7|8][0-9]{9}$/.test(mobile)) {
					util.toast('手机格式错误', '', 'error');
					return;
				}
				<?php  if(!empty($reregister)) { ?>
				if (!password || !repassword) {
					util.toast('请填写密码', '', 'error');
					return;
				}
				if (password !== repassword) {
					util.toast('密码不一致', '', 'error');
					return;
				}
				<?php  } ?>
				$.post('<?php  echo url("mc/bond/mobile")?>', {'code' : code, 'mobile' : mobile, 'password' : password, 'repassword' : repassword}, function(data) {
					data = $.parseJSON(data);
					if (data.message.errno == '-1') {
						util.toast(data.message.message, '', 'error');
					} else if (data.message.errno == '0') {
						util.toast(data.message.message, data.redirect, 'success');
					}
				})
			})
		});
	</script>
	<?php  } ?>
<?php  } ?>

<?php  if($do == 'settings') { ?>
<div class="mui-content">
	<ul class="mui-table-view mui-table-view-chevron">
		<?php  if(empty($profile_hide)) { ?>
		<li class="mui-table-view-cell">
			<a href="<?php  echo url('mc/profile') . 'wxref=mp.weixin.qq.com#wechat_redirect'?>" class="mui-navigate-right">个人资料</a>
		</li>
		<?php  } ?>
		<li class="mui-table-view-cell">
			<a href="<?php  echo url('mc/profile/address') . 'wxref=mp.weixin.qq.com#wechat_redirect'?>" class="mui-navigate-right">
				收货地址
				<span class="mui-pull-right">管理</span>
			</a>
		</li>
		<li class="mui-table-view-cell">
			<a href="<?php  echo url('mc/bond/pay_password') . 'wxref=mp.weixin.qq.com#wechat_redirect'?>" class="mui-navigate-right">
				支付密码
				<span class="mui-pull-right">设置</span>
			</a>
		</li>
	</ul>
	<ul class="mui-table-view mui-table-view-chevron">
		<?php  if(!empty($reregister)) { ?>
		<li class="mui-table-view-cell">
			<a href="<?php  echo url('mc/bond/binding_account', array('type' => '1'))?>" class="mui-navigate-right">
				完善账号
			</a>
		</li>
		<?php  } else { ?>
		<?php  if($item !== 'mobile') { ?>
		<li class="mui-table-view-cell">
			<a href="<?php  echo url('mc/bond/email')?>" class="mui-navigate-right">
				邮箱
				<span class="mui-pull-right"><span class="mui-mr10"><?php  if(!empty($reregister)) { ?>请绑定邮箱<?php  } else { ?><?php  echo $_W['member']['email'];?><?php  } ?></span></span>
			</a>
		</li>
		<?php  } ?>
		<?php  if($item !== 'email') { ?>
		<li class="mui-table-view-cell">
			<?php  if(empty($_W['member']['mobile'])) { ?>
			<a href="<?php  echo url('mc/bond/mobile', array('op' =>'mobilechange'))?>" class="mui-navigate-right">
			<?php  } else { ?>
			<a href="<?php  echo url('mc/bond/mobile', array('op' =>'index'))?>" class="mui-navigate-right">
			<?php  } ?>
				手机号
				<span class="mui-pull-right"><span class="mui-mr10"><?php  if(empty($_W['member']['mobile'])) { ?>请绑定手机号<?php  } else { ?><?php  echo $_W['member']['mobile'];?><?php  } ?></span></span>
			</a>
		</li>
		<?php  } ?>
		<?php  if(empty($reregister) && $ltype != 'code') { ?>
		<li class="mui-table-view-cell">
			<a href="<?php  echo url('mc/bond/password') . 'wxref=mp.weixin.qq.com#wechat_redirect'?>" class="mui-navigate-right">密码设置</a>
		</li>
		<?php  } ?>
		<?php  } ?>
	</ul>
	<ul class="mui-table-view mui-table-view-chevron">
		<?php  if(!empty($_W['setting']['copyright']['companyprofile'])) { ?>
		<li class="mui-table-view-cell">
			<a href="<?php  echo url('mc/bond/aboutus') . 'wxref=mp.weixin.qq.com#wechat_redirect'?>" class="mui-navigate-right">关于我们</a>
		</li>
		<?php  } ?>
		<li class="mui-table-view-cell">
			<a href="tel:<?php  echo $ucpage['params'][0]['params']['contact'];?>" class="mui-navigate-right" id='contact-us'>联系我们</a>
		</li>
	</ul>
	<div class="mui-content-padded">
		<a href="<?php  echo url('mc/home/login_out');?>" class="mui-btn mui-btn-block mui-btn-outlined mui-btn-danger">退出登录</a>
	</div>
</div>
<?php  } ?>
<?php  if($do == 'aboutus') { ?>
<div class="mui-content mc-about-us">
	<div class="mui-text-center logo"><img src="<?php  if(tomedia('headimg_'.$_W['acid'].'.jpg')) { ?><?php  echo tomedia('headimg_'.$_W['acid'].'.jpg');?><?php  } else { ?>resource/images/MicroEngine.ico<?php  } ?>" class="mui-img-circle"/></div>
	<div class="mui-content-padded desc">
		<?php  echo $_W['setting']['copyright']['companyprofile'];?>
	</div>
</div>
<?php  } ?>
<?php  if($do == 'email') { ?>
<form class="tab-content clearfix js-ajax-form <?php  if($_W['container'] !== 'wechat') { ?>profile-form<?php  } ?>" action="<?php  echo url('mc/bond/email');?>" method="post" enctype="multipart/form-data">
	<div class="mui-content mc-email">
		<div class="mui-control-content mui-active" id="email">
			<div class="tips mui-content-padded">
				您可以使用邮箱作为登录账号，绑定成功后暂不支持修改。
			</div>
			<div class="mui-input-group mui-mt15">
				<?php  if(empty($reregister) && !empty($_W['member']['email'])) { ?>
					<div class="mui-input-row">
						<label>邮箱</label>
						<input type="text" name="email" value="<?php  echo $_W['member']['email'];?>" disabled />
					</div>
				<?php  } else { ?>
				<div class="mui-input-row">
					<label>邮箱</label>
					<input type="text" name="email" value="" placeholder="请输入邮箱" />
				</div>
				<?php  } ?>
			</div>
		</div>
		<div class="mui-content-padded">
			<?php  if(!empty($reregister) || (empty($reregister) && empty($_W['member']['email']))) { ?>
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
			<input type="hidden" id="type" name="type" value="<?php  echo $type;?>" />
			<button class="mui-btn mui-btn-success mui-btn-block" type="submit" name="submit" value="立刻绑定">立即绑定</button>
			<?php  } else { ?>
			<a class="mui-btn mui-btn-block" href="javascript:history.back(-1);">返回</a>
			<?php  } ?>
		</div>
	</div>
</form>
<?php  } ?>
<?php  if($do == 'password') { ?>
<form class="tab-content clearfix js-ajax-form <?php  if($_W['container'] !== 'wechat') { ?>profile-form<?php  } ?>" action="<?php  echo url('mc/bond/password');?>" method="post" enctype="multipart/form-data">
	<div class="mui-content">
		<div class="mui-content-padded mui-text-muted">请设置密码</div>
		<div class="mui-input-group mui-mt15">
			<?php  if(empty($reregister) && !empty($profile['password'])) { ?>
			<div class="mui-input-row">
				<label>旧密码</label>
				<input type="password" value="" name="oldpassword" placeholder="请输入您的旧密码"/>
			</div>
			<?php  } ?>
			<div class="mui-input-row">
				<label>新密码</label>
				<input type="password" value="" name="password" placeholder="请输入您的新密码"/>
			</div>
			<div class="mui-input-row">
				<label class="mui-ellipsis">重复新密码</label>
				<input type="password" value="" name="repassword" placeholder="请再次输入您的新密码"/>
			</div>
		</div>
		<div class="mui-content-padded">
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
			<button class="mui-btn mui-btn-success mui-btn-block" type="submit" name="submit" value="确定">确定</button>
		</div>
	</div>
</form>
<?php  } ?>
<?php  if($do == 'credits') { ?>
	<?php  if($_GPC['type'] == 'record') { ?>
	<header class="mui-bar mui-bar-nav">
		<?php  if($_W['container'] !== 'wechat') { ?>
		<div class="mui-row fixed-bar">
			<div class="mui-col-xs-4">
				<button class="mui-btn mui-btn-link mui-btn-nav mui-pull-left mui-action-back">
					<span class="mui-icon mui-icon-left-nav"></span>
					返回
				</button>
			</div>
			<div class="mui-col-xs-4 mui-text-center"><?php  if(!empty($title)) { ?><?php  echo $title;?><?php  } else if(!empty($_W['page']['title'])) { ?><?php  echo $_W['page']['title'];?><?php  } ?></div>
			<div class="mui-col-xs-4 mui-text-right">
				<a href="#consume-date">
					<span><?php  if($_GPC['period'] <= 0) { ?><?php  echo date('Y.m', strtotime($_GPC['period'] . 'month'))?><?php  } else { ?>查看全部<?php  } ?></span>
					<span class="fa fa-angle-down mui-text-muted"></span>
				</a>
			</div>
		</div>
		<?php  } else { ?>
		<div class="mui-row fixed-bar">
			<div class="mui-col-xs-6"></div>
			<div class="mui-col-xs-6 mui-text-right">
				<a href="#consume-date">
					<span><?php  if($_GPC['period'] <= 0) { ?><?php  echo date('Y.m', strtotime($_GPC['period'] . 'month'))?><?php  } else { ?>查看全部<?php  } ?></span>
					<span class="fa fa-angle-down mui-text-muted"></span>
				</a>
			</div>
		</div>
		<?php  } ?>
	</header>
	<div id="consume-date" class="mui-popover mui-popover-top">
		<ul class="mui-table-view">
			<li class="mui-table-view-cell">
				<a href="<?php  echo url('mc/bond/credits', array('credittype' => $_GPC['credittype'], 'type' => 'record', 'period' => '1'))?>">查看全部</a>
			</li>
			<li class="mui-table-view-cell">
				<a href="<?php  echo url('mc/bond/credits', array('credittype' => $_GPC['credittype'], 'type' => 'record', 'period' => '0'))?>"><?php  echo date('Y.m', strtotime('today'))?></a>
			</li>
			<li class="mui-table-view-cell">
				<a href="<?php  echo url('mc/bond/credits', array('credittype' => $_GPC['credittype'], 'type' => 'record', 'period' => '-1'))?>"><?php  echo date('Y.m', strtotime('-1month'))?></a>
			</li>
			<li class="mui-table-view-cell">
				<a href="<?php  echo url('mc/bond/credits', array('credittype' => $_GPC['credittype'], 'type' => 'record', 'period' => '-2'))?>"><?php  echo date('Y.m', strtotime('-2month'))?></a>
			</li>
		</ul>
	</div>
	<div class="mui-content">
		<div class="mui-table mui-table-inline mui-pa10">
			<div class="mui-table-cell">
				<div class="mui-text-muted ">充值</div><?php  echo $income;?><?php  if($_GPC['credittype'] == 'credit2') { ?>元<?php  } else { ?>积分<?php  } ?>
			</div>
			<div class="mui-table-cell">
				<div class="mui-text-muted">消费</div><?php  echo $pay;?><?php  if($_GPC['credittype'] == 'credit2') { ?>元<?php  } else { ?>积分<?php  } ?>
			</div>
		</div>
		<div class="credits-display">
		<ul class="mui-table-view mui-credits">
			<?php  if(is_array($data)) { foreach($data as $row) { ?>
			<li class="mui-table-view-cell">
				<a href="<?php  echo murl('mc/bond/credits', array('type' => recorddetail, 'id' => $row['id'], 'credittype' => $_GPC['credittype']), true)?>">
					<div class="mui-row">
						<div class="mui-col-xs-6 mui-ellipsis-2">
							<?php  echo $row['remark'];?>
						</div>
						<div class="mui-col-xs-6 mui-text-right">
							<span class="mui-big <?php  if($_GPC['credittype'] == 'credit2') { ?>mui-rmb<?php  } ?>" style="color:<?php  echo $row['color']?>">
								<span class="money" style="color:<?php  echo $row['color']?>"><?php  echo $row['num'];?></span>
							</span>
							<span class="mui-block mui-text-muted mui-small"><?php  echo $row['createtime'];?></span>
						</div>
					</div>
				</a>
			</li>
			<?php  } } ?>
		</ul>
		</div>
	</div>
	<?php  } ?>
	<?php  if($_GPC['type'] == 'recorddetail') { ?>
	<div class="mui-bg-white mc-record-detail">
		<div class="mui-bb1 mui-row sum">
			<div class="mui-col-xs-6 mui-text-muted">
				付款金额
			</div>
			<div class="mui-col-xs-6 mui-text-right">
				<span class="mui-big <?php  if($_GPC['credittype'] == 'credit2') { ?>mui-rmb<?php  } ?>" style="color:<?php  echo $row['color']?>">
					<span class="money" style="color:<?php  echo $row['color']?>"><?php  echo $data['num'];?></span>
				</span>
			</div>
		</div>
		<div class="detail-info">
			<div class="mui-row">
				<div class="mui-col-xs-6 mui-text-muted">
					操作人
				</div>
				<div class="mui-col-xs-6 mui-text-right mui-ellipsis">
					<?php  if($data['username']) { ?><?php  echo $data['username'];?><?php  } else { ?>本人<?php  } ?>
				</div>
			</div>
			<div class="mui-row">
				<div class="mui-col-xs-6 mui-text-muted">
					数量
				</div>
				<div class="mui-col-xs-6 mui-text-right mui-ellipsis">
					<?php  echo $data['num'];?>
				</div>
			</div>
			<div class="mui-row">
				<div class="mui-col-xs-6 mui-text-muted">
					当前状态
				</div>
				<div class="mui-col-xs-6 mui-text-right mui-ellipsis">
					交易成功
				</div>
			</div>
			<div class="mui-row">
				<div class="mui-col-xs-6 mui-text-muted">
					时间
				</div>
				<div class="mui-col-xs-6 mui-text-right mui-ellipsis">
					<?php  echo date('Y-m-d H:i:s', $data['createtime'])?>
				</div>
			</div>
			<div class="mui-row">
				<div class="mui-col-xs-6 mui-text-muted">
					备注
				</div>
				<div class="mui-col-xs-6 mui-text-right">
					<?php  echo $data['remark'];?>
				</div>
			</div>
		</div>
	</div>
	<?php  } ?>
<script>
require(['mui.pullrefresh'], function(mui) {
	mui.init();
	mui.ready(function() {
		var page = 2;
		var pagetotal = <?php  echo $pagenums;?> + 1;
		if (page < pagetotal) {
			//循环初始化所有下拉刷新，上拉加载。
			mui.each(document.querySelectorAll('.credits-display'), function(index, pullRefreshEl) {
				mui(pullRefreshEl).pullToRefresh({
					up: {
						callback: function() {
							var self = this;
							setTimeout(function() {
								$('.mui-pull-bottom-tips').hide();
								var ul = self.element.querySelector('.mui-credits');
								ul.appendChild(createFragment(ul, index, 5));
								if (pagetotal <= page) {
									$('.mui-pull-bottom-tips').hide();
									self.endPullUpToRefresh(true);
								} else {
									self.endPullUpToRefresh(false);
								}
							}, 1000);
						}
					}
				});
			});

			var createFragment = function(ul, index, count, reverse) {
				var length = ul.querySelectorAll('li').length;
				var fragment = document.createDocumentFragment();
				var li;
				var url = "<?php  echo url('mc/bond/credits', array('credittype' => $_GPC['credittype'], 'type' => $_GPC['type'], 'period' => $_GPC['period']), true)?>";
				mui.post(url, {'page' : page}, function(data){
					data = $.parseJSON(data);
					if (data.state == 'error') {
						return false;
					}
					for (var i in data) {
						var href = "<?php  echo url('mc/bond/credits', array('type' => 'recorddetail', 'credittype' => $_GPC['credittype']),true)?>";
						li = document.createElement('li');
						li.className = 'mui-table-view-cell';
						li.innerHTML = '<a href="' + href + '&id=' + data[i].id + '" ><div class="mui-row"><div class="mui-col-xs-6 mui-ellipsis-2">' + data[i].remark + '</div><div class="mui-col-xs-6 mui-text-right"><span class="mui-big mui-rmb" style="color:' + data[i].color + '"><span class="money" style="color:' + data[i].color + '">' + data[i].num + '</span></span><span class="mui-block mui-text-muted mui-small">' + data[i].createtime + '</span></div></div></a>';
						ul.appendChild(li, ul.firstChild);
					}
					$('.mui-pull-bottom-tips').show();
				});
				page++;
				return fragment;
			};
		}
	});
});
</script>
<?php  } ?>

<?php  if($do == 'binding_account') { ?>
<form class="tab-content clearfix js-ajax-form <?php  if($_W['container'] !== 'wechat') { ?>profile-form<?php  } ?>" action="<?php  echo url('mc/bond/binding_account');?>" method="post" enctype="multipart/form-data">
	<div class="mui-content mc-email">
		<div class="mui-control-content mui-active" id="email">
			<div class="tips mui-content-padded">
				您可以使用邮箱或手机作为登录账号。
			
			</div>
			<div class="mui-input-group mui-mt15">
				<?php  if($type == '1') { ?>
				<div class="mui-input-row">
					<label>账号</label>
					<input type="text" name="username" value="" placeholder="请输入<?php  if($item == 'email') { ?>邮箱<?php  } else if($item == 'mobile') { ?>手机号<?php  } else { ?>邮箱或手机号<?php  } ?>" />
				</div>
				<?php  } else { ?>
				<div class="mui-input-row">
					<label>已有邮箱</label>
					<input type="text" name="username" value="" placeholder="请输入已有邮箱" />
				</div>
				<?php  } ?>
				<div class="mui-input-row">
					<label>登录密码</label>
					<input type="password" name="password" value="" placeholder="请输入<?php  if($type != '1') { ?>已有邮箱<?php  } ?>登录密码"/>
				</div>
			</div>
		</div>
		<div class="mui-content-padded">
			<div class="mui-text-center mui-mt15 mui-mb10">
			<?php  if(!empty($reregister) || (empty($reregister) && empty($_W['member']['email']))) { ?>
				<?php  if($_GPC['type'] == 1) { ?>
				<a href="<?php  echo url('mc/bond/binding_account', array('type' => '0'))?>">绑定已有账号</a>
				<?php  } else { ?>
				<a href="<?php  echo url('mc/bond/binding_account', array('type' => '1'))?>">绑定新账号</a>
				<?php  } ?>
			<?php  } ?>
			</div>
			<?php  if(!empty($reregister) || (empty($reregister) && empty($_W['member']['email']))) { ?>
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
			<input type="hidden" id="type" name="type" value="<?php  echo $type;?>" />
			<button class="mui-btn mui-btn-success mui-btn-block" type="submit" name="submit" value="立刻绑定">立即绑定</button>
			<?php  } else { ?>
			<a class="mui-btn mui-btn-block" href="javascript:history.back(-1);">返回</a>
			<?php  } ?>
		</div>
	</div>
</form>
<?php  } ?>
<?php  if($do == 'pay_password') { ?>
<form class="tab-content clearfix js-ajax-form <?php  if($_W['container'] !== 'wechat') { ?>profile-form<?php  } ?>" action="<?php  echo url('mc/bond/pay_password');?>" method="post" enctype="multipart/form-data">
	<div class="mui-content mc-email">
		<div class="mui-control-content mui-active">
			<div class="mui-input-group mui-mt15">
				<div class="mui-input-row">
					<label>开启支付密码</label>
					<input type="checkbox" name="pay_password_open" onclick="change()" <?php  if(!empty($pay_password)) { ?>checked<?php  } ?>/>
				</div>
			</div>
			<div class="mui-input-group mui-mt15" id="pay_password">
				<div class="mui-input-row">
					<label>支付密码</label>
					<input type="password" name="pay_password" value="" placeholder="请输入密码" />
				</div>
				<div class="mui-input-row">
					<label>确认支付密码</label>
					<input type="password" name="repeat_pay_password" value="" placeholder="请确认密码"/>
				</div>
			</div>
		</div>
		<div class="mui-content-padded">
			<button class="mui-btn mui-btn-block" type="submit">确定</button>
		</div>
	</div>
</form>
<script>
	$('#pay_password').hide();
	var change = function() {
		open = $('[name="pay_password_open"]').prop('checked');
		if (open === true) {
			$('#pay_password').show();
		} else {
			$('#pay_password').hide();
		}
	};
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>