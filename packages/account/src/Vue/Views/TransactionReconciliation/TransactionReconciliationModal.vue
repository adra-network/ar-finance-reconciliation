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
                                    -${{ transaction.credit_amount.toFixed(2) }}
                                </span>
                                <span v-if="transaction.debit_amount > 0">
                                    ${{ transaction.debit_amount.toFixed(2) }}
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
                            <th>Reference</th>
                            <th>Amount</th>
                            <th>Journal</th>
                            <th>Transaction ID</th>
                            <th>Comment</th>
                            <th></th>
                        </tr>
                        <tr v-for="transaction in _unreconciledTransactions">
                            <td>
                                {{ transaction.transaction_date }} - {{ transaction.reference }}
                            </td>
                            <td>
                                <span v-if="transaction.credit_amount > 0">
                                    -${{ transaction.credit_amount.toFixed(2) }}
                                </span>
                                <span v-if="transaction.debit_amount > 0">
                                    ${{ transaction.debit_amount.toFixed(2) }}
                                </span>
                            </td>
                            <td>
                                {{ transaction.journal }}
                            </td>
                            <td>
                                {{ transaction.code }}
                            </td>
                            <td>
                                {{ transaction.comment }}
                            </td>
                            <td>
                                <div class="btn btn-sm btn-primary" @click="reconcileTransaction(transaction.id)">Add to list</div>
                            </td>
                        </tr>
                    </table>

                    <div class="comments mb-3 mt-3">
                        Comments:
                        <div class="comment mt-2" v-for="comment in comments">
                            <hr>

                            <div>
                                {{ comment.created_at_formatted }} - {{ comment.user.name }} - {{ comment.comment }}
                            </div>
                            <div v-if="isAdmin">
                                <div v-show="changingVisibility !== comment.id">
                                    Visibility : <span class="text-success">{{ comment.scope }}</span>
                                    ( <span style="text-decoration: underline; cursor:pointer;" @click="toggleCommentScope(comment.id)">make {{ comment.scope === 'public' ? 'internal' : 'public' }}</span> )
                                </div>
                                <div v-show="changingVisibility === comment.id">
                                    <i class="fa fa-sync fa-spin"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-1 mb-1">
                        <select class="form-control" v-model="selectedCommentTemplate">
                            <option :value="null">Select template</option>
                            <option :value="template" v-for="template in commentTemplates">{{ template.comment }}</option>
                        </select>
                    </div>

                    <textarea placeholder="Leave your comment..." name="comments" rows="3" class="form-control" v-model="comment"></textarea>
                    <div class="mt-3 pull-right" @click="postComment">
                        <div class="btn btn-info">
                            <span v-show="!postingComment">Post comment</span>
                            <span v-show="postingComment">
                                <i class="fa fa-sync fa-spin"></i>
                            </span>
                        </div>
                    </div>
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
        comments: [],
        postingComment: false,
        changingVisibility: null,
        isAdmin: null,
        reconciliation_id: null,
        commentTemplates: null,
        selectedCommentTemplate: null
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
    watch: {
      selectedCommentTemplate: function (value) {
        if (value) {
          this.comment = value.comment
          this.selectedCommentTemplate = null
        }
      }
    },
    methods: {
      open(transaction_id, reference_id, account_id, referenceType, reconciliation_id) {

        this.transaction_id = null
        this.reconciliation_id = null

        if (transaction_id) {
          this.transactions = null
          this.reconciledTransactions = []
          this.transaction_id = transaction_id
          this.loadWithTransactionId(transaction_id)
        }
        if (reference_id) {
          this.loadWithReferenceId(reference_id, account_id, referenceType)
        }
        if (reconciliation_id) {
          this.reconciliation_id = reconciliation_id
          this.loadWithReconciliationId(reconciliation_id)
        }

        $('#transactionReconciliationModal').modal('toggle')
      },
      loadWithReconciliationId(reconciliation_id) {
        console.log(reconciliation_id);
        axios.get('/account/reconciliation-modal/info', {params: {reconciliation_id}}).then(response => {
          this.transactions = response.data.data.transactions
          this.comments = response.data.data.comments
          let reconcile = response.data.data.transactionsToReconcile
          this.isAdmin = response.data.data.isAdmin
          this.commentTemplates = response.data.data.commentTemplates
          _.each(reconcile, (transaction) => {
            this.reconcileTransaction(transaction)
          })
        })
      },
      loadWithTransactionId(transaction_id) {
        axios.get('/account/reconciliation-modal/info', {params: {transaction_id}}).then(response => {
          this.transactions = response.data.data.transactions
          this.comments = response.data.data.comments
          this.reconcileTransactionsByMainTransaction()
          this.isAdmin = response.data.data.isAdmin
          this.commentTemplates = response.data.data.commentTemplates
        })
      },
      loadWithReferenceId(reference_id, account_id, referenceType) {
        axios.get('/account/reconciliation-modal/info', {params: {reference_id, account_id, referenceType}}).then(response => {
          let data = response.data.data
          this.transactions = data.transactions
          let reconcile = data.transactionsToReconcile
          this.isAdmin = response.data.data.isAdmin
          this.commentTemplates = response.data.data.commentTemplates
          _.each(reconcile, (transaction) => {
            this.reconcileTransaction(transaction)
          })
        })
      },
      save() {
        axios.post('/account/reconciliation-modal/reconcile', {
          transactions: this.reconciledTransactions,
          comment: this.comment
        }).then(response => {
          $('#transactionReconciliationModal').modal('toggle')
          location.reload()
        }).catch(e => {
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
      },
      postComment() {
        this.postingComment = true
        axios.post('/account/reconciliation-modal/comment', {
          comment: this.comment,
          reconciliation_id: this.reconciliation_id,
          transaction_id: this.transaction_id
        }).then(response => {
          this.comments.push(response.data.data)
          this.postingComment = false
          this.comment = null
        }).catch(err => {
          console.log(err)
          this.postingComment = false
          this.comment = null
        })
      },
      toggleCommentScope(comment_id) {
        this.changingVisibility = comment_id
        axios.post('/account/reconciliation-modal/comment/' + comment_id + '/change-scope').then(response => {
          this.changingVisibility = false
          let index = _.findIndex(this.comments, c => c.id === comment_id)
          this.comments[index] = response.data.data

        }).catch(err => {
          console.log(err)
          this.changingVisibility = false
        })
      }
    }
  }
</script>
<style>
    .modal-lg {
        max-width: 80% !important;
    }
</style>