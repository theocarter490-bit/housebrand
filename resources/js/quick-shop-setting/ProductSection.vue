<template>
    <div class="p-2 bg-body rounded">


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
        <!-- Slider Customization -->
        <div class="d-flex flex-column gap-2 p-2 mb-2">
            <div>
                To add products in “Our Products” section, please first add them through the <a
                class="text-decoration-underline" href="/product/create">Add Product page</a>. Also, you can fill in all
                the
                required details from the pages below —
            </div>
            <a href="/brand" class="text-decoration-underline">Brands</a>
            <a href="/attribute" class="text-decoration-underline">Attribute</a>
            <a href="/unit" class="text-decoration-underline">Unit</a>
            <a href="/bulk-import" class="text-decoration-underline">Import</a>
        </div>


        <!-- Submit -->
        <div class="row">
            <ProceedButton
                @proceed-to-next="$emit('proceed-to-next',{ section: 'product', display_control: displayControl,label:sectionLabel })"/>
        </div>


        <!-- Offcanvas to add new customer -->

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
            sectionLabel: '',
        };
    },
    mounted() {

        const data = this.shopSetting.section_content;
        this.displayControl = data['product']['display_control'];
        this.sectionLabel = data['product']['label'];

    }
}
</script>
