<?php defined('IN_IA') or exit('Access Denied');?><ul class="nav nav-tabs">
    <li <?php  if($op=='display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('lesson');?>">课程列表</a></li>
	<li <?php  if($op=='postlesson') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('lesson', array('op'=>'postlesson','id'=>$_GPC['pid']));?>"><?php  if($_GPC['id']>0) { ?>编辑<?php  } else { ?>添加<?php  } ?>课程</a></li>
	<?php  if($op=='postsection') { ?>
	<li class="active"><a href="<?php  echo $this->createWebUrl('lesson', array('op'=>'postsection','pid'=>$_GPC['pid'],'id'=>$_GPC['id']));?>"><?php  if($_GPC['id']>0) { ?>编辑<?php  } else { ?>添加<?php  } ?>章节</a></li>
	<?php  } ?>
	<?php  if($op=='viewsection') { ?>
	<li class="active"><a href="<?php  echo $this->createWebUrl('lesson', array('op'=>'viewsection', 'pid'=>$_GPC['pid']));?>">章节列表</a></li>
	<?php  } ?>
	<?php  if($op=='previewVideo') { ?>
	<li class="active"><a href="javascript:;">预览章节视频</a></li>
	<?php  } ?>
	<li <?php  if($op=='record') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('lesson', array('op'=>'record'));?>">学习记录</a></li>
	<li <?php  if($op=='inform') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('lesson', array('op'=>'inform'));?>">开课提醒</a></li>
	<li <?php  if($op=='updomain') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('lesson', array('op'=>'updomain'));?>">更新音视频域名</a></li>
</ul>