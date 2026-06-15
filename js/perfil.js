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
    // Redirige al archivo PHP que destruye la sesión
    window.location.href = "php/logout.php";
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

function mostrarGraficas(idPublicacion) {
    openModal("#modalStats");
    renderInteraccionesChart(idPublicacion);
    mostrarUsuariosInteraccion();
}

function closeStats() { closeModal("#modalStats"); }

let interaccionesChart;


async function renderInteraccionesChart(idPublicacion) {
    const ctx = document.getElementById('interaccionesChart');
    if (!ctx) return;

    const formData = new FormData();
    formData.append("accion", "traerEstadisticas");
    formData.append("Idpublicacion", idPublicacion);

    const token = localStorage.getItem("token");

    const res = await fetch("api-rest/api.php", {
        method: "POST",
        headers: {
            "Authorization": "Bearer " + token
        },
        body: formData
    });

    const stats = await res.json();
    const totalInteracciones = stats.totalInteracciones || 0;

    const data = [
        stats.totalVistas || 0,
        stats.totalLikes || 0,
        stats.totalComentarios || 0
    ];

    const labels = ["Vistas", "Likes", "Comentarios"];

    
    const porcentajes = [
        stats.porcentajeVistas || 0,
        stats.porcentajeLikes || 0,
        stats.porcentajeComentarios || 0
    ];

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
                    labels: {
                        color: '#fff',
                        generateLabels(chart) {
                            const original = Chart.overrides.doughnut.plugins.legend.labels.generateLabels(chart);
                            return original.map((item, i) => {
                                return {
                                    ...item,
                                    text: `${item.text} (${porcentajes[i]}%)`
                                };
                            });
                        }
                    },
                    onClick: (e, legendItem, legend) => {
                        const index = legendItem.index;
                        legend.chart.toggleDataVisibility(index);
                        legend.chart.update();
                        toggleUsuarios(labels[index]);
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                        let label = context.label || '';
                        const value = context.raw ?? 0;
                        let porcentaje = 0;

                        if (totalInteracciones > 0) {
                            porcentaje = ((value / totalInteracciones) * 100).toFixed(2);
                        }
                        return `${label}: ${value} (${porcentaje}%)`;
                        }
                    }
                }
            }
        }
    });
}

async function obtenerUsuariosInteraccion() {
    const formData = new FormData();
    formData.append("accion", "listarUsuariosInteracciones");

    const res = await fetch("api-rest/api.php", {
        method: "POST",
        body: formData,
        headers: {
            "Authorization": "Bearer " + localStorage.getItem("token")
        }
    });

    return await res.json();
}

async function mostrarUsuariosInteraccion() {
    const lista = document.querySelector(".usuarios-interaccion ul");
    lista.innerHTML = "<li>Cargando...</li>";

    const datos = await obtenerUsuariosInteraccion();

    if (!datos.length) {
        lista.innerHTML = "<li>No hay interacciones</li>";
        return;
    }

    lista.innerHTML = "";

    datos.forEach(u => {
      let icono = "";
      if (u.tipo == 1) icono = '<i class="bi bi-hand-thumbs-up"></i>';
      if (u.tipo == 2) icono = '<i class="bi bi-eye"></i> Vista';
      if (u.tipo == 3) icono = '<i class="bi bi-chat-dots"></i> Comentario';

      lista.innerHTML += `
          <li>${u.usuario} - ${icono}</li>
      `;
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



// === ACTUALIZAR PERFIL ===


function abrirModalEditar() {
    const modal = document.getElementById("modalEditar");

    // Rellenar campos básicos
    document.getElementById("nombreInput").value = document.getElementById("nombre").innerText;
    document.getElementById("apellidoInput").value = document.getElementById("apellido").innerText;
    document.getElementById("nombreusuaInput").value = document.getElementById("n_usuario").innerText;
    document.getElementById("fechaInput").value = formatearFecha(document.getElementById("fecha").innerText);
    document.getElementById("paisInput").value = document.getElementById("pais").innerText;
    document.getElementById("nacionalidadInput").value = document.getElementById("nacionalidad").innerText;

    // ============================
    //     CORREO (nuevo formato)
    // ============================

    const correoActual = document.getElementById("correo").innerText;
    const correoUser = document.getElementById("correoInputUser");
    const correoDomain = document.getElementById("correoInputDomain");
    const correoReal = document.getElementById("correoRealEdit");

    if (correoActual.includes("@")) {
        const [userPart, domainPart] = correoActual.split("@");
        correoUser.value = userPart;
        correoDomain.value = "@" + domainPart;
        correoReal.value = correoActual; // input real
    }

    function actualizarCorreoReal() {
        correoReal.value = correoUser.value + correoDomain.value;
    }

    correoUser.addEventListener("input", actualizarCorreoReal);
    correoDomain.addEventListener("change", actualizarCorreoReal);

    // Selección de género
    let genero = document.getElementById("genero").innerText;
    document.querySelectorAll('input[name="gene"]').forEach(radio => {
        radio.checked = (radio.value === genero);
    });

    modal.classList.add("show");
}

// Convertir 15/03/2000 → 2000-03-15
function formatearFecha(fecha) {
  if (!fecha || !fecha.includes("/")) {
      return ""; // deja vacío si no es una fecha válida
  }

  const partes = fecha.split("/");

  if (partes.length !== 3) {
      return ""; // por si viene algo raro
  }

  let [dia, mes, anio] = partes;

  // por si vienen con espacios
  dia = dia.trim();
  mes = mes.trim();
  anio = anio.trim();

  // si algo está "undefined", lo evitamos
  if (!dia || !mes || !anio) return "";

  return '${anio}-${mes.padStart(2, "0")}-${dia.padStart(2, "0")';

}