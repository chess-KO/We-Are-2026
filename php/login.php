<?php
session_start();


$admin_user = "Arturo";
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
?>