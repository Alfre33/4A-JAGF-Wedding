<?php
class ControladorFormularios
{
    /*
    REGISTRO
    */
    static public function crtRegistro()
    {
        if (isset($_POST["registerName"])) {
            /*return $_POST["registerName"] . "<br>" . $_POST["registerEmail"] . "<br>" .$_POST["registerPassword"] . "<br>";*/
            if (
                preg_match('/^[a-zA-ZáéíóúñÑ\s]+$/', $_POST["registerName"]) &&
                preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $_POST["registerEmail"]) &&
                preg_match('/^[0-9a-zA-Z]+$/', $_POST["registerPassword"])
            ) {

                $tabla = "registros";
                $token = md5($_POST["registerName"] . "+" . $_POST["registerEmail"]);

                //ENCRIPTAR CONTRASEÑA
                $EncriptarPassword=crypt($_POST["registerPassword"],'$2a$07$alfredogasparwedding4a$');


                $datos = array(
                    "token" => $token,
                    "nombre" => $_POST["registerName"],
                    "email" => $_POST["registerEmail"],
                    "password" => $EncriptarPassword
                );

                $respuesta = ModeloFormularios::mdlRegistro($tabla, $datos);
                return $respuesta;
            } else {

                $respuesta = "error";
                return $respuesta;
            }
        }
    }
    /**
     * Selecion de registros de la tabla
     */
    static public function ctrSeleccionarRegistros($item, $valor)
    {
        if ($item == null && $valor == null) {
            $tabla = "registros";

            $respuesta = ModeloFormularios::mdlSeleccionarRegistros($tabla, null, null);

            return $respuesta;
        } else {
            $tabla = "registros";

            $respuesta = ModeloFormularios::mdlSeleccionarRegistros($tabla, $item, $valor);

            return $respuesta;
        }
    }
    /**
     * Ingreso
     */
    public function ctrIngreso()
    {
        if (isset($_POST["ingresoEmail"])) {
            $tabla = "registros";
            $item = "email";
            $valor = $_POST["ingresoEmail"];

            $respuesta = ModeloFormularios::mdlSeleccionarRegistros($tabla, $item, $valor);

            $EncriptarPassword=crypt($_POST["ingresoPassword"],'$2a$07$alfredogasparwedding4a$');

            if (is_array($respuesta)) {
                if ($respuesta["email"] == $_POST["ingresoEmail"] && $respuesta["password"] == $EncriptarPassword) {
            //RECAPTCHA
                ModeloFormularios::mdlActualizarIntentosFallidos($tabla,0,$respuesta["token"]);

                    $_SESSION["validarIngreso"] = "ok";

                    echo '<div class="alert-success">¡Bienvenido! Ha iniciado sesión con éxito</div>';


                    echo '<script>
                        if (window.history.replaceState){
                            window.history.replaceState(null, null, window.location.href);
                        }
                        setTimeout(function(){
                            window.location.href = "index.php?pagina=inicio";
                        }, 2000); 
                    </script>';
                } else {
                    //RECAPTCHA
                    if($respuesta["intentos_fallidos"]<3){
                        $tabla="registros";
                    $intentos_fallidos=$respuesta["intentos_fallidos"]+1;
                
                    $ActualizarIntentosFallidos=ModeloFormularios::mdlActualizarIntentosFallidos
                    ($tabla,$intentos_fallidos,$respuesta["token"]);
                    //echo'<pre>';print_r($intentos_fallidos);echo'</pre>';
                    }else{
                        echo '<div class="alert alert-warning">RECAPTCHA!! Verifica que no eres robot</div>';
                    }


                    echo '<script>
                        if (window.history.replaceState){
                            window.history.replaceState(null, null, window.location.href);
                        }
                    </script>';
                    echo '<div class="alert alert-danger">Contraseña o email incorrecto</div>';
                }
            } else {
                echo '<script>
                    if (window.history.replaceState){
                        window.history.replaceState(null, null, window.location.href);
                    }
                </script>';
                echo '<div class="alert alert-danger">Error en el sistema ';
            }
        }
    }

    static public function ctrActualizarRegistro()
    {
        if (isset($_POST["ActualizarNombre"])) {
            if (
                preg_match('/^[a-zA-ZáéíóúñÑ\s]+$/',$_POST["ActualizarNombre"]) &&
                preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $_POST["updateEmail"])
            ) {
                $usuario = ModeloFormularios::mdlSeleccionarRegistros(
                    "registros",
                    "token",
                    $_POST["tokenUsuario"]
                );
                $CompararToken = md5($usuario["nombre"] . "+" . $usuario["email"]);
                if ($CompararToken == $_POST["tokenUsuario"]) {
                    if ($_POST["updatePassword"] != "") {
                        if(preg_match('/^[0-9a-zA-Z]+$/', $_POST["updatePassword"])){
                            $password =crypt($_POST["updatePassword"],'$2a$07$alfredogasparwedding4a$');;
                        }
                    } else {
                        $password = $_POST["passwordActual"];
                    }

                    //ACTAULIZAR TOKEN
                    if ($_POST["nombreActual"] != $_POST["ActualizarNombre"] || $_POST["emailActual"] != $_POST["updateEmail"]) {
                        $nuevoToken = md5($_POST["ActualizarNombre"] . "+" . $_POST["updateEmail"]);
                    } else {
                        $nuevoToken = null;
                    }

                    $tabla = "registros";

                    $datos = array(
                        "token" => $_POST["tokenUsuario"],
                        "nuevoToken" => $nuevoToken, //viene un dato si se cambio el nombre o email y si no viene vacio
                        "nombre" => $_POST["ActualizarNombre"],
                        "email" => $_POST["updateEmail"],
                        "password" => $password
                    );

                    $respuesta = ModeloFormularios::mdlActualizarRegistros($tabla, $datos);

                    return $respuesta;
                } else {
                    $respuesta = "error";
                    return  $respuesta;
                }
            }else{
                $respuesta = "error";
                return  $respuesta;
            }
        }
    }
    
    public function ctrEliminarRegistro()
    {
        if (isset($_POST["deleteRegistro"])) {

            $usuario = ModeloFormularios::mdlSeleccionarRegistros(
                "registros",
                "token",
                $_POST["deleteRegistro"]
            );

            $CompararToken = md5($usuario["nombre"] . "+" . $usuario["email"]);
            if ($CompararToken == $_POST["deleteRegistro"]) {
                $tabla = "registros";
                $valor = $_POST["deleteRegistro"];
                $respuesta = ModeloFormularios::mdlEliminarRegistro($tabla, $valor);

                if ($respuesta == "ok") {
                    echo '<script>
                    if (window.history.replaceState){
                        window.history.replaceState(null, null, window.location.href);
                    }
                    </script>';
                    echo '<div class="alert-success"> El usuario ha sido Eliminado</div>
                        <script>
                        setTimeout(function(){
                        window.location = "index.php?pagina=inicio";
                        },1500);
                        </script>';
                }
            }
        }
    }
}
