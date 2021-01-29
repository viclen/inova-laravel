<template>
  <div class="justify-content-center w-100">
    <div class="row">
      <div class="col-lg-6 mb-3">
        <h4>
          Ordem de Prioridade
          <span class="float-right text-right">
            <button
              class="btn btn-secondary btn-sm"
              v-if="!edicaoRegras"
              v-on:click="editarRegras()"
            >
              <fa-icon icon="edit" />
            </button>
            <button class="btn btn-primary btn-sm" v-if="edicaoRegras" v-on:click="salvarRegras()">
              <fa-icon icon="save" />
            </button>
            <button class="btn btn-danger btn-sm" v-if="edicaoRegras" v-on:click="cancelarRegras()">
              <fa-icon icon="times" />
            </button>
          </span>
        </h4>
        <ul class="list-group">
          <draggable
            v-model="ordem"
            group="people"
            @start="drag=true"
            @end="drag=false"
            v-if="edicaoRegras"
          >
            <li class="list-group-item list-group-item-action" v-for="(regra,i) in ordem" :key="i">
              <fa-icon icon="bars" class="cursor-pointer" />
              <span class="ml-2">{{ regra.nome }}</span>

              <button
                class="btn btn-dark btn-sm float-right ml-1"
                v-on:click="mover(i, i+1)"
                v-bind:disabled="i >= ordem.length-1"
              >
                <fa-icon icon="chevron-down" />
              </button>
              <button
                class="btn btn-dark btn-sm float-right"
                v-on:click="mover(i, i-1)"
                v-bind:disabled="i <= 0"
              >
                <fa-icon icon="chevron-up" />
              </button>
            </li>
          </draggable>
          <template v-else>
            <li class="list-group-item list-group-item-action" v-for="(regra,i) in ordem" :key="i">
              <span class="ml-2">{{ regra.nome }}</span>
            </li>
          </template>
        </ul>
      </div>
      <div class="col-lg-6">
        <h4>Porcentagem do valor</h4>
        <b-input-group>
          <div class="input-group-prepend">
            <input type="number" class="input-group-text" v-model="porcentagem" style="width:80px" v-on:change="salvarPorcentagem()" />
          </div>
          <div class="form-control">
            <b-form-input
              type="range"
              min="0"
              max="100"
              v-model="porcentagem"
              @change="salvarPorcentagem()"
            ></b-form-input>
          </div>
        </b-input-group>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["dados"],
  data() {
    return {
      ordem: [],
      porcentagem: 0,
      edicaoRegras: false,
      ordemSalva: []
    };
  },
  mounted() {
    this.dados.forEach(item => {
      if (item.grupo == "ordem") {
        this.ordem.push(item);
      } else if (item.grupo == "valor") {
        this.porcentagem = item.valor;
      }
    });

    this.ordem.sort((a, b) => {
      return b.valor - a.valor;
    });

    this.ordemSalva = JSON.parse(JSON.stringify(this.ordem));
  },
  methods: {
    mover(indice, proximo) {
      let aux = this.ordem[indice];
      this.ordem[indice] = this.ordem[proximo];
      this.ordem[proximo] = aux;
      this.$forceUpdate();
    },
    editarRegras() {
      this.edicaoRegras = true;
    },
    cancelarRegras() {
      this.ordem = JSON.parse(JSON.stringify(this.ordemSalva));
      this.edicaoRegras = false;
    },
    salvarRegras() {
      this.request(
        this.ordem.map((regra, i) => {
          regra.valor = this.ordem.length - i - 1;
          return regra;
        }),
        () => {
          this.ordemSalva = JSON.parse(JSON.stringify(this.ordem));
          this.edicaoRegras = false;
        }
      );
    },
    salvarPorcentagem() {
      this.request({
        grupo: "valor",
        nome: "porcentagem",
        valor: this.porcentagem
      });
    },
    request(data, callback) {
      axios
        .post("/regras", {
          data
        })
        .then(r => {
          if (
            r.data.status == 1 &&
            callback != undefined &&
            typeof callback == "function"
          ) {
            callback();
          }
        })
        .catch(e => {
          console.log(e);
        });
    }
  }
};
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}
</style>


