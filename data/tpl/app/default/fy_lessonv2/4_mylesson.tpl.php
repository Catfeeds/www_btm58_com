<?php defined('IN_IA') or exit('Access Denied');?><!-- 
 * 我的课程
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
-->
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_headerv2', TEMPLATE_INCLUDEPATH)) : (include template('_headerv2', TEMPLATE_INCLUDEPATH));?>
<style>
.weui_tab{overflow-y: scroll;}
.weui_tabbar{position: fixed;}
.submit_list{margin-top:15px;}
.submit_list .bookname{position: absolute; bottom: 17px; left: 0; width: 120px; height: 16px; font-size: 12px; color: #fff; background-color: rgba(0, 0, 0, .7); text-align: center;}
.submit_list .item h3{max-height: 23px;}
.fr{margin-top: -5px;}
.mylesson-btn {display: inline-block;width: 80px;height: 30px; border-radius: 5px;text-align: center;line-height: 32px;color: #fff;font-size: 14px;margin-left: 5px;}
.cancle-btn{background-color:#a0a0a0;}
.pay-btn{background-color:#f23030;}
.evaluate-btn{background-color:#326fde;}
.verify-btn{background-color:#1da167;}
</style>

<div class="header-2 cbox">
	<a href="javascript:history.go(-1);" class="ico go-back"></a>
	<div class="flex title" style="max-width:80%;"><?php  echo $title;?></div>
</div>

<!-- 顶部导航  -->
<ul class="tab_wrap">
	<li class="tab_item <?php  if($_GPC['status']=='') { ?>tab_item_on<?php  } ?>">
		<a href="<?php  echo $this->createMobileUrl('mylesson');?>&status=">全部课程</a>
	</li>
	<li class="tab_item <?php  if($_GPC['status']=='0') { ?>tab_item_on<?php  } ?>">
		<a href="<?php  echo $this->createMobileUrl('mylesson', array('status'=>'0'));?>">待付款</a>
	</li>
	<li class="tab_item <?php  if($_GPC['status']=='1' && $_GPC['is_verify']=='') { ?>tab_item_on<?php  } ?>">
		<a href="<?php  echo $this->createMobileUrl('mylesson', array('status'=>'1'));?>">已付款</a>
	</li>
	<li class="tab_item <?php  if($_GPC['status']=='1' && $_GPC['is_verify']=='0') { ?>tab_item_on<?php  } ?>">
		<a href="<?php  echo $this->createMobileUrl('mylesson', array('status'=>'1','is_verify'=>'0'));?>">未核销</a>
	</li>
</ul>
<!-- /顶部导航  -->

<!-- 订单列表  -->
<?php  if(!empty($mylessonlist)) { ?>
<div id="order-list">
</div>
<?php  } else { ?>
<div class="my_empty">
    <div class="empty_bd  my_course_empty">
        <h3>没有找到任何课程~</h3>
        <p><a href="<?php  echo $this->createMobileUrl('index', array('t'=>1));?>">到首页去看看</a></p>
    </div>
</div>
<?php  } ?>
<!-- 订单列表  -->
<div id="loading_div" class="loading_div">
	<a href="javascript:void(0);" id="btn_Page"><i class="fa fa-arrow-circle-down"></i> 加载更多</a>
</div>
<footer>
    <a href="<?php  echo $this->createMobileUrl('index', array('t'=>1));?>"><?php  echo $setting['copyright'];?></a>
</footer>

<div id="loading" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:999999999;"><div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>

<script type="text/javascript">
	function GetQueryString(name) {
		var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
		var r = window.location.search.substr(1).match(reg);
		if(r != null) return unescape(r[2]);
		return null;
	}

	var status = GetQueryString('status');
	var is_verify = GetQueryString('is_verify');
	var ajaxUrl = "<?php  echo $this->createMobileUrl('mylesson', array('op'=>'ajaxgetlist'));?>";
	var attachUrl = "<?php  echo $_W['attachurl'];?>";
	var payUrl = "<?php  echo $this->createMobileUrl('pay');?>";
	var cancleUrl = "<?php  echo $this->createMobileUrl('mylesson', array('op'=>'cancle'));?>";
	var eUrl = "<?php  echo $this->createMobileUrl('evaluate');?>";
	var orderUrl = "<?php  echo $this->createMobileUrl('orderdetail');?>";
	var loading = document.getElementById("loading");
	$(function() {
		var nowPage = 1; //设置当前页数，全局变量
		function getData(page) {
			nowPage++; //页码自动增加，保证下次调用时为新的一页。  
			$.get(ajaxUrl, {
				page: page,
				status: status,
				is_verify: is_verify,
			}, function(data) {
				if(data.length > 0) {
					loading.style.display = 'none';
					var jsonObj = JSON.parse(data);
					insertDiv(jsonObj);
				}
			});

		}
		//初始化加载第一页数据  
		getData(1);

		//生成数据html,append到div中  
		function insertDiv(result) {
			var mainDiv = $("#order-list");
			var chtml = '';
			for(var j = 0; j < result.length; j++) {
				chtml += '<div class="submit_list" onclick="goLesson('+ result[j].id +')">';
				chtml += '	<div class="item">';
				chtml += '		<img src="' + attachUrl+result[j].images + '" alt="' + result[j].bookname + '">';
				chtml += '		<span class="bookname">' + result[j].bookname + '</span>';
				chtml += '		<div class="info">';
				chtml += '			<p>订单编号：' + result[j].ordersn + '</p>';
				chtml += '			<p>订单状态：<i class="red-color">' + result[j].statusname + '</i></p>';
				chtml += '			<p>下单时间：' + result[j].addtime + '</p>';
				if(result[j].lesson_type==1){
					chtml += '			<p>已选：'+result[j].spec_name+'</p>';
					if(result[j].is_verify==1){
					chtml += '			<p>核销状态：<i class="red-color">已核销</i></p>';
					}else{
					chtml += '			<p>核销状态：<i class="green-color">未核销</i></p>';
					}
				}else{
					if(result[j].spec_day>0){
						chtml += '			<p>规格：'+result[j].spec_day+'天</p>';
					}else{
						chtml += '			<p>规格：长期有效</p>';
					}
					if(result[j].validity!=0 && result[j].status>0){
						chtml += '	<p>有效期：<i class="red-color">'+result[j].validity+'</i></p>';
					}
				}
				
				chtml += '		</div>';
				chtml += '	</div>';
				chtml += '	<div class="sum">';
				chtml += '		订单总额：<em>￥'+result[j].price+'</em>';
				chtml += '		<span class="fr">';
				if(result[j].status>0 && result[j].lesson_type==1 && result[j].is_verify==0){
					chtml += '		<a href="'+orderUrl+'&orderid='+result[j].id+'" class="mylesson-btn verify-btn">核销二维码</a>';
				}
				if(result[j].status=='0'){
					chtml += '		<a href="'+cancleUrl+'&orderid='+result[j].id+'" class="mylesson-btn cancle-btn" onclick="changeOrder()">取消订单</a>';
					chtml += '		<a href="'+payUrl+'&orderid='+result[j].id+'" class="mylesson-btn pay-btn" onclick="changeOrder()">立即支付</a>';
				}else if(result[j].status=='1'){
					chtml += '		<a href="'+eUrl+'&orderid='+result[j].id+'" class="mylesson-btn evaluate-btn">评价课程</a>';
				}else if(result[j].status=='-1'){
					chtml += '		<a href="'+cancleUrl+'&orderid='+result[j].id+'&is_delete=1" class="mylesson-btn cancle-btn" onclick="changeOrder()">删除订单</a>';
				}
				chtml += '		</span>';
				chtml += '	</div>';
				chtml += '</div>';

			}
			mainDiv.append(chtml);
			if(result.length == 0) {
				document.getElementById("loading_div").innerHTML='<div class="loading_bd">没有了，已经到底啦</div>';
			}
		}

		//==============核心代码=============  
		var winH = $(window).height(); //页面可视区域高度   

		var scrollHandler = function() {
			var pageH = $(document.body).height();
			var scrollT = $(window).scrollTop(); //滚动条top   
			var aa = (pageH - winH - scrollT) / winH;
			if(aa < 0.02) {
				if(nowPage % 1 === 0) {
					getData(nowPage);
					$(window).unbind('scroll');
					$("#btn_Page").show();
				} else {
					getData(nowPage);
					$("#btn_Page").hide();
				}
			}
		}
		//定义鼠标滚动事件
		$(window).scroll(scrollHandler);
		//继续加载按钮事件
		$("#btn_Page").click(function() {
			loading.style.display = 'block';
			getData(nowPage);
			$(window).scroll(scrollHandler);
		});
	});
	
	function goLesson(id){
		var url = orderUrl + "&orderid="+id;
		location.href = url;
	}
	
	function changeOrder(){
		document.getElementById("loading").style.display = 'block';
	}
	
</script>

<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footerv2', TEMPLATE_INCLUDEPATH)) : (include template('_footerv2', TEMPLATE_INCLUDEPATH));?>