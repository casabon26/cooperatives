@extends('layouts.app')

@section('content')
  <div class="py-4">
    <h1 class="mb-3">About</h1>

    <section class="mt-4">
      <style>
        .mv-wrap { display:flex; gap:1.25rem; align-items:stretch; flex-wrap:wrap; }
        .mv-card { flex:1 1 320px; border-radius:12px; padding:1.25rem; background:linear-gradient(180deg,#fff,#fbfdff); box-shadow:0 8px 24px rgba(15,23,42,0.04); border:1px solid rgba(15,23,42,0.03); }
        .mv-title { font-size:1.1rem; font-weight:800; color:#0f172a; margin-bottom:0.5rem; }
        .mv-sub { color:#2563eb; font-weight:700; display:inline-block; background:rgba(37,99,235,0.06); padding:4px 8px; border-radius:6px; margin-bottom:0.5rem; }
        .mv-text { color:#334155; line-height:1.55; }
        .mv-quote { font-style:italic; color:#0b1220; font-weight:700; margin:0.5rem 0 0 0; }
        @media(max-width:640px){ .mv-wrap{flex-direction:column} }
        .charter-table { width:100%; border-collapse:collapse; margin:1.5rem 0; font-size:0.9rem; }
        .charter-table th { background:#dc2626; color:#fff; padding:0.75rem; text-align:left; font-weight:700; border:1px solid #ccc; }
        .charter-table td { padding:0.75rem; border:1px solid #ddd; }
        .charter-table tr:nth-child(even) { background:#f9fafb; }
        .charter-section-title { font-size:1.25rem; font-weight:900; margin:2rem 0 1rem 0; color:#111827; }
        .charter-subtitle { font-size:0.95rem; color:#555; margin-bottom:0.75rem; }
      </style>

      <div class="mv-wrap">
        <div class="mv-card">
          <div class="mv-title">Mission</div>
          <div class="mv-text">To organize, assist, and empower individuals, cooperatives, livelihood organizatios, and entrepreneurial groups through capability building, livelihood programs, employment, financial assistance, and partnership building.</div>
        </div>

        <div class="mv-card">
          <div class="mv-title">Vision</div>
          <div class="mv-text">A community of productive, progresive, and self-reliant citizens and coopertives managing sustainable enterprise leading to inclusive growth, poverty alleviation, and genuine social change.</div>
        </div>
      </div>
    </section>

    <!-- Citizens Charter Section -->
    <section class="mt-5">
      <h2 class="mb-3">CCLDO CABUYAO CITIZENS CHARTER 2024</h2>
      <p class="text-muted mb-4"><strong>External Services</strong></p>

      <!-- Table 1: REQUEST FOR COOPERATIVE DOCUMENTARY -->
      <div class="charter-section-title">1. REQUEST FOR COOPERATIVE DOCUMENTARY, REPORTORIAL, AND REGULATORY REQUIREMENTS</div>
      <div class="charter-subtitle">Cooperatives may request documents and other essential information for the formulation of cooperative policies, maintenance of operations/affairs, and compliance with the Cooperative Development Authority.</div>
      
      <table class="charter-table">
        <tr><td colspan="1" style="background:#dc2626;color:#fff;font-weight:700;"><strong>OFFICE OR DIVISION</strong></td><td colspan="4" style="background:#f5f5f5;">City Cooperative and Livelihood Development Office – Cooperative Development Division</td></tr>
        <tr><td style="background:#f5f5f5;font-weight:700;"><strong>CLASSIFICATION</strong></td><td colspan="4" style="background:#f5f5f5;">Simple</td></tr>
        <tr><td style="background:#dc2626;color:#fff;font-weight:700;"><strong>TYPE OF TRANSACTION</strong></td><td colspan="4" style="background:#f5f5f5;">G2C</td></tr>
        <tr><td style="background:#dc2626;color:#fff;font-weight:700;"><strong>WHO MAY AVAIL OF THE SERVICE</strong></td><td colspan="4" style="background:#f5f5f5;">Cooperative officers/members in the City of Cabuyao</td></tr>
        <tr style="background:#dc2626;color:#fff;">
          <th style="width:50%;"><strong>CHECKLIST OF REQUIREMENTS</strong></th>
          <th style="width:50%;"><strong>WHERE TO SECURE</strong></th>
        </tr>
        <tr>
          <td>Duly accomplished request form</td>
          <td>Cooperative Division</td>
        </tr>
        <tr style="background:#dc2626;color:#fff;">
          <th style="width:15%;">CLIENT STEPS</th>
          <th style="width:25%;">AGENCY ACTION</th>
          <th style="width:15%;">FEES TO BE PAID</th>
          <th style="width:20%;">PROCESSING TIME</th>
          <th style="width:25%;">PERSONS RESPONSIBLE</th>
        </tr>
        <tr>
          <td><strong>1. Submit the request</strong></td>
          <td>1. Receive request and refer client to concerned personnel.</td>
          <td>None</td>
          <td>1 minute</td>
          <td>Bernadette Aguirre</td>
        </tr>
        <tr>
          <td><strong>2. Undergo an interview</strong></td>
          <td>2. Interview client and print the requested document.</td>
          <td>None</td>
          <td>10 minutes</td>
          <td>Catherine Dela<br>Christian Benedict Bueno</td>
        </tr>
        <tr>
          <td><strong>3. Receive the document</strong></td>
          <td>3. Release the document</td>
          <td>None</td>
          <td>1 minute</td>
          <td>Bernadette Aguirre</td>
        </tr>
        <tr style="background:#dc2626;color:#fff;font-weight:700;">
          <td><strong>TOTAL</strong></td>
          <td></td>
          <td>None</td>
          <td>12 minutes</td>
          <td></td>
        </tr>
      </table>

      <!-- Table 2: REGISTRATION OF AND ASSISTANCE TO NEW COOPERATIVES/LIVELIHOOD ASSOCIATIONS -->
      <div class="charter-section-title">2. REGISTRATION OF AND ASSISTANCE TO NEW COOPERATIVES/LIVELIHOOD ASSOCIATIONS</div>
      <div class="charter-subtitle">Qualified groups of associations may register with the CCLDO to be endorsed to avail of assistance in the national and local government.</div>
      
      <table class="charter-table">
        <tr><td colspan="1" style="background:#dc2626;color:#fff;font-weight:700;"><strong>OFFICE OR DIVISION</strong></td><td colspan="4" style="background:#f5f5f5;">City Cooperative and Livelihood Development Office – Cooperative Development Division</td></tr>
        <tr><td style="background:#f5f5f5;font-weight:700;"><strong>CLASSIFICATION</strong></td><td colspan="4" style="background:#f5f5f5;">Simple</td></tr>
        <tr><td style="background:#dc2626;color:#fff;font-weight:700;"><strong>TYPE OF TRANSACTION</strong></td><td colspan="4" style="background:#f5f5f5;">G2B</td></tr>
        <tr><td style="background:#dc2626;color:#fff;font-weight:700;"><strong>WHO MAY AVAIL OF THE SERVICE</strong></td><td colspan="4" style="background:#f5f5f5;">Citizens of Cabuyao City</td></tr>
        <tr style="background:#dc2626;color:#fff;">
          <th style="width:40%;"><strong>CHECKLIST OF REQUIREMENTS</strong></th>
          <th style="width:40%;"><strong>WHERE TO SECURE</strong></th>
          <th style="width:20%;"></th>
        </tr>
        <tr>
          <td><strong>1. Duly accomplished request form</strong></td>
          <td>Cooperative Development Division/Livelihood Development Division/Administrative Division</td>
          <td></td>
        </tr>
        <tr>
          <td>
            <strong>2. Request letter</strong><br>
            3. Photocopy of CDA Certificate of Registration, Articles of Cooperation, By-Laws<br>
            4. Proof of existence and other supporting documents
          </td>
          <td>Requesting party/group or cooperative</td>
          <td></td>
        </tr>
      </table>

      <table class="charter-table" style="margin-top:2rem;">
        <tr style="background:#dc2626;color:#fff;">
          <th style="width:15%;">CLIENT STEPS</th>
          <th style="width:30%;">AGENCY ACTION</th>
          <th style="width:15%;">FEES TO BE PAID</th>
          <th style="width:20%;">PROCESSING TIME</th>
          <th style="width:20%;">PERSONS RESPONSIBLE</th>
        </tr>
        <tr>
          <td><strong>1. Submit the complete requirements</strong></td>
          <td>1. Receive request and endorse for review/assessment</td>
          <td>None</td>
          <td>1 minute</td>
          <td>Bernadette Aguirre</td>
        </tr>
        <tr>
          <td><strong>2. Undergo an assessment and interview</strong></td>
          <td>2. Interview client and assess qualification/capability</td>
          <td>None</td>
          <td>15 minutes</td>
          <td>Catherine Dela<br>Bea San Juan<br>Digna Peralta<br>Myra Piccuad<br>Christian Benedict Bueno</td>
        </tr>
        <tr>
          <td><strong>3. Receive the endorsement</strong></td>
          <td>3. Approval of request, release of endorsement, and issuance of Certificate of Registration</td>
          <td>None</td>
          <td>3 minutes</td>
          <td>Bea San Juan<br>Myra Piccuad</td>
        </tr>
        <tr style="background:#dc2626;color:#fff;font-weight:700;">
          <td><strong>TOTAL</strong></td>
          <td></td>
          <td>None</td>
          <td>19 minutes</td>
          <td></td>
        </tr>
      </table>

      <!-- Table 3: PROVISION OF TECHNICAL ASSISTANCE -->
      <div class="charter-section-title">3. PROVISION OF TECHNICAL ASSISTANCE, ADVISORY SERVICE, ORGANIZATIONAL SUPPORT, AND CAPABILITY ENHANCEMENT TO INDIVIDUALS/COOPERATIVES/LIVELIHOOD ASSOCIATIONS</div>
      
      <table class="charter-table">
        <tr><td colspan="1" style="background:#dc2626;color:#fff;font-weight:700;"><strong>OFFICE OR DIVISION</strong></td><td colspan="4" style="background:#f5f5f5;">City Cooperative and Livelihood Development Office – Cooperative Development Division</td></tr>
        <tr><td style="background:#f5f5f5;font-weight:700;"><strong>CLASSIFICATION</strong></td><td colspan="4" style="background:#f5f5f5;">Complex</td></tr>
        <tr><td style="background:#dc2626;color:#fff;font-weight:700;"><strong>TYPE OF TRANSACTION</strong></td><td colspan="4" style="background:#f5f5f5;">G2C</td></tr>
        <tr><td style="background:#dc2626;color:#fff;font-weight:700;"><strong>WHO MAY AVAIL OF THE SERVICE</strong></td><td colspan="4" style="background:#f5f5f5;">Citizens of Cabuyao City</td></tr>
        <tr style="background:#dc2626;color:#fff;">
          <th style="width:40%;"><strong>CHECKLIST OF REQUIREMENTS</strong></th>
          <th style="width:40%;"><strong>WHERE TO SECURE</strong></th>
          <th style="width:20%;"></th>
        </tr>
        <tr>
          <td>
            <strong>1. Answering queries on cooperative formation and livelihood intervention</strong><br>
            a. Request letter<br>
            b. Name of client or organized group/organization/association<br>
            c. Briefing/Orientation
          </td>
          <td>NGA or applicable government agency/instrumentality</td>
          <td></td>
        </tr>
        <tr>
          <td>
            <strong>2. Assistance in Conduct of Cooperative Pre-Registration Seminar and/or Pre-Membership Education Seminar</strong><br>
            a. Request letter<br>
            b. Proof of existence of organized group<br>
            c. Attendance to PRS/PMES
          </td>
          <td>NGA or applicable government agency/instrumentality</td>
          <td></td>
        </tr>
        <tr>
          <td>
            <strong>3. Conduct of Mandatory and Special Training for Cooperatives</strong><br>
            a. Request letter<br>
            b. Training design and budgetary proposal
          </td>
          <td>Requesting party</td>
          <td></td>
        </tr>
        <tr>
          <td>
            <strong>4. Technical Assistance and Advisory Support on Renewal of Cooperative Registration</strong><br>
            a. Basic Cooperative Information<br>
            b. BIR submitted financial statement/report<br>
            c. Other documentary requirements
          </td>
          <td>NGA or applicable government agency/instrumentality</td>
          <td></td>
        </tr>
        <tr>
          <td>
            <strong>5. Conduct of Livelihood, Skills, and Entrepreneurial Training</strong><br>
            a. Request letter<br>
            b. Training design and budgetary proposal
          </td>
          <td>Requesting party</td>
          <td></td>
        </tr>
        <tr>
          <td>
            <strong>6. Market Linkage, Organizational Enhancement; Enterprise Development</strong><br>
            a. Request letter<br>
            b. Proof of residency/existence/registration<br>
            c. Project proposal<br>
            d. Product sample
          </td>
          <td>Requesting party / NGA or applicable government agency/instrumentality</td>
          <td></td>
        </tr>
        <tr>
          <td>
            <strong>7. Use of Livelihood Center</strong><br>
            a. Request letter
          </td>
          <td>Requesting party</td>
          <td></td>
        </tr>
      </table>

      <table class="charter-table" style="margin-top:2rem;">
        <tr style="background:#dc2626;color:#fff;">
          <th style="width:15%;">CLIENT STEPS</th>
          <th style="width:30%;">AGENCY ACTION</th>
          <th style="width:15%;">FEES TO BE PAID</th>
          <th style="width:20%;">PROCESSING TIME</th>
          <th style="width:20%;">PERSONS RESPONSIBLE</th>
        </tr>
        <tr>
          <td><strong>1. Submit the request and complete requirements</strong></td>
          <td>1. Receive request and refer client to concerned personnel.</td>
          <td>None</td>
          <td>1 minute</td>
          <td>Bernadette Aguirre</td>
        </tr>
        <tr>
          <td><strong>2. Undergo an interview/assessment</strong></td>
          <td>2. Interview client and assess qualification/capability</td>
          <td>None</td>
          <td>10 minutes</td>
          <td>Catherine Dela<br>Digna Peralta<br>Bea San Juan<br>Myra Piccuad<br>Christian Benedict Bueno</td>
        </tr>
        <tr>
          <td><strong>3. Prepare for conduct and schedule</strong></td>
          <td>3. Arrange schedule and prepare logistical and administrative support for the following:<br>a. Briefing/orientation<br>b. Coop PRS/PMES<br>c. Coop Mandatory and Special Training<br>d. Livelihood, Skills, and Entrepreneurial Training<br>e. Marketing Activity/Trade Fair/Product Development</td>
          <td>None</td>
          <td>5 minutes</td>
          <td>Bea San Juan<br>Myra Piccuad</td>
        </tr>
        <tr>
          <td><strong>4. Attend scheduled activity/assistance/distribution</strong></td>
          <td>4. Conduct of actual training/assistance/distribution</td>
          <td>None</td>
          <td>4 to 24 hours</td>
          <td>Catherine Dela<br>Digna Peralta<br>Bea San Juan</td>
        </tr>
        <tr>
          <td><strong>5. Receive training/assistance/support</strong></td>
          <td>5. Release of assistance/issuance of Certificate of Participation/Completion/Registration</td>
          <td>None</td>
          <td>5 minutes</td>
          <td>Christian Benedict Bueno<br>Bea San Juan<br>Myra Piccuad</td>
        </tr>
        <tr style="background:#dc2626;color:#fff;font-weight:700;">
          <td><strong>TOTAL</strong></td>
          <td></td>
          <td>None</td>
          <td>4 to 24 hours and 21 minutes</td>
          <td></td>
        </tr>
      </table>
    </section>
  </div>
@endsection
