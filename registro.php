<!-- ------------------------------- -->
<!-- PARTE HTML: FORMULARIO VISUAL -->
<!-- ------------------------------- -->
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro</title>
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" href="css/registro.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body>
  <header>
    <div class="logo">
      <img src="img/2.png" alt="Logo Mundial" style="height:50px;">
    </div>
    <div class="inicio">
      <a href="index.php"><i class="bi bi-house-fill"></i></a>
    </div>
  </header>

  <div class="container py-5 d-flex justify-content-center">
    <div class="form-container p-4">
      <h2 class="text-center">Crea tu cuenta</h2>

      <form id="registroForm" action="api-rest/api.php" method="post" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="accion" value="registrarUsuario">

        <div class="mb-3">
          <label for="name" class="form-label"><strong>Nombre(s)</strong></label>
          <input type="text" id="name" name="user_name" class="form-control" required />
        </div>

        <div class="mb-3">
          <label for="apellido" class="form-label"><strong>Apellidos</strong></label>
          <input type="text" id="apellido" name="user_apellido" class="form-control" required />
        </div>

        <div class="mb-3">
          <label for="user_fecha" class="form-label"><strong>Fecha de nacimiento</strong></label>
          <input type="date" id="user_fecha" name="fecha_nac" class="form-control" required />
        </div>

        <div class="mb-3">
            <label for="emailUser" class="form-label"><strong>Correo electrónico</strong></label>
            <div class="d-flex gap-2">

              <!-- Usuario -->
              <input type="text" id="emailUser"class="form-control" 
              placeholder="tu.correo" required />

              <!-- Dominios válidos -->
              <select id="emailDomain" class="select-override" required>
                <option value="@gmail.com">@gmail.com</option>
                <option value="@hotmail.com">@hotmail.com</option>
                <option value="@outlook.com">@outlook.com</option>
                <option value="@yahoo.com">@yahoo.com</option>
              </select>

            </div>

          <!-- Campo real que se envía al backend -->
          <input type="hidden" name="user_email" id="emailReal">
        </div>

        <div class="mb-3">
          <label for="user" class="form-label"><strong>Nombre de usuario</strong></label>
          <input type="text" id="user" name="user_name2" class="form-control" required />
        </div>

        <div class="mb-3">
          <label for="password" class="form-label"><strong>Contraseña</strong></label>
          <input type="password" id="password" name="pass" class="form-control" required />
        </div>

        <div class="mb-3">
          <label for="image_user" class="form-label"><strong>Foto de perfil</strong></label>
          <input type="file" id="image_user" name="image" class="form-control"  accept="image/*" required>
        </div>

        <div class="mb-3">
          <label class="form-label d-block"><strong>Género</strong></label>
          <div class="form-check form-check-inline">
            <input type="radio" id="masculino" name="gene" value="Masculino" class="form-check-input" required>
            <label for="masculino" class="form-check-label">Masculino</label>
          </div>
          <div class="form-check form-check-inline">
            <input type="radio" id="femenino" name="gene" value="Femenino" class="form-check-input" required>
            <label for="femenino" class="form-check-label">Femenino</label>
          </div>
        </div>

        <div class="mb-3">
          <label for="pais" class="form-label"><strong>País</strong></label>
          <input list="lista-paises" id="pais" name="pais" class="form-control" placeholder="Escribe tu país" required>
          <datalist id="lista-paises">
            <option value="México">
            <option value="Argentina">
            <option value="España">
            <option value="Estados Unidos">
            <option value="Colombia">
            <option value="Chile">
          </datalist>
        </div>

        <div class="mb-3">
          <label for="Nacionalidad" class="form-label"><strong>Nacionalidad</strong></label>
          <input type="text" id="Nacionalidad" name="naci" class="form-control" placeholder="Escribe tu nacionalidad" required />
        </div>

        <div class="text-center">
          <button type="submit" class="btn btn-primary w-100" id="btnSubmit">
            Registrarse
          </button>
        </div>
      </form>

      <div class="text-center mt-3">
        <a href="login.php" class="link-registro">¿Ya tienes cuenta? <strong>Inicia sesión</strong></a>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const user = document.getElementById("emailUser");
      const domain = document.getElementById("emailDomain");
      const emailReal = document.getElementById("emailReal");

      function updateEmail() {
        let u = user.value.trim();
        let d = domain.value.trim();

        if (u !== "") {
          emailReal.value = u + d; 
        }
      }

      user.addEventListener("input", updateEmail);
      domain.addEventListener("change", updateEmail);
    });

    document.addEventListener('DOMContentLoaded', function () {
      const sel = document.getElementById('emailDomain');
      if (!sel) return;
      sel.classList.add('select-override');

      // estilo inline como última defensa
      Object.assign(sel.style, {
        backgroundColor: '#1a1a1aff',
        color: '#686767ff',
        border: '2px solid #hwb(168 0% 0%)',
        borderRadius: '12px',
        padding: '8px 12px',
        fontWeight: '700',
        appearance: 'none',
        WebkitAppearance: 'none',
        MozAppearance: 'none',
        backgroundImage: 'none'
      });
    });
  </script>

  <script>
    document.getElementById("registroForm").addEventListener("submit", async (e) => {
      e.preventDefault();
      
      const formData = new FormData(e.target);
      
      const res = await fetch("api-rest/api.php", {
        method: "POST",
        body: formData
      });
      
      const data = await res.json();

      
      alert(data.message);

      // Si quieres redirigir después del alert (solo si todo salió bien)
      if (data.status === "success") {
        window.location.href = "login.php";
      }
    });
  </script>
</body>
</html>
