<template>
    <!-- Modal -->
    <div class="modal fade" id="transactionReconciliationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Reconcile Transaction</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Comments:
                    <br/>
                    <textarea name="comments" rows="3" class="form-control" v-model="transaction.comment"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="save()">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
  export default {
    data() {
      return {
        transaction: {
          comment: null
        },
      }
    },
    methods: {
      open(transaction_id) {
        axios.get('transaction-modal/' + transaction_id).then(response => {
          this.transaction = response.data.data
        })

        $('#transactionReconciliationModal').modal('toggle')
      },
      save() {
        axios.put('transaction-modal/' + this.transaction.id, this.transaction).then(response => {
          this.transaction = response.data.data
          location.reload()
        }).catch(err => location.reload())
      },
    }
  }
</script>
<style>
    .modal-lg {
        max-width: 80% !important;
    }
</style>