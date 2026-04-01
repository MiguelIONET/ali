<?php
// enviar.php

// Importar las clases de PHPMailer
// Asegúrate de que la ruta sea correcta según tu estructura de carpetas
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// Configurar cabecera de respuesta JSON
header('Content-Type: application/json; charset=UTF-8');

// Respuesta por defecto
$response = [
    'status' => 'error',
    'message' => 'Error desconocido.'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Sanitización de entradas (Igual que tenías antes)
    $empresa = filter_var($_POST['empresa'] ?? 'No especificada', FILTER_SANITIZE_STRING);
    $nombre = filter_var($_POST['nombre'] ?? '', FILTER_SANITIZE_STRING);
    $asunto_usuario = filter_var($_POST['asunto'] ?? '', FILTER_SANITIZE_STRING);
    $email_usuario = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $telefono = filter_var($_POST['telefono'] ?? 'No especificado', FILTER_SANITIZE_STRING);
    $mensaje = filter_var($_POST['mensaje'] ?? '', FILTER_SANITIZE_STRING);

    // Validación básica
    if (empty($nombre) || !filter_var($email_usuario, FILTER_VALIDATE_EMAIL) || empty($mensaje) || empty($asunto_usuario)) {
        echo json_encode(['status' => 'error', 'message' => 'Faltan campos obligatorios o el correo es inválido.']);
        exit;
    }

    // 2. Instancia de PHPMailer
    $mail = new PHPMailer(true); // 'true' habilita las excepciones

    try {
        // --- CONFIGURACIÓN DEL SERVIDOR (SMTP) ---
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;   // Descomenta para ver errores detallados en el log (no en producción)
        $mail->isSMTP();                                            
        
        // ** AQUÍ DEBES PONER TUS DATOS REALES **
        $mail->Host       = 'kamioni.mx';      // Servidor SMTP (ej: smtp.gmail.com, mail.midominio.com)
        $mail->SMTPAuth   = true;                   // Activar autenticación SMTP
        $mail->Username   = 'contacto_web@kamioni.mx';  // Tu correo real (el que envía)
        $mail->Password   = 'Kamioni2026$';   // La contraseña de ese correo
        
        // Encriptación y puerto
        // Opción A: SSL (generalmente puerto 465)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
        $mail->Port       = 465;                                    
        
        // Opción B: TLS (generalmente puerto 587) - Si usas Outlook/Office365 usa este
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        // $mail->Port       = 587;

        // --- DESTINATARIOS ---
        // Quién envía: DEBE ser el mismo del Username para evitar bloqueos
        $mail->setFrom('contacto_web@kamioni.mx', 'Formulario Web'); 
        
        // A quién le llega el correo (A ti mismo)
        $mail->addAddress('joseluis.velazquez@kamioni.mx', 'Jose Luis Velazquez Ortega');     
        $mail->addAddress('luis.nava@kamioni.mx', '  Luis Adrian Nava Maya');    
        $mail->addAddress('israel.velazquez@kamioni.mx', ' Israel Velazquez Ortega');   
        $mail->addCC('contacto@kamioni.mx', 'Angel Mogollan Palizada'); 
        // Responder a: Aquí ponemos el correo del cliente que llenó el form
        $mail->addReplyTo($email_usuario, $nombre);

        // --- CONTENIDO ---
        $mail->isHTML(true);    
        $mail->CharSet = 'UTF-8';                              
        $mail->Subject = "Contacto Web: $asunto_usuario";
        $mail->Body    = "
                <div style='background-color: #f4f4f4; padding: 20px; font-family: Segoe UI, Tahoma, Geneva, Verdana, sans-serif;'>
    <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1);'>
        
        <div style='background-color: #ffffff; padding: 20px; text-align: center;'>
            <img src='https://kamioni.mx/images/logo.svg' alt='KAMIONI' style='max-width: 150px; display: block; margin: 0 auto;'>
        </div>

        <div style='padding: 30px; color: #333333;'>
            <h2 style='color: #BE307C; margin-top: 0; text-align: center;'>Nuevo contacto desde formulario Web</h2>
            <p style='font-size: 16px; line-height: 1.5;'>Un cliente ha llenado el formulario de contacto. Aquí están los detalles:</p>
            
            <table style='width: 100%; border-collapse: collapse; margin-top: 20px;'>
                <tr style='border-bottom: 1px solid #eeeeee;'>
                    <td style='padding: 10px; font-weight: bold; color: #555;'>Nombre:</td>
                    <td style='padding: 10px; color: #333;'>$nombre</td>
                </tr>
                <tr style='border-bottom: 1px solid #eeeeee;'>
                    <td style='padding: 10px; font-weight: bold; color: #555;'>Empresa:</td>
                    <td style='padding: 10px; color: #333;'>$empresa</td>
                </tr>
                <tr style='border-bottom: 1px solid #eeeeee;'>
                    <td style='padding: 10px; font-weight: bold; color: #555;'>Email:</td>
                    <td style='padding: 10px; color: #333;'><a href='mailto:$email_usuario' style='color: #0a9d65; text-decoration: none;'>$email_usuario</a></td>
                </tr>
                <tr style='border-bottom: 1px solid #eeeeee;'>
                    <td style='padding: 10px; font-weight: bold; color: #555;'>Teléfono:</td>
                    <td style='padding: 10px; color: #333;'>$telefono</td>
                </tr>
                 <tr style='border-bottom: 1px solid #eeeeee;'>
                    <td style='padding: 10px; font-weight: bold; color: #555;'>Asunto:</td>
                    <td style='padding: 10px; color: #333;'>$asunto_usuario</td>
                </tr>
            </table>

            <div style='background-color: #f9f9f9; padding: 15px; border-left: 4px solid #71A63C; margin-top: 20px;'>
                <p style='margin: 0; font-weight: bold; color: #555;'>Mensaje:</p>
                <p style='margin: 10px 0 0 0; font-style: italic;'>" . nl2br($mensaje) . "</p>
            </div>

            <div style='margin-top: 30px; text-align: center;'>
                <a href='mailto:$email_usuario' style='background-color: #71A63C; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>Responder Correo</a>
            </div>
        </div>

        <div style='background-color: #eeeeee; padding: 15px; text-align: center; font-size: 12px; color: #888888;'>
            <p style='margin: 0;'>Este mensaje fue enviado desde el formulario de contacto de Kamioni.mx</p>
        </div>
    </div>
</div>
        ";
        
        // Versión texto plano para clientes de correo antiguos
        $mail->AltBody = "Nuevo mensaje de $nombre ($email_usuario)\nEmpresa: $empresa\nTel: $telefono\n\nMensaje:\n$mensaje";

        // Enviar
        $mail->send();
        
        $response['status'] = 'success';
        $response['message'] = '¡Gracias! Tu mensaje ha sido enviado correctamente.';

    } catch (Exception $e) {
        // Error de PHPMailer
        $response['status'] = 'error';
        // En producción, es mejor poner "Error en el servidor" en lugar de $mail->ErrorInfo para no revelar datos
        $response['message'] = 'No se pudo enviar el mensaje. Error del servidor.'; 
        // Si necesitas depurar, descomenta la siguiente línea:
        // $response['debug'] = $mail->ErrorInfo;
    }

} else {
    $response['message'] = 'Método no permitido.';
}

// Devolver JSON al JavaScript
echo json_encode($response);
?>