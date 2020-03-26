<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 mb-3">
        <a :href="url('/edit')" class="btn btn-light">
          <span>
            <fa-icon icon="edit" />
          </span>
          &nbsp;
          Editar
        </a>
      </div>

      <div class="col-12 mb-3" v-for="(items, titulo) in processados" :key="titulo">
        <div class="card">
          <div class="card-header text-capitalize">{{ titulo }}</div>

          <div class="card-body">
            <tabela-acoes
              v-if="items[0] !== undefined"
              :mostrarid="false"
              :dados="items"
              :colunas="''"
              :controller="titulo"
              :colunasvalor="['fipe', 'valor']"
              :podecriar="false"
              :podevoltar="false"
              :podepesquisar="false"
              :colunascheck="['financiado']"
              :highlight="highlight"
            />

            <div v-else>
              <template v-for="(item, nome) in items">
                <div
                  v-if="!nome.includes('_id') && !nome.includes('_at')"
                  class="form-group row"
                  :key="nome"
                >
                  <label
                    :for="nome + '_'"
                    class="text-capitalize col-md-4 col-form-label text-md-right"
                  >{{ nome }}</label>
                  <div class="col-md-6">
                    <a
                      type="text"
                      :href="links[nome]"
                      :class="{ 'form-control': true, 'btn-link': links[nome]!=undefined }"
                    >{{item}}</a>
                  </div>
                </div>
              </template>
            </div>

            <div v-if="isEmpty(items)" class="alert alert-secondary mb-0" role="alert">Nenhum dado.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["dados", "highlight"],
  data() {
    return {
      links: {},
      processados: []
    };
  },
  mounted() {
    this.processados = JSON.parse(JSON.stringify(this.dados));

    for (let titulo in this.processados) {
      const items = this.processados[titulo];

      if (items[0] === undefined && typeof items == "object") {
        for (let nome in items) {
          const item = items[nome];
          if (nome.includes("_id")) {
            nome = nome.replace("_id", "");
            this.links[nome] = "/" + nome + "s/" + item;
          }
        }
      }
    }
  },
  methods: {
    url(add) {
      return window.location.href + add;
    },
    show(msg) {
      console.log(msg);
    },
    isEmpty(obj) {
      console.log(Object.keys(obj).length);
      return Object.keys(obj).length == 0;
    }
  }
};
</script>

<style>
</style>
