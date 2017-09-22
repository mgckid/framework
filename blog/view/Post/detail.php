<?php $this->layout('Layout/detail')?>
<main class="site-content">

    <div class="container mt-4">
        <div class="site-main">
            <div class="row">
                <div class="col-lg-8">
                    <main class="main-content">


                        <div class="card card-shadow">
                            <div class="card-body">


                                <h1 class="card-title mb-4"><?=$info['article']['title']?></h1>
                                <p class="card-text text-link-color-muted">
                                    <small>
                                        <span class="post-time"><?=date('Y年m月d日',strtotime($info['article']['created']))?></span>
                                        <span class="post-category"> &nbsp;&bull;&nbsp;<a href="<?=U('Index/category',['cate'=>$info['article']['category_alias']])?>" rel="category tag"><?=$info['article']['category_name']?></a></span>
                                    </small>
                                </p>

                                <div class="entry-content pt-3">

                                    <?=htmlspecialchars_decode($info['article']['content'])?>

                                    <div class="post-tags mt-4 mb-3">
                                        <?php foreach($info['article']['post_tag'] as $value):?>
                                        <a  href="<?=U('Post/tags',['tag_name'=>$value])?>" class="btn btn-light btn-sm mr-2 mb-2"><?=$value?></a>
                                        <?php endforeach?>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <nav class="post-navigation card" role="navigation">
                            <div class="card-body">
                                <h4 class="sr-only sr-only-focusable">Post navigation</h4>
                                <div class="nav-links clearfix">
                                    <div class="nav-previous float-left">&larr;
                                        <a href="<?=!empty($info['pre'])?U('Post/detail',['id'=>$info['pre']['post_id']]):'javascript:void(0)'?>" rel="prev">
                                            <?=!empty($info['pre'])?$info['pre']['title']:'无'?>
                                        </a>
                                    </div>
                                    <div class="nav-next float-right">
                                        <a href="<?=!empty($info['next'])?U('Post/detail',['id'=>$info['next']['post_id']]):'javascript:void(0)'?>" rel="prev">
                                            <?=!empty($info['next'])?$info['next']['title']:'无'?>
                                        </a>
                                    </div>
                                </div><!-- .nav-links -->

                            </div>
                        </nav><!-- .navigation -->

                        <div class="related-posts card">
                            <div class="card-body"><h3 class="card-title h6 mb-3">你可能喜欢：</h3>

                                <div class="row">
                                <?php foreach($related_article as $value):?>
                                    <div class="col-md-4 col-6">
                                        <div class="card border-0">
                                            <a class="entry-img" href="<?=U('Post/detail',['id'=>$value['post_id']])?>">
                                                <img src="<?=$value['main_image']?>" alt="<?=$value['title']?>" class="card-img rounded-0">
                                            </a>
                                            <div class="card-body px-0 py-3">
                                                <p class="card-title text-link-color line-clamp-2 text-overflow-ellipsis">
                                                    <a href="<?=U('Post/detail',['id'=>$value['post_id']])?>" rel="bookmark"><?=$value['title']?></a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach;?>
                                </div>
                            </div>
                        </div>


                    </main>
                </div>

                <!--侧边栏 开始-->
                <?=$this->insert('Common/sidebar')?>
                <!--侧边栏 结束-->
            </div>
        </div><!-- /.row -->
    </div><!-- /.container -->
</main>