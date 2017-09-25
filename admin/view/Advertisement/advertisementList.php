<?php $this->layout('Layout/admin'); ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <a class="btn btn-success btn-sm" data-power="Advertisement/addPosition"
           href="<?= U('Advertisement/addad') ?>">添加广告</a>
    </div>
    <div class="panel-body">
        <!--table-->
        <table class="table">
            <thead>
            <tr>
                <?php foreach ($list_init as $key=> $value):?>
                    <th><?=$value['field_name']?></th>
                <?php endforeach;?>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($adList as $value): ?>
                <tr id="row<?= $value['id'] ?>">
                    <?php foreach ($list_init as $key => $val): ?>
                        <td><?= !empty($val['enum']) ? $val['enum'][$value[$key]] : $value[$key] ?></td>
                    <?php endforeach;?>
                    <td>
                        <a class="btn btn-success btn-xs" data-power="Advertisement/addad"  href="<?= U('Advertisement/addad', array('id' => $val['id'])) ?>">编辑</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <!--/列表-->
        <?= $pages ?>
        <!--/分页-->
    </div>
</div>
