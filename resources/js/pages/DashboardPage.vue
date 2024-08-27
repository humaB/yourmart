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
    setTimeout(() => {
      if (localStorage.getItem("_token") === null) {
        this.generateToken();
      } else {
        axios.defaults.headers.common["Authorization"] =
          "Bearer " + localStorage.getItem("_token");
      }
    }, 500);
  },
  methods: {
    generateToken() {
      axios
        .get(
          window.location.origin +
            localStorage.getItem("_path") +
            "/public/generate_token"
        )
        .then((response) => {
          localStorage.setItem("_token", response.data.response.access_token);
          axios.defaults.headers.common["Authorization"] =
            "Bearer " + localStorage.getItem("_token");
        })
        .catch(() => {
          this.generateToken();
        });
    },
  },
};
</script>
