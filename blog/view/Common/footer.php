<footer class="footer mt-3">
    <div class="container">
        <p class="copyright mb-0">
            &copy; <?=date('Y',strtotime($siteInfo['site_found_date']))?>-<?=date('Y',time())?>
            .<a href="<?=C('HOME_URL')?>" title="<?=$siteInfo['site_name']?>"><?=$siteInfo['site_name']?></a>
            .<a href="###" rel="external nofollow" target="_blank"><?=$siteInfo['site_icp_code']?></a>
            .Style by <a>轻主题</a>
            .POWER BY <a href="<?=C('HOME_URL')?>" title="后端牛框架">houduanniu framework</a>
        </p>
    </div>
</footer>
<?php if(ENVIRONMENT=='product'):?>
    <script type='text/javascript' src='https://cdn.bootcss.com/jquery/3.2.0/jquery.min.js'></script>
    <script type='text/javascript' src='https://cdn.bootcss.com/bootstrap/4.0.0-beta/js/bootstrap.min.js'></script>
<?php else:?>
    <script type='text/javascript' src='/static/writing/js/jquery.js'></script>
    <script type='text/javascript' src='/static/writing/js/bootstrap.min.js'></script>
<?php endif;?>
<script type='text/javascript' src='/static/writing/js/popper.js'></script>
<script type='text/javascript' src='/static/writing/js/tether.js'></script>


