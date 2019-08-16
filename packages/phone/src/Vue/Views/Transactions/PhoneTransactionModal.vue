<template>
    <!-- Modal -->
    <div class="modal fade" id="phoneTransactionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">

            <allocation-form-content
                    v-show="view === 'allocation-form'"
                    @previousView="previousView"
                    @allocationCreated="onAllocationCreated"
            ></allocation-form-content>
            <phone-transaction-form-content
                    ref="phoneTransactionFormContent"
                    v-show="view === 'phone-transaction'"
                    :allocations='allocations'
                    @view="changeView"
            ></phone-transaction-form-content>

        </div>
    </div>
</template>
<script>
  import AllocationFormContent from './_AllocationFormContent'
  import PhoneTransactionFormContent from './_PhoneTransactionFormContent'

  export default {
    components: {
      AllocationFormContent,
      PhoneTransactionFormContent,
    },
    data() {
      return {
        views: [
          {'name': 'allocation-form'},
          {'name': 'phone-transactions'},
        ],
        viewHistory: [],
        transaction_id: null,
        allocations: null,
      }
    },
    computed: {
      view() {
        return _.last(this.viewHistory)
      }
    },
    methods: {
      open({
             view,
             transaction_id = null,
             caller_phone_number_id = null,
           }) {

        this.viewHistory = []
        this.changeView(view)

        this.loadAllocations()

        this.$refs.phoneTransactionFormContent.load({transaction_id, caller_phone_number_id})

        $('#phoneTransactionModal').modal('show')
      },

      loadAllocations() {
        return axios.get('/phone/allocations').then(response => {
          this.allocations = response.data.data
        })
      },

      /* VIEWS */
      changeView(to = null) {
        to = to || 'index'
        if (_.last(this.viewHistory) !== to) {
          this.viewHistory.push(to)
        }
      },
      previousView() {
        this.viewHistory.pop()
      },
      onAllocationCreated(allocation) {
        this.loadAllocations()
        this.$refs.phoneTransactionFormContent.setAllocationId(allocation.id)
        this.previousView()
      }
    }
  }
</script>
<style>
    .modal-lg {
        max-width: 80% !important;
    }
</style>