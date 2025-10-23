/**
 * Appointment Booking JavaScript
 * Handles appointment booking functionality
 */

class AppointmentBooking {
    constructor() {
        this.selectedDoctor = null;
        this.selectedDate = null;
        this.selectedTimeSlot = null;
        this.availableSlots = [];
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.initializeDatePicker();
        this.initializeDoctorSearch();
    }
    
    bindEvents() {
        // Doctor selection change
        $(document).on('change', '#doctor_id', (e) => {
            this.selectedDoctor = e.target.value;
            this.loadAvailableSlots();
        });
        
        // Date selection change
        $(document).on('change', '#appointment_date', (e) => {
            this.selectedDate = e.target.value;
            this.loadAvailableSlots();
        });
        
        // Time slot selection
        $(document).on('click', '.time-slot.available', (e) => {
            this.selectTimeSlot(e.target);
        });
        
        // Form submission
        $(document).on('submit', '#appointment-form', (e) => {
            this.validateForm(e);
        });
    }
    
    initializeDatePicker() {
        const today = new Date();
        const maxDate = new Date();
        maxDate.setMonth(maxDate.getMonth() + 3); // 3 months ahead
        
        $('#appointment_date').attr('min', today.toISOString().split('T')[0]);
        $('#appointment_date').attr('max', maxDate.toISOString().split('T')[0]);
    }
    
    initializeDoctorSearch() {
        $('#doctor_search').select2({
            placeholder: 'Search for a doctor or specialization...',
            allowClear: true,
            ajax: {
                url: '/api/doctors/search',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.doctors.map(doctor => ({
                            id: doctor.user_id,
                            text: `${doctor.first_name} ${doctor.last_name} - ${doctor.specialization_name}`,
                            doctor: doctor
                        }))
                    };
                },
                cache: true
            }
        });
        
        $('#doctor_search').on('select2:select', (e) => {
            const data = e.params.data;
            this.selectedDoctor = data.id;
            $('#doctor_id').val(data.id);
            this.loadAvailableSlots();
        });
    }
    
    loadAvailableSlots() {
        if (!this.selectedDoctor || !this.selectedDate) {
            this.clearTimeSlots();
            return;
        }
        
        this.showLoading();
        
        $.ajax({
            url: '/api/available-slots',
            method: 'GET',
            data: {
                doctor_id: this.selectedDoctor,
                date: this.selectedDate
            },
            success: (response) => {
                this.availableSlots = response.slots || [];
                this.renderTimeSlots();
            },
            error: (xhr) => {
                console.error('Error loading time slots:', xhr.responseText);
                this.showError('Failed to load available time slots');
            },
            complete: () => {
                this.hideLoading();
            }
        });
    }
    
    renderTimeSlots() {
        const container = $('#time-slots-container');
        container.empty();
        
        if (this.availableSlots.length === 0) {
            container.html('<p class="text-muted">No available time slots for this date.</p>');
            return;
        }
        
        const allSlots = this.generateAllTimeSlots();
        
        allSlots.forEach(slot => {
            const isAvailable = this.availableSlots.includes(slot);
            const isPast = this.isPastTimeSlot(slot);
            
            const slotElement = $(`
                <div class="time-slot ${isAvailable ? 'available' : 'booked'} ${isPast ? 'past' : ''}" 
                     data-time="${slot}">
                    ${slot}
                    ${!isAvailable ? '<i class="fas fa-lock ms-1"></i>' : ''}
                </div>
            `);
            
            if (isAvailable && !isPast) {
                slotElement.on('click', () => {
                    this.selectTimeSlot(slotElement[0]);
                });
            }
            
            container.append(slotElement);
        });
    }
    
    generateAllTimeSlots() {
        const slots = [];
        const start = 8; // 8 AM
        const end = 22; // 10 PM
        const lunchStart = 12; // 12 PM
        const lunchEnd = 13; // 1 PM
        
        for (let hour = start; hour < end; hour++) {
            if (hour >= lunchStart && hour < lunchEnd) {
                continue; // Skip lunch break
            }
            slots.push(`${hour.toString().padStart(2, '0')}:00`);
        }
        
        return slots;
    }
    
    isPastTimeSlot(slot) {
        if (!this.selectedDate) return false;
        
        const slotDateTime = new Date(`${this.selectedDate} ${slot}`);
        const now = new Date();
        
        return slotDateTime <= now;
    }
    
    selectTimeSlot(element) {
        // Remove previous selection
        $('.time-slot').removeClass('selected');
        
        // Add selection to clicked slot
        $(element).addClass('selected');
        
        // Store selected time slot
        this.selectedTimeSlot = $(element).data('time');
        $('#time_slot').val(this.selectedTimeSlot);
        
        // Enable form submission
        this.updateFormState();
    }
    
    clearTimeSlots() {
        $('#time-slots-container').empty();
        this.selectedTimeSlot = null;
        $('#time_slot').val('');
        this.updateFormState();
    }
    
    updateFormState() {
        const canSubmit = this.selectedDoctor && this.selectedDate && this.selectedTimeSlot;
        $('#submit-appointment').prop('disabled', !canSubmit);
    }
    
    validateForm(e) {
        if (!this.selectedDoctor) {
            e.preventDefault();
            this.showError('Please select a doctor');
            return false;
        }
        
        if (!this.selectedDate) {
            e.preventDefault();
            this.showError('Please select a date');
            return false;
        }
        
        if (!this.selectedTimeSlot) {
            e.preventDefault();
            this.showError('Please select a time slot');
            return false;
        }
        
        // Check if selected slot is still available
        if (!this.availableSlots.includes(this.selectedTimeSlot)) {
            e.preventDefault();
            this.showError('Selected time slot is no longer available');
            this.loadAvailableSlots(); // Refresh slots
            return false;
        }
        
        return true;
    }
    
    showLoading() {
        $('#time-slots-container').html('<div class="text-center"><div class="loading"></div> Loading time slots...</div>');
    }
    
    hideLoading() {
        // Loading will be replaced by renderTimeSlots()
    }
    
    showError(message) {
        // Remove existing alerts
        $('.alert-danger').remove();
        
        // Add new error alert
        const alert = $(`
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('#appointment-form').prepend(alert);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alert.alert('close');
        }, 5000);
    }
    
    showSuccess(message) {
        // Remove existing alerts
        $('.alert-success').remove();
        
        // Add new success alert
        const alert = $(`
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('#appointment-form').prepend(alert);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alert.alert('close');
        }, 5000);
    }
}

// Initialize when document is ready
$(document).ready(function() {
    new AppointmentBooking();
});

// Add CSS for selected state
const style = document.createElement('style');
style.textContent = `
    .time-slot.selected {
        background-color: var(--primary-color) !important;
        color: white !important;
        border-color: var(--primary-color) !important;
    }
    
    .time-slot.selected:hover {
        background-color: var(--primary-color) !important;
        color: white !important;
    }
`;
document.head.appendChild(style);
