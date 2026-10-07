<div id="preloader" class="preloader-wrapper">
    <div class="preloader-content">
        <img src="{{ getFilePath(administratorSetting()->loader) }}" alt="Loading..." class="preloader-gif">
    </div>
</div>

<style>
    .preloader-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.98);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        transition: opacity 0.3s ease-in-out;
    }

    .preloader-wrapper.fade-out {
        opacity: 0;
        pointer-events: none;
    }

    .preloader-content {
        text-align: center;
    }

    .preloader-gif {
        width: 100px;
        height: 100px;
        object-fit: contain;
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .preloader-wrapper {
            background: rgba(255, 255, 255, 0.98);
        }
    }
</style>

<script>
    // Hide preloader when page is fully loaded
    window.addEventListener('load', function() {
        const preloader = document.getElementById('preloader');
        setTimeout(function() {
            preloader.classList.add('fade-out');
            setTimeout(function() {
                preloader.style.display = 'none';
            }, 100);
        }, 200);
    });
</script>
