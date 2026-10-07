<template xmlns="http://www.w3.org/1999/html">
    <div class="p-2 bg-body rounded">
        <!-- Slider Form -->


        <!-- Slider Customization -->
        <div class="border rounded mb-3 p-2">
            <p>
                Customize your shop’s footer section and manage your pages effortlessly.
                Click on the links below to personalize the content of each area.
            </p>
            <p href="/cms/footer-widget" class="text-decoration-underline fw-bold">Footer Widgets</p>
            <ul>

                <li><a href="/cms/footer-widget" class="text-decoration-underline fw-bold">Company</a> – Add or edit
                    information about your company, including your mission, vision, and contact details.
                </li>
                <li><a href="/cms/footer-widget" class="text-decoration-underline fw-bold">Useful Links</a> –
                    Customize helpful navigation links for your visitors, like FAQs, Shipping Policy, or Terms &
                    Conditions.
                </li>
                <li><a href="/cms/footer-widget" class="text-decoration-underline fw-bold">Client Support</a> –
                    Update support-related links such as Help Center, Returns, or Customer Service pages.
                </li>

            </ul>
            <a href="/cms/pages" class="text-decoration-underline fw-bold">Pages</a> – Create and manage key
            website pages such as About Us, Contact Us, Our Story, Team, and other essential content.

        </div>

        <!-- Submit -->
        <div class="row">
            <ProceedButton @proceed-to-next="$emit('proceed-to-next')"/>
        </div>

    </div>
</template>

<script>
import axios from "axios";
import ProceedButton from "./components/ProceedButton.vue";

export default {
    components: {
        ProceedButton,
    }
};
</script>
