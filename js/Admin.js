function mostrarNotificacion(tipo, mensaje) {
  const toastContainer = document.getElementById("toastContainer");

 // así creo las notis
  const toast = document.createElement("div");
  toast.className = `toast align-items-center text-bg-${tipo} border-0 mb-2`;
  toast.role = "alert";
  toast.innerHTML = `
    <div class="d-flex">
      <div class="toast-body">
        ${mensaje}
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  `;

  toastContainer.appendChild(toast);

  // Inicializar toast de Bootstrap
  const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
  bsToast.show();

  // Eliminar del DOM cuando se cierre para las publis y comentarios
  toast.addEventListener("hidden.bs.toast", () => toast.remove());
}

// ==== FUNCIONES DE ACCIONES ====
/*async function crearCategoria() {
  const nombreCategoria = document.querySelector('.input-group input').value.trim();

  if (!nombreCategoria) {
    alert("Por favor, ingresa un nombre de categoría.");
    return;
  }

  const formData = new FormData();
  formData.append('accion', 'crearCategoria');
  formData.append('nombre', nombreCategoria);

  try {
    const response = await fetch('../api-rest/api.php', {
      method: 'POST',
      body: formData
    });

    let data;
    try {
      data = await response.json();
    
    } catch (err) {
        console.error("La respuesta no contiene JSON válido.");
        data = { message: "Error: la API no devolvió JSON." };
      }

      if (response.ok) {
        alert(data.message || "Categoría creada correctamente");
      } else {
        alert(data.message || "Error al crear la categoría");
      }

  } catch (error) {
    console.error("Error en la solicitud:", error);
    alert("No se pudo conectar con el servidor");
  }

}*/

function crearMundial() {
  mostrarNotificacion("primary", " Mundial registrado correctamente.");
}

async function aprobarPublicacion(idPublicacion) {
  const token = TOKEN_ADMIN; // se inyecta desde PHP
  const formData = new FormData();
  formData.append("accion", "actualizarEstadoPublicacion");
  formData.append("Idpublicacion", idPublicacion);
  formData.append("NuevoEstado", 1); // 1 = Aprobada

  try {
    const response = await fetch("api-rest/api.php", {
      method: "POST",
      headers: {
        "Authorization": "Bearer " + token
      },
      body: formData
    });

    const data = await response.json();
    console.log(data);

    if (response.ok) {
      mostrarNotificacion("success", "✅ Publicación aprobada.");
      document.querySelector(`button[onclick="aprobarPublicacion(${idPublicacion})"]`)
        ?.closest(".card-publicacion")
        ?.remove();
    } else {
      mostrarNotificacion("danger", data.message || "Error al aprobar publicación.");
    }
  } catch (error) {
    console.error("Error:", error);
    mostrarNotificacion("danger", "Error de conexión con el servidor.");
  }
}

async function eliminarPublicacion(idPublicacion) {
  const token = TOKEN_ADMIN; // se inyecta desde PHP
  const formData = new FormData();
  formData.append("accion", "actualizarEstadoPublicacion");
  formData.append("Idpublicacion", idPublicacion);
  formData.append("NuevoEstado", 2); // 2 = Eliminada/Rechazada

  try {
    const response = await fetch("api-rest/api.php", {
      method: "POST",
      headers: {
        "Authorization": "Bearer " + token
      },
      body: formData
    });

    const data = await response.json();
    console.log(data);

    if (response.ok) {
      mostrarNotificacion("danger", "🗑️ Publicación eliminada.");
      document.querySelector(`button[onclick="eliminarPublicacion(${idPublicacion})"]`)
        ?.closest(".card-publicacion")
        ?.remove();
    } else {
      mostrarNotificacion("warning", data.message || "No se pudo eliminar la publicación.");
    }
  } catch (error) {
    console.error("Error:", error);
    mostrarNotificacion("danger", "Error de conexión con el servidor.");
  }
}


function eliminarComentario(btn) {
  mostrarNotificacion("warning", " Comentario eliminado.");
  btn.closest(".card-comentario").remove(); 
}

function cerrarSesion() {
  mostrarNotificacion("dark", " Sesión cerrada correctamente.");

  window.location.href = "php/logout.php";
}

const listaCategorias = document.getElementById("listaCategorias");

// Mostrar mensajes (toast simple)
function mostrarMensaje(texto, tipo = "success") {
  const mensajes = document.getElementById("mensajes");
  const alert = document.createElement("div");
  alert.className = `alert alert-${tipo} mt-2`;
  alert.innerText = texto;
  mensajes.appendChild(alert);
  setTimeout(() => alert.remove(), 2500);
}

// Editar categoría
listaCategorias.addEventListener("click", (e) => {
  if (e.target.closest(".btn-editar")) {
    const li = e.target.closest("li");
    const span = li.querySelector(".categoria-nombre");
    const nombreActual = span.innerText;

    // Convertir en input
    const input = document.createElement("input");
    input.type = "text";
    input.value = nombreActual;
    li.replaceChild(input, span);
    input.focus();

    input.addEventListener("keypress", (ev) => {
      if (ev.key === "Enter") {
        const nuevoNombre = input.value.trim();
        if (nuevoNombre) {
          const nuevoSpan = document.createElement("span");
          nuevoSpan.classList.add("categoria-nombre");
          nuevoSpan.innerText = nuevoNombre;
          li.replaceChild(nuevoSpan, input);
          mostrarMensaje(` Categoría editada a "${nuevoNombre}"`);
        }
      }
    });
  }

  // Eliminar categoría
  if (e.target.closest(".btn-eliminar")) {
    const li = e.target.closest("li");
    const nombre = li.querySelector(".categoria-nombre").innerText;
    li.remove();
    mostrarMensaje(` Categoría "${nombre}" eliminada`, "danger");
  }
});