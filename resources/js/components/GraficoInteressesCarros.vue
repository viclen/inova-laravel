<template>
  <div class="row">
    <div class="col m-auto" v-if="show" ref="graficos">
      <GChart
        style="height: 300px; margin: -20px; margin-left: 0"
        type="PieChart"
        :data="grafico"
        :options="{}"
      />
    </div>
  </div>
</template>

<script>
export default {
  props: ["data", "show"],
  data() {
    return {
      grafico: [],
    };
  },
  mounted() {
    this.grafico = [["Carro", "Interesses"]];

    for (const carro of this.data) {
      const label = `${carro.nome} (${carro.marca})`;
      this.grafico.push([label, parseInt(carro.count_interesses)]);
    }
  },
};
</script>

<style>
</style>
