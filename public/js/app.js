// Inicializar iconos Lucide al cargar la página
document.addEventListener('DOMContentLoaded', () => {
  // Asegurar limpieza de modo oscuro si quedó persistido
  document.documentElement.classList.remove('dark');
  localStorage.removeItem('theme');
  if (window.lucide) {
    lucide.createIcons();
  }
});

// Componente interactivo para la gestión de medicamentos
function appGestion() {
  return {
    currentUser: {
      nombre: 'Carlos Pérez',
      rol: 'Empleado' // Alternar con 'Gerente' según la sesión o prueba
    },

    searchQuery: '',
    selectedLab: '',
    showInactivos: false,
    showModal: false,
    showModalDetalle: false,
    itemDetalle: null,
    isEditing: false,

    // Semilla inicial basada en database.sql
    medicamentos: [
      { id_medicamento: 1, codigo_ref: 'NAT-001', codigo_ubicacion: 'EST-A-01', nombre: 'Extracto de Valeriana 60ml', laboratorio: 'Laboratorios BioNatura', descripcion: 'Sedante y relajante muscular para el insomnio', precio: 4.50, stock: 25, estado: 1 },
      { id_medicamento: 2, codigo_ref: 'NAT-002', codigo_ubicacion: 'EST-A-03', nombre: 'Jarabe de Sábila y Miel 250ml', laboratorio: 'Herbal Medic', descripcion: 'Desinflamatorio gastrointestinal y regenerador gástrico', precio: 6.00, stock: 40, estado: 1 },
      { id_medicamento: 3, codigo_ref: 'NAT-003', codigo_ubicacion: 'EST-B-02', nombre: 'Cápsulas de Moringa 500mg (60 caps)', laboratorio: 'Fitofarma C.A.', descripcion: 'Suplemento antioxidante y energizante natural', precio: 7.50, stock: 18, estado: 1 },
      { id_medicamento: 4, codigo_ref: 'NAT-004', codigo_ubicacion: 'EST-C-01', nombre: 'Infusión de Toronjil y Manzanilla', laboratorio: 'Botánica El Edén', descripcion: 'Digestivo y calmante estomacal tradicional', precio: 2.80, stock: 50, estado: 1 },
    ],

    form: { id_medicamento: null, codigo_ref: '', codigo_ubicacion: '', nombre: '', laboratorio: '', descripcion: '', precio: '', stock: '' },

    get listaLaboratorios() {
      return [...new Set(this.medicamentos.map(m => m.laboratorio))].sort();
    },

    get totalLaboratorios() {
      return new Set(this.medicamentos.filter(m => m.estado === 1).map(m => m.laboratorio)).size;
    },

    get totalUbicaciones() {
      return new Set(this.medicamentos.filter(m => m.estado === 1).map(m => m.codigo_ubicacion)).size;
    },

    get medicamentosFiltrados() {
      const q = this.searchQuery.toLowerCase().trim();
      const targetEstado = this.showInactivos ? 0 : 1;
      return this.medicamentos.filter(m => {
        const matchesEstado = (m.estado === targetEstado);
        const matchesLab = !this.selectedLab || m.laboratorio === this.selectedLab;
        const matchesSearch = !q || m.nombre.toLowerCase().includes(q) || m.codigo_ref.toLowerCase().includes(q) || m.laboratorio.toLowerCase().includes(q) || m.codigo_ubicacion.toLowerCase().includes(q);
        return matchesEstado && matchesLab && matchesSearch;
      });
    },

    get valorTotalInventario() {
      return this.medicamentos
        .filter(m => m.estado === 1)
        .reduce((acc, curr) => acc + (Number(curr.precio) * Number(curr.stock)), 0);
    },

    openModalCrear() {
      this.isEditing = false;
      this.form = { id_medicamento: null, codigo_ref: '', codigo_ubicacion: '', nombre: '', laboratorio: '', descripcion: '', precio: '', stock: '' };
      this.showModal = true;
      this.$nextTick(() => lucide.createIcons());
    },

    openModalEditar(item) {
      this.isEditing = true;
      this.form = { ...item };
      this.showModal = true;
      this.$nextTick(() => lucide.createIcons());
    },

    verDetalle(item) {
      this.itemDetalle = item;
      this.showModalDetalle = true;
      this.$nextTick(() => lucide.createIcons());
      setTimeout(() => lucide.createIcons(), 50);
    },

    guardarMedicamento() {
      if (this.isEditing) {
        const index = this.medicamentos.findIndex(m => m.id_medicamento === this.form.id_medicamento);
        if (index !== -1) {
          this.medicamentos[index] = { ...this.form };
        }
      } else {
        this.medicamentos.push({
          id_medicamento: Date.now(),
          ...this.form,
          estado: 1
        });
      }
      this.showModal = false;
      this.$nextTick(() => lucide.createIcons());
    },

    toggleBorradoLogico(item) {
      const accion = item.estado === 1 ? 'inactivar' : 'restaurar';
      if (confirm(`¿Estás seguro de que deseas ${accion} el medicamento "${item.nombre}"?`)) {
        item.estado = item.estado === 1 ? 0 : 1;
        this.$nextTick(() => lucide.createIcons());
      }
    },

    init() {
      this.$nextTick(() => lucide.createIcons());
      this.$watch('showModal', () => {
        this.$nextTick(() => lucide.createIcons());
        setTimeout(() => lucide.createIcons(), 50);
      });
      this.$watch('showModalDetalle', () => {
        this.$nextTick(() => lucide.createIcons());
        setTimeout(() => lucide.createIcons(), 50);
      });
      this.$watch('showInactivos', () => this.$nextTick(() => lucide.createIcons()));
      this.$watch('currentUser.rol', () => this.$nextTick(() => lucide.createIcons()));
    }
  };
}
