# Faculty Leave Approval System

A professional, end-to-end CodeIgniter 4 (CI4) internal dashboard module designed to manage faculty leave requests entirely through a structured Multi-Role workflow.

---

## 🚀 Key Features & Workflows

### 🔒 Secure Authentication 
- **Session Protection:** Features a premium UI login screen capturing the user and directing them seamlessly.
- **Routing Engine:** Secured endpoints blocking standard Faculty members from accessing the HOD Dashboard URL directly, securely managing isolated UI experiences via CodeIgniter Session Data.

### 👨‍🏫 Faculty Operations
A split-screen `Flexbox` application portal serving as an individual's management zone.
- **Leave Application Gate:**
  - **Sanitization:** Active Regex blocking numeric values and special characters strictly from typing dynamically in the name field.
  - **Memory Limits:** Fixed character ceilings (`20 chars`) on codes and names.
  - **Chronological Date Engine:** Blocks attempting to select past disabled dates dynamically. Automatically updates minimum End-Date constraints based identically on the Start-Date choice.
- **My Leave History DataGrid:**
  - A clean right-side overview displaying instantly updated live status tags (`Pending`, `Approved`, `Rejected`).
  - **Action Block (Edit & Delete):** If an entry is `Pending`, Faculty retain the power to edit data (JavaScript dynamically repopulates and transforms the submission form flawlessly) or directly `Cancel / Delete` their queued submission using an interactive `SweetAlert` dialogue.

### 👨‍💼 HOD (Head of Department) Processing
A master tabular list overview screen dedicated solely to managerial oversight.
- Features a complete view sorting Faculty Names, precise Employee limits, and dates.
- **Instant Resolution Action Block:** Grants the HOD exclusive clearance to fire instant AJAX executions locking a request into an `Approved` or `Rejected` state globally without requiring heavy page reloads.

### 🛡️ Status Integrity Engine
- **Data Hardening:** The moment the HOD issues a final directive (Approved/Rejected), the system triggers an irremovable integrity lock.
- **Locked UI State:** Removes all Edit and Cancel buttons globally from the Faculty DataGrid and triggers a visual gray `🔒 Locked` badge, rendering the submission physically finalized on both the Frontend and the Backend.

---

## 💻 Technology Stack

* **Backend Environment:** PHP CodeIgniter 4 (MVC pattern)
* **Datastore Structure:** MySQL 
* **Design Guidelines:** Clean Vanilla Custom CSS built identically using modern SaaS scaling models (`Flex` + Global CSS Variables & Hover Shadows).
* **Interactivity Engine:** jQuery 3.6 + `SweetAlert2` featuring a custom Glassmorphic backdrop-filter pipeline.
* **Iconography:** FontAwesome 6.4
