<template>
  <b-card no-body class="mb-3">
    <b-tabs card v-model="tabs">
      <b-tab title="Interesses por Carro">
        <grafico-interesses-carros
          :data="data.grafico_carros"
          :show="tabs === 0"
        />
      </b-tab>
      <b-tab title="Interesses por Marca">
        <grafico-interesses-marcas
          :data="data.grafico_marcas"
          :show="tabs === 1"
        />
      </b-tab>
      <b-tab title="Interesses por Categoria">
        <grafico-interesses-categorias
          :data="data.grafico_categorias"
          :show="tabs === 2"
        />
      </b-tab>
    </b-tabs>
  </b-card>
</template>

<script>
export default {
  props: ["data"],
  data() {
    return {
      tabs: 0,
    };
  },
  mounted() {
    console.log(this.tabs);
  },
};
</script>

<style>
</style>
