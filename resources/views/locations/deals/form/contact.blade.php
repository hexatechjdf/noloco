@extends('layouts.app')
@push('style')
    <link href="{{ asset('admin/assets/dashboard/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .select2-container {
            width: 100% !important;
            /* z-index: 999999999999999999; */
        }

        div.dataTables_wrapper div.dataTables_length select {
            width: 100% !important;
        }

        .show_values {
            cursor: pointer;
        }

        .add_pill {
            padding: 2px 6px 4px 6px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f2f4f7;
            margin: 0;
        }

        .ptc-100 {
            padding-top: 10px;
        }

        .main-card {
            background: white;
            min-width: 0;
            padding: 32px 36px 28px 36px;
            border-radius: 14px;
            box-shadow: 0 7px 32px rgba(0, 0, 0, 0.09);
        }

        .form-rows [class^="col-"] {
            margin-top: 10px;
        }

        .collapse-head {
            font-weight: bold;
            font-size: 1.5rem;
        }

        .accordion-item {
            border: none;
        }

        .accordion-header {
            background: linear-gradient(90deg, #eaf1fb 0%, #fafdff 100%);
            box-shadow: 0 2px 8px rgba(24, 106, 214, 0.07);
            border: 1.5px solid #bdd2e2;
        }

        .accordion-button {
            background: linear-gradient(90deg, #eaf1fb 0%, #fafdff 100%);
        }

        .accordion-body {
            padding: 0px;
        }

        .injected-vehicle-card {
            padding: 5px 0;
        }

        .vehicle-card-img {
            border: 1px solid #ddd;
        }

        .vehicle-card-details {
            font-size: 13px;
            color: #333;
        }

        #qr-block canvas {
            margin: auto;
        }

        .input-fil-custom {
            padding: 7px 6px;
            font-size: 15px;
            background: #fafdff;
        }
        .h-20p{
            height:20px;
        }
        label{
justify-content: space-between;
        }
    </style>
@endpush
@section('content')
    <div class="container ">

        <form id="submForm" class="mt-3" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="appendData">
                        <div class="form-boxx">
                            @include('forms.internals.contactForm', [
                                'heading' => 'Vehicle',
                                'cols' => 'col-md-4',
                                'allowed_types' => ['simple'],
                                'form' => vehicleForm(),
                            ])
                            <div class="card  radius-10 main-card">
                                <div class="card-body">
                                    <div class="card-title">
                                        <h5 class="">Contact Fields</h5>
                                    </div>
                                    @include('forms.internals.fieldsSetting', [
                                        'heading' => 'Contact',
                                        'cols' => 'col-md-4',
                                        'allowed_types' => ['simple', 'extra', 'vs'],
                                        'form' => contact_informationForm(),
                                        'obj' => $contact,
                                    ])

                                    <div class="card-title mt-3">
                                        <h5 class="">Identification</h5>
                                    </div>
                                    @include('forms.internals.fieldsSetting', [
                                        'heading' => 'Contact',
                                        'cols' => 'col-md-4',
                                        'allowed_types' => ['simple', 'extra', 'vs'],
                                        'form' => identificationForm(),
                                        'obj' => $contact,
                                    ])

                                    <div class="accordion" id="creditAppAccordion">
                                        <div class="accordion-item mt-3">
                                            <h2 class="accordion-header" id="headingCreditApp">
                                                <button class="accordion-button collapsed collapse-head" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseCreditApp"
                                                    aria-expanded="false" aria-controls="collapseCreditApp">
                                                    <i class="bi bi-caret-right-fill me-2"></i>
                                                    Credit Application
                                                </button>
                                            </h2>
                                            <div id="collapseCreditApp" class="accordion-collapse collapse"
                                                aria-labelledby="headingCreditApp" data-bs-parent="#creditAppAccordion">
                                                <div class="accordion-body">
                                                    @include('forms.internals.fieldsSetting', [
                                                        'heading' => 'Credit App',
                                                        'cols' => 'col-md-4',
                                                        'allowed_types' => ['simple'],
                                                        'form' => creditApplicationForm(),
                                                        'obj' => $contact,
                                                    ])
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item mt-3">
                                            <h2 class="accordion-header" id="headingInsuranceApp">
                                                <button class="accordion-button collapsed collapse-head" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseInsuranceApp"
                                                    aria-expanded="false" aria-controls="collapseCreditApp">
                                                    <i class="bi bi-caret-right-fill me-2"></i>
                                                    Insurance Infromation
                                                </button>
                                            </h2>
                                            <div id="collapseInsuranceApp" class="accordion-collapse collapse"
                                                aria-labelledby="headingInsuranceApp"
                                                data-bs-parent="#insuranceAppAccordion">
                                                <div class="accordion-body">
                                                    @include('forms.internals.fieldsSetting', [
                                                        'heading' => 'Trade',
                                                        'cols' => 'col-md-4',
                                                        'allowed_types' => ['simple'],
                                                        'form' => insuranceInformationForm(),
                                                        'obj' => $contact,
                                                    ])
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary mt-3 contact_field_form" type="button">Submit</button>
                                </div>
                            </div>

                        </div>
                    </div>
                    @include('components.loader')
                </div>
                <div class="col-md-4">
                    <div class="card  radius-10 main-card ">
                        <div class="card-body">
                            @include('locations.components.vehicleFields', [
                                'is_source' => true,
                                'is_full' => '12',
                                'is_sold' => 'true',
                            ])

                            <div class="card-title mt-3">
                                <h5 class="mb-0">QR Code</h5>
                            </div>

                            <div id="qr-block" class="text-center mt-3">
                                <button id="generate-qr-btn" class="btn btn-primary">Generate QR Code to scan
                                    documents</button>
                                <div id="qr-timer" class="text-danger mt-2"></div>
                            </div>

                            <div class="card-title mt-3">
                                <h5 class="mb-0">Document Uploads</h5>
                            </div>

                            <div class="">
                                <label  class="form-label d-flex">Driver's License
                                    @if(@$contact['drivers_licence'])
                                    @php($urll = getValueGhlFile($contact['drivers_licence']))
                                    <a href="{{ $urll }}" target="_blank"><img class="down-image h-20p" src="{{ asset('assets/images/down.png') }}"></a>
                                    @endif
                                </label>
                                <input class="form-control input-fil-custom" type="file" id="drivers_license"
                                    name="drivers_licence" accept="image/*,application/pdf" required>
                            </div>

                            <div class="mt-3">
                                <label  class="form-label d-flex">Insurance Card
                                @if(@$contact['insurance_card'])
                                @php($urll = getValueGhlFile($contact['insurance_card']))
                                <a href="{{ $urll }}" target="_blank"><img class="down-image h-20p" src="{{ asset('assets/images/down.png') }}"></a>
                                @endif
                                </label>
                                <input class="form-control input-fil-custom" type="file" id="insurance_card"
                                    name="insurance_card" accept="image/*,application/pdf" required>
                            </div>
                        </div>


                        {{-- <div id="qrArea" class="mt-3" style="display: none;">
                            <div id="qrCodeContainer" class="mb-2"></div>
                            <div id="qrExpiredMsg" class="text-danger fw-bold" style="display: none;">
                                QR Code expired. Please generate again.
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>

        </form>

    </div>
@endsection

@push('script')
    @include('components.submitForm')
    @include('locations.components.dealsScript', ['script_type' => 'form'])
    @include('locations.components.googleaddress')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
    <script>
        let qrTimeout = null;

        function clearQRCode() {
            $('#qr-block canvas').remove();
            $('#qr-block .qr-note').remove();
            $('#qr-timer').text('QR Code expired. Please generate again.');
            $('#generate-qr-btn')
                .prop('disabled', false)
                .text('Generate QR Code to scan documents');
        }

        function injectQRCodeWithTimer() {
            clearQRCode(); // Remove existing QR if any

            const url = `https://api.premiummotors.com/widget/form/NMWhKX8IskAhSWpBoEGn?contact_id=12345`;
            const qrCanvas = $('<canvas></canvas>');

            new QRious({
                element: qrCanvas[0],
                value: url,
                size: 140
            });

            const note = $('<div class="qr-note mt-2">Scan to open/edit this application</div>');

            $('#generate-qr-btn').after(qrCanvas, note);
            $('#generate-qr-btn')
                .prop('disabled', true)
                .text('QR Code Active (expires in 5:00)');

            let secondsLeft = 300;

            function updateTimer() {
                if (secondsLeft <= 0) {
                    clearQRCode();
                    return;
                }
                const min = Math.floor(secondsLeft / 60);
                const sec = String(secondsLeft % 60).padStart(2, '0');
                $('#qr-timer').text(`QR Code expires in ${min}:${sec}`);
                secondsLeft--;
                qrTimeout = setTimeout(updateTimer, 1000);
            }

            updateTimer();
        }

        $(document).on('click', '#generate-qr-btn', function() {
            if (qrTimeout) clearTimeout(qrTimeout);
            injectQRCodeWithTimer();
        });
    </script>
    {{-- <script>
        const qrBtn = document.getElementById("generateQRBtn");
        const qrArea = document.getElementById("qrArea");
        const qrCodeContainer = document.getElementById("qrCodeContainer");
        const qrExpiredMsg = document.getElementById("qrExpiredMsg");
        const originalBtnText = "Generate QR Code";

        let timer = null;
        let timeLeft = 60; // 5 minutes
        let qrcode = null;
        const formURL = "https://chatgpt.com/c/68514086-3208-8010-8501-0b5db13f5cdf"; // Replace with your actual form route

        function formatTime(sec) {
            const min = String(Math.floor(sec / 60)).padStart(2, '0');
            const remSec = String(sec % 60).padStart(2, '0');
            return `${min}:${remSec}`;
        }

        function updateButtonText() {
            qrBtn.textContent = `QR Code Active (${formatTime(timeLeft)})`;
        }

        function startCountdown() {
            clearInterval(timer);
            updateButtonText();

            timer = setInterval(() => {
                timeLeft--;
                updateButtonText();

                if (timeLeft <= 0) {
                    clearInterval(timer);
                    qrCodeContainer.innerHTML = '';
                    qrExpiredMsg.style.display = 'block';
                    qrBtn.textContent = originalBtnText;
                }
            }, 1000);
        }

        function generateQRCodeJS() {
            timeLeft = 300;
            qrArea.style.display = 'block';
            qrExpiredMsg.style.display = 'none';
            qrCodeContainer.innerHTML = '';

            qrcode = new QRCode(qrCodeContainer, {
                text: formURL,
                width: 200,
                height: 200,
            });

            startCountdown();
        }

        qrBtn.addEventListener("click", generateQRCodeJS);
    </script> --}}
    <script>
        $(document).ready(function() {
            @if ($vin)
                $.ajax({
                    type: 'GET',
                    data: {
                        contactId: contactId,
                        locationId: locationId,
                    },
                    url: '{{ route('deals.inventories.search') }}?vin={{ $vin }}',
                    success: function(response) {
                        let item = null;
                        try {
                            item = Object.values(response)[0];
                        } catch (error) {
                            item = null;
                        }
                        vehiclesData[item.id] = item;

                        var option = new Option(item.name, item.id, true, true);
                        $(".vehicle_field").append(option).trigger('change');

                    }
                });
            @endif
        })

        $(document).ready(function() {
            function toggleDiv() {
                if ($("#flexCheckDefault").is(":checked")) {
                    $(".credit-fields").hide();
                } else {
                    $(".credit-fields").show();
                }
            }
            toggleDiv();
            $("#flexCheckDefault").on("change", function() {
                toggleDiv();
            });
        });
    </script>
@endpush
