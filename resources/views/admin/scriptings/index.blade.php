@extends('layouts.admin')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@latest/dist/tagify.css">
    <style>
        .show_values {
            cursor: pointer;
        }

        .credit-btn {
            background-color: #42526E;
            color: #fff;
            margin-right: 5px;
        }

        .custom-deal-dropdown {
            position: absolute !important;
            background: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            padding: 12px;
            border-radius: 10px;
            min-width: 360px;
            max-width: 420px;
            z-index: 9999;
            font-family: "Arial, sans-serif";
        }

        .create-credit-btn {
            margin-top: 6px;
            width: 100%;
            font-weight: bold;
            border-radius: 6px;
            padding: 6px 0;
        }
    </style>
@endpush
@section('content')
    <div class="container ">
        <div class="row">
            <div class="col-md-12 mt-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between" id="toolbar-contact-buttons">
                        <h4 class="h4">Scripts</h4>
                        <button class="btn btn-primary add-script">Add More</button>
                    </div>
                    <div class="card-body">

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (async () => {
            const NOLC_API = "http://localhost:8000/api/credit-report/list";
            const NOLC_TOKEN =
                "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySWQiOjEsImVtYWlsIjoic2Fzb0BzdGFyYXV0b2NybS5jb20iLCJwcm9qZWN0Ijoic3RhcmF1dG8iLCJ0eXBlIjoiQVBJIiwiaWF0IjoxNzQ0NDcyNzYwfQ.qC_1EnZnDefRVFP-MA3DtGKQUV93hZa3zD8qHtN-U40";
            const PLACEHOLDER_REPORT_URL = "#";

            function extractContactAndLocation() {
                return {
                    contactId: '7SA83zs0ZPRJaphlB77c',
                    locationId: 'geAOl3NEW1iIKIWheJcj'
                };

                // const locationMatch = window.location.href.match(/\/location\/([^/]+)/);
                // const contactMatch = window.location.href.match(/\/contacts\/([^/?#]+)/);
                // const button = document.querySelector("button[link*='contactId=']");
                // return {
                //   contactId: button?.getAttribute("link")?.match(/contactId=([^&]+)/)?.[1] || contactMatch?.[1] || null,
                //   locationId: button?.getAttribute("link")?.match(/locationId=([^&]+)/)?.[1] || locationMatch?.[1] || (window.HL?.getLocationId?.() || null)
                // };
            }

            async function fetchCreditReports(contactId, locationId) {
                const query = {
                    query: `
        query {
          creditReportsCollection(
            where: {
              highLevelClientId: { equals: "${contactId}" },
              dealershipSubAccountIdVal: { equals: "${locationId}" }
            },
            first: 50
          ) {
            edges {
              node {
                uuid
                id
                createdAt
                fullName { first last }
                creditBureau
                user
                score
              }
            }
          }
        }
      `
                };

                const res = await fetch(NOLC_API, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        contactId: contactId,
                        locationId: locationId
                    })
                });
                console.log(res);

                const data = await res.json();

                let d = data?.data?.creditReportsCollection?.edges?.map(edge => edge.node) || [];
                console.log(d);
                return d;
            }

            function openMoparOrPopup(url, title = "Credit Report") {
                if (window.HL?.openWidget) {
                    window.HL.openWidget("iframe", {
                        src: url,
                        title,
                        preferredWidth: 1200
                    });
                } else {
                    const w = 1000,
                        h = 800;
                    const left = (screen.width - w) / 2;
                    const top = (screen.height - h) / 2;
                    window.open(url, title, `width=${w},height=${h},top=${top},left=${left}`);
                }
            }

            function createCreditReportDropdown(reports, contactId, contactDetails) {
                const dropdown = document.createElement("div");
                dropdown.className = "custom-deal-dropdown";
                const newBtn = document.createElement("button");
                newBtn.textContent = "+ Run New Credit Report";
                newBtn.className = "btn btn-sm btn-primary create-credit-btn";
                newBtn.onclick = () => {
                    alert("Redirect to 'Run New Credit Report'");
                };


                if (!reports.length) {
                    dropdown.innerHTML = `<div style="font-size:14px; color:#777;">No credit reports found.</div>`;
                    dropdown.appendChild(newBtn);

                    return dropdown;
                }

                // Group reports by createdAt timestamp
                const groupedReports = {};
                reports.forEach(report => {
                    const key = report.createdAt + "_" + report.fullName?.first + report.fullName?.last;
                    groupedReports[key] = groupedReports[key] || [];
                    groupedReports[key].push(report);
                });

                const bureauIcons = {
                    EQUIFAX: "https://storage.googleapis.com/msgsndr/ZWry4bE66uaqlPng8WjQ/media/67fd3bd70f63bf775e29981b.png",
                    TRANS_UNION: "https://storage.googleapis.com/msgsndr/ZWry4bE66uaqlPng8WjQ/media/67fd3bf7a119410b40c5b672.png",
                    EXPERIAN: "https://storage.googleapis.com/msgsndr/ZWry4bE66uaqlPng8WjQ/media/67fd3bd771384b4077b578d1.png"
                };

                Object.values(groupedReports).forEach(group => {
                    const mainReport = group[0];
                    const name = `${mainReport.fullName?.first || ""} ${mainReport.fullName?.last || ""}`
                        .trim();
                    const date = new Date(mainReport.createdAt);
                    const isRecent = Date.now() - date.getTime() <= 30 * 86400000;
                    const formattedDate = date.toLocaleString();

                    const bureauScoreHTML = group.map(report => {
                        const icon = bureauIcons[report.creditBureau];
                        return icon ? `
                                <span style="display:flex; align-items:center; gap:4px;">
                                  <img src="${icon}" alt="${report.creditBureau}" style="width:16px; height:16px;" />
                                  <span style="font-size:11px; font-weight:500;">${report.score || "N/A"}</span>
                                </span>
                            ` : '';
                    }).join(`<span style="margin: 0 6px; color: #ccc;">|</span>`);

                    const card = document.createElement("div");
                    Object.assign(card.style, {
                        marginBottom: "14px",
                        padding: "10px",
                        border: "1px solid #eee",
                        borderRadius: "8px",
                        backgroundColor: isRecent ? "#e6f7ff" : "#fafafa",
                        fontSize: "12px"
                    });

                    card.innerHTML = `
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
          <div style="flex-grow:1;">
            <div style="display:flex; gap:6px; flex-wrap:wrap; font-weight:600;">
              <span>${name}</span>
              ${isRecent ? `<span style="font-size:10px; color:green; background:#e0f5e9; padding:1px 6px; border-radius:4px;">🟢 Recent</span>` : ""}
            </div>
            <div style="display:flex; gap:8px; margin:4px 0;">${bureauScoreHTML}</div>
            <div style="font-size:11px; color:#555;">
              <strong>Ran by:</strong> ${mainReport.user || "N/A"} at ${formattedDate}
            </div>
          </div>
          <div class="view-report" style="cursor:pointer; padding-left:10px;" title="View Report">
            <i class="fas fa-eye" style="font-size:15px; color:#2c5fdb;">View</i>
          </div>
        </div>
      `;

                    card.querySelector(".view-report").onclick = () => {
                        openMoparOrPopup(`${PLACEHOLDER_REPORT_URL}?id=${mainReport.uuid}`,
                            "View Credit Report");
                        dropdown.remove();
                    };

                    dropdown.appendChild(card);
                });

                dropdown.appendChild(newBtn);

                return dropdown;
            }

            function injectCreditButton(selector) {
                const container = document.querySelector(selector);
                if (!container || container.querySelector("#creditDropdownBtn")) return;

                const btn = document.createElement("button");
                btn.id = "creditDropdownBtn";
                btn.className = "btn btn-sm credit-btn";
                btn.innerHTML = `<i class="fas fa-file-alt"></i> <span>Credit Report</span>`;

                btn.onclick = async (e) => {
                    e.stopPropagation();
                    document.querySelector(".custom-deal-dropdown")?.remove();

                    btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> <span>Loading...</span>`;
                    btn.disabled = true;

                    try {
                        const {
                            contactId,
                            locationId
                        } = extractContactAndLocation();
                        if (!contactId || !locationId) return alert("Missing contact or location ID");

                        const reports = await fetchCreditReports(contactId, locationId);
                        reports.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));

                        const dropdown = createCreditReportDropdown(reports, contactId, {});
                        const rect = btn.getBoundingClientRect();
                        dropdown.style.top = `${rect.bottom + window.scrollY}px`;
                        dropdown.style.left = `${rect.left + window.scrollX}px`;
                        document.body.appendChild(dropdown);

                        document.addEventListener("click", (event) => {
                            if (!dropdown.contains(event.target) && event.target !== btn) dropdown
                                .remove();
                        }, {
                            once: true
                        });
                    } catch (err) {
                        console.error("Error:", err);
                        alert("Something went wrong.");
                    } finally {
                        btn.innerHTML = `<i class="fas fa-file-alt"></i> <span>Credit Report</span>`;
                        btn.disabled = false;
                    }
                };

                container.appendChild(btn);
            }

            const observeArea = (selector, callback) => {
                new MutationObserver(() => {
                    if (document.querySelector(selector) && !document.querySelector(
                            "#creditDropdownBtn")) {
                        callback();
                    }
                }).observe(document.body, {
                    childList: true,
                    subtree: true
                });
            };

            observeArea("#toolbar-contact-buttons", () => injectCreditButton("#toolbar-contact-buttons"));
            //   observeArea("#contact-buttons", () => injectCreditButton("#contact-buttons"));
        })();
    </script>
@endpush
