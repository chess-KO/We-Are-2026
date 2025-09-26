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
function crearCategoria() {
  mostrarNotificacion("success", " Categoría creada con éxito.");
}

function crearMundial() {
  mostrarNotificacion("primary", " Mundial registrado correctamente.");
}

function aprobarPublicacion(btn) {
  mostrarNotificacion("success", "Publicación aprobada.");
  btn.closest(".card-publicacion").remove(); 
}

function eliminarPublicacion(btn) {
  mostrarNotificacion("danger", " Publicación eliminada.");
  btn.closest(".card-publicacion").remove(); 
}

function eliminarComentario(btn) {
  mostrarNotificacion("warning", " Comentario eliminado.");
  btn.closest(".card-comentario").remove(); 
}

function cerrarSesion() {
  mostrarNotificacion("dark", " Sesión cerrada correctamente.");

    window.location.href = "index.html";
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