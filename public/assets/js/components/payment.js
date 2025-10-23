/**
 * Payment Component JavaScript
 * Handles payment processing functionality
 */

class PaymentHandler {
    constructor() {
        this.paymentMethod = null;
        this.appointmentId = null;
        this.amount = null;
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.initializePaymentMethods();
    }
    
    bindEvents() {
        // Payment method selection
        $(document).on('change', 'input[name="payment_method"]', (e) => {
            this.selectPaymentMethod(e.target.value);
        });
        
        // Payment form submission
        $(document).on('submit', '#payment-form', (e) => {
            this.handlePaymentSubmission(e);
        });
        
        // PayHere form submission
        $(document).on('submit', '#payhere-form', (e) => {
            this.handlePayHereSubmission(e);
        });
    }
    
    initializePaymentMethods() {
        // Add payment method selection UI if not present
        if ($('#payment-methods').length === 0) {
            this.createPaymentMethodSelector();
        }
    }
    
    createPaymentMethodSelector() {
        const paymentMethodsHtml = `
            <div id="payment-methods" class="row mb-4">
                <div class="col-12">
                    <h5>Select Payment Method</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card payment-method-card" data-method="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-credit-card fa-3x mb-3 text-primary"></i>
                                    <h6>Pay by Card</h6>
                                    <p class="text-muted">Secure online payment</p>
                                    <input type="radio" name="payment_method" value="card" class="form-check-input">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card payment-method-card" data-method="cash">
                                <div class="card-body text-center">
                                    <i class="fas fa-money-bill-wave fa-3x mb-3 text-success"></i>
                                    <h6>Pay at Reception</h6>
                                    <p class="text-muted">Pay cash at the front desk</p>
                                    <input type="radio" name="payment_method" value="cash" class="form-check-input">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#payment-form').prepend(paymentMethodsHtml);
        
        // Add click handlers for payment method cards
        $('.payment-method-card').on('click', function() {
            const method = $(this).data('method');
            $(`input[value="${method}"]`).prop('checked', true).trigger('change');
        });
    }
    
    selectPaymentMethod(method) {
        this.paymentMethod = method;
        
        // Update UI
        $('.payment-method-card').removeClass('border-primary');
        $(`.payment-method-card[data-method="${method}"]`).addClass('border-primary');
        
        // Show/hide relevant sections
        this.updatePaymentSections();
    }
    
    updatePaymentSections() {
        // Hide all payment sections
        $('.payment-section').hide();
        
        if (this.paymentMethod === 'card') {
            $('#card-payment-section').show();
        } else if (this.paymentMethod === 'cash') {
            $('#cash-payment-section').show();
        }
    }
    
    handlePaymentSubmission(e) {
        e.preventDefault();
        
        if (!this.paymentMethod) {
            this.showError('Please select a payment method');
            return false;
        }
        
        if (this.paymentMethod === 'card') {
            this.processCardPayment();
        } else if (this.paymentMethod === 'cash') {
            this.processCashPayment();
        }
    }
    
    processCardPayment() {
        const formData = this.getFormData();
        
        this.showLoading('Processing payment...');
        
        $.ajax({
            url: '/payment/card',
            method: 'POST',
            data: formData,
            success: (response) => {
                this.hideLoading();
                if (response.success) {
                    this.showPayHereForm(response.data);
                } else {
                    this.showError(response.message || 'Payment processing failed');
                }
            },
            error: (xhr) => {
                this.hideLoading();
                const response = xhr.responseJSON;
                this.showError(response?.message || 'Payment processing failed');
            }
        });
    }
    
    processCashPayment() {
        const formData = this.getFormData();
        
        this.showLoading('Recording cash payment...');
        
        $.ajax({
            url: '/payment/cash',
            method: 'POST',
            data: formData,
            success: (response) => {
                this.hideLoading();
                if (response.success) {
                    this.showSuccess('Cash payment recorded successfully');
                    setTimeout(() => {
                        window.location.href = '/appointments';
                    }, 2000);
                } else {
                    this.showError(response.message || 'Failed to record cash payment');
                }
            },
            error: (xhr) => {
                this.hideLoading();
                const response = xhr.responseJSON;
                this.showError(response?.message || 'Failed to record cash payment');
            }
        });
    }
    
    showPayHereForm(paymentData) {
        const payHereForm = `
            <form id="payhere-form" method="POST" action="${paymentData.return_url}">
                <input type="hidden" name="merchant_id" value="${paymentData.merchant_id}">
                <input type="hidden" name="return_url" value="${paymentData.return_url}">
                <input type="hidden" name="cancel_url" value="${paymentData.cancel_url}">
                <input type="hidden" name="notify_url" value="${paymentData.notify_url}">
                <input type="hidden" name="order_id" value="${paymentData.order_id}">
                <input type="hidden" name="items" value="${paymentData.items}">
                <input type="hidden" name="currency" value="${paymentData.currency}">
                <input type="hidden" name="amount" value="${paymentData.amount}">
                <input type="hidden" name="first_name" value="${paymentData.first_name}">
                <input type="hidden" name="last_name" value="${paymentData.last_name}">
                <input type="hidden" name="email" value="${paymentData.email}">
                <input type="hidden" name="phone" value="${paymentData.phone}">
                <input type="hidden" name="address" value="${paymentData.address}">
                <input type="hidden" name="city" value="${paymentData.city}">
                <input type="hidden" name="country" value="${paymentData.country}">
                <input type="hidden" name="hash" value="${paymentData.hash}">
            </form>
        `;
        
        $('body').append(payHereForm);
        
        // Show confirmation dialog
        this.showPayHereConfirmation(paymentData);
    }
    
    showPayHereConfirmation(paymentData) {
        const confirmationHtml = `
            <div class="modal fade" id="payhere-confirmation" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Payment Confirmation</h5>
                        </div>
                        <div class="modal-body">
                            <p>You will be redirected to PayHere to complete your payment.</p>
                            <div class="row">
                                <div class="col-6"><strong>Amount:</strong></div>
                                <div class="col-6">LKR ${parseFloat(paymentData.amount).toFixed(2)}</div>
                            </div>
                            <div class="row">
                                <div class="col-6"><strong>Order ID:</strong></div>
                                <div class="col-6">${paymentData.order_id}</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="proceed-payment">Proceed to Payment</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(confirmationHtml);
        
        const modal = new bootstrap.Modal(document.getElementById('payhere-confirmation'));
        modal.show();
        
        $('#proceed-payment').on('click', () => {
            modal.hide();
            $('#payhere-form').submit();
        });
        
        // Auto-submit after 5 seconds
        setTimeout(() => {
            if ($('#payhere-confirmation').hasClass('show')) {
                modal.hide();
                $('#payhere-form').submit();
            }
        }, 5000);
    }
    
    getFormData() {
        const formData = new FormData();
        
        // Add CSRF token
        const csrfToken = $('input[name="csrf_token"]').val();
        if (csrfToken) {
            formData.append('csrf_token', csrfToken);
        }
        
        // Add form data
        $('#payment-form').find('input, select, textarea').each(function() {
            if ($(this).attr('name') && $(this).attr('name') !== 'csrf_token') {
                formData.append($(this).attr('name'), $(this).val());
            }
        });
        
        return formData;
    }
    
    showLoading(message = 'Processing...') {
        const loadingHtml = `
            <div class="loading-overlay">
                <div class="loading-content">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">${message}</p>
                </div>
            </div>
        `;
        
        $('body').append(loadingHtml);
    }
    
    hideLoading() {
        $('.loading-overlay').remove();
    }
    
    showError(message) {
        this.hideAlerts();
        
        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('#payment-form').prepend(alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            $('.alert-danger').alert('close');
        }, 5000);
    }
    
    showSuccess(message) {
        this.hideAlerts();
        
        const alertHtml = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('#payment-form').prepend(alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            $('.alert-success').alert('close');
        }, 5000);
    }
    
    hideAlerts() {
        $('.alert').alert('close');
    }
}

// Initialize when document is ready
$(document).ready(function() {
    new PaymentHandler();
});

// Add CSS for payment components
const style = document.createElement('style');
style.textContent = `
    .payment-method-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .payment-method-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .payment-method-card.border-primary {
        border-color: var(--primary-color) !important;
    }
    
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    
    .loading-content {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        text-align: center;
    }
    
    .payment-section {
        display: none;
    }
    
    .payment-section.show {
        display: block;
    }
`;
document.head.appendChild(style);
