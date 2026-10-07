<template>
    <footer class="dark-blue-footer pt-5" :style="{'background-color':colorTheme?.bg_primary}">
        <!-- Newsletter Signup -->
        <div class="container py-4 border-bottom border-secondary mb-4"
             :style="{'background-color':colorTheme?.secondary}">
            <div class="row justify-content-center">
                <div class="col-lg-6 text-center">
                    <h5 class="mb-3" :style="{'color': colorTheme?.text_primary }">Subscribe to newsletter</h5>
                    <form class="d-flex justify-content-center">
                        <input type="email" class="form-control me-2 rounded-lg"
                               placeholder="Enter your email" style="max-width: 300px;">
                        <button class="btn rounded-lg fw-bold" type="submit"
                                :style="{'background-color':colorTheme?.primary,'color': colorTheme?.bg_secondary}">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="container pb-4" :style="{'color': colorTheme?.text_primary }">
            <div class="row">
                <!-- Footer Logo/About -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <p class="h5 mb-3 d-flex items-center">
                        <img style="max-width: 120px" :alt="this.shopSetting?.shop_name" :src="this.shopSetting?.logo"/>

                    </p>
                </div>
                <!-- Footer Links Columns -->
                <div class="col-lg-2 col-md-6 mb-4" v-for="widget in widgets">
                    <h6 class="text-uppercase fw-bold mb-3"
                        :style="{'color': colorTheme?.text_primary }">{{ widget.title }}</h6>
                    <ul class="list-unstyled space-y-1">
                        <li v-for="page in widget.pages">
                            <a :href="isSuperAdmin
    ? `${appUrl}/page/${page.slug}`
    : `${appUrl}/designer/${shopSetting.slug}/page/${page.slug}`"
                               :style="{ color: colorTheme?.text_primary }"
                               target="_blank"
                               class="text-decoration-none small ">
                                {{ page.title }}
                            </a>

                        </li>

                    </ul>
                </div>

                <!-- Payment Methods/Social Icons -->
                <div class="col-lg-3 col-md-6 mb-4 text-lg-end text-center text-md-start">
                    <p class="small mb-2">We accept:</p>
                    <!-- Payment Icons Placeholder -->
                    <div
                        class="d-flex justify-content-center justify-content-lg-end mb-3 space-x-3 "
                        :style="{'color': colorTheme?.text_primary }">
                        <a target="_blank" href="https://www.paypal.com/"  :style="{'color': colorTheme?.text_primary }"><i class="ti ti-credit-card"></i></a>
                        <a target="_blank" href="https://www.visa.com/"  :style="{'color': colorTheme?.text_primary }"><i class="ti ti-brand-paypal"></i></a>
                        <a target="_blank" href="https://stripe.com/"  :style="{'color': colorTheme?.text_primary }"><i class="ti ti-brand-stripe"></i></a>

                    </div>
                    <p class="small mb-2">Follow Us:</p>
                    <div v-if="shopSetting" class="d-flex justify-content-center justify-content-lg-end space-x-3 "
                         :style="{'color': colorTheme?.text_primary }">
                        <a target="_blank" v-if="shopSetting?.social_links?.instagram_url"
                           :href="shopSetting?.social_links?.instagram_url"
                           :style="{'color': colorTheme?.text_primary }">
                            <i class="ti ti-brand-instagram"></i>
                        </a>
                        <a target="_blank" v-if="shopSetting?.social_links?.facebook_url"
                           :href="shopSetting?.social_links?.facebook_url"
                           class=" " :style="{'color': colorTheme?.text_primary }"><i
                            class="ti ti-brand-facebook"></i></a>
                        <a target="_blank" v-if="shopSetting?.social_links?.twitter_url"
                           :href="shopSetting?.social_links?.twitter_url"
                           class="" :style="{'color': colorTheme?.text_primary }"><i
                            class="ti ti-brand-twitter"></i></a>
                        <a target="_blank" v-if="shopSetting?.social_links?.tiktok_url"
                           :href="shopSetting?.social_links?.tiktok_url"
                           class="" :style="{'color': colorTheme?.text_primary }"><i
                            class="ti ti-brand-tiktok"></i></a>
                        <a target="_blank" v-if="shopSetting?.social_links?.linkedin"
                           :href="shopSetting?.social_links?.linkedin"
                           class="" :style="{'color': colorTheme?.text_primary }"><i
                            class="ti ti-brand-linkedin"></i></a>
                        <a target="_blank" v-if="shopSetting?.social_links?.youtube_url"
                           :href="shopSetting?.social_links?.youtube_url"
                           class="" :style="{'color': colorTheme?.text_primary }"><i
                            class="ti ti-brand-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>
<script>
import axios from "axios";

export default {
    props: ['colorTheme', 'appUrl', 'isSuperAdmin', 'generalSetting'],
    data() {
        return {
            widgets: [],
            shopSetting: null,
        };
    },
    mounted() {
        this.getFooter();
        this.getShopSetting();
    },
    methods: {

        getFooter() {
            axios.get("/setting/quick-shop-setup/getFooter",)
                .then(response => {
                    this.widgets = response.data.widgets;
                })
                .catch(error => {
                    toastr.error(
                        error.response?.data?.message || "Something went wrong"
                    );
                });
        },
        getShopSetting() {
            axios.get("/setting/quick-shop-setup/getShopSetting")
                .then(response => {
                    this.shopSetting = response.data.shopSetting;
                })
                .catch(error => {
                    toastr.error(
                        error.response?.data?.message || "Something went wrong"
                    );
                });
        },
    },
};
</script>

