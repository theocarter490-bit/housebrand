<div class="col-xl-4 col-md-6 mb-4">
    <div class="card h-100">
        <div class="card-header d-flex justify-content-between">
            <div class="card-title mb-0">
                <h5 class="mb-0">Google Analytics</h5>
                <small class="text-muted">This data shows based on house brands user activity by google</small>
            </div>
            <div class="dropdown">
                <button
                    class="btn p-0"
                    type="button"
                    id="MonthlyCampaign"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                    <i class="ti ti-dots-vertical ti-sm text-muted"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="MonthlyCampaign">
                    <a class="dropdown-item filterAnalysis" href="javascript:void(0);" data-value="today">Today</a>
                    <a class="dropdown-item filterAnalysis" href="javascript:void(0);" data-value="7daysAgo">7 Days</a>
                    <a class="dropdown-item filterAnalysis" href="javascript:void(0);" data-value="15daysAgo">15
                        Days</a>
                    <a class="dropdown-item filterAnalysis" href="javascript:void(0);" data-value="30daysAgo">30
                        Days</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <ul class="p-0 m-0">
                <li class="mb-4 pb-1 d-flex justify-content-between align-items-center">
                    <div class="badge bg-label-success rounded p-2"><i class="ti ti-users-group ti-sm"></i></div>
                    <div class="d-flex justify-content-between w-100 flex-wrap">
                        <h6 class="mb-0 ms-3">Total User</h6>
                        <div class="d-flex">
                            <p class="mb-0 fw-medium badge bg-label-success" id="totalUser">0</p>
                        </div>
                    </div>
                </li>
                <li class="mb-4 pb-1 d-flex justify-content-between align-items-center">
                    <div class="badge bg-label-info rounded p-2"><i class="ti ti-user-star ti-sm"></i></div>
                    <div class="d-flex justify-content-between w-100 flex-wrap">
                        <h6 class="mb-0 ms-3">New User</h6>
                        <div class="d-flex">
                            <p class="mb-0 fw-medium badge bg-label-info rounded" id="newUser">0</p>
                        </div>
                    </div>
                </li>
                <li class="mb-4 pb-1 d-flex justify-content-between align-items-center">
                    <div class="badge bg-label-primary rounded p-2"><i class="ti ti-user-bolt ti-sm"></i></div>
                    <div class="d-flex justify-content-between w-100 flex-wrap">
                        <h6 class="mb-0 ms-3">Active User</h6>
                        <div class="d-flex">
                            <p class="mb-0 fw-medium badge bg-label-primary rounded" id="activeUser">0</p>
                        </div>
                    </div>
                </li>
                <li class="mb-4 pb-1 d-flex justify-content-between align-items-center">
                    <div class="badge bg-label-warning rounded p-2"><i class="ti ti-hourglass-high ti-sm"></i></div>
                    <div class="d-flex justify-content-between w-100 flex-wrap">
                        <h6 class="mb-0 ms-3">Average Session Duration</h6>
                        <div class="d-flex badge bg-label-warning">
                            <p class="mb-0 fw-medium " id="averageSessionDuration">0</p>s
                        </div>
                    </div>
                </li>

                <li class="mb-4 pb-1 d-flex justify-content-between align-items-center">
                    <div class="badge bg-label-danger rounded p-2">
                        <i class="ti ti-wave-saw-tool ti-sm text-body"></i>
                    </div>
                    <div class="d-flex justify-content-between w-100 flex-wrap">
                        <h6 class="mb-0 ms-3">Bounce Rate</h6>
                        <div class="d-flex badge bg-label-danger">
                            <p class="mb-0 fw-medium " id="bounceRate">0</p>%
                        </div>
                    </div>
                </li>
                <li class="d-flex justify-content-between align-items-center">
                    <div class="badge bg-label-success rounded p-2"><i class="ti ti-circle-check ti-sm"></i></div>
                    <div class="d-flex justify-content-between w-100 flex-wrap">
                        <h6 class="mb-0 ms-3">Engaged Sessions</h6>
                        <div class="d-flex">
                            <p class="mb-0 fw-medium badge bg-label-success" id="engagedSession">0</p>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Source Visit -->
<div class="col-xl-4 col-md-6 order-2 order-lg-1 mb-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="card-title mb-0">
                <h5 class="mb-0">Google Analytics Page View</h5>

            </div>
            <div class="dropdown">
                <button
                    class="btn p-0"
                    type="button"
                    id="sourceVisits"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                    <i class="ti ti-dots-vertical ti-sm text-muted"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="sourceVisits">
                    <a class="dropdown-item filterPageView" href="javascript:void(0);" data-value="today">Today</a>
                    <a class="dropdown-item filterPageView" href="javascript:void(0);" data-value="7daysAgo">7 Days</a>
                    <a class="dropdown-item filterPageView" href="javascript:void(0);" data-value="15daysAgo">15
                        Days</a>
                    <a class="dropdown-item filterPageView" href="javascript:void(0);" data-value="30daysAgo">30
                        Days</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <ul class="list-unstyled mb-0" id="pageViewDataWrapper">

            </ul>
        </div>
    </div>
</div>

<!--/ Source Visit -->

<div class="col-xl-4 col-md-6 order-2 order-lg-1 mb-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="card-title mb-0">
                <h5 class="mb-0">Google Analytics – Country View</h5>

            </div>
            <div class="dropdown">
                <button
                    class="btn p-0"
                    type="button"
                    id="sourceVisits"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                    <i class="ti ti-dots-vertical ti-sm text-muted"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="sourceVisits">
                    <a class="dropdown-item filterCountryWise" href="javascript:void(0);" data-value="today">Today</a>
                    <a class="dropdown-item filterCountryWise" href="javascript:void(0);" data-value="7daysAgo">7
                        Days</a>
                    <a class="dropdown-item filterCountryWise" href="javascript:void(0);" data-value="15daysAgo">15
                        Days</a>
                    <a class="dropdown-item filterCountryWise" href="javascript:void(0);" data-value="30daysAgo">30
                        Days</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <ul class="list-unstyled mb-0" id="countryWiseDataWrapper">

            </ul>
        </div>
    </div>
</div>


<div class="col-xl-6 col-md-6 order-2 order-lg-1 mb-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="card-title mb-0">
                <h5 class="mb-0">Google Analytics – Map View</h5>

            </div>
            <div class="dropdown">
                <button
                    class="btn p-0"
                    type="button"
                    id="sourceVisits"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                    <i class="ti ti-dots-vertical ti-sm text-muted"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="sourceVisits">
                    <a class="dropdown-item filterCountryWise" href="javascript:void(0);" data-value="today">Today</a>
                    <a class="dropdown-item filterCountryWise" href="javascript:void(0);" data-value="7daysAgo">7
                        Days</a>
                    <a class="dropdown-item filterCountryWise" href="javascript:void(0);" data-value="15daysAgo">15
                        Days</a>
                    <a class="dropdown-item filterCountryWise" href="javascript:void(0);" data-value="30daysAgo">30
                        Days</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- HTML -->
            <div id="chartdiv"></div>
        </div>
    </div>
</div>


@push('styles')

    <!-- Styles -->
    <style>
        #chartdiv {
            width: 100%;
            height: 500px;
        }
    </style>

@endpush


@push('scripts')
    <script>
        (function () {

                $('.filterAnalysis').on('click', function () {
                    let data = $(this).data('value');

                    getAnalysisData(data);
                });
                getAnalysisData('7daysAgo');

                function getAnalysisData(data) {
                    $.ajax({
                        url: "/dashboard/google-analytics",
                        method: "get",
                        data: {
                            _token: "{{ csrf_token() }}",
                            data: data,
                        },
                        success: function (response) {

                            $('#totalUser').text(response.analysisData.total_users);
                            $('#newUser').text(response.analysisData.new_user);
                            $('#activeUser').text(response.analysisData.active_user);

                            const mins = Math.floor(response.analysisData.average_session_duration / 60);
                            const secs = Math.floor(response.analysisData.average_session_duration % 60);

                            // Optional: pad with 0 if under 10
                            const paddedMins = mins.toString().padStart(2, '0');
                            const paddedSecs = secs.toString().padStart(2, '0');

                            let s = `${paddedMins}:${paddedSecs}`;

                            $('#averageSessionDuration').text(s);
                            $('#bounceRate').text(Number(response.analysisData.bounce_rate).toPrecision(2));
                            $('#engagedSession').text(response.analysisData.engaged_sessions);
                        },
                        error: function (error) {
                            console.log(error.responseJSON.message);
                        },
                    });
                }


            }
        )();
    </script>

    <script>
        (function () {


                $('.filterPageView').on('click', function () {
                    let data = $(this).data('value');

                    getPageViewData(data);
                });
                getPageViewData('7daysAgo');

                function getPageViewData(data) {
                    $.ajax({
                        url: "/dashboard/google-analytics/pageView",
                        method: "get",
                        data: {
                            _token: "{{ csrf_token() }}",
                            data: data,
                        },
                        success: function (response) {
                            $('#pageViewDataWrapper').empty();

                            let s = '';
                            $(response.pageView).each(function (index, value) {

                                s +=
                                    ` <li class="mb-3 pb-1">
                    <div class="d-flex align-items-start">
                        <div class="badge bg-label-secondary p-2 me-3 rounded">
                            <i class="ti ti-shadow ti-sm"></i>
                        </div>
                        <div class="d-flex justify-content-between w-100 flex-wrap gap-2">
                            <div class="me-2">
                                <h6 class="mb-0 text-capitalize">${value.path.split('/').filter(Boolean)[0] ?? "Home"} Page</h6>
                                <small class="text-muted">${value.path}</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <p class="mb-0 text-primary">${value.views} Times</p>
                            </div>
                        </div>
                    </div>
                </li>`
                            });
                            $('#pageViewDataWrapper').append(s);
                        },
                        error: function (error) {
                            console.log(error.responseJSON.message);
                        },
                    });
                }


            }
        )();
    </script>

    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/map.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/worldLow.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    <script>
        (function () {

                const countryCodes = {
                    "afghanistan": "af",
                    "albania": "al",
                    "algeria": "dz",
                    "american samoa": "as",
                    "andorra": "ad",
                    "angola": "ao",
                    "anguilla": "ai",
                    "antarctica": "aq",
                    "antigua and barbuda": "ag",
                    "argentina": "ar",
                    "armenia": "am",
                    "aruba": "aw",
                    "australia": "au",
                    "austria": "at",
                    "azerbaijan": "az",
                    "bahamas": "bs",
                    "bahrain": "bh",
                    "bangladesh": "bd",
                    "barbados": "bb",
                    "belarus": "by",
                    "belgium": "be",
                    "belize": "bz",
                    "benin": "bj",
                    "bermuda": "bm",
                    "bhutan": "bt",
                    "bolivia": "bo",
                    "bosnia and herzegovina": "ba",
                    "botswana": "bw",
                    "bouvet island": "bv",
                    "brazil": "br",
                    "british indian ocean territory": "io",
                    "brunei darussalam": "bn",
                    "bulgaria": "bg",
                    "burkina faso": "bf",
                    "burundi": "bi",
                    "cambodia": "kh",
                    "cameroon": "cm",
                    "canada": "ca",
                    "cape verde": "cv",
                    "cayman islands": "ky",
                    "central african republic": "cf",
                    "chad": "td",
                    "chile": "cl",
                    "china": "cn",
                    "christmas island": "cx",
                    "cocos (keeling) islands": "cc",
                    "colombia": "co",
                    "comoros": "km",
                    "congo": "cg",
                    "democratic republic of the congo": "cd",
                    "cook islands": "ck",
                    "costa rica": "cr",
                    "cote d'ivoire": "ci",
                    "croatia": "hr",
                    "cuba": "cu",
                    "cyprus": "cy",
                    "czech republic": "cz",
                    "denmark": "dk",
                    "djibouti": "dj",
                    "dominica": "dm",
                    "dominican republic": "do",
                    "ecuador": "ec",
                    "egypt": "eg",
                    "el salvador": "sv",
                    "equatorial guinea": "gq",
                    "eritrea": "er",
                    "estonia": "ee",
                    "eswatini": "sz",
                    "ethiopia": "et",
                    "falkland islands (malvinas)": "fk",
                    "faroe islands": "fo",
                    "fiji": "fj",
                    "finland": "fi",
                    "france": "fr",
                    "french guiana": "gf",
                    "french polynesia": "pf",
                    "french southern territories": "tf",
                    "gabon": "ga",
                    "gambia": "gm",
                    "georgia": "ge",
                    "germany": "de",
                    "ghana": "gh",
                    "gibraltar": "gi",
                    "greece": "gr",
                    "greenland": "gl",
                    "grenada": "gd",
                    "guadeloupe": "gp",
                    "guam": "gu",
                    "guatemala": "gt",
                    "guernsey": "gg",
                    "guinea": "gn",
                    "guinea-bissau": "gw",
                    "guyana": "gy",
                    "haiti": "ht",
                    "heard island and mcdonald islands": "hm",
                    "holy see (vatican city state)": "va",
                    "honduras": "hn",
                    "hong kong": "hk",
                    "hungary": "hu",
                    "iceland": "is",
                    "india": "in",
                    "indonesia": "id",
                    "iran": "ir",
                    "iraq": "iq",
                    "ireland": "ie",
                    "isle of man": "im",
                    "israel": "il",
                    "italy": "it",
                    "jamaica": "jm",
                    "japan": "jp",
                    "jersey": "je",
                    "jordan": "jo",
                    "kazakhstan": "kz",
                    "kenya": "ke",
                    "kiribati": "ki",
                    "north korea": "kp",
                    "south korea": "kr",
                    "kuwait": "kw",
                    "kyrgyzstan": "kg",
                    "laos": "la",
                    "latvia": "lv",
                    "lebanon": "lb",
                    "lesotho": "ls",
                    "liberia": "lr",
                    "libya": "ly",
                    "liechtenstein": "li",
                    "lithuania": "lt",
                    "luxembourg": "lu",
                    "macao": "mo",
                    "madagascar": "mg",
                    "malawi": "mw",
                    "malaysia": "my",
                    "maldives": "mv",
                    "mali": "ml",
                    "malta": "mt",
                    "marshall islands": "mh",
                    "martinique": "mq",
                    "mauritania": "mr",
                    "mauritius": "mu",
                    "mayotte": "yt",
                    "mexico": "mx",
                    "micronesia": "fm",
                    "moldova": "md",
                    "monaco": "mc",
                    "mongolia": "mn",
                    "montenegro": "me",
                    "montserrat": "ms",
                    "morocco": "ma",
                    "mozambique": "mz",
                    "myanmar": "mm",
                    "namibia": "na",
                    "nauru": "nr",
                    "nepal": "np",
                    "netherlands": "nl",
                    "new caledonia": "nc",
                    "new zealand": "nz",
                    "nicaragua": "ni",
                    "niger": "ne",
                    "nigeria": "ng",
                    "niue": "nu",
                    "norfolk island": "nf",
                    "northern mariana islands": "mp",
                    "norway": "no",
                    "oman": "om",
                    "pakistan": "pk",
                    "palau": "pw",
                    "palestine": "ps",
                    "panama": "pa",
                    "papua new guinea": "pg",
                    "paraguay": "py",
                    "peru": "pe",
                    "philippines": "ph",
                    "pitcairn": "pn",
                    "poland": "pl",
                    "portugal": "pt",
                    "puerto rico": "pr",
                    "qatar": "qa",
                    "reunion": "re",
                    "romania": "ro",
                    "russia": "ru",
                    "rwanda": "rw",
                    "saint barthelemy": "bl",
                    "saint helena": "sh",
                    "saint kitts and nevis": "kn",
                    "saint lucia": "lc",
                    "saint martin": "mf",
                    "saint pierre and miquelon": "pm",
                    "saint vincent and the grenadines": "vc",
                    "samoa": "ws",
                    "san marino": "sm",
                    "sao tome and principe": "st",
                    "saudi arabia": "sa",
                    "senegal": "sn",
                    "serbia": "rs",
                    "seychelles": "sc",
                    "sierra leone": "sl",
                    "singapore": "sg",
                    "sint maarten": "sx",
                    "slovakia": "sk",
                    "slovenia": "si",
                    "solomon islands": "sb",
                    "somalia": "so",
                    "south africa": "za",
                    "south georgia and the south sandwich islands": "gs",
                    "south sudan": "ss",
                    "spain": "es",
                    "sri lanka": "lk",
                    "sudan": "sd",
                    "suriname": "sr",
                    "svalbard and jan mayen": "sj",
                    "sweden": "se",
                    "switzerland": "ch",
                    "syria": "sy",
                    "taiwan": "tw",
                    "tajikistan": "tj",
                    "tanzania": "tz",
                    "thailand": "th",
                    "timor-leste": "tl",
                    "togo": "tg",
                    "tokelau": "tk",
                    "tonga": "to",
                    "trinidad and tobago": "tt",
                    "tunisia": "tn",
                    "turkey": "tr",
                    "turkmenistan": "tm",
                    "turks and caicos islands": "tc",
                    "tuvalu": "tv",
                    "uganda": "ug",
                    "ukraine": "ua",
                    "united arab emirates": "ae",
                    "united kingdom": "gb",
                    "united states": "us",
                    "united states minor outlying islands": "um",
                    "uruguay": "uy",
                    "uzbekistan": "uz",
                    "vanuatu": "vu",
                    "venezuela": "ve",
                    "vietnam": "vn",
                    "virgin islands (british)": "vg",
                    "virgin islands (u.s.)": "vi",
                    "wallis and futuna": "wf",
                    "western sahara": "eh",
                    "yemen": "ye",
                    "zambia": "zm",
                    "zimbabwe": "zw"
                };


                $('.filterCountryWise').on('click', function () {
                    let data = $(this).data('value');
                    getCountryWiseData(data);
                });
                getCountryWiseData('7daysAgo');

                function getCountryWiseData(data) {
                    $.ajax({
                        url: "/dashboard/google-analytics/topCountries",
                        method: "get",
                        data: {
                            _token: "{{ csrf_token() }}",
                            data: data,
                        },
                        success: function (response) {
                            $('#countryWiseDataWrapper').empty();
                            let s = '';

                            let mapData = [];
                            $(response.pageView).each(function (index, value) {

                                s +=
                                    ` <li class="mb-3 pb-1">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="badge me-3 rounded">
                            <img class="rounded w-75 h-75" src="https://flagcdn.com/48x36/${countryCodes[value.country.toLowerCase()]}.png" alt="Country Flag">
                        </div>
                        <div class="d-flex justify-content-between w-100 flex-wrap gap-2">
                            <div class="me-2">
                                <h6 class="mb-0 text-capitalize">${value.country} </h6>
                            </div>
                            <div class="d-flex align-items-center">
                                <p class="mb-0 text-primary">${value.views} Times</p>
                            </div>
                        </div>
                    </div>
                </li>`

                                mapData.push({
                                    id: countryCodes[value.country.toLowerCase()].toUpperCase(),
                                    name: value.country,
                                    value: value.views
                                });

                            });
                            $('#countryWiseDataWrapper').append(s);
                            mapFunction(mapData);
                        },
                        error: function (error) {
                            console.log(error.responseJSON.message);
                        },
                    });
                }


                let root;

                function mapFunction(mapData) {
                    am5.ready(function () {
                        // Dispose of previous chart if exists
                        if (root) {
                            root.dispose();
                        }

                        // Create new chart root
                        root = am5.Root.new("chartdiv");

                        root.setThemes([am5themes_Animated.new(root)]);

                        var chart = root.container.children.push(am5map.MapChart.new(root, {}));

                        var polygonSeries = chart.series.push(
                            am5map.MapPolygonSeries.new(root, {
                                geoJSON: am5geodata_worldLow,
                                exclude: ["AQ"]
                            })
                        );

                        var bubbleSeries = chart.series.push(
                            am5map.MapPointSeries.new(root, {
                                valueField: "value",
                                calculateAggregates: true,
                                polygonIdField: "id"
                            })
                        );

                        var circleTemplate = am5.Template.new({});

                        bubbleSeries.bullets.push(function (root, series, dataItem) {
                            var container = am5.Container.new(root, {});

                            var circle = container.children.push(
                                am5.Circle.new(root, {
                                    radius: 20,
                                    fillOpacity: 0.7,
                                    fill: am5.color(0x800080),
                                    cursorOverStyle: "pointer",
                                    tooltipText: `{name}: [bold]{value}[/]`
                                }, circleTemplate)
                            );

                            var countryLabel = container.children.push(
                                am5.Label.new(root, {
                                    text: "{name}",
                                    paddingLeft: 5,
                                    populateText: true,
                                    fontWeight: "bold",
                                    fontSize: 13,
                                    centerY: am5.p50
                                })
                            );

                            circle.on("radius", function (radius) {
                                countryLabel.set("x", radius);
                            });

                            return am5.Bullet.new(root, {
                                sprite: container,
                                dynamic: true
                            });
                        });

                        bubbleSeries.bullets.push(function (root, series, dataItem) {
                            return am5.Bullet.new(root, {
                                sprite: am5.Label.new(root, {
                                    text: "{value.formatNumber('#.')}",
                                    fill: am5.color(0xffffff),
                                    populateText: true,
                                    centerX: am5.p50,
                                    centerY: am5.p50,
                                    textAlign: "center"
                                }),
                                dynamic: true
                            });
                        });

                        bubbleSeries.set("heatRules", [
                            {
                                target: circleTemplate,
                                dataField: "value",
                                min: 10,
                                max: 50,
                                minValue: 0,
                                maxValue: 100,
                                key: "radius"
                            }
                        ]);

                        bubbleSeries.data.setAll(mapData);


                    }); // end am5.ready()
                }


            }
        )();
    </script>
@endpush





