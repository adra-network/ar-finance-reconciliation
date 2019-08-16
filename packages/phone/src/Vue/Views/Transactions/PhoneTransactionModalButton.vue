<template>
    <span>
        <a href="#" @click.prevent="openModal()" v-if="transaction_id">
            <i class="fas fa-cogs"></i>
        </a>

        <a href="#" @click.prevent="openModal()" v-if="caller_phone_number && caller_phone_number.phone_number">
            {{ caller_phone_number.phone_number }}
        </a>

        <span v-if="caller_phone_number && !caller_phone_number.phone_number">No number</span>

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
      caller_phone_number: {
        default: null
      }
    },
    methods: {
      openModal() {
        this.$parent.$refs.PhoneTransactionModal.open({
          transaction_id: this.transaction_id,
          caller_phone_number_id: this.caller_phone_number ? this.caller_phone_number.caller_phone_number_id : null,
          view: this.view,
        })
      },
    }
  }
</script>