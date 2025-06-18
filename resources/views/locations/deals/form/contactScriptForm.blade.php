<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact & Credit Application</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA9SQetjbchWmEJVV1uKsl4Q_gQID3FGBQ&libraries=places"></script>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f2f4f7;
      margin: 0;
      padding: 32px 0;
    }
    .container-flex {
      display: flex;
      max-width: 1500px;
      margin: 0 auto;
      gap: 36px;
      align-items: flex-start;
    }
    .main-card {
      background: white;
      flex: 2 1 0;
      min-width: 0;
      padding: 32px 36px 28px 36px;
      border-radius: 14px;
      box-shadow: 0 7px 32px rgba(0,0,0,0.09);
      width: 0;
    }
    .side-card {
      background: white;
      flex: 1 1 0;
      min-width: 300px;
      max-width: 390px;
      padding: 26px 22px 22px 22px;
      border-radius: 14px;
      box-shadow: 0 7px 32px rgba(0,0,0,0.09);
      display: flex;
      flex-direction: column;
      gap: 20px;
      position: sticky;
      top: 32px;
      height: fit-content;
    }
    @media (max-width: 950px) {
      .container-flex { flex-direction: column; }
      .side-card { max-width: 100%; width: 100%; position: static; }
    }
    h2 {
      font-weight: 700;
      font-size: 26px;
      margin-top: 0;
      margin-bottom: 12px;
    }
    .qr-block {
      text-align: center;
      padding: 10px 8px;
    }
    .qr-block canvas {
      border-radius: 9px;
      border: 1.5px solid #ddd;
    }
    .qr-note {
      font-size: 13px;
      color: #555;
      margin-top: 7px;
      font-weight: 600;
    }
    .side-section-title {
      font-size: 17px;
      font-weight: 600;
      color: #234;
      margin-top: 5px;
      margin-bottom: 9px;
      letter-spacing: 0.01em;
    }
    .vehicle-card-search-row {
      margin-bottom: 0;
    }
    .injected-vehicle-card {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 11px;
      background: #f6faff;
      border: 1.5px solid #bfd6ed;
      border-radius: 10px;
      box-shadow: 0 1px 7px rgba(0,0,0,0.06);
      font-size: 15px;
      cursor: pointer;
      margin-bottom: 4px;
      transition: box-shadow 0.14s;
    }
    .injected-vehicle-card:hover {
      box-shadow: 0 3px 12px rgba(40,104,196,0.08);
    }
    .vehicle-card-img {
      width: 82px;
      height: 52px;
      object-fit: cover;
      border-radius: 5px;
      background: #e9eef2;
      border: 1px solid #bbb;
    }
    .vehicle-card-details {
      font-weight: 600;
      line-height: 1.4;
    }
    .vehicle-search-box { display: none; }
    .vehicle-dropdown {
      position: absolute;
      background: white;
      border: 1.5px solid #bdd2e2;
      z-index: 9999;
      width: 100%;
      max-height: 300px;
      overflow-y: auto;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    }
    .vehicle-dropdown-item {
      display: flex;
      align-items: center;
      padding: 8px;
      cursor: pointer;
      border-bottom: 1px solid #eee;
      gap: 10px;
    }
    .vehicle-dropdown-img {
      width: 55px;
      height: 37px;
      object-fit: cover;
      border-radius: 3px;
      background: #e5ebf2;
    }
    .vehicle-dropdown-details {
      font-size: 14px;
      font-weight: 500;
      color: #234;
    }
    .form-section {
      margin-bottom: 26px;
    }
    .section-title {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 8px;
      margin-top: 28px;
      color: #235;
    }
    .row {
      display: flex;
      gap: 16px;
      margin-bottom: 14px;
      flex-wrap: wrap;
    }
    .col-1 { flex: 1 1 100%; }
    .col-2 { flex: 1 1 48%; }
    .col-3 { flex: 1 1 31%; }
    label {
      font-weight: 600;
      display: block;
      margin-bottom: 4px;
      font-size: 13px;
    }
    input, select {
      width: 100%;
      padding: 9px;
      border-radius: 7px;
      border: 1px solid #bbb;
      font-size: 15px;
      background: #fafcff;
      margin-bottom: 2px;
    }
    input:disabled, select:disabled {
      background: #f3f3f3;
      color: #aaa;
    }
    button {
      padding: 11px;
      background: #186ad6;
      color: white;
      border: none;
      border-radius: 7px;
      font-size: 16px;
      margin-top: 20px;
      cursor: pointer;
      font-weight: 600;
      min-width: 130px;
      transition: background 0.13s;
    }
    button:active {
      background: #124e9d;
    }
    .save-status {
      font-weight: 600;
      font-size: 15px;
      color: #28a745;
      margin-left: 15px;
      vertical-align: middle;
    }
    hr {
      margin: 26px 0 20px 0;
      border: none;
      border-top: 1.5px solid #e8e8e8;
    }
    .collapsible-toggle {
      display: flex;
      align-items: center;
      cursor: pointer;
      user-select: none;
      font-size: 18px;
      font-weight: 600;
      color: #186ad6;
      margin-bottom: 0;
      margin-top: 32px;
      padding: 12px 18px;
      border-radius: 8px;
      background: linear-gradient(90deg, #eaf1fb 0%, #fafdff 100%);
      box-shadow: 0 2px 8px rgba(24,106,214,0.07);
      border: 1.5px solid #bdd2e2;
      transition: background 0.18s, color 0.18s;
      gap: 12px;
    }
    .collapsible-toggle:hover {
      background: linear-gradient(90deg, #dbeafe 0%, #eaf6ff 100%);
      color: #124e9d;
    }
    .collapsible-arrow {
      display: inline-block;
      transition: transform 0.22s cubic-bezier(0.4,0,0.2,1);
      font-size: 22px;
      margin-right: 4px;
      color: #186ad6;
    }
    .collapsible-arrow.collapsed {
      transform: rotate(-90deg);
    }
    .collapsible-content {
      overflow: hidden;
      transition: max-height 0.28s cubic-bezier(0.4,0,0.2,1), opacity 0.22s;
      max-height: 2000px;
      opacity: 1;
      margin-bottom: 8px;
    }
    .collapsible-content.collapsed {
      max-height: 0;
      opacity: 0;
      pointer-events: none;
      margin-bottom: 0;
    }
    .upload-block {
      margin-bottom: 14px;
      display: flex;
      flex-direction: column;
      gap: 6px;
    }
    .upload-block label {
      font-size: 13.5px;
      font-weight: 600;
    }
    .upload-block input[type="file"] {
      border: 1.5px solid #c9d1de;
      padding: 7px 6px;
      font-size: 15px;
      background: #fafdff;
    }
  </style>
</head>
<body>
  <div class="container-flex">
    <div class="main-card" id="main-form">Loading...</div>
    <div class="side-card" id="side-card" style="display:none">
      <div class="side-section-title">Vehicle Info</div>
      <div id="vehicle-row"></div>
      <div class="side-section-title">QR Code</div>
      <div class="qr-block" id="qr-block">
        <button id="generate-qr-btn" type="button" style="margin: 10px auto; display: block; background: #186ad6; color: #fff; font-weight: 600; border-radius: 7px; padding: 10px 18px; font-size: 16px;">Generate QR Code to scan documents</button>
        <div id="qr-timer" style="margin-top: 8px; color: #d00; font-weight: 600;"></div>
      </div>
      <div class="side-section-title">Document Uploads</div>
      <div class="upload-block">
        <label for="dl_upload">Driver's License</label>
        <input type="file" id="dl_upload" name="dl_upload" accept="image/*,.pdf"/>
      </div>
      <div class="upload-block">
        <label for="insurance_upload">Insurance Card</label>
        <input type="file" id="insurance_upload" name="insurance_upload" accept="image/*,.pdf"/>
      </div>
      <div class="side-section-title">Credit Reports</div>
      <div class="credit-reports-section" id="credit-reports-section" style="display:none">
        <div class="no-reports-msg" style="font-size:15px;color:#888;text-align:center;margin:12px 0 0 0;">No Credit Reports found</div>
        <div class="scroll-controls" style="display:flex;justify-content:center;gap:10px;margin:4px 0;"></div>
        <div class="cards-container" style="display:flex;flex-direction:column;gap:10px;max-height:220px;overflow:hidden;"></div>
        <button type="button" class="run-new-btn" style="margin:10px auto 0 auto;display:block;background:#186ad6;font-weight:600;">Run New Credit Report</button>
      </div>
    </div>
  </div>
  <script>
  (async () => {
    // --- API CONFIG ---
    const GOOGLE_API_KEY = "AIzaSyA9SQetjbchWmEJVV1uKsl4Q_gQID3FGBQ";
    const MAKE_WEBHOOK = "https://hook.us2.make.com/2fuq04mpak0t73qell5dhpqqliuok0on";
    const UPDATE_CONTACT_WEBHOOK = "https://hook.us2.make.com/m55jbxxztvb8g998i4uagdj9p5qberb5"; // <-- your new webhook
    const NOLC_API = "https://api.portals.noloco.io/data/starauto";
    const NOLC_TOKEN = "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySWQiOjEsImVtYWlsIjoic2Fzb0BzdGFyYXV0b2NybS5jb20iLCJwcm9qZWN0Ijoic3RhcmF1dG8iLCJ0eXBlIjoiQVBJIiwiaWF0IjoxNzQ1MDMyMTg4fQ.5LL03HmbbFpeom_Nk5TSMvhqteOHx7TAckiv4XOISBk";
    const fallbackImage = "https://storage.googleapis.com/msgsndr/geAOl3NEW1iIKIWheJcj/media/67eb76621870f484684e9b25.webp";

    // --- URL PARAMS ---
    const query = new URLSearchParams(location.search);
    const contactId = query.get("contactId") || "";
    const locationId = query.get("locationId") || "";

    // Reference the new right panel
    const vehicleRow = document.getElementById("vehicle-row");
    const qrBlock = document.getElementById("qr-block");
    const sideCard = document.getElementById("side-card");

    if (!contactId || !locationId) {
      document.getElementById("main-form").innerHTML = '<div style="color:red;">Missing contact_id or location_id in URL.</div>';
      return;
    }

    // --- QR CODE ---
    const generateQrBtn = document.getElementById("generate-qr-btn");
    const qrTimer = document.getElementById("qr-timer");
    let qrTimeout = null;

    function clearQRCode() {
      // Remove QR code and timer
      const canvas = qrBlock.querySelector("canvas");
      if (canvas) canvas.remove();
      const note = qrBlock.querySelector(".qr-note");
      if (note) note.remove();
      qrTimer.innerText = "QR Code expired. Please generate again.";
      generateQrBtn.disabled = false;
      generateQrBtn.innerText = "Generate QR Code to scan documents";
    }

    function injectQRCodeWithTimer() {
      // Remove any existing QR code/timer
      clearQRCode();
      qrTimer.innerText = "";
      // Generate QR code
      const url = `https://api.premiummotors.com/widget/form/NMWhKX8IskAhSWpBoEGn?contact_id=${contactId}`;
      const qrCanvas = document.createElement("canvas");
      new QRious({ element: qrCanvas, value: url, size: 140 });
      qrBlock.insertBefore(qrCanvas, generateQrBtn.nextSibling);
      const note = document.createElement("div");
      note.innerText = "Scan to open/edit this application";
      note.className = "qr-note";
      qrBlock.insertBefore(note, qrCanvas.nextSibling);

      // Start 5 minute timer
      let secondsLeft = 300;
      generateQrBtn.disabled = true;
      generateQrBtn.innerText = "QR Code Active (expires in 5:00)";
      function updateTimer() {
        if (secondsLeft <= 0) {
          clearQRCode();
          return;
        }
        const min = Math.floor(secondsLeft / 60);
        const sec = secondsLeft % 60;
        qrTimer.innerText = `QR Code expires in ${min}:${sec.toString().padStart(2, "0")}`;
        secondsLeft--;
        qrTimeout = setTimeout(updateTimer, 1000);
      }
      updateTimer();
    }

    generateQrBtn.onclick = () => {
      if (qrTimeout) clearTimeout(qrTimeout);
      injectQRCodeWithTimer();
    };

    // --- FETCH CONTACT & SOURCE LIST DATA (Unified) ---
    async function fetchContactAndSourceList(locationId, contactId) {
      const res = await fetch(MAKE_WEBHOOK, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ contact_id: contactId, location_id: locationId })
      });
      const rawText = await res.text();
      let data = {};
      try { data = JSON.parse(rawText); } catch (e) { data = {}; }
      return data;
    }

    // --- FETCH INVENTORY ---
    async function fetchInventory(locationId) {
      const response = await fetch(NOLC_API, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Authorization": NOLC_TOKEN
        },
        body: JSON.stringify({
          query: `
            query {
              inventoryCollection(where: { dealershipSubAccountId: { equals: "${locationId}" } }) {
                edges {
                  node {
                    id uuid stock vin year make model trim listedPrice featuredPhoto vdpUrl miles
                  }
                }
              }
            }
          `
        })
      });
      const data = await response.json();
      return data.data.inventoryCollection.edges.map(e => e.node);
    }

    // --- FORMAT PHONE ---
    function formatUSPhone(val) {
      if (!val) return '';
      let digits = val.replace(/\D/g, "");
      if (digits.length > 10 && (digits.startsWith("1") || digits.startsWith("01"))) {
        digits = digits.slice(-10);
      }
      if (digits.length === 0) return '';
      if (digits.length < 4) return digits;
      if (digits.length < 7) return `(${digits.slice(0,3)}) ${digits.slice(3)}`;
      return `(${digits.slice(0,3)}) ${digits.slice(3,6)}-${digits.slice(6,10)}`;
    }
    function setupPhoneMask(input) {
      if (!input) return;
      input.value = formatUSPhone(input.value);
      input.setAttribute("inputmode", "tel");
      input.setAttribute("autocomplete", "tel");
      input.setAttribute("pattern", "[0-9]*");
      input.addEventListener("input", () => {
        input.value = formatUSPhone(input.value);
        input.setSelectionRange(input.value.length, input.value.length);
      });
      input.addEventListener("blur", () => {
        input.value = formatUSPhone(input.value);
      });
      input.addEventListener("paste", () => {
        setTimeout(() => { input.value = formatUSPhone(input.value); }, 0);
      });
    }

    // --- ADDRESS AUTOCOMPLETE ---
    function setupGoogleAutocomplete(addressInput, cityInput, stateInput, zipInput) {
      if (!addressInput) return;
      const autocomplete = new google.maps.places.Autocomplete(addressInput, { types: ["address"] });
      let autocompleteActive = false;
      const handlePlaceChanged = () => {
        const place = autocomplete.getPlace();
        let street = "", city = "", state = "", zip = "";
        if (place.address_components) {
          const comps = place.address_components;
          const find = type => comps.find(c => c.types.includes(type))?.long_name || "";
          const findShort = type => comps.find(c => c.types.includes(type))?.short_name || "";
          const streetNum = find("street_number");
          const route = find("route");
          street = `${streetNum} ${route}`.trim();
          city = find("locality") || find("sublocality") || "";
          state = findShort("administrative_area_level_1");
          zip = find("postal_code");
        }
        addressInput.value = street || place.name || "";
        if (cityInput) cityInput.value = city;
        if (stateInput) stateInput.value = state;
        if (zipInput) zipInput.value = zip;
      };
      addressInput.addEventListener("focus", () => {
        if (!autocompleteActive) {
          autocomplete.addListener("place_changed", handlePlaceChanged);
          autocompleteActive = true;
        }
      });
      addressInput.addEventListener("input", () => {
        if (!autocompleteActive) {
          autocomplete.addListener("place_changed", handlePlaceChanged);
          autocompleteActive = true;
        }
      });
    }

    // --- VEHICLE CARD ---
    function createVehicleCard(vehicle, onClick) {
      const card = document.createElement("div");
      card.className = "injected-vehicle-card";
      const img = document.createElement("img");
      img.src = vehicle?.featuredPhoto || fallbackImage;
      img.className = "vehicle-card-img";
      const details = document.createElement("div");
      details.className = "vehicle-card-details";
      details.innerHTML = vehicle
        ? `<strong>${vehicle.year} ${vehicle.make} ${vehicle.model} ${vehicle.trim || ""}</strong><br>
           Stock: ${vehicle.stock || ""} • ${vehicle.miles ? Number(vehicle.miles).toLocaleString() : "0"} mi`
        : `<em>Click to select a vehicle</em>`;
      card.appendChild(img);
      card.appendChild(details);
      card.addEventListener("click", onClick);
      return {
        card,
        update: v => {
          img.src = v?.featuredPhoto || fallbackImage;
          details.innerHTML = v
            ? `<strong>${v.year} ${v.make} ${v.model} ${v.trim || ""}</strong><br>
               Stock: ${v.stock || ""} • ${v.miles ? Number(v.miles).toLocaleString() : "0"} mi`
            : `<em>Click to select a vehicle</em>`;
        }
      };
    }
    function createVehicleDropdown(input, data, onSelect) {
      const dropdown = document.createElement("div");
      dropdown.className = "vehicle-dropdown";
      input.parentElement.style.position = "relative";
      input.parentElement.appendChild(dropdown);

      function showDropdown(term = "") {
        dropdown.innerHTML = "";
        const sorted = [...data].sort((a, b) => {
          const aKey = `${a.make} ${a.model} ${a.year}`.toLowerCase();
          const bKey = `${b.make} ${b.model} ${b.year}`.toLowerCase();
          return aKey.localeCompare(bKey);
        });
        const matches = sorted.filter(v =>
          `${v.year} ${v.make} ${v.model} ${v.trim} ${v.stock} ${v.vin}`.toLowerCase().includes(term)
        );
        matches.forEach(vehicle => {
          const item = document.createElement("div");
          item.className = "vehicle-dropdown-item";
          const image = document.createElement("img");
          image.src = vehicle.featuredPhoto || fallbackImage;
          image.className = "vehicle-dropdown-img";
          const text = document.createElement("div");
          text.className = "vehicle-dropdown-details";
          text.innerHTML = `
            <strong>${vehicle.year} ${vehicle.make} ${vehicle.model} ${vehicle.trim || ""}</strong><br>
            Vin#: ${vehicle.vin} | Stock #: ${vehicle.stock}<br>
            Price: $${Number(vehicle.listedPrice).toLocaleString()}
          `;
          item.appendChild(image);
          item.appendChild(text);
          item.onclick = () => {
            onSelect(vehicle);
            input.style.display = "none";
            input.value = "";
            dropdown.innerHTML = "";
          };
          dropdown.appendChild(item);
        });
        dropdown.style.display = matches.length ? "block" : "none";
      }
      input.addEventListener("input", () => {
        showDropdown(input.value.toLowerCase());
      });
      document.addEventListener("mousedown", function handleClickOutside(e) {
        if (
          dropdown.style.display === "block" &&
          !dropdown.contains(e.target) &&
          e.target !== input
        ) {
          dropdown.style.display = "none";
        }
      });
      return { showDropdown, hide: () => (dropdown.style.display = "none") };
    }

    // --- LAYOUT & RENDER ---
    const fieldGroups = [
      {
        title: "",
        rows: [
          [["Contact Source", "contact_source"], ["Down Payment", "down_payment"]]
        ]
      },
      {
        title: "Contact Information",
        rows: [
          [["First Name", "first_name"], ["Middle Name", "middle_name"], ["Last Name", "last_name"]],
          [["Phone Number", "phone"], ["Email", "email"]],
          [["Street Address", "address1"]],
          [["Address Suite/Apt/Bldg", "address_suiteaptbldg"]],
          [["City", "city"], ["State", "state"], ["Postal Code", "postal_code"]]
        ]
      },
      {
        title: "Identification",
        rows: [
          [["Date of Birth", "date_of_birth"], ["Social Security Number", "social_security_number"]],
          [["ID Type", "id_type"], ["ID Number", "id_number"]],
          [["State ID", "state_id"], ["Id Expiration", "id_expiration"]]
        ]
      },
      {
        title: "Credit Application",
        rows: [
          [["Residence Type", "residence_type"], ["Residence Payment", "residence_payment"]],
          [["Years at Residence", "years_at_residence"], ["Months at Residence", "months_at_residence"]],
          [["Previous Address", "previous_address"]],
          [["Previous Suite/Apt/Bldg", "previous_suiteaptbldg"]],
          [["Previous City", "previous_city"], ["Previous State", "previous_state"], ["Previous Postal Code", "previous_postal_code"]],
          [["Previous Residence Type", "previous_residence_type"], ["Previous Residence Payment", "previous_residence_payment"]],
          [["Previous Residence Years", "previous_residence_years"], ["Previous Residence Months", "previous_residence_months"]],
          [["Employment Status", "employment_status"]],
          [["Employer Name", "employer_name"]],
          [["Job Position", "job_position"], ["Employer Phone Number", "employer_phone_number"]],
          [["Job Years", "job_years"], ["Job Months", "job_months"]],
          [["Income Frequency", "income_frequency"], ["Gross Income", "gross_income"]],
          "separator",
          [["Other Income Source", "other_income_source"], ["Other Income Amount", "other_income_amount"]],
          [["Previous Employment Status", "previous_employment_status"]],
          [["Previous Employer", "previous_employer"]],
          [["Previous Job Position", "previous_job_position"], ["Previous Employer Phone", "previous_employer_phone"]],
          [["Previous Job Years", "previous_job_years"], ["Previous Job Months", "previous_job_months"]]
        ]
      },
      {
        title: "Insurance Information",
        rows: [
          [["Insurance Company", "insurance_company"], ["Insurance Agent", "insurance_agent"], ["Insurance Phone Number", "insurance_phone_number"]],
          [["Insurance Policy Number", "insurance_policy_number"], ["Insurance Deductible", "insurance_deductible"]],
          [["Insurance Address", "insurance_address"]],
          [["Insurance Suite/Apt/Bldg", "insurance_suiteaptbldg"]],
          [["Insurance City", "insurance_city"], ["Insurance State", "insurance_state"], ["Insurance Postal Code", "insurance_postal_code"]],
          [["Insurance Effective Date", "insurance_effective_date"], ["Insurance Expiration Date", "insurance_expiration_date"]]
        ]
      }
    ];

    // --- BUILD FORM ---
    const mainForm = document.getElementById("main-form");
    mainForm.innerHTML = "";

    // --- FETCH FIELD METADATA FOR SOURCE LIST ---
    async function fetchSourceListField(locationId) {
      // Simulate API call; replace with real endpoint if needed
      // Example response:
      // {"id":"...","name":"Source List","fieldKey":"contact.source_list",...,"picklistOptions":[...]}
      // For demo, hardcode as fallback:
      try {
        const resp = await fetch(MAKE_WEBHOOK, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ action: "get_source_list_field", location_id: locationId })
        });
        const data = await resp.json();
        if (data && Array.isArray(data.picklistOptions)) return data.picklistOptions;
      } catch (e) {}
      // fallback options
      return [
        "Carfax","CarGurus","Cars.Com","CarZing","AutoTrader","Facebook Marketplace",
        "Facebook","Showroom Visit","Referral","Friends and Family","Website"
      ];
    }

    // --- Fetch inventory, contact, source list options ---
    const [inventory, unifiedData] = await Promise.all([
      fetchInventory(locationId),
      fetchContactAndSourceList(locationId, contactId)
    ]);
    const apiContactData = Array.isArray(unifiedData.contact) ? unifiedData.contact[0] : (unifiedData.contact || {});
    const sourceListOptions = unifiedData.sourceListField?.picklistOptions || [
      "Carfax","CarGurus","Cars.Com","CarZing","AutoTrader","Facebook Marketplace",
      "Facebook","Showroom Visit","Referral","Friends and Family","Website"
    ];
    const idTypeOptions = unifiedData.idTypeField?.picklistOptions || [
      "Drivers License", "State ID", "Passport", "Other"
    ];

    // Debug: Log raw API data
    console.log("apiContactData (raw from API):", apiContactData);

    // --- Normalize API keys ---
    function normalizeKeys(obj) {
      const out = {};
      for (const k in obj) {
        const norm = k.toLowerCase().replace(/[^a-z0-9]+/g, "_");
        out[norm] = obj[k];
      }
      return out;
    }
    const normalizedApiContactData = normalizeKeys(apiContactData);

    // Map Make.com keys to form field names
    function mapApiToFields(apiData) {
      const map = {
        "vin": "vin",
        "make": "make",
        "model": "model",
        "year": "year",
        "stock": "stock",
        "odometar": "odometar",
        "source": "contact_source",
        "down_payment": "down_payment",
        "address": "address1",
        "address_suiteaptbldg": "address_suiteaptbldg",
        "city": "city",
        "state": "state",
        "postal": "postal_code",
        "id_type": "id_type",
        "id_state": "state_id",
        "id_number": "id_number",
        "id_expiration": "id_expiration",
        "date_of_birth": "date_of_birth",
        "social_security_number": "social_security_number",
        "residence_type": "residence_type",
        "residence_payment": "residence_payment",
        "years_at_residence": "years_at_residence",
        "months_at_residence": "months_at_residence",
        "previous_address": "previous_address",
        "previous_suiteaptbldg": "previous_suiteaptbldg",
        "previous_city": "previous_city",
        "previous_state": "previous_state",
        "previous_postal_code": "previous_postal_code",
        "previous_residence_type": "previous_residence_type",
        "previous_residence_payment": "previous_residence_payment",
        "previous_residence_years": "previous_residence_years",
        "previous_residence_months": "previous_residence_months",
        "employment_status": "employment_status",
        "employer_name": "employer_name",
        "job_position": "job_position",
        "employer_phone_number": "employer_phone_number",
        "job_years": "job_years",
        "job_months": "job_months",
        "income_frequency": "income_frequency",
        "gross_income": "gross_income",
        "other_income_source": "other_income_source",
        "other_income_amount": "other_income_amount",
        "previous_employment_status": "previous_employment_status",
        "previous_employer": "previous_employer",
        "previous_job_position": "previous_job_position",
        "previous_employer_phone": "previous_employer_phone",
        "previous_job_years": "previous_job_years",
        "previous_job_months": "previous_job_months",
        "insurance_company": "insurance_company",
        "insurance_policy": "insurance_policy_number",
        "insurance_agent": "insurance_agent",
        "insurance_phone": "insurance_phone_number",
        "insurance_deductable": "insurance_deductible",
        "insurance_address": "insurance_address",
        "insurance_suiteaptbldg": "insurance_suiteaptbldg",
        "insurance_city": "insurance_city",
        "insurance_state": "insurance_state",
        "insurance_postal_code": "insurance_postal_code",
        "insurance_effective_date": "insurance_effective_date",
        "insurance_expiration_date": "insurance_expiration_date",
        "first_name": "first_name",
        "middle_name": "middle_name",
        "last_name": "last_name",
        "phone": "phone",
        "email_address": "email"
      };
      const out = {};
      for (const k in apiData) {
        if (map[k]) out[map[k]] = apiData[k];
      }
      return out;
    }
    const contactData = mapApiToFields(normalizedApiContactData);

    // Debug: Log mapped data
    console.log("contactData (mapped for form):", contactData);

    let selectedVehicle = null;
    if (contactData.stock) {
      selectedVehicle = inventory.find(v => v.stock && String(v.stock).toLowerCase() === String(contactData.stock).toLowerCase());
    }
    if (!selectedVehicle && contactData.vin) {
      selectedVehicle = inventory.find(v => v.vin && String(v.vin).toLowerCase() === String(contactData.vin).toLowerCase());
    }
    if (!selectedVehicle && (contactData.year || contactData.make || contactData.model)) {
      selectedVehicle = {
        year: contactData.year || "",
        make: contactData.make || "",
        model: contactData.model || "",
        trim: contactData.trim || "",
        stock: contactData.stock || "",
        vin: contactData.vin || "",
        miles: contactData.odometar || "",
        featuredPhoto: "",
      };
    }

    // --- Vehicle Card and Search, but now in side card ---
    vehicleRow.innerHTML = "";
    const vehicleInput = document.createElement("input");
    vehicleInput.type = "text";
    vehicleInput.placeholder = "Search for vehicle (year, make, model, stock, VIN...)";
    vehicleInput.className = "vehicle-search-box";
    vehicleRow.appendChild(vehicleInput);
    const { card: vehicleCard, update: updateVehicleCard } = createVehicleCard(selectedVehicle, () => {
      vehicleInput.style.display = vehicleInput.style.display === "block" ? "none" : "block";
      if (vehicleInput.style.display === "block") {
        vehicleInput.focus();
        dropdownInstance.showDropdown();
      }
    });
    vehicleRow.appendChild(vehicleCard);
    const dropdownInstance = createVehicleDropdown(vehicleInput, inventory, vehicle => {
      selectedVehicle = vehicle;
      updateVehicleCard(vehicle);
      ["year", "make", "model", "vin", "stock", "odometar"].forEach(id => {
        const input = document.getElementById(id);
        if (input) input.value = vehicle[id] || vehicle[id === "odometar" ? "miles" : id] || "";
      });
    });

    // --- QR code in side card ---
    sideCard.style.display = "flex";

    // --- Main form ---
    const form = document.createElement("form");
    form.autocomplete = "off";
    fieldGroups.forEach(group => {
      if (group.title) {
        const section = document.createElement("div");
        section.className = "form-section";
        let isCollapsible = (
          group.title === "Credit Application" ||
          group.title === "Insurance Information"
        );
        let contentDiv = section;
        if (isCollapsible) {
          const toggle = document.createElement("div");
          toggle.className = "collapsible-toggle";
          const arrow = document.createElement("span");
          arrow.className = "collapsible-arrow collapsed";
          arrow.innerHTML = "▶";
          toggle.appendChild(arrow);
          const label = document.createElement("span");
          label.innerText = group.title;
          toggle.appendChild(label);
          section.appendChild(toggle);
          contentDiv = document.createElement("div");
          contentDiv.className = "collapsible-content collapsed";
          section.appendChild(contentDiv);
          let collapsed = true;
          toggle.onclick = () => {
            collapsed = !collapsed;
            contentDiv.classList.toggle("collapsed", collapsed);
            arrow.classList.toggle("collapsed", collapsed);
          };
        } else {
          const h = document.createElement("div");
          h.className = "section-title";
          h.innerText = group.title;
          section.appendChild(h);
        }
        group.rows.forEach(row => {
          if (row === "separator") {
            const hr = document.createElement("hr");
            contentDiv.appendChild(hr);
            return;
          }
          const rowDiv = document.createElement("div");
          rowDiv.className = `row`;
          row.forEach(([label, name]) => {
            if (["year","make","model","vin","stock","odometar"].includes(name)) {
              const input = document.createElement("input");
              input.type = "hidden";
              input.id = name;
              input.name = name;
              input.value = selectedVehicle?.[name] || contactData[name] || "";
              rowDiv.appendChild(input);
              return;
            }
            const colType = row.length === 1 ? "col-1" : row.length === 2 ? "col-2" : "col-3";
            const col = document.createElement("div");
            col.className = colType;
            col.innerHTML = `<label for="${name}">${label}</label>`;
            let input;
            if (name === "state_id") {
              input = document.createElement("select");
              input.id = "state_id";
              input.name = "state_id";
              input.innerHTML = `
                <option value="">Select State</option>
                <optgroup label="United States">
                  <option value="AL - Alabama">AL - Alabama</option>
                  <option value="AK - Alaska">AK - Alaska</option>
                  <option value="AZ - Arizona">AZ - Arizona</option>
                  <option value="AR - Arkansas">AR - Arkansas</option>
                  <option value="CA - California">CA - California</option>
                  <option value="CO - Colorado">CO - Colorado</option>
                  <option value="CT - Connecticut">CT - Connecticut</option>
                  <option value="DE - Delaware">DE - Delaware</option>
                  <option value="FL - Florida">FL - Florida</option>
                  <option value="GA - Georgia">GA - Georgia</option>
                  <option value="HI - Hawaii">HI - Hawaii</option>
                  <option value="ID - Idaho">ID - Idaho</option>
                  <option value="IL - Illinois">IL - Illinois</option>
                  <option value="IN - Indiana">IN - Indiana</option>
                  <option value="IA - Iowa">IA - Iowa</option>
                  <option value="KS - Kansas">KS - Kansas</option>
                  <option value="KY - Kentucky">KY - Kentucky</option>
                  <option value="LA - Louisiana">LA - Louisiana</option>
                  <option value="ME - Maine">ME - Maine</option>
                  <option value="MD - Maryland">MD - Maryland</option>
                  <option value="MA - Massachusetts">MA - Massachusetts</option>
                  <option value="MI - Michigan">MI - Michigan</option>
                  <option value="MN - Minnesota">MN - Minnesota</option>
                  <option value="MS - Mississippi">MS - Mississippi</option>
                  <option value="MO - Missouri">MO - Missouri</option>
                  <option value="MT - Montana">MT - Montana</option>
                  <option value="NE - Nebraska">NE - Nebraska</option>
                  <option value="NV - Nevada">NV - Nevada</option>
                  <option value="NH - New Hampshire">NH - New Hampshire</option>
                  <option value="NJ - New Jersey">NJ - New Jersey</option>
                  <option value="NM - New Mexico">NM - New Mexico</option>
                  <option value="NY - New York">NY - New York</option>
                  <option value="NC - North Carolina">NC - North Carolina</option>
                  <option value="ND - North Dakota">ND - North Dakota</option>
                  <option value="OH - Ohio">OH - Ohio</option>
                  <option value="OK - Oklahoma">OK - Oklahoma</option>
                  <option value="OR - Oregon">OR - Oregon</option>
                  <option value="PA - Pennsylvania">PA - Pennsylvania</option>
                  <option value="RI - Rhode Island">RI - Rhode Island</option>
                  <option value="SC - South Carolina">SC - South Carolina</option>
                  <option value="SD - South Dakota">SD - South Dakota</option>
                  <option value="TN - Tennessee">TN - Tennessee</option>
                  <option value="TX - Texas">TX - Texas</option>
                  <option value="UT - Utah">UT - Utah</option>
                  <option value="VT - Vermont">VT - Vermont</option>
                  <option value="VA - Virginia">VA - Virginia</option>
                  <option value="WA - Washington">WA - Washington</option>
                  <option value="WV - West Virginia">WV - West Virginia</option>
                  <option value="WI - Wisconsin">WI - Wisconsin</option>
                  <option value="WY - Wyoming">WY - Wyoming</option>
                </optgroup>
              `;
              // Set value to full value if present
              input.value = contactData[name] || "";
            } else if (name === "id_type") {
              // --- Dynamic ID Type Dropdown ---
              input = document.createElement("select");
              input.id = "id_type";
              input.name = "id_type";
              input.innerHTML = `<option value="">Select ID Type</option>` +
                idTypeOptions.map(opt => `<option value="${opt}">${opt}</option>`).join("");
              input.value = contactData[name] || "";
            } else if (name.toLowerCase().includes("date")) {
              input = document.createElement("input");
              input.type = "date";
              input.id = name;
              input.name = name;
            } else if (name.includes("phone")) {
              input = document.createElement("input");
              input.type = "tel";
              input.id = name;
              input.name = name;
            } else if (name.includes("email")) {
              input = document.createElement("input");
              input.type = "email";
              input.id = name;
              input.name = name;
            } else {
              input = document.createElement("input");
              input.type = "text";
              input.id = name;
              input.name = name;
            }
            input.value = contactData[name] || "";
            col.appendChild(input);
            rowDiv.appendChild(col);
          });
          contentDiv.appendChild(rowDiv);
        });
        form.appendChild(section);
      }
    });

    // --- Source List Dropdown above Vehicle Card ---
    // Insert above vehicleRow in side-card
    const sourceListDiv = document.createElement("div");
    sourceListDiv.style.marginBottom = "10px";
    sourceListDiv.innerHTML = `
      <label for="source_list" style="font-weight:600;display:block;margin-bottom:4px;font-size:13px;">Source List</label>
    `;
    const sourceListSelect = document.createElement("select");
    sourceListSelect.id = "source_list";
    sourceListSelect.name = "source_list";
    sourceListSelect.style.width = "100%";
    sourceListSelect.style.padding = "9px";
    sourceListSelect.style.borderRadius = "7px";
    sourceListSelect.style.border = "1px solid #bbb";
    sourceListSelect.style.fontSize = "15px";
    sourceListSelect.style.background = "#fafcff";
    sourceListSelect.innerHTML = `<option value="">Select Source</option>` +
      sourceListOptions.map(opt => `<option value="${opt}">${opt}</option>`).join("");
    // Set value from API if present
    sourceListSelect.value = apiContactData?.Source || apiContactData?.source_list || "";
    sourceListDiv.appendChild(sourceListSelect);
    vehicleRow.parentNode.insertBefore(sourceListDiv, vehicleRow);

    // --- Save Button ---
    const btnRow = document.createElement("div");
    btnRow.style.display = "flex";
    btnRow.style.alignItems = "center";
    btnRow.style.marginTop = "14px";
    btnRow.style.gap = "18px";
    btnRow.style.width = "100%";
    const saveBtn = document.createElement("button");
    saveBtn.type = "button";
    saveBtn.innerText = "Save";
    saveBtn.style.background = "#186ad6";
    saveBtn.style.marginLeft = "auto";
    const saveStatus = document.createElement("span");
    saveStatus.className = "save-status";
    btnRow.appendChild(saveBtn);
    btnRow.appendChild(saveStatus);
    form.appendChild(btnRow);

    // --- Form Field Masks and Autocomplete ---
    setTimeout(() => {
      setupPhoneMask(document.getElementById("phone"));
      setupPhoneMask(document.getElementById("insurance_phone_number"));
      setupGoogleAutocomplete(
        document.getElementById("address1"),
        document.getElementById("city"),
        document.getElementById("state"),
        document.getElementById("postal_code")
      );
    }, 0);

    // Autofill vehicle hidden fields from selected card
    function updateVehicleFields(vehicle) {
      ["year","make","model","vin","stock","odometar"].forEach(id => {
        const input = document.getElementById(id);
        if (input) input.value = vehicle[id] || vehicle[id === "odometar" ? "miles" : id] || "";
      });
    }
    if (selectedVehicle) updateVehicleFields(selectedVehicle);

    // --- Save Button Logic ---
    saveBtn.onclick = async () => {
      saveBtn.disabled = true;
      saveBtn.innerText = "Saving...";
      saveStatus.innerText = "";
      const formData = {};
      form.querySelectorAll("input, select").forEach(input => {
        if (input.id) {
          // For state_id, always use the full value (e.g., "OH - Ohio")
          if (input.id === "state_id") {
            formData[input.id] = input.value;
          } else {
            formData[input.id] = input.value;
          }
        }
      });
      // Add Source List field from side panel
      formData["source_list"] = sourceListSelect.value;
      if (selectedVehicle) {
        ["year","make","model","vin","stock","odometar"].forEach(id => {
          formData[id] = selectedVehicle[id] || selectedVehicle[id === "odometar" ? "miles" : id] || "";
        });
      }
      const payload = {
        action: "credit_app_update_contact",
        contact_id: contactId,
        location_id: locationId,
        ...formData
      };
      try {
        const resp = await fetch(UPDATE_CONTACT_WEBHOOK, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });
        if (resp.ok) {
          saveStatus.innerText = "Saved";
          saveStatus.style.color = "#28a745";
        } else {
          saveStatus.innerText = "Error saving";
          saveStatus.style.color = "#d00";
        }
      } catch (e) {
        saveStatus.innerText = "Error saving";
        saveStatus.style.color = "#d00";
      }
      saveBtn.disabled = false;
      saveBtn.innerText = "Save";
      setTimeout(() => {
        saveStatus.innerText = "";
        saveStatus.style.color = "#28a745";
      }, 2500);
    };

    mainForm.appendChild(form);

    // --- Credit Reports Section ---
    // Insert after Document Uploads in sideCard
    const creditSectionTitle = document.createElement("div");
    creditSectionTitle.className = "side-section-title";
    creditSectionTitle.innerText = "Credit Reports";
    const creditSection = document.createElement("div");
    creditSection.id = "credit-reports-section";
    creditSection.style.display = "flex";
    creditSection.style.flexDirection = "column";
    creditSection.style.gap = "12px";
    creditSection.style.marginBottom = "10px";
    // Scroll controls
    const scrollControls = document.createElement("div");
    scrollControls.style.display = "flex";
    scrollControls.style.justifyContent = "center";
    scrollControls.style.gap = "10px";
    scrollControls.style.margin = "4px 0";
    // Cards container
    const cardsContainer = document.createElement("div");
    cardsContainer.style.display = "flex";
    cardsContainer.style.flexDirection = "column";
    cardsContainer.style.gap = "10px";
    cardsContainer.style.maxHeight = "220px";
    cardsContainer.style.overflow = "hidden";
    // Message if no records
    const noReportsMsg = document.createElement("div");
    noReportsMsg.style.fontSize = "15px";
    noReportsMsg.style.color = "#888";
    noReportsMsg.style.textAlign = "center";
    noReportsMsg.style.margin = "12px 0 0 0";
    // Run New Credit Report button
    const runNewBtn = document.createElement("button");
    runNewBtn.type = "button";
    runNewBtn.innerText = "Run New Credit Report";
    runNewBtn.style.margin = "10px auto 0 auto";
    runNewBtn.style.display = "block";
    runNewBtn.style.background = "#186ad6";
    runNewBtn.style.fontWeight = "600";
    runNewBtn.onclick = () => {
      // Placeholder: implement actual logic as needed
      alert("Run New Credit Report clicked!");
    };

    // Helper to render credit report cards
    function renderCreditCards(reports, startIdx) {
      cardsContainer.innerHTML = "";
      const visible = reports.slice(startIdx, startIdx + 2);
      visible.forEach(r => {
        const card = document.createElement("div");
        card.style.background = "#f6faff";
        card.style.border = "1.5px solid #bfd6ed";
        card.style.borderRadius = "8px";
        card.style.padding = "12px 14px";
        card.style.boxShadow = "0 1px 7px rgba(0,0,0,0.06)";
        card.style.fontSize = "15px";
        card.style.display = "flex";
        card.style.flexDirection = "column";
        card.style.gap = "4px";
        // Example fields: date, bureau, score, status
        card.innerHTML = `
          <div style="font-weight:600;font-size:15.5px;">
            ${r.bureau || "Bureau"} • ${r.date || ""}
          </div>
          <div>Score: <b>${r.score || "N/A"}</b></div>
          <div>Status: <span style="color:${r.status === "Approved" ? "#28a745" : "#d00"}">${r.status || "N/A"}</span></div>
        `;
        cardsContainer.appendChild(card);
      });
    }

    // Fetch and render credit reports
    async function fetchAndRenderCreditReports() {
      // Placeholder: replace with your actual API call
      // Example: const resp = await fetch(...); const reports = await resp.json();
      // For demo, use mock data:
      let reports = [];
      try {
        const resp = await fetch(MAKE_WEBHOOK, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ action: "get_credit_reports", contact_id: contactId })
        });
        reports = await resp.json();
        if (!Array.isArray(reports)) reports = [];
      } catch (e) {
        reports = [];
      }

      // For demo, fallback to mock data if empty
      // reports = reports.length ? reports : [
      //   { bureau: "TransUnion", date: "2024-06-01", score: 710, status: "Approved" },
      //   { bureau: "Equifax", date: "2024-05-15", score: 690, status: "Denied" },
      //   { bureau: "Experian", date: "2024-04-10", score: 720, status: "Approved" }
      // ];

      let startIdx = 0;
      function updateCards() {
        cardsContainer.innerHTML = "";
        scrollControls.innerHTML = "";
        if (!reports.length) {
          noReportsMsg.innerText = "No Credit Reports found";
          noReportsMsg.style.display = "block";
          cardsContainer.style.display = "none";
        } else {
          noReportsMsg.style.display = "none";
          cardsContainer.style.display = "flex";
          renderCreditCards(reports, startIdx);
          if (reports.length > 2) {
            // Scroll up
            const upBtn = document.createElement("button");
            upBtn.type = "button";
            upBtn.innerText = "▲";
            upBtn.style.fontSize = "18px";
            upBtn.disabled = startIdx === 0;
            upBtn.onclick = () => {
              if (startIdx > 0) {
                startIdx -= 1;
                updateCards();
              }
            };
            // Scroll down
            const downBtn = document.createElement("button");
            downBtn.type = "button";
            downBtn.innerText = "▼";
            downBtn.style.fontSize = "18px";
            downBtn.disabled = startIdx + 2 >= reports.length;
            downBtn.onclick = () => {
              if (startIdx + 2 < reports.length) {
                startIdx += 1;
                updateCards();
              }
            };
            scrollControls.appendChild(upBtn);
            scrollControls.appendChild(downBtn);
          }
        }
      }

      updateCards();
    }

    // Insert credit section after uploads
    setTimeout(() => {
      const uploads = sideCard.querySelectorAll(".upload-block");
      const lastUpload = uploads[uploads.length - 1];
      lastUpload.parentNode.insertBefore(creditSectionTitle, lastUpload.nextSibling);
      lastUpload.parentNode.insertBefore(creditSection, creditSectionTitle.nextSibling);
      creditSection.appendChild(cardsContainer);
      creditSection.appendChild(noReportsMsg);
      creditSection.appendChild(scrollControls);
      creditSection.appendChild(runNewBtn);
      fetchAndRenderCreditReports();
    }, 0);
  })();
  </script>
</body>
</html>


