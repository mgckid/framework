<?php $this->layout('Layout/admin'); ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <label for="">
            <a class="btn btn-success btn-sm"  data-power="System/addConfig" href="<?=U('System/addConfig')?>">添加新变量</a>
        </label>
    </div>
    <div class="panel-body">
        <div class="col-md-12">
            <form class="form form-horizontal" name="add" action="<?= U('System/sysConfig') ?>"   method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>配置说明</th>
                                <th width="60%">配置值</th>
                                <th>配置名称</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($configList as $value): ?>
                            <tr id="row<?=$value['id']?>">
                                <td><?= $value['description'] ?>:</td>
                                <td>
                                    <?php if ($value['form_type']=='text'): ?>
                                        <input class="form-control" type="text" name="<?= $value['name'] ?>" value="<?= $value['value'] ?>">
                                    <?php endif ?>
                                    <?php if ($value['form_type'] == 'mtext'): ?>
                                        <textarea class="form-control" name="<?= $value['name'] ?>"  rows="3"><?= $value['value'] ?></textarea>
                                    <?php endif ?>
                                </td>
                                <td><?= $value['name'] ?></td>
                                <td>
                                    <a class="btn btn-success btn-xs" data-power="System/addConfig" href="<?=U('System/addConfig',array('id'=>$value['id']))?>">编辑变量</a>
                                    <a class="btn btn-danger btn-xs" data-power="System/delConfig" href="javascript:void(0)" onclick="del(<?= $value['id'] ?>)">删除</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">操作</label>
                    <div class="col-sm-10">
                        <button class="btn btn-success " data-power="System/addConfig">保存</button>
                        <button type="reset" class="btn btn-danger ml10">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!--/panel-->


<script type="text/javascript">
    $('form[name=add]').ajaxForm({
        dataType: 'json',
        error: function () {
            layer.msg('服务器连接失败');
        },
        success: function (data) {
            if (data.status == 1) {
                $('form').find('input:reset').click();
            }
            layer.alert(data.msg)
        }
    });
</script>

<script>
    //删除栏目
    function del(id) {
        layer.confirm('您确定要删除选中的栏目么？', {
            btn: ['确定', '取消'] //按钮
        }, function () {
            $.post('<?= U('System/delConfig') ?>', {id: id}, function (data) {
                layer.msg(data.msg);
                if (data.status == 1) {
                    $("#row" + id).remove();
                }
            }, 'json');

        }, function () {
            return
        });
    }
</script>
