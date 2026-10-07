<div >
    <div class="modal fade" id="timeTrackerModal" tabindex="-1" aria-hidden="true" v-cloak>
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Time Tracker</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <!-- Billable Toggle -->
                    <div class="toggle-container d-flex justify-content-center mb-4" v-if="!isRunning && !isPaused">
                        <div class="btn-group" role="group">
                            <button type="button"
                                    :class="['btn', 'py-2', 'border-0', 'shadow-none',
                                            timeType === 'billable' ? 'btn-primary active' : 'btn-outline-primary']"
                                    @click="setTimeType('billable', 1)">
                                Billable Hour
                            </button>
                            <button type="button"
                                    :class="['btn', 'py-2', 'border-0', 'shadow-none',
                                            timeType === 'non-billable' ? 'btn-primary active' : 'btn-outline-primary']"
                                    @click="setTimeType('non-billable', 0)">
                                Non-Billable Hour
                            </button>
                        </div>
                    </div>

                    <!-- Timer Display -->
                    <div class="timer-display text-center mb-4">
                        <div class="time mb-2">@{{ formattedTime }}</div>
                        <div class="text-muted mb-3">
                            Total time tracked: <span>@{{ formattedTotalTime }}</span>
                        </div>
                    </div>

                    <!-- Progress Circle -->
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="progress-wrapper"
                             @mousedown="startProgress"
                             @touchstart="startProgress">
                            <svg class="progress-circle" viewBox="0 0 88 88">
                                <circle cx="44" cy="44" r="40"
                                        :style="{ strokeDashoffset: progressOffset }"></circle>
                            </svg>
                            <div class="inner-circle d-flex align-items-center justify-content-center"
                                 :style="{ backgroundColor: isRunning ? 'rgba(115, 103, 240, 0.19)' : 'transparent' }">
                                <div :class="['running-text', { active: isRunning || isPaused }]">
                                    <!-- Initial Play Icon -->
                                    <svg v-show="!isRunning && !isPaused" class="initial-pause"
                                         xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                                         viewBox="0 0 48 48" fill="none">
                                        <g clip-path="url(#clip0)">
                                            <path d="M18.75 31.4996V16.5004L30.9368 24L18.75 31.4996Z"
                                                  stroke="url(#paint0)" stroke-width="9.5"
                                                  stroke-linecap="round" stroke-linejoin="round"/>
                                        </g>
                                        <defs>
                                            <linearGradient id="paint0" x1="27" y1="8" x2="27" y2="40">
                                                <stop stop-color="#7367F0"/>
                                                <stop offset="1" stop-color="#423B8A"/>
                                            </linearGradient>
                                            <clipPath id="clip0">
                                                <rect width="48" height="48" fill="white"/>
                                            </clipPath>
                                        </defs>
                                    </svg>

                                    <!-- Running/Paused Text -->
                                    <div v-show="isRunning" class="running-inner-text">Running</div>
                                    <div v-show="isPaused" class="running-inner-text">Paused</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timer Controls -->
                    <div class="d-flex justify-content-center align-items-center gap-3 mt-4">
                        <!-- Play/Pause Button -->
                        <div v-show="isRunning || isPaused" @click="togglePause" style="cursor: pointer;">
                            <i :class="['fa', isPaused ? 'fa-play' : 'fa-pause', 'play-pause-icon btn btn-label-primary border border-primary']"
                               style="font-size: 24px; color: #7367F0;"></i>
                        </div>
                    </div>

                    <!-- Form -->
                    <form v-show="!showPreview" class="mt-4">
                        <div class="mb-3">
                            <label class="form-label">Client <span class="text-danger">*</span></label>
                            <select v-model="form.client"
                                    :class="['form-select', { 'is-invalid': errors.client }]"
                                    @change="onClientChange">
                                <option value="" disabled>Select Client</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">
                                    @{{ client.name }}
                                </option>
                            </select>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Project <span class="text-danger">*</span></label>
                                <select v-model="form.project"
                                        :class="['form-select', { 'is-invalid': errors.project }]">
                                    <option value="" disabled>Select Project</option>
                                    <option v-for="project in projects" :key="project.id" :value="project.id">
                                        @{{ project.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Employee <span class="text-danger">*</span></label>
                                <select v-model="form.teamMember"
                                        :class="['form-select', { 'is-invalid': errors.teamMember }]"
                                        @change="onTeamMemberChange">
                                    <option value="" disabled>Select Employee</option>
                                    <option v-for="member in teamMembers" :key="member.id" :value="member.id">
                                        @{{ member.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Service <span class="text-danger">*</span></label>
                                <select v-model="form.service"
                                        :class="['form-select', { 'is-invalid': errors.service }]"
                                        @change="onServiceChange">
                                    <option value="" disabled>Select Service</option>
                                    <option v-for="service in services" :key="service.id" :value="service.id">
                                        @{{ service.project_service.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hourly Rate ($) <span class="text-danger">*</span></label>
                                <input type="number" v-model="form.hourlyRate"
                                       :class="['form-control', { 'is-invalid': errors.hourlyRate }]"
                                       step="0.01">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea v-model="form.description" class="form-control" rows="3"></textarea>
                        </div>
                    </form>

                    <!-- Preview -->
                    <div v-show="showPreview" class="preview-section">
                        <div class="preview-row">
                            <div class="status-container">
                                <div class="status-label">Time Type</div>
                                <div class="status-pill">@{{ timeType === 'billable' ? 'Billable' : 'Non-Billable' }}
                                </div>
                            </div>
                            <div class="status-container">
                                <div class="status-label">Duration</div>
                                <div class="status-pill">@{{ formattedTotalTime }}</div>
                            </div>
                            <div class="status-container">
                                <div class="status-label">Client</div>
                                <div class="status-pill">@{{ displayNames.client }}</div>
                            </div>
                            <div class="status-container">
                                <div class="status-label">Project</div>
                                <div class="status-pill">@{{ displayNames.project }}</div>
                            </div>
                        </div>
                        <div class="preview-row">
                            <div class="status-container">
                                <div class="status-label">Employee</div>
                                <div class="status-pill">@{{ displayNames.teamMember }}</div>
                            </div>
                            <div class="status-container">
                                <div class="status-label">Service</div>
                                <div class="status-pill">@{{ displayNames.service }}</div>
                            </div>
                            <div class="status-container">
                                <div class="status-label">Hourly Rate</div>
                                <div class="status-pill">$@{{ form.hourlyRate }}</div>
                            </div>
                            <div class="status-container">
                                <div class="status-label">Description</div>
                                <div class="status-pill">@{{ form.description || 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div v-if="isPaused" class="d-flex align-items-center justify-content-center mt-3">
                        <button class="btn btn-primary" @click="handleSave">Save Time</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


