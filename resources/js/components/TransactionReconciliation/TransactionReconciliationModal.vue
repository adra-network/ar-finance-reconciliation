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
                    <table class="table table-bordered">
                        <tr>
                            <th>
                                Reference
                            </th>
                            <th>
                                Amount
                            </th>
                            <th></th>
                        </tr>
                        <tr v-for="transaction in _reconciledTransactions">
                            <td>
                                {{ transaction.transaction_date }} - {{ transaction.code }} - {{ transaction.reference }}
                            </td>
                            <td>
                                <span v-if="transaction.credit_amount > 0">
                                    -${{ transaction.credit_amount }}
                                </span>
                                <span v-if="transaction.debit_amount > 0">
                                    ${{ transaction.debit_amount }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn btn-sm btn-danger" @click="unreconcileTransaction(transaction.id)" v-if="transaction.id !== transaction_id">
                                    <i class="fa fa-times"></i>
                                </div>
                                <div class="btn btn-sm btn-danger" style="visibility: hidden;" v-if="transaction.id === transaction_id">
                                    <i class="fa fa-times"></i>
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <th>Running total:</th>
                            <th>{{ _runningTotal }}</th>
                            <th></th>
                        </tr>
                    </table>
                    <h4>Choose From Unreconciled Transactions</h4>
                    <table class="table table-bordered">
                        <tr>
                            <th>
                                Reference
                            </th>
                            <th>
                                Amount
                            </th>
                            <th>
                                Select
                            </th>
                        </tr>
                        <tr v-for="transaction in _unreconciledTransactions">
                            <td>
                                {{ transaction.transaction_date }} - {{ transaction.code }}
                            </td>
                            <td>
                                <span v-if="transaction.credit_amount > 0">
                                    -${{ transaction.credit_amount }}
                                </span>
                                <span v-if="transaction.debit_amount > 0">
                                    ${{ transaction.debit_amount }}
                                </span>
                            </td>
                            <td>
                                <div class="btn btn-sm btn-primary" @click="reconcileTransaction(transaction.id)">Add to list</div>
                            </td>
                        </tr>
                    </table>
                    Comments:
                    <br/>
                    <textarea name="comments" rows="3" class="form-control" v-model="comment"></textarea>
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
        transaction_id: null,
        reference_id: null,
        transactions: null,
        reconciledTransactions: [],
        comment: null,
      }
    },
    computed: {
      _reconciledTransactions() {
        return _.filter(this.transactions, t => {
          return _.indexOf(this.reconciledTransactions, t.id) > -1
        })
      },
      _unreconciledTransactions() {
        return _.filter(this.transactions, t => {
          return _.indexOf(this.reconciledTransactions, t.id) === -1 && t.reconciliation_id === null
        })
      },
      _runningTotal() {
        let total = 0.00
        _.each(this._reconciledTransactions, t => {
          total += parseFloat(t.credit_amount) || 0.00
          total -= parseFloat(t.debit_amount) || 0.00
        })

        total = total.toFixed(2)

        if (total <= 0) {
          return '$' + Math.abs(total).toFixed(2)
        }
        return '-$' + total

      }
    },
    methods: {
      open(transaction_id, reference_id, account_id) {

        if (transaction_id) {
          this.transactions = null
          this.reconciledTransactions = []
          this.transaction_id = transaction_id
          this.loadWithTransactionId(transaction_id)
        }
        if (reference_id) {
          this.loadWithReferenceId(reference_id, account_id)
        }

        $('#transactionReconciliationModal').modal('toggle')
      },
      loadWithTransactionId(transaction_id) {
        axios.get('/admin/reconciliation-modal/info', {params: {transaction_id}}).then(response => {
          this.transactions = response.data.data.transactions
          this.reconcileTransactionsByMainTransaction()
        })
      },
      loadWithReferenceId(reference_id, account_id) {
        axios.get('/admin/reconciliation-modal/info', {params: {reference_id, account_id}}).then(response => {
          let data = response.data.data
          this.transactions = data.transactions
          let reconcile = data.transactionsToReconcile
          _.each(reconcile, (transaction) => {
            this.reconcileTransaction(transaction)
          })
        })
      },
      save() {
        axios.post('/admin/reconciliation-modal/reconcile', {
          transactions: this.reconciledTransactions,
          comment: this.comment
        }).then(response => {
//          this.$awn.success('Success')
          $('#transactionReconciliationModal').modal('toggle')
          location.reload()
        }).catch(e => {
//          this.$awn.alert('Failed to save.')
          $('#transactionReconciliationModal').modal('toggle')
          location.reload()
        })
      },
      reconcileTransaction(id) {
        this.reconciledTransactions.push(id)
      },
      unreconcileTransaction(id) {
        let index = _.indexOf(this.reconciledTransactions, id)
        this.reconciledTransactions.splice(index, 1)
      },
      reconcileTransactionsByMainTransaction() {
        let mainTransactionIndex = _.findIndex(this.transactions, t => t.id === this.transaction_id)
        let reconciliation_id = this.transactions[mainTransactionIndex].reconciliation_id
        if (!reconciliation_id) {
          this.reconcileTransaction(this.transaction_id)
        } else {
          _.each(this.transactions, t => {
            if (t.reconciliation_id === reconciliation_id) {
              this.reconcileTransaction(t.id)
            }
          })
        }
      }
    }
  }
</script>