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

        <div class="mb-3">
            <label class="form-label">Section Label</label>
            <input type="text" v-model="sectionLabel" class="form-control" value="Product Section"
                   placeholder="Discover More"/>
            <span class="text-danger">{{ validationErrorMessage['sectionLabel'] }}</span>
        </div>
        <div class="mb-3">
            <label class="form-label">Section Description</label>
            <textarea v-model="sectionDescription" class="form-control" rows="2"
                      placeholder="Create the perfect f..."
            ></textarea>
            <span class="text-danger">{{ validationErrorMessage['sectionDescription'] }}</span>
        </div>


        <!-- Slider Customization -->
        <label class="form-label">Add New Inspiration</label>
        <form @submit.prevent="store">
            <div class="border p-2 rounded mb-3 bg-white">
                <div class="mb-3">
                    <label class="form-label">Title<span class="text-danger">*</span></label>
                    <input type="text" v-model="name" class="form-control" placeholder="Your Dream Home Awaits"
                    />
                    <span class="text-danger">{{ validationErrorMessage['name'] }}</span>
                </div>
                <!-- Image Upload -->
                <div class="mb-3">
                    <label class="form-label">Inspiration Image<span class="text-danger">*</span></label>
                    <div class="d-flex flex-column align-items-start align-items-sm-center gap-4">
                        <img :src="imagePreview" id="darkLogo" alt="user-avatar"
                             class="d-block w-px-200 h-px-auto rounded"/>

                        <div class="button-wrapper">
                            <label for="darkLogoInput" class="btn btn-primary me-2 mb-3 waves-effect waves-light">
                                <span class="d-none d-sm-block">Upload</span>
                                <i class="ti ti-upload d-block d-sm-none"></i>
                                <input type="file" id="darkLogoInput" @change="previewImage"
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
                <div class="mb-3 ecommerce-select2-dropdown">
                    <label
                        class="form-label">Select Category</label>
                    <div class="d-flex align-items-center gap-1">
                        <div class="col-9">
                            <select id="category-status" v-model.number="category_id" name="status"
                                    class="select2 form-select"
                                    data-placeholder="Select category">
                                <option value="" disabled>Select a category</option>
                                <option v-for="item in categoryList" :key="item.id" :value="item.id">{{
                                        item.name
                                    }}
                                </option>
                            </select>
                            <span class="text-danger statusError error">{{
                                    validationErrorMessage['category_id']
                                }}</span>

                        </div>
                        <div class="col-2">
                            <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas"
                                    data-bs-target="#offcanvasEcommerceCategoryList"><i
                                class="ti ti-plus ti-xs me-0"></i></button>
                        </div>

                    </div>
                </div>
                <div class="mb-4 ecommerce-select2-dropdown">
                    <label
                        class="form-label">Select Status</label>
                    <select id="category-status" v-model.number="status" name="status" class="select2 form-select"
                            data-placeholder="Select category status">
                        <option value="1" selected>Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    <span class="text-danger statusError error">{{ validationErrorMessage['status'] }}</span>
                </div>
                <div class="mb-3">
                    <button class="btn border border-primary text-primary w-100">Submit</button>
                </div>

            </div>
        </form>

        <!-- Submit -->
        <div class="row">
            <ProceedButton
                @proceed-to-next="$emit('proceed-to-next',{ section: 'inspiration', display_control: displayControl,label:sectionLabel,description:sectionDescription })"/>
        </div>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEcommerceCategoryList"
             aria-labelledby="offcanvasEcommerceCategoryListLabel">
            <!-- Offcanvas Header -->
            <div class="offcanvas-header py-4">
                <h5 id="offcanvasEcommerceCategoryListLabel"
                    class="offcanvas-title">Add Category</h5>
                <button type="button" id="closeAddModal" class="btn-close bg-label-secondary text-reset"
                        data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div>
            <!-- Offcanvas Body -->
            <div class="offcanvas-body border-top">
                <form class="pt-0" id="addModal" @submit.prevent="storeCategory">
                    <!-- Title -->
                    <div class="mb-3">
                        <label class="form-label" for="ecommerce-category-title">Name <span
                            class="text-danger">*</span></label>
                        <input type="text" v-model="categoryName" class="form-control" id="ecommerce-category-title"
                               placeholder="Enter category name" name="name" aria-label="category title"/>
                        <span class="text-danger nameError error">{{ categoryValidationErrorMessage['name'] }}</span>
                    </div>

                    <!-- Status -->
                    <div class="mb-4 ecommerce-select2-dropdown">
                        <label
                            class="form-label">Select Category
                            Status</label>
                        <select id="category-status" v-model.number="categoryStatus" name="status"
                                class="select2 form-select"
                                data-placeholder="Select category status">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <span class="text-danger statusError error">{{
                                categoryValidationErrorMessage['status']
                            }}</span>
                    </div>
                    <!-- Submit and reset -->
                    <div class="mb-3">
                        <button type="submit"
                                class="btn btn-primary me-sm-3 me-1 data-submit">Add
                            <span class="loader"></span>
                        </button>
                        <button type="reset" class="btn bg-label-danger"
                                data-bs-dismiss="offcanvas">Discard
                        </button>
                    </div>
                </form>
            </div>
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
            sectionDescription: '',

            categoryList: [],

            categoryName: "",
            categoryStatus: 1,
            categoryValidationErrorMessage: [],

            name: "",
            imageFile: null,
            category_id: '',
            status: 1,
            sectionType: 2,
            imagePreview: "https://placeholder.pics/svg/600x300/DEDEDE/555555/Banner%20Image",
            validationErrorMessage: [],
        };
    },
    props: ['shopSetting'],
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
            this.imagePreview = "https://placeholder.pics/svg/600x300/DEDEDE/555555/Banner%20Image";
        },
        store() {

            // Prepare FormData for backend
            const formData = new FormData();
            formData.append("display_control", this.displayControl ? 1 : 0);
            formData.append("name", this.name);
            formData.append("status", this.status);
            formData.append("category_id", this.category_id);
            if (this.imageFile) {
                formData.append("image", this.imageFile);
            }


            axios.post(`/section/portfolioAndInspiration/` + this.sectionType + `/store`, formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            })
                .then(response => {
                    if (response.data.errors && response.data.status === 403) {

                        this.validationErrorMessage = {};

                        const errors = response.data.errors || {};

                        Object.keys(errors).forEach((field) => {
                            this.validationErrorMessage[field] = errors[field][0];
                        });
                        return;
                    }
                    toastr.success(response.data.message);
                    this.resetForm();
                    this.$emit('update-content', {content: 'inspirationContent'});
                })
                .catch(error => {


                    // Handle other types of errors
                    toastr.error(error.response?.data?.message || "Something went wrong");
                });
        },
        resetForm() {
            this.name = "";
            this.imageFile = null;
            this.category_id = '';
            this.status = 1;
            this.imagePreview = "https://placeholder.pics/svg/600x300/DEDEDE/555555/Banner%20Image";
            this.validationErrorMessage = [];
        },

        storeCategory() {
            const formData = new FormData();
            formData.append("name", this.categoryName);
            formData.append("status", this.categoryStatus);

            // Send to backend via Axios
            axios.post("/section/category/store", formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            })
                .then(response => {
                    if (response.data.errors && response.data.status === 403) {

                        this.categoryValidationErrorMessage = {};

                        const errors = response.data.errors || {};

                        Object.keys(errors).forEach((field) => {
                            this.categoryValidationErrorMessage[field] = errors[field][0];
                        });
                        return;
                    }
                    toastr.success(response.data.message);
                    this.closeOffcanvas();
                    this.getCategories();
                    this.categoryName = "";
                    this.categoryStatus = 1;
                })
                .catch(error => {
                    // Handle other types of errors
                    toastr.error(error.response?.data?.message || "Something went wrong");
                });
        },
        getCategories() {
            axios.get("/section/category/getCategories")
                .then(response => {
                    this.categoryList = response.data.categories;
                })
                .catch(error => {
                    toastr.error(
                        error.response?.data?.message || "Something went wrong"
                    );
                });
        },
        closeOffcanvas() {
            document.getElementById('closeAddModal').click();
        }
    },
    mounted() {
        this.getCategories();


        const data = this.shopSetting.section_content;
        this.displayControl = data['inspiration']['display_control'];
        this.sectionLabel = data['inspiration']['label'];
        this.sectionDescription = data['inspiration']['description'];


        // this.tagifyCategoryListInit(this.categoryList);
    }

};
</script>
