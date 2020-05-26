<template>
  <div class="container">
    <div class="row mb-1">
      <div class="col">
        <a href="/estoques" class="btn btn-light">
          <fa-icon icon="arrow-left" />&nbsp;
          Voltar
        </a>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <b-card header="Cadastro de Estoque">
          <b-form-group>
            <label>Marca</label>
            <v-select
              :options="opcoesMarcas"
              v-bind:id="'marca'"
              v-bind:class="{'border border-danger rounded is-invalid': false}"
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
              v-bind:id="'carro'"
              v-bind:class="{'border border-danger rounded is-invalid': !!erros.carro}"
              v-model="carro"
            >
              <div slot="no-options">Nenhum resultado.</div>
            </v-select>
            <span class="invalid-feedback">{{ erros.carro }}</span>
          </b-form-group>
          <hr />
          <h4>Caracteristicas</h4>
          <div
            v-if="caracteristicasSelecionadas.length == 0"
          >Nenhuma característica selecionada. O carro será financiado? Automático? Qual o ano ou valor?</div>
          <div>
            <caracteristica-input
              class="mt-2"
              v-for="caracteristica in caracteristicasSelecionadas"
              :key="caracteristica.id"
              v-model="caracteristica.valor"
              :dados="caracteristica"
              :mostrarcomparador="false"
              :remover="() => removerCaracteristica(caracteristica.id)"
            />
          </div>
          <b-dropdown
            variant="dark"
            menu-class="w-100"
            toggle-class="d-flex justify-content-between align-items-center"
            class="w-100 mt-2"
          >
            <template v-slot:button-content>
              <fa-icon icon="plus" />Adicionar caracteristica
            </template>

            <b-dropdown-item
              class="text-capitalize"
              v-for="opcao in opcoesCaracteristicas"
              :key="opcao.id"
              v-on:click="() => adicionarCaracteristica(opcao)"
            >{{ opcao.nome }}</b-dropdown-item>
          </b-dropdown>

          <b-form-group class="mt-3">
            <label>Observações</label>
            <b-textarea v-model="observacoes" />
          </b-form-group>

          <div class="text-center">
            <b-button variant="primary" @click="salvar">
              <fa-icon icon="save" />&nbsp;
              Salvar
            </b-button>
          </div>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["caracteristicas", "carros", "marcas", "dados"],
  data() {
    return {
      opcoesMarcas: [],
      erros: [],
      opcoesCarros: [],
      opcoesCaracteristicas: [],
      marca: { id: 0, label: "Todas" },
      carro: null,
      caracteristicasSelecionadas: [],
      observacoes: ""
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
    this.selecionarMarca(null);

    if (this.dados) {
      this.dados.caracteristicas.forEach(car => {
        let caracteristica = this.getCaracteristica(car.caracteristica_id);
        this.caracteristicasSelecionadas.push({
          ...caracteristica,
          valor: {
            valor: car.valor,
            comparador: "="
          }
        });
      });

      this.observacoes = this.dados.observacoes;
      for (const i in this.opcoesCarros) {
        const carro = this.opcoesCarros[i];
        if (carro.id == this.dados.carro_id) {
          this.carro = { ...carro };
        }
      }

      this.carregarCaracteristicas();
    }
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
      this.marca = marca;
      if (!marca) {
        this.marca = { id: 0, label: "Todas" };
      }
    },
    adicionarCaracteristica(caracteristica) {
      let opcoesCaracteristicas = [];
      let caracteristicasSelecionadas = [...this.caracteristicasSelecionadas];

      caracteristica.valor = {
        valor: caracteristica.valor_padrao || "",
        comparador: "="
      };
      caracteristicasSelecionadas.push({ ...caracteristica });

      this.caracteristicasSelecionadas = caracteristicasSelecionadas;

      this.carregarCaracteristicas();
    },
    removerCaracteristica(id) {
      let caracteristicasSelecionadas = [];

      this.caracteristicasSelecionadas.forEach(caracteristica => {
        if (caracteristica.id != id) {
          caracteristicasSelecionadas.push({ ...caracteristica });
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
          opcoesCaracteristicas.push({ ...c });
        }
      });

      this.opcoesCaracteristicas = opcoesCaracteristicas;
    },
    salvar() {
      if (!this.carro) {
        this.erros.carro = "Por favor, selecione o carro";
        this.$forceUpdate();
        return;
      }

      let dados = {
        caracteristicas: this.caracteristicasSelecionadas.map(
          ({ id, valor }) => ({ id, valor: valor.valor })
        ),
        observacoes: this.observacoes,
        carro_id: this.carro.id
      };

      if (this.dados) {
        dados.id = this.dados.id;
      }

      let url = "/estoques";
      axios.post(url, dados).then(r => {
        if (r.data.status == "1") {
          let toast = this.$toasted.success("Estoque salvo!", {
            theme: "toasted-primary",
            position: "bottom-right",
            duration: 5000
          });

          setTimeout(() => {
            window.location = "/estoques/" + r.data.estoque.id;
          }, 500);
        } else {
          let toast = this.$toasted.error("Houve algum erro.", {
            theme: "toasted-primary",
            position: "bottom-right",
            duration: 5000
          });
          console.log(r.data);
        }
      });
    },
    getCaracteristica(id) {
      for (const i in this.caracteristicas) {
        const c = this.caracteristicas[i];
        if (c.id == id) {
          return c;
        }
      }
    },
    getValorCaracteristica(caracteristica, valor) {
      if (caracteristica.tipo == 3) {
        for (const i in caracteristica.opcoes) {
          const opcao = caracteristica.opcoes[i];
          if (opcao.ordem == valor) {
            return opcao.valor;
          }
        }
      } else if (caracteristica.tipo == 4) {
        return valor ? "Sim" : "Não";
      } else {
        return valor;
      }
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
    }
  }
};
</script>

<style>
</style>
