<?php $this->layout('Layout/admin') ?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="col-md-10">
            <?= \houduanniu\web\Form::create() ?>
            <div class="form-group">
                <label class="col-sm-2 control-label">操作</label>

                <div class="col-sm-10">
                    <button class="btn btn-success" type="submit">提交</button>
                    <button type="reset" class="btn btn-danger ml10">重置</button>
                    <button type="button" id="fenci" class="btn btn-primary ml10">文章分词</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
<!--/panel-->
<!--js组件 开始-->
<!--上传 开始-->
<?= $this->insert('Common/plug_upload_fileinput') ?>
<script>
    $(function () {
        fileInput('upload_file');
    })
</script>
<!--上传 结束-->
<!--编辑器 开始-->
<?= $this->insert('Common/plug_ueditor') ?>
<script>
    $(function () {
        ueditor('content');
    })
</script>
<!--编辑器 结束-->
<!--js组件 结束-->

<!--表单提交 开始-->
<script>
    $(function () {
        $('form[name=autoform]').ajaxForm({
            dataType: 'json',
            error: function () {
                layer.msg('服务器连接失败');
            },
            success: function (data) {
                layer.alert(data.msg)
                if (data.status == 1) {
                    $('form[name=autoform]').find('input:reset').click();
                    setTimeout(function () {
                          window.history.go(-1)
                    }, 2000);
                }
            }
        });
    })
</script>
<!--表单提交 结束-->
<script>
    $(function () {
        //栏目分类
        var param = {
            model_name: 'BaseLogic',
            method_name: 'getCategoryData'
        };
        $.post('<?=U('pop/index')?>', param, function (data) {
            if(data.status==1){
                var option='';
                var selected_id = $('#category_id').data('selected');
                $.each(data.data,function(i,n){
                    var selected = selected_id== n.id?'selected = "selected"':'';
                    option = option + '<option value="' + n.id + '" ' + selected + '>'+n.category_name+'</option>'
                })
                $('#category_id').append(option);
            }
        }, 'json')
    })
    //分词操作
    $('#fenci').on('click', function () {
        var content = $('[name=content]').val();
        var json = {
            model_name: 'Article',
            method_name: 'getFenci',
            param: {
                content: content
            }
        };
        $.post('<?=U('pop/index')?>', json, function (data) {
            if (data.status != 1) {
                layer.alert(data.msg);
            }
            //关键字
            $('[name=keywords]').val(data.data.keyword);
            //描述
            $('[name=description]').val(data.data.description);
            //标签
            $('[name=post_tag]').val(data.data.tag)
        }, 'json')
    })
    //获取栏目数据

</script>

