@props([
    'title' => 'Add Payment Method',
    'buttonText' => 'Save Card',
])

    <script src="https://js.stripe.com/v3/"></script>
    <style>

        #payment-card {
            width: 100%;
            max-width: 480px;
            animation: fadeIn 0.3s ease-out;
        }

        #payment-card .payment-card-form {
            width: 100%;
            padding: 32px;
            border-radius: var(--payment-card-radius);
            box-shadow: var(--payment-card-shadow);
            border: 1px solid var(--payment-card-border);
            background-color: var(--payment-card-background);
        }

        #payment-card .payment-card-form h2 {
            margin: 0 0 28px 0;
            color: var(--payment-card-text);
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            letter-spacing: -0.5px;
        }

        #payment-card .form-group {
            margin-bottom: 15px;
            position: relative;
        }

        #payment-card .form-row {
            display: flex;
            gap: 10px;
        }

        #payment-card .half-width {
            flex: 1;
            min-width: 0;
        }

        #payment-card label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            color: var(--payment-card-text-light);
            font-weight: 500;
        }

        #payment-card input,
        #payment-card .stripe-element {
            width: 100%;
            padding: 10px 10px;
            border: 1px solid var(--payment-card-border);
            border-radius: var(--payment-card-radius);
            background: white;
            font-size: 15px;
            color: #5F5F5F;
            transition: var(--payment-card-transition);
            box-sizing: border-box;
            appearance: none;
            outline: none;
            box-shadow: none;
        }

        #payment-card input::placeholder {
            color: #94a3b8;
            opacity: 1;
        }

        #payment-card .stripe-element {
            padding: 13px 16px;
            height: 48px;
        }

        #payment-card input:hover,
        #payment-card .stripe-element:hover {
            border-color: var(--payment-card-border-hover);
        }

        #payment-card input:focus,
        #payment-card .stripe-element:focus {
            outline: none;
            box-shadow: none;
            border-color: var(--payment-card-border);
        }

        #payment-card .security-notice {
            display: flex;
            align-items: center;
            margin: 10px 0 15px 0;
            font-size: 13px;
            color: grey;
            line-height: 1.5;
            padding: 10px;
            background-color: rgba(248, 250, 252, 0.8);
            border-radius: var(--payment-card-radius);
            border: 1px solid var(--payment-card-border);
        }

        #payment-card .lock-icon {
            flex-shrink: 0;
            width: 16px;
            height: 16px;
            margin-right: 10px;
            margin-top: 2px;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%2364748b"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>');
        }

        #payment-card button {
            width: 100%;
            padding: 10px;
            background-color: var(--payment-card-primary);
            color: white;
            border: none;
            border-radius: var(--payment-card-radius);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--payment-card-transition);
            margin-top: 8px;
            position: relative;
            overflow: hidden;
        }

        #payment-card button:hover {
            background-color: var(--payment-card-primary-hover);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }

        #payment-card button:active {
            transform: translateY(1px);
        }

        #payment-card button:disabled {
            background-color: #cbd5e1;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        #payment-card button::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: scale(0);
            transition: var(--payment-card-transition);
            border-radius: 50%;
        }

        #payment-card button:hover::after {
            transform: scale(1);
        }

        #payment-card .error-message {
            color: var(--payment-card-error);
            font-size: 13px;
            margin-top: 8px;
            display: none;
        }

        #payment-card .has-error input,
        #payment-card .has-error .stripe-element {
            border-color: var(--payment-card-error);
        }

        #payment-card .has-error .error-message {
            display: block;
        }

        #payment-card .status-message {
            margin-top: 15px;
            padding: 12px;
            border-radius: var(--payment-card-radius);
            font-size: 14px;
            text-align: center;
            display: none;
        }

        #payment-card .status-success {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
            display: block;
        }

        #payment-card .status-error {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            display: block;
        }

        #payment-card .status-info {
            background-color: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
            display: block;
        }

        @media (max-width: 480px) {
            #payment-card {
                padding: 16px;
            }

            #payment-card .payment-card-form {
                padding: 24px 20px;
                border-radius: 0;
                border: none;
                box-shadow: none;
            }

            #payment-card .form-row {
                flex-direction: column;
                gap: 20px;
            }

            #payment-card .half-width {
                width: 100%;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }

        #payment-card .processing {
            animation: pulse 1.5s infinite;
        }

        #payment-card .spinner {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
            vertical-align: middle;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
<div class="container-fluid" id="payment-card">
    <div class="payment-card-form">
        <h2>{{ $title }}</h2>

        <div class="form-group">
            <label for="card-number">Card Number</label>
            <div id="card-number" class="stripe-element">
                <!-- Stripe Card Number element will be inserted here -->
            </div>
            <div class="error-message" id="card-number-error"></div>
        </div>

        <div class="form-row">
            <div class="form-group half-width">
                <label for="card-expiry">Expiration Date</label>
                <div id="card-expiry" class="stripe-element">
                    <!-- Stripe Expiry element will be inserted here -->
                </div>
                <div class="error-message" id="card-expiry-error"></div>
            </div>

            <div class="form-group half-width">
                <label for="card-cvc">Security Code</label>
                <div id="card-cvc" class="stripe-element">
                    <!-- Stripe CVC element will be inserted here -->
                </div>
                <div class="error-message" id="card-cvc-error"></div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group half-width">
                <label for="first-name">First Name</label>
                <input type="text" id="first-name" placeholder="Enter first name" required>
                <div class="error-message" id="first-name-error"></div>
            </div>

            <div class="form-group half-width">
                <label for="last-name">Last Name</label>
                <input type="text" id="last-name" placeholder="Enter last name" required>
                <div class="error-message" id="last-name-error"></div>
            </div>
        </div>

        <div class="security-notice">
            <span class="lock-icon"></span>
            <span>Your payment information is encrypted and securely processed by Stripe. We never store your card details on our servers.</span>
        </div>

        <input type="hidden" name="payment_method_id" id="payment_method_id">

        <button type="button" id="submit-button">
            <span id="button-text">{{ $buttonText }}</span>
        </button>

        <div id="payment-status" class="status-message"></div>
    </div>
</div>

<script>
    // Initialize Stripe with your publishable key
    const stripe = Stripe("{{ config('services.stripe.public') }}");

    // Create Stripe elements
    const elements = stripe.elements();
    const cardNumber = elements.create('cardNumber', {
        style: {
            base: {
                color: '#1e293b',
                fontSize: '15px',
                fontFamily: '"Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
                '::placeholder': {
                    color: '#94a3b8',
                },
            },
            invalid: {
                color: '#dc2626',
            },
        },
        showIcon: true,
        placeholder: '1234 1234 1234 1234'
    });
    cardNumber.mount('#card-number');

    const cardExpiry = elements.create('cardExpiry', {
        style: {
            base: {
                color: '#1e293b',
                fontSize: '15px',
                fontFamily: '"Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
                '::placeholder': {
                    color: '#94a3b8',
                },
            },
            invalid: {
                color: '#dc2626',
            },
        },
        placeholder: 'MM/YY'
    });
    cardExpiry.mount('#card-expiry');

    const cardCvc = elements.create('cardCvc', {
        style: {
            base: {
                color: '#1e293b',
                fontSize: '15px',
                fontFamily: '"Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
                '::placeholder': {
                    color: '#94a3b8',
                },
            },
            invalid: {
                color: '#dc2626',
            },
        },
        placeholder: 'CVC'
    });
    cardCvc.mount('#card-cvc');

    // Form validation functions
    function setError(element, errorElement, message) {
        element.classList.add('has-error');
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }

    function clearError(element, errorElement) {
        element.classList.remove('has-error');
        errorElement.style.display = 'none';
    }

    function validateField(fieldId, errorId) {
        const field = document.getElementById(fieldId);
        const errorElement = document.getElementById(errorId);

        if (!field.value.trim()) {
            setError(field, errorElement, 'This field is required');
            return false;
        }

        clearError(field, errorElement);
        return true;
    }

    // Status message display
    function showStatus(message, type) {
        const statusElement = document.getElementById('payment-status');
        statusElement.textContent = message;
        statusElement.className = 'status-message'; // Reset classes

        if (type === 'success') {
            statusElement.classList.add('status-success');
        } else if (type === 'error') {
            statusElement.classList.add('status-error');
        } else {
            statusElement.classList.add('status-info');
        }
    }

    // Reset form function
    function resetForm() {
        document.getElementById('first-name').value = '';
        document.getElementById('last-name').value = '';
        cardNumber.clear();
        cardExpiry.clear();
        cardCvc.clear();

        const submitButton = document.getElementById('submit-button');
        const buttonText = document.getElementById('button-text');
        submitButton.disabled = false;
        submitButton.style.backgroundColor = '';
        buttonText.textContent = 'Add Payment Method';
        document.querySelector('.spinner')?.remove();
    }

    // Handle Stripe element validation
    cardNumber.on('change', function(event) {
        const errorElement = document.getElementById('card-number-error');
        if (event.error) {
            setError(document.getElementById('card-number').parentElement, errorElement, event.error.message);
        } else {
            clearError(document.getElementById('card-number').parentElement, errorElement);
        }
    });

    cardExpiry.on('change', function(event) {
        const errorElement = document.getElementById('card-expiry-error');
        if (event.error) {
            setError(document.getElementById('card-expiry').parentElement, errorElement, event.error.message);
        } else {
            clearError(document.getElementById('card-expiry').parentElement, errorElement);
        }
    });

    cardCvc.on('change', function(event) {
        const errorElement = document.getElementById('card-cvc-error');
        if (event.error) {
            setError(document.getElementById('card-cvc').parentElement, errorElement, event.error.message);
        } else {
            clearError(document.getElementById('card-cvc').parentElement, errorElement);
        }
    });

    // Form submission handler
    document.getElementById('submit-button').addEventListener('click', async () => {
        // Validate fields
        const isFirstNameValid = validateField('first-name', 'first-name-error');
        const isLastNameValid = validateField('last-name', 'last-name-error');

        if (!isFirstNameValid || !isLastNameValid) {
            showStatus('Please fill in all required fields', 'error');
            return;
        }

        // Set loading state
        const submitButton = document.getElementById('submit-button');
        const buttonText = document.getElementById('button-text');
        submitButton.disabled = true;
        buttonText.textContent = 'Processing...';
        submitButton.insertAdjacentHTML('beforeend', '<span class="spinner"></span>');
        showStatus('Processing your payment method...', 'info');

        try {
            // Create payment method
            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardNumber,
                billing_details: {
                    name: `${document.getElementById('first-name').value} ${document.getElementById('last-name').value}`
                }
            });

            if (error) {
                throw error;
            }

            document.getElementById('payment_method_id').value = paymentMethod.id;
            console.log('PaymentMethod created:', paymentMethod);
            showStatus('Payment Card Verified!', 'success');

            // ========== Call to function that is in Current View ============= //
                await submitaddedCard();

            // Simulate server request
            await new Promise(resolve => setTimeout(resolve, 1000));

            // Update button to show success
            submitButton.style.backgroundColor = '#10b981';
            buttonText.textContent = 'Success!';

            // Reset form after delay
            setTimeout(() => {
                resetForm();
                showStatus('', ''); // Clear status message
            }, 2000);

        } catch (error) {
            console.error('Error:', error);
            showStatus(`Error: ${error.message}`, 'error');

            // Update button to show error
            submitButton.style.backgroundColor = '';
            buttonText.textContent = 'Add Payment Method';

            // Reset button after delay
            setTimeout(() => {
                submitButton.style.backgroundColor = '';
                buttonText.textContent = 'Add Payment Method';
                submitButton.disabled = false;
                document.querySelector('.spinner')?.remove();
            }, 3000);
        }
    });
</script>
