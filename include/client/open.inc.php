<?php
if(!defined('OSTCLIENTINC')) die('Access Denied!');
$info=array();
if($thisclient && $thisclient->isValid()) {
    $info=array('name'=>$thisclient->getName(),
                'email'=>$thisclient->getEmail(),
                'phone'=>$thisclient->getPhoneNumber());
}

$info=($_POST && $errors)?Format::htmlchars($_POST):$info;

$form = null;
if (!$info['topicId']) {
    if (array_key_exists('topicId',$_GET) && preg_match('/^\d+$/',$_GET['topicId']) && Topic::lookup($_GET['topicId']))
        $info['topicId'] = intval($_GET['topicId']);
    else
        $info['topicId'] = $cfg->getDefaultTopicId();
}

$forms = array();
if ($info['topicId'] && ($topic=Topic::lookup($info['topicId']))) {
    foreach ($topic->getForms() as $F) {
        if (!$F->hasAnyVisibleFields())
            continue;
        if ($_POST) {
            $F = $F->instanciate();
            $F->isValidForClient();
        }
        $forms[] = $F->getForm();
    }
}

?>
<h1><?php echo __('Open a New Ticket');?></h1>
<p><?php echo __('Please fill in the form below to open a new ticket.');?></p>

<div id="dl-ticket-guide" class="dl-ticket-guide">
    <button type="button" class="dl-ticket-guide-dismiss" aria-label="Cerrar">&times;</button>
    <div class="dl-ticket-guide-step">
        <svg class="dl-ticket-guide-icon" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M10 14h44a4 4 0 0 1 4 4v22a4 4 0 0 1-4 4H24l-10 10V44h-4a4 4 0 0 1-4-4V18a4 4 0 0 1 4-4Z" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/>
            <path d="M18 26h28M18 34h18" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
        </svg>
        <p><strong>Contanos tu problema</strong><br>Completá el formulario con el mayor detalle posible.</p>
    </div>
    <div class="dl-ticket-guide-arrow" aria-hidden="true">&rarr;</div>
    <div class="dl-ticket-guide-step">
        <svg class="dl-ticket-guide-icon" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <circle cx="26" cy="20" r="9" stroke="currentColor" stroke-width="3"/>
            <path d="M10 50c0-10 7-16 16-16s16 6 16 16" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
            <path d="M40 20h14a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3h-3v6l-7-6h-4a3 3 0 0 1-3-3V23a3 3 0 0 1 3-3Z" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/>
        </svg>
        <p><strong>Un agente te responde</strong><br>Recibís la respuesta por correo y en tu cuenta.</p>
    </div>
    <div class="dl-ticket-guide-arrow" aria-hidden="true">&rarr;</div>
    <div class="dl-ticket-guide-step">
        <svg class="dl-ticket-guide-icon" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <circle cx="32" cy="34" r="20" stroke="currentColor" stroke-width="3"/>
            <path d="M32 22v12l9 6" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M22 8h20" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
        </svg>
        <p><strong>Seguí el estado cuando quieras</strong><br>Volvé a &ldquo;Tickets&rdquo; para ver las novedades.</p>
    </div>
</div>

<form id="ticketForm" method="post" action="open.php" enctype="multipart/form-data">
  <?php csrf_token(); ?>
  <input type="hidden" name="a" value="open">
  <table width="800" cellpadding="1" cellspacing="0" border="0">
    <tbody>
<?php
        if (!$thisclient) {
            $uform = UserForm::getUserForm()->getForm($_POST);
            if ($_POST) $uform->isValid();
            $uform->render(array('staff' => false, 'mode' => 'create'));
        }
        else { ?>
            <tr><td colspan="2"><hr /></td></tr>
        <tr><td><?php echo __('Email'); ?>:</td><td><?php
            echo $thisclient->getEmail(); ?></td></tr>
        <tr><td><?php echo __('Client'); ?>:</td><td><?php
            echo Format::htmlchars($thisclient->getName()); ?></td></tr>
        <?php } ?>
    </tbody>
    <tbody>
    <tr><td colspan="2"><hr />
        <div class="form-header" style="margin-bottom:0.5em">
        <b><?php echo __('Help Topic'); ?></b>
        <span class="dl-help-icon" tabindex="0" role="note" aria-label="Ayuda">?<span class="dl-tooltip-bubble">Elegí la categoría que mejor describe tu consulta: ayuda a que el ticket llegue directo al área correcta.</span></span>
        </div>
    </td></tr>
    <tr>
        <td colspan="2">
            <select id="topicId" name="topicId" onchange="javascript:
                    var data = $(':input[name]', '#dynamic-form').serialize();
                    $.ajax(
                      'ajax.php/form/help-topic/' + this.value,
                      {
                        data: data,
                        dataType: 'json',
                        success: function(json) {
                          $('#dynamic-form').empty().append(json.html);
                          $(document.head).append(json.media);
                        }
                      });">
                <option value="" selected="selected">&mdash; <?php echo __('Select a Help Topic');?> &mdash;</option>
                <?php
                if($topics=Topic::getPublicHelpTopics()) {
                    foreach($topics as $id =>$name) {
                        echo sprintf('<option value="%d" %s>%s</option>',
                                $id, ($info['topicId']==$id)?'selected="selected"':'', $name);
                    }
                } ?>
            </select>
            <font class="error">*&nbsp;<?php echo $errors['topicId']; ?></font>
        </td>
    </tr>
    </tbody>
    <tbody id="dynamic-form">
        <?php
        $options = array('mode' => 'create');
        foreach ($forms as $form) {
            include(CLIENTINC_DIR . 'templates/dynamic-form.tmpl.php');
        } ?>
    </tbody>
    <tbody>
    <?php
    if($cfg && $cfg->isCaptchaEnabled() && (!$thisclient || !$thisclient->isValid())) {
        if($_POST && $errors && !$errors['captcha'])
            $errors['captcha']=__('Please re-enter the text again');
        ?>
    <tr class="captchaRow">
        <td class="required"><?php echo __('CAPTCHA Text');?>:</td>
        <td>
            <span class="captcha"><img src="captcha.php" border="0" align="left"></span>
            &nbsp;&nbsp;
            <input id="captcha" type="text" name="captcha" size="6" autocomplete="off">
            <em><?php echo __('Enter the text shown on the image.');?></em>
            <font class="error">*&nbsp;<?php echo $errors['captcha']; ?></font>
        </td>
    </tr>
    <?php
    } ?>
    <tr><td colspan=2>&nbsp;</td></tr>
    </tbody>
  </table>
<hr/>
  <p class="buttons" style="text-align:center;">
        <input type="submit" value="<?php echo __('Create Ticket');?>">
        <input type="reset" name="reset" value="<?php echo __('Reset');?>">
        <input type="button" name="cancel" value="<?php echo __('Cancel'); ?>" onclick="javascript:
            $('.richtext').each(function() {
                var redactor = $(this).data('redactor');
                if (redactor && redactor.opts.draftDelete)
                    redactor.plugin.draft.deleteDraft();
            });
            window.location.href='index.php';">
  </p>
</form>
