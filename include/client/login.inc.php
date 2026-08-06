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
    $body = __('To better serve you, we encourage our clients to register for an account and verify the email address we have on record.');
}

$can_open_ticket = ($cfg->getClientRegistrationMode() != 'disabled'
    || !$cfg->isClientLoginRequired());
?>
<div class="login-shell">
    <div class="login-brand">
        <img class="login-brand-heart" src="<?php echo ROOT_PATH; ?>images/devlo-heart-icon.png" alt="">
        <h1 class="login-brand-title"><?php echo Format::display($title); ?></h1>
        <p class="login-brand-tagline"><?php echo Format::display($body); ?></p>

        <div class="login-brand-actions">
<?php if ($can_open_ticket) { ?>
            <a href="open.php" class="login-action-btn login-action-new"><?php echo __('Open a New Ticket'); ?></a>
<?php } ?>
            <a href="view.php" class="login-action-btn login-action-status"><?php echo __('Check Ticket Status'); ?></a>
        </div>
    </div>

    <div class="login-panel">
        <h2 class="login-panel-title"><?php echo __('Sign In'); ?></h2>
        <form action="login.php" method="post" id="clientLogin" class="login-form">
            <?php csrf_token(); ?>
<?php if ($errors['login']) { ?>
            <div class="login-error"><?php echo Format::htmlchars($errors['login']); ?></div>
<?php } ?>
            <div class="login-field">
                <label for="username"><?php echo __('Email or Username'); ?></label>
                <input id="username" type="text" name="luser" size="30" value="<?php echo $email; ?>" class="nowarn">
            </div>
            <div class="login-field">
                <label for="passwd"><?php echo __('Password'); ?></label>
                <input id="passwd" type="password" name="lpasswd" size="30" maxlength="128" value="<?php echo $passwd; ?>" class="nowarn">
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
<?php if ($cfg && $cfg->isClientRegistrationEnabled()) { ?>
            <p><?php echo __('Not yet registered?'); ?> <a href="account.php?do=create"><?php echo __('Create an account'); ?></a></p>
<?php } ?>
            <p><b><?php echo __("I'm an agent"); ?></b> — <a href="<?php echo ROOT_PATH; ?>scp/"><?php echo __('sign in here'); ?></a></p>
        </div>
    </div>
</div>
