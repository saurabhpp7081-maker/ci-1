<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('authentication/includes/head.php'); ?>

<body class="tw-bg-neutral-100 login_admin">
    <style>
        .mobile-otp-auth-wrapper {
            max-width: 460px;
        }

        .mobile-otp-badge {
            display: inline-block;
            margin-bottom: 12px;
            padding: 7px 14px;
            border-radius: 999px;
            background: #e8f4ff;
            color: #1d6fa5;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .mobile-otp-card {
            position: relative;
        }

        .mobile-otp-card .form-group {
            margin-bottom: 18px;
        }

        .mobile-otp-card .control-label {
            margin-bottom: 8px;
            color: #334155;
            font-weight: 600;
        }

        .mobile-otp-card .form-control {
            height: 46px;
            border-radius: 10px;
        }

        .mobile-otp-card .otp-input input,
        .mobile-otp-card .otp-input .form-control {
            letter-spacing: .35em;
            text-align: center;
            font-size: 20px;
            font-weight: 700;
        }

        .mobile-otp-helper {
            color: #64748b;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 18px;
        }

        .mobile-otp-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 4px;
        }

        .mobile-otp-actions .btn {
            min-width: 140px;
        }

        .mobile-otp-footer {
            margin-top: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .mobile-otp-footer .btn-link {
            padding: 0;
            border: 0;
            color: #2563eb;
        }

        .mobile-otp-hidden {
            display: none;
        }

        @media (max-width: 480px) {
            .mobile-otp-auth-wrapper {
                max-width: 100%;
            }

            .mobile-otp-actions .btn {
                width: 100%;
            }
        }
    </style>

    <div class="tw-max-w-md tw-mx-auto tw-pt-24 authentication-form-wrapper mobile-otp-auth-wrapper tw-relative tw-z-20">
        <div class="company-logo text-center">
            <?php get_dark_company_logo(); ?>
        </div>

        <div class="text-center tw-mb-5">
            <span class="mobile-otp-badge"><?= _l('mobile_otp_login_badge'); ?></span>
            <h1 class="tw-text-neutral-800 tw-text-2xl tw-font-bold tw-mb-1">
                <?= _l('mobile_otp_login_heading'); ?>
            </h1>
            <p class="tw-text-neutral-600">
                <?= _l('mobile_otp_login_subheading'); ?>
            </p>
        </div>

        <div class="tw-bg-white tw-mx-2 sm:tw-mx-6 tw-py-8 tw-px-6 sm:tw-px-8 tw-shadow-sm tw-rounded-lg tw-border tw-border-solid tw-border-neutral-600/20 mobile-otp-card">
            <div id="mobile-otp-alert" class="alert mobile-otp-hidden"></div>

            <?= form_open('', ['id' => 'mobile-otp-mobile-form', 'novalidate' => true]); ?>
            <?= render_input(
                'mobile',
                'mobile_otp_login_mobile_label',
                '',
                'tel',
                [
                    'autocomplete' => 'tel',
                    'autofocus'    => true,
                    'placeholder'  => _l('mobile_otp_login_mobile_placeholder'),
                ]
            ); ?>

            <button type="submit" id="send-otp-button" class="btn btn-primary btn-block tw-font-semibold tw-py-2">
                <?= _l('mobile_otp_login_send_button'); ?>
            </button>
            <?= form_close(); ?>

            <?= form_open('', ['id' => 'mobile-otp-verify-form', 'class' => 'mobile-otp-hidden', 'novalidate' => true]); ?>
            <input type="hidden" id="otp-mobile" name="mobile">

            <div class="otp-input">
                <?= render_input(
                    'otp',
                    'mobile_otp_login_otp_label',
                    '',
                    'text',
                    [
                        'inputmode'     => 'numeric',
                        'maxlength'     => 6,
                        'autocomplete'  => 'one-time-code',
                        'placeholder'   => '000000',
                    ]
                ); ?>
            </div>

            <p class="mobile-otp-helper" id="otp-help-text">
                <?= _l('mobile_otp_login_otp_help'); ?>
            </p>

            <div class="mobile-otp-actions">
                <button type="submit" id="verify-otp-button" class="btn btn-primary">
                    <?= _l('mobile_otp_login_verify_button'); ?>
                </button>
                <button type="button" id="change-mobile-button" class="btn btn-default">
                    <?= _l('mobile_otp_login_change_mobile'); ?>
                </button>
            </div>

            <div class="mobile-otp-footer">
                <span class="mobile-otp-helper tw-mb-0" id="resend-timer"></span>
                <button type="button" id="resend-otp-button" class="btn-link mobile-otp-hidden">
                    <?= _l('mobile_otp_login_resend_button'); ?>
                </button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>

    <script src="<?= base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
    <script>
        $(function () {
            var sendUrl = <?= json_encode($send_otp_url); ?>;
            var verifyUrl = <?= json_encode($verify_otp_url); ?>;
            var resendUrl = <?= json_encode($resend_otp_url); ?>;
            var redirectFallback = <?= json_encode(admin_url()); ?>;
            var resendAfterSeconds = <?= (int) $resend_after_seconds; ?>;
            var csrfTokenName = <?= json_encode($csrf_token_name); ?>;
            var csrfTokenHash = <?= json_encode($csrf_token_hash); ?>;

            var $alertBox = $('#mobile-otp-alert');
            var $mobileForm = $('#mobile-otp-mobile-form');
            var $verifyForm = $('#mobile-otp-verify-form');
            var $mobileInput = $('#mobile');
            var $otpMobileInput = $('#otp-mobile');
            var $otpInput = $('#otp');
            var $sendButton = $('#send-otp-button');
            var $verifyButton = $('#verify-otp-button');
            var $resendButton = $('#resend-otp-button');
            var $resendTimer = $('#resend-timer');
            var $changeMobileButton = $('#change-mobile-button');
            var $otpHelpText = $('#otp-help-text');
            var activeTimer = null;

            function showAlert(type, message) {
                $alertBox
                    .removeClass('mobile-otp-hidden alert-success alert-danger alert-warning alert-info')
                    .addClass('alert-' + type)
                    .text(message);
            }

            function hideAlert() {
                $alertBox
                    .removeClass('alert-success alert-danger alert-warning alert-info')
                    .addClass('mobile-otp-hidden')
                    .text('');
            }

            function setLoading($button, isLoading, defaultText) {
                $button.prop('disabled', isLoading).text(isLoading ? 'Please wait...' : defaultText);
            }

            function buildPayload(data) {
                data[csrfTokenName] = csrfTokenHash;
                return data;
            }

            function setVerifyStep(mobile) {
                $mobileForm.addClass('mobile-otp-hidden');
                $verifyForm.removeClass('mobile-otp-hidden');
                $otpMobileInput.val($.trim(mobile));
                $otpInput.val('').focus();
                $otpHelpText.text('<?= addslashes(_l('mobile_otp_login_sent_to')); ?>' + ' ' + $.trim(mobile));
            }

            function setMobileStep() {
                $verifyForm.addClass('mobile-otp-hidden');
                $mobileForm.removeClass('mobile-otp-hidden');
                $resendButton.addClass('mobile-otp-hidden').prop('disabled', false);
                $resendTimer.text('');
                if (activeTimer) {
                    clearInterval(activeTimer);
                    activeTimer = null;
                }
                $mobileInput.focus();
            }

            function startResendTimer(seconds) {
                var until = Date.now() + (seconds * 1000);
                localStorage.setItem('mobileOtpResendUntil', String(until));

                if (activeTimer) {
                    clearInterval(activeTimer);
                }

                var tick = function () {
                    var remaining = Math.max(0, Math.ceil((until - Date.now()) / 1000));

                    if (remaining <= 0) {
                        $resendTimer.text('');
                        $resendButton.removeClass('mobile-otp-hidden').prop('disabled', false);
                        localStorage.removeItem('mobileOtpResendUntil');
                        clearInterval(activeTimer);
                        activeTimer = null;
                        return;
                    }

                    $resendButton.addClass('mobile-otp-hidden');
                    $resendTimer.text('<?= addslashes(_l('mobile_otp_login_resend_in')); ?>'.replace('%s', remaining));
                };

                tick();
                activeTimer = setInterval(tick, 1000);
            }

            function resumeTimerIfNeeded() {
                var stored = parseInt(localStorage.getItem('mobileOtpResendUntil') || '0', 10);
                if (stored > Date.now() && !$verifyForm.hasClass('mobile-otp-hidden')) {
                    startResendTimer(Math.ceil((stored - Date.now()) / 1000));
                }
            }

            function submitRequest(url, data) {
                return $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: buildPayload(data),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            }

            $mobileForm.on('submit', function (e) {
                e.preventDefault();
                hideAlert();
                setLoading($sendButton, true, '<?= addslashes(_l('mobile_otp_login_send_button')); ?>');

                submitRequest(sendUrl, {
                    mobile: $mobileInput.val()
                }).done(function (payload) {
                    showAlert('success', payload.message);
                    if (payload.otp) {
                        console.log('Mobile OTP Login test OTP:', payload.otp);
                    }
                    setVerifyStep($mobileInput.val());
                    startResendTimer(payload.resend_after_seconds || resendAfterSeconds);
                }).fail(function (xhr) {
                    var message = 'Unable to send OTP.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showAlert('danger', message);
                }).always(function () {
                    setLoading($sendButton, false, '<?= addslashes(_l('mobile_otp_login_send_button')); ?>');
                });
            });

            $verifyForm.on('submit', function (e) {
                e.preventDefault();
                hideAlert();
                setLoading($verifyButton, true, '<?= addslashes(_l('mobile_otp_login_verify_button')); ?>');

                submitRequest(verifyUrl, {
                    mobile: $otpMobileInput.val(),
                    otp: $otpInput.val()
                }).done(function (payload) {
                    showAlert('success', payload.message);
                    window.location.href = payload.redirect_url || redirectFallback;
                }).fail(function (xhr) {
                    var message = 'Unable to verify OTP.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showAlert('danger', message);
                }).always(function () {
                    setLoading($verifyButton, false, '<?= addslashes(_l('mobile_otp_login_verify_button')); ?>');
                });
            });

            $resendButton.on('click', function () {
                hideAlert();
                $resendButton.prop('disabled', true);

                submitRequest(resendUrl, {
                    mobile: $otpMobileInput.val()
                }).done(function (payload) {
                    showAlert('success', payload.message);
                    if (payload.otp) {
                        console.log('Mobile OTP Login test OTP:', payload.otp);
                    }
                    startResendTimer(payload.resend_after_seconds || resendAfterSeconds);
                }).fail(function (xhr) {
                    var message = 'Unable to resend OTP.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showAlert('danger', message);
                    $resendButton.prop('disabled', false);
                });
            });

            $changeMobileButton.on('click', function () {
                hideAlert();
                setMobileStep();
            });

            resumeTimerIfNeeded();
        });
    </script>
</body>

</html>
