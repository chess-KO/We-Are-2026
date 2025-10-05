
window.addEventListener("load", () => {

  const swiper = new Swiper(".mySwiper", {
  effect: "coverflow",
  grabCursor: true,
  centeredSlides: true,
  slidesPerView: 1,
  spaceBetween: 10,

  coverflowEffect: {
    rotate: 25,     // menos rotación para evitar deformación en móviles
    stretch: 0,
    depth: 180,     // profundidad ajustada
    modifier: 1,
    slideShadows: true,
  },
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
      slidesPerView: 1,
      spaceBetween: 10,
    },
    768: {
      slidesPerView: 2,
      spaceBetween: 15,
    },
    992: {
      slidesPerView: 2,
      spaceBetween: 20,
    },
    1200: {
      slidesPerView: 3,
      spaceBetween: 25,
    },
  }
});


  const modal = document.getElementById("modalMundial");
  const closeBtn = modal.querySelector(".close-btn");
  const form = document.getElementById("formMundial");
  let currentCard = null;
  let currentImg = "";
  let currentLogo = "";

  // Abrir modal al editar
  document.querySelectorAll(".edit-btn").forEach(btn => {
    btn.addEventListener("click", (e) => {
      const card = e.target.closest(".card");
      currentCard = card;

      const title = card.querySelector("h5").innerText;
      const desc = card.querySelector("p").innerText;
      const img = card.querySelector(".main-img").src;
      const logo = card.querySelector(".logo-img").src;

      document.getElementById("nombreMundial").value = title;
      document.getElementById("descMundial").value = desc;
      currentImg = img;
      currentLogo = logo;

      modal.classList.add("show");
    });
  });

  // Cerrar modal
  closeBtn.addEventListener("click", () => modal.classList.remove("show"));
  window.addEventListener("click", (e) => {
    if (e.target === modal) modal.classList.remove("show");
  });

  // Guardar cambios
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    if (!currentCard) return;

    const newTitle = document.getElementById("nombreMundial").value;
    const newDesc = document.getElementById("descMundial").value;
    const imgInput = document.getElementById("imgMundial");
    const logoInput = document.getElementById("logoMundial");

    currentCard.querySelector("h5").innerText = newTitle;
    currentCard.querySelector("p").innerText = newDesc;

    // Imagen destacada
    if (imgInput.files.length > 0) {
      const reader = new FileReader();
      reader.onload = () => {
        currentCard.querySelector(".main-img").src = reader.result;
      };
      reader.readAsDataURL(imgInput.files[0]);
    } else {
      currentCard.querySelector(".main-img").src = currentImg;
    }

    // Logo del mundial
    if (logoInput.files.length > 0) {
      const readerLogo = new FileReader();
      readerLogo.onload = () => {
        currentCard.querySelector(".logo-img").src = readerLogo.result;
      };
      readerLogo.readAsDataURL(logoInput.files[0]);
    } else {
      currentCard.querySelector(".logo-img").src = currentLogo;
    }

    alert("✅ Mundial editado correctamente");
    modal.classList.remove("show");
    form.reset();
  });

  // Eliminar carta
  document.querySelectorAll(".delete-btn").forEach(btn => {
    btn.addEventListener("click", (e) => {
      const card = e.target.closest(".swiper-slide");
      if (confirm("¿Seguro que deseas eliminar este mundial?")) {
        const index = Array.from(swiper.slides).indexOf(card);
        if (index >= 0) {
          swiper.removeSlide(index); // elimina el slide correctamente
          swiper.update();          // actualiza Swiper
          alert("🗑️ Mundial eliminado"); // confirmación
        }
      }
    });
  });
});
// Función para abrir modal y cargar comentarios
const comentariosPorPublicacion = [
  [
    { 
      nombre: "Pedro Gómez", 
      avatar: "https://i.pinimg.com/236x/94/8f/91/948f912fa3cb07527c3dfa38ae449cfc.jpg", 
      mensaje: "si,es muy triste",
      fecha: "2025-09-12 10:30"
    },
    { 
      nombre: "María López", 
      avatar: "https://i.pinimg.com/236x/79/9e/10/799e104095a372e5dfef0f0bb4b37b97.jpg", 
      mensaje: "Que grande argentina!",
      fecha: "2025-09-12 11:00"
    }
  ],
  [
    { 
      nombre: "Juan Pérez", 
      avatar: "https://i.pinimg.com/736x/79/9e/10/799e104095a372e5dfef0f0bb4b37b97.jpg", 
      mensaje: "Es un raro caso",
      fecha: "2025-09-12 12:00"
    }
  ],
  [
    { 
      nombre: "Ana Torres", 
      avatar: "https://i.pinimg.com/236x/94/8f/91/948f912fa3cb07527c3dfa38ae449cfc.jpg", 
      mensaje: "Increíble partido",
      fecha: "2025-09-12 14:15"
    }
  ],
  [
    { 
      nombre: "Luis Fernández", 
      avatar: "https://i.pinimg.com/236x/94/8f/91/948f912fa3cb07527c3dfa38ae449cfc.jpg", 
      mensaje: "que interesante",
      fecha: "2025-09-12 15:20"
    }
  ]
];

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

function openComentariosModal(btn) {
  const modal = document.querySelector('#modalComentarios');
  const container = document.querySelector('#comentariosContainer');

  container.innerHTML = "";

  const publicacion = btn.closest('.publicacion');
  const index = Array.from(document.querySelectorAll('.publicacion')).indexOf(publicacion);
  modal.setAttribute("data-index", index);

  comentariosPorPublicacion[index].forEach((c, i) => {
    const comentario = crearComentarioHTML(c, index, i);
    container.appendChild(comentario);
  });

  modal.style.display = 'flex';
}

function agregarComentario(btn) {
  const modal = btn.closest('.custom-modal');
  const input = modal.querySelector('.comentar input');
  const mensaje = input.value.trim();
  if (mensaje === "") return; 

  const index = modal.getAttribute("data-index");
  const ahora = new Date();
  const fecha = ahora.toLocaleString(); 

  const nuevoComentario = {
    avatar: "https://i.pinimg.com/736x/79/9e/10/799e104095a372e5dfef0f0bb4b37b97.jpg", 
    nombre: "Usuario",
    mensaje: mensaje,
    fecha: fecha
  };

  comentariosPorPublicacion[index].push(nuevoComentario);

  const container = modal.querySelector("#comentariosContainer");
  const comentario = crearComentarioHTML(nuevoComentario, index, comentariosPorPublicacion[index].length - 1);
  container.appendChild(comentario);

  input.value = "";
  container.scrollTop = container.scrollHeight;
}


function mostrarMensaje(texto) {
  const msg = document.createElement("div");
  msg.classList.add("mensaje-flotante");
  msg.textContent = texto;
  document.body.appendChild(msg);
  setTimeout(() => msg.remove(), 2000);
}

function crearComentarioHTML(c, pubIndex, comIndex) {
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
      <button class="btn-eliminar" title="Eliminar">&#128465;</button>
      <button class="btn-editar" title="Editar">&#9998;</button>
    </div>
  `;

  // eliminar comentario
  div.querySelector(".btn-eliminar").addEventListener("click", () => {
    comentariosPorPublicacion[pubIndex].splice(comIndex, 1);
    div.remove();
    mostrarMensaje("Comentario eliminado ✅");
  });

  // editar comentario
div.querySelector(".btn-editar").addEventListener("click", () => {
  const texto = div.querySelector(".texto");
  const input = document.createElement("input");
  input.type = "text";
  input.value = texto.textContent;
  input.classList.add("input-editar");

  // reemplazar texto por input
  texto.replaceWith(input);
  input.focus();

  input.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      const nuevoTexto = input.value.trim();
      if (nuevoTexto !== "") {
        comentariosPorPublicacion[pubIndex][comIndex].mensaje = nuevoTexto;
        const p = document.createElement("p");
        p.classList.add("texto");
        p.textContent = nuevoTexto;
        input.replaceWith(p);
        mostrarMensaje("Comentario editado ✏️");
      }
    }
  });
});

  return div;
}
document.querySelectorAll(".like").forEach(likeBtn => {
  const likeCount = likeBtn.querySelector(".count");

  likeBtn.addEventListener("click", () => {
    likeBtn.classList.toggle("active");

    let count = parseInt(likeCount.textContent);
    if (likeBtn.classList.contains("active")) {
      likeCount.textContent = count + 1;
    } else {
      likeCount.textContent = count - 1;
    }
  });
});





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
      console.log("Mundial seleccionado:", mundialActual);
      filtrarPublicaciones(mundialActual);

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
  const seleccion = publicacion.querySelector(".seleccion");
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
    const select = document.createElement("select");
    select.classList.add("input-editable");

    const opciones = ["Copas", "Jugadores", "Historia", "Noticias"];
    opciones.forEach(op => {
      const option = document.createElement("option");
      option.value = op;
      option.textContent = op;
      if (categoria.textContent.includes(op)) option.selected = true;
      select.appendChild(option);
    });

    categoria.replaceWith(select);
  }
  if (mundial && mundial.tagName.toLowerCase() !== "input") {
    const inputSelec = document.createElement("input");
    inputSelec.type = "text";
    inputSelec.value = mundial.textContent.replace("Mundial:","").trim();
    inputSelec.classList.add("input-editable", "mundial");
    mundial.replaceWith(inputSelec);
  }
  // Selección
  if (seleccion && seleccion.tagName.toLowerCase() !== "input") {
    const inputSelec = document.createElement("input");
    inputSelec.type = "text";
    inputSelec.value = seleccion.textContent.replace("Selección:", "").trim();
    inputSelec.classList.add("input-editable", "seleccion");
    seleccion.replaceWith(inputSelec);
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
  const tituloInput = publicacion.querySelector("input.input-editable.titulo");
  const descInput = publicacion.querySelector("input.input-editable.descripcion");
  const seleccionInput = publicacion.querySelector("input.input-editable.seleccion");
  const mundialInput = publicacion.querySelector("input.input-editable.mundial");
  const selectCat = publicacion.querySelector("select.input-editable");
  const mediaPreview = publicacion.querySelector(".media-preview");

  // Título
  if (tituloInput) {
    const h3 = document.createElement("h3");
    h3.classList.add("titulo");
    h3.textContent = tituloInput.value;
    tituloInput.replaceWith(h3);
  }

  // Descripción
  if (descInput) {
    const h4 = document.createElement("h4");
    h4.classList.add("descripcion");
    h4.textContent = descInput.value;
    descInput.replaceWith(h4);
  }

  // Categoría
  if (selectCat) {
    const span = document.createElement("span");
    span.classList.add("categoria");
    span.innerHTML = `<strong>Categoría:</strong> ${selectCat.value}`;
    selectCat.replaceWith(span);
  }

  // Selección
  if (seleccionInput) {
    const span = document.createElement("span");
    span.classList.add("seleccion");
    span.innerHTML = `<strong>Selección:</strong> ${seleccionInput.value}`;
    seleccionInput.replaceWith(span);
  }
    if (mundialInput) {
    const span = document.createElement("span");
    span.classList.add("mundial");
    span.innerHTML = `<strong>Mundial:</strong> ${mundialInput.value}`;
    mundialInput.replaceWith(span);
  }

  // Imagen / Video final
  const mediaDiv = publicacion.querySelector(".media");
  if (mediaPreview) {
    const preview = mediaPreview.querySelector("img, video");
    if (preview) {
      mediaDiv.innerHTML = "";
      mediaDiv.appendChild(preview);
    }
    mediaPreview.remove();
  } else {
    // Si no se seleccionó nada, restaurar original
    if (publicacion.dataset.originalMedia) {
      mediaDiv.innerHTML = publicacion.dataset.originalMedia;
    }
  }

  // Actualizar fechas
  const fechas = publicacion.querySelector(".fechas");
  if (fechas) {
    const ahora = new Date().toLocaleString();
    const spans = fechas.querySelectorAll("span");
    if (spans[0]) spans[0].textContent = `Elaboración: ${ahora}`;
    if (spans[1]) spans[1].textContent = `Aprobación: ${ahora}`;
  }

  // Cambiar botón a Editar
  btn.textContent = "Editar";
  btn.onclick = () => activarEdicionPublicacion(btn);
}
