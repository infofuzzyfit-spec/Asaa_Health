<?php
/**
 * Email Service
 * Handles email notifications using PHPMailer
 */

require_once __DIR__ . '/../../config/email.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/User.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mailer;
    private $appointmentModel;
    private $userModel;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->appointmentModel = new Appointment();
        $this->userModel = new User();
        $this->configure();
    }
    
    private function configure() {
        try {
            $this->mailer->isSMTP();
            $this->mailer->Host = SMTP_HOST;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = SMTP_USERNAME;
            $this->mailer->Password = SMTP_PASSWORD;
            $this->mailer->SMTPSecure = SMTP_ENCRYPTION;
            $this->mailer->Port = SMTP_PORT;
            
            $this->mailer->setFrom(FROM_EMAIL, FROM_NAME);
            $this->mailer->addReplyTo(REPLY_TO_EMAIL, FROM_NAME);
        } catch (Exception $e) {
            error_log("Email configuration error: " . $e->getMessage());
        }
    }
    
    public function sendAppointmentStatusEmail($appointmentId, $status) {
        try {
            $appointment = $this->appointmentModel->find($appointmentId);
            if (!$appointment) {
                return false;
            }
            
            $patient = $this->userModel->find($appointment['patient_id']);
            $doctor = $this->userModel->find($appointment['doctor_id']);
            
            if (!$patient || !$doctor) {
                return false;
            }
            
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($patient['email'], $patient['first_name'] . ' ' . $patient['last_name']);
            
            $template = $this->getEmailTemplate($status);
            $subject = $this->getEmailSubject($status);
            
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $this->replaceTemplateVariables($template, [
                'patient_name' => $patient['first_name'] . ' ' . $patient['last_name'],
                'doctor_name' => $doctor['first_name'] . ' ' . $doctor['last_name'],
                'appointment_date' => date('F j, Y', strtotime($appointment['appointment_date'])),
                'appointment_time' => $appointment['time_slot'],
                'appointment_id' => $appointmentId
            ]);
            
            $result = $this->mailer->send();
            $this->logEmail($patient['email'], $subject, $result ? 'SENT' : 'FAILED');
            
            return $result;
        } catch (Exception $e) {
            error_log("Email send error: " . $e->getMessage());
            $this->logEmail('', '', 'FAILED');
            return false;
        }
    }
    
    public function sendNewUserCredentials($userId, $password) {
        try {
            $user = $this->userModel->find($userId);
            if (!$user) {
                return false;
            }
            
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($user['email'], $user['first_name'] . ' ' . $user['last_name']);
            
            $template = $this->getEmailTemplate('new-user-credentials');
            $subject = 'Your ASAA Healthcare Account Credentials';
            
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $this->replaceTemplateVariables($template, [
                'user_name' => $user['first_name'] . ' ' . $user['last_name'],
                'email' => $user['email'],
                'password' => $password,
                'role' => $user['role']
            ]);
            
            $result = $this->mailer->send();
            $this->logEmail($user['email'], $subject, $result ? 'SENT' : 'FAILED');
            
            return $result;
        } catch (Exception $e) {
            error_log("Email send error: " . $e->getMessage());
            $this->logEmail('', '', 'FAILED');
            return false;
        }
    }
    
    private function getEmailTemplate($type) {
        $templateFile = EMAIL_TEMPLATES_PATH . "appointment-{$type}.html";
        
        if (!file_exists($templateFile)) {
            $templateFile = EMAIL_TEMPLATES_PATH . "appointment-review.html"; // Default template
        }
        
        return file_get_contents($templateFile);
    }
    
    private function getEmailSubject($status) {
        $subjects = [
            'REVIEW' => 'Appointment Under Review - ASAA Healthcare',
            'ACCEPTED' => 'Appointment Confirmed - ASAA Healthcare',
            'CANCELLED' => 'Appointment Cancelled - ASAA Healthcare',
            'CONSULTING' => 'Consultation in Progress - ASAA Healthcare',
            'COMPLETED' => 'Appointment Completed - ASAA Healthcare'
        ];
        
        return $subjects[$status] ?? 'Appointment Update - ASAA Healthcare';
    }
    
    private function replaceTemplateVariables($template, $variables) {
        foreach ($variables as $key => $value) {
            $template = str_replace("{{$key}}", $value, $template);
        }
        
        return $template;
    }
    
    private function logEmail($recipient, $subject, $status) {
        $logData = [
            'recipient_email' => $recipient,
            'subject' => $subject,
            'status' => $status,
            'sent_at' => date('Y-m-d H:i:s')
        ];
        
        // Log to database
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO email_logs (recipient_email, subject, status, sent_at) VALUES (?, ?, ?, ?)",
            array_values($logData)
        );
    }
}
