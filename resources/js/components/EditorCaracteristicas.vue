<template>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h1>Características</h1>
      </div>
    </div>
    <div class="row justify-content-center">
      <div class="col-12">
        <b-card
          v-for="(caracteristica, i) in caracteristicas"
          :key="i"
          class="mb-3"
          body-class="p-0"
          :id="'caracteristica' + caracteristica.id"
        >
          <template v-slot:header>
            <div @click="mostrar(i)" style="cursor: pointer">
              {{ caracteristica.nome || '- nova caracteristica -' }}
              <span class="float-right">
                <button
                  class="btn btn-sm btn-outline-danger mr-2"
                  @click="removerCaracteristica(i)"
                >
                  <fa-icon icon="times" />
                </button>
                <fa-icon :icon="caracteristica.mostrar ? 'chevron-up' : 'chevron-down'" />
              </span>
            </div>
          </template>
          <div
            :class="{ 'dados-caracteristica': true, 'mostrar': !!caracteristica.mostrar }"
            :style="{
              'max-height': caracteristica.mostrar ? 160 + (caracteristica.opcoes.length * 45) + 'px' : 0
            }"
          >
            <b-input-group prepend="Nome">
              <b-input
                @change="salvarCaracteristica(caracteristica)"
                type="text"
                v-model="caracteristica.nome"
              />
            </b-input-group>

            <b-input-group
              prepend="Tipo"
              class="mt-2"
              @change="salvarCaracteristica(caracteristica)"
            >
              <b-select v-model="caracteristica.tipo">
                <b-select-option value="0">Texto</b-select-option>
                <b-select-option value="1">Número</b-select-option>
                <b-select-option value="2">Valor decimal (R$)</b-select-option>
                <b-select-option value="3">Opção</b-select-option>
                <b-select-option value="4">Sim e não</b-select-option>
              </b-select>
            </b-input-group>

            <div class="mt-2" v-if="caracteristica.tipo == 3">
              <div
                v-for="opcao in caracteristica.opcoes"
                :key="opcao.ordem"
                :active="caracteristica.valor_padrao == opcao.ordem"
                class="mt-1"
              >
                <b-input-group>
                  <b-input-group-prepend>
                    <b-input-group-text class="pr-1">
                      <b-form-checkbox
                        :checked="caracteristica.valor_padrao == opcao.ordem"
                        @change="selecionarPadrao(caracteristica, opcao.ordem)"
                      />
                    </b-input-group-text>
                  </b-input-group-prepend>
                  <b-input
                    type="text"
                    v-model="opcao.valor"
                    :ref="'caracteristica' + caracteristica.id + 'opcao' + opcao.ordem"
                    @change="salvarCaracteristica(caracteristica)"
                  />
                  <b-input-group-append>
                    <button
                      class="btn btn-danger"
                      @click="removerOpcao(caracteristica, opcao.ordem)"
                    >
                      <fa-icon icon="times" />
                    </button>
                  </b-input-group-append>
                </b-input-group>
              </div>

              <div class="text-center mt-1">
                <button class="btn btn-primary" @click="adicionarOpcao(caracteristica)">
                  <fa-icon icon="plus" />&nbsp;
                  Adicionar opcao
                </button>
              </div>
            </div>

            <div v-else class="mt-1">
              <b-input-group prepend="Valor padrão">
                <b-input
                  v-if="caracteristica.tipo == 0"
                  type="text"
                  v-model="caracteristica.valor_padrao"
                  @change="salvarCaracteristica(caracteristica)"
                />
                <b-input
                  v-if="caracteristica.tipo == 1"
                  type="number"
                  step="1"
                  placeholder="2000"
                  v-model="caracteristica.valor_padrao"
                  @change="salvarCaracteristica(caracteristica)"
                />
                <b-input
                  v-if="caracteristica.tipo == 2"
                  type="number"
                  step=".01"
                  placeholder="R$ 0.00"
                  v-model="caracteristica.valor_padrao"
                  @change="salvarCaracteristica(caracteristica)"
                />
                <b-select
                  v-if="caracteristica.tipo == 4"
                  v-model="caracteristica.valor_padrao"
                  @change="salvarCaracteristica(caracteristica)"
                >
                  <b-select-option value="1">Sim</b-select-option>
                  <b-select-option value="0">Não</b-select-option>
                </b-select>
              </b-input-group>
            </div>
          </div>
        </b-card>
        <div class="text-center mb-5">
          <button class="btn btn-success" @click="adicionarCaracteristica()">
            <fa-icon icon="plus" />&nbsp;
            Adicionar característica
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["dados"],
  data() {
    return {
      caracteristicas: []
    };
  },
  mounted() {
    if (this.dados) {
      this.caracteristicas = this.dados;
    }
  },
  methods: {
    mostrar(i) {
      this.caracteristicas[i].mostrar = !this.caracteristicas[i].mostrar;
      this.$forceUpdate();
      this.$nextTick(() =>
        this.$scrollTo("#caracteristica" + this.caracteristicas[i].id)
      );
    },
    salvarCaracteristica(caracteristica) {
      axios.post("/caracteristicas", caracteristica).then(r => {
        this.$toasted.success("Salva", {
          theme: "toasted-primary",
          position: "bottom-right",
          duration: 2000
        });
      });
    },
    removerOpcao(caracteristica, ordem) {
      caracteristica.opcoes.splice(ordem, 1);

      caracteristica.opcoes = caracteristica.opcoes.map((opcao, i) => {
        opcao.ordem = i;
        return opcao;
      });

      this.salvarCaracteristica(caracteristica);

      this.$nextTick(() =>
        this.$scrollTo("#caracteristica" + caracteristica.id)
      );
    },
    selecionarPadrao(caracteristica, valor) {
      if (caracteristica.valor_padrao == valor) {
        caracteristica.valor_padrao = null;
      } else {
        caracteristica.valor_padrao = valor;
      }

      this.salvarCaracteristica(caracteristica);
    },
    adicionarOpcao(caracteristica) {
      let ultimaOpcao = caracteristica.opcoes[caracteristica.opcoes.length - 1];

      if (ultimaOpcao && !ultimaOpcao.valor) {
        this.$refs[
          "caracteristica" + caracteristica.id + "opcao" + ultimaOpcao.ordem
        ][0].focus();

        return;
      }

      let opcao = {
        caracteristica_id: caracteristica.id,
        ordem: caracteristica.opcoes.length,
        valor: ""
      };

      caracteristica.opcoes.push(opcao);

      this.$nextTick(() =>
        this.$nextTick(() => {
          this.$refs[
            "caracteristica" + caracteristica.id + "opcao" + opcao.ordem
          ][0].focus();
        })
      );
    },
    adicionarCaracteristica() {
      let data = {
        nome: "",
        valor_padrao: null,
        tipo: 0,
        opcoes: [],
        mostrar: true
      };

      axios.post("/caracteristicas", data).then(r => {
        this.caracteristicas.push(r.data);
        this.$toasted.success("Adicionada", {
          theme: "toasted-primary",
          position: "bottom-right",
          duration: 2000
        });

        this.$nextTick(() => this.$scrollTo("#caracteristica" + r.data.id));
      });
    },
    removerCaracteristica(i) {
      this.caracteristicas[i].mostrar = true;
      axios.delete("/caracteristicas/" + this.caracteristicas[i].id).then(r => {
        if (r.data.status == 1) {
          this.caracteristicas.splice(i, 1);
          this.$toasted.success("Removida", {
            theme: "toasted-primary",
            position: "bottom-right",
            duration: 2000
          });
          this.$forceUpdate();
        } else {
          this.$toasted.error(r.data.error, {
            theme: "toasted-primary",
            position: "bottom-right",
            duration: 5000
          });
        }
      });
    }
  }
};
</script>

<style>
.dados-caracteristica {
  max-height: 0vw;
  overflow: hidden;
  transition: 500ms;
  padding: 0rem 1rem 0rem 1rem;
}
.dados-caracteristica.mostrar {
  padding: 1rem;
}
</style>
