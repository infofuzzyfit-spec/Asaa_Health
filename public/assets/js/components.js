/**
 * ASAA Healthcare Management System - Component JavaScript
 * Handles component interactions and functionality
 */

class ComponentManager {
    constructor() {
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.initializeComponents();
    }
    
    bindEvents() {
        // Back to top button
        $(window).scroll(() => {
            if ($(window).scrollTop() > 300) {
                $('.back-to-top').addClass('show');
            } else {
                $('.back-to-top').removeClass('show');
            }
        });
        
        // Time slot selection
        $(document).on('click', '.time-slot', function() {
            if ($(this).hasClass('booked') || $(this).hasClass('past')) {
                return;
            }
            
            $('.time-slot').removeClass('selected');
            $(this).addClass('selected');
            $('#selected_time_slot').val($(this).data('slot'));
        });
        
        // Doctor selection
        $(document).on('change', '#specialization', function() {
            const specializationId = $(this).val();
            const doctorSelect = $('#doctor');
            
            if (specializationId) {
                doctorSelect.prop('disabled', false);
                this.loadDoctors(specializationId);
            } else {
                doctorSelect.prop('disabled', true);
                doctorSelect.html('<option value="">Select Doctor</option>');
            }
        });
        
        // Doctor change
        $(document).on('change', '#doctor', function() {
            const doctorId = $(this).val();
            if (doctorId) {
                $('#selected_doctor_id').val(doctorId);
                this.loadDoctorDetails(doctorId);
                this.loadAvailableSlots(doctorId);
            }
        });
        
        // Date change
        $(document).on('change', '#appointment_date', function() {
            const doctorId = $('#doctor').val();
            const date = $(this).val();
            
            if (doctorId && date) {
                this.loadAvailableSlots(doctorId, date);
            }
        });
        
        // Form validation
        $(document).on('submit', 'form', function(e) {
            if (!this.validateForm($(this))) {
                e.preventDefault();
            }
        });
        
        // Auto-hide alerts
        $('.alert[data-auto-hide="true"]').each(function() {
            const duration = $(this).data('duration') || 5000;
            setTimeout(() => {
                $(this).fadeOut();
            }, duration);
        });
    }
    
    initializeComponents() {
        // Initialize Select2
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2-wrapper select').select2({
                placeholder: 'Select an option',
                allowClear: true
            });
        }
        
        // Initialize tooltips
        if (typeof $.fn.tooltip !== 'undefined') {
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
        
        // Initialize popovers
        if (typeof $.fn.popover !== 'undefined') {
            $('[data-bs-toggle="popover"]').popover();
        }
        
        // Initialize modals
        $('.modal').on('shown.bs.modal', function() {
            $(this).find('input:first').focus();
        });
    }
    
    loadDoctors(specializationId) {
        $.ajax({
            url: '/api/doctors/by-specialization',
            method: 'GET',
            data: { specialization_id: specializationId },
            success: (response) => {
                const doctorSelect = $('#doctor');
                doctorSelect.html('<option value="">Select Doctor</option>');
                
                response.doctors.forEach(doctor => {
                    doctorSelect.append(`
                        <option value="${doctor.user_id}" data-fee="${doctor.consultation_fee}">
                            ${doctor.first_name} ${doctor.last_name} - ${doctor.specialization_name}
                        </option>
                    `);
                });
            },
            error: (xhr) => {
                this.showError('Failed to load doctors');
            }
        });
    }
    
    loadDoctorDetails(doctorId) {
        const selectedOption = $(`#doctor option[value="${doctorId}"]`);
        const fee = selectedOption.data('fee');
        $('#consultation_fee').val(fee ? `LKR ${fee}` : '');
    }
    
    loadAvailableSlots(doctorId, date = null) {
        if (!date) {
            date = $('#appointment_date').val();
        }
        
        if (!date) {
            return;
        }
        
        $('.time-slots-container').html('<div class="text-center"><div class="loading"></div> Loading time slots...</div>');
        
        $.ajax({
            url: '/api/appointments/available-slots',
            method: 'GET',
            data: { 
                doctor_id: doctorId,
                date: date
            },
            success: (response) => {
                this.renderTimeSlots(response.slots);
            },
            error: (xhr) => {
                $('.time-slots-container').html('<div class="alert alert-danger">Failed to load time slots</div>');
            }
        });
    }
    
    renderTimeSlots(slots) {
        let html = '<div class="time-slots-grid">';
        
        if (slots.length === 0) {
            html = '<div class="alert alert-info">No available time slots for this date</div>';
        } else {
            slots.forEach(slot => {
                const isPast = this.isPastTimeSlot(slot);
                const classes = isPast ? 'time-slot past' : 'time-slot available';
                html += `<button type="button" class="${classes}" data-slot="${slot}">${slot}</button>`;
            });
            html += '</div>';
        }
        
        $('.time-slots-container').html(html);
    }
    
    isPastTimeSlot(slot) {
        const today = new Date();
        const selectedDate = new Date($('#appointment_date').val());
        const slotTime = new Date(selectedDate.toDateString() + ' ' + slot);
        
        return slotTime < today;
    }
    
    validateForm(form) {
        let isValid = true;
        
        // Clear previous errors
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
        
        // Required field validation
        form.find('[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                $(this).after(`<div class="invalid-feedback">This field is required</div>`);
                isValid = false;
            }
        });
        
        // Email validation
        form.find('input[type="email"]').each(function() {
            const email = $(this).val();
            if (email && !this.isValidEmail(email)) {
                $(this).addClass('is-invalid');
                $(this).after(`<div class="invalid-feedback">Please enter a valid email address</div>`);
                isValid = false;
            }
        });
        
        // Password confirmation
        const password = form.find('input[name="password"]').val();
        const passwordConfirmation = form.find('input[name="password_confirmation"]').val();
        if (password && passwordConfirmation && password !== passwordConfirmation) {
            form.find('input[name="password_confirmation"]').addClass('is-invalid');
            form.find('input[name="password_confirmation"]').after(`<div class="invalid-feedback">Passwords do not match</div>`);
            isValid = false;
        }
        
        return isValid;
    }
    
    isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    showError(message) {
        this.showAlert(message, 'danger');
    }
    
    showSuccess(message) {
        this.showAlert(message, 'success');
    }
    
    showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('body').prepend(alertHtml);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 5000);
    }
}

// Utility functions
function scrollToTop() {
    $('html, body').animate({ scrollTop: 0 }, 'slow');
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function formatTime(time) {
    return new Date('1970-01-01T' + time).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-LK', {
        style: 'currency',
        currency: 'LKR'
    }).format(amount);
}

// Table functions
function changePage(page) {
    // Implement pagination logic
    console.log('Changing to page:', page);
}

function viewRecord(id) {
    // Implement view record logic
    console.log('Viewing record:', id);
}

function editRecord(id) {
    // Implement edit record logic
    console.log('Editing record:', id);
}

function deleteRecord(id) {
    if (confirm('Are you sure you want to delete this record?')) {
        // Implement delete record logic
        console.log('Deleting record:', id);
    }
}

// Initialize component manager when DOM is ready
$(document).ready(function() {
    new ComponentManager();
});
