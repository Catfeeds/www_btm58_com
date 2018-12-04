<?php defined('IN_IA') or exit('Access Denied');?><!-- 
 * 讲师列表
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
-->
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_headerv2', TEMPLATE_INCLUDEPATH)) : (include template('_headerv2', TEMPLATE_INCLUDEPATH));?>
<link href="<?php echo MODULE_URL;?>template/mobile/style/cssv2/search.css?v=<?php  echo $versions;?>" rel="stylesheet" />
<style>
a.package{padding: 15px 0 15px 90px;}
a.package .package__cover-wrap{width: 80px;}
a.package .package__cover-wrap .package__cover{background-size: 80px 80px;}
a.package .package__content .package__info{height: 80px;overflow: hidden;}
a.package .package__cover-wrap .package__cover .package__cover-tips{text-align:center;background-color: rgba(0, 0, 0, .5);}
</style>
<!-- 顶部搜索框 -->
<header class="m-header z-img-ready border-top">
	<div class="header_search">
		<div class="u-search">
			<i class="fa fa-search"></i>
			<input type="text" id="searchInput" class="search_input z-abled" value="<?php  echo $_GPC['keyword'];?>" autocorrect="off" placeholder="搜索老师名称">
		</div>
	</div>
</header>
<!-- /顶部搜索框 -->

<!-- 讲师列表 -->
<div class="section">
	<div style="margin:54px auto 10px;">
		<?php  if(!empty($keyword)) { ?>
		<div class="search-result-toast">
			共找到<span class="search-result-word"><?php  echo $total;?></span>位讲师
		</div>
		<?php  } ?>
		<ul id="js-course-list" class="course-list list-view" style="min-height:1px;">
		</ul>
		<div id="loading_div" class="loading_div">
			<a href="javascript:void(0);" id="btn_Page"><i class="fa fa-arrow-circle-down"></i> 加载更多</a>
		</div>
	</div>
	<footer>
	    <a href="<?php  echo $this->createMobileUrl('index', array('t'=>1));?>"><?php  echo $setting['copyright'];?></a>
	</footer>
</div>
<!-- /讲师列表 -->

<div class="apply-btn <?php  if($setting['teacher_income']==0) { ?>hide<?php  } ?>">
	<a href="<?php  echo $this->createMobileUrl('applyteacher');?>">申请入驻</a>
</div>

<!-- 遮罩层 -->
<div id="sort_background" class="dropdown__background"></div>
<!-- /遮罩层 -->
<div id="loading" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:999999999;"><div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>

<script type="text/javascript">
var search = function() {
    var keywords = $.trim($("#searchInput").val());
    if (keywords == '') {
        searchUrl = '<?php  echo $this->createMobileUrl("teacherlist");?>';
    } else {
        searchUrl = '<?php  echo $this->createMobileUrl("teacherlist");?>&keyword=' + encodeURIComponent(keywords);
    }
    document.location.href = searchUrl;
    return false;
};
$("#searchInput").keydown(function(event) {
	if (event.keyCode == 13) {
		search();
	}
});
$("#search_btn").on("click", function(){
	search();
});
</script>
<script type="text/javascript">
var ajaxUrl   = "<?php  echo $this->createMobileUrl('teacherlist', array('op'=>'ajaxgetteacherlist'));?>&keyword=" + $.trim($("#searchInput").val());
var attachUrl = "<?php  echo $_W['attachurl'];?>";
var teacherUrl = "<?php  echo $this->createMobileUrl('teacher');?>";
$(function () {
    var nowPage = 1; //设置当前页数，全局变量
    function getData(page) {  
        nowPage++; //页码自动增加，保证下次调用时为新的一页。  
        $.get(ajaxUrl, {page: page}, function (data) {  
            if (data.length > 0) {
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
        var mainDiv =$("#js-course-list");
        var chtml = '';  
        for (var j = 0; j < result.length; j++) {
            chtml += '<li class="lesson_list">';
            chtml += '	<a href="' + teacherUrl + '&teacherid=' + result[j].id + '" class="package">';
            chtml += '		<div class="package__cover-wrap">';
            chtml += '			<div class="package__cover" style="background-image: url(' + attachUrl + result[j].teacherphoto + ');">';
            chtml += '				<span class="package__cover-tips package__cover-tips--status">' + result[j].teacher + '</span>';
            chtml += '			</div>';
            chtml += '		</div>';
            chtml += '		<div class="package__content">';
            chtml += '			<div class="package__info">';
            chtml += '				<i class="u-price">讲师简介：</i>' + result[j].teacherdes;
            chtml += '			</div>';
            chtml += '		</div>';
            chtml += '	</a>';
            chtml += '</li>';
        }
		mainDiv.append(chtml);
		if(result.length==0){
			document.getElementById("loading_div").innerHTML='<div class="loading_bd">没有了，已经到底了</div>';
		}
    }  
  
    //==============核心代码=============  
    var winH = $(window).height(); //页面可视区域高度   
  
    var scrollHandler = function () {  
        var pageH = $(document.body).height();  
        var scrollT = $(window).scrollTop(); //滚动条top   
        var aa = (pageH - winH - scrollT) / winH;  
        if (aa < 0.02) { 
            if (nowPage % 1 === 0) {
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
    $("#btn_Page").click(function () {
    	loading.style.display = 'block';
        getData(nowPage);
        $(window).scroll(scrollHandler);
    });
  
});
</script>

<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footerv2', TEMPLATE_INCLUDEPATH)) : (include template('_footerv2', TEMPLATE_INCLUDEPATH));?>
