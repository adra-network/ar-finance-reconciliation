<template>
    <span>
        <a href="#" @click="openModal()" v-if="transaction_id">
        <i class="fas fa-cogs"></i>
    </a>
        <a href="#" @click="openModal()" v-if="reference_id">
        <i class="fas fa-layer-group"></i>
    </a>

    </span>
</template>
<script>
  export default {
    props: {
      'transaction_id': {
        default: null
      },
      'reference_id': {
        default: null
      }
    },
    methods: {
      openModal() {
        this.$parent.$refs.reconciliationModal.open(this.transaction_id, this.reference_id)
      }
    }
  }
</script>