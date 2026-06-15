function openNuevaPub() {
  const modal = document.getElementById("modalNuevaPubInner");
  if (!modal) return;

  modal.classList.add("show");

  // Recuperar el mundial seleccionado desde sessionStorage
  const mundial = JSON.parse(sessionStorage.getItem("mundialSeleccionado") || "{}");

  // Mostrar sede y año en el input readonly
  const inputMundial = modal.querySelector("#mundialInput");
  if (inputMundial && mundial.sede && mundial.anio) {
    inputMundial.value = `${mundial.sede} ${mundial.anio}`;
  }

  // Guardar ID en el input hidden
  const idHidden = modal.querySelector("#idmundial");
  if (idHidden) idHidden.value = mundial.id || "";
}


function closeNuevaPub() {
  document.getElementById("modalNuevaPubInner")?.classList.remove("show");
}

window.addEventListener("click", (e) => {
  const modal = document.getElementById("modalNuevaPubInner");
  if (e.target === modal) modal.classList.remove("show");
});

function mostrarNotificacion(tipo, mensaje) {
  const toastContainer = document.getElementById("toastContainer");

  
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


  const bsToast = new bootstrap.Toast(toast);
  bsToast.show();


  toast.addEventListener("hidden.bs.toast", () => {
    toast.remove();
  });
}

async function enviarPublicacion(e) {
  e.preventDefault();
  const form = e.target;

  const token = TOKEN;
  const formData = new FormData(form);
  
  if (usuarioSesion && usuarioSesion.Idusuario) {
    formData.append("idusuario", usuarioSesion.Idusuario);
  } else {
    console.warn("No hay usuario en sesión, no se puede crear la publicación.");
    mostrarNotificacion("danger", "Debes iniciar sesión para publicar.");
    return;
  }

  formData.append("accion", "crearPublicacion");

  for (let [key, value] of formData.entries()) {
    console.log(key, value);
  }

  try {
    const response = await fetch("api-rest/api.php", {
      method: "POST",
      headers: {
        "Authorization": "Bearer " + token
      },
      body: formData,
    });

    const data = await response.json();

    if (response.ok) {
      mostrarNotificacion("success", data.message || "Publicación creada con éxito.");
      form.reset();
      closeNuevaPub();
    } else {
      mostrarNotificacion("danger", data.message || "Error al crear publicación.");
    }
  } catch (err) {
    console.error("Error en el envío:", err);
    mostrarNotificacion("danger", "No se pudo conectar con la API.");
  }
}


document.addEventListener("DOMContentLoaded", () => {
  fetch("modal_Crear_publi.php")
    .then(res => res.text())
    .then(html => {
      document.getElementById("modalNuevaPub").innerHTML = html;

      // Llenar categorías desde el array PHP exportado
      const selectCategoria = document.querySelector("#modalNuevaPubInner select[name='categoria']");
      if (selectCategoria && typeof categoriasData !== "undefined") {
        categoriasData.forEach(cat => {
          const opt = document.createElement("option");
          opt.value = cat.Idcategoria;
          opt.textContent = cat.Nombre;
          selectCategoria.appendChild(opt);
        });
      }

      // ✅ Enlazamos el evento submit a la función enviarPublicacion()
      const form = document.querySelector("#modalNuevaPubInner form");
      if (form) {
        form.addEventListener("submit", enviarPublicacion);
      } else {
        console.error("Formulario no encontrado dentro del modal.");
      }
    })
    .catch(err => console.error("Error cargando el modal:", err));
});
