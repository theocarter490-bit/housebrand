const Vue = require('vue/dist/vue.js');
// Disable the Vue DevTools extension
Vue.config.devtools = false;

// Disable the "You are running Vue in development mode" console log
Vue.config.productionTip = false;
import axios from 'axios';

// Set axios defaults
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
if (csrfToken) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

new Vue({
    el: '#timer-app',

    data() {
        return {
            // Timer
            timer: null,
            isRunning: false,
            isPaused: false,
            currentSeconds: 0,
            totalSecond: 0,
            timeTrackingId: '',

            // UI State
            timeType: 'billable',
            timeValue: 1,
            showPreview: false,
            isHolding: false,
            progressOffset: 251.2,

            // Form Data
            form: {
                client: '',
                project: '',
                teamMember: '',
                service: '',
                hourlyRate: 0.00,
                description: ''
            },

            // Display Names (for preview)
            displayNames: {
                client: '',
                project: '',
                teamMember: '',
                service: ''
            },

            // Validation
            errors: {},

            // Dropdowns
            clients: [],
            projects: [],
            teamMembers: [],
            services: []
        };
    },

    computed: {
        formattedTime() {
            return this.formatTime(this.currentSeconds);
        },

        formattedTotalTime() {
            return this.formatTime(this.totalSecond);
        },

        hasErrors() {
            return Object.keys(this.errors).length > 0;
        }
    },

    mounted() {
        this.init();
        this.addEventListeners();
    },

    beforeDestroy() {
        this.cleanup();
    },

    methods: {
        // ==================== INITIALIZATION ====================
        async init() {
            try {
                await this.loadClients();
                await this.loadTeamMembers();
                await this.loadRunningTime();
            } catch (error) {
                console.error('Init error:', error);
                toastr.error('Failed to initialize');
            }
        },

        // ==================== TIMER FUNCTIONS ====================
        formatTime(totalSeconds) {
            const h = Math.floor(totalSeconds / 3600);
            const m = Math.floor((totalSeconds % 3600) / 60);
            const s = totalSeconds % 60;
            return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        },

        startTimer() {
            if (!this.validate()) {
                this.progressOffset = 251.2;
                return false;
            }

            this.isRunning = true;
            this.isPaused = false;
            this.showPreview = true;
            this.timer = setInterval(() => {
                this.currentSeconds++;
                this.totalSecond++;
            }, 1000);

            return true;
        },

        stopTimer() {
            this.isRunning = false;
            this.isPaused = true;
            clearInterval(this.timer);
        },

        // ==================== PROGRESS ANIMATION ====================
        startProgress(e) {
            if (this.isRunning || this.isPaused) return;
            e.preventDefault();
            if (this.isHolding) return;

            this.isHolding = true;
            this.animateProgress();

            setTimeout(() => {
                if (this.isHolding) {
                    this.isHolding = false;
                    if (this.startTimer()) {
                        this.apiStartTracking();
                    }
                }
            }, 500);
        },

        stopProgress(e) {
            if (!this.isHolding) return;
            this.isHolding = false;
            this.progressOffset = 251.2;
        },

        animateProgress() {
            const startTime = Date.now();
            const animate = () => {
                if (!this.isHolding) return;
                const elapsed = Date.now() - startTime;
                const progress = Math.min(elapsed / 500, 1);
                this.progressOffset = 251.2 * (1 - progress);
                if (progress < 1) requestAnimationFrame(animate);
            };
            requestAnimationFrame(animate);
        },

        // ==================== BUTTON ACTIONS ====================
        setTimeType(type, value) {
            this.timeType = type;
            this.timeValue = value;
        },

        togglePause() {
            if (this.isRunning) {
                // Pause
                this.stopTimer();
                this.apiPauseTracking();
            } else if (this.isPaused) {
                // Resume
                this.isPaused = false;
                this.isRunning = true;
                this.timer = setInterval(() => {
                    this.currentSeconds++;
                    this.totalSecond++;
                }, 1000);
                this.apiRestartTracking();
            }
        },

        // ==================== VALIDATION ====================
        validate() {
            this.errors = {};

            if (!this.form.client) this.errors.client = true;
            if (!this.form.project) this.errors.project = true;
            if (!this.form.teamMember) this.errors.teamMember = true;
            if (!this.form.service) this.errors.service = true;
            if (!this.form.hourlyRate) this.errors.hourlyRate = true;

            if (this.hasErrors) {
                toastr.error('Please fill in all required fields');
                return false;
            }
            return true;
        },

        updateDisplayNames() {
            const client = this.clients.find(c => c.id == this.form.client);
            const project = this.projects.find(p => p.id == this.form.project);
            const member = this.teamMembers.find(m => m.id == this.form.teamMember);
            const service = this.services.find(s => s.id == this.form.service);

            this.displayNames = {
                client: client?.name || '',
                project: project?.title || '',
                teamMember: member?.name || '',
                service: service?.project_service?.title || ''
            };
        },

        // ==================== API CALLS ====================
        async loadClients() {
            try {
                const {data} = await axios.get('/project-management/project/time-billing/customer/get');
                this.clients = data.customers || [];
            } catch (error) {
                console.error('Load clients error:', error);
                toastr.error('Failed to load clients');
            }
        },

        async loadTeamMembers() {
            try {
                const {data} = await axios.get('/project-management/project/time-billing/employees/get');
                this.teamMembers = data.employees || [];
            } catch (error) {
                console.error('Load team members error:', error);
                toastr.error('Failed to load team members');
            }
        },

        async onClientChange() {
            if (!this.form.client) {
                this.projects = [];
                return;
            }

            try {
                const {data} = await axios.get('/project-management/project/time-billing/projects/get', {
                    params: {user_id: this.form.client}
                });
                this.projects = data.projects || [];
                this.form.project = '';
            } catch (error) {
                console.error('Load projects error:', error);
                toastr.error('Failed to load projects');
            }
        },

        async onTeamMemberChange() {
            if (!this.form.teamMember) {
                this.services = [];
                return;
            }

            try {
                const {data} = await axios.get(
                    `/project-management/project/time-billing/employee/${this.form.teamMember}/services`
                );
                this.services = data.services || [];
                this.form.service = '';
            } catch (error) {
                console.error('Load services error:', error);
                toastr.error('Failed to load services');
            }
        },

        onServiceChange() {
            const service = this.services.find(s => s.id == this.form.service);
            if (service) {
                this.form.hourlyRate = service.fee;
            }
        },

        async loadRunningTime() {
            try {
                const {data} = await axios.get('/project-management/project/time-billing/time-tracking/getRunningTime');

                if (!data || Object.keys(data).length === 0) return;

                this.timeTrackingId = data.id;

                // Calculate current seconds if timer is running
                if (data.current_active_log) {
                    const startTime = new Date(data.current_active_log.start_time).getTime() / 1000;
                    const now = Date.now() / 1000;
                    this.currentSeconds = Math.floor(now - startTime);

                    const activeLogDuration = data.current_active_log.duration || 0;
                    this.totalSecond = (data.logs_sum_duration || 0) - activeLogDuration + this.currentSeconds;
                } else {
                    this.currentSeconds = 0;
                    this.totalSecond = data.logs_sum_duration || 0;
                }

                // Populate form with existing data
                this.form = {
                    client: data.user_id,
                    project: data.project.id,
                    teamMember: data.employee.id,
                    service: data.assign_project_service_id,
                    hourlyRate: data.rate,
                    description: data.description || ''
                };

                this.displayNames = {
                    client: data.client.name,
                    project: data.project.title,
                    teamMember: data.employee.name,
                    service: data.service_type.project_service.title
                };

                // FIX: Set billing type from API
                this.timeValue = data.bill_type;
                this.timeType = data.bill_type === 1 ? 'billable' : 'non-billable';

                // Set timer state based on API data
                if (data.current_active_log && data.current_active_log?.end_time == null) {
                    // Timer is currently running
                    this.isRunning = true;
                    this.isPaused = false;
                    this.showPreview = true;
                    this.timer = setInterval(() => {
                        this.currentSeconds++;
                        this.totalSecond++;
                    }, 1000);
                } else {
                    // Timer is paused
                    this.isRunning = false;
                    this.isPaused = true;
                    this.showPreview = true;
                }

            } catch (error) {
                console.error('Load running time error:', error);
            }
        },

        async apiStartTracking() {
            try {
                const {data} = await axios.post('/project-management/project/time-billing/time-tracking/start', {
                    project_id: this.form.project,
                    client_id: this.form.client,
                    employee_id: this.form.teamMember,
                    assign_project_service_id: this.form.service,
                    rate: this.form.hourlyRate,
                    description: this.form.description,
                    bill_type: this.timeValue
                });

                this.updateDisplayNames();

                this.timeTrackingId = data.entry.id;
                toastr.success(data.message);
            } catch (error) {
                console.error('Start tracking error:', error);
                toastr.error(error.response?.data?.message || 'Failed to start tracking');
            }
        },

        async apiPauseTracking() {
            try {
                const {data} = await axios.post('/project-management/project/time-billing/time-tracking/pauseTracking', {
                    time_tracker_id: this.timeTrackingId
                });
                this.currentSeconds = 0;
                toastr.warning(data.message);
            } catch (error) {
                console.error('Pause tracking error:', error);
                toastr.error(error.response?.data?.message || 'Failed to pause tracking');
            }
        },

        async apiRestartTracking() {
            try {
                const {data} = await axios.post('/project-management/project/time-billing/time-tracking/restartTracking', {
                    time_tracker_id: this.timeTrackingId
                });
                toastr.success(data.message);
            } catch (error) {
                console.error('Restart tracking error:', error);
                toastr.error(error.response?.data?.message || 'Failed to restart tracking');
            }
        },

        async handleSave() {
            try {
                const response = await axios.post('/project-management/project/time-billing/time-tracking/saveTracking', {
                    time_tracker_id: this.timeTrackingId
                });
                toastr.success(response.data.message);
                window.location.href = `/project-management/project/time-billing/${this.form.project}`;
            } catch (error) {
                toastr.error(error.response?.data?.message || 'Save failed');
            }
        },

        // ==================== CLEANUP ====================
        addEventListeners() {
            document.addEventListener('mouseup', this.stopProgress);
            document.addEventListener('touchend', this.stopProgress);
        },

        cleanup() {
            clearInterval(this.timer);
            document.removeEventListener('mouseup', this.stopProgress);
            document.removeEventListener('touchend', this.stopProgress);
        },
    }
});
