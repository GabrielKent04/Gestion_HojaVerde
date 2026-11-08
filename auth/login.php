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
  <title>Acceso al Sistema</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="../public/css/styles.css">
</head>
<body class="bg-gradient-to-br from-[#144517] via-[#1B5E20] to-[#2E7D32] min-h-screen flex items-center justify-center p-4">

  <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 sm:p-10 border border-emerald-100">
    <div class="text-center mb-8">
      <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-emerald-50 text-[#1B5E20] mb-4 shadow-inner">
        <i data-lucide="leaf" class="w-10 h-10"></i>
      </div>
      <h1 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Hoja Verde</h1>
      <p class="text-xs font-semibold text-emerald-700 uppercase tracking-widest mt-1">Centro Naturista</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-xs font-bold mb-4 flex items-center gap-2">
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
          <input type="text" name="username" id="username" required placeholder="gerente o empleado"
            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 text-sm focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20 outline-none transition bg-slate-50 focus:bg-white">
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Contraseña</label>
        <div class="relative">
          <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
            <i data-lucide="lock" class="w-4 h-4"></i>
          </span>
          <input type="password" name="password" id="password" required placeholder="••••••••"
            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 text-sm focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20 outline-none transition bg-slate-50 focus:bg-white">
        </div>
      </div>

      <button type="submit"
        class="w-full py-3.5 px-4 rounded-xl font-bold text-sm bg-[#1B5E20] hover:bg-[#144517] text-white active:scale-[0.98] transition shadow-md flex items-center justify-center gap-2 cursor-pointer mt-2">
        <i data-lucide="log-in" class="w-4 h-4"></i>
        <span>Ingresar al Sistema</span>
      </button>
    </form>

    <div class="mt-8 pt-6 border-t border-slate-100 text-[11px] text-slate-400">
      <p class="font-bold text-slate-500 mb-1">Cuentas activas en la Base de Datos:</p>
      <div class="flex justify-between bg-slate-50 p-2.5 rounded-lg border border-slate-100 font-mono">
        <span>Gerente: <strong class="text-slate-700">gerente</strong></span>
        <span>Empleado: <strong class="text-slate-700">empleado</strong></span>
      </div>
      <p class="text-center mt-2">Clave por defecto: <span class="font-mono text-slate-600">password123</span></p>
    </div>
  </div>

  <script src="../public/js/app.js"></script>
</body>
</html>
