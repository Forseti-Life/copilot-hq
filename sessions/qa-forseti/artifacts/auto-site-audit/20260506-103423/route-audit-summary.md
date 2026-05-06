# Route audit summary

- Base URL: https://forseti.life
- Routes checked: 154

## Admin routes returning 200 (potential ACL bug)
- None

## API routes with errors (>=400)
| Status | Module | Route | Path | URL |
|---:|---|---|---|---|
| 404 | job_hunter | job_hunter.google_jobs_details | `/jobhunter/api/googlejobs/details/{job_name}` | https://forseti.life/jobhunter/api/googlejobs/details/test |
| 404 | job_hunter | job_hunter.google_jobs_search_api | `/jobhunter/api/googlejobs/search` | https://forseti.life/jobhunter/api/googlejobs/search |

## Other non-admin route errors (>=400)
| Status | Module | Route | Path | URL |
|---:|---|---|---|---|
| 404 | forseti_content | forseti_content.contact | `/contact` | https://forseti.life/contact |
| 404 | forseti_content | forseti_content.how_it_works | `/how-it-works` | https://forseti.life/how-it-works |
| 404 | forseti_safety_content | forseti.how_it_works | `/how-it-works` | https://forseti.life/how-it-works |
| 404 | job_hunter | job_hunter.add | `/jobhunter/applications/add` | https://forseti.life/jobhunter/applications/add |
| 404 | job_hunter | job_hunter.addposting | `/jobhunter/addposting` | https://forseti.life/jobhunter/addposting |
| 404 | job_hunter | job_hunter.analytics | `/jobhunter/analytics` | https://forseti.life/jobhunter/analytics |
| 404 | job_hunter | job_hunter.application_notes_load | `/jobhunter/jobs/{job_id}/notes` | https://forseti.life/jobhunter/jobs/1/notes |
| 404 | job_hunter | job_hunter.application_status | `/jobhunter/jobs/{job_id}/application-status` | https://forseti.life/jobhunter/jobs/1/application-status |
| 404 | job_hunter | job_hunter.application_submission | `/jobhunter/application-submission` | https://forseti.life/jobhunter/application-submission |
| 404 | job_hunter | job_hunter.application_submission_job | `/jobhunter/application-submission/{job_id}` | https://forseti.life/jobhunter/application-submission/1 |
| 404 | job_hunter | job_hunter.application_submission_step2 | `/jobhunter/application-submission/{job_id}/resolve-redirect-chain` | https://forseti.life/jobhunter/application-submission/1/resolve-redirect-chain |
| 404 | job_hunter | job_hunter.application_submission_step3 | `/jobhunter/application-submission/{job_id}/identify-auth-path` | https://forseti.life/jobhunter/application-submission/1/identify-auth-path |
| 404 | job_hunter | job_hunter.application_submission_step4 | `/jobhunter/application-submission/{job_id}/create-account` | https://forseti.life/jobhunter/application-submission/1/create-account |
| 404 | job_hunter | job_hunter.application_submission_step5 | `/jobhunter/application-submission/{job_id}/submit-application` | https://forseti.life/jobhunter/application-submission/1/submit-application |
| 404 | job_hunter | job_hunter.application_submission_step5_screenshot | `/jobhunter/application-submission/{job_id}/screenshot/{filename}` | https://forseti.life/jobhunter/application-submission/1/screenshot/test |
| 404 | job_hunter | job_hunter.application_submission_step_stub | `/jobhunter/application-submission/{job_id}/step/{step}` | https://forseti.life/jobhunter/application-submission/1/step/1 |
| 404 | job_hunter | job_hunter.applications_dashboard | `/jobhunter/applications` | https://forseti.life/jobhunter/applications |
| 404 | job_hunter | job_hunter.bulk_actions | `/jobhunter/bulk-actions` | https://forseti.life/jobhunter/bulk-actions |
| 404 | job_hunter | job_hunter.bulk_import_companies | `/jobhunter/bulk-import-companies` | https://forseti.life/jobhunter/bulk-import-companies |
| 404 | job_hunter | job_hunter.companies_list | `/jobhunter/companies/list` | https://forseti.life/jobhunter/companies/list |
| 404 | job_hunter | job_hunter.company_add | `/jobhunter/companies/add` | https://forseti.life/jobhunter/companies/add |
| 404 | job_hunter | job_hunter.company_delete | `/jobhunter/companies/{company_id}/delete` | https://forseti.life/jobhunter/companies/1/delete |
| 404 | job_hunter | job_hunter.company_edit | `/jobhunter/companies/{company_id}/edit` | https://forseti.life/jobhunter/companies/1/edit |
| 404 | job_hunter | job_hunter.company_interest_form | `/jobhunter/companies/{company_id}/interest` | https://forseti.life/jobhunter/companies/1/interest |
| 404 | job_hunter | job_hunter.company_job_discovery | `/jobhunter/job-discovery/company/{company}` | https://forseti.life/jobhunter/job-discovery/company/test |
| 404 | job_hunter | job_hunter.company_research | `/jobhunter/companyresearch` | https://forseti.life/jobhunter/companyresearch |
| 404 | job_hunter | job_hunter.company_research_form | `/jobhunter/companies/{company_id}/research` | https://forseti.life/jobhunter/companies/1/research |
| 404 | job_hunter | job_hunter.company_research_list | `/jobhunter/companies` | https://forseti.life/jobhunter/companies |
| 404 | job_hunter | job_hunter.company_watchlist | `/jobhunter/companies/my-list` | https://forseti.life/jobhunter/companies/my-list |
| 404 | job_hunter | job_hunter.contacts_add | `/jobhunter/contacts/add` | https://forseti.life/jobhunter/contacts/add |
| 404 | job_hunter | job_hunter.contacts_edit | `/jobhunter/contacts/{contact_id}/edit` | https://forseti.life/jobhunter/contacts/1/edit |
| 404 | job_hunter | job_hunter.contacts_list | `/jobhunter/contacts` | https://forseti.life/jobhunter/contacts |
| 404 | job_hunter | job_hunter.cover_letter | `/jobhunter/coverletter/{job_id}` | https://forseti.life/jobhunter/coverletter/1 |
| 404 | job_hunter | job_hunter.credentials | `/jobhunter/settings/credentials` | https://forseti.life/jobhunter/settings/credentials |
| 404 | job_hunter | job_hunter.dashboard | `/jobhunter` | https://forseti.life/jobhunter |
| 404 | job_hunter | job_hunter.deadlines | `/jobhunter/deadlines` | https://forseti.life/jobhunter/deadlines |
| 404 | job_hunter | job_hunter.documentation | `/jobhunter/documentation` | https://forseti.life/jobhunter/documentation |
| 404 | job_hunter | job_hunter.documentation.architecture | `/jobhunter/documentation/architecture` | https://forseti.life/jobhunter/documentation/architecture |
| 404 | job_hunter | job_hunter.documentation.faq | `/jobhunter/documentation/faq` | https://forseti.life/jobhunter/documentation/faq |
| 404 | job_hunter | job_hunter.documentation.google_jobs | `/jobhunter/documentation/google-jobs-integration` | https://forseti.life/jobhunter/documentation/google-jobs-integration |

(Truncated: 97 rows)
