<template>
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Create allocation</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <allocation-form ref="allocationForm" @created="onAllocationCreated"></allocation-form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="previousView">Back</button>
            <button type="button" class="btn btn-primary" @click="$refs.allocationForm.save()">Create</button>
        </div>
    </div>
</template>
<script>
  export default {

    methods: {
      previousView() {
        this.$emit('previousView')
      },
      onAllocationCreated(allocation) {
        this.$emit('allocationCreated', allocation)
      }
    }

  }
</script>