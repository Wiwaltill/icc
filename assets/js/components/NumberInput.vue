<template>
  <div class="input-group">
    <div class="input-group-prepend">
      <button class="btn btn-outline-secondary" type="button" @click.prevent="minus()">
        <i class="fas fa-minus"></i>
      </button>
    </div>
    <input type="text" :name="name" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" :class="'form-control ' + this.class" :id="id">

    <div class="input-group-append">
      <button class="btn btn-outline-secondary" type="button" @click.prevent="plus()">
        <i class="fas fa-plus"></i>
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'number_input',
  props: {
    modelValue: Number,
    min: Number,
    max: Number,
    id: String,
    class: String,
    name: String
  },
  emits: ['update:modelValue'],
  methods: {
    minus() {
      if(this.modelValue > this.min) {
        this.$emit('update:modelValue', this.modelValue-1);
      }
    },
    plus() {
      if(this.modelValue < this.max) {
        this.$emit('update:modelValue', this.modelValue+1);
      }
    }
  }
}
</script>