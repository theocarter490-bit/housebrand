<template>
    <section class="container my-2 py-2 text-center">
        <h2 class="mb-5 text-3xl font-semibold" :style="{'color': colorTheme?.text_primary }">{{ content.label }}</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <!-- Product Card Loop (3 items) -->
            <div class="col" v-for="product in productList">
                <div class="card h-100 border-0 shadow-lg rounded-xl overflow-hidden d-flex flex-column">

                    <div class="card-image-section flex-grow-1"
                         style="flex-basis: 60%; max-height: 60%; overflow: hidden;">
                        <img
                            :src="product.image"
                            alt="Product Image"
                            class="w-100 h-100 object-fit-cover">
                    </div>

                    <div class="card-body text-start" style="flex-basis: 40%; max-height: 40%; overflow: hidden;">
                        <h5 class="card-title fw-bold">{{ product.name }}</h5>
                        <p class="card-text small text-muted">{{ product.category.name }}</p>
                        <p class="fw-bold text-lg text-dark">{{ product.price }}</p>
                    </div>

                </div>
            </div>
        </div>
        <button
            class="btn  fw-bold px-5 py-3 mt-5 rounded-xl shadow-lg hover:shadow-2xl transition duration-300 ease-in-out"
            :style="{'background-color':colorTheme?.primary,'color': colorTheme?.bg_secondary}"
            type="button">VIEW MORE
        </button>
    </section>
</template>
<script>
import axios from "axios";

export default {
    data() {
        return {
            content: {},
            productList: [],
        }
    },
    props: ['shopSetting', 'colorTheme'],
    mounted() {
        this.updateContent();
        this.getProduct();
    },
    methods: {
        updateContent() {
            if (this.shopSetting && this.shopSetting.section_content) {
                try {
                    const parsed = this.shopSetting.section_content;
                    this.content = parsed.product || {};
                } catch (e) {
                    console.error("Invalid section_content format", e);
                    this.content = {};
                }
            }
        },
        getProduct() {
            axios
                .get("/api/v1/product/list", {
                    params: {
                        designer: this.shopSetting.slug,
                        per_page: 3,
                    },
                })
                .then((response) => {
                    this.productList = response.data.data.data;
                })
                .catch((error) => {
                    toastr.error(
                        error.response?.data?.message || "Something went wrong"
                    );
                });
        }
    },
    watch: {
        shopSetting: {
            handler() {
                this.updateContent();
            },
            deep: true, // watches nested object changes too
        }
    },
}
</script>
