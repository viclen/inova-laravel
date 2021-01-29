<template>
  <span>
    <div class="dropdown">
      <button
        class="btn btn-secondary btn-sm dropdown-toggle text-nowrap"
        type="button"
        data-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
      >
        <fa-icon icon="cog" />Ações
      </button>
      <div class="dropdown-menu">
        <button
          v-if="ver"
          class="dropdown-item text-success"
          v-on:click="verRegistro()"
          type="button"
        >
          <fa-icon icon="eye" />&nbsp;Ver
        </button>
        <button
          v-if="editar"
          class="dropdown-item text-dark"
          v-on:click="editarRegistro()"
          type="button"
        >
          <fa-icon icon="edit" />&nbsp;Editar
        </button>
        <button
          v-if="excluir"
          class="dropdown-item text-danger"
          v-on:click="excluirRegistro()"
          type="button"
        >
          <fa-icon icon="trash" />&nbsp;Excluir
        </button>
      </div>
    </div>

    <b-modal
      v-model="mostrarModalDelete"
      header-class="d-none"
      centered
      no-close-on-backdrop
      hide-backdrop
      hide-header-close
    >
      <div class="d-block">
        <h3>Tem certeza que deseja excluir o registro?</h3>
      </div>
      <template v-slot:modal-footer>
        <div class="w-100">
          <b-button
            class="float-right"
            variant="outline-secondary"
            @click="cancelDelete()"
          >
            <fa-icon icon="times" />&nbsp;Cancelar
          </b-button>
          <b-button
            class="float-right mr-2"
            variant="outline-danger"
            @click="confirmDelete()"
          >
            <fa-icon icon="trash" />&nbsp;Excluir
          </b-button>
        </div>
      </template>
    </b-modal>
  </span>
</template>

<script>
export default {
  props: ["ver", "editar", "excluir", "controller", "id"],
  data() {
    return {
      mostrarModalDelete: false,
    };
  },
  mounted() {
  },
  methods: {
    excluirRegistro() {
      this.mostrarModalDelete = true;
    },
    confirmDelete() {
      let url = this.getUrl() + this.id;
      axios.delete(url).then((r) => {
        if (r.data.status == "1") {
          window.location = window.location.href;

          this.$toasted.success("Apagado com sucesso!", {
            theme: "toasted-primary",
            position: "bottom-right",
            duration: 5000,
          });
        } else {
          console.log(r);
          this.$toasted.error("Não foi possível apagar.", {
            theme: "toasted-primary",
            position: "bottom-right",
            duration: 5000,
          });
        }
        this.mostrarModalDelete = false;
      });
    },
    cancelDelete() {
      this.mostrarModalDelete = false;
    },
    editarRegistro() {
      window.location = this.getUrl() + this.id + "/edit";
    },
    verRegistro() {
      window.location = this.getUrl() + this.id;
    },
    getUrl() {
      if (this.controller.endsWith("s")) {
        return "/" + this.controller + "/";
      } else {
        return "/" + this.controller + "s/";
      }
    },
  },
};
</script>

<style>
</style>
