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
                    <div v-if="comments.length > 0">
                    <h4>Comments</h4>
                    <div class="row">
                        <div class="col">
                            <div v-for="comment in comments">
                                {{ comment.created_at_formatted }} - {{ comment.user.name }} - {{ comment.comment }}
                            </div>
                        </div>
                    </div>
                    <hr>
                    </div>
                    <label>Comment</label>
                    <br/>
                    <textarea name="comments" rows="3" class="form-control" v-model="comment"></textarea>
                    <br/>
                    <input type="checkbox" name="scope" value="public" v-model="scope" /> Public comment
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
        comments: [],
        comment: null,
        scope: null,
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
        return axios.get('/account/transaction-comment-modal/' + this.transaction_id).then(response => {
          this.comments = response.data.data.comments
        })
      },
      save() {
        axios.post('/account/transaction-comment-modal', {transaction_id: this.transaction_id, comment: this.comment, scope: this.scope}).then(response => {
          location.reload()
        }).catch(err => {
          this.$awn.alert("Something went wrong with saving comment data.")
        })
      }
    }

  }
</script>