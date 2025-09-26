function openNuevaPub() {
  document.getElementById("modalNuevaPubInner")?.classList.add("show");
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

document.addEventListener("DOMContentLoaded", () => {
  fetch("modal_Crear_publi.html")
    .then(res => res.text())
    .then(html => {
      document.getElementById("modalNuevaPub").innerHTML = html;

      
      const form = document.querySelector("#modalNuevaPubInner form");
      if (!form) return;
      form.addEventListener("submit", (e) => {
        e.preventDefault();
        const titulo = form.querySelector("input[type='text']").value;
        const mundial = form.querySelectorAll("select")[0].value;
        const categoria = form.querySelectorAll("select")[1].value;
        const archivo = form.querySelector("input[type='file']").files[0];
        const descripcion = form.querySelector("textarea").value;

        console.log({ titulo, mundial, categoria, archivo, descripcion });
        mostrarNotificacion("success", " Publicación creada con éxito.");
        closeNuevaPub();
        form.reset();
      });
    })
    .catch(err => console.error("Error cargando el modal:", err));
});
