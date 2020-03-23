<template>
  <div class="justify-content-center w-100">
    <div class="row">
      <div class="col-md-6">
        <ul class="list-group">
          <draggable v-model="ordem" group="people" @start="drag=true" @end="drag=false">
            <li class="list-group-item list-group-item-action" v-for="(regra,i) in ordem" :key="i">
              <fa-icon icon="bars" class="cursor-pointer" />
              <span class="ml-2">{{ regra.nome }}</span>

              <button
                class="btn btn-dark btn-sm float-right"
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
        </ul>
      </div>
      <div class="col-md-6">
        <b-input-group prepend="Porcentagem do valor">
          <div class="form-control">
            <b-form-input type="range" min="0" max="100" v-model="porcentagem"></b-form-input>
          </div>
          <div class="input-group-prepend">
            <input type="number" class="input-group-text" v-model="porcentagem" style="width:80px" />
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
      porcentagem: 0
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
  },
  methods: {
    mover(indice, proximo) {
      let aux = this.ordem[indice];
      this.ordem[indice] = this.ordem[proximo];
      this.ordem[proximo] = aux;
      this.$forceUpdate();
    }
  }
};
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}
</style>


