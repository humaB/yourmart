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
          <tr v-for="(item , index) in tbody" :key="item.id">
            <td>{{ index + 1 }}</td>
            <td>{{ item.name }}</td>
            <td>{{ item.email }}</td>
            <td > {{ item.role }}</td>
            <td> {{ item.allowed_ip_address }}</td>
            <td>

              <a href="#" class="btn btn-icon icon-left btn-primary" data-toggle="modal" data-target="#editUser" @click="edit( item.id ,item.name, item.email, item.role )"><i class="far fa-edit"></i>
              </a>
              <a href="#" class="btn btn-icon icon-left btn-danger" data-toggle="modal" data-target="#delete_confirmation" @click="deleteFunc( item.id )"><i class="fas fa-trash-alt"></i>
              </a>
              </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>

  <script>
  export default {
    name: "UserTable",
    props: ["id","th", "tbody", "edit_form"],
    data() {
      return {
        public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
      }
    },
    methods : {
        edit( id, name, email , role ){
            let password = "";
            this.$emit( 'edit', { id, name, email , password , role } )
        }
    }
  };
  </script>

  <style scoped>
  .cap {
    text-transform: capitalize;
  }
  </style>
