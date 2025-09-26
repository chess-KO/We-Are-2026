const $ = (sel) => document.querySelector(sel);


function openModal(selector)  { const m = $(selector); if (m) m.classList.add('show'); }
function closeModal(selector) { const m = $(selector); if (m) m.classList.remove('show'); }

// === MODALES ===
function abrirModalEditar() { openModal('#modalEditar'); }
function cerrarModalEditar() { closeModal('#modalEditar'); }

function abrirModalCerrar() { openModal('#modalCerrar'); }
function cerrarModalCerrar() { closeModal('#modalCerrar'); }
function confirmarCerrar() {
  alert("Sesión cerrada.");
  window.location.href = "index.html";
}

document.addEventListener('click', (e) => {
  const modal = $('#modalEditar');
  if (modal && modal.classList.contains('show')) {
    if (e.target === modal) { // click en el overlay
      cerrarModalEditar();
    }
  }
});


function agregarComentario(btn) {
    const publicacion = btn.closest(".publicacion");
    const input = publicacion.querySelector("input");
    const contenedor = publicacion.querySelector(".lista-comentarios");

    const texto = input.value.trim();
    if (texto !== "") {
        const fecha = new Date();
        const fechaStr = fecha.toLocaleDateString();
        const horaStr = fecha.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

        const nuevo = document.createElement("div");
        nuevo.classList.add("card-comentario"); 
        nuevo.innerHTML = `
            <div class="usuario">
                <div class="avatar" style="background-image: url('https://i.pinimg.com/736x/79/9e/10/799e104095a372e5dfef0f0bb4b37b97.jpg');"></div>
                <span class="nombre">Tú</span>
            </div>
            <p>${texto}</p>
            <div class="info">
                <small>${fechaStr} ${horaStr}</small>
            </div>
        `;
        contenedor.appendChild(nuevo);
        input.value = "";
    }
}
// === CHART ===
let interaccionesChart;
function mostrarGraficas() {
  openModal("#modalStats");
  renderInteraccionesChart();
}
function closeStats() { closeModal("#modalStats"); }

function renderInteraccionesChart() {
  const ctx = document.getElementById('interaccionesChart');
  if (!ctx) return;

  const data = [800, 300, 150];
  const labels = ['Vistas', 'Likes', 'Comentarios'];

  if (interaccionesChart) interaccionesChart.destroy();

  interaccionesChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels,
      datasets: [{
        data,
        borderWidth: 0,
        backgroundColor: ['#fff018ff', '#fd007eff', '#11fce8ff'],
        hoverBackgroundColor: ['#bea304ff', '#cc008fff', '#00d0dfff']
      }]
    },
    options: {
      cutout: '55%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: { color: '#fff' },
          onClick: (e, legendItem, legend) => {
            const index = legendItem.index;
            const ci = legend.chart;

            // toggle dataset visibility
            ci.toggleDataVisibility(index);
            ci.update();

            // toggle lista usuarios
            toggleUsuarios(labels[index]);
          }
        }
      }
    }
  });
}

function toggleUsuarios(tipo) {
  // busca todos los <li> de la lista
  document.querySelectorAll(".usuarios-interaccion li").forEach(li => {
    if (li.textContent.includes(tipo.slice(0, -1))) { 
      // "Vistas" -> "Vista"
      li.style.display = (li.style.display === "none") ? "" : "none";
    }
  });
}


document.addEventListener("DOMContentLoaded", () => {
    const formEditar = document.getElementById("formEditar");
    const passInput = document.getElementById("passInput");
    const imageInput = document.getElementById("image_user");

    formEditar.addEventListener("submit", async (e) => {
        e.preventDefault();

        // Validar contraseña
        const pass = passInput.value;
        const regex = /^(?=.*[A-Z])(?=.*[a-zñ])(?=.*[\W_]).{8,}$/u;

        if (!regex.test(pass)) {
            alert("La contraseña debe tener al menos:\n- 8 caracteres\n- 1 mayúscula\n- 1 minúscula (incluyendo ñ)\n- 1 caracter especial");
            return;
        }

        // Validar imagen
        if (imageInput.files.length > 0) {
            const file = imageInput.files[0];
            const validTypes = ["image/jpeg", "image/png", "image/jpg", "image/webp"];
            if (!validTypes.includes(file.type)) {
                alert("Solo se permiten imágenes (JPG, PNG, WEBP).");
                return;
            }
        }

        const formData = new FormData(formEditar);

        try {
            const response = await fetch("php/perfil.php", {
                method: "POST",
                body: formData
            });

            const data = await response.json();
            if (data.status === "success") {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error("Error:", error);
            alert("Ocurrió un error al actualizar el perfil");
        }
    });
});

function abrirModalEditar() {
  const modal = document.getElementById("modalEditar");

  // Obtener valores del perfil
  document.getElementById("nombreInput").value = document.getElementById("nombre").innerText;
  document.getElementById("apellidoInput").value = document.getElementById("apellido").innerText;
  document.getElementById("nombreusuaInput").value = document.getElementById("n_usuario").innerText;
  document.getElementById("fechaInput").value = formatearFecha(document.getElementById("fecha").innerText);
  document.getElementById("paisInput").value = document.getElementById("pais").innerText;
  document.getElementById("nacionalidadInput").value = document.getElementById("nacionalidad").innerText;
  document.getElementById("correoInput").value = document.getElementById("correo").innerText;

  // Seleccionar radio según género
  let genero = document.getElementById("genero").innerText;
  document.querySelectorAll('input[name="gene"]').forEach(radio => {
    radio.checked = (radio.value === genero);
  });

  modal.classList.add("show");
}

// Convierte "15/03/2000" a "2000-03-15" (formato input date)
function formatearFecha(fecha) {
  const [dia, mes, anio] = fecha.split("/");
  return `${anio}-${mes}-${dia}`;
}