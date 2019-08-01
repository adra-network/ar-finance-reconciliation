<template>
    <span>
        <a href="#" @click.prevent="openModal()" v-if="transaction_id">
            <i class="fas fa-cogs"></i>
        </a>
    </span>
</template>
<script>
  export default {
    props: {
      'transaction_id': {
        default: null
      },
    },
    methods: {
      openModal() {
        this.$parent.$refs.PhoneTransactionModal.open(
          this.transaction_id,
        )
      }
    }
  }
</script>