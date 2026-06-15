<?php
session_start();
require_once "../config/Database.class.php";
require_once "../models/usuario.class.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $database = new Database();
    $db = $database->connect();

    $usuario = new Usuario($db);
    $usuario->alias        = $_POST['user_name2'] ?? '';
    $usuario->pass         = $_POST['user_pass'] ?? '';

    //obtener la informacion del usuario que se intenta logear
    $row = $usuario->iniciarSesion();
    

    if ($row) {

        // Verificar contraseña encriptada
        

        if (password_verify($usuario->pass, $row['Pass'])) {
            // Guardar datos en sesión
            $_SESSION['usuario'] = $row['Alias'];
            $_SESSION['id_usuario'] = $row['Idusuario'];
            
            // Mantener sesión activa
            // PHP mantendrá la sesión abierta mientras no se cierre el navegador o expire el tiempo de sesión
            header("Location: ../index.php");
            exit();
        }
    }

    // Si falla login
    echo "<script>alert('Usuario o contraseña incorrectos');
          window.location.href='../login.php';</script>";
}

//---------------------

/*$admin_user = "Arturo";
$admin_pass = "Ayub23$";

// Recibir datos del formulario
$user = $_POST['user_name2'] ?? '';
$pass = $_POST['user_pass'] ?? '';

// Validar si es admin 
if ($user === $admin_user && $pass === $admin_pass) {
    $_SESSION['admin'] = true;
    $_SESSION['usuario'] = $admin_user;
    header("Location: ../Admin.html");

    exit();
}
else{
    echo "<script>
    alert('Usuario o contraseña incorrectos');
    window.location.href='../login.html';
</script>";
}

//  Validar si es usuario normal
require_once "conexion.php"; // archivo que conecta a la base de datos, aun no se hace pero creo que va así

// Prepara consulta 
$stmt = $conn->prepare("SELECT id, username, password FROM usuarios WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Verificar contraseña-> se encripta
    if (password_verify($pass, $row['password'])) {
        $_SESSION['usuario'] = $row['username'];
        $_SESSION['id_usuario'] = $row['id'];

        header("Location: ../usuario.php");
        exit();
    }
}

//mensaje cuando no se encuentra
echo "<script>
    alert('Usuario o contraseña incorrectos');
    window.location.href='../login.html';
</script>";
exit();
?>*/