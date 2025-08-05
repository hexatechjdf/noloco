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

        .h-20p {
            height: 20px;
        }

        label {
            justify-content: space-between;
        }

        .form-header {
            background: #1a3a68;
            padding: 10px 0px;
            text-align: center;
            margin-bottom: 20px;
            color: white;
            border-radius: 5px;
        }

        #creditForm {
            max-width: 700px;
            margin: 0 auto;
            padding: 15px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .btn {
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
            border: none;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .credit-footer-action {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
@endpush
@section('content')
    <div class="container ">

        <form id="submForm" class="mt-3" enctype="multipart/form-data">
            <input type="hidden" name="is_noloco"  value="{{ $is_noloco }}" >
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
                                    <div class="w-100 text-right">
                                        <button class="btn btn-primary mt-3 contact_field_form" type="button">Submit</button>
                                    </div>
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
                                'is_noloco' => $is_noloco,
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
                                <label class="form-label d-flex">Driver's License
                                    <div class="drivers_licence_box">
                                    </div>
                                </label>
                                <input class="form-control input-fil-custom" type="file" id="drivers_license"
                                    name="drivers_licence" accept="image/*,application/pdf" >
                            </div>

                            <div class="mt-3">
                                <label class="form-label d-flex">Insurance Card
                                    <div class="insurance_card_box">
                                    </div>
                                </label>
                                <input class="form-control input-fil-custom" type="file" id="insurance_card"
                                    name="insurance_card" accept="image/*,application/pdf" >
                            </div>
                            <div class="card-title mt-3">
                                <h5 class="mb-0">Credit Reports</h5>
                            </div>

                            <div class="">
                                <div class="credit-container"></div>
                            </div>
                            <div class="">
                                <button class="btn btn-primary mt-3 open_credit w-100" type="button">Run New Credit
                                    report</button>
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

    <div class="modal fade" id="creditModal" tabindex="-1" role="dialog" aria-labelledby="creditModalTitle"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="creditForm">
                    <div class="modal-body">
                        <div class="form-header">
                            <h3>Run New Credit Report</h3>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>First Name</label>
                                <input type="text" class="form-control firstName" name="firstName" required
                                    value="{{ @$contact['firstName'] }}" />
                            </div>
                            <div class="col-md-6">
                                <label>Middle Name</label>
                                <input type="text" class="form-control middle_name" name="middle_name"
                                    value="{{ @$contact['middle_name'] }}" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Last Name</label>
                            <input type="text" class="form-control lastName" name="lastName" required
                                value="{{ @$contact['lastName'] }}" />
                        </div>

                        <div class="mb-3">
                            <label>Address</label>
                            <input type="text" class="form-control address1" name="address1" required
                                value="{{ @$contact['address1'] }}" />
                        </div>

                        <div class="mb-3">
                            <label>Bureau Types</label>
                            <div class="d-flex flex-wrap gap-3">
                                <label><input type="checkbox" name="experian" class="experian" checked />
                                    Experian</label>
                                <label><input type="checkbox" name="equifax" class="equifax" checked /> Equifax</label>
                                <label><input type="checkbox" name="transunion" class="transunion" checked />
                                    TransUnion</label>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>City</label>
                                <input type="text" class="form-control city" name="city" required
                                    value="{{ @$contact['city'] }}" />
                            </div>
                            <div class="col-md-4">
                                <label>State</label>
                                <input type="text" class="form-control state" name="state" required
                                    value="{{ @$contact['state'] }}" />
                            </div>
                            <div class="col-md-4">
                                <label>ZIP</label>
                                <input type="text" class="form-control postalCode" name="postalCode" required
                                    value="{{ @$contact['postalCode'] }}" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Date of Birth</label>
                                <input type="date" class="form-control dateOfBirth" name="dateOfBirth" required
                                    value="{{ @$contact['dateOfBirth'] }}" />
                            </div>
                            <div class="col-md-6">
                                <label>SSN</label>
                                <input type="password" class="form-control social_security_number"
                                    name="social_security_number" value="{{ @$contact['social_security_number'] }}"
                                    required />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class=" credit-footer-action w-100">
                            <button type="submit" class="btn btn-success w-100">Save changes</button>

                            <button type="button" class="btn btn-danger w-100 close-modal"
                                data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@push('script')
    @include('components.submitForm')
    @include('locations.components.dealsScript', ['script_type' => 'form'])
    @include('locations.components.googleaddress')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
    <script>
        let sour = `{{ $source }}`;
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
            $('.sources').val(sour).trigger('change');
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
                        console.log(response);
                        try {
                            item = Object.values(response)[0];
                            vehiclesData[item.id] = item;
                            @if ($is_noloco)
                                setVehicleFields(item)
                            @else
                                var option = new Option(item.name, item.id, true, true);
                                $(".vehicle_field").append(option).trigger('change');
                            @endif

                        } catch (error) {
                            item = null;
                        }


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

        $(document).ready(function() {
            @if (@$contact)
                let contact = @json($contact);
                getImageUrlFromContact(contact, 'insurance_card');
                getImageUrlFromContact(contact, 'drivers_licence');
            @endif

            fetchCreditReports(contactId, locationId);
        });

        async function fetchCreditReports(contactId, locationId) {
            const res = await fetch("https://gostarauto.com/api/credit-report/list", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    contactId: contactId,
                    locationId: locationId
                })
            });

            const json = await res.json();
            const reports = json?.credit?.data?.creditReportsCollection?.edges?.map(e => e.node) || [];
            const contactData = json.contact;
            console.log(contactData);

            // Sort latest first
            reports.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));

            let dropdown = $('.credit-container');

            if (!reports.length) {
                dropdown.html(`<div style="font-size:14px; color:#777;">No credit reports found.</div>`);
                return dropdown;
            }

            const bureauIcons = {
                EQUIFAX: "https://storage.googleapis.com/msgsndr/ZWry4bE66uaqlPng8WjQ/media/67fd3bd70f63bf775e29981b.png",
                TRANS_UNION: "https://storage.googleapis.com/msgsndr/ZWry4bE66uaqlPng8WjQ/media/67fd3bf7a119410b40c5b672.png",
                EXPERIAN: "https://storage.googleapis.com/msgsndr/ZWry4bE66uaqlPng8WjQ/media/67fd3bd771384b4077b578d1.png"
            };

            // Grouping reports
            const groupedReports = {};
            reports.forEach(report => {
                const key = report.createdAt + "_" + report.fullName?.first + report.fullName?.last;
                if (!groupedReports[key]) groupedReports[key] = [];
                groupedReports[key].push(report);
            });

            $.each(groupedReports, function(key, group) {
                const mainReport = group[0];
                const name = `${mainReport.fullName?.first || ""} ${mainReport.fullName?.last || ""}`.trim();
                const date = new Date(mainReport.createdAt);
                const formattedDate = date.toLocaleString();
                const isRecent = Date.now() - date.getTime() <= 30 * 86400000;
                const url = mainReport.url || "#";

                const bureauHTML = mainReport.creditBureau.map(bureau => {
                    const icon = bureauIcons[bureau];
                    return icon ? `
                <span style="display:flex; align-items:center; gap:4px;">
                    <img src="${icon}" alt="${bureau}" style="width:16px; height:16px;" />
                </span>
            ` : '';
                }).join(`<span style="margin: 0 6px; color: #ccc;">|</span>`);

                const $card = $(`
            <div class="credit-card mb-2 p-2" style="
                border: 1px solid #eee;
                border-radius: 8px;
                background-color: ${isRecent ? "#e6f7ff" : "#fafafa"};
                font-size: 12px;
            ">
                <div style="display:flex; justify-content:space-between;">
                    <div>
                        <div style="display:flex; gap:6px; font-weight:600;">
                            <span>${name}</span>
                            ${isRecent ? `<span style="font-size:10px; color:green; background:#e0f5e9; padding:1px 6px; border-radius:4px;">🟢 Recent</span>` : ""}
                        </div>
                        <div style="display:flex; gap:8px; margin:4px 0;">${bureauHTML}</div>
                        <div style="font-size:11px; color:#555;"><strong>Ran at:</strong> ${formattedDate}</div>
                    </div>
                    <div class="view-report" style="cursor:pointer;" title="View Report">
                        <i class="fas fa-eye text-primary"> View</i>
                    </div>
                </div>
            </div>
        `);

                // Click handler for view
                $card.find('.view-report').on('click', function() {
                    openMoparOrPopup(`${url}?id=${mainReport.uuid}`, "View Credit Report");
                    $dropdown.remove();
                });

                dropdown.append($card);
            });

            return dropdown;
        }


        $(document).on('click', '.close-modal', function() {
            $(this).closest('.modal').modal('hide')
        });
        $(document).on('click', '.open_credit', function() {
            openCreditForm()
        });


        function openCreditForm() {
            $('#creditModal').modal('show'); // Bootstrap modal show

            // Attach submit handler
            $('#creditForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());

                data.contact_id = contactId;
                data.location_id = locationId;

                console.log("Submitted data:", data);
                runCreditReport(data);

                $('#creditModal').modal('hide');
            });
        }

        function runCreditReport(data) {
            fetch('https://gostarauto.com/api/credit-report/settle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(response => {
                    console.log('Credit report response:', response);
                    if (response.status && response.src) {
                        alert("Submitted Successfully")
                    } else {
                        alert(response.message)
                    }
                    // Optionally show a success message or close modal
                })
                .catch(error => {
                    console.error('Submission error:', error);
                });
        }
    </script>
@endpush
