<template>
    <div class="modal fade" id="transactionCommentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Transaction comment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label>Comment</label>
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
        comment: null
      }
    },
    methods: {
      open(transaction_id) {
        this.transaction_id = transaction_id
        this.load().then(response => {
          $('#transactionCommentModal').modal('toggle')
        }).catch(err => {
          this.$awn.alert("Something went wrong with loading comment data.")
        })
      },
      load() {
        return axios.get('transaction-comment-modal/' + this.transaction_id).then(response => {
          this.comment = response.data.data.comment
        })
      },
      save() {
        axios.post('transaction-comment-modal', {transaction_id: this.transaction_id, comment: this.comment}).then(response => {
//          $('#transactionCommentModal').modal('toggle')
          location.reload()
        }).catch(err => {
          this.$awn.alert("Something went wrong with saving comment data.")
        })
      }
    }

  }
</script>