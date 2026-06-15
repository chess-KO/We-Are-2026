
window.addEventListener("load", () => {

  const swiper = new Swiper(".mySwiper", {
  grabCursor: true,
  centeredSlides: true,

  slidesPerView: 1,
  spaceBetween: 12,

  effect: "slide",

  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },

  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },

  breakpoints: {
    480: {
      effect: "coverflow",
      slidesPerView: 1,
      spaceBetween: 10,
      coverflowEffect: {
        rotate: 15,
        depth: 80,
        slideShadows: false
      }
    },
    768: {
      effect: "coverflow",
      slidesPerView: 2,
      spaceBetween: 15,
      coverflowEffect: {
        rotate: 20,
        depth: 140
      }
    },
    992: {
      effect: "coverflow",
      slidesPerView: 2,
      spaceBetween: 20,
      coverflowEffect: {
        rotate: 25,
        depth: 180
      }
    },
    1200: {
      effect: "coverflow",
      slidesPerView: 3,
      spaceBetween: 25,
      coverflowEffect: {
        rotate: 25,
        depth: 200
      }
    }
  }
});


  const modal = document.getElementById("modalMundial");
  const closeBtn = modal.querySelector(".close-btn");
  const form = document.getElementById("formMundial");

  let currentSlide = null;

  // === Abrir modal al editar ===
  document.querySelectorAll(".edit-btn").forEach(btn => {
      btn.addEventListener("click", (e) => {

          const slide = e.target.closest(".swiper-slide");
          currentSlide = slide;

          const idMundial = slide.dataset.mundial;

          const cardFront = slide.querySelector(".card-front");
          const cardBack  = slide.querySelector(".card-back");

          // Título = sede + año
          const titulo = cardFront.querySelector("h5").innerText;
          const parts = titulo.split(" ");
          const año   = parts.pop();
          const sede  = parts.join(" ");

          document.getElementById("añoMundial").value = año;
          document.getElementById("sedeMundial").value = sede;
          document.getElementById("descMundial").value = cardFront.querySelector("p").innerText;

          const li = cardBack.querySelectorAll("li");
          document.getElementById("mascotaMundial").value    = li[1].innerText.replace("Mascota:", "").trim();
          document.getElementById("campeonMundial").value    = li[2].innerText.replace("Campeón:", "").trim();
          document.getElementById("SubcampeonMundial").value = li[3].innerText.replace("Subcampeón:", "").trim();
          document.getElementById("MarcadorFinalMundial").value = li[4].innerText.replace("Marcador final:", "").trim();
          document.getElementById("FinalMundial").value      = li[5].innerText.replace("Final:", "").trim();
          document.getElementById("LiderGolMundial").value   = li[6].innerText.replace("Líder de goleo:", "").trim();
          document.getElementById("TerceroMundial").value    = li[7].innerText.replace("3° Lugar:", "").trim();
          document.getElementById("CuartoMundial").value     = li[8].innerText.replace("4° Lugar:", "").trim();

          modal.classList.add("show");
      });
  });

  // === Cerrar modal ===
  closeBtn.addEventListener("click", () => modal.classList.remove("show"));
  window.addEventListener("click", (e) => {
      if (e.target === modal) modal.classList.remove("show");
  });

  // === Guardar cambios ===
  form.addEventListener("submit", (e) => {
      e.preventDefault();

      if (!currentSlide) {
          console.error("No hay slide seleccionado");
          return;
      }

      const idMundial = currentSlide.dataset.mundial;
      const token = TOKEN;
      const formData = new FormData();
      formData.append("accion", "modificarMundial");
      formData.append("Idmundial", idMundial);

      formData.append("Año", document.getElementById("añoMundial").value);
      formData.append("Sede", document.getElementById("sedeMundial").value);
      formData.append("Descripcion", document.getElementById("descMundial").value);
      formData.append("Mascota", document.getElementById("mascotaMundial").value);
      formData.append("Campeon", document.getElementById("campeonMundial").value);
      formData.append("Subcampeon", document.getElementById("SubcampeonMundial").value);
      formData.append("MarcadorFinal", document.getElementById("MarcadorFinalMundial").value);
      formData.append("Final", document.getElementById("FinalMundial").value);
      formData.append("Lidergoleo", document.getElementById("LiderGolMundial").value);
      formData.append("Tercerlugar", document.getElementById("TerceroMundial").value);
      formData.append("Cuartolugar", document.getElementById("CuartoMundial").value);

      // Archivos (BLOB)
      const fotoInput = document.getElementById("imgMundial");
      const logoInput = document.getElementById("logoMundial");

      if (fotoInput.files.length > 0) {
          formData.append("Foto", fotoInput.files[0]);
      }
      if (logoInput.files.length > 0) {
          formData.append("Logo", logoInput.files[0]);
      }

      fetch("api-rest/api.php", {
          method: "POST",
          headers: {
            "Authorization": "Bearer " + token
          },
          body: formData
      })
      .then(r => r.json())
      .then(data => {
          console.log(data);

          if (data.estado === "ok") {
              alert("Mundial actualizado correctamente");
              modal.classList.remove("show");
              form.reset();
          } else {
              alert(data.mensaje);
          }
      })
      .catch(err => {
          console.error("Error en fetch:", err);
          alert("No se pudo comunicar con la API");
      });
  });



  // Eliminar carta
  document.querySelectorAll(".delete-btn").forEach(btn => {
      btn.addEventListener("click", async (e) => {

        const slide = e.target.closest(".swiper-slide");
        const idMundial = slide.dataset.mundial;
        const token = TOKEN;
        if (!confirm("¿Seguro que deseas eliminar este mundial?")) return;

        const formData = new FormData();
        formData.append("accion", "eliminarMundial");
        formData.append("Idmundial", idMundial);

        try {
            const res = await fetch("api-rest/api.php", {
                method: "POST",
                headers: {
                  "Authorization": "Bearer " + token
                },
                body: formData
            });

            const json = await res.json();
            console.log("Respuesta eliminar:", json);

            if (json.estado === "ok") {
                // borrar del swiper
                const index = Array.from(swiper.slides).indexOf(slide);
                if (index >= 0) {
                    swiper.removeSlide(index);
                    swiper.update();
                }

                alert("🗑️ Mundial eliminado");
            } else {
                alert("⚠️ No se pudo eliminar: " + json.mensaje);
            }

        } catch (err) {
            console.error("Error eliminando:", err);
            alert("Error al comunicar con la API");
        }
      });
  });

});


function closeModal(selector) { 
  const modal = document.querySelector(selector); 
  modal.style.display = 'none';
}


window.onclick = function(event) { 
  const modal = document.querySelector('#modalComentarios'); 
  if (event.target == modal) { 
    modal.style.display = "none"; 
  } 
};

async function openComentariosModal(btn) {
  const modal = document.querySelector('#modalComentarios');
  const container = document.querySelector('#comentariosContainer');
  container.innerHTML = "<p>Cargando comentarios...</p>";

  const publicacion = btn.closest('.publicacion');
  const idPublicacion = publicacion.getAttribute('data-idpublicacion');
  modal.setAttribute("data-idpublicacion", idPublicacion);

  modal.style.display = 'flex';

  try {
    const formData = new FormData();
    formData.append('accion', 'listarComentarios');
    formData.append('Idpublicacion', idPublicacion);

    const response = await fetch("api-rest/api.php", {
      method: "POST",
      headers: TOKEN ? { "Authorization": "Bearer " + TOKEN } : {},
      body: formData
    });

    const raw = await response.text();
    const data = JSON.parse(raw);

    container.innerHTML = "";

    if (Array.isArray(data)) {
      if (data.length === 0) {
        container.innerHTML = "<p class='text-muted'>No hay comentarios aún.</p>";
      } else {
        data.forEach(c => {
          c.esPropio = Boolean(c.esPropio);
          c.esAdmin = Boolean(c.esAdmin);

          const comentario = crearComentarioHTML({
            Idcomentario: c.Idcomentario, // ← CORRECTO
            avatar: c.FotoUsuario || "img/default_avatar.png",
            nombre: c.Alias,
            mensaje: c.Texto,
            fecha: c.Fecha,
            esPropio: c.esPropio,
            esAdmin: c.esAdmin
          });

          container.appendChild(comentario);
        });
      }
    }

  } catch (error) {
    console.error("Error al cargar comentarios:", error);
    container.innerHTML = "<p class='text-danger'>Error de conexión.</p>";
  }
}



async function agregarComentario(btn) {
  const modal = btn.closest('.custom-modal');
  const input = modal.querySelector('.comentar input');
  const mensaje = input.value.trim();
  if (mensaje === "") return;

  const idPublicacion = modal.getAttribute("data-idpublicacion");
  const token = TOKEN;

  try {
    const formData = new FormData();
    formData.append('accion', 'insertarComentario');
    formData.append('Idpublicacion', idPublicacion);
    formData.append('Mensaje', mensaje);

    const response = await fetch("api-rest/api.php", {
      method: "POST",
      headers: { "Authorization": "Bearer " + token },
      body: formData
    });

    const data = await response.json();

    if (response.ok) {
      input.value = "";
      mostrarMensaje("Comentario agregado correctamente ✅");
      await cargarComentarios(idPublicacion);
    } else {
      alert(data.message || "Error al agregar comentario");
    }

  } catch (error) {
    console.error("Error:", error);
    alert("Error de conexión con el servidor");
  }
}

async function cargarComentarios(idPublicacion) {
const container = document.querySelector('#comentariosContainer');
  container.innerHTML = "<p>Cargando comentarios...</p>";

  try {
    const formData = new FormData();
    formData.append('accion', 'listarComentarios');
    formData.append('Idpublicacion', idPublicacion);

    const response = await fetch("api-rest/api.php", {
      method: "POST",
      headers: TOKEN ? { "Authorization": "Bearer " + TOKEN } : {},
      body: formData
    });

    const data = await response.json();
    container.innerHTML = "";

    if (response.ok && Array.isArray(data)) {
      if (data.length === 0) {
        container.innerHTML = "<p class='text-muted'>No hay comentarios aún.</p>";
      } else {
        data.forEach(c => {
          const comentario = crearComentarioHTML({
            Idcomentario: c.Idcomentario, // ← CORRECTO
            avatar: c.FotoUsuario || "img/default_avatar.png",
            nombre: c.Alias,
            mensaje: c.Texto,
            fecha: c.Fecha,
            esPropio: c.esPropio,
            esAdmin: c.esAdmin
          });

          container.appendChild(comentario);
        });
      }
    } else {
      container.innerHTML = "<p class='text-danger'>Error al cargar comentarios.</p>";
    }

  } catch (error) {
    console.error("Error al recargar comentarios:", error);
    container.innerHTML = "<p class='text-danger'>Error de conexión.</p>";
  }
}




function mostrarMensaje(texto) {
  const msg = document.createElement("div");
  msg.classList.add("mensaje-flotante");
  msg.textContent = texto;
  document.body.appendChild(msg);
  setTimeout(() => msg.remove(), 2000);
}

function crearComentarioHTML(c) {
  const div = document.createElement('div');
  div.classList.add('card-comentario');

  div.innerHTML = `
    <div class="usuario">
      <div class="avatar" style="background-image:url('${c.avatar}')"></div>
      <span class="nombre">${c.nombre}</span>
    </div>

    <p class="texto">${c.mensaje}</p>

    <div class="info">
      <span class="fecha">${c.fecha}</span>

      ${c.esAdmin ? `<button class="btn-eliminar" title="Eliminar">&#128465;</button>` : ""}
      ${c.esPropio ? `<button class="btn-editar" title="Editar">&#9998;</button>` : ""}
    </div>
  `;


  // =========================================================
  // =============== BOTÓN EDITAR =============================
  // =========================================================
  if (c.esPropio) {
    const btnEdit = div.querySelector(".btn-editar");

    btnEdit.addEventListener("click", async () => {
      console.log("ID CORRECTO:", c.Idcomentario);

      const pTexto = div.querySelector(".texto");

      const input = document.createElement("input");
      input.type = "text";
      input.value = c.mensaje;
      input.className = "input-editar";

      pTexto.replaceWith(input);
      input.focus();

      input.addEventListener("keydown", async (e) => {
        if (e.key === "Enter") {
          const nuevo = input.value.trim();
          if (nuevo.length === 0) return;

          const formData = new FormData();
          formData.append("accion", "editarComentario");
          formData.append("Idcomentario", c.Idcomentario); // ← CORRECTO
          formData.append("Comentario", nuevo);

          const response = await fetch("api-rest/api.php", {
            method: "POST",
            headers: { "Authorization": "Bearer " + TOKEN },
            body: formData
          });

          const raw = await response.text();
          console.log("RAW EDITAR:", raw);

          let data;
          try {
            data = JSON.parse(raw);
          } catch {
            alert("Respuesta no válida del servidor");
            return;
          }

          if (response.ok) {
            const p = document.createElement("p");
            p.className = "texto";
            p.textContent = nuevo;
            input.replaceWith(p);
          } else {
            alert("Error: " + (data.message || "No se pudo editar"));
          }
        }
      });
    });
  }



  // =========================================================
  // ============== BOTÓN ELIMINAR ===========================
  // =========================================================
  if (c.esAdmin) {
    const btnDel = div.querySelector(".btn-eliminar");

    btnDel.addEventListener("click", async () => {
      if (!confirm("¿Eliminar comentario?")) return;

      const formData = new FormData();
      formData.append("accion", "eliminarComentario");
      formData.append("Idcomentario", c.Idcomentario); // ← CORRECTO

      const response = await fetch("api-rest/api.php", {
        method: "POST",
        headers: { "Authorization": "Bearer " + TOKEN },
        body: formData
      });

      const data = await response.json();

      if (response.ok) {
        div.remove();
      } else {
        alert(data.message);
      }
    });
  }

  return div;
}


//PARA INTERACCIONES

//Likes
document.querySelectorAll(".like").forEach(likeBtn => {
  const likeCount = likeBtn.querySelector(".count");
  const publicacion = likeBtn.closest(".publicacion");
  const idPublicacion = publicacion.getAttribute("data-idpublicacion");

  likeBtn.addEventListener("click", async () => {
    const token = TOKEN; // inyectado desde PHP
    likeBtn.classList.toggle("active");

    try {
      const formData = new FormData();
      formData.append('accion', 'toggleLike');
      formData.append('Idpublicacion', idPublicacion);

      const response = await fetch("api-rest/api.php", {
        method: "POST",
        headers: { "Authorization": "Bearer " + token },
        body: formData
      });

      const data = await response.json();
      if (response.ok) {
        likeCount.textContent = data.totalLikes;
      } else {
        console.error(data.message);
      }
    } catch (error) {
      console.error("Error al registrar like:", error);
    }
  });
});


//Vistas
document.addEventListener("DOMContentLoaded", () => {
  const publicaciones = document.querySelectorAll(".publicacion");
  const token = TOKEN; // inyectado desde PHP

  // Usamos IntersectionObserver para detectar cuando la publicación aparece
  const observer = new IntersectionObserver(entries => {
    entries.forEach(async entry => {
      if (entry.isIntersecting) {
        const pub = entry.target;
        const idPublicacion = pub.getAttribute("data-idpublicacion");

        try {
          const formData = new FormData();
          formData.append('accion', 'registrarVista');
          formData.append('Idpublicacion', idPublicacion);

          await fetch("api-rest/api.php", {
            method: "POST",
            headers: { "Authorization": "Bearer " + token },
            body: formData
          });

          observer.unobserve(pub); // Evita registrar más de una vez
        } catch (error) {
          console.error("Error al registrar vista:", error);
        }
      }
    });
  }, { threshold: 0.5 }); // 50% visible

  publicaciones.forEach(pub => observer.observe(pub));
});




//Este wey es el que escucha los clicks en las tarjetas de mundial
let mundialActual = null;

document.addEventListener("DOMContentLoaded", () => {
  // --- LIMPIAR src y data-mundial (elimina espacios accidentales)
  document.querySelectorAll('img').forEach(img => {
    const raw = img.getAttribute('src');
    if (raw && raw !== raw.trim()) {
      img.setAttribute('src', raw.trim());
    }
  });
  document.querySelectorAll('.swiper-slide').forEach(slide => {
    if (slide.dataset.mundial) slide.dataset.mundial = slide.dataset.mundial.trim();
  });

  // --- delegación: un solo listener sobre wrapper (más robusto)
  const wrapper = document.querySelector('.swiper-wrapper');
  if (wrapper) {
    wrapper.addEventListener('click', e => {
      const slide = e.target.closest('.swiper-slide');
      if (!slide) return; // click fuera de una slide
      // Mostrar botones
      const btnCrear = document.getElementById("btnCrearPubli");
      const btnReg = document.getElementById("btnRegresar");
      if (btnCrear) btnCrear.style.display = "block";
      if (btnReg) btnReg.style.display = "block";

      // Guardar Mundial seleccionado y filtrar
      mundialActual = slide.dataset.mundial || null;
      console.log("ID del mundial seleccionado:", mundialActual);

      //Esto filtra las publicaciones por año, despues lo cambiaré para que se filtren por ID del mundial
      filtrarPublicaciones(mundialActual);

      // Guarda los datos visibles de la tarjeta (Sede y Año)
      const sede = slide.querySelector('.caption h5')?.innerText.split(" ")[0] || "";
      const anio = slide.querySelector('.caption h5')?.innerText.split(" ")[1] || "";

      // Guarda temporalmente los datos del mundial seleccionado
      sessionStorage.setItem("mundialSeleccionado", JSON.stringify({
        id: mundialActual,
        sede: sede,
        anio: anio
      }));
      

      // mostrar botones editar
      document.querySelectorAll(".btn-editar-pub").forEach(btn => {
        btn.style.display = "block";
      });
    });
  } else {
    console.warn("No se encontró .swiper-wrapper");
  }
});

// FILTRO DE PUBLICACIONES
function filtrarPublicaciones(mundial) {
  const publicaciones = document.querySelectorAll(".publicacion");

  publicaciones.forEach(pub => {
    if (pub.dataset.mundial === mundial) {
      pub.style.display = "flex"; // usa flex para mantener diseño interno
    } else {
      pub.style.display = "none";
    }

  });
}




// Evento para botón regresar
document.getElementById("btnRegresar").addEventListener("click", () => {
  document.querySelectorAll(".publicacion").forEach(pub => {
    pub.style.display = "block"; // restaurar CSS original
  });

  document.getElementById("btnCrearPubli").style.display = "none";
  document.getElementById("btnRegresar").style.display = "none";

  mundialActual = null;

  // Ocultar botones editar
  document.querySelectorAll(".btn-editar-pub").forEach(btn => {
    btn.style.display = "none";
  });

  // Recalcular Masonry
  if (window.msnry) {
    msnry.reloadItems();
    msnry.layout();
  }
});



//Para editar publicaciones
function activarEdicionPublicacion(btn) {
  if (!mundialActual) {
    alert("Solo puedes editar estando en una página de un mundial ⚽");
    return;
  }

  const publicacion = btn.closest(".publicacion");

  const titulo = publicacion.querySelector(".titulo");
  const descripcion = publicacion.querySelector(".descripcion");
  const mundial = publicacion.querySelector(".mundial");
  const categoria = publicacion.querySelector(".categoria");
  const imagen = publicacion.querySelector(".media img, .media video");

  // Título
  if (titulo && titulo.tagName.toLowerCase() !== "input") {
    const inputTitulo = document.createElement("input");
    inputTitulo.type = "text";
    inputTitulo.value = titulo.textContent.trim();
    inputTitulo.classList.add("input-editable", "titulo");
    titulo.replaceWith(inputTitulo);
  }

  // Descripción
  if (descripcion && descripcion.tagName.toLowerCase() !== "input") {
    const inputDesc = document.createElement("input");
    inputDesc.type = "text";
    inputDesc.value = descripcion.textContent.trim();
    inputDesc.classList.add("input-editable", "descripcion");
    descripcion.replaceWith(inputDesc);
  }

  
  // Categoría -> combo
  if (categoria && categoria.tagName.toLowerCase() !== "select") {
    const selectCategoria = document.createElement("select");
    selectCategoria.classList.add("input-editable", "combo-categoria");

    // Cargar opciones desde la API
    fetch("api-rest/api.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ accion: "traerCategorias" })
    })
    .then(res => res.json())
    .then(categorias => {
      categorias.forEach(c => {
        const option = document.createElement("option");
        option.value = c.Idcategoria;
        option.textContent = `${c.Nombre}`;
        if (categoria.textContent.includes(c.Nombre)) {
          option.selected = true;
        }
        selectCategoria.appendChild(option);
      });
    })
    .catch(err => console.error("Error cargando mundiales:", err));

    categoria.replaceWith(selectCategoria);
  }
  // === Combo de mundiales scrolleable ===
  if (mundial && mundial.tagName.toLowerCase() !== "select") {
    const selectMundial = document.createElement("select");
    selectMundial.classList.add("input-editable", "combo-mundial");

    // Cargar opciones desde la API
    fetch("api-rest/api.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ accion: "traerMundiales" })
    })
    .then(res => res.json())
    .then(mundiales => {
      mundiales.forEach(m => {
        const option = document.createElement("option");
        option.value = m.Idmundial;
        option.textContent = `${m.Sede} ${m.Año}`;
        if (mundial.textContent.includes(m.Sede) || mundial.textContent.includes(m.Año)) {
          option.selected = true;
        }
        selectMundial.appendChild(option);
      });
    })
    .catch(err => console.error("Error cargando mundiales:", err));

    mundial.replaceWith(selectMundial);
  }
  

  // Imagen -> input file con preview
  if (imagen && (imagen.tagName.toLowerCase() === "img" || imagen.tagName.toLowerCase() === "video")) {
    const originalMedia = imagen.cloneNode(true); // copia original
    publicacion.dataset.originalMedia = originalMedia.outerHTML; // guardo original

    const inputImg = document.createElement("input");
    inputImg.type = "file";
    inputImg.accept = "image/*,video/*";
    inputImg.classList.add("file-upload"); // 👈 para aplicar el css correcto

    const label = document.createElement("label");
    label.classList.add("label-file");
    label.appendChild(inputImg);

    // Vista previa inmediata
    inputImg.addEventListener("change", e => {
      const file = e.target.files[0];
      if (file) {
        const url = URL.createObjectURL(file);
        const preview = document.createElement(file.type.startsWith("image/") ? "img" : "video");
        preview.src = url;
        preview.style.width = "100%";
        preview.style.borderRadius = "10px";
        if (preview.tagName === "VIDEO") {
          preview.controls = true;
        }

        const wrapper = document.createElement("div");
        wrapper.classList.add("media-preview");
        wrapper.appendChild(preview);
        wrapper.appendChild(inputImg);
        inputImg.style.display = "none";

        label.replaceWith(wrapper);
      }
    });

    imagen.replaceWith(label);
  }

  btn.textContent = "Guardar";
  btn.onclick = () => guardarPublicacion(publicacion, btn);
}

function guardarPublicacion(publicacion, btn) {

  const idPublicacion = publicacion.dataset.idpublicacion;

  const tituloInput = publicacion.querySelector("input.input-editable.titulo");
  const descInput = publicacion.querySelector("input.input-editable.descripcion");
  const mundialSelect = publicacion.querySelector("select.combo-mundial");
  const categoriaSelect = publicacion.querySelector("select.combo-categoria");
  const mediaPreview = publicacion.querySelector(".media-preview");
  const fileInput = mediaPreview ? mediaPreview.querySelector("input[type=file]") : null;

  // --- valores reales ---
  const nuevoTitulo = tituloInput ? tituloInput.value : publicacion.querySelector(".titulo").textContent.trim();
  const nuevaDescripcion = descInput ? descInput.value : publicacion.querySelector(".descripcion").textContent.trim();
  const nuevoMundial = mundialSelect ? mundialSelect.value : publicacion.dataset.mundial;
  const nuevaCategoria = categoriaSelect ? categoriaSelect.value : publicacion.querySelector(".categoria").dataset.idcategoria;

  const formData = new FormData();
  formData.append("accion", "modificarPublicacion");
  formData.append("Idpublicacion", idPublicacion);
  formData.append("Titulo", nuevoTitulo);
  formData.append("Descripcion", nuevaDescripcion);
  formData.append("Idmundial", nuevoMundial);
  formData.append("Idcategoria", nuevaCategoria);

  if (fileInput && fileInput.files.length > 0) {
    formData.append("archivoNuevo", fileInput.files[0]);
  }

  fetch("api-rest/api.php", {
      method: "POST",
      headers: {
          "Authorization": "Bearer " + TOKEN
      },
      body: formData
  })
  .then(async res => {
      let text = await res.text();
      console.log("RESP RAW:", text);

      // 🔥 Eliminar basura antes y después del JSON
      text = text.trim();

      // 🔥 Intentar detectar la parte JSON en caso de que venga mezclada con warnings
      const jsonStart = text.indexOf("{");
      const jsonEnd = text.lastIndexOf("}");

      if (jsonStart === -1 || jsonEnd === -1) {
          console.error("Formato desconocido:", text);
          return null;
      }

      text = text.substring(jsonStart, jsonEnd + 1);

      try {
          return JSON.parse(text);
      } catch (e) {
          console.error("Error parseando JSON:", e, text);
          return null;
      }
  })
  .then(data => {
      if (data.success) {
          alert("Publicación modificada ✔. Espera a que el Administrador la apruebe.");
      } else {
          alert("Error: " + data.message);
      }
  })
  .catch(err => {
      console.error("Error enviando modificación:", err);
  });

  // Restaurar botón
  btn.textContent = "Editar";
  btn.onclick = () => activarEdicionPublicacion(btn);
}

