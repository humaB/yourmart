<template>
  <div>
      <div class="form-group form-float col-md-4">
        <div class="form-line">
            <label class="form-label"
              >Department
              <span class="text-danger">*</span></label
            >
            <input type="text" class="form-control">
        </div>
      </div>
      <div class="form-group form-float col-md-4">
        <div class="form-line">
            <label class="form-label"
              >Designation
              <span class="text-danger">*</span></label
            >
            <input type="text" class="form-control">
        </div>
      </div>
  </div>
</template>
<script>
export default {
  name: "UserAdminForm",
};
</script>
