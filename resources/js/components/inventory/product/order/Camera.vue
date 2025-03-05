<template>
    <div>

      <video ref="video" autoplay></video>
      <canvas ref="canvas" style="display: none"></canvas>

      <div>
        <button @click="startCamera" class="btn btn-primary">
            <i class="fas fa-camera"></i> Open Camera
          </button>
          <button @click="captureImage" class="btn btn-success">
            <i class="fas fa-image"></i> Capture
          </button>
          <button @click="stopCamera" class="btn btn-danger">
            <i class="fas fa-power-off"></i> Close Camera
          </button>

      </div>

      <div v-if="capturedImage" class="mt-3">
        <img :src="capturedImage" alt="Captured Image" class="mb-3" style="width: 100px; height: auto; display: block;">

        <input type="text" class="form-control mb-3" v-model="comment" placeholder="Enter Remarks">

        <button @click="uploadImage" class="btn btn-primary w-100">
          <i class="fas fa-upload"></i> Upload Image
        </button>
      </div>

    </div>
  </template>

  <script>

  export default {
    props : ['details'],
    data() {
      return {
        stream: null,
        capturedImage: null,
        imageFile: null,
        comment : ""
      };
    },
    mounted() {
        this.$parent.$on("commentAdded", (value) => {
            if (value) {
                this.close();
            }
        });
    },
    methods: {
      async startCamera() {
        try {
          this.stream = await navigator.mediaDevices.getUserMedia({ video: true });
          this.$refs.video.srcObject = this.stream;
        } catch (error) {
          console.error("Error accessing the camera:", error);
        }
      },
      stopCamera() {
        if (this.stream) {
          this.stream.getTracks().forEach(track => track.stop());
          this.stream = null;
        }
      },
      close(){
        this.stream.getTracks().forEach(track => track.stop());
        this.stream = null;
        this.imageFile = null;
        this.comment = "";
        this.capturedImage = null;
      },
      captureImage() {
        const video = this.$refs.video;
        const canvas = this.$refs.canvas;
        const context = canvas.getContext("2d");

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        this.capturedImage = canvas.toDataURL("image/png");
        this.imageFile = this.dataURLtoFile(this.capturedImage, "captured_image.png");
      },
      dataURLtoFile(dataUrl, filename) {
        let arr = dataUrl.split(","),
          mime = arr[0].match(/:(.*?);/)[1],
          bstr = atob(arr[1]),
          n = bstr.length,
          u8arr = new Uint8Array(n);
        while (n--) {
          u8arr[n] = bstr.charCodeAt(n);
        }
        return new File([u8arr], filename, { type: mime });
      },
      async uploadImage() {
        let vm = this;
        if (!this.imageFile) {
          alert("No image to upload!");
          return;
        }

            const fd = new FormData();
            fd.append('id', vm.details.id);
            fd.append('comment', vm.comment);
            fd.append('attachment', vm.imageFile);

        this.$emit('addCommentDirect', fd)
      },
    },
  };
  </script>

  <style scoped>
  video {
    width: 100%;
    max-width: 400px;
    border: 2px solid #ddd;
    margin-bottom: 10px;
  }
  </style>
