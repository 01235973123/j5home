// donation-script.js - Complete multi-step donation form with Validation Engine

// Global variables
let currentStep = 1;
const totalSteps = 4;

function getJoomlaText(key, fallback) {
    try {
        if (typeof Joomla !== 'undefined' && 
            typeof Joomla.JText !== 'undefined' && 
            typeof Joomla.JText._ === 'function') {
            return Joomla.JText._(key, fallback || key);
        }
        
        // Fallback to script options if available
        if (typeof Joomla !== 'undefined' && 
            typeof Joomla.getOptions === 'function') {
            const options = Joomla.getOptions('joomla.jtext');
            if (options && options[key]) {
                return options[key];
            }
        }
        
        // Last fallback
        return fallback || key;
    } catch (e) {
        console.warn('Error getting Joomla text:', e);
        return fallback || key;
    }
}

var amountRequired = getJoomlaText('JD_PLEASE_SELECT_DONATION_AMOUNT_OR_ENTER_CUSTOM_AMOUNT', 'Please select a donation amount or enter a custom amount');


// Document ready
JD.jQuery(function($) {
    
    // Initialize validation engine
    setTimeout(function() {
        initializeValidation();
    
        // Setup form functionality
        setupStepNavigation();
        setupAmountSelection();
        setupPaymentMethods();
        setupFormValidation();
        
        // Initialize first step
        showStep(1);
        updateProgressBar();
        
    }, 100);
});

// Initialize validation engine
function initializeValidation() {
    const $ = JD.jQuery;
    
    // Initialize validation engine for the form
    $("#donation-form").validationEngine({
        promptPosition: "bottomLeft",
        scroll: false,
        showArrow: false,
        focusFirstField: false,
        autoHidePrompt: true,
        autoHideDelay: 5000,
        fadeDuration: 0.3,
        prettySelect: true,
        addSuccessCssClassToField: "input-valid",
        addFailureCssClassToField: "input-error"
    });
    
    //console.log('Validation engine initialized');
}

// Setup step navigation
function setupStepNavigation() {
    const $ = JD.jQuery;
    
    // Next button click
    $(document).on('click', '.btn-next', function(e) {
        e.preventDefault();
        //console.log('Next button clicked, current step:', currentStep);
        
        if (validateCurrentStep()) {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
                updateProgressBar();
                // Update summary on step 4
                if (currentStep === 4) {
                    updateDonationSummary();
                }
            }
        }
    });
    
    // Previous button click
    $(document).on('click', '.btn-prev', function(e) {
        e.preventDefault();
        //console.log('Previous button clicked, current step:', currentStep);
        
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
            updateProgressBar();
        }
    });
    
    // Submit button click
    $(document).on('click', '.btn-submit', function(e) {
        e.preventDefault();
        //console.log('Submit button clicked');
        submitForm();
    });
    
    // Progress step click
    $(document).on('click', '.progress-step.clickable', function() {
        const targetStep = parseInt($(this).data('step'));
        //console.log('Progress step clicked, target:', targetStep);
        
        // Validate all previous steps
        if (validateStepsUpTo(targetStep - 1)) {
            currentStep = targetStep;
            showStep(currentStep);
            updateProgressBar();
            
            if (currentStep === 4) {
                updateDonationSummary();
            }
        }
    });
}

// Validate current step
function validateCurrentStep() {
    const $ = JD.jQuery;
    let isValid = true;
    
    //console.log('Validating step:', currentStep);
    
    // Clear previous step errors
    $(`#step-${currentStep} .step-error`).remove();
    
    // Get current step fields that need validation
    const currentStepElement = $(`#step-${currentStep}`);
    const fieldsToValidate = currentStepElement.find('.validate');
    
    // Validate each field in current step
    fieldsToValidate.each(function() {
        const field = $(this);
        const fieldValid = $("#donation-form").validationEngine('validateField', field);
        
        if (!fieldValid) {
            isValid = false;
            //console.log('Field validation failed:', field.attr('name') || field.attr('id'));
        }
    });
    
    // Custom validation for specific steps
    switch(currentStep) {
        case 1:
            isValid = validateStep1() && isValid;
            break;
        case 2:
            isValid = validateStep2() && isValid;
            break;
        case 3:
            isValid = validateStep3() && isValid;
            break;
        case 4:
            isValid = validateStep4() && isValid;
            break;
    }
    
    //console.log('Step validation result:', isValid);
    return isValid;
}

// Validate step 1 (Amount)
function validateStep1() {
    const $ = JD.jQuery;
    let isValid = true;
    let errors = [];

    // Clear previous errors
    $('#step1-global-error').hide().html('');
    $('.jd-custom-error').remove();

    // 1. Check frequency selection
    const frequencyOptions = $('input[name="r_frequency"]');
    if (frequencyOptions.length > 0) {
        const selectedFrequency = $('input[name="r_frequency"]:checked').val();
        if (!selectedFrequency) {
            errors.push(getJoomlaText('JD_PLEASE_SELECT_A_DONATION_FREQUENCY'));
            isValid = false;
        }
    }

    // 2. Check amount selection
    const predefinedAmounts = $('input[name="rd_amount"]:not([value="custom"])');
    const customAmountField = $('#custom-amount');
    const customAmountValue = parseFloat(customAmountField.val());

    function getMinAmount() {
        if (typeof DONATION_CONFIG !== 'undefined' && DONATION_CONFIG.MIN_AMOUNT > 0) {
            return DONATION_CONFIG.MIN_AMOUNT;
        }
        return 1;
    }
    function getMaxAmount() {
        if (typeof DONATION_CONFIG !== 'undefined' && DONATION_CONFIG.MAX_AMOUNT > 0) {
            return DONATION_CONFIG.MAX_AMOUNT;
        }
        return 10000;
    }

    if (predefinedAmounts.length > 0) {
        const selectedAmount = $('input[name="rd_amount"]:checked').val();

        if (!selectedAmount && (!customAmountValue || isNaN(customAmountValue))) {
            errors.push(amountRequired);
            isValid = false;
        }
        if (selectedAmount === 'custom') {
            if (!customAmountValue || isNaN(customAmountValue)) {
                errors.push(getJoomlaText('JD_PLEASE_ENTER_A_VALID_CUSTOM_AMOUNT'));
                isValid = false;
            } else {
                const minAmount = getMinAmount();
                const maxAmount = getMaxAmount();
                if (customAmountValue < minAmount) {
                    errors.push(JD_LANG.MIN_DONATION_AMOUNT);
                    isValid = false;
                } else if (customAmountValue > maxAmount) {
                    errors.push(JD_LANG.MAX_DONATION_AMOUNT);
                    isValid = false;
                }
            }
        }
    } else {
        if (!customAmountValue || isNaN(customAmountValue)) {
            errors.push(getJoomlaText('JD_PLEASE_ENTER_VALID_DONATION_AMOUNT'));
            isValid = false;
        } else {
            const minAmount = getMinAmount();
            const maxAmount = getMaxAmount();
            if (customAmountValue < minAmount) {
                errors.push(JD_LANG.MIN_DONATION_AMOUNT);
                isValid = false;
            } else if (customAmountValue > maxAmount) {
                errors.push(JD_LANG.MAX_DONATION_AMOUNT);
                isValid = false;
            }
        }
        const customAmountOption = $('input[name="rd_amount"][value="custom"]');
        if (customAmountOption.length > 0 && customAmountValue && !isNaN(customAmountValue)) {
            customAmountOption.prop('checked', true);
        }
    }

    // Show errors if any
    if (errors.length > 0) {
        $('#step1-global-error').html(errors.join('<br>')).show();
    }

    return isValid;
}




function validateStep2() {
    const $ = JD.jQuery;
    const step2Container = $('#step-2');
    
    if (step2Container.length === 0) {
        return true;
    }
    
    //console.log('=== STARTING STEP 2 VALIDATION ===');
    
    let overallValid = true;
    const validationResults = [];
    
    // Get all fields to validate
    const $fieldsToValidate = step2Container.find('input, select, textarea').filter(function() {
        const classAttr = $(this).attr('class') || '';
        return classAttr.match(/validate\[/);
    });
    
    //console.log('Found', $fieldsToValidate.length, 'fields to validate');
    
    // First pass: Clean up only valid fields
    $fieldsToValidate.each(function() {
        const $field = $(this);
        smartErrorCleanup($field); // Only cleans if field is valid
    });
    
    // Second pass: Validate and show errors for invalid fields
    $fieldsToValidate.each(function() {
        const $field = $(this);
        const fieldId = $field.attr('id') || $field.attr('name') || 'unnamed';
        
        //console.log('Validating field:', fieldId, 'Value:', $field.val());
        
        // Get validation result
        const fieldResult = validateSingleField($field);
        
        // Store result
        validationResults.push({
            fieldId: fieldId,
            field: $field,
            isValid: fieldResult.isValid,
            message: fieldResult.message,
            value: $field.val()
        });
        
        //console.log('Field', fieldId, ':', fieldResult.isValid ? 'VALID' : 'INVALID', 'Message:', fieldResult.message);
        
        if (!fieldResult.isValid) {
            overallValid = false;
            
            // Show error for invalid field
            if (fieldResult.message && fieldResult.message.trim() !== '') {
                ///console.log('Showing error for invalid field:', fieldId, fieldResult.message);
                
                // Remove any existing custom error first
                $('#error_' + fieldId).remove();
                
                // Show new error
                showCustomError($field, fieldResult.message);
            }
        } else {
            //console.log('Field is valid, ensuring clean state:', fieldId);
            // Ensure no errors for valid field
            $('#error_' + fieldId).remove();
            $field.removeClass('validation-error-field error invalid');
        }
    });

    
    return overallValid;
}


function smartErrorCleanup($field) {
    const $ = JD.jQuery;
    const fieldId = $field.attr('id') || $field.attr('name') || '';
    
    //console.log('Smart cleanup for field:', fieldId);
    
    // 1. Only remove ValidationEngine errors if field is actually valid
    const quickValidation = validateSingleField($field);
    
    if (quickValidation.isValid) {
        //console.log('Field is valid, removing errors:', fieldId);
        
        // Remove ValidationEngine errors
        try {
            $field.validationEngine('hide');
        } catch (e) {}
        
        // Remove specific ValidationEngine error elements for this field
        $('.formError').each(function() {
            const $error = $(this);
            const errorField = $error.attr('data-field') || '';
            const $prevField = $error.prev('input');
            
            if (errorField === fieldId || 
                $prevField.attr('id') === fieldId ||
                $error.closest('.smart-form-group').find('#' + fieldId).length > 0) {
                //console.log('Removing VE error for valid field:', $error.text());
                $error.remove();
            }
        });

        clearCustomError($field);
        
        // Remove custom errors for valid field
        //$('#error_' + fieldId + ', [id^="error_' + fieldId + '"]').remove();
        
        // Remove error classes
        //$field.removeClass('validation-error-field error invalid validationError');
        //$field.closest('.smart-form-group, .field-container').removeClass('has-error error invalid');
    } else {
        //console.log('Field is invalid, keeping existing errors:', fieldId);
        // Don't remove errors for invalid fields - let validation handle them
    }
}


function validateSingleField($field) {
    const $ = JD.jQuery;
    const allRules = $.validationEngineLanguage.allRules;
    const classAttr = $field.attr('class');
    const validationMatch = classAttr ? classAttr.match(/validate\[(.*?)\]/) : null;

    if (!validationMatch || !allRules) {
        return { isValid: true, message: '' };
    }

    const ruleString = validationMatch[1];
    const rules = ruleString.match(/([a-zA-Z0-9_]+(\[[^\]]+\])?)/g);
    
    // Xác định loại field và lấy giá trị phù hợp
    const fieldType = $field.attr('type');
    const isCheckbox = fieldType === 'checkbox';
    const isRadio = fieldType === 'radio';
    
    let fieldValue;
    if (isCheckbox || isRadio) {
        fieldValue = $field.is(':checked');
    } else {
        fieldValue = $field.val();
    }

    for (let rule of rules) {
        const trimmedRule = rule.trim();
        let ruleName = trimmedRule.split('[')[0].trim();
        let ruleConfig = allRules[ruleName];

        // Nếu là custom[...] thì lấy rule bên trong
        if (ruleName === 'custom') {
            const match = trimmedRule.match(/custom\[(.*?)\]/);
            if (match && match[1]) {
                const customRuleName = match[1].split(',')[0].trim();
                ruleConfig = allRules[customRuleName];
                ruleName = customRuleName;
            }
        }

        if (!ruleConfig) continue;

        // Required validation - xử lý khác nhau cho checkbox/radio và input text
        if (ruleName === 'required') {
            let isEmpty = false;
            
            if (isCheckbox || isRadio) {
                // Với checkbox/radio, required nghĩa là phải được check
                isEmpty = !fieldValue; // fieldValue là boolean từ is(':checked')
            } else {
                // Với input text, required nghĩa là không được rỗng
                isEmpty = !fieldValue || fieldValue.trim() === '';
            }
            
            if (isEmpty) {
                return { 
                    isValid: false, 
                    message: ruleConfig.alertText 
                };
            }
        }

        // Skip other validations if field is empty (unless required)
        // Với checkbox/radio, chỉ skip nếu không được check
        if (isCheckbox || isRadio) {
            if (!fieldValue && ruleName !== 'required') {
                continue;
            }
        } else {
            if ((!fieldValue || fieldValue.trim() === '') && ruleName !== 'required') {
                continue;
            }
        }

        // Các validation khác chỉ áp dụng cho text input, không áp dụng cho checkbox/radio
        if (isCheckbox || isRadio) {
            // Với checkbox/radio, chỉ cần validate required
            // Nếu có rule khác, có thể thêm logic xử lý ở đây
            continue;
        }

        // onlyNumberSp validation
        if (ruleName === 'onlyNumberSp') {
            const regex = /^[0-9\ ]+$/;
            if (!regex.test(fieldValue)) {
                return { 
                    isValid: false, 
                    message: ruleConfig.alertText 
                };
            }
        }

        // creditCard validation (Luhn algorithm)
        if (ruleName === 'creditCard') {
            if (!isValidCreditCard(fieldValue)) {
                return { 
                    isValid: false, 
                    message: ruleConfig.alertText 
                };
            }
        }

        // Regex-based validations
        if (ruleConfig.regex && ruleConfig.regex !== 'none' && ruleName !== 'onlyNumberSp') {
            let regex = ruleConfig.regex;
            if (typeof regex === 'string') regex = new RegExp(regex);
            if (!regex.test(fieldValue)) {
                return { 
                    isValid: false, 
                    message: ruleConfig.alertText 
                };
            }
        }

        // Function-based validations (như date)
        if (ruleConfig.func && typeof ruleConfig.func === 'function') {
            if (!ruleConfig.func($field)) {
                return { 
                    isValid: false, 
                    message: ruleConfig.alertText 
                };
            }
        }

        // Size validations
        if (ruleName === 'minSize') {
            const minLength = parseInt(trimmedRule.match(/\[(\d+)\]/)?.[1] || 0);
            if (fieldValue.length < minLength) {
                return { 
                    isValid: false, 
                    message: ruleConfig.alertText + ' ' + minLength + ' ' + ruleConfig.alertText2 
                };
            }
        }

        if (ruleName === 'maxSize') {
            const maxLength = parseInt(trimmedRule.match(/\[(\d+)\]/)?.[1] || 0);
            if (fieldValue.length > maxLength) {
                return { 
                    isValid: false, 
                    message: ruleConfig.alertText + ' ' + maxLength + ' ' + ruleConfig.alertText2 
                };
            }
        }

        // Equals validation
        if (ruleName === 'equals') {
            const targetField = trimmedRule.match(/\[#?(.*?)\]/)[1];
            const $targetField = targetField.startsWith('#') ? $(targetField) : $('#' + targetField);
            const targetValue = $targetField.val();

            if (fieldValue !== targetValue) {
                return { 
                    isValid: false, 
                    message: ruleConfig.alertText 
                };
            }
        }
    }

    return { isValid: true, message: '' };
}


// Hàm kiểm tra số thẻ tín dụng theo thuật toán Luhn
function isValidCreditCard(number) {
    if (!number) return false;
    // Xóa dấu cách
    number = number.replace(/\D/g, '');
    let sum = 0, shouldDouble = false;
    for (let i = number.length - 1; i >= 0; i--) {
        let digit = parseInt(number.charAt(i));
        if (shouldDouble) {
            digit *= 2;
            if (digit > 9) digit -= 9;
        }
        sum += digit;
        shouldDouble = !shouldDouble;
    }
    return (sum % 10) === 0;
}



// Validate step 3 (Payment)
function validateStep3() {
    const $ = JD.jQuery;
    const step3Container = $('#step-3');
    
    if (step3Container.length === 0) {
        return true;
    }
    
    //console.log('=== STARTING STEP 3 VALIDATION ===');
    
    let overallValid = true;
    const validationResults = [];

    const paymentMethod = $('input[name="payment_method"]:checked').val();
    if (!paymentMethod) {
        showCustomError('#payment_methods',getJoomlaText('JD_SELECT_PAYMENT_OPTION'));
        overallValid = false;
    }
    
    if ($('#creditcarddivmain').is(':visible')) {
        // Get all fields to validate
        const $fieldsToValidate = step3Container.find('input').filter(function() {
            const classAttr = $(this).attr('class') || '';
            return classAttr.match(/validate\[/);
        });
        
        
        // First pass: Clean up only valid fields
        $fieldsToValidate.each(function() {
            const $field = $(this);
            smartErrorCleanup($field); // Only cleans if field is valid
        });
        
        // Second pass: Validate and show errors for invalid fields
        $fieldsToValidate.each(function() {
            const $field = $(this);
            const fieldId = $field.attr('id') || $field.attr('name') || 'unnamed';
            
           //console.log('Validating field:', fieldId, 'Value:', $field.val());
            
            // Get validation result
            const fieldResult = validateSingleField($field);
            
            // Store result
            validationResults.push({
                fieldId: fieldId,
                field: $field,
                isValid: fieldResult.isValid,
                message: fieldResult.message,
                value: $field.val()
            });
            
            
            if (!fieldResult.isValid) {
                overallValid = false;
                
                // Show error for invalid field
                if (fieldResult.message && fieldResult.message.trim() !== '') {
                    
                    // Remove any existing custom error first
                    $('#error_' + fieldId).remove();
                    
                    // Show new error
                    if (fieldId === 'x_card_num') {
                        showCustomError($field, fieldResult.message, {
                            position: 'top'
                        });
                    } else {
                        // Show normal error for other fields
                        showCustomError($field, fieldResult.message);
                    }
                }
            } else {
                //console.log('Field is valid, ensuring clean state:', fieldId);
                // Ensure no errors for valid field
                $('#error_' + fieldId).remove();
                $field.removeClass('validation-error-field error invalid');
            }
        });
    }
    
    return overallValid;
}

function validateStep4() {
    const $ = JD.jQuery;
    const step4Container = $('#step-4');
    
    if (step4Container.length === 0) {
        return true;
    }
    
    //console.log('=== STARTING STEP 4 VALIDATION ===');
    
    let overallValid = true;
    const validationResults = [];
    
    
    // Get all fields to validate
    const $fieldsToValidate = step4Container.find('input[type="checkbox"]').filter(function() {
        const classAttr = $(this).attr('class') || '';
        return classAttr.match(/validate\[/);
    });
    
    // First pass: Clean up only valid fields
    $fieldsToValidate.each(function() {
        const $field = $(this);
        smartErrorCleanup($field); // Only cleans if field is valid
    });
    
    // Second pass: Validate and show errors for invalid fields
    $fieldsToValidate.each(function() {
        const $field = $(this);
        //console.log('Validating field:', $field.attr('id'), 'Value:', $field.is(':checked'));
        const fieldId = $field.attr('id') || $field.attr('name') || 'unnamed';
        
        //console.log('Validating field:', fieldId, 'Value:', $field.val());
        
        // Get validation result
        const fieldResult = validateSingleField($field);
        
        // Store result
        validationResults.push({
            fieldId: fieldId,
            field: $field,
            isValid: fieldResult.isValid,
            message: fieldResult.message,
            value: $field.val()
        });
        
        //console.log('Field', fieldId, ':', fieldResult.isValid ? 'VALID' : 'INVALID', 'Message:', fieldResult.message);
        
        if (!fieldResult.isValid) {
            overallValid = false;
            
            // Show error for invalid field
            if (fieldResult.message && fieldResult.message.trim() !== '') {
                //console.log('Showing error for invalid field:', fieldId, fieldResult.message);
                
                // Remove any existing custom error first
                $('#error_' + fieldId).remove();
                
                // Show error for checkbox - display below label
                showCheckboxError($field, fieldResult.message);
            }
        } else {
            //console.log('Field is valid, ensuring clean state:', fieldId);
            // Ensure no errors for valid field
            $('#error_' + fieldId).remove();
            $field.removeClass('validation-error-field error invalid');
        }
    });

    return overallValid;
}

// Function để hiển thị lỗi cho checkbox bên dưới label
function showCheckboxError($field, message) {
    const $ = JD.jQuery;
    const fieldId = $field.attr('id') || $field.attr('name') || 'unnamed';
    const errorId = 'error_' + fieldId;
    
    // Remove existing error
    $('#' + errorId).remove();
    
    // Add error class to field
    $field.addClass('validation-error-field error invalid');
    
    // Find the label associated with this checkbox
    let $label = null;
    
    // Method 1: Find label with for attribute
    if ($field.attr('id')) {
        $label = $('label[for="' + $field.attr('id') + '"]');
    }
    
    // Method 2: Find label that contains this checkbox
    if (!$label || $label.length === 0) {
        $label = $field.closest('label');
    }
    
    // Method 3: Find next sibling label
    if (!$label || $label.length === 0) {
        $label = $field.next('label');
    }
    
    // Method 4: Find previous sibling label
    if (!$label || $label.length === 0) {
        $label = $field.prev('label');
    }
    
    // Method 5: Find label in same parent container
    if (!$label || $label.length === 0) {
        $label = $field.parent().find('label').first();
    }
    
    // Create error element
    const $errorElement = $('<div>', {
        id: errorId,
        class: 'validation-error-message checkbox-error',
        html: message,
        css: {
            color: '#ff0000',
            fontSize: '12px',
            marginTop: '5px',
            display: 'block',
            fontWeight: 'normal'
        }
    });
    
    // Insert error message
    if ($label && $label.length > 0) {
        // Insert after the label
        $label.after($errorElement);
    } else {
        // Fallback: insert after the checkbox
        $field.after($errorElement);
    }
    
    // Optional: Add some styling to make it more visible
    $errorElement.hide().fadeIn(300);
}

// Validate multiple steps up to a certain step
function validateStepsUpTo(stepNumber) {
    const $ = JD.jQuery;
    
    for (let i = 1; i <= stepNumber; i++) {
        const stepElement = $(`#step-${i}`);
        const fieldsToValidate = stepElement.find('.validate');
        
        let stepValid = true;
        fieldsToValidate.each(function() {
            const field = $(this);
            const fieldValid = $("#donation-form").validationEngine('validateField', field, true);
            if (!fieldValid) {
                stepValid = false;
            }
        });
        
        if (!stepValid) {
            //console.log('Step validation failed for step:', i);
            return false;
        }
    }
    
    return true;
}

// Show step error message
function showStepError(message) {
    const $ = JD.jQuery;
    const currentStepElement = $(`#step-${currentStep}`);
    
    // Remove existing error
    currentStepElement.find('.step-error').remove();
    
    // Add new error
    const errorHtml = `<div class="step-error alert alert-danger">${message}</div>`;
    currentStepElement.find('.step-content').prepend(errorHtml);
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        currentStepElement.find('.step-error').fadeOut(500, function() {
            $(this).remove();
        });
    }, 5000);
    
    // Scroll to error
    currentStepElement.find('.step-error')[0].scrollIntoView({ 
        behavior: 'smooth', 
        block: 'center' 
    });
}

function setupAmountSelection() {
    const $ = JD.jQuery;
    
    // Handle amount option clicks
    $(document).on('click', '.amount-option', function() {
        const radio = $(this).find('input[type="radio"]');
        radio.prop('checked', true);
        
        // Clear validation errors
        $("#donation-form").validationEngine('hideAll');
        $('.jd-custom-error').remove();
        
        // If selecting a pre-defined amount, clear custom amount field
        if (radio.val() !== 'custom') {
            $('#custom-amount').val('');
        }
        
        // Show/hide custom amount input
        if (radio.val() === 'custom') {
            $('#custom-amount').focus();
        }
    });
    
    // Handle custom amount input
    $(document).on('input', '#custom-amount', function() {
        // Format number input
        let value = $(this).val().replace(/[^0-9.]/g, '');
        if (value.split('.').length > 2) {
            value = value.substring(0, value.lastIndexOf('.'));
        }
        $(this).val(value);
        
        // Clear errors
        $(this).removeClass('input-error');
        $(this).parent().find('.jd-custom-error').remove();
        
        // If user enters a value in custom amount
        if (value && parseFloat(value) > 0) {
            // Uncheck all pre-defined amounts
            $('input[name="rd_amount"]:not([value="custom"])').prop('checked', false);
            
            // Auto-select custom amount option if it exists
            const customAmountOption = $('input[name="rd_amount"][value="custom"]');
            if (customAmountOption.length > 0) {
                customAmountOption.prop('checked', true);
            }
        } else {
            // If custom amount is empty, uncheck custom option
            const customAmountOption = $('input[name="rd_amount"][value="custom"]');
            if (customAmountOption.length > 0) {
                customAmountOption.prop('checked', false);
            }
        }
    });
    
    // Handle custom amount focus (when clicking directly on input)
    $(document).on('focus', '#custom-amount', function() {
        // Only auto-select custom option if there's no value yet
        if (!$(this).val()) {
            // Uncheck all pre-defined amounts when focusing on empty custom amount
            $('input[name="rd_amount"]:not([value="custom"])').prop('checked', false);
            
            // Auto-select custom amount option if it exists
            const customAmountOption = $('input[name="rd_amount"][value="custom"]');
            if (customAmountOption.length > 0) {
                customAmountOption.prop('checked', true);
            }
        }
        
        // Clear validation errors
        $('.jd-custom-error').remove();
    });
    
    // Handle frequency change
    $(document).on('change', 'input[name="r_frequency"]', function() {
        $("#donation-form").validationEngine('hideAll');
        $('.jd-custom-error').remove();
    });
    
    // Handle pre-defined amount selection
    $(document).on('change', 'input[name="rd_amount"]:not([value="custom"])', function() {
        if ($(this).is(':checked')) {
            // Clear custom amount when selecting pre-defined amount
            $('#custom-amount').val('');
            $('.jd-custom-error').remove();
        }
    });
}

// Setup form validation
function setupFormValidation() {
    const $ = JD.jQuery;
    
    // Remove validation classes from credit card fields initially
    $('#card-number, #expiry, #cvv, #cardholder-name').removeClass('validate[required,custom[creditCard]] validate[required] validate[required,minSize[3],maxSize[4],custom[onlyNumberSp]] validate[required,minSize[2],custom[onlyLetterSp]]');
}

// Setup terms modal
function setupTermsModal() {
    const $ = JD.jQuery;
    
    // Open terms modal
    $(document).on('click', '#terms-link', function(e) {
        e.preventDefault();
        $('#terms-modal').addClass('active');
        $('body').addClass('modal-open');
    });
    
    // Close terms modal
    $(document).on('click', '.close-modal', function() {
        $('#terms-modal').removeClass('active');
        $('body').removeClass('modal-open');
    });
    
    // Close modal when clicking outside
    $(document).on('click', '#terms-modal', function(e) {
        if (e.target === this) {
            $(this).removeClass('active');
            $('body').removeClass('modal-open');
        }
    });
}

// Show specific step
function showStep(step) {
    const $ = JD.jQuery;
    
    // Hide all steps
    $('.step').removeClass('active');
    
    // Show current step
    $(`#step-${step}`).addClass('active');
    
    // Update button states
    $('.btn-prev').toggle(step > 1);
    $('.btn-next').toggle(step < totalSteps);
    $('.btn-submit').toggle(step === totalSteps);
    
    // Scroll to top of form
    $('.donation-form-container')[0].scrollIntoView({ 
        behavior: 'smooth', 
        block: 'start' 
    });
}

// Update progress bar
function updateProgressBar() {
    const $ = JD.jQuery;
    
    $('.smart-progress-step').each(function(index) {
        const stepNum = index + 1;
        const $step = $(this);
        
        $step.removeClass('active completed clickable');
        
        if (stepNum < currentStep) {
            $step.addClass('completed clickable');
        } else if (stepNum === currentStep) {
            $step.addClass('active');
        } else if (validateStepsUpTo(stepNum - 1)) {
            $step.addClass('clickable');
        }
    });
    
    // Update progress lines
    $('.smart-progress-line').each(function(index) {
        const $line = $(this);
        if (index < currentStep - 1) {
            $line.addClass('active');
        } else {
            $line.removeClass('active');
        }
    });
}

// Update summary on step 4
function updateDonationSummary() {
    const $ = JD.jQuery;
    // Get form values
    const frequency     = $('input[name="r_frequency"]:checked').val();
    if (frequency != "one-time") {
        $('#donation_type').val('recurring');
    }
    const amount        = $('input[name="rd_amount"]:checked').val();
    const customAmount  = $('#custom-amount').val();
    const paymentMethod = $('input[name="payment_method"]:checked').val();
    const firstName     = $('#first_name').val();
    const lastName      = $('#last_name').val();
    const email         = $('#email').val();
    const anonymous     = $('#hide_me').is(':checked');

    // Update summary values
    $('#summary-frequency').text(getFrequencyText(frequency));

    const donationAmount = amount === 'custom' ? parseFloat(customAmount) : parseFloat(amount);

    // Sử dụng currency_symbol thay cho $
    $('#summary-amount').text(`${currency_symbol}${donationAmount.toFixed(2)}`);
    $('#summary-total').text(`${currency_symbol}${donationAmount.toFixed(2)}`);

    $('#summary-payment').text(getPaymentMethodText(paymentMethod));

    if (anonymous) {   
        $('#summary-donor').text(getJoomlaText('JD_ANONYMOUS'));
    } else {
        $('#summary-donor').text(`${firstName} ${lastName}`);
    }
    $('#summary-email').text(email);
}


function showCustomError(element, message, options = {}) {
    const $ = JD.jQuery;
    
    // Default options
    const defaults = {
        position: 'bottom', // 'top', 'bottom', 'left', 'right'
        className: 'jd-custom-error',
        removeExisting: true,
        fadeIn: true,
        autoHide: false,
        autoHideDelay: 5000,
        showIcon: true
    };
    
    const settings = $.extend({}, defaults, options);
    
    // Check if element is checkbox or radio
    const $element = $(element);
    const isCheckbox = $element.attr('type') === 'checkbox';
    const isRadio = $element.attr('type') === 'radio';
    
    // For checkbox/radio, find the target div for error message placement
    let $targetElement = $element;
    if (isCheckbox || isRadio) {
        const elementId = $element.attr('id');
        if (elementId) {
            const $targetDiv = $('#div_' + elementId);
            if ($targetDiv.length > 0) {
                $targetElement = $targetDiv;
            } else {
                // Fallback: use parent container or closest form group
                $targetElement = $element.closest('.form-group, .field-group, .checkbox-group, .radio-group');
                if ($targetElement.length === 0) {
                    $targetElement = $element.parent();
                }
            }
        }
    }
    
    // Remove existing errors if specified
    if (settings.removeExisting) {
        // Remove error class only for non-checkbox/radio elements
        if (!isCheckbox && !isRadio) {
            $element.removeClass('input-error');
        }
        
        // Remove existing error messages
        if (isCheckbox || isRadio) {
            // For checkbox/radio, remove error messages from target element
            $targetElement.find('.' + settings.className).remove();
            $targetElement.siblings('.' + settings.className).remove();
            
            // Also check the original element's siblings
            $element.siblings('.' + settings.className).remove();
            $element.parent().find('.' + settings.className).remove();
        } else {
            // For regular inputs
            $element.parent().find('.' + settings.className).remove();
            $element.siblings('.' + settings.className).remove();
        }
    }
    
    // Add error class only to regular input elements (not checkbox/radio)
    if (!isCheckbox && !isRadio) {
        $element.addClass('input-error');
    }
    
    // Create error message element
    const errorHtml = `
        <div class="${settings.className}" style="display: none;">
            ${settings.showIcon ? '<i class="error-icon">⚠</i>' : ''}
            <span class="error-text">${message}</span>
        </div>
    `;
    
    let $errorElement = $(errorHtml);
    
    // Position the error message
    if (isCheckbox || isRadio) {
        // For checkbox/radio, place error message in target element
        switch (settings.position) {
            case 'top':
                $targetElement.prepend($errorElement);
                break;
            case 'bottom':
            default:
                $targetElement.append($errorElement);
                break;
        }
    } else {
        // For regular inputs, use original positioning logic
        switch (settings.position) {
            case 'top':
                $element.before($errorElement);
                break;
            case 'left':
                $element.before($errorElement);
                $errorElement.css('display', 'inline-block');
                break;
            case 'right':
                $element.after($errorElement);
                $errorElement.css('display', 'inline-block');
                break;
            case 'bottom':
            default:
                $element.after($errorElement);
                break;
        }
    }
    
    // Show error with animation
    if (settings.fadeIn) {
        $errorElement.fadeIn(300);
    } else {
        $errorElement.show();
    }
    
    // Auto hide if specified
    if (settings.autoHide) {
        setTimeout(() => {
            $errorElement.fadeOut(300, function() {
                $(this).remove();
                // Only remove error class from regular input elements
                if (!isCheckbox && !isRadio) {
                    $element.removeClass('input-error');
                }
            });
        }, settings.autoHideDelay);
    }
    
    // Return error element for further manipulation
    return $errorElement;
}

// Cập nhật helper function để clear errors
function clearFieldCustomError($field) {
    const $ = JD.jQuery;
    const isCheckbox = $field.attr('type') === 'checkbox';
    const isRadio = $field.attr('type') === 'radio';
    
    // Remove error class only from regular input fields (not checkbox/radio)
    if (!isCheckbox && !isRadio) {
        $field.removeClass('input-error');
    }
    
    if (isCheckbox || isRadio) {
        const fieldId = $field.attr('id');
        if (fieldId) {
            const $targetDiv = $('#div_' + fieldId);
            if ($targetDiv.length > 0) {
                // Remove error messages from target div
                $targetDiv.find('.jd-custom-error, .custom-error').fadeOut(200, function() {
                    $(this).remove();
                });
            }
        }
        
        // Also clear from parent containers
        $field.closest('.form-group, .field-group, .checkbox-group, .radio-group')
              .find('.jd-custom-error, .custom-error').fadeOut(200, function() {
                  $(this).remove();
              });
    }
    
    // Clear from siblings and parent (for all field types)
    $field.siblings('.jd-custom-error, .custom-error').fadeOut(200, function() {
        $(this).remove();
    });
    $field.parent().find('.jd-custom-error, .custom-error').fadeOut(200, function() {
        $(this).remove();
    });
}



function getValidationEngineErrorMessage($field) {
    const $ = JD.jQuery;
    
    // Truy cập allRules đã được định nghĩa từ PHP
    const allRules = $.validationEngineLanguage.allRules;
    
    if (!allRules) {
        return 'Please check your input';
    }
    
    const classAttr = $field.attr('class');
    if (!classAttr) {
        return '';
    }

    const validationMatch = classAttr.match(/validate\[([^]*)\]/);
    
    if (!validationMatch) return '';
    
    const rules = validationMatch ? validationMatch[1].split(',').map(rule => rule.trim()) : [];
    const fieldValue = $field.val();
    
    for (let rule of rules) {
        const trimmedRule = rule.trim();
        const ruleName = trimmedRule.split('[')[0];
        
        if (allRules[ruleName]) {
            const ruleConfig = allRules[ruleName];
            
            // Kiểm tra rule cụ thể
            if (ruleName === 'required' && (!fieldValue || fieldValue.trim() === '')) {
                return ruleConfig.alertText;
            }
            
            // Thêm xử lý cho custom[number]
            if (trimmedRule === 'custom[number' && fieldValue) {
                // Regex kiểm tra số (cả số nguyên và thập phân)
                const numberRegex = /^[-+]?\d*\.?\d+$/;
                if (!numberRegex.test(fieldValue)) {
                    return ruleConfig.alertText || 'Please enter a valid number';
                }
                
                // Kiểm tra nếu giá trị là số hợp lệ nhưng có chữ số 0 ở đầu (trừ số 0 và số thập phân)
                if (/^0\d+$/.test(fieldValue) && !/^0\.\d+$/.test(fieldValue)) {
                    return 'Number cannot start with zero';
                }
            }
            
            if (ruleName === 'email' && fieldValue && !ruleConfig.regex.test(fieldValue)) {
                return ruleConfig.alertText;
            }
            
            if (ruleName === 'number' && fieldValue && !ruleConfig.regex.test(fieldValue)) {
                return ruleConfig.alertText;
            }
            
            if (ruleName === 'integer' && fieldValue && !ruleConfig.regex.test(fieldValue)) {
                return ruleConfig.alertText;
            }
            
            if (ruleName === 'phone' && fieldValue && !ruleConfig.regex.test(fieldValue)) {
                return ruleConfig.alertText;
            }
            
            if (ruleName === 'url' && fieldValue && !ruleConfig.regex.test(fieldValue)) {
                return ruleConfig.alertText;
            }
            
            // Handle minSize rule
            if (trimmedRule.startsWith('minSize[')) {
                const minLength = parseInt(trimmedRule.match(/\[(\d+)\]/)[1]);
                if (fieldValue && fieldValue.length < minLength) {
                    const msg = $field.data('errormessage-range-underflow');
                    return msg ? msg : (ruleConfig.alertText + ' ' + minLength + (ruleConfig.alertText2 ? ' ' + ruleConfig.alertText2 : ''));
                }
            }
            
            // Handle maxSize rule
            if (trimmedRule.startsWith('maxSize[')) {
                const maxLength = parseInt(trimmedRule.match(/\[(\d+)\]/)[1]);
                if (fieldValue && fieldValue.length > maxLength) {
                    const msg = $field.data('errormessage-range-overflow');
                    return msg ? msg : (ruleConfig.alertText + ' ' + maxLength + (ruleConfig.alertText2 ? ' ' + ruleConfig.alertText2 : ''));
                }
            }
            
            // Handle equals rule
            if (trimmedRule.startsWith('equals[')) {
                const targetField = trimmedRule.match(/\[(.*?)\]/)[1];
                const targetValue = $('#' + targetField).val();
                if (fieldValue !== targetValue) {
                    const msg = $field.data('errormessage-equals');
                    return msg ? msg : ruleConfig.alertText;
                }
            }
            if (trimmedRule.startsWith('min[')) {
                
                const minValue = parseFloat(trimmedRule.match(/\[(.*?)\]/)[1]);
                if (fieldValue && parseFloat(fieldValue) < minValue) {
                    const msg = $field.data('errormessage-range-underflow');
                    //console.log(msg);
                    return msg ? msg : ruleConfig.alertText;
                }
            }
            // max[max]
            if (trimmedRule.startsWith('max[')) {
                const maxValue = parseFloat(trimmedRule.match(/\[(.*?)\]/)[1]);
                if (fieldValue && parseFloat(fieldValue) > maxValue) {
                    const msg = $field.data('errormessage-range-overflow');
                    return msg ? msg : ruleConfig.alertText;
                }
            }
        }
    }
    
    return '';
}

// Helper function to clear custom errors
function clearCustomError(element, className = 'jd-custom-error') {
    const $ = JD.jQuery;
    const $element = $(element);
    const isCheckbox = $element.attr('type') === 'checkbox';
    const isRadio = $element.attr('type') === 'radio';
    
    // Remove error class only from regular input fields (not checkbox/radio)
    if (!isCheckbox && !isRadio) {
        $element.removeClass('input-error');
    }
    
    // Remove both custom-error and jd-custom-error classes
    const errorClasses = [className, 'jd-custom-error', 'custom-error'];
    
    errorClasses.forEach(cls => {
        // Remove from parent
        $element.parent().find('.' + cls).remove();
        // Remove from siblings
        $element.siblings('.' + cls).remove();
    });
    
    // For checkbox/radio, also check target div
    if (isCheckbox || isRadio) {
        const elementId = $element.attr('id');
        if (elementId) {
            const $targetDiv = $('#div_' + elementId);
            if ($targetDiv.length > 0) {
                errorClasses.forEach(cls => {
                    $targetDiv.find('.' + cls).remove();
                });
            }
        }
        
        // Also check closest form groups
        const $formGroup = $element.closest('.form-group, .field-group, .checkbox-group, .radio-group');
        if ($formGroup.length > 0) {
            errorClasses.forEach(cls => {
                $formGroup.find('.' + cls).remove();
            });
        }
    }
}

// Helper function to clear all custom errors in a container
function clearAllCustomErrors(container = document, className = 'jd-custom-error') {
    const $ = JD.jQuery;
    const $container = $(container);
    
    // Remove input-error class only from regular input fields (not checkbox/radio)
    $container.find('input:not([type="checkbox"]):not([type="radio"]), textarea, select')
              .removeClass('input-error');
    
    // Remove both custom-error and jd-custom-error classes
    const errorClasses = [className, 'jd-custom-error', 'custom-error'];
    
    errorClasses.forEach(cls => {
        $container.find('.' + cls).remove();
    });
}



// Get frequency text
function getFrequencyText(frequency) {
    switch(frequency) {
        case 'one-time': return getJoomlaText('JD_ONE_TIME');
        case 'd': return getJoomlaText('JD_DAILY');
        case 'w': return getJoomlaText('JD_WEEKLY');
        case 'm': return getJoomlaText('JD_MONTHLY');
        case 'b': return getJoomlaText('JD_BI_WEEKLY');
        case 'q': return getJoomlaText('JD_QUARTERLY');
        case 's': return getJoomlaText('JD_SEMI_ANNUALLY');
        case 'a': return getJoomlaText('JD_ANNUALLY');
        default: return getJoomlaText('JD_ONE_TIME');
    }
}

// Get payment method text
function getPaymentMethodText(methodName) {
    var method = paymentMethods.find(function(method) {
        return method.name === methodName;
    });
    return method ? method.title : null; // Trả về title hoặc null nếu không tìm thấy
}


// Submit form
function submitForm() {
    const $ = JD.jQuery;
    
    // Additional step 4 validation
    if (!validateCurrentStep()) {
        //console.log('Step 4 validation failed');
        return;
    }
    
    // Show loading
    showLoading();
    
    return processFormSubmission();
}

function processFormSubmission() {
    const $ = JD.jQuery;
    const form = $("#os_form");
    
    // Prevent default form submission
    form.off('submit').on('submit', function(e) {
        e.preventDefault();
    });
    
    // Disable submit button to prevent double submission
    form.find('.btn-submit').prop('disabled', true);
    
    // Get payment method
    let paymentMethod;
    if ($('input:radio[name^=payment_method]').length) {
        paymentMethod = $('input:radio[name^=payment_method]:checked').val();
    } else {
        paymentMethod = $('input[name^=payment_method]').val();
    }
    //console.log(paymentMethod);
    // Handle different payment methods
    return handlePaymentMethod(paymentMethod, form);
}

function handlePaymentMethod(paymentMethod, form) {
    const $ = JD.jQuery;
    
    // Stripe legacy payment processing
    if (typeof stripePublicKey !== 'undefined' && 
        paymentMethod.indexOf('os_stripe') == 0 && 
        $('#tr_card_number').is(':visible')) {
        Stripe.card.createToken({
            number: $('input[name^=x_card_num]').val(),
            cvc: $('input[name^=x_card_code]').val(),
            exp_month: $('select[name^=exp_month]').val(),
            exp_year: $('select[name^=exp_year]').val(),
            name: $('input[name^=card_holder_name]').val()
        }, stripeResponseHandler);
        
        return false;
    }

    //alert(paymentMethod);
    
    // Stripe card element
    if (typeof stripe !== 'undefined' && 
        paymentMethod.indexOf('os_stripe') == 0) {
        stripe.createToken(card).then(function(result) {
            if (result.error) {
                // Inform the customer that there was an error
                alert(result.error.message);
                form.find('.btn-submit').prop('disabled', false);
                hideLoading();
            } else {
               
                // Send the token to your server
                stripeTokenHandler(result.token);
                alert(result.token);
            }
        });
        
        return false;
    }
    
    // Square legacy payment processing
    if (paymentMethod == 'os_squareup' && $('#tr_card_number').is(':visible')) {
        sqPaymentForm.requestCardNonce();
        return false;
    }
    
    // Square card element
    if (paymentMethod.indexOf('os_squarecard') === 0 && $('#square-card-form').is(':visible')) {
        squareCardCallBackHandle();
        return false;
    }
    
    // Default form submission for other payment methods
    return submitFormData();
}

function submitFormData() {
    const $ = JD.jQuery;
    
    //console.log('Submitting form with standard payment method');
    
    const form = $("#os_form");
    
    if (form.length === 0) {
        //console.error('Form #os_form not found');
        hideLoading();
        return false;
    }

    
    try {
        if (form.hasClass('validationEngineContainer')) {
            form.validationEngine('detach');
        }
        // Remove submit event handlers
        //form.off('submit');
        form[0].submit();
        
    } catch (error) {
        console.error('Error submitting form:', error);
        hideLoading();
        form.find('.btn-submit').prop('disabled', false);
        return false;
    }
    
    return true;
}

var card;
// Initialize validation engine and other components
JD.jQuery(function($){
    $(document).ready(function(){
        
        $("#os_form").validationEngine('attach', {
            // Disable default prompts (tooltips)
            showPrompts: false,
            promptPosition: "centerRight",
            scroll: false,
            
            // Custom validation callback
            onValidationComplete: function(form, status){
                // Clear all existing custom errors first
                clearAllCustomErrors();
                
                if (!status) {
                    // Get all invalid fields
                    var invalidFields = form.find('.formError').parent().find('input, select, textarea');
                    
                    // Show custom errors for each invalid field
                    invalidFields.each(function() {
                        var $field = $(this);
                        var errorMessage = getValidationEngineErrorMessage($field);
                        
                        if (errorMessage) {
                            showCustomError($field[0], errorMessage, {
                                position: 'bottom',
                                className: 'jd-custom-error',
                                fadeIn: true,
                                showIcon: true
                            });
                        }
                    });
                }
                
                return status;
            },
            
            // Custom field validation callback
            onFieldFailure: function(field) {
                // field là DOM element, cần wrap thành jQuery object
                var $field = $(field);
                var errorMessage = getValidationEngineErrorMessage($field);
                if (errorMessage) {
                    showCustomError(field, errorMessage, {
                        position: 'bottom',
                        className: 'jd-custom-error',
                        fadeIn: true,
                        showIcon: true
                    });
                }
            },
            
            // Clear custom errors on field success
            onFieldSuccess: function(field) {
                // field là DOM element, cần wrap thành jQuery object
                var $field = $(field);
                clearCustomError($field);
            }
        });


        // Initialize Square card element if available
        if (Joomla.getOptions('squareAppId')) {
            createSquareCardElement();
        }

        // Initialize Stripe card element if available
        if (typeof stripe !== 'undefined') {
            var style = {
                base: {
                    fontSize: '16px',
                    color: "#32325d",
                }
            };

            // Create an instance of the card Element
            card = elements.create('card');

            // Add an instance of the card Element into the `card-element` <div>
            card.mount('#stripe-card-element');
        }

        // Validate login form if needed
        //if($("[name*='validate_form_login']").val() == 1) {
            //JDVALIDATEFORM("#jd-login-form");
       // }
    });
});

function setupPaymentMethods() {
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const paymentForms = document.querySelectorAll('.payment-form');
    
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            // Ẩn tất cả các form thanh toán
            paymentForms.forEach(form => {
                form.style.display = 'none';
            });
            
            // Hiển thị form tương ứng với phương thức thanh toán
            const selectedForm = document.getElementById(`${this.value}_message`);
            if (selectedForm) {
                selectedForm.style.display = 'block';
            }
            
            // Kiểm tra xem phương thức thanh toán có hỗ trợ recurring không
            const paymentMethodLabel = this.closest('.payment-method');
            const supportsRecurring = paymentMethodLabel.getAttribute('data-supports-recurring') === 'true';
            
            // Cập nhật tùy chọn recurring
            updateRecurringOptions(supportsRecurring);
        });
    });
    
    // Kích hoạt sự kiện change cho phương thức thanh toán mặc định
    const defaultPaymentMethod = document.querySelector('input[name="payment_method"]:checked');
    if (defaultPaymentMethod) {
        defaultPaymentMethod.dispatchEvent(new Event('change'));
    }
}

function updateRecurringOptions(supportsRecurring) {
    const frequencyOptions = document.querySelectorAll('.frequency-option');
    const oneTimeOption = document.querySelector('input[value="one-time"]');
    
    frequencyOptions.forEach(option => {
        const input = option.querySelector('input[type="radio"]');
        
        // Nếu là one-time, luôn cho phép
        if (input.value === 'one-time') {
            option.classList.remove('disabled');
            input.disabled = false;
        } else {
            // Nếu là recurring (monthly hoặc annually)
            if (!supportsRecurring) {
                // Nếu không hỗ trợ recurring
                option.classList.add('disabled');
                input.disabled = true;
                
                // Nếu đang chọn recurring, chuyển sang one-time
                if (input.checked && oneTimeOption) {
                    oneTimeOption.checked = true;
                    // Kích hoạt sự kiện change để cập nhật UI
                    oneTimeOption.dispatchEvent(new Event('change'));
                }
            } else {
                // Nếu hỗ trợ recurring
                option.classList.remove('disabled');
                input.disabled = false;
            }
        }
    });
    
    // Cập nhật summary sau khi thay đổi
    //updateSubmitBtnTxt();
}

// Show loading overlay
function showLoading() {
    const $ = JD.jQuery;
    
    const loadingHtml = `
        <div class="loading-overlay">
            <div class="loading-content">
                <div class="spinner"></div>
                <h3>` + getJoomlaText('JD_PROCESSING_YOUR_DONATION') + `</h3>
                <p>` + getJoomlaText('JD_PLEASE_WAIT_WHILE_WE_PROCESS_YOUR_DONATION') + `</p>
                <p><small>` + getJoomlaText('JD_DO_NOT_REFRESH_OR_CLOSE') + `</small></p>
            </div>
        </div>
    `;
    
    $('body').append(loadingHtml);
    $('body').addClass('loading');
}

// Hide loading overlay
function hideLoading() {
    const $ = JD.jQuery;
    
    $('.loading-overlay').fadeOut(500, function() {
        $(this).remove();
    });
    $('body').removeClass('loading');
}

// Utility function to format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Utility function to validate credit card number using Luhn algorithm
function validateCreditCard(cardNumber) {
    const cleanNumber = cardNumber.replace(/\s/g, '');
    
    if (!/^\d+$/.test(cleanNumber)) {
        return false;
    }
    
    let sum = 0;
    let isEven = false;
    
    for (let i = cleanNumber.length - 1; i >= 0; i--) {
        let digit = parseInt(cleanNumber.charAt(i));
        
        if (isEven) {
            digit *= 2;
            if (digit > 9) {
                digit -= 9;
            }
        }
        
        sum += digit;
        isEven = !isEven;
    }
    
    return sum % 10 === 0;
}

// Initialize form when DOM is ready
JD.jQuery(document).ready(function() {
    //console.log('DOM ready, initializing form...');
});

// Debug function
function debugFormState() {
    const $ = JD.jQuery;
    
    console.log('=== Form Debug Info ===');
    console.log('Current Step:', currentStep);
    console.log('Form Data:', collectFormData());
    console.log('Validation Engine Status:', $("#donation-form").data('validationEngine'));
    console.log('======================');
}

// Expose debug function globally for testing
window.debugFormState = debugFormState;



function toggleDedicateSection() {
    const checkbox = document.getElementById('show_dedicate');
    const dedicateDiv = document.getElementById('honoreediv');
    const nameInput = document.getElementById('dedicate_name');
    const emailInput = document.getElementById('dedicate_email');
    const $ = JD.jQuery;
    
    if (checkbox.checked) {
        // Show dedicate section
        dedicateDiv.classList.remove('d-none');
        checkbox.value = '1';
        
        // Add validation classes for jQuery Validate plugin
        $(nameInput).addClass('validate[required]');
        $(emailInput).addClass('validate[required]');
        
        // Also add HTML5 required attribute as fallback
        nameInput.setAttribute('required', 'required');
        emailInput.setAttribute('required', 'required');
        
        // Smooth scroll to section
        dedicateDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        
    } else {
        // Hide dedicate section
        dedicateDiv.classList.add('d-none');
        checkbox.value = '0';
        
        // Remove validation classes
        $(nameInput).removeClass('validate[required]');
        $(emailInput).removeClass('validate[required]');
        
        // Remove HTML5 required attributes
        nameInput.removeAttribute('required');
        emailInput.removeAttribute('required');
        
        // Clear field values
        nameInput.value = '';
        emailInput.value = '';
        
        // Clear custom errors using the provided function
        clearCustomError(nameInput);
        clearCustomError(emailInput);
        
        // Clear any standard validation errors (Bootstrap/other frameworks)
        document.querySelectorAll('#honoreediv .is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        
        // Clear jQuery Validate plugin errors if present
        $(nameInput).removeClass('error');
        $(emailInput).removeClass('error');
        $(nameInput).siblings('label.error').remove();
        $(emailInput).siblings('label.error').remove();
    }
}


// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('show_dedicate');
    if (checkbox && !checkbox.checked) {
        document.getElementById('honoreediv').classList.add('d-none');
    }
});

