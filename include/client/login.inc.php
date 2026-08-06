<?php
if(!defined('OSTCLIENTINC')) die('Access Denied');

$email=Format::input($_POST['luser']?:$_GET['e']);
$passwd=Format::input($_POST['lpasswd']?:$_GET['t']);

$content = Page::lookupByType('banner-client');

if ($content) {
    list($title, $body) = $ost->replaceTemplateVariables(
        array($content->getLocalName(), $content->getLocalBody()));
} else {
    $title = __('Sign In');
    $body = __('Código con sentido, software con pasión.');
}

$can_open_ticket = ($cfg->getClientRegistrationMode() != 'disabled'
    || !$cfg->isClientLoginRequired());
?>
<div class="login-shell">
    <div class="login-brand">
        <div class="login-brand-logo">
            <img class="login-brand-heart" src="<?php echo ROOT_PATH; ?>images/devlo-heart-icon.png" alt="">
            <span class="login-brand-wordmark"><?php echo $ost->getConfig()->getTitle(); ?></span>
        </div>

        <h1 class="login-brand-title"><?php echo Format::display($title); ?></h1>
        <p class="login-brand-tagline"><?php echo Format::display($body); ?></p>

        <div class="login-brand-actions">
<?php if ($can_open_ticket) { ?>
            <a href="open.php" class="login-brand-link"><?php echo __('Open a New Ticket'); ?> &rarr;</a>
<?php } ?>
            <a href="view.php" class="login-brand-link"><?php echo __('Check Ticket Status'); ?> &rarr;</a>
        </div>

        <div class="login-illustration" aria-hidden="true">
            <svg viewBox="0 0 220 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <ellipse cx="110" cy="176" rx="70" ry="8" stroke="currentColor" stroke-opacity="0.35" stroke-width="2"/>
                <rect x="40" y="34" width="140" height="96" rx="18" stroke="currentColor" stroke-width="3"/>
                <path d="M78 130 L78 156 L104 130 Z" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/>
                <text x="110" y="92" text-anchor="middle" font-family="'Space Grotesk', sans-serif" font-size="30" font-weight="600" fill="currentColor">&lt;/&gt;</text>
                <circle cx="34" cy="30" r="4" fill="currentColor" fill-opacity="0.5"/>
                <circle cx="190" cy="50" r="3" fill="currentColor" fill-opacity="0.5"/>
                <circle cx="196" cy="118" r="4" fill="currentColor" fill-opacity="0.5"/>
                <circle cx="24" cy="120" r="3" fill="currentColor" fill-opacity="0.5"/>
            </svg>
        </div>
    </div>

    <div class="login-panel">
        <div class="login-tabs">
            <span class="login-tab login-tab-active"><?php echo __('Sign In'); ?></span>
<?php if ($cfg && $cfg->isClientRegistrationEnabled()) { ?>
            <a class="login-tab" href="account.php?do=create"><?php echo __('Create an account'); ?></a>
<?php } ?>
        </div>

        <form action="login.php" method="post" id="clientLoginForm" class="login-form">
            <?php csrf_token(); ?>
<?php if ($errors['login']) { ?>
            <div class="login-error"><?php echo Format::htmlchars($errors['login']); ?></div>
<?php } ?>
            <div class="login-field">
                <label for="username"><?php echo __('Email or Username'); ?></label>
                <input id="username" type="text" name="luser" size="30" placeholder="<?php echo __('Email or Username'); ?>" value="<?php echo $email; ?>" class="nowarn">
            </div>
            <div class="login-field">
                <label for="passwd"><?php echo __('Password'); ?></label>
                <input id="passwd" type="password" name="lpasswd" size="30" maxlength="128" placeholder="••••••••" value="<?php echo $passwd; ?>" class="nowarn">
            </div>
            <div class="login-form-actions">
                <input class="btn" type="submit" value="<?php echo __('Sign In'); ?>">
<?php if ($suggest_pwreset) { ?>
                <a class="login-forgot" href="pwreset.php"><?php echo __('Forgot My Password'); ?></a>
<?php } ?>
            </div>
<?php
$ext_bks = array();
foreach (UserAuthenticationBackend::allRegistered() as $bk)
    if ($bk instanceof ExternalAuthentication)
        $ext_bks[] = $bk;

if (count($ext_bks)) { ?>
            <div class="login-ext-auth">
<?php   foreach ($ext_bks as $bk) { ?>
                <div class="external-auth"><?php $bk->renderExternalLink(); ?></div>
<?php   } ?>
            </div>
<?php } ?>
        </form>

        <div class="login-panel-footer">
            <b><?php echo __("I'm an agent"); ?></b> — <a href="<?php echo ROOT_PATH; ?>scp/"><?php echo __('sign in here'); ?></a>
        </div>
    </div>
</div>
