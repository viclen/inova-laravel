<template>
  <div>
    <b-input-group>
      <b-input-group-prepend class="text-capitalize">
        <b-input-group-text>{{ dados.nome }}</b-input-group-text>
      </b-input-group-prepend>

      <b-form-select
        v-if="comparadores.length > 0"
        v-model="comparador"
        :options="comparadores"
        v-on:input="updateValue()"
      />

      <b-form-input
        type="text"
        v-if="dados.tipo == 0"
        v-model="valor"
        v-on:input="updateValue()"
      />

      <b-form-input
        type="number"
        v-if="dados.tipo == 1"
        v-model="valor"
        v-on:input="updateValue()"
      />

      <input
        type="text"
        placeholder="R$ 00.000,00"
        class="form-control"
        v-on:input="(e) => formatarValor(e.target.value)"
        v-if="dados.tipo == 2"
        v-model="valor"
      />

      <b-form-select
        v-if="dados.tipo == 3 || dados.tipo == 4"
        v-model="valor"
        :options="opcoes"
        v-on:input="updateValue()"
      />

      <b-input-group-append>
        <b-button variant="outline-danger" v-on:click="remover">
          <fa-icon icon="times" />
        </b-button>
      </b-input-group-append>
    </b-input-group>

    <div v-if="valor === ''" class="text-danger p-1">
      Por favor, complete esse campo
    </div>
  </div>
</template>

<script>
export default {
  props: ["dados", "remover", "value", "mostrarcomparador"],
  data() {
    return {
      valor: this.value ? this.value.valor || "" : null,
      opcoes: [],
      comparadores: [],
      comparador: this.value ? this.value.comparador || "=" : "=",
    };
  },
  watch: {
    value: function () {
      if (this.value) {
        this.valor = this.value.valor || "";
        this.comparador = this.value.comparador || "=";

        if (this.dados.tipo == 2) {
          this.formatarValor(this.value.valor, false);
        }
      }
    },
  },
  mounted() {
    let opcoes = [];

    if (this.dados.tipo == 0) {
      this.comparadores = [
        { value: "<", text: "Começa com" },
        { value: ">", text: "Termina em" },
        { value: "=", text: "Igual a" },
        { value: "~", text: "Contém" },
      ];
    } else if (this.dados.tipo == 1) {
      this.comparadores = [
        { value: "<", text: "Menor que" },
        { value: ">", text: "Maior que" },
        { value: "=", text: "Igual a" },
        { value: "~", text: "Em torno de" },
      ];
    } else if (this.dados.tipo == 2) {
      this.comparadores = [
        { value: "<", text: "Menor que" },
        { value: ">", text: "Maior que" },
        { value: "=", text: "Igual a" },
        { value: "~", text: "Em torno de" },
      ];
    } else if (this.dados.tipo == 3) {
      this.dados.opcoes.forEach((opcao) => {
        opcoes.push({
          value: opcao.ordem,
          text: opcao.valor,
        });
      });
      this.valor = this.value.valor || opcoes[0].value;
    } else if (this.dados.tipo == 4) {
      opcoes = [
        {
          value: 1,
          text: "Sim",
        },
        {
          value: 0,
          text: "Não",
        },
      ];
      this.valor = this.value.value || opcoes[0].value;
    }

    if (this.mostrarcomparador === false) {
      this.comparadores = [];
    }

    this.opcoes = opcoes;

    this.updateValue();
  },
  methods: {
    updateValue() {
      let valor = this.valor;

      if (this.dados.tipo == 2) {
        valor = (this.valor + "").replace(/[.]/g, "").replace(/[,]/, ".");
      }

      this.$emit("input", {
        valor,
        comparador: this.comparador,
      });
    },
    formatarValor(entrada, update = true) {
      entrada = entrada || this.valor;
      entrada = entrada + "";

      let permitido = "1234567890";
      let decimal = 0;
      let saida = "";

      for (let i = 0; i < entrada.length; i++) {
        let c = entrada.charAt(i);
        if (permitido.includes(c) && decimal < 3) {
          saida += c;
          decimal = decimal > 0 ? decimal + 1 : 0;
        } else if (c == "," && !decimal) {
          saida += c;
          decimal = 1;
        }
      }

      let saidacomponto = "";
      let count = 0;
      for (
        let i = saida.includes(",") ? saida.indexOf(",") - 1 : saida.length - 1;
        i >= 0;
        i--
      ) {
        if (count > 2) {
          saidacomponto = "." + saidacomponto;
          count = 0;
        }
        count++;
        saidacomponto = saida.charAt(i) + saidacomponto;
      }
      saida = saidacomponto + (decimal ? "," + saida.split(",")[1] : "");

      this.valor = saida;

      if (update) {
        this.updateValue();
      }
    },
  },
};
</script>

<style>
</style>
