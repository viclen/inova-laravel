<template>
  <div>
    <div :class="{
        'loading-background': true,
        show: loading,
    }">
      <div class="loading-container">
        <b-spinner type="grow" label="Loading..."></b-spinner>
      </div>
    </div>

    <div v-if="!isRoute('login')" :class="{ 'sidebar': true, 'mostrar': mostrar }">
      <div class="sidebar-container">
        <button class="toggle" @click="mostrar = !mostrar">
          <fa-icon v-if="mostrar" icon="chevron-left" />
          <fa-icon v-else icon="bars" />
        </button>

        <div class="imagem">
          <img src="/image/logo.png" />
        </div>

        <div>
          <b-dropdown :text="user.name" variant="link" class="ml-1 mb-1">
            <template v-slot:button-content>
              <fa-icon icon="user" />
              &nbsp;
              {{user.name}}
            </template>
            <b-dropdown-item href="/alterarsenha">
              <fa-icon icon="lock" />&nbsp;
              Alterar Senha
            </b-dropdown-item>

            <a
              class="dropdown-item"
              href="/logout"
              onclick="event.preventDefault();document.getElementById('logout-form').submit();"
            >
              <fa-icon icon="sign-out-alt" />&nbsp;
              Sair
            </a>
          </b-dropdown>
        </div>

        <form id="logout-form" action="/logout" method="POST" style="display: none;">
          <input type="hidden" name="_token" :value="csrf" />
        </form>

        <div class="menu">
          <a @click="go('/home')" :class="{ 'menu-item': true, 'active': isRoute('home') }">
            <fa-icon icon="home" class="item-icon" />Home
          </a>
          <a
            @click="go('/interesses')"
            :class="{ 'menu-item': true, 'active': isRoute('interesses') }"
          >
            <fa-icon icon="address-book" class="item-icon" />Interesses
          </a>
          <a @click="go('/clientes')" :class="{ 'menu-item': true, 'active': isRoute('clientes') }">
            <fa-icon icon="users" class="item-icon" />Clientes
          </a>
          <a @click="go('/carros')" :class="{ 'menu-item': true, 'active': isRoute('carros') && !isRoute('clientes') }">
            <fa-icon icon="car" class="item-icon" />Carros
          </a>
          <a @click="go('/marcas')" :class="{ 'menu-item': true, 'active': isRoute('marcas') }">
            <fa-icon icon="ad" class="item-icon" />Marcas
          </a>
          <a @click="go('/estoques')" :class="{ 'menu-item': true, 'active': isRoute('estoques') }">
            <fa-icon icon="box-open" class="item-icon" />Estoques
          </a>
          <a
            @click="go('/caracteristicas')"
            :class="{ 'menu-item': true, 'active': isRoute('caracteristicas') }"
          >
            <fa-icon icon="project-diagram" class="item-icon" />Características
          </a>
        </div>
        <div class="footer">
          Desenvolvido por
          <a
            href="http://autosavestudio.com"
            class="text-capitalize text-success d-block text-decoration-none"
            target="_blank"
          >AutoSave Studio</a>
        </div>
      </div>
    </div>

    <div
      v-if="!loading"
      :class="{ 'sidebar-margin': !isRoute('login'), 'mostrar': mostrar && !isRoute('login') }"
    >
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  props: ["user", "csrf"],
  data() {
    return {
      mostrar: false,
      loading: true
    };
  },
  mounted() {
    setTimeout(() => (this.mostrar = window.innerWidth >= 992), 1);
    setTimeout(() => {
      this.loading = false;
    }, 200);
  },
  methods: {
    isRoute(name) {
      return window.location.href.includes("/" + name);
    },
    go(url) {
      this.mostrar = false;
      this.loading = true;
      setTimeout(() => (window.location = url), 400);
    }
  }
};
</script>

<style>
:root {
  --width: 200px;
  --danger: #e3342f;
}

.toggle {
  position: absolute;
  right: -40px;
  top: 0px;
  background: white;
  outline: none !important;
  border: none;
  padding: 5px 10px;
  border-bottom-right-radius: 10px;
  font-size: 20px;
  width: 40px;
  box-shadow: 3px 3px 5px -3px black;
}

.sidebar {
  position: fixed;
  left: calc(-1 * var(--width));
  top: 0;
  bottom: 0;
  width: var(--width);
  background: white;
  transition: 500ms;
  z-index: 1000;
}
.sidebar.mostrar {
  box-shadow: 0px 0px 5px 0px black;
  left: 0;
}
.sidebar-margin {
  margin-left: 0;
  transition: 200ms;
}
.sidebar-container {
  position: relative;
  height: 100%;
}
.menu-item {
  display: block;
  padding: 10px;
  text-decoration: none;
  color: black;
  transition: 500ms;
  border-left: 0px solid var(--danger);
  cursor: pointer;
}
.menu-item:hover,
.menu-item.active {
  text-decoration: none;
  border-left-width: 5px;
  color: var(--danger);
}
.menu-item.active {
  background: #e3352f17;
}
.item-icon {
  width: 25px !important;
}
.imagem {
  padding: 1em;
}
.imagem > img {
  width: 100%;
}
.footer {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  text-align: center;
  padding: 10px;
  font-weight: bold;
}

.loading-background {
  z-index: -10;
  opacity: 0;
  background: white;
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  transition: 500ms;
}
.loading-background.show {
  z-index: 1000;
  opacity: 1;
}
.loading-container {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

@media screen and (min-width: 992px) {
  .sidebar-margin.mostrar {
    margin-left: var(--width);
  }
}
</style>
