<template>
    <div class="slider">
        <video
            v-if="isVideoStyle"
            :src="slides[0].image"
            autoplay
            muted
            loop
            class="w-100 h-100 object-cover position-absolute top-0 start-0"
        ></video>
        <div v-if="!isVideoStyle">
            <div
                v-for="(slide, index) in slides"
                :key="index"
                class="slide"
                :class="{ active: index === currentIndex }"
                :style="{ backgroundImage: `url(${slide.image})` }"
            >
                <div class="overlay">
                    <h1>{{ slide.title }}</h1>
                    <p v-html="slide.description">{{ slide.description }}</p>
                </div>
            </div>

            <div class="controls">
                <button @click="prevSlide">‹</button>
                <button @click="nextSlide">›</button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            currentIndex: 0,
            slides: [],
            isVideoStyle: false,
        };
    },
    props: ['shopSetting'],
    mounted() {
        this.getSliders();
        this.startAutoSlide();
        const data = this.shopSetting;
        this.isVideoStyle = data.home_slider_style === 4;
        // console.log(this.displayControl);
    },
    methods: {
        nextSlide() {
            this.currentIndex = (this.currentIndex + 1) % this.slides.length;
        },
        prevSlide() {
            this.currentIndex =
                (this.currentIndex - 1 + this.slides.length) % this.slides.length;
        },
        startAutoSlide() {
            setInterval(() => {
                this.nextSlide();
            }, 5000);
        },
        getSliders() {
            axios.get("/api/v1/sliders/0", {
                params: {
                    designer: this.shopSetting.slug,
                }
            })
                .then(response => {
                    this.slides = response.data.data.sliders;
                })
                .catch(error => {
                    toastr.error(
                        error.response?.data?.message || "Something went wrong"
                    );
                });
        },
    }
};
</script>

<style scoped>
.slider {
    position: relative;
    width: 100%;
    height: 600px;
    overflow: hidden;
}

.slide {
    position: absolute;
    top: 0;
    left: 100%;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    transition: all 0.8s ease;
    opacity: 0;
}

.slide.active {
    left: 0;
    opacity: 1;
}

.overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #fff;
    text-align: center;
    padding: 20px 40px;
    border-radius: 8px;
}

.overlay h1 {
    margin-bottom: 10px;
    font-size: 40px;
    color: white;
}

.overlay p {
    font-size: 16px;
}

.controls {
    position: absolute;
    bottom: 15px;
}

.controls button {
    background: rgba(0, 0, 0, 0.6);
    border: none;
    color: #fff;
    font-size: 24px;
    margin: 0 5px;
    padding: 5px 12px;
    border-radius: 50%;
    cursor: pointer;
    outline: none;
}

.controls button:hover {
    background: rgba(0, 0, 0, 0.8);
}
</style>
