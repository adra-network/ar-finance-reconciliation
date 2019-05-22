<template>
    <a href="#" @click="openModal()">
        <i class="fas fa-cogs"></i>
    </a>
</template>
<script>
  export default {
    props: ['transaction_id'],
    methods: {
      openModal() {
        this.$parent.$refs.reconciliationModal.open(this.transaction_id)
      }
    }
  }
</script>