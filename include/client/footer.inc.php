        </div>
<?php if ($nav) { ?>
        </div>
        </div>
<?php } ?>
    </div>
    <div id="footer">
        <img src="<?php echo ROOT_PATH; ?>images/devlo-heart-icon.png" alt="" class="footer-heart">
        <p class="footer-tagline"><?php echo __('Código con sentido, software con pasión.'); ?></p>
        <p class="footer-copy"><?php echo __('Copyright &copy;'); ?> <?php echo date('Y'); ?> <?php
        echo Format::htmlchars((string) $ost->company ?: 'DevLov'); ?></p>
    </div>
<div id="overlay"></div>
<div id="loading">
    <h4><?php echo __('Please Wait!');?></h4>
    <p><?php echo __('Please wait... it will take a second!');?></p>
</div>
<?php
if (($lang = Internationalization::getCurrentLanguage()) && $lang != 'en_US') { ?>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>ajax.php/i18n/<?php
        echo $lang; ?>/js"></script>
<?php } ?>
<script type="text/javascript">
    getConfig().resolve(<?php
        include INCLUDE_DIR . 'ajax.config.php';
        $api = new ConfigAjaxAPI();
        print $api->client(false);
    ?>);
</script>
</body>
</html>
