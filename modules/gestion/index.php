<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/conexion.php';

$medicamentos = $pdo->query('SELECT * FROM medicamentos')->fetchAll();
foreach ($medicamentos as &$medicamento) {
    $medicamento['id_medicamento'] = (int) $medicamento['id_medicamento'];
    $medicamento['precio'] = (float) $medicamento['precio'];
    $medicamento['stock'] = (int) $medicamento['stock'];
    $medicamento['estado'] = (int) $medicamento['estado'];
}
unset($medicamento);

$jsonFlags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_THROW_ON_ERROR;
$appGestionArgsJson = json_encode(
    [$_SESSION['username'], $_SESSION['rol'], $medicamentos],
    $jsonFlags
);
$appGestionArgsHtml = htmlspecialchars($appGestionArgsJson, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="es" x-data="appGestion(...<?php echo $appGestionArgsHtml; ?>)" x-cloak>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Medicamentos</title>
  <script src="../../public/js/tailwind.js"></script>
  <script defer src="../../public/js/alpine.min.js"></script>
  <script src="../../public/js/lucide.min.js"></script>
  <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body class="bg-[#F9FBF9] min-h-screen text-slate-800 flex flex-col">

  <!-- TOP BAR -->
  <header class="bg-[#144517] border-b border-emerald-800/60 shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-4 sm:px-6 h-[70px]">
      
      <div class="flex items-center gap-3">
        <div class="bg-emerald-600 rounded-xl w-10 h-10 flex items-center justify-center flex-shrink-0 text-white shadow-sm">
          <i data-lucide="leaf" class="w-5 h-5"></i>
        </div>
        <div class="flex flex-col">
          <span class="text-emerald-200 text-[10px] font-bold uppercase tracking-widest leading-none mb-1">CENTRO NATURISTA</span>
          <span class="text-white text-lg sm:text-xl font-black tracking-tight leading-none uppercase">HOJA VERDE</span>
        </div>
      </div>

      <div class="flex items-center gap-4">
        <div class="flex items-center gap-3">
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

  <!-- BARRA SUPERIOR DE OPCIONES (Superficial para Gerente) -->
  <template x-if="currentUser.rol === 'Gerente'">
    <nav class="bg-white border-b border-slate-200 shadow-2xs sticky top-[70px] z-40">
      <div class="max-w-7xl mx-auto flex items-center gap-1 px-4 sm:px-6 overflow-x-auto text-xs sm:text-sm">
        <a href="#" @click.prevent class="flex items-center gap-2 px-4 py-3 border-b-2 border-emerald-700 text-emerald-800 font-bold whitespace-nowrap">
          <i data-lucide="package" class="w-4 h-4"></i>
          <span>Inventario</span>
        </a>
        <a href="#" @click.prevent class="flex items-center gap-2 px-4 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition whitespace-nowrap cursor-pointer">
          <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
          <span>Reportes</span>
        </a>
        <a href="#" @click.prevent class="flex items-center gap-2 px-4 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition whitespace-nowrap cursor-pointer">
          <i data-lucide="users" class="w-4 h-4"></i>
          <span>Usuarios</span>
        </a>
        <a href="#" @click.prevent class="flex items-center gap-2 px-4 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition whitespace-nowrap cursor-pointer">
          <i data-lucide="landmark" class="w-4 h-4"></i>
          <span>Tasa BCV</span>
        </a>
      </div>
    </nav>
  </template>

  <!-- CONTENIDO -->
  <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 space-y-6">

    <!-- Tarjetas de Métricas -->
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center gap-3.5">
        <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center flex-shrink-0">
          <i data-lucide="package" class="w-5 h-5"></i>
        </div>
        <div>
          <span class="text-xs text-slate-500 font-medium block">Total Activos</span>
          <span class="text-xl sm:text-2xl font-black text-slate-800" x-text="medicamentos.filter(m => m.estado === 1).length"></span>
        </div>
      </div>

      <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center gap-3.5">
        <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
          <i data-lucide="alert-triangle" class="w-5 h-5"></i>
        </div>
        <div>
          <span class="text-xs text-slate-500 font-medium block">Stock Bajo (&lt; 20)</span>
          <span class="text-xl sm:text-2xl font-black text-amber-600" x-text="medicamentos.filter(m => m.estado === 1 && m.stock < 20).length"></span>
        </div>
      </div>

      <!-- Tarjeta 3 (Gerente: Inactivos | Empleado: Laboratorios) -->
      <template x-if="currentUser.rol === 'Gerente'">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center gap-3.5">
          <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center flex-shrink-0">
            <i data-lucide="archive" class="w-5 h-5"></i>
          </div>
          <div>
            <span class="text-xs text-slate-500 font-medium block">Inactivos</span>
            <span class="text-xl sm:text-2xl font-black text-slate-600" x-text="medicamentos.filter(m => m.estado === 0).length"></span>
          </div>
        </div>
      </template>
      <template x-if="currentUser.rol === 'Empleado'">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center gap-3.5">
          <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
            <i data-lucide="flask-conical" class="w-5 h-5"></i>
          </div>
          <div>
            <span class="text-xs text-slate-500 font-medium block">Laboratorios</span>
            <span class="text-xl sm:text-2xl font-black text-slate-800" x-text="[...new Set(medicamentos.filter(m => m.estado === 1).map(m => m.laboratorio))].length"></span>
          </div>
        </div>
      </template>

      <!-- Tarjeta 4 (Gerente: Valor Almacén | Empleado: Estanterías Ocupadas) -->
      <template x-if="currentUser.rol === 'Gerente'">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center gap-3.5">
          <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-800 flex items-center justify-center flex-shrink-0">
            <i data-lucide="circle-dollar-sign" class="w-5 h-5"></i>
          </div>
          <div>
            <span class="text-xs text-slate-500 font-medium block">Valor Almacén ($)</span>
            <span class="text-xl sm:text-2xl font-black text-emerald-700" x-text="'$' + valorTotalInventario.toFixed(2)"></span>
          </div>
        </div>
      </template>
      <template x-if="currentUser.rol === 'Empleado'">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center gap-3.5">
          <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
            <i data-lucide="map-pin" class="w-5 h-5"></i>
          </div>
          <div>
            <span class="text-xs text-slate-500 font-medium block">Estantes en Uso</span>
            <span class="text-xl sm:text-2xl font-black text-slate-800" x-text="[...new Set(medicamentos.filter(m => m.estado === 1).map(m => m.codigo_ubicacion))].length"></span>
          </div>
        </div>
      </template>
    </section>

    <!-- Toolbar CRUD -->
    <section class="space-y-4">
      <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 max-w-xl">
          <div class="relative flex-1">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
            <input x-model="searchQuery" type="text" placeholder="Buscar medicamento, código o ubicación…"
              class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20 outline-none transition bg-white">
          </div>
          <select x-model="selectedLab" class="px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:border-emerald-700 outline-none bg-white text-slate-600 cursor-pointer">
            <option value="">Todos los laboratorios</option>
            <template x-for="lab in listaLaboratorios" :key="lab">
              <option :value="lab" x-text="lab"></option>
            </template>
          </select>
        </div>

        <div class="flex items-center gap-2 flex-wrap justify-end">
          <!-- Botón PDF (Solo Gerente) -->
          <template x-if="currentUser.rol === 'Gerente'">
            <a href="../../reports/reporte.php" target="_blank"
              class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition shadow-xs">
              <i data-lucide="file-text" class="w-4 h-4 text-emerald-400"></i>
              <span>Reporte PDF</span>
            </a>
          </template>

          <!-- Inhabilitados (Solo Gerente) -->
          <template x-if="currentUser.rol === 'Gerente'">
            <button @click="showInactivos = !showInactivos"
              class="flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold hover:bg-slate-100 transition"
              :class="showInactivos ? 'bg-amber-50 text-amber-900 border-amber-300' : 'bg-white text-slate-700'">
              <i data-lucide="archive" class="w-4 h-4"></i>
              <span x-text="showInactivos ? 'Ver Activos' : 'Inhabilitados (' + medicamentos.filter(m => m.estado === 0).length + ')'"></span>
            </button>
          </template>

          <!-- Botón Nuevo Medicamento (Ambos roles) -->
          <button @click="openModalCrear()"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#1B5E20] hover:bg-[#144517] text-white text-xs font-bold transition shadow-xs cursor-pointer">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Nuevo Medicamento</span>
          </button>
        </div>
      </div>

      <div x-show="statusError" x-text="statusError" role="alert" class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700"></div>

      <!-- Tabla CRUD -->
      <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs sm:text-sm">
            <thead class="bg-emerald-50/70 border-b border-emerald-100 text-slate-600 font-bold uppercase text-[11px] tracking-wider">
              <tr>
                <th class="px-4 py-3.5">Código</th>
                <th class="px-4 py-3.5">Ubicación</th>
                <th class="px-4 py-3.5">Medicamento</th>
                <th class="px-4 py-3.5">Laboratorio</th>
                <th class="px-4 py-3.5 text-center">Stock</th>
                <th class="px-4 py-3.5 text-right">Precio ($)</th>
                <th class="px-4 py-3.5 text-center">Estado</th>
                <th class="px-4 py-3.5 text-center">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <template x-for="item in medicamentosFiltrados" :key="item.id_medicamento">
                <tr class="hover:bg-slate-50/60 transition">
                  <td class="px-4 py-3 font-mono font-bold text-emerald-900" x-text="item.codigo_ref"></td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1 font-mono font-bold text-[11px] bg-slate-100 text-slate-700 px-2.5 py-1 rounded-md border border-slate-200" x-text="item.codigo_ubicacion"></span>
                  </td>
                  <td class="px-4 py-3">
                    <div class="font-bold text-slate-800" x-text="item.nombre"></div>
                    <div class="text-[11px] text-slate-400 max-w-xs truncate" x-text="item.descripcion"></div>
                  </td>
                  <td class="px-4 py-3 text-slate-600 font-medium" x-text="item.laboratorio"></td>
                  <td class="px-4 py-3 text-center">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold"
                          :class="item.stock === 0 ? 'bg-red-100 text-red-700' : (item.stock < 20 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-800')"
                          x-text="item.stock + ' uds'">
                    </span>
                  </td>
                  <td class="px-4 py-3 text-right font-bold text-slate-800" x-text="'$' + Number(item.precio).toFixed(2)"></td>
                  <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                          :class="item.estado === 1 ? 'bg-green-100 text-green-800' : 'bg-slate-200 text-slate-600'"
                          x-text="item.estado === 1 ? 'Activo' : 'Inactivo'">
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-1">
                      <!-- Botón Detalle (Ambos roles) -->
                      <button @click="verDetalle(item)" class="p-1.5 rounded-lg text-slate-500 hover:text-emerald-800 hover:bg-emerald-50 transition cursor-pointer" title="Ver detalle">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                      </button>

                      <!-- Acciones exclusivas del Gerente -->
                      <template x-if="currentUser.rol === 'Gerente'">
                        <div class="flex items-center gap-1">
                          <button @click="openModalEditar(item)" class="p-1.5 rounded-lg text-emerald-800 hover:bg-emerald-50 transition cursor-pointer" title="Editar">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                          </button>
                          <button x-show="item.estado === 1" @click="toggleBorradoLogico(item)" class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 transition cursor-pointer" title="Inactivar">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                          </button>
                          <button x-show="item.estado === 0" @click="toggleBorradoLogico(item)" class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition cursor-pointer" title="Restaurar">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                          </button>
                        </div>
                      </template>
                    </div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
        <div x-show="medicamentosFiltrados.length === 0" class="p-8 text-center text-slate-400 text-xs">
          No se encontraron medicamentos coincidentes.
        </div>
      </div>
    </section>

  </main>

  <!-- MODAL: DETALLE DEL MEDICAMENTO -->
  <div 
    x-show="showModalDetalle" 
    x-transition.opacity 
    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4" 
    @click.self="showModalDetalle = false"
  >
    <template x-if="itemDetalle">
      <div 
        class="bg-white rounded-2xl shadow-xl w-full max-w-lg border border-slate-200/90 overflow-hidden" 
        @click.stop
      >
        <!-- Cuerpo superior del modal -->
        <div class="p-6">
          
          <!-- Fila 1: Laboratorio + Código a la izquierda | Badge Activo + Cerrar a la derecha -->
          <div class="flex items-center justify-between gap-4 mb-3">
            <div class="flex items-center gap-2 min-w-0">
              <span 
                class="text-xs font-bold text-emerald-700 uppercase tracking-wider truncate" 
                x-text="itemDetalle.laboratorio"
              ></span>
              <span class="text-slate-300">•</span>
              <span 
                class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md shrink-0" 
                style="font-variant-numeric: normal; font-family: inherit;"
                x-text="itemDetalle.codigo_ref"
              ></span>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
              <span 
                :class="itemDetalle.estado === 1 ? 'bg-emerald-50 text-emerald-700 border-emerald-200/60' : 'bg-slate-100 text-slate-600 border-slate-200'" 
                class="text-xs px-2.5 py-0.5 rounded-full font-medium border inline-flex items-center gap-1.5"
              >
                <span class="w-1.5 h-1.5 rounded-full" :class="itemDetalle.estado === 1 ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                <span x-text="itemDetalle.estado === 1 ? 'Activo' : 'Inactivo'"></span>
              </span>

              <button 
                @click="showModalDetalle = false" 
                class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg p-1 transition cursor-pointer" 
                title="Cerrar"
              >
                <i data-lucide="x" class="w-4 h-4"></i>
              </button>
            </div>
          </div>

          <!-- Fila 2: Icono + Nombre del Medicamento (a todo el ancho disponible) -->
          <div class="flex items-center gap-3.5 mb-6">
            <div class="w-11 h-11 rounded-xl bg-emerald-900/5 border border-emerald-900/10 flex items-center justify-center text-emerald-700 shrink-0">
              <i data-lucide="leaf" class="w-5 h-5"></i>
            </div>
            <h3 
              class="text-xl font-bold text-slate-900 leading-snug" 
              x-text="itemDetalle.nombre || itemDetalle.medicamento"
            ></h3>
          </div>

          <!-- Grid de 3 tarjetas de métricas -->
          <div class="grid grid-cols-3 gap-3 mb-5">
            
            <!-- Ubicación -->
            <div class="bg-slate-50/80 border border-slate-200/80 rounded-xl p-3.5">
              <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-400 uppercase tracking-wide">
                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                <span>Ubicación</span>
              </div>
              <div 
                class="text-base font-bold text-slate-900 mt-1.5" 
                style="font-variant-numeric: normal; font-family: inherit;"
                x-text="itemDetalle.ubicacion || itemDetalle.codigo_ubicacion"
              ></div>
              <div class="text-xs text-slate-500 mt-0.5">Estante físico</div>
            </div>

            <!-- Existencias -->
            <div class="bg-slate-50/80 border border-slate-200/80 rounded-xl p-3.5">
              <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-400 uppercase tracking-wide">
                <i data-lucide="package" class="w-3.5 h-3.5 text-slate-400"></i>
                <span>Existencias</span>
              </div>
              <div class="mt-1.5 flex items-baseline gap-1">
                <span class="text-base font-bold text-slate-900" x-text="itemDetalle.stock ?? itemDetalle.existencias"></span>
                <span class="text-xs font-medium text-slate-500">uds</span>
              </div>
              <div 
                class="text-xs font-medium mt-0.5"
                :class="(itemDetalle.stock ?? itemDetalle.existencias) > 0 ? 'text-emerald-600' : 'text-rose-600'"
                x-text="(itemDetalle.stock ?? itemDetalle.existencias) > 0 ? 'Disponible' : 'Agotado'"
              ></div>
            </div>

            <!-- Precio Unitario -->
            <div class="bg-slate-50/80 border border-slate-200/80 rounded-xl p-3.5">
              <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-400 uppercase tracking-wide">
                <i data-lucide="tag" class="w-3.5 h-3.5 text-slate-400"></i>
                <span>Precio Unit.</span>
              </div>
              <div 
                class="text-base font-bold text-emerald-600 mt-1.5" 
                x-text="'$' + Number(itemDetalle.precio).toFixed(2)"
              ></div>
              <div class="text-xs text-slate-500 mt-0.5">Ref. USD</div>
            </div>

          </div>

          <!-- Indicaciones Terapéuticas -->
          <div class="bg-slate-50/50 rounded-xl p-3.5 border border-slate-100">
            <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">
              Indicaciones Terapéuticas
            </div>
            <p 
              class="text-sm text-slate-700 leading-relaxed" 
              x-text="itemDetalle.indicaciones || itemDetalle.descripcion || 'Sin indicaciones registradas.'"
            ></p>
          </div>

        </div>

        <!-- Footer integrado a todo el ancho -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200/80 flex items-center justify-between">
          <span class="text-sm font-medium text-slate-600">Valor total en inventario</span>
          <span 
            class="text-base font-bold text-slate-900" 
            x-text="'$' + ((Number(itemDetalle.stock ?? itemDetalle.existencias) || 0) * (Number(itemDetalle.precio) || 0)).toFixed(2)"
          ></span>
        </div>

      </div>
    </template>
  </div>

  <!-- MODAL: NUEVO / EDITAR MEDICAMENTO -->
  <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4" @click.self="showModal = false">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md border border-slate-100 overflow-hidden transform transition-all" @click.stop>
      
      <!-- Cabecera limpia -->
      <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-b from-slate-50/60 to-white">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#1B5E20] border border-emerald-100 flex items-center justify-center shrink-0 shadow-2xs">
            <i data-lucide="package-plus" class="w-5 h-5" x-show="!isEditing"></i>
            <i data-lucide="edit-3" class="w-5 h-5" x-show="isEditing"></i>
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900" x-text="isEditing ? 'Editar Medicamento' : 'Nuevo Medicamento'"></h3>
            <p class="text-[11px] text-slate-400" x-text="isEditing ? 'Modificar datos del inventario' : 'Registrar nuevo producto en stock'"></p>
          </div>
        </div>
        <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <form @submit.prevent="guardarMedicamento()" class="p-5 space-y-4">
        <div x-show="saveError" x-text="saveError" role="alert" class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700"></div>
        
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Código de Referencia</label>
            <input x-model="form.codigo_ref" type="text" required placeholder="Ej: NAT-005"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white text-xs sm:text-sm focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/10 outline-none uppercase font-mono">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Ubicación en Estante</label>
            <input x-model="form.codigo_ubicacion" type="text" required placeholder="Ej: EST-B-01"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white text-xs sm:text-sm focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/10 outline-none uppercase font-mono">
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre comercial</label>
          <input x-model="form.nombre" type="text" required placeholder="Ej: Jarabe de Propóleo y Miel 250ml"
            class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white text-xs sm:text-sm focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/10 outline-none">
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Laboratorio</label>
          <input x-model="form.laboratorio" type="text" required placeholder="Ej: Laboratorios BioNatura"
            class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white text-xs sm:text-sm focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/10 outline-none">
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Descripción / Indicaciones</label>
          <textarea x-model="form.descripcion" rows="2" placeholder="Indicaciones terapéuticas o componentes principales..."
            class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white text-xs sm:text-sm focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/10 outline-none resize-none"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Precio unitario ($)</label>
            <input x-model.number="form.precio" type="number" step="0.01" min="0" required placeholder="0.00"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white text-xs sm:text-sm focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/10 outline-none font-semibold">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Stock disponible</label>
            <input x-model.number="form.stock" type="number" min="0" required placeholder="10"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white text-xs sm:text-sm focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/10 outline-none font-semibold">
          </div>
        </div>

        <div class="flex gap-2.5 pt-3 border-t border-slate-100">
          <button type="button" @click="showModal = false"
            class="flex-1 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition cursor-pointer">
            Cancelar
          </button>
          <button type="submit"
            :disabled="isSaving"
            :class="isSaving ? 'opacity-60 cursor-wait' : ''"
            class="flex-1 py-2.5 rounded-xl bg-[#1B5E20] hover:bg-[#144517] text-white text-xs font-bold transition shadow-xs cursor-pointer">
            <span x-text="isSaving ? 'Guardando…' : (isEditing ? 'Guardar Cambios' : 'Guardar Medicamento')"></span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <footer class="text-center py-4 text-[11px] text-slate-400 border-t border-slate-200 bg-white">
    Centro Naturista Hoja Verde · Algoritmo y Programación II — 2026
  </footer>

  <script src="../../public/js/app.js?v=<?php echo time(); ?>"></script>
</body>
</html>
