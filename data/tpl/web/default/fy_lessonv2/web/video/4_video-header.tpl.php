<?php defined('IN_IA') or exit('Access Denied');?><ul class="nav nav-tabs">
	<li <?php  if($_GPC['do']=='video' && in_array($op, array('display','upqiniu','qiniuPreview'))) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('video');?>">七牛云对象存储</a></li>
	<li <?php  if($_GPC['do']=='video' && in_array($op, array('qcloud','upqcloud','qcloudPreview'))) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('video', array('op'=>'qcloud'));?>">腾讯云对象存储</a></li>
	<li <?php  if($_GPC['do']=='aliyunvod') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('aliyunvod');?>">阿里云点播</a></li>
	<li <?php  if($_GPC['do']=='qcloudvod') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('qcloudvod');?>">腾讯云点播</a></li>
</ul>