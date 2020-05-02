<template>
  <div>
    <input
      v-model="inputValue"
      type="text"
      v-on:keyup="run()"
      v-bind:required="required"
      v-bind:readonly="readonly"
      v-bind:name="name"
      v-bind:autocomplete="autocomplete"
      v-bind:id="id"
      v-bind:class="{
        'form-control': true,
        'is-invalid': error,
    }"
      v-bind:placeholder="pattern"
    />
  </div>
</template>

<script>
export default {
  props: [
    "value",
    "name",
    "autocomplete",
    "required",
    "error",
    "id",
    "placeholder",
    "verificar_telefone",
    "readonly",
    "pattern"
  ],
  data() {
    return {
      inputValue: "",
    };
  },
  mounted() {
    if (this.value) {
      this.inputValue = this.value;
    }
    this.run();
  },
  methods: {
    run() {
      if (this.pattern && this.inputValue) {
        var value = "" + this.inputValue;
        var onlyNumbers = [];
        for (let i = 0; i < value.length; i++) {
          if (!isNaN(parseInt(value.charAt(i)))) {
            onlyNumbers.push(value.charAt(i));
          }
        }
        var finalValue = "";
        onlyNumbers.reverse();
        for (let i = 0; i < this.pattern.length && onlyNumbers.length; i++) {
          if (this.pattern.charAt(i) == "9") {
            finalValue += onlyNumbers.pop();
          } else {
            finalValue += this.pattern.charAt(i);
          }
        }
        this.inputValue = finalValue;
        this.updateValue();
      }
    },
    updateValue() {
      this.$emit("input", this.inputValue);
    }
  }
};
</script>

<style scoped>
</style>
