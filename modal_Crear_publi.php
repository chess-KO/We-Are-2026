
<div id="modalNuevaPubInner" class="modal">
  <div class="modal-content modal-nueva">
    <span class="close" onclick="closeNuevaPub()">&times;</span>
    <h2>Nueva Publicación</h2>

    <form method="POST" action="api-rest/api.php" enctype="multipart/form-data">
      <input type="hidden" name="accion" value="crearPublicacion">
      <input type="hidden" name="idmundial" id="idmundial">
      
      <label>Título</label>
      <input type="text" name="titulo" placeholder="Escribe un título">

      <label>Mundial</label>
      <input type="text"  id="mundialInput" readonly>

      <label>Categoría</label>
      <select name="categoria">
        <?php foreach ($categorias as $c): ?>
          <option value="<?= $c['Idcategoria'] ?>">
            <?= htmlspecialchars($c['Nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>


      <label>Foto / Video</label>
      <input type="file" name="archivo" accept="image/*,video/*" required>

      <label>Descripción</label>
      <textarea name="descripcion" rows="4" placeholder="Escribe algo..."></textarea>

      <button type="submit" class="btn-publicar">Publicar</button>
    </form>

  </div>
</div>
                  

                 

