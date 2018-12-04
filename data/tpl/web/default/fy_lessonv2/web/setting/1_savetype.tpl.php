<?php defined('IN_IA') or exit('Access Denied');?><div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
		<div class="panel panel-default">
			<div class="panel-heading">存储方式</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">存储方式设置</label>
					<div class="col-sm-9">
						<?php  if(is_array($videoSaveList)) { foreach($videoSaveList as $key => $item) { ?>
						<label class="radio-inline">
                            <input type="radio" name="savetype" value="<?php  echo $key;?>" <?php  if($setting['savetype']==$key) { ?>checked<?php  } ?>><?php  echo $item;?>
                        </label>
						<?php  } } ?>
						<span class="help-block">支持多个存储方式同时使用，发布课程章节时可在章节里自主选择存储方式</span>
					</div>
				</div>
				<!-- 七牛云对象存储 Start -->
				<div class="qiniu-params-admin" <?php  if($setting['savetype']!=1) { ?>style="display:none;"<?php  } ?>>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">Bucket(存储桶名称)</label>
						<div class="col-sm-9">
							<input type="text" name="qiniu[bucket]" class="form-control" value="<?php  echo $qiniu['bucket'];?>" autocomplete="off">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">七牛ACCESS_KEY</label>
						<div class="col-sm-9">
							<input type="text" name="qiniu[access_key]" class="form-control" value="<?php  echo $qiniu['access_key'];?>" autocomplete="off">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">七牛SECRET_KEY</label>
						<div class="col-sm-9">
							<input type="text" name="qiniu[secret_key]" class="form-control" value="<?php  echo $qiniu['secret_key'];?>" autocomplete="off">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">存储区域</label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="qiniu[qiniu_area]" value="1" <?php  if($qiniu['qiniu_area']==1) { ?>checked<?php  } ?>> 华东</label>
							&nbsp;
							<label class="radio-inline"><input type="radio" name="qiniu[qiniu_area]" value="2" <?php  if($qiniu['qiniu_area']==2) { ?>checked<?php  } ?>> 华北</label>
							&nbsp;
							<label class="radio-inline"><input type="radio" name="qiniu[qiniu_area]" value="3" <?php  if($qiniu['qiniu_area']==3) { ?>checked<?php  } ?>> 华南</label>
							&nbsp;
							<label class="radio-inline"><input type="radio" name="qiniu[qiniu_area]" value="4" <?php  if($qiniu['qiniu_area']==4) { ?>checked<?php  } ?>> 北美</label>
							<span class="help-block">请根据您的对象存储空间选择对应的区域</strong></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">七牛加速域名</label>
						<div class="col-sm-9">
							<input type="text" name="qiniu[url]" class="form-control" value="<?php  echo $qiniu['url'];?>" autocomplete="off">
							<span class="help-block">不带http://或https://，例如：v.haoshu888.com</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">开启https</label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="qiniu[https]" value="1" <?php  if($qiniu['https']==1) { ?>checked<?php  } ?> /> 开启</label>&nbsp;
							<label class="radio-inline"><input type="radio" name="qiniu[https]" value="0" <?php  if($qiniu['https']==0) { ?>checked<?php  } ?> /> 关闭</label>
							<span class="help-block">开启https后，系统将自动把视频链接的http协议转为https，如果你的站点没有开启https化，请不要开启该选项</span>
						</div>
					</div>
				</div>
				<!-- 七牛云对象存储 End -->

				<!-- 腾讯云对象存储 Start -->
				<div class="qcloud-params-admin" <?php  if($setting['savetype']!=2) { ?>style="display:none;"<?php  } ?>>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">腾讯云APPID</label>
						<div class="col-sm-9">
							<input type="text" name="qcloud[appid]" class="form-control" value="<?php  echo $qcloud['appid'];?>" autocomplete="off">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">Bucket(存储桶名称)</label>
						<div class="col-sm-9">
							<input type="text" name="qcloud[bucket]" class="form-control" value="<?php  echo $qcloud['bucket'];?>" autocomplete="off">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">腾讯云SecretID</label>
						<div class="col-sm-9">
							<input type="text" name="qcloud[secretid]" class="form-control" value="<?php  echo $qcloud['secretid'];?>" autocomplete="off">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">腾讯云SecretKey</label>
						<div class="col-sm-9">
							<input type="text" name="qcloud[secretkey]" class="form-control" value="<?php  echo $qcloud['secretkey'];?>" autocomplete="off">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">存储区域</label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="qcloud[qcloud_area]" value="bj" <?php  if($qcloud['qcloud_area']=='bj') { ?>checked<?php  } ?>>北京</label>
							<label class="radio-inline"><input type="radio" name="qcloud[qcloud_area]" value="tj" <?php  if($qcloud['qcloud_area']=='tj') { ?>checked<?php  } ?>>北京一区(华北)</label>
							<label class="radio-inline"><input type="radio" name="qcloud[qcloud_area]" value="sh" <?php  if($qcloud['qcloud_area']=='sh') { ?>checked<?php  } ?>>上海(华东)</label>
							<label class="radio-inline"><input type="radio" name="qcloud[qcloud_area]" value="gz" <?php  if($qcloud['qcloud_area']=='gz') { ?>checked<?php  } ?>>广州(华南)</label>
							<label class="radio-inline"><input type="radio" name="qcloud[qcloud_area]" value="cd" <?php  if($qcloud['qcloud_area']=='cd') { ?>checked<?php  } ?>>成都(西南)</label>
							<label class="radio-inline"><input type="radio" name="qcloud[qcloud_area]" value="sgp" <?php  if($qcloud['qcloud_area']=='sgp') { ?>checked<?php  } ?>>新加坡</label>
							<!-- <label class="radio-inline"><input type="radio" name="qcloud[qcloud_area]" value="hk" <?php  if($qcloud['qcloud_area']=='hk') { ?>checked<?php  } ?>>香港</label>
							<label class="radio-inline"><input type="radio" name="qcloud[qcloud_area]" value="ca" <?php  if($qcloud['qcloud_area']=='ca') { ?>checked<?php  } ?>>多伦多</label>
							<label class="radio-inline"><input type="radio" name="qcloud[qcloud_area]" value="ger" <?php  if($qcloud['qcloud_area']=='ger') { ?>checked<?php  } ?>> 法兰克福</label> -->
							<span class="help-block">请根据您的对象存储空间选择对应的区域</strong></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">源站类型</label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="qcloud[qcloud_type]" value="xml" <?php  if($qcloud['qcloud_type']=='xml') { ?>checked<?php  } ?>>XML节点</label>
							<label class="radio-inline"><input type="radio" name="qcloud[qcloud_type]" value="json" <?php  if($qcloud['qcloud_type']=='json') { ?>checked<?php  } ?>>JSON节点</label>
							<span class="help-block">在腾讯云对象存储里~域名管理~下能查看到源站类型的选“XML节点”，其他的选“JSON节点”</strong></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">腾讯云加速域名</label>
						<div class="col-sm-9">
							<input type="text" name="qcloud[url]" class="form-control" value="<?php  echo $qcloud['url'];?>" autocomplete="off">
							<span class="help-block">不带http://或https://，例如：video.haoshu888.com</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">开启https</label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="qcloud[https]" value="1" <?php  if($qcloud['https']==1) { ?>checked<?php  } ?> /> 开启</label>&nbsp;
							<label class="radio-inline"><input type="radio" name="qcloud[https]" value="0" <?php  if($qcloud['https']==0) { ?>checked<?php  } ?> /> 关闭</label>
							<span class="help-block">开启https后，系统将自动把视频链接的http协议转为https，如果你的站点没有开启https化，请不要开启该选项</span>
						</div>
					</div>
				</div>
				<!-- 腾讯云对象存储 End -->

				<!-- 阿里云点播 Start -->
				<div class="aliyun-params-admin" <?php  if($setting['savetype']!=3) { ?>style="display:none;" <?php  } ?>>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">regionId(区域)</label>
						<div class="col-sm-9">
							<input type="text" name="aliyun[region_id]" class="form-control" value="<?php  echo $aliyun['region_id'];?>">
							<span class="help-block">请填写cn-shanghai</strong></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">Access Key ID</label>
						<div class="col-sm-9">
							<input type="text" name="aliyun[access_key_id]" class="form-control" value="<?php  echo $aliyun['access_key_id'];?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">Access Key Secret</label>
						<div class="col-sm-9">
							<input type="text" name="aliyun[access_key_secret]" class="form-control" value="<?php  echo $aliyun['access_key_secret'];?>">
						</div>
					</div>
				</div>
				<!-- 阿里云点播 End -->

				<!-- 腾讯云点播 Start -->
				<div class="qcloudVod-params-admin" <?php  if($setting['savetype']!=4) { ?>style="display:none;" <?php  } ?>>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">APPId</label>
						<div class="col-sm-9">
							<input type="text" name="qcloudvod[appid]" class="form-control" value="<?php  echo $qcloudvod['appid'];?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">Secret Id</label>
						<div class="col-sm-9">
							<input type="text" name="qcloudvod[secret_id]" class="form-control" value="<?php  echo $qcloudvod['secret_id'];?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">Secret Key</label>
						<div class="col-sm-9">
							<input type="text" name="qcloudvod[secret_key]" class="form-control" value="<?php  echo $qcloudvod['secret_key'];?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">防盗链KEY</label>
						<div class="col-sm-9">
							<input type="text" name="qcloudvod[safety_key]" class="form-control" value="<?php  echo $qcloudvod['safety_key'];?>">
						</div>
					</div>
				</div>
				<!-- 腾讯云点播 End -->
			</div>
		</div>

		<div class="form-group col-sm-12">
			<input type="hidden" name="id" value="<?php  echo $setting['id'];?>" />
			<input type="submit" name="submit" value="保存设置" class="btn btn-primary col-lg-1" />
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
</div>
<script type="text/javascript">
	$(function() {
		$(':radio[name="savetype"]').click(function() {
			if($(this).val() == '0') {
				$('.qiniu-params-admin').hide();
				$('.qcloud-params-admin').hide();
				$('.aliyun-params-admin').hide();
				$('.qcloudVod-params-admin').hide();
			} else if($(this).val() == '1') {
				$('.qiniu-params-admin').show();
				$('.qcloud-params-admin').hide();
				$('.aliyun-params-admin').hide();
				$('.qcloudVod-params-admin').hide();
			} else if($(this).val() == '2') {
				$('.qiniu-params-admin').hide();
				$('.qcloud-params-admin').show();
				$('.aliyun-params-admin').hide();
				$('.qcloudVod-params-admin').hide();
			} else if($(this).val() == '3') {
				$('.qiniu-params-admin').hide();
				$('.qcloud-params-admin').hide();
				$('.aliyun-params-admin').show();
				$('.qcloudVod-params-admin').hide();
			} else if($(this).val() == '4') {
				$('.qiniu-params-admin').hide();
				$('.qcloud-params-admin').hide();
				$('.aliyun-params-admin').hide();
				$('.qcloudVod-params-admin').show();
			}
		});
	});
</script>