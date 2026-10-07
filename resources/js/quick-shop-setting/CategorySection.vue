<template>
    <div class="p-2 bg-body rounded">
        <!-- Slider Form -->

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
            <input type="text" class="form-control" v-model="sectionLabel" placeholder="Discover More"/>
        </div>
        <button class="btn btn-primary w-100 mb-3" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasEcommerceCategoryList">
            <i class="ti ti-plus ti-xs me-0 me-sm-2">

            </i>Create Category
        </button>
        <!-- Slider Customization -->
        <div class="border p-2 rounded mb-3 bg-white">
            <div class="mb-3">
                <label for="TagifyCategory" class="form-label">Category List</label>
                <input
                    id="TagifyCategory"
                    name="TagifyProductList"
                    class="form-control"
                    placeholder="Select Category"
                    value=""/>
            </div>

            <div
                class="mb-3 bg-label-warning rounded p-2 d-flex flex-row gap-2 align-items-center justify-content-center">
                <div>
                    <i class="ti ti-alert-triangle text-warning bg-white p-2 rounded"></i>
                </div>
                <div>
                    Categories without any products won’t be visible on your public shop. You’ll be able to add
                    products to your categories while adding a New Product.
                </div>
            </div>

        </div>

        <!-- Submit -->
        <div class="row">
            <ProceedButton
                @proceed-to-next="$emit('proceed-to-next',{ section: 'category', display_control: displayControl,label:sectionLabel })"/>
        </div>


        <!-- Offcanvas to add new customer -->
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
                <form class="pt-0" id="addModal" @submit.prevent="store">
                    <!-- Title -->
                    <div class="mb-3">
                        <label class="form-label" for="ecommerce-category-title">Name <span
                            class="text-danger">*</span></label>
                        <input type="text" v-model="name" class="form-control" id="ecommerce-category-title"
                               placeholder="Enter category name" name="name" aria-label="category title"/>
                        <span class="text-danger nameError error">{{ validationErrorMessage['name'] }}</span>
                    </div>

                    <!-- Image -->
                    <div class="mb-3">
                        <label class="form-label"
                               for="category-image">Category Image
                            <span class="text-danger">*</span></label>
                        <input class="form-control" ref="file" @change="setImage" type="file" name="image"
                               id="category-image"/>
                        <span class="text-danger imageError error">{{ validationErrorMessage['image'] }}</span>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" v-model="description" class="form-control" id="category-descripton"
                                  cols="30"
                                  rows="10"></textarea>
                        <span class="text-danger descriptionError error">{{
                                validationErrorMessage['description']
                            }}</span>
                    </div>
                    <!-- Status -->
                    <div class="mb-4 ecommerce-select2-dropdown">
                        <label
                            class="form-label">Select Category
                            Status</label>
                        <select id="category-status" v-model.number="status" name="status" class="select2 form-select"
                                data-placeholder="Select category status">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <span class="text-danger statusError error">{{ validationErrorMessage['status'] }}</span>
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
            tagifyCategoryList: null,
            name: "",
            displayControl: '',
            sectionLabel: '',
            description: "",
            status: 1,
            imageFile: '',
            categoryList: [],
            validationErrorMessage: [],
        };
    },
    props: ['shopSetting'],
    methods: {
        setImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.imageFile = file;
            }
        },
        get() {
            axios.get("/setting/quick-shop-setup/category")
                .then(response => {
                    response.data.category.forEach((value) => {
                        this.categoryList.push({
                            value: value.id,
                            name: value.name,
                            avatar: value.image,
                        });
                    });
                    this.tagifyCategoryListInit(this.categoryList);
                })
                .catch(error => {
                    toastr.error(
                        error.response?.data?.message || "Something went wrong"
                    );
                });
        },
        store() {
            this.validationErrorMessage = {};
            const formData = new FormData();

            formData.append("name", this.name);
            formData.append("description", this.description);
            formData.append("status", this.status);
            if (this.imageFile) {
                formData.append("image", this.imageFile);
            }

            // Send to backend via Axios
            axios.post("/category/store", formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            })
                .then(response => {
                    if (response.data.errors && response.data.status === 403) {

                        this.validationErrorMessage = {};

                        const errors = response.data.errors || {};

                        Object.keys(errors).forEach((field) => {
                            this.validationErrorMessage[field] = errors[field][0]; // Take first message
                        });


                        return;
                    }
                    toastr.success(response.data.message);
                    this.name = '';
                    this.description = "";
                    this.imageFile = '';
                    this.$refs.file.value = null;

                    this.closeOffcanvas();
                    this.get();
                    this.$emit('update-content', {content: 'categoryContent'});
                })
                .catch(error => {

                    toastr.error(error.response?.data?.message || "Something went wrong");
                });
        },
        tagifyCategoryListInit(dataList) {
            const tagifyCategoryListEl = document.querySelector('#TagifyCategory');
            // Destroy the existing Tagify instance if it exists
            if (this.tagifyCategoryList) {
                this.tagifyCategoryList.removeAllTags();
                this.tagifyCategoryList.destroy();
            }

            function tagTemplate(tagData) {
                return `
                <tag title="${tagData.name}"
                  contenteditable='false'
                  spellcheck='false'
                  tabIndex="-1"
                  class="${this.settings.classNames.tag} ${tagData.class ? tagData.class : ''}"
                  ${this.getAttributes(tagData)}
                >
                  <x title='' class='tagify__tag__removeBtn' role='button' aria-label='remove tag'></x>
                  <div>
                    <div class='tagify__tag__avatar-wrap'>
                      <img onerror="this.style.visibility='hidden'" src="${tagData.avatar}">
                    </div>
                    <span class='tagify__tag-text'>${tagData.name}</span>
                  </div>
                </tag>
              `;
            }

            function suggestionItemTemplate(tagData) {
                return `
                    <div ${this.getAttributes(tagData)}
                      class='tagify__dropdown__item align-items-center ${tagData.class ? tagData.class : ''}'
                      tabindex="0"
                      role="option"
                    >
                      ${
                    tagData.avatar
                        ? `<div class='tagify__dropdown__item__avatar-wrap'>
                          <img onerror="this.style.visibility='hidden'" src="${tagData.avatar}">
                        </div>`
                        : ''
                }
                      <div class="fw-medium">${tagData.name}</div>
                    </div>
                  `;
            }

            function dropdownHeaderTemplate(suggestions) {
                return `
                    <div class="${this.settings.classNames.dropdownItem} ${this.settings.classNames.dropdownItem}__addAll">
                        <strong>${this.value.length ? `Select remaining` : 'Select All'}</strong>
                        <span>${suggestions.length} products</span>
                    </div>`;
            }

            // initialize Tagify on the above input node reference
            this.tagifyCategoryList = new Tagify(tagifyCategoryListEl, {
                tagTextProp: 'name',
                enforceWhitelist: true,
                skipInvalid: true,
                dropdown: {
                    closeOnSelect: false,
                    enabled: 0,
                    classname: 'users-list',
                    searchKeys: ['name']
                },
                templates: {
                    tag: tagTemplate,
                    dropdownItem: suggestionItemTemplate,
                    dropdownHeader: dropdownHeaderTemplate
                },
                whitelist: dataList
            });

            // Step 4: Reapply the selected tags
            this.tagifyCategoryList.removeAllTags();

            // attach events listeners
            this.tagifyCategoryList.on('dropdown:select', onSelectSuggestion) // allows selecting all the suggested (whitelist) items
                .on('edit:start', onEditStart); // show custom text in the tag while in edit-mode

            function onSelectSuggestion(e) {
                // custom class from "dropdownHeaderTemplate"
                if (e.detail.elm.classList.contains(`${this.tagifyCategoryList.settings.classNames.dropdownItem}__addAll`))
                    this.tagifyCategoryList.dropdown.selectAll();
            }

            function onEditStart({detail: {tag, data}}) {
                this.tagifyCategoryList.setTagTextNode(tag, `${data.name}`);
            }


        },
        closeOffcanvas() {
            document.getElementById('closeAddModal').click();
        }
    },
    mounted() {
        this.get();
        const data = this.shopSetting.section_content;
        this.displayControl = data['category']['display_control'];
        this.sectionLabel = data['category']['label'];

    }
}
</script>
