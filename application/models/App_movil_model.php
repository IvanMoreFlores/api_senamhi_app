
<?php

class App_movil_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getUserInfo($usuario) {
        $sql = "SELECT * FROM VIEW_MOVIL_LOGIN WHERE USUARIO = UPPER(?)";
        $result = $this->db->query($sql, array($usuario));
        if ($result->num_rows() > 0) {
            return $result->row_array();
        } else {
            return null;
        }
    }

    public function updatePassword($new_password, $old_password, $id_usuario) {
        $this->db->query("UPDATE SIMENH_USUARIO SET PASS='$new_password',OLD_PASS='$old_password',FLG_NUEVO='1' WHERE ID_USUARIO='$id_usuario'");
        if ($this->db->affected_rows() >= 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getCountEmailToken($email) {
        $sql = "  SELECT COUNT(*) CANTIDAD
                    FROM SIMENH_TOKENS
                   WHERE USER_EMAIL = ? ";

        $result = $this->db->query($sql, array($email));
        if ($result->row()->CANTIDAD) {
            return $result->row()->CANTIDAD;
        } else {
            return null;
        }
    }

    public function deleteEmailToken($email) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $this->db->trans_begin();
            $this->db->where('USER_EMAIL', $email);
            $this->db->delete('SIMENH_TOKENS');

            if ($this->db->trans_status() === true) {
                $this->db->trans_commit();
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se elimin&oacute; correctamente en la tabla SIMENH_TOKENS!!.';
            } else {
                $this->db->trans_rollback();
                throw new Exception('Error transacci&oacute;n DELETE SIMENH_TOKENS');
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }

        return $data;
    }

    public function insertTokenUsuario($arrayInsert) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $this->db->trans_begin();
            $this->db->set('FECHA_REGISTRO', "SYSDATE", FALSE);
            $this->db->insert('SIMENH_TOKENS', $arrayInsert);
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                throw new Exception('Error al insertar en la tabla SIMENH_TOKENS');
            } else {
                $this->db->trans_commit();
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se insert&oacute; correctamente!!';
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    public function getCountEmail($email) {
        $sql = "  SELECT COUNT(*) CANTIDAD
                    FROM SIMENH_USUARIO U
                   WHERE U.EMAIL = ? ";
        $result = $this->db->query($sql, array($email));
        if ($result->row()->CANTIDAD) {
            return $result->row()->CANTIDAD;
        } else {
            return null;
        }
    }

    // getTablas


    public function getEstacion($id_usuario) {
        $sql = "SELECT * FROM VIEW_MOVIL_ESTACION_X_USUARIO WHERE ID_USUARIO = UPPER(?)";
        $result = $this->db->query($sql, array($id_usuario));
        if ($result->num_rows() > 0) {
            return $result->result_array();
        } else {
            return null;
        }
    }

    public function getHora($V_COD_TIPO) {
        $sql = "SELECT * FROM SIMENH_HORA_SINOPTICA WHERE COD_TIPO_ESTA=UPPER(?) ORDER BY COD_DET_SH ASC";
        $result = $this->db->query($sql, array($V_COD_TIPO));
        if ($result->num_rows() > 0) {
            return $result->result_array();
        } else {
            return null;
        }
    }

    public function getParametro($V_COD_ESTA) {
        $sql = "SELECT	* FROM	VIEW_MOVIL_PARAMETROS WHERE V_COD_ESTA = ?";
        $result = $this->db->query($sql, array($V_COD_ESTA));
        if ($result->num_rows() > 0) {
            return $result->result_array();
        } else {
            return null;
        }
    }

    public function getDeficit() {
        $sql = "SELECT	* FROM	VIEW_MOVIL_DEFICINSTRU";
        $result = $this->db->query($sql, array());
        if ($result->num_rows() > 0) {
            return $result->result_array();
        } else {
            return null;
        }
    }

    public function getNube() {
        $sql = "SELECT	* FROM	VIEW_MOVIL_TIPO_NUBE_X_PARAM";
        $result = $this->db->query($sql, array());
        if ($result->num_rows() > 0) {
            return $result->result_array();
        } else {
            return null;
        }
    }

    public function getViento() {
        $sql = "SELECT	* FROM	VIEW_MOVIL_DIRECC_VIENTO";
        $result = $this->db->query($sql, array());
        if ($result->num_rows() > 0) {
            return $result->result_array();
        } else {
            return null;
        }
    }

    public function getUmbrales() {
        $sql = "SELECT	* FROM	VIEW_MOVIL_UMBRALES_PARAM";
        $result = $this->db->query($sql, array());
        if ($result->num_rows() > 0) {
            return $result->result_array();
        } else {
            return null;
        }
    }

    ///////  MOVIL  ///////

    public function getPasswordActualByIdUsuario($idUsuario) {
        $sql = "  SELECT U.PASS
                    FROM SIMENH_USUARIO U
                   WHERE ID_USUARIO = ? ";

        $result = $this->db->query($sql, array($idUsuario));
        if ($result->row() != null) {
            return $result->row()->PASS;
        } else {
            return null;
        }
    }

    public function getDataToken($token) {
        $sql = "  SELECT ID_TOKEN,
                         TXT_TOKEN,
                         USER_EMAIL,
                         (TO_CHAR(FECHA_REGISTRO,'YYYY-MM-DD')) FECHA_REGISTRO,
                         (TO_CHAR((SELECT SYSDATE FROM dual),'YYYY-MM-DD')) FECHA_ACTUAL
                    FROM SIMENH_TOKENS
                   WHERE TXT_TOKEN = ? ";

        $result = $this->db->query($sql, array($token));
        if ($result->row() != null) {
            return $result->row_array();
        } else {
            return null;
        }
    }

    public function getIdUsuarioByCorreo($correoUsuario) {
        $sql = "  SELECT U.ID_USUARIO
                    FROM SIMENH_USUARIO U
                   WHERE U.EMAIL = ? ";

        $result = $this->db->query($sql, array($correoUsuario));
        if ($result->row() != null) {
            return $result->row()->ID_USUARIO;
        } else {
            return null;
        }
    }

    public function ejecutarProcedure($nomProcedure, $cadenaPara, $arrayData) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $arrayCrede = $this->getCredencialesBD();
            $stringProcedure = "begin PKG_VOZYDATA." . $nomProcedure . "(" . $cadenaPara . " :P_VALOR_RETORNA_CURSOR); end;";
            $conn = oci_connect($arrayCrede['user_name'], $arrayCrede['password'], $arrayCrede['db']);
            if (!$conn) {
                throw new Exception('Hubo un error al conectar la bd');
            }
            $curs = oci_new_cursor($conn);
            $stid = oci_parse($conn, $stringProcedure);

            foreach ($arrayData as $clave => $valor) {
                oci_bind_by_name($stid, $clave, $arrayData[$clave]);
            }

            oci_bind_by_name($stid, ":P_VALOR_RETORNA_CURSOR", $curs, -1, OCI_B_CURSOR);

            oci_execute($stid);
            oci_execute($curs);
            while (($row = oci_fetch_array($curs, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
                if (count($row) > 0) {
                    $data['error'] = EXIT_SUCCESS;
                    $data['respuesta'] = $row;
                }
            }

            oci_free_statement($stid);
            oci_free_statement($curs);
            oci_close($conn);
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }

        return $data;
    }

    public function getCredencialesBD() {
        $arrayCredenciales = array(
            "con_string" => 'localhost/xe',
            "user_name" => 'SC_VOZYDATA',
            "password" => 'Sc_v0zydAta',
            "db" => '(DESCRIPTION =
                        (ADDRESS = (PROTOCOL = TCP)(HOST = 172.25.0.188)(PORT = 1521))
                        (CONNECT_DATA =
                            (SERVER = DEDICATED)
                            (SID = DESAOTI )
                        )
                      )',
            "user_name" => 'SC_VOZYDATA'
        );
        return $arrayCredenciales;
    }

}
