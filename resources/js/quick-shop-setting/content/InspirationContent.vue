<template>
    <section class="beige-section my-5 shadow-inner" :style="{'background-color':colorTheme?.bg_primary}">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h2 class="fw-bold text-4xl" :style="{'color': colorTheme?.text_primary }">{{ content.label }}</h2>
                    <p class="mt-3 text-gray-700">{{ content.description }}</p>
                    <button
                        class="btn  fw-bold px-4 py-2 mt-3 rounded-xl shadow-md"
                        :style="{'background-color':colorTheme?.primary,'color': colorTheme?.bg_secondary}"
                        type="button">VIEW COLLECTION
                    </button>
                </div>
                <div class="col-md-8">
                    <div
                        class="d-flex p-3 gap-3"
                        style="overflow-x: auto; white-space: nowrap; scrollbar-width: thin;"
                    >
                        <div
                            class="flex-shrink-0 position-relative rounded-xl shadow-lg overflow-hidden"
                            v-for="(inspiration, index) in inspirationList"
                            :key="inspiration.id"
                            style="min-width: 300px; height: 350px;"
                        >
                            <!-- Background Image -->
                            <img
                                :src="inspiration.image"
                                alt="Inspiration Image"
                                class="w-100 h-100 position-absolute top-0 start-0"
                                style="object-fit: cover;"
                            />

                            <!-- Floating Info Box -->
                            <div
                                class="position-absolute bottom-0 start-0 end-0 p-3"
                                style="background: rgba(255, 255, 255, 0.85); border-top-left-radius: 12px; border-top-right-radius: 12px;"
                            >
                                <h5 class="mb-1 text-dark fw-bold">{{ inspiration.title }}</h5>
                                <p class="mb-0 text-secondary small">{{ inspiration.category?.name }}</p>
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
    data() {
        return {
            inspirationList: [],
            content: {},
        };
    },
    props: ['shopSetting','colorTheme'],

    mounted() {
        this.getInspiration();
        this.updateContent();
    },
    methods: {

        getInspiration() {
            axios.get("/api/v1/special-section", {
                params: {
                    designer: this.shopSetting.slug,
                    type: 2,
                }
            })
                .then(response => {
                    this.inspirationList = response.data.data.data;
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
                    this.content = parsed.inspiration || {};
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
