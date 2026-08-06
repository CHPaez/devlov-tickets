UPDATE ost_content SET body = '<h1>Bienvenido al Centro de Soporte</h1> <p> Para agilizar tus solicitudes y atenderte mejor, usamos un sistema de tickets. Cada solicitud recibe un número único que podés usar para seguir su progreso y respuestas en línea. Guardamos el historial completo de tus solicitudes para tu referencia. Se requiere una dirección de correo válida para enviar un ticket. </p>' WHERE id = 1;

UPDATE ost_content SET name = 'Iniciá sesión en %{company.name}', body = 'Para atenderte mejor, te invitamos a registrar una cuenta.' WHERE id = 9;
UPDATE ost_form SET title = 'Información de Contacto' WHERE id = 1;
UPDATE ost_form SET title = 'Detalles del Ticket', instructions = 'Contanos qué necesitás' WHERE id = 2;

UPDATE ost_form_field SET label = 'Dirección de Correo' WHERE id = 1;
UPDATE ost_form_field SET label = 'Nombre Completo' WHERE id = 2;
UPDATE ost_form_field SET label = 'Número de Teléfono' WHERE id IN (3, 25, 29);
UPDATE ost_form_field SET label = 'Notas Internas' WHERE id IN (4, 31);
UPDATE ost_form_field SET label = 'Resumen del Problema' WHERE id = 20;
UPDATE ost_form_field SET label = 'Detalles del Problema', hint = 'Contanos el motivo de tu consulta.' WHERE id = 21;
UPDATE ost_form_field SET label = 'Nivel de Prioridad' WHERE id = 22;
UPDATE ost_content SET name = 'Gracias', body = '<div>%{ticket.name},
<br>
<br>
Gracias por contactarnos.
<br>
<br>
Se creó tu solicitud de ticket y un representante se va a comunicar con vos a la brevedad si es necesario.
<br>
<br>
Equipo de soporte
</div>' WHERE id = 2;
UPDATE ost_config SET value='es' WHERE `key`='system_language';
