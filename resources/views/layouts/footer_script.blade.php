<script src="{{ asset(mix('assets/vendor/libs/jquery/jquery.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/js/helpers.js')) }}"></script>
<script src="{{ asset(mix('assets/js/config.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/popper/popper.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/js/bootstrap.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/node-waves/node-waves.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/hammer/hammer.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/typeahead-js/typeahead.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/bootstrap-select/bootstrap-select.js')) }}"></script>
<script src="{{ asset(mix('js/bootstrap.js')) }}"></script>

<script src="{{ asset(mix('assets/vendor/js/menu.js')) }}"></script>
<script src="{{ asset(mix('assets/js/main.js')) }}"></script>
<script src="{{ asset(mix('assets/js/pages-auth.js')) }}"></script>


<script src="{{ asset(mix('assets/vendor/libs/apex-charts/apexcharts.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/swiper/swiper.js')) }}"></script>

<script src="{{ asset(mix('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/cleavejs/cleave.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/cleavejs/cleave-phone.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/moment/moment.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/flatpickr/flatpickr.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/select2/select2.js')) }}"></script>
<script src="{{ asset(mix('assets/js/form-layouts.js')) }}"></script>


{{--<script src="{{ asset(mix('assets/js/modal-add-role.js')) }}"></script>--}}
<script src="{{ asset(mix('assets/vendor/libs/sweetalert2/sweetalert2.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/bs-stepper/bs-stepper.js')) }}"></script>
<script src="{{ asset(mix('assets/js/form-wizard-icons.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/quill/katex.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/quill/quill.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/dropzone/dropzone.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/tagify/tagify.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/jquery-repeater/jquery-repeater.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/toastr/toastr.js')) }}"></script>
<script src="{{ asset(mix('assets/js/app-ecommerce-product-add.js')) }}"></script>
<script src="{{ asset(mix('assets/js/ui-popover.js')) }}"></script>
<script src="{{ asset(mix('assets/js/app-chat.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/jkanban/jkanban.js')) }}"></script>
<script src="{{ asset(mix('assets/js/form-wizard-numbered.js')) }}"></script>



<script>
    document.onload = function () {
        window.appTimezone = "{{ config('app.timezone') }}";

        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "4000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

    }
    @if(env('APP_ENV') == 'production')
        $.fn.dataTable.ext.errMode = 'none';
    @endif
    $(document).ajaxError(function (event, jqXHR) {
        if (jqXHR.status === 401) {
            window.location.href = '/login';
        }
    });
    // (Optional) Also catch completed requests just in case
    $(document).ajaxComplete(function (event, xhr) {
        if (xhr.status === 401) {
            window.location.href = '/login';
        }
    });
    $.ajaxSetup({
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        statusCode: {
            401: function () {
                window.location.href = "/login";
            },
        }
    });

    function changeLanguage() {
        var lang = $('#active_lang').val();
        $.ajax({
            url: "{{ route('setting.language.changeLanguage') }}",
            method: 'POST',
            data: {
                lang: lang
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function (response) {
                location.reload();
            },
            error: function (error) {
                toastr.error(error.responseJSON.message);
            }
        });
    }

    var submitButton = $('.loaderBtn');
    var loader = $('.loader');

    $(document).ready(function () {
        $('.loader').hide();
        $('form').on('submit', function () {
            $('.loader').css('display', 'inline-block');
            $('button[type="submit"]').hide();
        });
    });

    $(document).ready(function () {
        function updateDateTime() {
            const now = new Date();
            const date = now.toDateString();
            const time = now.toLocaleTimeString();
            const startOfYear = new Date(now.getFullYear(), 0, 1);
            const elapsedTime = now - startOfYear;
            const elapsedSeconds = Math.floor(elapsedTime / 1000);
            $('#date-time').text(`${date}`);
            $('#date-view').text(`${time}`)
        }

        setInterval(updateDateTime, 1000);
        updateDateTime();
    });
</script>

<script src="{{ asset('/sw.js') }}"></script>
<script>
    if ("serviceWorker" in navigator) {
        // Register a service worker hosted at the root of the
        // site using the default scope.
        navigator.serviceWorker.register("/sw.js").then(
            (registration) => {
                console.log("Service worker registration succeeded:", registration);
            },
            (error) => {
                console.error(`Service worker registration failed: ${error}`);
            },
        );
    } else {
        console.error("Service workers are not supported.");
    }
</script>

<script>
    $(document).on('select2:open', function () {
        $('body').css('overflow-x', 'hidden');
    });

    $(document).on('select2:close', function () {
        $('body').css('overflow-x', '');
    });

    // Global fix for DataTables pagination select positioning in responsive views
    $(document).on('init.dt', function(e, settings) {
        var api = new $.fn.dataTable.Api(settings);
        var $wrapper = $(api.table().container());
        var $lengthSelect = $wrapper.find('.dataTables_length select');
        
        if ($lengthSelect.length) {
            if ($lengthSelect.hasClass('select2-hidden-accessible')) {
                $lengthSelect.select2('destroy');
            }
            if (!$lengthSelect.parent().hasClass('position-relative')) {
                $lengthSelect.wrap('<div class="position-relative"></div>');
            }
            $lengthSelect.select2({
                minimumResultsForSearch: Infinity,
                dropdownParent: $lengthSelect.parent()
            });
        }
    });
</script>


{!! Toastr::message() !!}


@stack('scripts')
</body>

</html>
