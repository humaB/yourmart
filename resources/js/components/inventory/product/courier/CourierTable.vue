<template>
  <div class="table-responsive">
    <table
      class="table table-striped table-hover"
      :id="id"
      style="width: 100%"
    >
      <thead>
        <tr>
          <th v-for="item in th" :key="item">
            {{ item }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in tbody" :key="item.id">
          <td>{{ index + 1 }}</td>
          <td>{{ item.courier_name }}</td>
          <td>{{ item.contact_person }}</td>
          <td>{{ item.contact_person_contact }}</td>
          <td>
            <a href="#" class="btn btn-icon icon-left btn-info" data-toggle="modal" data-target="#courierDetailPopup" @click="fetchDetails(item.id)">
                <i class="far fa-eye"></i>
              </a>
            <a href="#" class="btn btn-icon icon-left btn-primary" data-toggle="modal" data-target="#editCourier" @click="edit(item.id, item.name, item.contact, item.address)">
              <i class="far fa-edit"></i>
            </a>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: "CourierTable",
  props: ["id", "th", "tbody", "edit_form"],
  data() {
    return {
      public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
    };
  },
  methods: {
    edit(id, name, contact, address) {
      this.$emit('edit', { id, name, contact, address });
    },
    fetchDetails( id ){
        this.$emit('fetchDetails', { id })
    }
  }
};
</script>

<style scoped>
.cap {
  text-transform: capitalize;
}
</style>
