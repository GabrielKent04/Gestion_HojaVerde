<?php

session_start();

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
  require_once '../config/conexion.php';

  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

  $sql = "SELECT u.*, r.nombre_rol 
          From usuarios u 
          INNER JOIN roles r ON u.id_rol = r.id_rol
          WHERE u.username = :username AND u.estado = 1";

  $stmt = $pdo->prepare($sql);

  $stmt->execute([':username' => $username]);
  $usuario = $stmt->fetch();

  if($usuario && password_verify($password, $usuario['password']))
  {
    $_SESSION['id_usuario'] = $usuario['id_usuario'];
    $_SESSION['username'] = $usuario['username'];
    $_SESSION['rol'] = $usuario['nombre_rol'];

    header("Location: ../modules/gestion/index.php");
    exit();
  }

  else
  {
    $error = "Nombre de usuario o contraseña incorrectos.";
  }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hoja Verde — Acceso al Sistema</title>
  <script src="../public/js/tailwind.js"></script>
  <script src="../public/js/lucide.min.js"></script>
  <link rel="stylesheet" href="../public/css/styles.css">
</head>
<body class="bg-gradient-to-br from-[#0D2E10] via-[#144517] to-[#1B5E20] min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

  <!-- Esferas de luz ambiental (Glow dinámico) -->
  <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-400/20 rounded-full blur-3xl pointer-events-none"></div>
  <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>
  <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[550px] bg-emerald-600/10 rounded-full blur-[100px] pointer-events-none"></div>

  <!-- Tarjeta Principal con efecto Glassmorphism y acento Hover -->
  <div class="relative bg-white/95 backdrop-blur-xl rounded-3xl shadow-[0_25px_60px_-15px_rgba(0,0,0,0.4)] hover:shadow-[0_35px_70px_-15px_rgba(0,0,0,0.5)] w-full max-w-md p-8 sm:p-10 border border-white/60 hover:border-emerald-300/60 transition-all duration-300 ease-out hover:-translate-y-1.5 hover:scale-[1.01]">
    
    <div class="text-center mb-8">
      <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100/60 text-[#1B5E20] mb-4 shadow-sm border border-emerald-200/50">
        <i data-lucide="leaf" class="w-10 h-10 text-emerald-700"></i>
      </div>
      <h1 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Hoja Verde</h1>
      <p class="text-xs font-bold text-emerald-700 uppercase tracking-widest mt-1">Centro Naturista</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-xs font-bold mb-5 flex items-center gap-2 shadow-xs">
        <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 flex-shrink-0"></i>
        <span><?php echo htmlspecialchars($error); ?></span>
      </div>
    <?php endif; ?>

    <form id="loginForm" action="login.php" method="POST" class="space-y-4">
      <div>
        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nombre de Usuario</label>
        <div class="relative">
          <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
            <i data-lucide="user" class="w-4 h-4"></i>
          </span>
          <input type="text" name="username" id="username" required placeholder="Ingresa tu usuario"
            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 text-sm focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20 outline-none transition bg-slate-50/70 focus:bg-white text-slate-800">
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Contraseña</label>
        <div class="relative">
          <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
            <i data-lucide="lock" class="w-4 h-4"></i>
          </span>
          <input type="password" name="password" id="passwordInput" required placeholder="••••••••"
            class="w-full pl-10 pr-11 py-3 rounded-xl border border-slate-200 text-sm focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20 outline-none transition bg-slate-50/70 focus:bg-white text-slate-800">
          <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-emerald-700 transition p-1" title="Mostrar/Ocultar contraseña">
            <i id="toggleEyeIcon" data-lucide="eye" class="w-4 h-4"></i>
          </button>
        </div>
      </div>

      <button type="submit" id="submitBtn"
        class="w-full py-3.5 px-4 rounded-xl font-bold text-sm bg-gradient-to-r from-[#1B5E20] to-[#2E7D32] hover:from-[#144517] hover:to-[#1B5E20] text-white active:scale-[0.98] transition shadow-md flex items-center justify-center gap-2 cursor-pointer mt-3">
        <span id="btnIconContainer">
          <i data-lucide="log-in" class="w-4 h-4"></i>
        </span>
        <span id="btnText">Ingresar al Sistema</span>
      </button>
    </form>
  </div>

  <script>
    function togglePasswordVisibility() {
      const input = document.getElementById('passwordInput');
      const icon = document.getElementById('toggleEyeIcon');
      if (input.type === 'password') {
        input.type = 'text';
        icon.setAttribute('data-lucide', 'eye-off');
      } else {
        input.type = 'password';
        icon.setAttribute('data-lucide', 'eye');
      }
      if (window.lucide) {
        lucide.createIcons();
      }
    }

    document.getElementById('loginForm').addEventListener('submit', function(e) {
      if (this.dataset.submitting === 'true') return;
      e.preventDefault();

      const btn = document.getElementById('submitBtn');
      const iconContainer = document.getElementById('btnIconContainer');
      const btnText = document.getElementById('btnText');

      this.dataset.submitting = 'true';
      btn.disabled = true;
      btn.classList.add('opacity-90', 'cursor-wait');

      iconContainer.innerHTML = `
        <svg class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      `;
      btnText.textContent = 'Verificando y accediendo...';

      setTimeout(() => {
        this.submit();
      }, 650);
    });

    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide) {
        lucide.createIcons();
      }
    });
  </script>
</body>
</html>