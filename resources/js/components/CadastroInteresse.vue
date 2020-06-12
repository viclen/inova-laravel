<template>
  <div @keyup="checkEnter">
    <b-tabs pills card v-model="tabIndex">
      <!-- interesses -->
      <b-tab title="Interesses" :disabled="resultados !== null || passo != 0" active>
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
      <b-tab title="Cliente" :disabled="resultados !== null || passo != 1">
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
        <div v-if="erroCliente">
          <b-alert
            show
            variant="danger"
          >Por favor, selecione o cliente ou complete os dados para continuar.</b-alert>
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
      <b-tab title="Troca" :disabled="resultados !== null || passo != 2">
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
      <!-- resultado -->
      <b-tab title="Resultados" v-if="resultados !== null">
        <div>
          <b-alert
            v-if="resultados.length"
            variant="success"
            class="text-center"
            dismissible
            show
          >Encontramos os seguintes carros em estoque para os interesses cadastrados!</b-alert>
          <b-alert
            v-else
            variant="danger"
            class="text-center"
            show
            dismissible
          >Nenhum carro encontrado.</b-alert>
        </div>

        <b-card
          v-for="(match, i) in resultados"
          :key="i"
          :header="match.estoque.carro ? match.estoque.carro.nome : '' "
          :header-bg-variant="
            match.prioridade < match.interesse.caracteristicas.length ?
                    'light'
                :
                    'info'
            "
          :header-text-variant="match.prioridade < match.interesse.caracteristicas.length ? 'dark' : 'white'"
          class="mb-2"
        >
          <div class="row mb-1">
            <div class="col">Prioridade</div>
            <div class="col">
              <span
                class="text-secondary"
                v-if="match.prioridade < match.interesse.caracteristicas.length"
              >Baixa</span>
              <span
                class="text-danger"
                v-else-if="match.prioridade > match.interesse.caracteristicas.length * 2"
              >Muito Alta</span>
              <span class="text-dark" v-else>Alta</span>
            </div>
          </div>
          <div
            :class="{
                'row mb-1': true,
                'text-success': match.caracteristicas.includes(`carro`)
            }"
            v-if="match.estoque.carro"
          >
            <div class="col">Carro</div>
            <div class="col">
              <a :href="'/carros/' + match.estoque.carro.id">{{ match.estoque.carro.nome }}</a>
            </div>
          </div>
          <div
            :class="{
                'row mb-1': true,
                'text-success': match.caracteristicas.includes(`marca`)
            }"
            v-if="match.estoque.carro && match.estoque.carro.marca"
          >
            <div class="col">Marca</div>
            <div class="col">
              <a
                :href="'/marcas/' + match.estoque.carro.marca.id"
              >{{ match.estoque.carro.marca.nome }}</a>
            </div>
          </div>
          <div
            v-for="(caracteristica, j) in match.estoque.caracteristicas"
            :class="{
                'row mb-1': true,
                'text-success': match.caracteristicas.replace('[', ' ').replace(']', ' ').replace(',', ' ').includes(` ${caracteristica.caracteristica_id} `)
            }"
            :key="j"
          >
            <div class="col text-capitalize">{{ caracteristica.descricao.nome }}</div>
            <div
              class="col text-capitalize"
            >{{ getValorCaracteristica(getCaracteristica(caracteristica.caracteristica_id), caracteristica.valor) }}</div>
          </div>

          <template v-slot:footer>
            <a :href="'/interesses' + match.interesse_id" class="btn btn-sm btn-secondary">
              <fa-icon icon="eye" />&nbsp;
              Ver
            </a>
            <a :href="'/' + match.interesse_id" class="btn btn-sm btn-success">
              <fa-icon :icon="['fab', 'whatsapp']" />&nbsp;
              Whatsapp
            </a>
            <a :href="'/' + match.interesse_id" class="btn btn-sm btn-primary">
              <fa-icon icon="phone" />&nbsp;
              Ligar
            </a>
          </template>
        </b-card>
      </b-tab>
      <!-- resultado -->
    </b-tabs>
    <b-card-footer class="d-flex justify-content-between">
      <b-button
        @click="() => mudarTab(tabIndex-1)"
        variant="secondary"
        :disabled="tabIndex == 0 || tabIndex > 2"
      >
        <fa-icon icon="arrow-left" />&nbsp;
        Voltar
      </b-button>

      <b-button variant="primary" v-if="tabIndex == 2" @click="salvar">
        <fa-icon icon="save" />&nbsp;
        Salvar
      </b-button>

      <b-button
        variant="danger"
        v-if="tabIndex == 1 && (dadosCliente.nome || dadosCliente.telefone)"
      >
        <fa-icon icon="times" />&nbsp;
        Limpar
      </b-button>

      <b-button @click="() => mudarTab(tabIndex+1)" variant="secondary" :disabled="tabIndex >= 2">
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
      passo: 0,
      resultados: null,
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

        this.$nextTick(() => {
          this.$nextTick(() => {
            this.$scrollTo("#interesse_" + int_index);
          });
        });
      }
    },
    mudarTab(index) {
      if (this.tabIndex == 0 && this.validarInteresses() === false) {
        return;
      }

      if (this.tabIndex == 1 && !this.validarCliente()) {
        return;
      }

      if (index >= 0) {
        this.passo = index;
        this.$forceUpdate();
        this.$nextTick(() => {
          this.$nextTick(() => {
            this.tabIndex = index;
          });
        });
      }
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
              label: carro.nome + " | " + carro.marca.nome
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
          valor: caracteristica.valor_padrao || "",
          comparador: "="
        };
        caracteristicasSelecionadas.push({ ...caracteristica });

        this.troca.caracteristicasSelecionadas = caracteristicasSelecionadas;
      } else {
        let interesse = this.interesses[int_index];

        let opcoesCaracteristicas = [];
        let caracteristicasSelecionadas = [
          ...interesse.caracteristicasSelecionadas
        ];

        caracteristica.valor = {
          valor: caracteristica.valor_padrao || "",
          comparador: "="
        };
        caracteristicasSelecionadas.push({ ...caracteristica });

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
            caracteristicasSelecionadas.push({ ...caracteristica });
          }
        });

        this.troca.caracteristicasSelecionadas = caracteristicasSelecionadas;
      } else {
        let interesse = this.interesses[int_index];

        let caracteristicasSelecionadas = [];

        interesse.caracteristicasSelecionadas.forEach(caracteristica => {
          if (caracteristica.id != id) {
            caracteristicasSelecionadas.push({ ...caracteristica });
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
            opcoesCaracteristicas.push({ ...c });
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
            opcoesCaracteristicas.push({ ...c });
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
    validarInteresses() {
      let dados = {};

      let valido = true;
      dados.interesses = this.interesses.map((interesse, i) => {
        if (
          !this.interesses[i].carro &&
          !this.interesses[i].caracteristicasSelecionadas.length
        ) {
          valido = false;
          this.interesses[i].erro = true;
        } else {
          interesse.caracteristicasSelecionadas.forEach(caracteristica => {
            if (!caracteristica.valor || caracteristica.valor.valor === "") {
              valido = false;
              this.interesses[i].erro = true;
            }
          });
        }

        if (valido) {
          this.interesses[i].erro = false;
          return {
            carro_id: interesse.carro ? interesse.carro.id : null,
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

      if (!valido) {
        this.tabIndex = 0;
        this.$forceUpdate();
        return false;
      }

      return dados;
    },
    salvar() {
      let dados = this.validarInteresses();

      if (dados === false) {
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

      let url = "/interesses";
      axios.post(url, dados).then(r => {
        if (r.data.status == "1") {
          let toast = this.$toasted.success("Interesses salvos!", {
            theme: "toasted-primary",
            position: "bottom-right",
            duration: 5000
          });
          this.resultados = r.data.resultados;
          this.resultados.sort((a, b) => b.prioridade - a.prioridade);
          console.log(this.resultados);
          setTimeout(() => {
            this.tabIndex = 3;
            this.$forceUpdate();
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
