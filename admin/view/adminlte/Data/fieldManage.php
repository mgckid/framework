<?php $this->layout('Layout/admin') ?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="operate_box mb10">
            <a class="btn btn-success btn-xs" data-power="Data/addField" href="<?= U('Data/addField',['dictionary_id'=>$dictionary_info['id']]) ?>">添加字段</a>
        </div>
        <table class="table ">
            <tr>
                <?php foreach ($list_init as $key=> $value):?>
                    <th><?=$value['field_name']?></th>
                <?php endforeach;?>
                <th>操作</th>
            </tr>
            <?php foreach ($list as  $value):?>
                <tr id="row<?= $value['id'] ?>">
                    <?php foreach ($list_init as $key => $val): ?>
                        <td><?= !empty($val['enum']) ? $val['enum'][$value[$key]] : $value[$key] ?></td>
                    <?php endforeach;?>
                    <td>
                        <a class="btn btn-success btn-xs"  href="<?= U('Data/editField', array('id' => $value['id'])) ?>" data-power="Data/editField">编辑</a>
                        <a class="btn btn-danger ml10 btn-xs" href="javascript:void(0)" onclick="delRecord(<?= $value['id'] ?>)" data-power="Data/delField">删除</a>
                    </td>
                </tr>
            <?php endforeach; ?>


        </table>
        <!--/列表-->
    </div>
</div>
<script>
    //删除记录
    function delRecord(id) {
        layer.confirm('您确定要删除选中的记录么？', {
            btn: ['确定', '取消'] //按钮
        }, function () {
            $.post('<?= U('Data/delField') ?>', {id: id}, function (data) {
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