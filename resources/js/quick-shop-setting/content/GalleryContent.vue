<template>
    <section class="container py-2" :style="{'background-color':colorTheme?.bg_primary}">
        <h2 class="text-center mb-4 text-3xl font-semibold" :style="{'color': colorTheme?.text_primary }">
            {{ content.label }}</h2>

        <!-- Tabs Navigation -->
        <ul class="nav justify-content-center mb-4 border-bottom pb-2">
            <li class="nav-item" v-for="(gallery, index) in galleryList" :key="gallery.id">
                <button
                    class="nav-link"
                    :style="{'color': colorTheme?.text_primary }"
                    :class="{ active: activeTab === index }"
                    @click="activeTab = index"
                    style="font-weight: 600; color: #333;"
                >
                    {{ gallery.title }}
                </button>
            </li>
        </ul>

        <!-- Gallery Content -->
        <div v-for="(gallery, index) in galleryList" :key="'gallery-' + index" v-show="activeTab === index">
            <div class="row align-items-center">
                <!-- Left Large Image -->
                <div class="col-md-6 mb-4">
                    <div
                        class="position-relative rounded-xl shadow-lg overflow-hidden"
                        style="min-height: 400px;"
                    >
                        <img
                            :src="gallery.image"
                            alt="Gallery Image"
                            class="w-100 h-100"
                            style="object-fit: cover;"
                        />
                    </div>
                </div>

                <!-- Right Side: Designer Cards -->
                <div class="col-md-6">
                    <div class="row row-cols-2 g-4 h-100">
                        <div
                            class="col d-flex"
                            v-for="details in gallery.details"
                            :key="details.id"
                        >
                            <div
                                class="card border-0 text-center rounded-xl shadow-sm overflow-hidden w-100"
                                style="flex: 1; height: 190px; position: relative;"
                            >
                                <!-- Designer Image -->
                                <img
                                    :src="details.image"
                                    alt="Designer"
                                    class="w-100 h-100 position-absolute top-0 start-0"
                                    style="object-fit: cover;"
                                />

                                <!-- Overlay with Name and Category -->
                                <div
                                    class="position-absolute bottom-0 start-0 w-100 text-start px-3 py-2"
                                    style="background: rgba(255, 255, 255, 0.8);"
                                >
                                    <p class="mb-0 small text-muted">{{ details.category }}</p>
                                    <h6 class="mb-0 fw-semibold text-dark">{{ details.name }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</template>

<script>
import axios from "axios";

export default {
    props: ["shopSetting", 'colorTheme'],
    data() {
        return {
            galleryList: [],
            activeTab: 0,
            content: {},
        };
    },
    mounted() {
        this.getGalleryData();
        this.updateContent();
    },
    methods: {
        getGalleryData() {
            axios
                .get("/api/v1/gallery", {
                    params: {
                        designer: this.shopSetting.slug,
                        type: 1,
                        per_page: 6,
                        item_per_data: 4,
                    },
                })
                .then((response) => {
                    this.galleryList = response.data.data;
                    if (this.galleryList.length > 0) this.activeTab = 0;
                })
                .catch((error) => {
                    toastr.error(
                        error.response?.data?.message || "Something went wrong"
                    );
                });
        },
        updateContent() {
            if (this.shopSetting && this.shopSetting.section_content) {
                try {
                    const parsed = this.shopSetting.section_content;
                    this.content = parsed.gallery || {};
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

<style scoped>
.nav-link.active {
    color: #000 !important;
    border-bottom: 2px solid #000;
}
</style>
