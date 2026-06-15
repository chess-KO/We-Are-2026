document.addEventListener("DOMContentLoaded", async () => {
  const contenedor = document.getElementById("contenedorNoticias");

  try {

    const respuesta = await fetch("api-tercero/api/noticias_api/ultimas");
    const texto = await respuesta.text();
    console.log("Respuesta bruta:", texto); // 👈 muestra el error real
    const datos = JSON.parse(texto);

    if (!Array.isArray(datos) || datos.length === 0) {
      contenedor.innerHTML = `<p class="text-center text-muted">No se encontraron noticias </p>`;
      return;
    }

    //  Crear las tarjetas de noticia
datos.forEach(noticia => {
  const card = document.createElement("div");
  card.classList.add("col-md-4", "col-sm-6");

  card.innerHTML = `
    <div class="card h-100 shadow-lg border-0 bg-dark text-white">
      <img src="${noticia.urlImagen || 'img/noticia_default.jpg'}" 
           class="card-img-top" 
           alt="${noticia.titulo || 'Noticia'}" 
           onerror="this.src='img/noticia_default.jpg'">
      <div class="card-body">
        <h5 class="card-title fw-bold">${noticia.titulo || 'Sin título'}</h5>
        <p class="card-text">${noticia.descripcion || 'Sin descripción disponible.'}</p>
      </div>
      <div class="card-footer text-end border-0 custom-footer">
        <a href="${noticia.url}" target="_blank" class="btn btn-sm neon-btn">
          Leer más
        </a>
      </div>
    </div>
  `;

  contenedor.appendChild(card);
});
  } catch (error) {
    console.error("Error al cargar noticias:", error);
    contenedor.innerHTML = `<p class="text-danger text-center">Error al cargar las noticias </p>`;
  }
});