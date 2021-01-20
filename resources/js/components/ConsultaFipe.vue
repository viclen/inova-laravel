<template>
  <div class="container">
    <div class="row">
      <div class="col-12 p-3">
        <b-card no-body>
          <b-tabs card v-model="tab">
            <b-tab title="Dados" active>
              <b-form-group v-if="error">
                <span class="text-danger">{{ error }}</span>
              </b-form-group>

              <b-form-group>
                <label>Marca</label>
                <v-select
                  :options="opcoesMarcas"
                  id="marca"
                  v-model="marca"
                  @input="(marca) => selecionarMarca(marca)"
                >
                  <div slot="no-options">Nenhum resultado.</div>
                </v-select>
              </b-form-group>

              <b-form-group>
                <label>Carro</label>
                <v-select
                  :options="opcoesCarros"
                  id="carro"
                  v-model="carro"
                  @input="(Carro) => selecionarCarro(Carro)"
                >
                  <div slot="no-options">Nenhum resultado.</div>
                </v-select>
              </b-form-group>

              <b-form-group>
                <label>Ano</label>
                <v-select
                  :options="opcoesAnos"
                  id="ano"
                  v-model="ano"
                  @input="(Ano) => selecionarAno(Ano)"
                >
                  <div slot="no-options">Nenhum resultado.</div>
                </v-select>
              </b-form-group>

              <b-form-group>
                <label>Modelo</label>
                <v-select :options="opcoesModelos" id="modelo" v-model="modelo">
                  <div slot="no-options">Nenhum resultado.</div>
                </v-select>
                <span v-if="carregandoModelos">Carregando...</span>
              </b-form-group>

              <b-form-group>
                <b-input-group>
                  <b-input-group-prepend class="text-capitalize">
                    <b-input-group-text>Preço</b-input-group-text>
                  </b-input-group-prepend>

                  <b-form-select v-model="comparador" :options="comparadores" />

                  <input
                    type="text"
                    placeholder="R$ 00.000,00"
                    class="form-control"
                    v-on:input="() => (preco = formatarValor(preco))"
                    v-model="preco"
                  />
                </b-input-group>
              </b-form-group>

              <div class="text-center">
                <b-button variant="primary" @click="salvar">
                  <fa-icon icon="dollar-sign" />&nbsp; Consultar
                </b-button>
              </div>
            </b-tab>
            <b-tab title="Resultados">
              <table class="table table-hover table-striped">
                <thead>
                  <tr>
                    <th>Nome</th>
                    <th>Ano</th>
                    <th>Combustível</th>
                    <th>Preço</th>
                    <!-- <th>Ações</th> -->
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="modelo in resultados" :key="modelo.id">
                    <td>
                      {{ modelo.nome }}
                    </td>
                    <td>
                      {{ modelo.ano }}
                    </td>
                    <td>
                      {{ modelo.combustivel }}
                    </td>
                    <td>R$ {{ formatarValor(modelo.preco) }}</td>
                    <!-- <td>
                        <a href="/modelos">
                            <fa-icon icon="eye" />&nbsp; Ver
                        </a>
                    </td> -->
                  </tr>
                </tbody>
              </table>
            </b-tab>
          </b-tabs>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["carros", "marcas"],
  data() {
    return {
      opcoesMarcas: [],
      opcoesCarros: [],
      opcoesAnos: [],
      opcoesModelos: [],
      marca: { id: 0, label: "Todas" },
      carro: null,
      modelo: null,
      ano: { id: 0, label: "Todos" },
      carregandoModelos: false,
      todosModelos: [],
      preco: "",
      comparador: "<",
      comparadores: [
        { value: "<", text: "Menor que" },
        { value: ">", text: "Maior que" },
        { value: "=", text: "Igual a" },
        { value: "~", text: "Em torno de" },
      ],
      error: "",
      resultados: [],
      tab: 0,
    };
  },
  mounted() {
    let opcoesMarcas = [];
    this.marcas.forEach((marca) => {
      opcoesMarcas.push({
        id: marca.id,
        label: marca.nome,
      });
    });
    this.opcoesMarcas = opcoesMarcas;

    let opcoesCarros = [];
    this.carros.forEach((carro) => {
      opcoesCarros.push({
        id: carro.id,
        label: carro.nome + " - " + carro.marca.nome,
      });
    });
    this.opcoesCarros = opcoesCarros;

    this.selecionarMarca(null);
  },
  methods: {
    selecionarMarca(marca) {
      let opcoesCarros = [];

      if (!marca || !marca.id) {
        marca = { id: 0, label: "Todas" };
        opcoesCarros = this.carros.map((carro) => ({
          id: carro.id,
          label: carro.nome + " - " + carro.marca.nome,
        }));
      } else {
        this.carros.forEach((carro) => {
          if (carro.marca.id == marca.id) {
            opcoesCarros.push({
              id: carro.id,
              label: carro.nome + " - " + carro.marca.nome,
            });
          }
        });
      }

      this.opcoesCarros = opcoesCarros;
      this.marca = marca;
      this.carro = null;
    },
    selecionarCarro(carro) {
      if (carro && carro.id) {
        this.carregandoModelos = true;
        axios.get(`/fipe/${carro.id}`).then(({ data }) => {
          const opcoesModelos = [];
          const opcoesAnos = [];

          this.todosModelos = data.filter((modelo) => !!modelo.nome);

          data.forEach((modelo) => {
            if (modelo.nome) {
              opcoesModelos.push({
                id: modelo.id,
                label: `${modelo.nome} - ${modelo.ano}`,
              });

              if (!opcoesAnos.includes(modelo.ano)) {
                opcoesAnos.push(modelo.ano);
              }
            }
          });

          this.opcoesModelos = opcoesModelos;
          this.opcoesAnos = [
            { id: 0, label: "Todos" },
            ...opcoesAnos
              .sort((a, b) => b - a)
              .map((ano) => ({
                label: ano.toString().replace("32000", "Novo"),
                id: ano,
              })),
          ];
          this.modelo = null;
          this.ano = { id: 0, label: "Todos" };
          this.carregandoModelos = false;
        });
      }
    },
    selecionarAno(ano) {
      if (ano && ano.id) {
        this.carregandoModelos = true;

        const opcoesModelos = [];
        const opcoesAnos = [];

        this.todosModelos.forEach((modelo) => {
          if (modelo.ano == ano.id) {
            opcoesModelos.push({
              id: modelo.id,
              label: `${modelo.nome} - ${modelo.ano}`,
            });
          }
        });

        this.opcoesModelos = opcoesModelos;
        this.modelo = null;
        this.carregandoModelos = false;
      } else {
        this.opcoesModelos = this.todosModelos.map((modelo) => ({
          id: modelo.id,
          label: `${modelo.nome} - ${modelo.ano}`,
        }));
      }
    },
    formatarValor(entrada) {
      entrada = entrada.toString();

      var permitido = "1234567890";
      var decimal = 0;
      var saida = "";

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

      return saida;
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
    salvar() {
      axios
        .post("/fipe", {
          modelo: this.modelo && this.modelo.id,
          ano: this.ano && this.ano.id,
          carro: this.carro && this.carro.id,
          marca: this.marca && this.marca.id,
          comparador: this.comparador,
          preco: this.soNumeros(this.preco),
        })
        .then((r) => {
          this.resultados = r.data.slice(0, 50);
          this.tab = 1;
        })
        .catch((e) => {
          if (e.response && e.response.data)
            this.error = e.response.data.message;
          else this.error = "";
        });
    },
  },
};
</script>

<style>
</style>
