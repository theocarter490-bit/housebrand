<template>
    <div class="p-2 bg-body rounded">
        <!-- Slider Form -->

        <!-- Display Control -->
        <div class="mb-3">
            <label class="switch switch-success">
                <span class="switch-label">Display Control</span>
                <input type="checkbox" v-model="displayControl" class="switch-input"/>
                <span class="switch-toggle-slider">
            <span class="switch-on"></span>
            <span class="switch-off"></span>
          </span>
            </label>
        </div>

        <hr class="m-2">

        <!-- Slider Customization -->
        <div class="border p-2 rounded mb-3 bg-white">
            <form @submit.prevent="store()">
                <div class="mb-3">
                    <label class="form-label">Title<span class="text-danger">*</span></label>
                    <input type="text" v-model="title" class="form-control" placeholder="Your Dream Home Awaits"
                    />
                    <span class="text-danger">{{ validationErrorMessage['title'] }}</span>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description<span class="text-danger">*</span></label>
                    <textarea v-model="description" class="form-control" rows="2" placeholder="Create the perfect ..."
                    ></textarea>
                    <span class="text-danger">{{ validationErrorMessage['description'] }}</span>
                </div>

                <div class="mb-3">
                    <label class="form-label">File Type</label>
                    <select class="form-select" id="fileType" v-model.number="fileType">
                        <option value="0">Image</option>
                        <option value="1">Video</option>
                    </select>
                    <span class="text-danger">{{ validationErrorMessage['file_type'] }}</span>
                </div>

                <!-- Image Upload -->
                <div class="mb-3">
                    <div class="d-flex flex-column align-items-start align-items-sm-center gap-4">
                        <img :src="imagePreview" id="darkLogo" alt="user-avatar"
                             class="d-block w-px-200 h-px-auto rounded"/>

                        <div class="button-wrapper">
                            <label for="darkLogoInput" class="btn btn-primary me-2 mb-3 waves-effect waves-light">
                                <span class="d-none d-sm-block">Upload</span>
                                <i class="ti ti-upload d-block d-sm-none"></i>
                                <input type="file" ref="file" id="darkLogoInput" @change="previewImage"
                                       accept="image/png, image/jpeg, image/jpg" hidden>
                            </label>

                            <button type="button" class="btn btn-label-secondary mb-3 waves-effect" @click="resetImage">
                                <span class="d-none d-sm-block">Reset</span>
                            </button>

                            <div class="text-muted">Allowed JPG, GIF or PNG. Max size of 800KB</div>
                        </div>
                    </div>
                    <span class="text-danger">{{ validationErrorMessage['image'] }}</span>
                </div>
                <div class="mb-3">
                    <button class="btn border border-primary text-primary w-100">Submit</button>
                </div>
            </form>
        </div>

        <!-- Submit -->
        <div class="row">
            <ProceedButton
                @proceed-to-next="$emit('proceed-to-next', { section: 'hero', display_control: displayControl })"/>
        </div>

    </div>
</template>

<script>
import axios from "axios";
import ProceedButton from "./components/ProceedButton.vue";

export default {
    components: {ProceedButton},
    props: ['shopSetting'],
    data() {
        return {
            displayControl: false,
            section: '',
            title: "",
            description: "",
            fileType: 0,
            imageFile: null,
            imagePreview: "https://placeholder.pics/svg/600x300/DEDEDE/555555/Banner%20Image",
            validationErrorMessage: [],
        };
    },
    methods: {
        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.imageFile = file;
                this.imagePreview = URL.createObjectURL(file);
            }
        },
        resetImage() {
            this.imageFile = null;
            this.imagePreview = "";
            document.getElementById("darkLogoInput").value = "";
            this.imagePreview = "https://placeholder.pics/svg/600x300/DEDEDE/555555/Banner%20Image";
        },
        store() {

            // Prepare FormData for backend
            const formData = new FormData();
            formData.append("display_control", this.displayControl ? 1 : 0);
            formData.append("title", this.title);
            formData.append("description", this.description);
            formData.append("file_type", this.fileType);
            if (this.imageFile) {
                formData.append("image", this.imageFile);
            }

            // Send to backend via Axios
            axios.post("/setting/quick-shop-setup/heroSection", formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            })
                .then(response => {
                    toastr.success(response.data.message);
                    this.title = "";
                    this.description = "";
                    this.fileType = 0;
                    this.imageFile = null;
                    this.$refs.file.value = null;
                    this.imagePreview = "https://placeholder.pics/svg/600x300/DEDEDE/555555/Banner%20Image";
                    this.sliderStyle = 1;
                    this.$emit('update-content', {content: 'heroContent'});
                })
                .catch(error => {
                    if (error.response && error.response.status === 403) {
                        // Reset old errors
                        this.validationErrorMessage = {};

                        // Laravel validation errors are usually in error.response.data.errors
                        const errors = error.response.data.data || {};

                        // Loop through and assign each error message
                        Object.keys(errors).forEach((field) => {
                            this.validationErrorMessage[field] = errors[field][0]; // Take first message
                        });

                        return;
                    }

                    // Handle other types of errors
                    toastr.error(error.response?.data?.message || "Something went wrong");
                });
        },
    },
    mounted() {
        const data = this.shopSetting.section_content;
        this.displayControl = data['hero']['display_control'];
    }
};
</script>
