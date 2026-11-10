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
function appGestion(nombre = 'Usuario', rol = 'Empleado', medicamentosIniciales = []) {
  return {
    currentUser: {
      nombre: nombre,
      rol: rol
    },

    searchQuery: '',
    selectedLab: '',
    showInactivos: false,
    showModal: false,
    showModalDetalle: false,
    itemDetalle: null,
    isEditing: false,
    isSaving: false,
    saveError: '',
    statusError: '',
    changingStatusId: null,

    medicamentos: medicamentosIniciales,

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
      this.saveError = '';
      this.form = { id_medicamento: null, codigo_ref: '', codigo_ubicacion: '', nombre: '', laboratorio: '', descripcion: '', precio: '', stock: '' };
      this.showModal = true;
      this.$nextTick(() => lucide.createIcons());
    },

    openModalEditar(item) {
      this.isEditing = true;
      this.saveError = '';
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

    async guardarMedicamento() {
      if (this.isSaving) return;
      this.isSaving = true;
      this.saveError = '';

      try {
        const datos = new URLSearchParams();
        if (this.isEditing) {
          datos.set('id_medicamento', this.form.id_medicamento);
        }
        ['codigo_ref', 'codigo_ubicacion', 'nombre', 'laboratorio', 'descripcion', 'precio', 'stock']
          .forEach(campo => datos.set(campo, this.form[campo] ?? ''));
        const respuesta = await fetch(this.isEditing ? 'editar.php' : 'crear.php', {
          method: 'POST',
          headers: { 'Accept': 'application/json' },
          body: datos
        });
        let resultado;
        try {
          resultado = await respuesta.json();
        } catch {
          throw new Error('El servidor devolvió una respuesta inválida.');
        }
        if (!respuesta.ok || !resultado.success) {
          throw new Error(resultado.message || 'No se pudo guardar el medicamento.');
        }

        const fueEdicion = this.isEditing;
        if (this.isEditing) {
          const index = this.medicamentos.findIndex(m => m.id_medicamento === resultado.medicamento.id_medicamento);
          if (index === -1) {
            throw new Error('El medicamento actualizado no está en la lista.');
          }
          this.medicamentos.splice(index, 1, resultado.medicamento);
        } else {
          this.medicamentos.push(resultado.medicamento);
        }
        this.showModal = false;
        this.$nextTick(() => lucide.createIcons());

        // Aviso nativo del navegador
        setTimeout(() => {
          if (fueEdicion) {
            alert('¡Medicamento editado exitosamente!');
          } else {
            alert('¡Medicamento agregado exitosamente!');
          }
        }, 80);
      } catch (error) {
        this.saveError = error.message || 'No se pudo guardar el medicamento.';
      } finally {
        this.isSaving = false;
      }
    },

    async toggleBorradoLogico(item) {
      const accion = item.estado === 1 ? 'inactivar' : 'restaurar';
      if (this.changingStatusId !== null || !confirm(`¿Estás seguro de que deseas ${accion} el medicamento "${item.nombre}"?`)) {
        return;
      }

      this.changingStatusId = item.id_medicamento;
      this.statusError = '';
      try {
        const datos = new URLSearchParams({
          id_medicamento: String(item.id_medicamento),
          estado: String(item.estado === 1 ? 0 : 1)
        });
        const respuesta = await fetch('eliminar.php', {
          method: 'POST',
          headers: { 'Accept': 'application/json' },
          body: datos
        });
        let resultado;
        try {
          resultado = await respuesta.json();
        } catch {
          throw new Error('El servidor devolvió una respuesta inválida.');
        }
        if (!respuesta.ok || !resultado.success) {
          throw new Error(resultado.message || 'No se pudo cambiar el estado del medicamento.');
        }

        const index = this.medicamentos.findIndex(m => m.id_medicamento === resultado.medicamento.id_medicamento);
        if (index === -1) {
          throw new Error('El medicamento actualizado no está en la lista.');
        }
        this.medicamentos.splice(index, 1, resultado.medicamento);
        this.$nextTick(() => lucide.createIcons());

        // Aviso nativo del navegador
        setTimeout(() => {
          if (resultado.medicamento.estado === 0) {
            alert('¡Medicamento inhabilitado exitosamente!');
          } else {
            alert('¡Medicamento restaurado exitosamente!');
          }
        }, 80);
      } catch (error) {
        this.statusError = error.message || 'No se pudo cambiar el estado del medicamento.';
      } finally {
        this.changingStatusId = null;
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
