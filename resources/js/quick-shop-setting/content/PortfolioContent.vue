<template>
    <section class="container py-5 text-center">
        <h2 class="mb-1 text-3xl font-semibold" :style="{'color': colorTheme?.text_primary }">{{ content.label }}</h2>
        <p class="text-muted mb-4">{{ content.description }}</p>
        <div class="row g-2">
            <div class="col-md-3" v-for="(portfolio, index) in portfolioList" :key="portfolio.id">
                <div
                    class="position-relative rounded-lg shadow-sm overflow-hidden"
                    style="height: 150px; background-color: #f8f9fa;"
                >
                    <!-- Background Image -->
                    <img
                        :src="portfolio.image"
                        alt="Portfolio Image"
                        class="w-100 h-100 position-absolute top-0 start-0"
                        style="object-fit: cover;"
                    />

                    <!-- Floating Info Box -->
                    <div
                        class="position-absolute bottom-0 start-0 end-0 p-2 px-3"
                        style="background: rgba(255, 255, 255, 0.85);"
                    >
                        <p class="mb-1 text-secondary small">{{ portfolio.category?.name }}</p>
                        <h6 class="mb-0 fw-semibold text-dark">{{ portfolio.title }}</h6>
                    </div>
                </div>
            </div>

        </div>
        <button
            class="btn fw-bold px-5 py-3 mt-5 rounded-xl shadow-lg hover:shadow-2xl transition duration-300 ease-in-out"
            :style="{'background-color':colorTheme?.primary,'color': colorTheme?.bg_secondary}"
            type="button">BROWSE
        </button>
    </section>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            portfolioList: [],
            content: {},
        };
    },
    props: ['shopSetting','colorTheme'],

    mounted() {
        this.getPortfolio();
        this.updateContent();
    },
    methods: {

        getPortfolio() {
            axios.get("/api/v1/special-section", {
                params: {
                    designer: this.shopSetting.slug,
                    type: 1,
                    per_page: 8,
                }
            })
                .then(response => {
                    this.portfolioList = response.data.data.data;
                })
                .catch(error => {
                    toastr.error(
                        error.response?.data?.message || "Something went wrong"
                    );
                });
        },
        updateContent() {
            if (this.shopSetting && this.shopSetting.section_content) {
                try {
                    const parsed = this.shopSetting.section_content;
                    this.content = parsed.portfolio || {};
                } catch (e) {
                    console.error("Invalid section_content format", e);
                    this.content = {};
                }
            }
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
};
</script>
