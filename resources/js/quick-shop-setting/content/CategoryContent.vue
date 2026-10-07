<template>
    <section class="container py-5 text-center" :style="{'background-color':colorTheme?.bg_primary}">
        <h2 class="mb-5 text-3xl font-semibold"
            :style="{'color': colorTheme?.text_primary }"
        >{{ content.label }}</h2>
        <div class="row row-cols-2 row-cols-md-5 g-4 justify-content-center">
            <!-- Category Item Loop -->
            <div class="col" v-for="(category, index) in categoryList" v-if="categoryList.length>0" :key="category.id">
                <div class="d-flex flex-column align-items-center">
                    <!-- Rounded Image Card -->
                    <a href="#"
                       class="d-block rounded-circle overflow-hidden shadow-md hover:shadow-lg position-relative"
                       style="width: 120px; height: 120px;">
                        <img :src="category.image" alt="category.name"
                             class="w-100 h-100 object-cover"/>
                    </a>
                    <!-- Category Name -->
                    <p class="mt-2 small fw-bold text-center w-100 text-truncate">{{ category.name }}</p>
                </div>
            </div>
            <div
                v-if="categoryList.length===0"
                class="col"
                v-for="n in 4"
                :key="'skeleton-' + n"
            >
                <div class="d-flex flex-column align-items-center">
                    <!-- Skeleton Circle -->
                    <div
                        class="rounded-circle bg-light position-relative overflow-hidden"
                        style="width: 120px; height: 120px;"
                    >
                        <div class="skeleton-shimmer"></div>
                    </div>
                    <!-- Skeleton Text -->
                    <div
                        class="mt-2 bg-light rounded w-75"
                        style="height: 12px; position: relative; overflow: hidden;"
                    >
                        <div class="skeleton-shimmer"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            categoryList: [],
            content: {},
        };
    },
    props: ['shopSetting', 'colorTheme'],

    mounted() {
        this.getCategory();
        this.updateContent();
    },
    methods: {

        getCategory() {
            axios.get("/api/v1/categories", {
                params: {
                    designer: this.shopSetting.slug,
                    per_page: 5
                }
            })
                .then(response => {
                    this.categoryList = response.data.data.categories.data;
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
                    this.content = parsed.category || {};
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

<style>
.skeleton-shimmer {
    position: absolute;
    top: 0;
    left: -150px;
    width: 150px;
    height: 100%;
    background: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.4) 50%, rgba(255, 255, 255, 0) 100%);
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    100% {
        left: 100%;
    }
}

</style>
