<?php
// Procesa el formulario de contacto (contacto.html) y envía el mensaje por correo.

$destinatario = "efictalia@efictalia.com.mx";
$remitente    = "noreply@efictalia.com.mx"; // debe pertenecer al dominio para no caer en spam

// Solo se aceptan envíos del formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contacto.html', true, 303);
    exit;
}

// Campo trampa: invisible para personas, los bots suelen rellenarlo
if (!empty($_POST['website'])) {
    header('Location: respuesta.html', true, 303);
    exit;
}

$nombre  = trim(strip_tags($_POST['name'] ?? ''));
$email   = trim($_POST['email'] ?? '');
$mensaje = trim(strip_tags($_POST['message'] ?? ''));

function volver_con_error($texto) {
    echo '<script>alert(' . json_encode($texto) . '); history.back();</script>';
    exit;
}

if ($nombre === '' || $email === '' || $mensaje === '') {
    volver_con_error('Nombre, correo electrónico y mensaje son necesarios.');
}

// filter_var rechaza saltos de línea, lo que evita la inyección de cabeceras
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    volver_con_error('Formato de correo electrónico incorrecto.');
}

if (mb_strlen($nombre) > 100 || mb_strlen($mensaje) > 5000) {
    volver_con_error('El mensaje es demasiado largo.');
}

$nombre = str_replace(["\r", "\n"], ' ', $nombre);

$asunto  = '=?UTF-8?B?' . base64_encode('Mensaje desde efictalia.com.mx') . '?=';
$cuerpo  = "Nombre: $nombre\n";
$cuerpo .= "Email: $email\n\n";
$cuerpo .= "Mensaje:\n$mensaje\n";

$cabeceras  = "From: Efictalia Web <$remitente>\r\n";
$cabeceras .= "Reply-To: $email\r\n";
$cabeceras .= "MIME-Version: 1.0\r\n";
$cabeceras .= "Content-Type: text/plain; charset=UTF-8\r\n";
$cabeceras .= "Content-Transfer-Encoding: 8bit\r\n";

if (!mail($destinatario, $asunto, $cuerpo, $cabeceras, "-f$remitente")) {
    volver_con_error('No se pudo enviar el mensaje. Por favor escríbenos a ' . $destinatario);
}

header('Location: respuesta.html', true, 303);
exit;
