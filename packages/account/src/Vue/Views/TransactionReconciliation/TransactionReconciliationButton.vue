<template>
    <span>
        <a href="#" @click.prevent="openModal()" v-if="transaction_id">
            <i class="fas fa-cogs"></i>
        </a>
        <a href="#" @click.prevent="openModal()" v-if="reference_id || (reference_id === null && referenceType === 'unallocated') || reconciliation_id">
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
      },
      'account_id': {
        default: null
      },
      'referenceType': {
        default: 'date'
      },
      'reconciliation_id': {
        default: null
      },
    },
    methods: {
      openModal() {
        this.$parent.$refs.ReconciliationModal.open(
          this.transaction_id,
          this.reference_id,
          this.account_id,
          this.referenceType,
          this.reconciliation_id,
        )
      }
    }
  }
</script>