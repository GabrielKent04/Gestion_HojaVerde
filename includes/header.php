<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Centro Naturista Hoja Verde</title>
  <!-- Tailwind & Fuentes -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body class="bg-[#F9FBF9] min-h-screen text-slate-800 flex flex-col">

  <!-- BARRA SUPERIOR INSTITUCIONAL -->
  <header class="bg-[#144517] border-b border-emerald-800/60 shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-4 sm:px-6 h-[70px]">
      
      <!-- Logo y Nombre -->
      <div class="flex items-center gap-3">
        <div class="bg-emerald-600 rounded-xl w-10 h-10 flex items-center justify-center flex-shrink-0 text-white shadow-sm">
          <i data-lucide="leaf" class="w-5 h-5"></i>
        </div>
        <div class="flex flex-col">
          <span class="text-emerald-200 text-[10px] font-bold uppercase tracking-widest leading-none mb-1">CENTRO NATURISTA</span>
          <span class="text-white text-lg sm:text-xl font-black tracking-tight leading-none uppercase">HOJA VERDE</span>
        </div>
      </div>

      <!-- Indicador de Usuario y Salida -->
      <div class="flex items-center gap-4">
        <div class="flex items-center gap-3 border-l border-white/20 pl-4">
          <div class="text-right leading-tight hidden md:block">
            <span class="block text-white text-xs font-bold" x-text="currentUser.nombre"></span>
            <span class="block text-emerald-200 text-[10px] font-bold uppercase tracking-wider" x-text="currentUser.rol"></span>
          </div>
          <div class="w-9 h-9 rounded-xl bg-emerald-800/80 text-white flex items-center justify-center shadow-xs border border-white/10">
            <i data-lucide="user" class="w-4 h-4"></i>
          </div>
          <a href="../../auth/logout.php" class="text-white/70 hover:text-red-300 p-2 rounded-xl transition" title="Cerrar sesión">
            <i data-lucide="log-out" class="w-4 h-4"></i>
          </a>
        </div>
      </div>

    </div>
  </header>
