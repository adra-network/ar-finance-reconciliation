<template>
    <div id="allocation-create">
        <div class="form-group">
            <label>Name</label>
            <input type="text" class="form-control" v-model="allocation.name">
        </div>
        <div class="form-group">
            <label>Charge to:</label>
            <select class="form-control" v-model="allocation.charge_to">
                <option :value="value" v-for="(value, title) in ChargeTo.enum">{{ title }}</option>
            </select>
        </div>

        <div class="form-group" v-if="allocation.charge_to === ChargeTo.ACCOUNT">
            <label>Account number:</label>
            <input type="text" class="form-control" v-model="allocation.account_number">
        </div>

        <div v-if="saveButton">
            <div class="btn btn-danger" @click="save">
                <span v-if="!edit">Create</span>
                <span v-if="edit">Update</span>
            </div>
        </div>
    </div>
</template>
<script>
  function allocationData() {
    return {
      id: null,
      name: null,
      charge_to: ChargeTo.NONE,
      account_number: null,
    }
  }

  import ChargeTo from './../ChargeToEnum'

  export default {
    props: {
      saveButton: false,
      redirectAfterSave: false,
      edit: null,
    },
    created() {
      if (this.edit) {
        this.load()
      }
    },
    data() {
      return {
        allocation: allocationData(),
        ChargeTo: ChargeTo
      }
    },
    methods: {
      reset() {
        this.allocation = allocationData()
      },
      load() {
        axios.get('/phone/allocations/' + this.edit).then(response => {
          this.allocation = response.data.data
        }).catch(err => console.log(err))
      },
      save() {
        if (this.allocation.id) {
          axios.put('/phone/allocations/' + this.edit, this.allocation).then(response => {
            this.allocation = response.data.data
            this.$emit('updated', response.data.data)

            if (this.redirectAfterSave) {
              window.location.replace(this.redirectAfterSave)
            }
          }).catch(err => {
            this.$emit('failed', err)
          })
        } else {
          axios.post('/phone/allocations', this.allocation).then(response => {
            this.$emit('created', response.data.data)
            this.reset()

            if (this.redirectAfterSave) {
              window.location.replace(this.redirectAfterSave)
            }
          }).catch(err => {
            this.$emit('failed', err)
          })
        }
      }
    }
  }
</script>