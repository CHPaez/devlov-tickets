<?php
$title=($cfg && is_object($cfg) && $cfg->getTitle())
    ? $cfg->getTitle() : 'osTicket :: '.__('Support Ticket System');
$signin_url = ROOT_PATH . "login.php"
    . ($thisclient ? "?e=".urlencode($thisclient->getEmail()) : "");
$signout_url = ROOT_PATH . "logout.php?auth=".$ost->getLinkToken();

header("Content-Type: text/html; charset=UTF-8");
header("Content-Security-Policy: frame-ancestors ".$cfg->getAllowIframes()."; script-src 'self' 'unsafe-inline'; object-src 'none'");

if (($lang = Internationalization::getCurrentLanguage())) {
    $langs = array_unique(array($lang, $cfg->getPrimaryLanguage()));
    $langs = Internationalization::rfc1766($langs);
    header("Content-Language: ".implode(', ', $langs));
}
?>
<!DOCTYPE html>
<html<?php
if ($lang
        && ($info = Internationalization::getLanguageInfo($lang))
        && (@$info['direction'] == 'rtl'))
    echo ' dir="rtl" class="rtl"';
if ($lang) {
    echo ' lang="' . $lang . '"';
}

// Dropped IE Support Warning
if (osTicket::is_ie())
    $ost->setWarning(__('osTicket no longer supports Internet Explorer.'));
?>>
<head>
    <script>(function(){try{var t=localStorage.getItem('dl_theme');if(t==='dark')document.documentElement.setAttribute('data-theme','dark');}catch(e){}})();</script>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo Format::htmlchars($title); ?></title>
    <meta name="description" content="customer support platform">
    <meta name="keywords" content="osTicket, Customer support system, support ticket system">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/osticket.css" media="screen">
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>css/theme.css" media="screen">
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>css/print.css" media="print">
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/typeahead.css"
         media="screen" />
    <link type="text/css" href="<?php echo ROOT_PATH; ?>css/ui-lightness/jquery-ui-1.13.2.custom.min.css"
        rel="stylesheet" media="screen" />
    <link rel="stylesheet" href="<?php echo ROOT_PATH ?>css/jquery-ui-timepicker-addon.css" media="all">
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/thread.css" media="screen">
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/redactor.css" media="screen">
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/font-awesome.min.css">
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/flags.css">
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/rtl.css"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/select2.min.css">
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>assets/devlov/tokens.css">
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>assets/devlov/theme-overrides.css">
    <!-- Favicons -->
    <link rel="icon" type="image/svg+xml" href="<?php echo ROOT_PATH ?>images/favicon.svg" />
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-3.7.0.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-ui-1.13.2.custom.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-ui-timepicker-addon.js"></script>
    <script src="<?php echo ROOT_PATH; ?>js/osticket.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/filedrop.field.js"></script>
    <script src="<?php echo ROOT_PATH; ?>js/bootstrap-typeahead.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor-plugins.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor-osticket.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/select2.min.js"></script>
    <script>window.DL_ROOT_PATH = "<?php echo ROOT_PATH; ?>";</script>
    <script src="<?php echo ROOT_PATH; ?>assets/devlov/vendor/lottie-light.min.js"></script>
    <script src="<?php echo ROOT_PATH; ?>assets/devlov/lottie-init.js"></script>
    <script src="<?php echo ROOT_PATH; ?>assets/devlov/ticket-guide-init.js"></script>
    <script src="<?php echo ROOT_PATH; ?>assets/devlov/theme-toggle.js"></script>
    <script src="<?php echo ROOT_PATH; ?>assets/devlov/starfield.js"></script>
    <?php
    if($ost && ($headers=$ost->getExtraHeaders())) {
        echo "\n\t".implode("\n\t", $headers)."\n";
    }

    // Offer alternate links for search engines
    // @see https://support.google.com/webmasters/answer/189077?hl=en
    if (($all_langs = Internationalization::getConfiguredSystemLanguages())
        && (count($all_langs) > 1)
    ) {
        $langs = Internationalization::rfc1766(array_keys($all_langs));
        $qs = array();
        parse_str($_SERVER['QUERY_STRING'], $qs);
        foreach ($langs as $L) {
            $qs['lang'] = $L; ?>
        <link rel="alternate" href="//<?php echo $_SERVER['HTTP_HOST'] . htmlspecialchars($_SERVER['REQUEST_URI']); ?>?<?php
            echo http_build_query($qs); ?>" hreflang="<?php echo $L; ?>" />
<?php
        } ?>
        <link rel="alternate" href="//<?php echo $_SERVER['HTTP_HOST'] . htmlspecialchars($_SERVER['REQUEST_URI']); ?>"
            hreflang="x-default" />
<?php
    }
    ?>
</head>
<body>
    <button type="button" class="dl-theme-toggle dl-theme-toggle-fixed" title="Modo creativo">
        <svg class="dl-theme-toggle-icon dl-theme-toggle-sun" viewBox="0 0 24 24" width="18" height="18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <circle cx="12" cy="12" r="4.5" stroke="currentColor" stroke-width="1.6"/>
            <path d="M12 2.5v2.4M12 19.1v2.4M4.2 4.2l1.7 1.7M18.1 18.1l1.7 1.7M2.5 12h2.4M19.1 12h2.4M4.2 19.8l1.7-1.7M18.1 5.9l1.7-1.7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
        <svg class="dl-theme-toggle-icon dl-theme-toggle-moon" viewBox="0 0 24 24" width="18" height="18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M20 14.2A8.5 8.5 0 1 1 9.8 4a7 7 0 0 0 10.2 10.2Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
        </svg>
    </button>
    <div id="container">
        <?php
        if($ost->getError())
            echo sprintf('<div class="error_bar">%s</div>', $ost->getError());
        elseif($ost->getWarning())
            echo sprintf('<div class="warning_bar">%s</div>', $ost->getWarning());
        elseif($ost->getNotice())
            echo sprintf('<div class="notice_bar">%s</div>', $ost->getNotice());
        ?>
<?php if ($nav) { ?>
        <div id="app-body">
        <div id="sidebar">
            <a id="logo" href="<?php echo ROOT_PATH; ?>index.php"
            title="<?php echo __('Support Center'); ?>">
                <span class="valign-helper"></span>
                <img class="brand-heart" src="<?php echo ROOT_PATH; ?>images/devlo-heart-icon.png" alt="">
                <span class="brand-wordmark"><?php echo $ost->getConfig()->getTitle(); ?></span>
            </a>
            <div id="sidebar-account">
            <p>
             <?php
                if ($thisclient && is_object($thisclient) && $thisclient->isValid()
                    && !$thisclient->isGuest()) {
                 echo Format::htmlchars($thisclient->getName()).'&nbsp;|';
                 ?>
                <a href="<?php echo ROOT_PATH; ?>profile.php"><?php echo __('Profile'); ?></a> |
                <a href="<?php echo ROOT_PATH; ?>tickets.php"><?php echo sprintf(__('Tickets <b>(%d)</b>'), $thisclient->getNumTickets()); ?></a> -
                <a href="<?php echo $signout_url; ?>"><?php echo __('Sign Out'); ?></a>
            <?php
            } else {
                if ($cfg->getClientRegistrationMode() == 'public') { ?>
                    <?php echo __('Guest User'); ?> | <?php
                }
                if ($thisclient && $thisclient->isValid() && $thisclient->isGuest()) { ?>
                    <a href="<?php echo $signout_url; ?>"><?php echo __('Sign Out'); ?></a><?php
                }
                elseif ($cfg->getClientRegistrationMode() != 'disabled') { ?>
                    <a href="<?php echo $signin_url; ?>"><?php echo __('Sign In'); ?></a>
<?php
                }
            } ?>
            </p>
<?php
if (($all_langs = Internationalization::getConfiguredSystemLanguages())
    && (count($all_langs) > 1)
) {
    $qs = array();
    parse_str($_SERVER['QUERY_STRING'], $qs);
    ?>
            <p>
    <?php foreach ($all_langs as $code=>$info) {
        list($lang, $locale) = explode('_', $code);
        $qs['lang'] = $code;
    ?>
        <a class="flag flag-<?php echo strtolower($info['flag'] ?: $locale ?: $lang); ?>"
            href="?<?php echo http_build_query($qs);
            ?>" title="<?php echo Internationalization::getLanguageDescription($code); ?>">&nbsp;</a>
    <?php } ?>
            </p>
<?php } ?>
            </div>
            <ul id="nav">
            <?php
            if(($navs=$nav->getNavLinks()) && is_array($navs)){
                foreach($navs as $name => $navitem) {
                    echo sprintf('<li><a class="%s %s" href="%s">%s</a></li>%s',$navitem['active']?'active':'',$name,(ROOT_PATH.$navitem['href']),$navitem['desc'],"\n");
                }
            } ?>
            </ul>
        </div>
        <div id="main-panel">
        <div id="content"<?php if (!empty($dl_starfield)) echo ' data-dl-starfield'; ?>>

         <?php if($errors['err']) { ?>
            <div id="msg_error"><?php echo $errors['err']; ?></div>
         <?php }elseif($msg) { ?>
            <div id="msg_notice"><?php echo $msg; ?></div>
         <?php }elseif($warn) { ?>
            <div id="msg_warning"><?php echo $warn; ?></div>
         <?php } ?>
<?php } else { ?>
        <div id="header">
            <a class="pull-left" id="logo" href="<?php echo ROOT_PATH; ?>index.php"
            title="<?php echo __('Support Center'); ?>">
                <span class="valign-helper"></span>
                <img class="brand-heart" src="<?php echo ROOT_PATH; ?>images/devlo-heart-icon.png" alt="">
                <span class="brand-wordmark"><?php echo $ost->getConfig()->getTitle(); ?></span>
            </a>
        </div>
        <div class="clear"></div>
        <hr>
        <div id="content">

         <?php if($errors['err']) { ?>
            <div id="msg_error"><?php echo $errors['err']; ?></div>
         <?php }elseif($msg) { ?>
            <div id="msg_notice"><?php echo $msg; ?></div>
         <?php }elseif($warn) { ?>
            <div id="msg_warning"><?php echo $warn; ?></div>
         <?php } ?>
<?php } ?>
