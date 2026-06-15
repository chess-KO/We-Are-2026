<?php
session_start();
require_once "api-rest/middleware.php";

// 🔐 Verifica que sea admin
$usuario = TokenSimple::verificarSesion(true);

// ✅ URL de la API
$apiUrlPendientes = "http://localhost/PCI-BDM/api-rest/api.php";

// ✅ Token del admin
$token = $_SESSION['token'] ?? '';

// ✅ Crear contexto con cabecera Authorization
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Authorization: Bearer $token\r\n" .
                    "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query(['accion' => 'listarPublicacionesPendientes'])
    ]
]);

// ✅ Ejecutar petición
$responsePendientes = @file_get_contents($apiUrlPendientes, false, $context);

// ✅ Manejo de errores
if ($responsePendientes === false) {
    $publicacionesPendientes = [];
} else {
    $publicacionesPendientes = json_decode($responsePendientes, true) ?? [];
}
//Traer las categorías
$apiUrlCategorias =  "http://localhost/PCI-BDM/api-rest/api.php";


$content = http_build_query(['accion' => 'listarPanelAdmin']);
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Authorization: Bearer $token\r\n" .
                    "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => $content
    ]
]);

$response = @file_get_contents($apiUrlPendientes, false, $context);
$data = json_decode($response, true);

$publicacionesPendientes = $data['publicaciones'] ?? [];
$categorias = $data['categorias'] ?? [];
// Debug temporal
 file_put_contents("debug_admin_pendientes.txt", $responsePendientes);
?>



<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administrador</title>
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="css/Admin.css">
</head>

<body>

  <!-- HEADER -->
  <header class="d-flex justify-content-between align-items-center p-3 bg-dark text-white">
    <div class="logo">
      <img src="img/2.png" alt="Logo Mundial" style="height:50px;">
    </div>
<!--
    <div class="flex-grow-1 mx-4">
      <input type="text" class="form-control buscador" placeholder="Buscar mundial, jugador, país...">
    </div>
-->
    <div class="inicio">
      <a href="index.php" class="text-white"><i class="bi bi-house-fill fs-3"></i></a>
    </div>
  </header>

  <!-- MENSAJES Y TOAST -->
  <div class="container mt-3" id="mensajes"></div>
  <div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer" style="z-index:2000;"></div>

  <!-- GRID PRINCIPAL -->
  <main class="admin-grid container-fluid">

    <!-- INFO DEL ADMINISTRADOR-->
    <aside class="sidebar p-3 rounded shadow-sm">
      <img src="https://upload.wikimedia.org/wikipedia/commons/c/c7/Arturo-elias-ayub_%28cropped%29.jpg"
        alt="Foto de perfil" class="perfil rounded-circle mb-3" style="width:100px;height:100px;">
      <h5 class="fw-bold">Arturo Ayub</h5>
      <p><i class="bi bi-envelope-fill"></i> Correo: <span class="fw-semibold">AyubAE@gmail.com</span></p>
      <p><i class="bi bi-key-fill"></i> Contraseña: ********</p>
      <button class="btn btn-outline-danger w-100 mt-2" onclick="cerrarSesion()">
        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
      </button>
    </aside>

    <section class="contenido">
      <h1 class="mb-4">Panel de Administración</h1>

    
      <!-- Crear Categoría -->
      <div class="card p-3 mb-4 shadow-sm">
        <h4><i class="bi bi-tags-fill text-primary"></i> Crear Categoría</h4>

        <form action="api-rest/api.php" id= "formCrearCategoria" method="POST" class="mt-2 d-flex gap-2">
          <!-- Acción oculta para que la API sepa qué hacer -->
          <input type="hidden" name="accion" value="crearCategoria">

          <!-- Campo de texto para el nombre de la categoría -->
          <input type="text" name="nombre" class="form-control" placeholder="Ejemplo: Jugadas, Goles, Curiosidades" required>

          <!-- Botón para enviar el formulario -->
          <button type="submit" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Agregar
          </button>
        </form>
      </div>


      <!-- Crear Mundial -->
      <div class="card p-3 mb-4 shadow-sm">
        <h4><i class="bi bi-globe2 text-danger"></i> Crear página de Mundial</h4>
        <form action="api-rest/api.php" id="formCrearMundial" method = post enctype="multipart/form-data">
          <input type="hidden" name="accion" value="crearMundial">

          <div class="mb-2">
            <label class="form-label">Año del Mundial</label>
            <input type="text" id="anio" name="anio" class="form-control" placeholder="Ejemplo: 2022">
          </div>
          <div class="mb-2">
            <label class="form-label">Sede</label>
            <textarea class="form-control" id="sede" name="sede" rows="2" placeholder="Escribe la sede del mundial"></textarea>
          </div>
          <div class="mb-2">
            <label class="form-label">Descripción breve</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="2" placeholder="Escribe un resumen del mundial..."></textarea>
          </div>
          <div class="mb-2">
            <label class="form-label">Imagen destacada</label>
            <input type="file" id="imagen_destacada" name="imagen_destacada" class="form-control" accept=".png, .jpg, .jpeg">
          </div>
          <div class="mb-2">
            <label class="form-label">Logo del mundial</label>
            <input type="file" id="logo" name="logo" class="form-control" accept=".png, .jpg, .jpeg">
          </div>
          <div class="mb-2">
            <label class="form-label">Mascota del mundial</label>
            <input type="text" id="mascota" name="mascota" class="form-control">
          </div>
          <div class="mb-2">
            <label class="form-label">Campeón</label>
            <textarea class="form-control" id="campeon" name="campeon" rows="2" placeholder="¿Quién ganó la copa?"></textarea>
          </div>
             <div class="mb-2">
            <label class="form-label">Sub Campeón</label>
            <textarea class="form-control" id="subcampeon" name="subcampeon" rows="2" placeholder="Segundo lugar"></textarea>
          </div>
          <div class="mb-2">
            <label class="form-label">Marcador Final</label>
            <textarea class="form-control" id="marcador_final" name="marcador_final" rows="2" placeholder="Escribe el resultado de la Final"></textarea>
          </div>
           <div class="mb-2">
            <label class="form-label">Final</label>
            <textarea class="form-control" id="final" name="final" rows="2" placeholder="¿Cómo terminó el partido?"></textarea>
          </div>
            <div class="mb-2">
            <label class="form-label">Líder de Goleo</label>
            <textarea class="form-control" id="lider_goleo" name="lider_goleo" rows="2" placeholder="¿Quién metió más goles?"></textarea>
          </div>
           <div class="mb-2">
            <label class="form-label">3er lugar</label>
            <textarea class="form-control" id="lugar_3" name="lugar_3" rows="2" placeholder="Equipo que quedó en tercer lugar"></textarea>
          </div>
           <div class="mb-2">
            <label class="form-label">4to lugar</label>
            <textarea class="form-control" id="lugar_4" name="lugar_4" rows="2" placeholder="Equipo que quedó en cuarto lugar"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">
            Crear Mundial
          </button>
        </form>
      </div>

      <!-- Publicaciones pendientes -->
      <div class="card p-3 mb-4 shadow-sm">
        <h4><i class="bi bi-newspaper text-success"></i> Publicaciones pendientes</h4>

        <?php if (!empty($publicacionesPendientes)): ?>
            <?php foreach ($publicacionesPendientes as $pub): ?>
                <div class="card-publicacion p-3 border rounded mt-3">
                    <div class="pub-header d-flex justify-content-between">
                        <div class="usuario d-flex align-items-center">
                            <div class="avatar rounded-circle me-2"
                                style="width:40px;height:40px;background-size:cover;
                                background-image:url('<?= htmlspecialchars($pub['FotoUsuario'] ?? 'img/default.jpg') ?>');">
                            </div>
                            <span class="fw-semibold"><?= htmlspecialchars($pub['NombreUsuario'] ?? 'Usuario desconocido') ?></span>
                        </div>
                        <div class="detalle text-muted">
                            <small>
                                Mundial: <?= htmlspecialchars($pub['Sede'] ?? 'Desconocido') ?> <?= htmlspecialchars($pub['Año'] ?? '') ?> | 
                                Categoría: <?= htmlspecialchars($pub['Categoria'] ?? 'Sin categoría') ?>
                            </small>
                        </div>
                    </div>

                    <h3 class="titulo"><?= htmlspecialchars($pub['Titulo'] ?? 'Sin título') ?></h3>

                    <h5 class="mt-2"><?= htmlspecialchars($pub['Descripcion'] ?? 'Sin descripcion') ?></h5>

                    <div class="media my-2">
                      <?php if (!empty($pub['Archivo'])): ?>
                        <img src="<?= $pub['Archivo'] ?>" alt="Publicación" class="img-fluid rounded">
                      <?php elseif (!empty($pub['Ruta'])): ?>
                        <video controls>
                          <source src="<?= $pub['Ruta'] ?>" type="video/mp4">
                        </video>
                      <?php endif; ?>
                    </div>

                    <div class="fechas text-muted mb-2">
                        <small>Elaboración: <?= htmlspecialchars($pub['Fecha'] ?? '-') ?></small>
                    </div>

                    <div class="acciones">
                        <button class="btn btn-success btn-sm" onclick="aprobarPublicacion(<?= (int)$pub['Idpublicacion'] ?>)">
                            <i class="bi bi-check2-circle"></i> Aprobar
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarPublicacion(<?= (int)$pub['Idpublicacion'] ?>)">
                            <i class="bi bi-trash3"></i> Eliminar
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted mt-3">No hay publicaciones pendientes por aprobar.</p>
        <?php endif; ?>
      </div>


    </section>

    <!-- CATEGORÍAS -->
    <aside class="categorias-box p-3 rounded shadow-sm">
            <h4><i class="bi bi-tags-fill"></i> Categorías</h4>
            <ul id="listaCategorias">
        <?php if (!empty($categorias)): ?>
          <?php foreach ($categorias as $cat): ?>
            <li>
              <span class="categoria-nombre"><?= htmlspecialchars($cat['Nombre']) ?></span>
              <div class="acciones">
                <button class="btn-editar" data-id="<?= (int)$cat['Idcategoria'] ?>">
                  <i class="bi bi-pencil-fill"></i>
                </button>
                <button class="btn-eliminar" data-id="<?= (int)$cat['Idcategoria'] ?>">
                  <i class="bi bi-trash-fill"></i>
                </button>
              </div>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <li class="text-muted">No hay categorías registradas.</li>
        <?php endif; ?>
      </ul>

    </aside>

    <!-- CONTENIDO PRINCIPAL -->

  </main>

  <!-- JS -->
  <script>
  const TOKEN_ADMIN = <?= json_encode($_SESSION['token'] ?? '') ?>;
  </script>

  <script>
    document.getElementById("formCrearCategoria").addEventListener("submit", async (e) => {
    e.preventDefault();

      const token = TOKEN_ADMIN;
      const formData = new FormData(e.target);

      try {
        const res = await fetch("api-rest/api.php", {
          method: "POST",
          headers: {
            "Authorization": "Bearer " + token
          },
          body: formData
        });


        const text = await res.text(); 
        let data;

        try {
          data = JSON.parse(text);
        } catch {

          alert("Error inesperado:\n" + text);
          return;
        }

      
        alert(data.message || "Operación completada.");

        
        if (res.ok && data.message.includes("exitosamente")) {
          window.location.reload();
        }

      } catch (error) {
        alert("Error al conectar con el servidor:\n" + error.message);
      }
    });
  document.getElementById("formCrearMundial").addEventListener("submit", async (e) => {
    e.preventDefault();

    const token = TOKEN_ADMIN;
    const formData = new FormData(e.target);

    try {
      const res = await fetch("api-rest/api.php", {
        method: "POST",
        headers: {
          "Authorization": "Bearer " + token
        },
        body: formData
      });

      
      const text = await res.text(); 
      let data;

      try {
        data = JSON.parse(text);
      } catch {
        
        alert("Error inesperado:\n" + text);
        return;
      }

      alert(data.message || "Operación completada.");


      if (res.ok && data.message.includes("exitosamente")) {
        window.location.reload();
      }

    } catch (error) {
      alert("Error al conectar con el servidor:\n" + error.message);
    }
  });
  </script>

    <script src="js/Admin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
      const lista = document.getElementById('listaCategorias');
      const token = TOKEN_ADMIN; // usa el mismo token que para publicaciones

      lista.addEventListener('click', async (e) => {
        const btnEditar = e.target.closest('.btn-editar');
        const btnEliminar = e.target.closest('.btn-eliminar');

        // === EDITAR ===
        if (btnEditar) {
          console.log("Click en botón editar");
          const id = btnEditar.dataset.id;
          const li = btnEditar.closest('li');
          const nombreActual = li.querySelector('.categoria-nombre')?.textContent?.trim() || "";
          const nuevoNombre = prompt("Nuevo nombre para la categoría:", nombreActual);
          if (!nuevoNombre) return;

          const formData = new FormData();
          formData.append("accion", "actualizarCategoria");
          formData.append("Idcategoria", id);
          formData.append("Nombre", nuevoNombre);
          formData.append("Activo", 1);

          const res = await fetch("api-rest/api.php", {
            method: "POST",
            headers: {
              "Authorization": "Bearer " + token
            },
            body: formData
          });

          const text = await res.text();
          console.log("Respuesta editar:", text);
          try {
            const data = JSON.parse(text);
            alert(data.message || "Operación completada.");
            if (data.success) location.reload();
          } catch {
            alert("Error inesperado:\n" + text);
          }
        }

        // === DESACTIVAR ===
        if (btnEliminar) {
          console.log(" Click en botón eliminar");
          const id = btnEliminar.dataset.id;
          if (!confirm("¿Seguro que deseas desactivar esta categoría?")) return;

          const formData = new FormData();
          formData.append("accion", "actualizarCategoria");
          formData.append("Idcategoria", id);
          formData.append("Nombre", "");
          formData.append("Activo", 0);

          const res = await fetch("api-rest/api.php", {
            method: "POST",
            headers: {
              "Authorization": "Bearer " + token
            },
            body: formData
          });

          const text = await res.text();
          console.log("Respuesta eliminar:", text);
          try {
            const data = JSON.parse(text);
            alert(data.message || "Operación completada.");
            if (data.success) location.reload();
          } catch {
            alert("Error inesperado:\n" + text);
          }
        }
      });
  });

  </script>
</body>
</html>
