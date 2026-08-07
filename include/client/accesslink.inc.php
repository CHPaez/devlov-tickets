<?php
if(!defined('OSTCLIENTINC')) die('Access Denied');

$email=Format::input($_POST['lemail']?$_POST['lemail']:$_GET['e']);
$ticketid=Format::input($_POST['lticket']?$_POST['lticket']:$_GET['t']);

if ($cfg->isClientEmailVerificationRequired())
    $button = __("Email Access Link");
else
    $button = __("View Ticket");
?>
<h1><?php echo __('Check Ticket Status'); ?></h1>
<p><?php
echo __('Please provide your email address and a ticket number.');
if ($cfg->isClientEmailVerificationRequired())
    echo ' '.__('An access link will be emailed to you.');
else
    echo ' '.__('This will sign you in to view your ticket.');
?></p>

<div id="dl-access-guide" class="dl-ticket-guide">
    <button type="button" class="dl-ticket-guide-dismiss" aria-label="Cerrar">&times;</button>
    <div class="dl-ticket-guide-step">
        <svg class="dl-ticket-guide-icon" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <rect x="10" y="8" width="30" height="40" rx="4" stroke="currentColor" stroke-width="3"/>
            <path d="M18 20h14M18 28h14M18 36h8" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
            <circle cx="42" cy="42" r="10" stroke="currentColor" stroke-width="3"/>
            <path d="M49.5 49.5L57 57" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
        </svg>
        <p><strong>Ingresá tu correo y número de ticket</strong><br>Los mismos datos que usaste al crearlo.</p>
    </div>
    <div class="dl-ticket-guide-arrow" aria-hidden="true">&rarr;</div>
    <div class="dl-ticket-guide-step">
        <svg class="dl-ticket-guide-icon" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <rect x="8" y="16" width="48" height="34" rx="5" stroke="currentColor" stroke-width="3"/>
            <path d="M10 20l22 16 22-16" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
<?php if ($cfg->isClientEmailVerificationRequired()) { ?>
        <p><strong>Te enviamos un enlace de acceso</strong><br>Revisá tu correo para ingresar de forma segura.</p>
<?php } else { ?>
        <p><strong>Te mostramos el estado actual</strong><br>Accedés directamente, sin pasos extra.</p>
<?php } ?>
    </div>
    <div class="dl-ticket-guide-arrow" aria-hidden="true">&rarr;</div>
    <div class="dl-ticket-guide-step">
        <svg class="dl-ticket-guide-icon" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <circle cx="32" cy="34" r="20" stroke="currentColor" stroke-width="3"/>
            <path d="M32 22v12l9 6" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M22 8h20" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
        </svg>
        <p><strong>Vas a poder ver las respuestas</strong><br>Todo el historial y las novedades del agente.</p>
    </div>
</div>

<form action="login.php" method="post" id="accessLinkForm" class="access-form">
    <?php csrf_token(); ?>
<?php if ($errors['login']) { ?>
    <div class="form-error"><?php echo Format::htmlchars($errors['login']); ?></div>
<?php } ?>
    <div class="form-field">
        <label for="email"><?php echo __('Email Address'); ?></label>
        <input id="email" placeholder="<?php echo __('e.g. john.doe@osticket.com'); ?>" type="text"
            name="lemail" size="30" value="<?php echo $email; ?>" class="nowarn">
    </div>
    <div class="form-field">
        <label for="ticketno"><?php echo __('Ticket Number'); ?></label>
        <input id="ticketno" type="text" name="lticket" placeholder="<?php echo __('e.g. 051243'); ?>"
            size="30" value="<?php echo $ticketid; ?>" class="nowarn">
    </div>
    <div class="form-actions">
        <input class="btn" type="submit" value="<?php echo $button; ?>">
    </div>
<?php if ($cfg && $cfg->getClientRegistrationMode() !== 'disabled') { ?>
    <p class="access-form-hint">
        <?php echo __('Have an account with us?'); ?>
        <a href="login.php"><?php echo __('Sign In'); ?></a>
    <?php if ($cfg->isClientRegistrationEnabled()) { ?>
        <?php echo sprintf(__('or %s register for an account %s to access all your tickets.'),
            '<a href="account.php?do=create">','</a>'); ?>
    <?php } ?>
    </p>
<?php } ?>
</form>

<?php if ($cfg->getClientRegistrationMode() != 'disabled'
    || !$cfg->isClientLoginRequired()) { ?>
<p class="access-form-footer">
    <?php echo sprintf(
    __("If this is your first time contacting us or you've lost the ticket number, please %s open a new ticket %s"),
        '<a href="open.php">','</a>'); ?>
</p>
<?php } ?>
