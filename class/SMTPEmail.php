<?php
// si es windows y no está cargada (phpinfo) la extensión soap, carga la dll. 
// que debe estar como dll en carpeta de funcionamiento
// if (!extension_loaded("soap"))
//     dl("php_soap.dll");

include("../lib/init.php");

/*
----------------------------------------------------------------------------------------
      27/09/2019 - Gerson Bolívar
      este método recibe los parámetros a través de la interfaz SOAP 
      la definición de los parámetros se puede observar en EmailEX.wsdl. 
      el formato para la definición de los parámetros y estructuras es xml v1
      ----------------------------------------------------------------------------------
      el método requiere una ruta física para crear temporalmente los archivos 
----------------------------------------------------------------------------------------    
*/
class SMTPEmail
{


    function enviarEmailEX($to, $cc, $bcc, $asunto, $mensaje, $adjunto, $Formato)
    {
        $fecha = new DateTime();
        $observacion = "";
        $token = "AAC56BE333E832A";
        $configApp = include('../config/configSMTP.php'); //estructura con los parámetros de cofiguración capa SOAP
        //if ($usuario == $configApp['usuarioWS'] && $password == $configApp['passwordWS']) {
        try {
            //validando datos necesarios para el envio de correo 
            $resultadoValidacion = $this->validarParametros($to, $cc, $bcc, $mensaje, $asunto);
            if ($resultadoValidacion != "ok") {
                if ($resultadoValidacion == "-1")
                    return "faltan datos (to,asunto,mensaje)";
                if ($resultadoValidacion == "to" || $resultadoValidacion == "cc" || $resultadoValidacion == "bcc")
                    return $resultadoValidacion . ": error en formato email";
            }
            //generando los archivos en carpeta temporal
            $nombreArchivos = array();
            foreach ($adjunto as $clave => $valor) {
                file_put_contents($configApp['rutaTemporalArchivos'] . $clave, base64_decode($valor)); //se genera el archivo físico desde el base 64 recibido
                $nombreArchivos[] = $configApp['rutaTemporalArchivos'] . $clave; //se guardan en array los nombres de los archivos para posterior uso en el envio de correo
            }
            //instanciando la clase de integracióncon Exchange Server
            $ec = new ExchangeClient();
            $ec->init($configApp['cuentaUsuarioEnvio'], $configApp['passwordUsuarioEnvio'], NULL, $configApp['urlServidor']); //descubrimiento del Exchange Server

            $ec->send_message($to, $asunto, $mensaje, $Formato, true, true, $nombreArchivos, $cc, $bcc);

            //$ec->send_message($to, $asunto, $mensaje,"HTML",true,true,$nombreArchivos,$cc); //envio de email
            // if (!$this->escribirBitacora($to[0], $cc[0], $bcc[0], $asunto, $mensaje, $configApp["habilitarLogBitacora"], $configApp["tipoLogBitacora"], $token, $configApp["rutaLogBitacora"], $configApp["nombreArchivoUnicoBitacora"])) {
            //     $this->escribirError($to[0], $asunto, $configApp["habilitarLogError"], $token, $configApp["rutaLogErrores"], "No se puede escribir en bitácora");
            //     $observacion = "sin bitácora";
            // }
            //eliminando los archivos de la carpeta temporal
            foreach ($adjunto as $clave => $valor) {
                unlink($configApp['rutaTemporalArchivos'] . $clave);
            }
            //error_log("¡mensaje enviado!", 3, $configApp['rutaLogBitacora']."/bitacora.log");
            return "mensaje enviado! " . $observacion; //llegando a este punto, todo ok

        } catch (Exception $ex) {
           // $this->escribirError($to[0], $asunto, $configApp["habilitarLogError"], $token, $configApp["rutaLogErrores"], $ex->getMessage());
            //error_log("[".$fecha->format( 'd-m-Y h:m:s' )."] Error".$ex->getMessage()."\r\n", 3, $configApp['rutaLogErrores']."/error.log");
            return "Error interno. Contacte al administrador de la aplicación";
        }
        //} else {
        //     error_log("[" . $fecha->format('d-m-Y h:m:s') . "] Error: usuario o contraseña incorrectos: " . $usuario . "," . $password . "\r\n", 3, $configApp['rutaLogErrores'] . "/error.log");
        //     return 'usuario o contraseña incorrectos';
        //}
    }

    /*
----------------------------------------------------------------------------------------
      27/09/2019 - Gerson Bolívar
      este método escribe en archivos de bitácora dependiendo de la configuración en el archivo configApp.php
      los valores posibles de configuración son:
      - 1 :un solo archivo log
      - 2 :un archivo log por cada envio (Timestamp + token + to (primer destinatario))
      ----------------------------------------------------------------------------------
      el método registrará siempre y cuando esté habilitada la bitácora en configApp.php 
----------------------------------------------------------------------------------------    
*/
/*
    function escribirBitacora($to, $cc, $bcc, $asunto, $mensaje, $Habilitado, $tipoBitacora, $token, $rutaLogBitacora, $nombreArchivoBitacora)
    {
        $fecha = new DateTime();
        $configApp = include('../config/configApp.php'); //estructura con los parámetros de cofiguración capa SOAP
        if ($Habilitado == 1) {
            try {
                if ($tipoBitacora == 1)
                    error_log("[" . $fecha->format('d-m-Y h:m:s') . "] to: " . $to . ", cc: " . $cc . ", bcc: " . $bcc . ", asunto: " . $asunto . ", mensaje: " . $mensaje . ", token: " . $token . ", " . $fecha->getTimestamp() . "\r\n", 3, $rutaLogBitacora . "/" . $nombreArchivoBitacora);
                else if ($tipoBitacora == 2)
                    error_log("[" . $fecha->format('d-m-Y h:m:s') . "] to: " . $to . ", cc: " . $cc . ", bcc: " . $bcc . ", asunto: " . $asunto . ", mensaje: " . $mensaje . "\r\n", 3, $rutaLogBitacora . "/" . $fecha->getTimestamp() . $token . $to . ".log");
            } catch (Exception $ex) {
                error_log("Error" . $ex->getMessage() . "\r\n", 3, $configApp['rutaLogErrores'] . "/error.log");
                return false;
            }
        }
        return true;
    }
    */

    /*
----------------------------------------------------------------------------------------
      29/09/2019 - Gerson Bolívar
      
      el método registrará siempre y cuando esté habilitado el log de errores en configApp.php 
----------------------------------------------------------------------------------------    
*/
    function escribirError($to, $asunto, $Habilitado, $token, $rutaLogErrores, $excepcion)
    {
        $fecha = new DateTime();

        if ($Habilitado == 1) {
            try {
                error_log("[" . $fecha->format('d-m-Y h:m:s') . "] to: " . $to . ", asunto: " . $asunto . ", token: " . $token . ", error: " . $excepcion . "\r\n", 3, $rutaLogErrores . "/error.log");
            } catch (Exception $ex) {
                return "Error interno. No se puede escribir en log. Contacte al administrador de la aplicación";
            }
        }
    }

    /*
----------------------------------------------------------------------------------------
      29/09/2019 - Gerson Bolívar
      
      método para validar los valores de los parámetros obligatorios para enviar email. 
----------------------------------------------------------------------------------------    
*/
    function validarParametros($to, $cc, $bcc, $mensaje, $asunto)
    {
        //campos obligatorios
        if (isset($to) && isset($mensaje) && isset($asunto)) {
            foreach ($to as &$correo) {
               // $this->escribirError($correo, "", 1, "sdfdssdf", "/var/www/html/ApiMonday/error/", $correo);
                if (!filter_var(trim($correo), FILTER_VALIDATE_EMAIL))
                    return "to";
            }
            if (isset($cc))
                foreach ($cc as $correo)
                    if (!filter_var($correo, FILTER_VALIDATE_EMAIL))
                        return "cc";
            if (isset($bcc))
                foreach ($bcc as $correo)
                    if (!filter_var($correo, FILTER_VALIDATE_EMAIL))
                        return "bcc";
            return "ok";
        } else
            return "-1";
    }
}
