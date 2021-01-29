<template>
  <div class="justify-content-center w-100">
    <div class="row mb-1">
      <div class="col-md-3">
        <a
          v-if="this.podevoltar !== false"
          v-bind:href="'/'"
          class="btn btn-light mb-2"
        >
          <fa-icon icon="arrow-left" />&nbsp;Voltar
        </a>
      </div>
      <div class="col-md-6">
        <div v-if="this.podepesquisar !== false" class="input-group mb-2">
          <input
            type="text"
            class="form-control"
            v-bind:class="{ 'is-invalid': searchInvalid }"
            v-model="pesquisa"
            placeholder="Pesquisar"
            v-on:keyup="(event) => checkEnterPesquisa(event)"
            ref="searchInput"
          />

          <div class="input-group-append">
            <button
              v-show="this.pesquisa"
              class="btn btn-white border-top border-bottom"
              v-on:click="cancelarPesquisa()"
            >
              <fa-icon icon="times" />
            </button>
            <button class="btn btn-success" v-on:click="pesquisar()">
              <fa-icon icon="search" />
            </button>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <a
          v-if="mostrarCadastrar"
          v-bind:href="'/' + controller + 's/create'"
          class="btn btn-success mb-2 float-right"
        >
          <fa-icon icon="plus" />&nbsp;Novo
        </a>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <div
          class="alert alert-secondary"
          role="alert"
          v-if="!dadosTabela.length"
        >
          Nenhum dado.
        </div>

        <table class="table table-responsive table-striped w-100 bg-white">
          <thead class="w-100">
            <tr>
              <template v-for="(coluna, i) in titulosColunas">
                <th
                  v-bind:key="i"
                  v-bind:style="
                    'width: ' +
                    Math.floor(100 / (titulosColunas.length + 1)) +
                    '%'
                  "
                >
                  {{ coluna }}
                </th>
              </template>
              <th v-bind:style="'width: 1%'">Ações</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="(item, i) in dadosTabela">
              <tr
                v-bind:key="i"
                v-bind:class="{
                  'bg-info': highlight && i == 0,
                  'text-white': highlight && i == 0,
                }"
              >
                <template v-for="(coluna, j) in item">
                  <td v-bind:key="j" class="nowrap ellipsis">
                    <fa-icon
                      v-if="coluna === true"
                      icon="check"
                      v-bind:class="{ 'text-success': !(highlight && i == 0) }"
                    />
                    <fa-icon
                      v-else-if="coluna === false"
                      icon="times"
                      v-bind:class="{ 'text-danger': !(highlight && i == 0) }"
                    />
                    <span v-else-if="j == 'telefone'">
                      <a
                        :href="
                          (mobile
                            ? 'https://api.whatsapp.com/send?phone=55'
                            : 'https://web.whatsapp.com/send?phone=55') +
                          soNumeros(coluna)
                        "
                        class="text-success mr-1"
                        style="font-size: 20px"
                        target="_blank"
                      >
                        <fa-icon :icon="['fab', 'whatsapp-square']" />
                      </a>
                      <a :href="'tel:+55' + soNumeros(coluna)" target="_blank">
                        <fa-icon icon="phone-square" style="font-size: 20px" />
                      </a>
                      {{ coluna }}
                    </span>
                    <span v-else>{{ coluna }}</span>
                  </td>
                </template>
                <td v-bind:key="i">
                  <div class="dropdown" v-if="n_acoes > 1">
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
                        v-if="mostrarVer"
                        class="dropdown-item text-success"
                        v-on:click="verRegistro(i)"
                      >
                        <fa-icon icon="eye" />&nbsp;Ver
                      </button>
                      <button
                        v-if="mostrarEditar"
                        class="dropdown-item text-dark"
                        v-on:click="editarRegistro(i)"
                      >
                        <fa-icon icon="edit" />&nbsp;Editar
                      </button>
                      <button
                        v-if="mostrarExcluir"
                        class="dropdown-item text-danger"
                        v-on:click="excluirRegistro(i)"
                      >
                        <fa-icon icon="trash" />&nbsp;Excluir
                      </button>
                    </div>
                  </div>

                  <div v-if="n_acoes <= 1">
                    <button
                      v-if="mostrarVer"
                      class="btn btn-sm btn-success text-nowrap"
                      v-on:click="verRegistro(i)"
                    >
                      <fa-icon icon="eye" />&nbsp;Ver
                    </button>
                    <button
                      v-if="mostrarEditar"
                      class="btn btn-sm btn-dark text-nowrap"
                      v-on:click="editarRegistro(i)"
                    >
                      <fa-icon icon="edit" />&nbsp;Editar
                    </button>
                    <button
                      v-if="mostrarExcluir"
                      class="btn btn-sm btn-danger text-nowrap"
                      v-on:click="excluirRegistro(i)"
                    >
                      <fa-icon icon="trash" />&nbsp;Excluir
                    </button>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
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
  </div>
</template>

<script>
export default {
  props: [
    "dados",
    "colunas",
    "controller",
    "podecriar",
    "ver",
    "editar",
    "excluir",
    "mostrarid",
    "podevoltar",
    "podepesquisar",
    "colunasvalor",
    "colunascheck",
    "highlight",
  ],
  data() {
    return {
      titulosColunas: [],
      dadosTabela: [],
      deleteId: -1,
      mostrarModalDelete: false,
      mostrarCadastrar: true,
      mostrarVer: true,
      mostrarEditar: true,
      mostrarExcluir: true,
      pesquisa: "",
      searchInvalid: false,
      n_acoes: 3,
      mobile: false,
    };
  },
  mounted() {
    this.prepararTabela();
    if (this.podecriar === false) {
      this.mostrarCadastrar = false;
    }
    if (this.ver === false) {
      this.mostrarVer = false;
      this.n_acoes--;
    }
    if (this.editar === false) {
      this.mostrarEditar = false;
      this.n_acoes--;
    }
    if (this.excluir === false) {
      this.mostrarExcluir = false;
      this.n_acoes--;
    }
    if (window.location.href.includes("/search/")) {
      var search = window.location.href.split("search/")[1];
      if (search.includes("?")) {
        search = search.split("?")[0];
      }
      this.pesquisa = decodeURI(search);
    }

    this.mobile = window.mobileCheck();

    setTimeout(() => {
      if (
        /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
          navigator.userAgent
        )
      ) {
        this.$refs.searchInput.focus();
      }
    }, 200);
  },
  methods: {
    prepararTabela() {
      if (this.dados) {
        if (this.colunas) {
          this.titulosColunas = this.colunas.map((c) => {
            if (
              !c.includes("_at") &&
              ((this.mostrarid !== false && (c == "id" || c.includes())) ||
                (c != "id" && !c.includes("_id")))
            ) {
              return this.capitalize(c);
            }
          });
        } else {
          for (var property in this.dados[0]) {
            if (
              !property.includes("_at") &&
              ((this.mostrarid !== false &&
                (property == "id" || property.includes("_id"))) ||
                (property != "id" && !property.includes("_id")))
            ) {
              this.titulosColunas.push(this.capitalize(property));
            }
          }
        }
        this.dadosTabela = this.dados.map((dado) => {
          var retorno = {};
          this.titulosColunas.forEach((t) => {
            //   console.log(t);
            if (
              !t.includes("_at") &&
              ((this.mostrarid !== false && t == "id") || t != "id")
            ) {
              if (
                this.colunasvalor &&
                this.colunasvalor.includes(t.toLowerCase())
              ) {
                retorno[t.toLowerCase()] = this.formatarValor(
                  dado[t.toLowerCase()],
                  true,
                  true
                );
              } else if (
                this.colunascheck &&
                this.colunascheck.includes(t.toLowerCase())
              ) {
                retorno[t.toLowerCase()] = dado[t.toLowerCase()] == 1;
              } else {
                retorno[t.toLowerCase()] = dado[t.toLowerCase()];
              }
            }
          });
          return retorno;
        });
      }
    },
    capitalize(str) {
      str = str.replace(str.charAt(0), str.charAt(0).toUpperCase());
      return str;
    },
    excluirRegistro(id) {
      this.deleteId = id;
      this.mostrarModalDelete = true;
    },
    confirmDelete() {
      let id = this.deleteId;
      if (id > -1) {
        let url = this.getUrl() + this.dados[id].id;
        axios.delete(url).then((r) => {
          if (r.data.status == "1") {
            this.dadosTabela.splice(id, 1);
            this.dados.splice(id, 1);

            let toast = this.$toasted.success("Apagado com sucesso!", {
              theme: "toasted-primary",
              position: "bottom-right",
              duration: 5000,
            });
          } else {
            let toast = this.$toasted.error("Não foi possível apagar.", {
              theme: "toasted-primary",
              position: "bottom-right",
              duration: 5000,
            });
            // console.log(r);
          }
          this.mostrarModalDelete = false;
          this.deleteId = -1;
        });
      }
    },
    cancelDelete() {
      this.deleteId = -1;
      this.mostrarModalDelete = false;
    },
    editarRegistro(id) {
      window.location = this.getUrl() + this.dados[id].id + "/edit";
    },
    verRegistro(id) {
      window.location = this.getUrl() + this.dados[id].id;
    },
    openPesquisa() {
      this.pesquisaAberta = !this.pesquisaAberta;
    },
    pesquisar() {
      if (this.pesquisa.length) {
        window.location = this.getUrl() + `search/${this.pesquisa}`;
      } else {
        this.searchInvalid = true;
      }
    },
    cancelarPesquisa() {
      if (window.location.href.includes("/search/")) {
        window.location = this.getUrl();
      } else {
        this.pesquisa = "";
        this.searchInvalid = false;
      }
    },
    checkEnterPesquisa(event) {
      if (event.keyCode === 13) {
        this.pesquisar();
      } else if (event.keyCode === 27) {
        this.cancelarPesquisa();
      }
    },
    formatarValor(entrada, colocar00, colocarponto) {
      if (!entrada) {
        return "R$ 0,00";
      }

      entrada = (entrada + "").replace(".", ",");

      var permitido = "1234567890";
      var decimal = 0;
      var saida = "";

      for (let i = 0; i < entrada.length; i++) {
        let c = entrada.charAt(i);
        if (permitido.includes(c) && decimal < 3) {
          saida += c;
          decimal = decimal > 0 ? decimal + 1 : 0;
        }
        if (c == "," && !decimal) {
          saida += c;
          decimal = 1;
        }
      }

      if (colocar00 !== false) {
        if (decimal == 0) {
          saida += ",00";
        } else if (decimal == 1) {
          saida += "00";
        } else if (decimal == 2) {
          saida += "0";
        }
      }

      if (colocarponto !== false) {
        let saidacomponto = "";
        let count = 0;
        for (let i = saida.indexOf(",") - 1; i >= 0; i--) {
          if (count > 2) {
            saidacomponto = "." + saidacomponto;
            count = 0;
          }
          count++;
          saidacomponto = saida.charAt(i) + saidacomponto;
        }
        saida = saidacomponto + "," + saida.split(",")[1];
      }

      return "R$ " + saida;
    },
    soNumeros(entrada) {
      entrada = entrada + "";
      let permitido = "1234567890";
      let saida = "";
      for (let i = 0; i < entrada.length; i++) {
        if (permitido.includes(entrada.charAt(i))) {
          saida += entrada.charAt(i);
        }
      }
      return saida;
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

<style scoped>
.nowrap {
  white-space: nowrap;
}
.cancel-icon {
  position: absolute;
  right: 17%;
  top: 50%;
  transform: translateY(-50%);
}
.ellipsis {
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 150px;
}
.ellipsis:hover {
  max-width: 100%;
}
</style>


