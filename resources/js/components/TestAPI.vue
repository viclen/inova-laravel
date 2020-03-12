<template>
  <div class="container">
    <div class="row" v-if="type==0">
      <div class="col-12">
        <input type="email" class="form-control" v-model="email" />
      </div>
      <div class="col-12">
        <input type="password" v-model="senha" class="form-control" />
      </div>
      <div class="col-12">
        <button class="btn btn-primary" v-on:click="login()">Login</button>
      </div>
    </div>
    <div class="row" v-if="type==1">
      <div class="col-12">
        <input type="text" v-model="rota" />
      </div>
      <div class="col-12">
        <button class="btn btn-primary" v-on:click="getRoute()">Buscar</button>
        <button class="btn btn-primary" v-on:click="testPost()">Testar post</button>
      </div>
    </div>
    <div class="row">
      <div class="col-12">{{ result }}</div>
    </div>
  </div>
</template>

<script>
const URL = "/api/";

export default {
  data() {
    return {
      email: "",
      senha: "",
      token: "",
      result: "Nenhum resultado",
      rota: "",
      type: 0
    };
  },
  methods: {
    login() {
      axios
        .post(URL + "login", {
          email: this.email,
          senha: this.senha
        })
        .then(r => {
          if (r.data.status == 1) {
            this.token = r.data.token;
            this.type = 1;
          }
        });
    },
    getRoute() {
      axios
        .get(
          URL +
            this.rota +
            (this.rota.includes("?") ? "&" : "?") +
            "api_token=" +
            this.token
        )
        .then(r => {
          this.result = JSON.stringify(r.data);
        })
        .catch(e => {
          this.result = e.message;
        });
    },
    testPost() {
      let data = {
        nome: "Teste carro",
        marca_id: "1"
      };

      axios
        .post(URL + "carros", data, {
          headers: {
            Authorization: "Bearer " + this.token
          }
        })
        .then(r => {
          console.log(r.data);
        })
        .catch(e => {
          console.log(e);
        });
    }
  }
};
</script>

<style>
</style>
