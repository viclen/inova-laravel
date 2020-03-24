<template>
  <div>
    <div class="spinner-container" v-if="loading">
      <div>
        <b-spinner type="border" variant="dark" />
      </div>
    </div>

    <div class="form-group row" v-for="(campo,i) in campos" v-bind:key="i">
      <label
        v-bind:for="campo.nome+'_'"
        class="text-capitalize col-md-4 col-form-label text-md-right"
        v-if="campo.nome != 'id'"
      >
        {{ campo.label }}
        <span v-if="campo.tipo == 'dinheiro'">(R$)</span>
      </label>

      <div class="col-md-6" v-if="campo.nome != 'id'">
        <v-select
          v-if="campo.tipo=='select'"
          :options="campo.opcoes"
          v-bind:id="campo.nome+'_'"
          v-bind:class="{'border border-danger rounded is-invalid': campo.error != undefined}"
          v-model="campo.valor"
        >
          <div slot="no-options">Nenhum resultado.</div>
        </v-select>

        <input
          type="text"
          v-else-if="campo.tipo=='input-text'"
          v-bind:id="campo.nome+'_'"
          v-bind:class="{'form-control': true, 'is-invalid': campo.error != undefined}"
          v-model="campo.valor"
          v-on:keyup="(event) => keyEvent(event)"
        />

        <input
          type="number"
          v-else-if="campo.tipo=='number'"
          v-bind:id="campo.nome+'_'"
          v-bind:class="{'form-control': true, 'is-invalid': campo.error != undefined}"
          v-model="campo.valor"
          v-on:keyup="(event) => keyEvent(event)"
        />

        <textarea
          v-bind:class="{'form-control': true, 'is-invalid': campo.error != undefined}"
          v-else-if="campo.tipo=='textarea'"
          v-bind:id="campo.nome+'_'"
          v-model="campo.valor"
        ></textarea>

        <input
          type="email"
          v-else-if="campo.tipo=='email'"
          v-bind:id="campo.nome+'_'"
          v-bind:class="{'form-control': true, 'is-invalid': campo.error != undefined}"
          v-model="campo.valor"
          placeholder="nome@exemplo.com"
          v-on:keyup="(event) => keyEvent(event)"
        />

        <input
          type="text"
          v-else-if="campo.tipo=='dinheiro'"
          v-bind:id="campo.nome+'_'"
          v-bind:class="{'form-control': true, 'is-invalid': campo.error != undefined}"
          v-model="campo.valor"
          v-on:keyup="campo.valor = formatarDinheiro(campo.valor)"
          placeholder="R$ 12.345,00"
        />

        <input
          type="text"
          v-else-if="campo.tipo=='telefone'"
          v-bind:id="campo.nome+'_'"
          v-bind:class="{'form-control': true, 'is-invalid': campo.error != undefined}"
          v-model="campo.valor"
          v-on:keyup="campo.valor = formatarTelefone(campo.valor)"
          placeholder="(55) 99999-9999"
        />

        <input
          type="text"
          v-else-if="campo.tipo=='cpf'"
          v-bind:id="campo.nome+'_'"
          v-bind:class="{'form-control': true, 'is-invalid': campo.error != undefined}"
          v-model="campo.valor"
          v-on:keyup="campo.valor = formatarCpf(campo.valor)"
          placeholder="123.456.789-10"
        />

        <input
          type="text"
          v-else-if="campo.tipo=='placa'"
          v-bind:id="campo.nome+'_'"
          v-bind:class="{'form-control': true, 'is-invalid': campo.error != undefined}"
          v-model="campo.valor"
          v-on:keyup="campo.valor = formatarPlaca(campo.valor)"
          placeholder="ABC-1234"
        />

        <b-form-checkbox
          switch
          class="mt-2"
          v-else-if="campo.tipo=='checkbox'"
          v-bind:id="campo.nome+'_'"
          v-bind:class="{'is-invalid': campo.error != undefined}"
          v-model="campo.valor"
        />

        <span v-if="campo.error != undefined" class="invalid-feedback" role="alert">
          <strong>{{ campo.error }}</strong>
        </span>
      </div>
    </div>
    <div class="form-group row">
      <div class="col-md-10 text-right">
        <button class="btn btn-primary" v-on:click="salvar()">
          <fa-icon icon="save" />&nbsp;
          Salvar
        </button>
        <button class="btn btn-secondary" v-on:click="cancelar()">
          <fa-icon icon="times" />&nbsp;
          Cancelar
        </button>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: [
    "dados",
    "mostrar",
    "esconder",
    "opcoes",
    "valores",
    "action",
    "redirect"
  ],
  data() {
    return {
      campos: [],
      loading: false,
      id: undefined
    };
  },
  mounted() {
    let campos = [];

    for (const campo in this.dados) {
      if (
        this.dados.hasOwnProperty(campo) &&
        campo != "id" &&
        campo != "created_at" &&
        campo != "updated_at"
      ) {
        const tipo = this.dados[campo];

        if (
          (this.mostrar == undefined || this.mostrar.includes(campo)) &&
          !(this.esconder != undefined && this.esconder.includes(campo))
        ) {
          let opcoes =
            this.opcoes != undefined && this.opcoes[campo.replace("_id", "s")]
              ? this.opcoes[campo.replace("_id", "s")].map(item => ({
                  id: item.id,
                  label: item.nome
                }))
              : undefined;

          campos.push({
            nome: campo,
            label: campo.replace("_id", ""),
            tipo: this.getInput(campo, tipo),
            opcoes,
            valor: ""
          });
        }
      }
    }

    this.$nextTick(() => {
      if (this.valores != undefined && this.valores.id) {
        this.id = this.valores.id;

        for (const nome in this.valores) {
          const valor = this.valores[nome];

          for (const i in this.campos) {
            let campo = this.campos[i];
            if (campo.nome == nome) {
              if (campo.tipo == "select") {
                for (const j in campo.opcoes) {
                  const opcao = campo.opcoes[j];
                  if (opcao.id == valor) {
                    this.campos[i].valor = opcao;
                    break;
                  }
                }
              } else if (campo.tipo == "placa") {
                this.campos[i].valor = this.formatarPlaca(valor);
              } else if (campo.tipo == "cpf") {
                this.campos[i].valor = this.formatarCpf(valor);
              } else if (campo.tipo == "dinheiro") {
                this.campos[i].valor = this.formatarDinheiro(valor);
              } else if (campo.tipo == "telefone") {
                this.campos[i].valor = this.formatarTelefone(valor);
              } else if (campo.tipo == "checkbox") {
                this.campos[i].valor = valor ? true : false;
              } else {
                this.campos[i].valor = valor;
              }

              break;
            }
          }
        }
      }
    });

    this.campos = campos;
  },
  methods: {
    salvar() {
      this.loading = true;

      let data = {};

      this.campos.forEach(campo => {
        if (campo.tipo == "select") {
          data[campo.nome] = campo.valor ? campo.valor.id : null;
        } else if (campo.tipo == "checkbox") {
          data[campo.nome] = campo.valor ? 1 : 0;
        } else if (campo.tipo == "dinheiro") {
          data[campo.nome] = (campo.valor + "")
            .split(".")
            .join("")
            .replace(",", ".");
        } else {
          data[campo.nome] = campo.valor;
        }
      });

      if (this.valores && this.id) {
        let url = this.action != undefined ? this.action : window.location.href;

        if (!url.includes(this.id)) {
          url = url.endsWith("/") ? url + this.id : url + "/" + this.id;
          url = url.replace("/edit", "");
        }

        axios.put(url, data).then(r => this.fimRequest(r));
      } else {
        axios
          .post(
            this.action != undefined ? this.action : window.location.href,
            data
          )
          .then(r => this.fimRequest(r))
          .catch(e => {
            this.loading = false;

            let toast = this.$toasted.error("Não foi possível salvar.", {
              theme: "toasted-primary",
              position: "bottom-right",
              duration: 2000
            });

            console.log(e);
          });
      }
    },
    fimRequest(r) {
      this.campos.forEach((campo, i) => {
        this.campos[i].error = undefined;
      });

      if (r.data.status == "1") {
        setTimeout(() => {
          if (this.redirect) {
            window.location = this.redirect;
          } else {
            window.location = window.location.href;
          }
        }, 1000);

        let toast = this.$toasted.success("Salvo com sucesso!", {
          theme: "toasted-primary",
          position: "bottom-right",
          duration: 2000
        });
      } else {
        this.loading = false;

        let toast = this.$toasted.error("Não foi possível salvar.", {
          theme: "toasted-primary",
          position: "bottom-right",
          duration: 2000
        });

        console.log(r.data);

        if (r.data.errors) {
          this.campos.forEach((campo, i) => {
            for (let nome in r.data.errors) {
              if (campo.nome == nome) {
                this.campos[i].error = r.data.errors[nome].join(", ");
              }
            }
          });

          this.$forceUpdate();
        }
      }

      this.mostrarModalDelete = false;
      this.deleteId = -1;
    },
    getInput(campo, tipo) {
      if (campo.startsWith("telefone")) {
        return "telefone";
      } else if (campo.startsWith("placa")) {
        return "placa";
      } else if (campo.startsWith("cpf")) {
        return "cpf";
      } else if (campo.startsWith("email")) {
        return "email";
      } else if (tipo.startsWith("varchar")) {
        return "input-text";
      } else if (tipo.startsWith("text")) {
        return "textarea";
      } else if (campo.endsWith("_id")) {
        return "select";
      } else if (tipo.includes("tinyint(1)")) {
        return "checkbox";
      } else if (tipo.includes("int")) {
        return "number";
      } else if (tipo.includes("float") || tipo.includes("double")) {
        return "dinheiro";
      }
    },
    formatarDinheiro(entrada) {
      entrada = entrada + "";

      if (entrada.indexOf(".") == entrada.length - 3) {
        entrada =
          entrada.substring(0, entrada.length - 3) +
          "," +
          entrada.substring(entrada.length - 2);
      }

      var onlyNumbers = "";
      for (let i = entrada.length - 1; i >= 0; i--) {
        if (!isNaN(parseInt(entrada.charAt(i))) || entrada.charAt(i) == ",") {
          if (onlyNumbers.includes(",") && entrada.charAt(i) == ",") {
            continue;
          }
          onlyNumbers += entrada.charAt(i);
        }
      }
      if (onlyNumbers.includes(",")) {
        var n = onlyNumbers.split(",")[1];
        var n1 = "";
        if (n.length > 3) {
          for (let i = 0; i < n.length; i++) {
            if ((i + 1) % 3 == 0 && i != n.length - 1) {
              n1 += n.charAt(i) + ".";
            } else {
              n1 += n.charAt(i);
            }
          }
        } else {
          n1 = n;
        }
        var n2 = this.reverseStr(onlyNumbers.split(",")[0]);
        if (n2.length > 2) {
          n2 = n2.charAt(0) + n2.charAt(1);
        }
        onlyNumbers = this.reverseStr(n1) + "," + n2;
      } else {
        var n1 = "";
        if (onlyNumbers.length > 3) {
          for (let i = 0; i < onlyNumbers.length; i++) {
            if ((i + 1) % 3 == 0 && i != onlyNumbers.length - 1) {
              n1 += onlyNumbers.charAt(i) + ".";
            } else {
              n1 += onlyNumbers.charAt(i);
            }
          }
        } else {
          n1 = onlyNumbers;
        }
        onlyNumbers = this.reverseStr(n1);
      }
      return onlyNumbers;
    },
    formatarTelefone(entrada) {
      let pattern = "(xx) xxxxx-xxxx";
      var onlyNumbers = [];
      for (let i = 0; i < entrada.length; i++) {
        if (!isNaN(parseInt(entrada.charAt(i)))) {
          onlyNumbers.push(entrada.charAt(i));
        }
      }
      if (onlyNumbers.length > 10) {
        pattern = "(xx) xxxxx-xxxx";
      } else {
        pattern = "(xx) xxxx-xxxx";
      }
      var finalValue = "";
      onlyNumbers.reverse();
      for (let i = 0; i < pattern.length && onlyNumbers.length; i++) {
        if (pattern.charAt(i) == "x") {
          finalValue += onlyNumbers.pop();
        } else {
          finalValue += pattern.charAt(i);
        }
      }
      return finalValue;
    },
    formatarCpf(entrada) {
      let pattern = "xxx.xxx.xxx-xx";
      var onlyNumbers = [];
      for (let i = 0; i < entrada.length; i++) {
        if (!isNaN(parseInt(entrada.charAt(i)))) {
          onlyNumbers.push(entrada.charAt(i));
        }
      }

      var finalValue = "";
      onlyNumbers.reverse();
      for (let i = 0; i < pattern.length && onlyNumbers.length; i++) {
        if (pattern.charAt(i) == "x") {
          finalValue += onlyNumbers.pop();
        } else {
          finalValue += pattern.charAt(i);
        }
      }
      return finalValue;
    },
    formatarPlaca(entrada) {
      let pattern = "AAA-0000";
      let allow_letters =
        "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
      let allow_numbers = "1234567890";

      let letters = "";
      let numbers = "";

      for (let i = 0; i < entrada.length && i < pattern.length; i++) {
        if (
          pattern.charAt(i) == "A" &&
          allow_letters.includes(entrada.charAt(i))
        ) {
          letters += entrada.charAt(i);
        } else if (
          pattern.charAt(i) == "0" &&
          allow_numbers.includes(entrada.charAt(i))
        ) {
          numbers += entrada.charAt(i);
        } else if (pattern.charAt(i) == "-") {
          if (entrada.charAt(i) == "-") {
            letters += entrada.charAt(i);
          } else if (allow_numbers.includes(entrada.charAt(i))) {
            letters += "-";
            numbers += entrada.charAt(i);
          }
        }
      }

      var finalValue = letters;

      if (finalValue.length > 3) {
        finalValue += numbers;
      }

      return finalValue.toUpperCase();
    },
    cancelar() {
      if (this.redirect) {
        window.location = this.redirect;
      } else {
        window.history.back();
      }
    },
    reverseStr(str) {
      var reversed = "";
      for (let i = str.length - 1; i >= 0; i--) {
        reversed += str.charAt(i);
      }
      return reversed;
    },
    keyEvent(event) {
      if (event.keyCode == 13) {
        this.salvar();
      }
    }
  }
};
</script>
<style>
.spinner-container {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(255, 255, 255, 0.5);
  z-index: 10;
}
.spinner-container > div {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}
</style>
