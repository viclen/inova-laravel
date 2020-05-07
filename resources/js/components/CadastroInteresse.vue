<template>
  <div @keyup="checkEnter">
    <b-tabs card v-model="tabIndex">
      <!-- interesses -->
      <b-tab title="Interesses" active>
        <b-card
          v-for="(interesse, index) in interesses"
          :key="index"
          :body-class="interesse.mostrar ? '' : 'p-0'"
          :class="{
            'mb-3': true,
            'border': interesse.erro == true,
            'border-danger': interesse.erro == true
          }"
          :id="'interesse_' + index"
        >
          <!-- interesse -->
          <template v-slot:header>
            <div
              @click="() => mostrarInteresse(index)"
              class="d-flex justify-content-between align-items-center"
            >
              <b-button
                v-if="interesses.length > 1"
                variant="outline-danger"
                class="btn-sm"
                @click="() => removerInteresse(index)"
              >
                <fa-icon icon="times" />
              </b-button>
              <span class="text-center text-capitalize" style="flex: 1">
                {{((interesse.carro ? interesse.carro.label + " | " : "" )
                +
                interesse.caracteristicasSelecionadas.map(c => {
                return c.nome;
                }).join(", "))
                ||
                "Nada selecionado"}}
              </span>
              <fa-icon :icon="interesse.mostrar ? 'chevron-up' : 'chevron-down'" />
            </div>
          </template>

          <div
            class="text-danger mb-2"
            v-if="interesse.erro == true"
          >Selecione um carro ou pelo menos uma caracteristica para continuar.</div>

          <div v-if="interesse.mostrar">
            <b-form-group>
              <label>Marca</label>
              <v-select
                :options="opcoesMarcas"
                v-bind:id="'marca'"
                v-bind:class="{'border border-danger rounded is-invalid': false}"
                v-model="interesse.marca"
                @input="(marca) => selecionarMarca(index, marca)"
              >
                <div slot="no-options">Nenhum resultado.</div>
              </v-select>
            </b-form-group>

            <b-form-group>
              <label>Carro</label>
              <v-select
                :options="interesse.opcoesCarros"
                v-bind:id="'carro'"
                v-bind:class="{'border border-danger rounded is-invalid': false}"
                v-model="interesse.carro"
                @input="interesses[0].erro = false"
              >
                <div slot="no-options">Nenhum resultado.</div>
              </v-select>
            </b-form-group>
            <hr />
            <h4>Caracteristicas</h4>
            <div
              v-if="interesse.caracteristicasSelecionadas.length == 0"
            >Nenhuma característica selecionada. O carro será financiado? Automático? Qual o ano ou valor?</div>
            <div>
              <caracteristica-input
                class="mt-2"
                v-for="caracteristica in interesse.caracteristicasSelecionadas"
                :key="caracteristica.id"
                v-model="caracteristica.valor"
                :dados="caracteristica"
                :remover="() => removerCaracteristica(index, caracteristica.id)"
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
                v-for="opcao in interesse.opcoesCaracteristicas"
                :key="opcao.id"
                v-on:click="() => adicionarCaracteristica(index, opcao)"
              >{{ opcao.nome }}</b-dropdown-item>
            </b-dropdown>
            <hr />
            <b-form-group>
              <label>Origem</label>
              <b-form-select v-model="interesse.origem">
                <b-form-select-option
                  v-for="(origem, i) in [
                'Facebook',
                'Whatsapp',
                'Instagram',
                'Loja',
                'Telefone',
                'OLX',
                'Outro'
              ]"
                  :value="i"
                  :key="i"
                >{{ origem }}</b-form-select-option>
              </b-form-select>
            </b-form-group>
            <b-form-group>
              <label>Observações</label>
              <b-form-textarea v-model="interesse.observacoes"></b-form-textarea>
            </b-form-group>
          </div>
          <!-- interesse -->
        </b-card>
        <b-button
          v-if="interesses[interesses.length - 1].carro != null || interesses[interesses.length - 1].caracteristicasSelecionadas.length > 0"
          variant="success"
          class="w-100"
          @click="adicionarInteresse"
        >
          Adicionar Interesse
          &nbsp;
          <fa-icon icon="plus" />
        </b-button>
      </b-tab>
      <!-- interesses -->
      <!-- cliente -->
      <b-tab title="Cliente">
        <h4>Existente?</h4>
        <b-form-group>
          <label>Pesquisar</label>
          <v-select
            :options="opcoesClientes"
            v-bind:id="'cliente'"
            v-bind:class="{'border border-danger rounded is-invalid': false}"
            @input="selecionarCliente"
            v-model="cliente"
            :disabled="cliente == null && (dadosCliente.nome || dadosCliente.telefone) ? true : false"
          >
            <div slot="no-options">Nenhum resultado.</div>
          </v-select>
        </b-form-group>
        <hr />
        <div v-if="erroCliente" class="text-danger">
          <hr />Por favor, selecione o cliente ou complete os dados para continuar.
        </div>
        <div>
          <b-form-group>
            <label>Nome *</label>
            <b-form-input required :readonly="cliente != null" v-model="dadosCliente.nome"></b-form-input>
          </b-form-group>
          <b-form-group>
            <label>Telefone *</label>
            <number-pattern-input
              :required="true"
              :readonly="cliente != null"
              v-model="dadosCliente.telefone"
              pattern="(99) 99999-9999"
            />
          </b-form-group>
          <b-form-group>
            <label>Endereco</label>
            <b-form-input :readonly="cliente != null" v-model="dadosCliente.endereco"></b-form-input>
          </b-form-group>
          <b-form-group>
            <label>Cidade</label>
            <b-form-input :readonly="cliente != null" v-model="dadosCliente.cidade"></b-form-input>
          </b-form-group>
          <b-form-group>
            <label>Email</label>
            <b-form-input :readonly="cliente != null" v-model="dadosCliente.email"></b-form-input>
          </b-form-group>
          <b-form-group>
            <label>CPF</label>
            <number-pattern-input
              :readonly="cliente != null"
              v-model="dadosCliente.cpf"
              pattern="999.999.999-99"
            />
          </b-form-group>
        </div>
      </b-tab>
      <!-- cliente -->
      <!-- troca -->
      <b-tab title="Troca">
        <b-form-group>
          <label>Marca</label>
          <v-select
            :options="opcoesMarcas"
            v-bind:id="'marca'"
            v-bind:class="{'border border-danger rounded is-invalid': false}"
            v-model="troca.marca"
            @input="(marca) => selecionarMarca(troca.index, marca, true)"
          >
            <div slot="no-options">Nenhum resultado.</div>
          </v-select>
        </b-form-group>

        <b-form-group>
          <label>Carro</label>
          <v-select
            :options="troca.opcoesCarros"
            v-bind:id="'carro'"
            v-bind:class="{'border border-danger rounded is-invalid': false}"
            v-model="troca.carro"
          >
            <div slot="no-options">Nenhum resultado.</div>
          </v-select>
        </b-form-group>
        <hr />
        <h4>Caracteristicas</h4>
        <div
          v-if="troca.caracteristicasSelecionadas.length == 0"
        >Nenhuma característica selecionada. O carro será financiado? Automático? Qual o ano ou valor?</div>
        <div>
          <caracteristica-input
            class="mt-2"
            v-for="caracteristica in troca.caracteristicasSelecionadas"
            :key="caracteristica.id"
            v-model="caracteristica.valor"
            :dados="caracteristica"
            :mostrarcomparador="false"
            :remover="() => removerCaracteristica(0, caracteristica.id, true)"
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
            v-for="opcao in troca.opcoesCaracteristicas"
            :key="opcao.id"
            v-on:click="() => adicionarCaracteristica(0, opcao, true)"
          >{{ opcao.nome }}</b-dropdown-item>
        </b-dropdown>
      </b-tab>
      <!-- troca -->
    </b-tabs>
    <b-card-footer class="d-flex justify-content-between">
      <b-button @click="tabIndex--" variant="secondary" :disabled="tabIndex == 0">
        <fa-icon icon="arrow-left" />&nbsp;
        Voltar
      </b-button>

      <b-button variant="primary" v-if="tabIndex >= 2" @click="salvar">
        <fa-icon icon="save" />&nbsp;
        Salvar
      </b-button>

      <b-button
        variant="danger"
        @click="limparDadosCliente"
        v-if="tabIndex == 1 && (dadosCliente.nome || dadosCliente.telefone)"
      >
        <fa-icon icon="times" />&nbsp;
        Limpar
      </b-button>

      <b-button @click="proximaTab" variant="secondary" :disabled="tabIndex >= 2">
        Proximo&nbsp;
        <fa-icon icon="arrow-right" />
      </b-button>
    </b-card-footer>
  </div>
</template>

<script>
export default {
  props: ["caracteristicas", "carros", "marcas", "clientes"],
  data() {
    return {
      cliente: null,
      dadosCliente: {},
      opcoesClientes: [],
      erroCliente: false,
      opcoesMarcas: [],
      erros: [],
      tabIndex: 0,
      interesses: [
        {
          opcoesCarros: [],
          opcoesCaracteristicas: [],
          marca: { id: 0, label: "Todas" },
          carro: null,
          caracteristicasSelecionadas: [],
          mostrar: true,
          origem: 6
        }
      ],
      troca: {
        opcoesCarros: [],
        opcoesCaracteristicas: [],
        marca: { id: 0, label: "Todas" },
        carro: null,
        caracteristicasSelecionadas: []
      }
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

    let opcoesClientes = [];
    this.clientes.forEach(cliente => {
      opcoesClientes.push({
        id: cliente.id,
        label: cliente.nome
      });
    });
    this.opcoesClientes = opcoesClientes;

    this.carregarCaracteristicas(0);
    this.selecionarMarca(0, null);
    this.carregarCaracteristicas(0, true);
    this.selecionarMarca(0, null, true);
  },
  methods: {
    mostrarInteresse(int_index) {
      if (this.interesses[int_index].mostrar) {
        this.interesses[int_index].mostrar = false;
      } else {
        for (const i in this.interesses) {
          this.interesses[i].mostrar = false;
        }
        this.interesses[int_index].mostrar = true;

        this.$nextTick(() =>
          this.$nextTick(() => {
            this.$scrollTo("#interesse_" + int_index);
          })
        );
      }
    },
    proximaTab() {
      if (
        this.tabIndex == 0 &&
        !this.interesses[0].carro &&
        !this.interesses[0].caracteristicasSelecionadas.length
      ) {
        this.interesses[0].erro = true;
        this.$forceUpdate();

        return;
      }

      if (this.tabIndex == 1 && !this.validarCliente()) {
        return;
      }

      this.tabIndex++;
    },
    checkEnter(event) {
      if (event.code == "Enter") {
        event.preventDefault();
        this.proximaTab();
      }
    },
    selecionarMarca(int_index, marca, troca = false) {
      if (troca === true) {
        let opcoesCarros = [];
        this.carros.forEach(carro => {
          if (!marca || carro.marca.id == marca.id) {
            opcoesCarros.push({
              id: carro.id,
              label: carro.nome + " - " + carro.marca.nome
            });
          }
        });
        this.troca.opcoesCarros = opcoesCarros;
        this.troca.marca = marca;
        if (!marca) {
          this.troca.marca = { id: 0, label: "Todas" };
        }
      } else {
        let interesse = this.interesses[int_index];
        let opcoesCarros = [];
        this.carros.forEach(carro => {
          if (!marca || carro.marca.id == marca.id) {
            opcoesCarros.push({
              id: carro.id,
              label: carro.nome + " - " + carro.marca.nome
            });
          }
        });
        interesse.opcoesCarros = opcoesCarros;
        interesse.marca = marca;
        if (!marca) {
          interesse.marca = { id: 0, label: "Todas" };
        }
        this.interesses[int_index] = interesse;
      }
    },
    selecionarCliente(cliente) {
      if (cliente) {
        for (const i in this.clientes) {
          const c = this.clientes[i];
          if (c.id == cliente.id) {
            this.dadosCliente = c;
            break;
          }
        }
      } else {
        this.dadosCliente = {};
      }
    },
    adicionarCaracteristica(int_index, caracteristica, troca = false) {
      if (troca === true) {
        let opcoesCaracteristicas = [];
        let caracteristicasSelecionadas = [
          ...this.troca.caracteristicasSelecionadas
        ];

        caracteristica.valor = {
          valor: "",
          comparador: "="
        };
        caracteristicasSelecionadas.push(caracteristica);

        this.troca.caracteristicasSelecionadas = caracteristicasSelecionadas;
      } else {
        let interesse = this.interesses[int_index];

        let opcoesCaracteristicas = [];
        let caracteristicasSelecionadas = [
          ...interesse.caracteristicasSelecionadas
        ];

        caracteristica.valor = {
          valor: "",
          comparador: "="
        };
        caracteristicasSelecionadas.push(caracteristica);

        interesse.caracteristicasSelecionadas = caracteristicasSelecionadas;
        this.interesses[int_index] = interesse;
      }
      this.carregarCaracteristicas(int_index, troca);
    },
    removerCaracteristica(int_index, id, troca = false) {
      if (troca === true) {
        let caracteristicasSelecionadas = [];

        this.troca.caracteristicasSelecionadas.forEach(caracteristica => {
          if (caracteristica.id != id) {
            caracteristicasSelecionadas.push(caracteristica);
          }
        });

        this.troca.caracteristicasSelecionadas = caracteristicasSelecionadas;
      } else {
        let interesse = this.interesses[int_index];

        let caracteristicasSelecionadas = [];

        interesse.caracteristicasSelecionadas.forEach(caracteristica => {
          if (caracteristica.id != id) {
            caracteristicasSelecionadas.push(caracteristica);
          }
        });

        interesse.caracteristicasSelecionadas = caracteristicasSelecionadas;
        this.interesses[int_index] = interesse;
      }
      this.carregarCaracteristicas(int_index, troca);
    },
    carregarCaracteristicas(int_index, troca = false) {
      if (troca === true) {
        let opcoesCaracteristicas = [];

        this.caracteristicas.forEach(c => {
          let achou = false;
          for (
            let i = 0;
            i < this.troca.caracteristicasSelecionadas.length;
            i++
          ) {
            if (this.troca.caracteristicasSelecionadas[i].id == c.id) {
              achou = true;
              break;
            }
          }
          if (!achou) {
            opcoesCaracteristicas.push(c);
          }
        });

        this.troca.opcoesCaracteristicas = opcoesCaracteristicas;
      } else {
        let interesse = this.interesses[int_index];

        let opcoesCaracteristicas = [];

        this.caracteristicas.forEach(c => {
          let achou = false;
          for (
            let i = 0;
            i < interesse.caracteristicasSelecionadas.length;
            i++
          ) {
            if (interesse.caracteristicasSelecionadas[i].id == c.id) {
              achou = true;
              break;
            }
          }
          if (!achou) {
            opcoesCaracteristicas.push(c);
          }
        });

        interesse.opcoesCaracteristicas = opcoesCaracteristicas;
        this.interesses[int_index] = interesse;
      }
    },
    adicionarInteresse() {
      this.interesses.push({
        index: this.interesses.length,
        opcoesCarros: [],
        opcoesCaracteristicas: [],
        marca: { id: 0, label: "Todas" },
        carro: null,
        caracteristicasSelecionadas: [],
        mostrar: false,
        origem: 6
      });
      this.selecionarMarca(this.interesses.length - 1, null);
      this.carregarCaracteristicas(this.interesses.length - 1);

      this.$nextTick(() => this.mostrarInteresse(this.interesses.length - 1));
    },
    removerInteresse(int_index) {
      this.interesses.splice(int_index, 1);
      for (const i in this.interesses) {
        this.interesses[i].index = i;
      }
    },
    salvar() {
      let dados = {};

      dados.interesses = this.interesses.map((interesse, i) => {
        let valido = true;

        interesse.caracteristicasSelecionadas.forEach(caracteristica => {
          if (caracteristica.valor === "") {
            valido = false;
            this.interesses[i].erro = true;
            this.tabIndex = 0;
          }
        });

        if (valido && !interesse.carro) {
          valido = false;
          this.interesses[i].erro = true;
          this.tabIndex = 0;
        }

        if (valido) {
          return {
            carro_id: interesse.carro.id,
            caracteristicas: interesse.caracteristicasSelecionadas.map(
              caracteristica => ({
                id: caracteristica.id,
                valor: caracteristica.valor
              })
            ),
            origem: interesse.origem,
            observacoes: interesse.observacoes || ""
          };
        }
      });

      if (this.tabIndex == 0) {
        return;
      }

      if (this.validarCliente()) {
        dados.cliente = this.dadosCliente;
      } else {
        return;
      }

      if (this.troca.carro) {
        dados.troca = {
          carro_id: this.troca.carro.id,
          caracteristicas: this.troca.caracteristicasSelecionadas.map(
            caracteristica => ({
              id: caracteristica.id,
              valor: caracteristica.valor.valor
            })
          ),
          origem: this.troca.origem,
          observacoes: this.troca.observacoes
        };
      }

      console.log(dados);
      let url = "/interesses";
      axios
        .post(url, dados)
        .then(r => {
          if (r.data.status == "1") {
            let toast = this.$toasted.success("Interesses salvos!", {
              theme: "toasted-primary",
              position: "bottom-right",
              duration: 5000
            });
            window.location.href = "/interesses/" + r.data.id;
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
    limparDadosCliente() {
      this.cliente = null;
      this.selecionarCliente(null);
      this.$forceUpdate();
    },
    validarCliente() {
      if (
        !this.cliente &&
        (!this.dadosCliente.nome || !this.dadosCliente.telefone)
      ) {
        this.erroCliente = true;
        this.$forceUpdate();
        this.tabIndex = 1;
        return false;
      }
      this.erroCliente = false;
      this.$forceUpdate();
      return true;
    }
  }
};
</script>

<style>
</style>
