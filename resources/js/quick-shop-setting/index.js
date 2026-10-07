const Vue = require('vue/dist/vue.js');
// Disable the Vue DevTools extension
Vue.config.devtools = false;

// Disable the "You are running Vue in development mode" console log
Vue.config.productionTip = false;
import categoryContent from "./content/CategoryContent.vue";
import heroSection from './HeroSection.vue';
import categorySection from './CategorySection.vue';
import productSection from "./ProductSection.vue";
import inspirationSection from "./InspirationSection.vue";
import portfolioSection from "./PortfolioSection.vue";
import gallerySection from './GallerySection.vue';
import footerSection from "./FooterSection.vue";
import heroContent from './content/HeroContent.vue';
import headerContent from "./content/HeaderContent.vue";
import productContent from "./content/ProductContent.vue";
import inspirationContent from "./content/InspirationContent.vue";
import portfolioContent from "./content/PortfolioContent.vue";
import galleryContent from "./content/GalleryContent.vue";
import footerContent from "./content/FooterContent.vue";
import axios from "axios";

new Vue({
    el: '#quick-shop-setup-app',
    components: {
        heroSection,
        categorySection,
        productSection,
        inspirationSection,
        portfolioSection,
        gallerySection,
        footerSection,
        heroContent,
        headerContent,
        categoryContent,
        productContent,
        inspirationContent,
        portfolioContent,
        galleryContent,
        footerContent,
    },
    data: {
        section: 'heroSection',
        heroContentKey: 0,
        categoryContentKey: 0,
        inspirationContentKey: 0,
        portfolioContentKey: 0,
        galleryContentKey: 0,
        shopSetting: null,
        colorTheme: null,
        sections: [
            {value: 'heroSection', label: 'Hero Slider'},
            {value: 'categorySection', label: 'Category'},
            {value: 'productSection', label: 'Products'},
            {value: 'inspirationSection', label: 'Inspiration'},
            {value: 'portfolioSection', label: 'Portfolio'},
            {value: 'gallerySection', label: 'Gallery'},
            {value: 'footerSection', label: 'Footer'},
        ],
    },
    methods: {
        setSection(section) {
            this.section = section;
            const sectionRefs = {
                heroSection: 'heroSectionRef',
                categorySection: 'categorySectionRef',
                productSection: 'productSectionRef',
                inspirationSection: 'inspirationSectionRef',
                portfolioSection: 'portfolioSectionRef',
                gallerySection: 'gallerySectionRef',
            };

            this.$nextTick(() => {
                const container = this.$refs.stepperContent;
                const el = this.$refs[sectionRefs[section]];

                if (!container || !el) return;

                // Get element's offset relative to container
                const targetOffsetTop = el.offsetTop - container.offsetTop;

                // Scroll the container
                container.scrollTo({
                    top: targetOffsetTop,
                    behavior: 'smooth'
                });
            });
        },
        nextStep(data) {
            const i = this.sections.findIndex(s => s.value === this.section);

            if (i !== -1 && i < this.sections.length - 1) {
                axios.post("/setting/quick-shop-setup/section-content", {
                    section: data.section,
                    display_control: data.display_control ? 1 : 0,
                    label: data.label,
                    description: data.description,
                })
                    .then(() => {
                        this.$emit('proceed-to-next');
                        this.setSection(this.sections[i + 1].value);
                        this.getShopSetting();
                    })
                    .catch(err => {
                        const errors = err.response?.data?.data || {};
                        this.validationErrorMessage = Object.fromEntries(
                            Object.entries(errors).map(([k, v]) => [k, v[0]])
                        );
                        toastr.error(err.response?.data?.message || "Something went wrong");
                    });
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Shop Setup Successful',
                    text: '🎉 Your shop is ready to start selling!',
                    customClass: {confirmButton: 'btn btn-success waves-effect waves-light'}
                });
            }
        },
        updateContent(data) {

            if (data.content === 'heroContent') {
                this.heroContentKey += 1;
            }
            if (data.content === 'categoryContent') {
                this.categoryContentKey += 1;
            }
            if (data.content === 'inspirationContent') {
                this.inspirationContentKey += 1;
            }
            if (data.content === 'portfolioContent') {
                this.portfolioContentKey += 1;
            }
            if (data.content === 'galleryContent') {
                this.galleryContentKey += 1;
            }
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
        getColorTheme() {
            axios.get("/setting/quick-shop-setup/getColorTheme")
                .then(response => {
                    this.colorTheme = response.data.colorTheme;
                })
                .catch(error => {
                    toastr.error(
                        error.response?.data?.message || "Something went wrong"
                    );
                });
        }

    },
    mounted() {
        document.getElementById('chat-loading').style.display = 'none';
        this.getShopSetting();
        this.getColorTheme();
    }

});
