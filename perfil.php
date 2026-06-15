<?php
session_start();
// Si no hay sesión iniciada, redirigir al login
if (!isset($_SESSION['usuarioSesion'])) {
    header("Location: login.php");
    exit();
}

require_once "api-rest/middleware.php";
$usuarioT = TokenSimple::verificarSesion();
$token = $_SESSION['token'] ?? '';

$apiUrl = "http://localhost/PCI-BDM/api-rest/api.php";
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' =>
            "Authorization: Bearer $token\r\n" .
            "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query(['accion' => 'listarPublicacionesUsuario'])
    ]
]);
$response = @file_get_contents($apiUrl, false, $context);
$publicaciones = $response ? json_decode($response, true) : [];


// 🔥 Traer comentarios de publicaciones del usuario
$comapiUrl = "http://localhost/PCI-BDM/api-rest/api.php";
$contextComentarios = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' =>
            "Authorization: Bearer $token\r\n" .
            "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query(['accion' => 'listarComentariosAutor'])
    ]
]);

$responseComentarios = @file_get_contents($comapiUrl, false, $contextComentarios);
$comentariosUsuario = json_decode($responseComentarios, true);

// si falla json_decode, forzar array vacío
if (!is_array($comentariosUsuario)) {
    $comentariosUsuario = [];
}


$usuario = $_SESSION['usuarioSesion'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>perfil</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/perfil.css">

</head>

<body>
    <header>
        <div class="logo">
            <img src="img/2.png" alt="Logo Mundial" style="height:50px;">
        </div>
        <!--
        <div class="flex-grow-1 mx-4">
            <input type="text" class="form-control" placeholder="Buscar mundial, jugador, país...">
        </div>
        -->
        <div class="inicio">
            <a href="index.php"><i class="bi bi-house-fill"></i></a>
        </div>
    </header>
    <div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>
    <div class="container">
        <div class="perfil">

            <img 
            src="<?= $usuario['Foto']; ?>" 
            alt="Foto de perfil">


            <div class="perfil-info">
                <h2 id="n_usuario"><?= htmlspecialchars($usuario['Alias']); ?></h2>
                <p><strong>Nombre:</strong> <span id="nombre"><?= htmlspecialchars($usuario['Nombre']); ?></span></p>
                <p><strong>Apellido:</strong> <span id="apellido"><?= htmlspecialchars($usuario['Apellidos']); ?></span></p>
                <p><strong>Fecha de nacimiento:</strong> <span id="fecha"><?= htmlspecialchars($usuario['Fechanatal']); ?></span></p>
                <p><strong>Género:</strong> 
                    <span id="genero">
                        <?= ($usuario['Genero'] == 1) ? 'Masculino' : 'Femenino'; ?>
                    </span>
                </p>
                <p><strong>País:</strong> <span id="pais"><?= htmlspecialchars($usuario['Paisnatal']); ?></span></p>
                <p><strong>Nacionalidad:</strong> <span id="nacionalidad"><?= htmlspecialchars($usuario['Nacionalidad']); ?></span></p>
                <p><strong>Correo:</strong> <span id="correo"><?= htmlspecialchars($usuario['Email']); ?></span></p>
                <p><strong>Contraseña:</strong> ********</p>

                <div class="botones">
                    <button class="editar" onclick="abrirModalEditar()">Editar perfil</button>
                    <button class="stats" onclick="mostrarGraficas()">Ver estadísticas</button>
                    <button class="cerrar" onclick="abrirModalCerrar()">Cerrar sesión</button>
                </div>
            </div>
        </div>


        <section class="publicaciones">
            <h2>Mis Publicaciones</h2>


            <?php if (count($publicaciones) > 0): ?>
                <?php
                // Agrupar comentarios por Idpublicacion
                $comentariosPorPublicacion = [];
                foreach ($comentariosUsuario as $c) {
                    $comentariosPorPublicacion[$c['Idpublicacion']][] = $c;
                }
                    
                ?>
                <?php foreach ($publicaciones as $pub): ?>
                    <div class="publicacion border rounded p-3 mt-3" data-id="<?= $pub['Idpublicacion'] ?>">
                    <div class="pub-header d-flex justify-content-between align-items-center">
                        <div class="usuario d-flex align-items-center">
                        <div class="avatar rounded-circle me-2"
                            style="width:40px;height:40px;background-size:cover;
                                    background-image:url('<?= htmlspecialchars($pub['FotoUsuario'] ?? 'img/default.jpg') ?>');"></div>
                        <span class="nombre"><?= htmlspecialchars($pub['NombreUsuario'] ?? 'Yo') ?></span>
                        </div>
                        <div class="detalle">
                        <small>
                            Mundial: <?= htmlspecialchars($pub['Sede'] ?? '-') ?> <?= htmlspecialchars($pub['Año'] ?? '') ?> |
                            Categoría: <?= htmlspecialchars($pub['Categoria'] ?? '-') ?>
                        </small>
                        </div>
                    </div>

                    <h3 class="titulo"><?= htmlspecialchars($pub['Titulo'] ?? 'Sin título') ?></h3>
                    <h5 class="descripcion"><?= htmlspecialchars($pub['Descripcion'] ?? 'Sin descripcion') ?></h5>

                    <div class="media">
                        <?php if (!empty($pub['Ruta'])): ?>
                            <?php if (preg_match('/\.(mp4|webm|ogg)$/i', $pub['Ruta'])): ?>
                            <video src="<?= htmlspecialchars($pub['Ruta']) ?>" class="w-100 rounded" controls></video>
                            <?php else: ?>
                            <img src="<?= htmlspecialchars($pub['Ruta']) ?>" alt="Publicación" class="img-fluid rounded">
                            <?php endif; ?>
                        <?php elseif (!empty($pub['Archivo'])): ?>
                            <img src="<?= htmlspecialchars($pub['Archivo']) ?>" alt="Publicación" class="img-fluid rounded">
                        <?php endif; ?>
                    </div>

                    <div class="fechas">
                        <small>Elaboración: <?= htmlspecialchars($pub['Fecha'] ?? '-') ?></small>
                    </div>

                    <div class="interacciones">
                        <span class="like-btn" onclick="toggleLike(<?= $pub['Idpublicacion'] ?>)">
                        <i class="bi bi-hand-thumbs-up"></i>
                        <span class="like-count"><?= (int)$pub['Likes'] ?></span> likes
                        </span>
                        <span class="mx-3"><i class="bi bi-chat-dots"></i> <?= (int)$pub['Comentarios'] ?> comentarios</span>
                        <span><i class="bi bi-eye"></i> <?= (int)$pub['Vistas'] ?> vistas</span>
                    </div>

                    <!--
                    <div class="comentar">
                        <input type="text" placeholder="Escribe un comentario..." class="form-control mb-2">
                        <button class="btn btn-primary btn-sm" onclick="enviarComentario(this, <?= $pub['Idpublicacion'] ?>)">Comentar</button>
                    </div>
                    -->

                    <!--
                    <div class="lista-comentarios">

                        <?php 
                        $idPub = $pub['Idpublicacion'];
                        if (!empty($comentariosPorPublicacion[$idPub])): 
                            foreach ($comentariosPorPublicacion[$idPub] as $c): 
                        ?>

                        <div class="card-comentario">
                            <div class="usuario">
                                <div class="avatar"
                                    style="background-image:url('<?= $c['FotoUsuario'] ?>');
                                    width:35px;height:35px;background-size:cover;">
                                </div>
                                <span class="nombre"><?= htmlspecialchars($c['Alias']) ?></span>
                            </div>
                            <p><?= htmlspecialchars($c['Texto']) ?></p>

                            <div class="info">
                            <small><?= htmlspecialchars($c['Fecha']) ?></small>
                            </div>
                            
                        </div>

                        <?php endforeach; else: ?>

                            <small class="text">No hay comentarios.</small>

                        <?php endif; ?>

                    </div>
                        -->

                </div>

                <?php endforeach; ?>

                <?php else: ?>
                <p class="text-muted mt-3">Aún no tienes publicaciones.</p>

            <?php endif; ?>
                
        </section>


        <div id="modalStats" class="modal">
            <div class="modal-content modal-stats">
                <span class="close" onclick="closeStats()">&times;</span>
                <h2> Interacciones</h2>
                <div class="grafica">
                    <canvas id="interaccionesChart"></canvas>
                </div>
                <div class="usuarios-interaccion">
                    <h3>Usuarios que interactuaron</h3>
                    <ul>
                        <li>Pedro Gómez - <i class="bi bi-hand-thumbs-up"></i> Comentario</li>
                        <li>María López - <i class="bi bi-chat-dots"></i> Like</li>
                        <li>Ana Torres - <i class="bi bi-eye"></i> Vista</li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="modalEditar" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="cerrarModalEditar()">&times;</span>
                <h2>Editar perfil</h2>

                <form id="formEditar" enctype="multipart/form-data">
                    <input type="hidden" name="accion" value="modificarUsuario">
                    <input type="hidden" name="Idusuario" value="<?= $_SESSION['usuarioSesion']['Idusuario']; ?>">

                    <label for="nombreInput">Nombre:</label>
                    <input type="text" id="nombreInput" name="nombre">

                    <label for="apellidoInput">Apellido:</label>
                    <input type="text" id="apellidoInput" name="apellido">

                    <label for="nombreusuaInput">Nombre de usuario:</label>
                    <input type="text" id="nombreusuaInput" name="n_usuario">

                    <label for="fechaInput">Fecha de nacimiento:</label>
                    <input type="date" id="fechaInput" name="fecha_nac">

                    <label for="paisInput">País:</label>
                    <input type="text" id="paisInput" name="pais">

                    <label for="nacionalidadInput">Nacionalidad:</label>
                    <input type="text" id="nacionalidadInput" name="nacionalidad">

                    <!-- 🔥 CORREO (usuario + dominio) -->
                    <label>Correo:</label>
                    <div class="d-flex gap-2">

                        <!-- parte usuario -->
                        <input 
                            type="text" 
                            id="correoInputUser" 
                            class="form-control"
                            placeholder="tu.correo"
                            required
                        >

                        <!-- dominio -->
                        <select id="correoInputDomain" class="select-override" required>
                            <option value="@gmail.com">@gmail.com</option>
                            <option value="@hotmail.com">@hotmail.com</option>
                            <option value="@outlook.com">@outlook.com</option>
                            <option value="@yahoo.com">@yahoo.com</option>
                        </select>

                    </div>

                    <!-- correo REAL -->
                    <input type="hidden" id="correoRealEdit" name="correo">

                    <label for="passInput">Contraseña:</label>
                    <input type="password" id="passInput" name="pass">

                    <div class="radio-group">
                        <label><input type="radio" name="gene" value="Masculino"> Masculino</label>
                        <label><input type="radio" name="gene" value="Femenino"> Femenino</label>
                    </div>

                    <label for="image_user" class="custom-file-upload">
                        Seleccionar imagen
                    </label>
                    <input type="file" id="image_user" name="image" accept="image/*" hidden>

                    <button type="submit">Guardar cambios</button>
                </form>
            </div>
        </div>


    <div id="modalCerrar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalCerrar()">&times;</span>
            <h2>¿Seguro que quieres cerrar sesión?</h2>
            <button onclick="confirmarCerrar()">Sí, cerrar</button>
            <button onclick="cerrarModalCerrar()">Cancelar</button>
        </div>
    </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="js/perfil.js"></script>
    <script> 
      
        // Token JWT guardado en sesión PHP → lo pasamos a JavaScript
        const TOKEN = "<?= $_SESSION['token'] ?? '' ?>";

        // También lo guardamos en localStorage para el resto de la página
        if (TOKEN) {
            localStorage.setItem("token", TOKEN);
        } else {
            console.warn("⚠ No hay token en la sesión PHP");
        }

    </script>
    
    <script>
        document.getElementById("formEditar").addEventListener("submit", async function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            //  Token almacenado en sesión PHP, lo inyectamos aquí desde PHP
            const token = "<?= $_SESSION['token'] ?? '' ?>";

            try {
                const response = await fetch("api-rest/api.php", {
                    method: "POST",
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    body: formData
                });

                console.log("Status:", response.status);
                const text = await response.text();
                console.log("Respuesta cruda:", text);

                let data;
                try {
                data = JSON.parse(text);
                } catch {
                alert("Error: el servidor devolvió una respuesta no válida (ver consola).");
                return;
                }

                if (response.ok && data.success) {
                alert(data.message);
                window.location.reload();
                } else {
                alert(data.message || "Error al actualizar el usuario");
                }

            } catch (error) {
                console.error("Error al enviar:", error);
                alert("Error de conexión con el servidor");
            }
        });
        document.addEventListener('DOMContentLoaded', function () {
            const sel = document.getElementById('correoInputDomain');
            if (!sel) return;
            sel.classList.add('select-override');

            // estilo inline como última defensa
            Object.assign(sel.style, {
                backgroundColor: '#1a1a1aff',
                color: '#686767ff',
                border: '2px solid #hwb(168 0% 0%)',
                borderRadius: '12px',
                padding: '8px 12px',
                fontWeight: '700',
                appearance: 'none',
                WebkitAppearance: 'none',
                MozAppearance: 'none',
                backgroundImage: 'none'
            });
        });
    </script>

</body>

</html>