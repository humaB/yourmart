<template>
  <div>ABC</div>
</template>

<script>
export default {
  data() {
    return {
      api_url: process.env.MIX_API_URL,
      public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
    };
  },
  created() {

  },
  methods: {
  },
};
</script>
