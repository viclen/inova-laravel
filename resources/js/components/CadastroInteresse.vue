<template>
  <div>
    <b-tabs card v-model="tabIndex">
      <b-tab title="Carro" active>
        <b-form-group>
          <label>Marca</label>
          <v-select
            :options="opcoesMarcas"
            v-bind:id="'marca'"
            v-bind:class="{'border border-danger rounded is-invalid': false}"
            v-model="marca"
            @input="selecionarMarca"
          >
            <div slot="no-options">Nenhum resultado.</div>
          </v-select>
        </b-form-group>

        <b-form-group>
          <label>Carro</label>
          <v-select
            :options="opcoesCarros"
            v-bind:id="'carro'"
            v-bind:class="{'border border-danger rounded is-invalid': false}"
            v-model="carro"
          >
            <div slot="no-options">Nenhum resultado.</div>
          </v-select>
        </b-form-group>
      </b-tab>
      <b-tab title="Características">
        <b-input-group prepend="Adicionar">
          <b-form-select v-model="caracteristicaSelecionada" :options="opcoesCaracteristicas" />
          <b-input-group-append>
            <b-button variant="outline-primary" v-on:click="adicionarCaracteristica">
              <fa-icon icon="plus" />
            </b-button>
          </b-input-group-append>
        </b-input-group>
        <div>
          <hr />
          <caracteristica-input
            class="mt-2"
            v-for="caracteristica in caracteristicasSelecionadas"
            :key="caracteristica.index"
            v-model="caracteristica.valor"
            :dados="caracteristicas[caracteristica.index]"
            :remover="() => removerCaracteristica(caracteristica.id)"
          />
        </div>
      </b-tab>
    </b-tabs>
    <b-card-footer class="d-flex justify-content-between">
      <b-button @click="tabIndex--" variant="primary" :disabled="tabIndex == 0">
        <fa-icon icon="arrow-left" />Voltar
      </b-button>

      <b-button @click="tabIndex++" variant="primary" :disabled="tabIndex >= 1">
        <fa-icon icon="arrow-right" />Proximo
      </b-button>
    </b-card-footer>
  </div>
</template>

<script>
export default {
  props: ["caracteristicas", "carros", "marcas"],
  data() {
    return {
      carro: null,
      marca: { id: 0, label: "Todas" },
      opcoesCarros: [],
      opcoesMarcas: [],
      erros: [],
      caracteristicasSelecionadas: [],
      caracteristicaSelecionada: null,
      opcoesCaracteristicas: [],
      tabIndex: 0
    };
  },
  mounted() {
    let opcoesMarcas = [];
    this.marcas.forEach(marca => {
      opcoesMarcas.push({
        id: marca.id,
        label: marca.nome
      });
    });
    this.opcoesMarcas = opcoesMarcas;

    let opcoesCarros = [];
    this.carros.forEach(carro => {
      opcoesCarros.push({
        id: carro.id,
        label: carro.nome + " - " + carro.marca.nome
      });
    });
    this.opcoesCarros = opcoesCarros;

    this.carregarCaracteristicas();
  },
  methods: {
    selecionarMarca(marca) {
      let opcoesCarros = [];
      this.carros.forEach(carro => {
        if (!marca || carro.marca.id == marca.id) {
          opcoesCarros.push({
            id: carro.id,
            label: carro.nome + " - " + carro.marca.nome
          });
        }
      });
      this.opcoesCarros = opcoesCarros;
      if (!marca) {
        this.marca = { id: 0, label: "Todas" };
      }
    },
    adicionarCaracteristica() {
      let opcoesCaracteristicas = [];
      let caracteristicasSelecionadas = [...this.caracteristicasSelecionadas];

      this.caracteristicas.forEach((caracteristica, index) => {
        if (caracteristica.id == this.caracteristicaSelecionada) {
          caracteristicasSelecionadas.push({
            index: index,
            valor: "",
            id: caracteristica.id
          });
        }
      });

      this.caracteristicasSelecionadas = caracteristicasSelecionadas;

      this.carregarCaracteristicas();
    },
    removerCaracteristica(id) {
      let caracteristicasSelecionadas = [];

      this.caracteristicasSelecionadas.forEach(caracteristica => {
        if (caracteristica.id != id) {
          caracteristicasSelecionadas.push(caracteristica);
        }
      });

      this.caracteristicasSelecionadas = caracteristicasSelecionadas;

      this.carregarCaracteristicas();
    },
    carregarCaracteristicas() {
      let opcoesCaracteristicas = [];

      this.caracteristicas.forEach(c => {
        let achou = false;
        for (let i = 0; i < this.caracteristicasSelecionadas.length; i++) {
          if (this.caracteristicasSelecionadas[i].id == c.id) {
            achou = true;
            break;
          }
        }
        if (!achou) {
          opcoesCaracteristicas.push({
            value: c.id,
            text: c.nome
          });
        }
      });

      this.opcoesCaracteristicas = opcoesCaracteristicas;
      this.caracteristicaSelecionada = opcoesCaracteristicas.length
        ? opcoesCaracteristicas[0].value
        : null;
    }
  }
};
</script>

<style>
</style>
