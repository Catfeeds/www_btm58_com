<?php defined('IN_IA') or exit('Access Denied');?><div class="main">
	<div class="alert alert-info">
	    请勿随意删除VIP等级，删除VIP等级会影响已购买该等级的会员使用.
	</div>
	<form action="" method="post" onsubmit="return formcheck(this)">
		<div class="panel panel-default">
			<div class="panel-heading">
				VIP等级设置
			</div>
			<div class="panel-body">
				<table class="table">
					<thead>
						<tr>
							<th style="width:8%;">ID</th>
							<th style="width:15%;">等级名称</th>
							<th style="width:10%;">有效期</th>
							<th style="width:10%;">价格</th>
							<th style="width:10%;">赠送积分</th>
							<th style="width:10%;">会员折扣</th>
							<th style="width:10%;">状态</th>
							<th style="width:12%;">一二三级佣金</th>
							<th style="width:17%;">操作</th>
						</tr>
					</thead>
					<tbody>
						<?php  if(is_array($level_list)) { foreach($level_list as $item) { ?>
						<tr>
							<td><?php  echo $item['id'];?></td>
							<td><?php  echo $item['level_name'];?></td>
							<td><?php  echo $item['level_validity'];?>天</td>
							<td><?php  echo $item['level_price'];?>元</td>
							<td><?php echo $item['integral']>0 ? $item['integral'].'积分' : '无';?></td>
							<td><?php  echo $item['discount'];?>%</td>
							<td><?php echo $item['is_show']==1 ? '显示':'隐藏';?></td>
							<td><?php  echo $item['com']['commission1'];?>%/<?php  echo $item['com']['commission2'];?>%/<?php  echo $item['com']['commission3'];?>%</td>
							<td>
                            	<a class="btn btn-default" href="<?php  echo $this->createWebUrl('setting', array('op'=>'vipLevel','level_id'=>$item['id']));?>">编辑</a>
                            	<a class="btn btn-default" href="<?php  echo $this->createWebUrl('setting', array('op'=>'delVipLevel','level_id'=>$item['id']));?>" onclick="return confirm('确认删除此等级吗？');return false;">删除</a>
							</td>
						</tr>
						<?php  } } ?>
					</tbody>
				</table>
			</div>
			<div class="panel-footer">
				<a class="btn btn-default" href="<?php  echo $this->createWebUrl('setting',array('op'=>'vipLevel'));?>"><i class="fa fa-plus"></i> 添加新等级</a>
			</div>
		</div>
	</form>
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
		<div class="panel panel-default">
			<div class="panel-heading">购买会员服务</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">VIP分销开关</label>
					<div class="col-sm-9">
						<label class="radio-inline"><input type="radio" name="vip_sale" value="1" <?php  if($comsetting['vip_sale']==1) { ?>checked<?php  } ?> /> 开启</label> &nbsp;
						<label class="radio-inline"><input type="radio" name="vip_sale" value="0" <?php  if($comsetting['vip_sale']==0) { ?>checked<?php  } ?> /> 关闭</label>
						<span class="help-block"><strong></strong>开启后，下级购买或续费VIP服务时，上级也会获得分销佣金</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">会员服务描述</label>
					<div class="col-sm-9">
						<?php  echo tpl_ueditor('vipdesc', $comsetting['vipdesc']);?>
						<div class="help-block">
							该描述不为空时，会显示在前台手机端“VIP服务”页面，尽量仅填写文字而不包含图品、音视频等其他多媒体元素。
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group col-sm-12">
			<input type="hidden" name="id" value="<?php  echo $comsetting['id'];?>" />
			<input type="submit" name="submit" value="保存设置" class="btn btn-primary col-lg-1" />
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
</div>