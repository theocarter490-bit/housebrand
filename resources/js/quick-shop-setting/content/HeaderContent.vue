<template>
    <header>
        <!-- Navigation Bar -->
        <nav class="navbar navbar-expand-lg px-3" :style="{'background-color':colorTheme?.secondary}">
            <div class="container-fluid">
                <!-- Logo Placeholder -->
                <a class="navbar-brand d-flex items-center" href="#">
                    <img style="max-width: 120px; max-height: 50px;" :alt="this.shopSetting?.shop_name"
                         :src="this.shopSetting?.logo"/>
                </a>
                <!-- Toggler for mobile navigation -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarNav" aria-controls="navbarNav"
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="#" :style="{'color':colorTheme?.text_secondary}">Products</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="#" :style="{'color':colorTheme?.text_secondary}">About
                            Us</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="#" :style="{'color':colorTheme?.text_secondary}">Inspiration</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="#" :style="{'color':colorTheme?.text_secondary}">Contact</a>
                        </li>
                    </ul>
                    <form class="d-flex align-items-center" role="search">
                        <input class="form-control me-2 rounded-lg" type="search"
                               placeholder="Search" aria-label="Search">
                        <img alt="user_image" :src="this.shopSetting?.logo"
                             class="rounded-circle border" width="30px"
                             height="30px">
                    </form>
                </div>
            </div>
        </nav>
    </header>
</template>
<script>
import axios from "axios";

export default {
    props: ['shopSetting', 'colorTheme'],
};
</script>
