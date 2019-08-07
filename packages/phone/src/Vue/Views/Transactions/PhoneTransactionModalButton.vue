<template>
    <span>
        <a href="#" @click.prevent="openModal()" v-if="transaction_id">
            <i class="fas fa-cogs"></i>
        </a>

        <a href="#" @click.prevent="openModal()" v-if="phone_number">
            {{ phone_number.phone_number }}
        </a>

    </span>
</template>
<script>
  export default {
    props: {
      view: {
        required: true,
      },
      transaction_id: {
        default: null
      },
      phone_number: {
        default: null
      }
    },
    methods: {
      openModal() {
        this.$parent.$refs.PhoneTransactionModal.open({
          transaction_id: this.transaction_id,
          phone_number_id: this.phone_number ? this.phone_number.phone_number_id : null,
          view: this.view,
        })
      },
    }
  }
</script>