<template>
  <div>
    <div :class="{ 'sidebar': true, 'mostrar': mostrar }">
      <div class="sidebar-container">
        <button class="toggle" @click="mostrar = !mostrar">
          <fa-icon v-if="mostrar" icon="chevron-left" />
          <fa-icon v-else icon="bars" />
        </button>

        <div class="imagem">
          <img src="/image/logo.png" />
        </div>
        <hr />
        <div class="menu">
          <a @click="go('/clientes')" :class="{ 'menu-item': true, 'active': isRoute('clientes') }">
            <fa-icon icon="list" class="item-icon" />Clientes
          </a>
          <a @click="go('/carros')" :class="{ 'menu-item': true, 'active': isRoute('carros') }">
            <fa-icon icon="list" class="item-icon" />Carros
          </a>
          <a @click="go('/marcas')" :class="{ 'menu-item': true, 'active': isRoute('marcas') }">
            <fa-icon icon="list" class="item-icon" />Marcas
          </a>
          <a @click="go('/estoques')" :class="{ 'menu-item': true, 'active': isRoute('estoques') }">
            <fa-icon icon="list" class="item-icon" />Estoques
          </a>
          <a @click="go('/regras')" :class="{ 'menu-item': true, 'active': isRoute('regras') }">
            <fa-icon icon="list" class="item-icon" />Regras
          </a>
          <a
            @click="go('/interesses')"
            :class="{ 'menu-item': true, 'active': isRoute('interesses') }"
          >
            <fa-icon icon="list" class="item-icon" />Interesses
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

    <div :class="{ 'sidebar-margin': true, mostrar }">
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      mostrar: false
    };
  },
  mounted() {
    setTimeout(() => (this.mostrar = window.innerWidth >= 992), 1);
  },
  methods: {
    isRoute(name) {
      return window.location.href.includes("/" + name);
    },
    go(url) {
      this.mostrar = false;
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

@media screen and (min-width: 992px) {
  .sidebar-margin.mostrar {
    margin-left: var(--width);
  }
}
</style>
