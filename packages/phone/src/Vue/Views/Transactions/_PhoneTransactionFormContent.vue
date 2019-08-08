<template>
    <div class="modal-content" v-if="phoneNumber">
        <div class="modal-header">
            <h5 class="modal-title" v-if="!transaction">Phone number</h5>
            <h5 class="modal-title" v-if="transaction">Phone Transaction</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" v-model="phoneNumber.name">
            </div>
            <div class="form-group">
                <label>Charge to: <span class="btn btn-success btn-sm" @click="$emit('view', 'allocation-form')">+</span></label>
                <select class="form-control" v-model="transaction.allocation_id" v-if="transaction">
                    <option :value="allocation.id" v-for="allocation in allocations">{{ allocation.name }}</option>
                </select>
                <select class="form-control" v-model="phoneNumber.allocation_id" v-if="!transaction">
                    <option :value="allocation.id" v-for="allocation in allocations">{{ allocation.name }}</option>
                </select>
                <div class="alert alert-warning mt-3" v-if="shouldSuggestAllocation">This allocation is auto suggested. Press save to add it.</div>
            </div>
            <div class="form-group">
                <label>Auto Allocation:</label>
                <select class="form-control" v-model="phoneNumber.auto_allocation">
                    <option :value="value" v-for="(value, title) in AutoAllocateEnum.enum">{{ title }}</option>
                </select>
            </div>
            <div class="form-group">
                <label>Comments:</label>
                <textarea name="comments" rows="3" class="form-control" v-model="transaction.comment" v-if="transaction"></textarea>
                <textarea name="comments" rows="3" class="form-control" v-model="phoneNumber.comment" v-if="!transaction"></textarea>
            </div>

            <div class="form-group">
                <input type="checkbox" v-model="phoneNumber.remember"> Remember
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" @click="save()">Save</button>
        </div>
    </div>
</template>
<script>
  import AutoAllocateEnum from './../AutoAllocateEnum'

  export default {
    props: {
      allocations: {
        required: true,
      },
    },
    data() {
      return {
        AutoAllocateEnum,
        transaction: null,
        phoneNumber: null,
        shouldSuggestAllocation: false,
      }
    },
    methods: {
      load({transaction_id, phone_number_id}) {
        this.transaction = null
        this.phoneNumber = null
        axios.post('/phone/transaction-modal/load', {transaction_id, phone_number_id}).then(response => {
          this.transaction = response.data.transaction
          this.phoneNumber = response.data.phoneNumber

          this.shouldSuggestAllocation = this.phoneNumber.suggested_allocation && ((this.transaction && this.transaction.allocation_id === null) || (!this.transaction && this.phoneNumber.allocation_id === null))

          if (this.shouldSuggestAllocation) {
            if (this.transaction) {
              this.transaction.allocation_id = this.phoneNumber.suggested_allocation.id
            } else {
              this.phoneNumber.allocation_id = this.phoneNumber.suggested_allocation.id
            }
          }
        })
      },
      save() {
        axios
          .post('/phone/transaction-modal/save', {transaction: this.transaction, phoneNumber: this.phoneNumber})
          .then(response => location.reload())
          .catch(err => location.reload())
      },
      setAllocationId(id) {
        this.shouldSuggestAllocation = false
        if (this.transaction) {
          this.transaction.allocation_id = id
        } else {
          this.phoneNumber.allocation_id = id
        }
      },
    }
  }
</script>