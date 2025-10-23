<?php
/**
 * Appointment Form Component
 * Appointment booking form with doctor search and time slot selection
 */

require_once __DIR__ . '/../Component.php';

class AppointmentForm extends Component {
    public function render(): string {
        $action = $this->prop('action', '/appointments/book');
        $method = $this->prop('method', 'POST');
        $csrfToken = $this->prop('csrfToken', '');
        $errors = $this->prop('errors', []);
        $doctors = $this->prop('doctors', []);
        $specializations = $this->prop('specializations', []);
        $availableSlots = $this->prop('availableSlots', []);
        $selectedDoctor = $this->prop('selectedDoctor', '');
        $selectedDate = $this->prop('selectedDate', '');
        $selectedSlot = $this->prop('selectedSlot', '');
        
        $classes = $this->buildClass('appointment-form');
        
        $attributes = $this->attributes;
        $attributes['class'] = $classes;
        $this->attributes = $attributes;
        
        $errorHtml = '';
        if (!empty($errors)) {
            $errorHtml = '<div class="alert alert-danger">';
            foreach ($errors as $error) {
                $errorHtml .= "<p class='mb-0'>{$this->escape($error)}</p>";
            }
            $errorHtml .= '</div>';
        }
        
        $specializationOptions = '';
        foreach ($specializations as $spec) {
            $specializationOptions .= "<option value='{$spec['specialization_id']}'>{$this->escape($spec['specialization_name'])}</option>";
        }
        
        $doctorOptions = '';
        foreach ($doctors as $doctor) {
            $selected = $doctor['user_id'] == $selectedDoctor ? 'selected' : '';
            $doctorOptions .= "<option value='{$doctor['user_id']}' {$selected}>{$this->escape($doctor['first_name'])} {$this->escape($doctor['last_name'])} - {$this->escape($doctor['specialization_name'])}</option>";
        }
        
        $timeSlotsHtml = '';
        if (!empty($availableSlots)) {
            $timeSlotsHtml = '<div class="time-slots-container"><h6>Available Time Slots:</h6><div class="time-slots-grid">';
            foreach ($availableSlots as $slot) {
                $selected = $slot === $selectedSlot ? 'selected' : '';
                $timeSlotsHtml .= "<button type='button' class='time-slot btn btn-outline-primary {$selected}' data-slot='{$slot}'>{$slot}</button>";
            }
            $timeSlotsHtml .= '</div></div>';
        }
        
        return "
        <form {$this->buildAttributes()} action='{$action}' method='{$method}'>
            {$errorHtml}
            <input type='hidden' name='csrf_token' value='{$this->escape($csrfToken)}'>
            <input type='hidden' name='doctor_id' id='selected_doctor_id' value='{$selectedDoctor}'>
            <input type='hidden' name='time_slot' id='selected_time_slot' value='{$selectedSlot}'>
            
            <div class='row'>
                <div class='col-md-6'>
                    <div class='mb-3'>
                        <label for='specialization' class='form-label'>Specialization</label>
                        <select class='form-select' id='specialization' name='specialization'>
                            <option value=''>Select Specialization</option>
                            {$specializationOptions}
                        </select>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='mb-3'>
                        <label for='doctor' class='form-label'>Doctor</label>
                        <select class='form-select' id='doctor' name='doctor' disabled>
                            <option value=''>Select Doctor</option>
                            {$doctorOptions}
                        </select>
                    </div>
                </div>
            </div>
            
            <div class='row'>
                <div class='col-md-6'>
                    <div class='mb-3'>
                        <label for='appointment_date' class='form-label'>Appointment Date</label>
                        <input type='date' class='form-control' id='appointment_date' name='appointment_date' value='{$selectedDate}' min='" . date('Y-m-d') . "' required>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='mb-3'>
                        <label for='consultation_fee' class='form-label'>Consultation Fee</label>
                        <input type='text' class='form-control' id='consultation_fee' name='consultation_fee' readonly>
                    </div>
                </div>
            </div>
            
            {$timeSlotsHtml}
            
            <div class='mb-3'>
                <label for='notes' class='form-label'>Notes (Optional)</label>
                <textarea class='form-control' id='notes' name='notes' rows='3' placeholder='Any additional information or symptoms...'></textarea>
            </div>
            
            <div class='d-grid'>
                <button type='submit' class='btn btn-primary btn-lg'>Book Appointment</button>
            </div>
        </form>";
    }
}
