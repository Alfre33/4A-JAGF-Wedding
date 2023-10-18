<!--=========== Breadcumd Section Here ========= -->

<?php
if (!isset($_SESSION["validarIngreso"])) {
    echo '<script>window.location="index.php?pagina=ingreso";</script>';
    return;
} else {
    if ($_SESSION["validarIngreso"] != "ok") {
        echo '<script>window.location="index.php?pagina=ingreso";</script>';
        return;
    }
}

?>
<section class="breadcumd__banner">
    <div class="container">
        <div class="breadcumd__wrapper center">
            <h1 class="left__content">
                blog single
            </h1>
            <ul class="right__content">
                <li>
                    <a href="index.html">
                        Home
                    </a>
                </li>
                <li>
                    <i class="fa-solid fa-chevron-right"></i>
                </li>
                <li>
                    blog
                </li>
            </ul>
        </div>
    </div>
</section>
<!--=========== Breadcumd Section Here ========= -->
<?php

if (isset($_GET["token"])) {
    $item = "token";
    $valor = $_GET["token"];

    $usuario = ControladorFormularios::ctrSeleccionarRegistros($item, $valor);
}
?>

<div class="d-flex justify-content-center text-center">
    <form class="p-5 bg-light" method="post">
        <h3>Actualizar Datos</h3>
        <br>
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                </div>
                <input type="text" class="form-control" value="<?php echo $usuario["nombre"]; ?>" placeholder="Actualiza Nombre" id="name" name="ActualizarNombre">
            </div>
        </div>
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                </div>
                <input type="email" class="form-control" value="<?php echo $usuario["email"]; ?>" placeholder="Actualiza tu email" id="email" name="updateEmail">
            </div>
        </div>
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                </div>
                <input type="password" class="form-control" placeholder="Actualiza tu Contraseña" id="pwd" name="updatePassword">

                <input type="hidden" name="passwordActual" value="<?php echo $usuario["password"]; ?>">
                <input type="hidden" name="tokenUsuario" value="<?php echo $usuario["token"]; ?>">

                <input type="hidden" name="nombreActual" value="<?php echo $usuario["nombre"]; ?>">
                <input type="hidden" name="emailActual" value="<?php echo $usuario["email"]; ?>">
            </div>
        </div>

        <?php


        $respuesta = ControladorFormularios::ctrActualizarRegistro();
        if ($respuesta == "ok") {
            echo '<script>
                if (window.history.replaceState){
                    window.history.replaceState(null, null, window.location.href);
                }
                </script>';

            echo '<div class="alert-success"> El usuario ha sido actualizado</div>
                <script>
                    setTimeout(function(){
                        window.location = "index.php?pagina=inicio";
                    }, 1600);
                </script>';
        }
        if ($respuesta == "error") {
            echo '<script>
            if (window.history.replaceState){
                window.history.replaceState(null, null, window.location.href);
            }
            </script>';

            echo '<div class="alert-danger"> ERROR!! no se pudo actualizar el usuario</div>';
        }

        ?>
        <br>
        <div class="d-flex justify-content-center text-center">
            <form class="p-5 bg-light" method="post">
                <!-- Tus campos de formulario aquí, puedes usar $usuario["name"], $usuario["email"], etc. -->
                <br>
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </form>
        </div>