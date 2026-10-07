<template xmlns="http://www.w3.org/1999/html">
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

        <div class="mb-3">
            <label class="form-label">Section Label</label>
            <input type="text" v-model="sectionLabel" class="form-control" value="Gallery Section"
                   placeholder="Discover More"/>
        </div>
        <!-- Slider Customization -->
        <div class="border rounded mb-3">
            <div class="nav-align-top nav-tabs-shadow">
                <ul class="nav nav-tabs nav-fill ms-0 me-2" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                data-bs-target="#navs-justified-home" aria-controls="navs-justified-home"
                                aria-selected="true"> Gallery
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                data-bs-target="#navs-justified-profile" aria-controls="navs-justified-profile"
                                aria-selected="false">Images
                        </button>
                    </li>
                </ul>
                <div class="tab-content p-3">
                    <div class="tab-pane fade show active h-100" id="navs-justified-home" role="tabpanel">
                        <form @submit.prevent="storeGallery">
                            <div class="mb-3">
                                <label class="form-label">Title<span class="text-danger">*</span></label>
                                <input type="text" v-model="galleryName" class="form-control"
                                       placeholder="Your Dream Home Awaits"
                                />
                                <span class="text-danger">{{ galleryValidationErrorMessage['name'] }}</span>
                            </div>

                            <!-- Gallery Image Upload -->
                            <div class="mb-3">
                                <label class="form-label">Cover Image<span class="text-danger">*</span></label>
                                <div class="d-flex flex-column align-items-start align-items-sm-center gap-4">
                                    <img :src="galleryImagePreview" id="galleryImage" alt="gallery-cover"
                                         class="d-block w-px-200 h-px-auto rounded"/>

                                    <div class="button-wrapper">
                                        <label for="galleryImageInput"
                                               class="btn btn-primary me-2 mb-3 waves-effect waves-light">
                                            <span class="d-none d-sm-block">Upload</span>
                                            <i class="ti ti-upload d-block d-sm-none"></i>
                                            <input type="file" id="galleryImageInput" @change="previewGalleryImage"
                                                   accept="image/png, image/jpeg, image/jpg" hidden>
                                        </label>

                                        <button type="button" class="btn btn-label-secondary mb-3 waves-effect"
                                                @click="resetGalleryImage">
                                            <span class="d-none d-sm-block">Reset</span>
                                        </button>

                                        <div class="text-muted">Allowed JPG, GIF or PNG. Max size of 800KB</div>
                                    </div>
                                </div>
                                <span class="text-danger">{{ galleryValidationErrorMessage['image'] }}</span>
                            </div>
                            <div class="mb-4 ecommerce-select2-dropdown">
                                <label
                                    class="form-label">Select
                                    Status</label>
                                <select id="gallery-status" v-model.number="galleryStatus" name="status"
                                        class="select2 form-select"
                                        data-placeholder="Select status">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <span class="text-danger statusError error">{{
                                        galleryValidationErrorMessage['status']
                                    }}</span>
                            </div>
                            <div class="text-primary">
                                <button class="btn border border-primary w-100" type="submit"
                                        :disabled="isGallerySubmitting">
                                    <span v-if="!isGallerySubmitting">Add Gallery</span>
                                    <span v-else>
                                        <span class="spinner-border spinner-border-sm me-2" role="status"
                                              aria-hidden="true"></span>
                                        Adding Gallery...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="navs-justified-profile" role="tabpanel">
                        <form @submit.prevent="storeImages">
                            <div class="mb-4 ecommerce-select2-dropdown">
                                <label
                                    class="form-label">Select
                                    Gallery<span class="text-danger">*</span></label>
                                <select id="gallery" v-model.number="gallery_id" name="gallery_id"
                                        class=" form-select"
                                        data-placeholder="Select Gallery">
                                    <option value="" disabled>Select a gallery</option>
                                    <option v-for="item in galleryList" :key="item.id" :value="item.id">
                                        {{ item.name }}
                                    </option>
                                </select>
                                <span class="text-danger statusError error">{{
                                        imageValidationErrorMessage['gallery_id']
                                    }}</span>
                            </div>
                            <hr class="m-2">
                            <div class="mb-3">
                                <label class="form-label">Title<span class="text-danger">*</span></label>
                                <input type="text" v-model="imageTitle" class="form-control"
                                       placeholder="Your Dream Home Awaits"
                                />
                                <span class="text-danger">{{ imageValidationErrorMessage['title'] }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Details</label>
                                <textarea type="text" v-model="imageDetails" class="form-control" rows="2"
                                          placeholder="Enter image details"
                                ></textarea>
                                <span class="text-danger">{{ imageValidationErrorMessage['description'] }}</span>
                            </div>

                            <!-- Image Upload for Gallery Images -->
                            <div class="mb-3">
                                <label class="form-label">Image<span class="text-danger">*</span></label>
                                <div class="d-flex flex-column align-items-start align-items-sm-center gap-4">
                                    <img :src="imagePreview" id="imagePreviewImg" alt="image-preview"
                                         class="d-block w-px-200 h-px-auto rounded"/>

                                    <div class="button-wrapper">
                                        <label for="imageInput"
                                               class="btn btn-primary me-2 mb-3 waves-effect waves-light">
                                            <span class="d-none d-sm-block">Upload</span>
                                            <i class="ti ti-upload d-block d-sm-none"></i>
                                            <input type="file" id="imageInput" @change="previewImage"
                                                   accept="image/png, image/jpeg, image/jpg" hidden>
                                        </label>

                                        <button type="button" class="btn btn-label-secondary mb-3 waves-effect"
                                                @click="resetImage">
                                            <span class="d-none d-sm-block">Reset</span>
                                        </button>
                                    </div>
                                </div>
                                <span class="text-danger">{{ imageValidationErrorMessage['image'] }}</span>
                            </div>
                            <div class="text-primary">
                                <button class="btn border border-primary w-100" type="submit"
                                        :disabled="isImageSubmitting">
                                    <span v-if="!isImageSubmitting">Add Image</span>
                                    <span v-else>
                                        <span class="spinner-border spinner-border-sm me-2" role="status"
                                              aria-hidden="true"></span>
                                        Adding Image...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="row">
            <ProceedButton
                @proceed-to-next="$emit('proceed-to-next',{ section: 'gallery', display_control: displayControl,label:sectionLabel})"/>
        </div>

    </div>
</template>

<script>
import axios from "axios";
import ProceedButton from "./components/ProceedButton.vue";

export default {
    components: {ProceedButton},
    data() {
        return {
            displayControl: false,
            sectionLabel: '',

            // Gallery List
            galleryList: [],

            // Gallery Form Data
            galleryName: '',
            galleryImageFile: null,
            galleryStatus: 1,
            galleryImagePreview: "https://placeholder.pics/svg/600x300/DEDEDE/555555/Gallery%20Cover",
            galleryValidationErrorMessage: [],
            isGallerySubmitting: false,

            // Image Form Data
            gallery_id: '',
            imageTitle: "",
            imageDetails: "",
            imageFile: null,
            imageStatus: 1,
            imagePreview: "https://placeholder.pics/svg/600x300/DEDEDE/555555/Gallery%20Image",
            imageValidationErrorMessage: [],
            isImageSubmitting: false,
        };
    },
    props: ['shopSetting'],
    methods: {
        // Gallery Image Preview and Reset
        previewGalleryImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.galleryImageFile = file;
                this.galleryImagePreview = URL.createObjectURL(file);
            }
        },
        resetGalleryImage() {
            this.galleryImageFile = null;
            this.galleryImagePreview = "https://placeholder.pics/svg/600x300/DEDEDE/555555/Gallery%20Cover";
            document.getElementById("galleryImageInput").value = "";
        },

        // Image Preview and Reset
        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.imageFile = file;
                this.imagePreview = URL.createObjectURL(file);
            }
        },
        resetImage() {
            this.imageFile = null;
            this.imagePreview = "https://placeholder.pics/svg/600x300/DEDEDE/555555/Gallery%20Image";
            document.getElementById("imageInput").value = "";
        },

        // Reset Gallery Form
        resetGalleryForm() {
            this.galleryName = '';
            this.galleryImageFile = null;
            this.galleryStatus = 1;
            this.galleryImagePreview = "https://placeholder.pics/svg/600x300/DEDEDE/555555/Gallery%20Cover";
            this.galleryValidationErrorMessage = [];
            document.getElementById("galleryImageInput").value = "";
        },

        // Reset Image Form
        resetImageForm() {
            this.gallery_id = '';
            this.imageTitle = "";
            this.imageDetails = "";
            this.imageFile = null;
            this.imagePreview = "https://placeholder.pics/svg/600x300/DEDEDE/555555/Gallery%20Image";
            this.imageValidationErrorMessage = [];
            document.getElementById("imageInput").value = "";
        },

        // Store Gallery
        storeGallery() {
            if (this.isGallerySubmitting) return;

            this.isGallerySubmitting = true;
            this.galleryValidationErrorMessage = [];

            const formData = new FormData();
            formData.append("name", this.galleryName);
            formData.append("status", this.galleryStatus);
            if (this.galleryImageFile) {
                formData.append("image", this.galleryImageFile);
            }

            axios.post("/gallery/store", formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            })
                .then(response => {
                    if (response.data.errors && response.data.status === 403) {
                        this.galleryValidationErrorMessage = {};
                        const errors = response.data.errors || {};
                        Object.keys(errors).forEach((field) => {
                            this.galleryValidationErrorMessage[field] = errors[field][0];
                        });
                        return;
                    }
                    toastr.success(response.data.message);
                    this.resetGalleryForm();
                    this.getGalleries();
                    this.$emit('update-content', {content: 'galleryContent'});
                })
                .catch(error => {
                    toastr.error(error.response?.data?.message || "Something went wrong");
                })
                .finally(() => {
                    this.isGallerySubmitting = false;
                });
        },

        // Store Images
        storeImages() {
            if (this.isImageSubmitting) return;

            this.isImageSubmitting = true;
            this.imageValidationErrorMessage = [];

            const formData = new FormData();
            formData.append("gallery_id", this.gallery_id);
            formData.append("title", this.imageTitle);
            formData.append("description", this.imageDetails);

            if (this.imageFile) {
                formData.append("image", this.imageFile);
            }

            axios.post("/gallery/details/storeSingleDetails", formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            })
                .then(response => {

                    if (response.data.errors && response.data.status === 403) {
                        this.imageValidationErrorMessage = {};
                        const errors = response.data.errors || {};
                        Object.keys(errors).forEach((field) => {
                            this.imageValidationErrorMessage[field] = errors[field][0];
                        });
                        return;
                    }
                    toastr.success(response.data.message);
                    this.resetImageForm();
                    this.$emit('update-content', {content: 'galleryContent'});
                })
                .catch(error => {
                    toastr.error(error.response?.data?.message || "Something went wrong");
                })
                .finally(() => {
                    this.isImageSubmitting = false;
                });
        },

        // Fetch Galleries
        getGalleries() {
            axios.get("/gallery/getGallery/list")
                .then(response => {
                    this.galleryList = response.data.gallery;
                })
                .catch(error => {
                    toastr.error(
                        error.response?.data?.message || "Failed to load galleries"
                    );
                });
        },
    },
    mounted() {
        this.getGalleries();
        const data = this.shopSetting.section_content;

        this.displayControl = data['gallery']['display_control'];
        this.sectionLabel = data['gallery']['label'];
    }

};
</script>
