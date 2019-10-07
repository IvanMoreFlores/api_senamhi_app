<?php

defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class App_movil extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("App_movil_model", "movil");
    }

    public function getLogin() {
        $array_detalle = json_decode(file_get_contents('php://input'), true);
        $user = $array_detalle['usuario'];
        $pasw = $array_detalle['password'];
        $dataUser = $this->movil->getUserInfo($user);
        if ($dataUser) {
            if ($dataUser['FLG_NUEVO'] == 0) {
                if ($pasw == $dataUser['PASS']) {
//                    sleep(10);
                    echo json_encode($dataUser);
                } else {
                    $mensaje = array("error" => "Contraseña incorrecta");
                    echo json_encode($mensaje);
                }
            } else {
                if (password_verify($pasw, $dataUser['PASS'])) {
//                    sleep(10);
                    echo json_encode($dataUser);
                } else {
                    $mensaje = array("error" => "Contraseña incorrecta");
                    echo json_encode($mensaje);
                }
            }
        } else {
            $mensaje = array("error" => "No se encontró usuario");
            echo json_encode($mensaje);
        }
    }

    public function updatePassword() {
        $array_detalle = json_decode(file_get_contents('php://input'), true);
        $id_usuario = $array_detalle['id_usuario'];
        $new_password = $array_detalle['new_password'];
        $old_password = $array_detalle['old_password'];
        $respuesta = $this->movil->updatePassword(password_hash($new_password, PASSWORD_DEFAULT), $old_password, $id_usuario);
        echo json_encode($respuesta);
    }

    public function recoverPass() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        $array_detalle = json_decode(file_get_contents('php://input'), true);
        $email = $array_detalle['email'];

        if ($this->movil->getCountEmail($email) > 0) {
            $token = hash("sha512", $email);
            $arrayInsert = array(
                "TXT_TOKEN" => $token,
                "USER_EMAIL" => $email
            );
            $flgExisteEmailToken = $this->movil->getCountEmailToken($email);
            if ($flgExisteEmailToken > 0) {
                $data = $this->movil->deleteEmailToken($email);
            }
            $data = $this->movil->insertTokenUsuario($arrayInsert);
            $texto = 'Ingrese al siguiente enlace para cambiar su contraseña <a href="' . base_url() . 'olvidoPassword?token=' . $token . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" style="cursor:pointer" data-original-title="Cambiar contraseña" > AQUÍ.</a><br>';
            $asunto = 'Recuperación de contraseña';
            $result = $this->enviarEmail($email, $asunto, $this->makeHTMLToEMail($texto));
            echo json_encode($result);
        } else {
            $data['error'] = EXIT_ERROR;
            $data['msj'] = 'Email invalido';
            echo json_encode($data);
        }
    }

    public function enviarEmail($correoDestino, $asunto, $body) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $CI = & get_instance();
            $CI->load->library('email');
            $configGmail = array('protocol' => PROTOCOL,
                'smtp_host' => SMTP_HOST,
                'smtp_port' => SMTP_PORT,
                'smtp_user' => CORREO_BASE,
                'smtp_pass' => PASSWORD_BASE,
                'mailtype' => MAILTYPE,
                'charset' => 'utf-8',
                'newline' => "\r\n",
                'wordwrap' => TRUE,
                'starttls' => TRUE,
                'validate' => TRUE,
                'priority' => 1);
            $CI->email->initialize($configGmail);
            $CI->email->from(CORREO_BASE);
            $CI->email->to($correoDestino);
            $CI->email->subject($asunto);
            $CI->email->message($body);
            if ($CI->email->send()) {
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se envió correctamente el email';
            } else {
                $err = print_r($CI->email->print_debugger(), TRUE);
                log_message('error', 'err: ' . $err);
                throw new Exception($err);
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    public function makeHTMLToEMail($texto = '') {
        $html = '<div>
                    ' . $texto . '
                </div>
                <br>
                <div>
                    <table width="730" cellspacing="0" cellpadding="0" border="0">
                        <tbody>
                            <tr>
                                <th colspan="3" scope="col" style="background-color: rgb(46, 116, 181); line-height: 10px;">&nbsp;</th>
                            </tr>
                            <tr>
                                <td width="200">
                                    <img style="margin: 8px 15px 8px 10px;" width="200" height="94" dfsrc="https://www.senamhi.gob.pe/public/images/logo-senamhi-webmail.png" src="https://www.senamhi.gob.pe/public/images/logo-senamhi-webmail.png" saveddisplaymode="">
                                </td>
                                <td width="300">
                                    <div style="margin: 5px 15px 5px 10px;"><span style="display: block; font-family: &quot;arial narrow&quot;, sans-serif; font-weight: bold; font-size: 10pt; line-height: 15pt;">OTIC</span><span style="display: block; font-family: &quot;arial narrow&quot;, sans-serif; font-size: 9pt;">APLICACIÓN WEB DE VOZ Y DATA</span><span style="display: block; font-family: &quot;arial narrow&quot;, sans-serif; font-size: 9pt;">OFICINA DE TECNOLOGIAS DE LA INFORMACION Y LA COMUNICACION</span> <span style="display: block; font-family: &quot;arial narrow&quot;, sans-serif; font-size: 9pt; line-height: 15pt;"> SENAMHI - PERÚ </span></div>
                                </td>
                                <td width="220">
                                    <div style="margin: 5px 10px;"><span style="display: block; font-family: &quot;arial narrow&quot;, sans-serif; font-size: 10pt; line-height: 15pt;"> <b>D:</b> Jr. Cahuide 785, Jesús <span class="Object" role="link" id="OBJ_PREFIX_DWT226_com_zimbra_date"><span class="Object" role="link" id="OBJ_PREFIX_DWT236_com_zimbra_date">Mar</span></span>ía - Lima</span> <span style="display: block; font-family: &quot;arial narrow&quot;, sans-serif; font-size: 10pt;"> <b>T:</b>01 6141414 Anexo - </span> <span style="display: block; font-family: &quot;arial narrow&quot;, sans-serif; font-size: 10pt;"> <b>C:</b> - </span> <span style="display: block; font-family: &quot;arial narrow&quot;, sans-serif; font-size: 10pt;"> <b>E:</b> <span class="Object" role="link" id="OBJ_PREFIX_DWT227_ZmEmailObjectHandler"><span class="Object" role="link" id="OBJ_PREFIX_DWT237_ZmEmailObjectHandler">app.vozdata@senamhi.gob.pe</span></span> </span> <span style="display: block; font-family: &quot;arial narrow&quot;, sans-serif; font-size: 10pt; line-height: 15pt;"> <b>W:</b> <span class="Object" role="link" id="OBJ_PREFIX_DWT228_com_zimbra_url"><span class="Object" role="link" id="OBJ_PREFIX_DWT238_com_zimbra_url"><a href="https://www.senamhi.gob.pe" target="_blank">www.senamhi.gob.pe</a></span></span> </span></div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" style="background-color: rgb(217, 217, 217); line-height: 10px;">&nbsp;</td>
                            </tr>
                            <tr>
                            </tr>
                            <tr>
                                <td colspan="3"><span style="font-size: 7pt; font-family: arial, sans-serif; color: dimgray; font-style: italic; margin: 6px; display: block;"> SENAMHI es una institución responsable con el medio ambiente. Le pedimos no imprimir este correo a menos que sea absolutamente necesario. Reduzca - Reuse - Recicle </span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>';
        return $html;
    }

    // getTablas


    public function getEstacion() {
        $array_detalle = json_decode(file_get_contents('php://input'), true);
        $id_usuario = $array_detalle['id_usuario'];
        $getEstacion = $this->movil->getEstacion($id_usuario);
        if ($getEstacion) {
            echo json_encode($getEstacion);
        } else {
            $mensaje = array("error" => "No se encontró estación");
            echo json_encode($mensaje);
        }
    }

    public function getHora() {
        $array_detalle = json_decode(file_get_contents('php://input'), true);
        $V_COD_TIPO = $array_detalle['V_COD_TIPO'];
        $getHora = $this->movil->getHora($V_COD_TIPO);
        if ($getHora) {
            echo json_encode($getHora);
        } else {
            $mensaje = array("error" => "No se encontró horas sinopticas");
            echo json_encode($mensaje);
        }
    }

    public function getParametro() {
        $array_detalle = json_decode(file_get_contents('php://input'), true);
        $V_COD_ESTA = $array_detalle['V_COD_ESTA'];
        $getParametro = $this->movil->getParametro($V_COD_ESTA);
        if ($getParametro) {
            echo json_encode($getParametro);
        } else {
            $mensaje = array("error" => "No se encontró parametros");
            echo json_encode($mensaje);
        }
    }

    public function getDeficit() {
        $array_detalle = json_decode(file_get_contents('php://input'), true);
        $getDeficit = $this->movil->getDeficit();
        if ($getDeficit) {
            echo json_encode($getDeficit);
        } else {
            $mensaje = array("error" => "No se encontró deficiencia de instrumentos");
            echo json_encode($mensaje);
        }
    }

    public function getNube() {
        $array_detalle = json_decode(file_get_contents('php://input'), true);
        $getNube = $this->movil->getNube();
        if ($getNube) {
            echo json_encode($getNube);
        } else {
            $mensaje = array("error" => "No se encontró datos de nube para mostrar");
            echo json_encode($mensaje);
        }
    }

    public function getViento() {
        $array_detalle = json_decode(file_get_contents('php://input'), true);
        $getViento = $this->movil->getViento();
        if ($getViento) {
            echo json_encode($getViento);
        } else {
            $mensaje = array("error" => "No se encontró datos de viento para mostrar");
            echo json_encode($mensaje);
        }
    }

    public function getUmbrales() {
        $array_detalle = json_decode(file_get_contents('php://input'), true);
        $getUmbrales = $this->movil->getUmbrales();
        if ($getUmbrales) {
            echo json_encode($getUmbrales);
        } else {
            $mensaje = array("error" => "No se encontró validaciones para mostrar");
            echo json_encode($mensaje);
        }
    }

    public function postAgregar() {
        $array_detalle = json_decode(file_get_contents('php://input'), true);
        $arrayData = array(
            ":PARAM1" => (string) $array_detalle['V_COD_ESTA'], // ID ESTACION
            ":PARAM2" => (string) $array_detalle['ID_HORA_SINOPTICA'], // ID HORA SINOPTICA
            ":PARAM3" => null, // FECHA_REGISTRO DE WEB
            ":PARAM4" => (string) $array_detalle['FECHA_MOVIL'], // FECHA MOVIL
            ":PARAM5" => (string) $array_detalle['ID_USUARIO'], // ID USUARIO
            ":PARAM6" => (string) (isset($array_detalle['NUM_LATITUD']) ? str_replace('.', ',', $array_detalle['NUM_LATITUD']) : null), // LATITUD
            ":PARAM7" => (string) (isset($array_detalle['NUM_LONGITUD']) ? str_replace('.', ',', $array_detalle['NUM_LONGITUD']) : null), // LONGITUD
            ":PARAM8" => (string) $array_detalle['FLG_CANAL'], // CANAL
            ":PARAM9" => (string) $array_detalle['FLG_MEDIO'], // MEDIO
            ":PARAM10" => null, // ID CABECERA
            ":PARAM11" => trim((string) $array_detalle['DETALLE'], '|'), // CADENA DETALLE
            ":PARAM12" => null, // CADENA LOG
        );
        $nomProcedure = 'SPC_INSERT_CABE_ING_MOV_WEB';
        $cadenaParam = ':PARAM1, :PARAM2, :PARAM3, :PARAM4, :PARAM5, :PARAM6, :PARAM7, :PARAM8, :PARAM9, :PARAM10, :PARAM11, :PARAM12,';
        log_message('error', print_r($arrayData, true));
        $result = $this->movil->ejecutarProcedure($nomProcedure, $cadenaParam, $arrayData);
        echo json_encode($result);
    }

//str_replace('.', ',', $array_detalle['NUM_LATITUD'])
}
