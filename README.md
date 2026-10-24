# Student / Employee ID Card Generator in PHP

A secure, beautiful **ID Card Generator** built with **Core PHP + MySQL** (no framework). Add a student or employee with their photo, then generate a ready-to-use ID card — preview it, download it as a PNG, or print it.

## Features

- **Add / Edit / Delete ID Cards** — full details: name, class/grade or role, ID number, email, phone, address, date of birth, expiry date and photo
- **Beautiful ID Card Design** — modern gradient card with photo, all details, validity badge (Valid / Expired) and signature line
- **Download as PNG** — one click converts the card to a PNG image (dom-to-image), Print button with print-friendly layout
- **Instant Search** — find a card by its ID Card No, plus server-side search & pagination on the list page
- **Duplicate Protection** — an ID Card No can never be added twice (checked in code + UNIQUE key in the database)
- **Secure Photo Upload** — JPG/PNG/WEBP only, 2 MB limit, random file names, upload folder blocks PHP execution
- **Modern Responsive UI** — Warm Paper & Deep Teal design, sidebar dashboard, stat cards, works on mobile

## Setup (2 minutes)

1. Copy this project into `htdocs` (XAMPP) / `www` (WAMP) / `public_html` (hosting).
2. Open **phpMyAdmin → Import** and import `database/id_card.sql` (creates the `id_card` database and the `cards` table — no personal data included).
3. If your MySQL setup is not `root` with an empty password, update `config.php`.
4. Open the project in your browser, click **Add New Card**, fill the form, then **Generate ID Card**.

> **Upgrading from the old version?** Your existing `id_card` database keeps working — database, table and column names are unchanged. The new `database/id_card.sql` is only needed for fresh installs.
>
> **Note:** this tool has no login system by design (it is a single-purpose generator). If you put it on a public server, protect the folder with your hosting's password-protection / `.htaccess` authentication so only your staff can open it.

## Project Structure

```
config.php                  Database connection (mysqli, utf8mb4)
includes/functions.php      Session, CSRF, flash messages, secure upload, validation
includes/card-form.php      Shared Add / Edit form
header.php / footer.php     App layout (sidebar, topbar)
index.php                   Cards list + search + pagination + stats
add.php                     Add a new card (with photo)
edit.php                    Edit every field of a card (photo preserved)
delete.php                  Delete a card (POST + CSRF only, removes photo file)
id-card.php                 Generate / preview / download / print the ID card
assets/css/style.css        Complete modern stylesheet (no Bootstrap/jQuery needed)
assets/js/app.js            Alerts + delete confirmation
assets/images/              Logo & card artwork
assets/uploads/             Uploaded photos (PHP execution blocked by .htaccess)
database/id_card.sql        Clean database schema
```

## Bugs & security fixes in this update (2026)

This project was originally written in 2022 as a learning project. This update fixes the serious issues found in the old code — **please upgrade**:

| # | Issue in old code | Fix |
|---|---|---|
| 1 | **SQL injection everywhere** — delete via `index.php?delete=$sno`, update, insert and card search all pasted raw input into SQL | Every query is a prepared statement |
| 2 | **Every saved email got a stray `]` appended** — the INSERT used `'$email]'` (you can see `rishi@gmail.com]` in the old data dump) | Email is stored exactly as typed, and validated |
| 3 | **Class/Grade was silently lost** — the form collected it but the INSERT never saved the `grade` column | Grade is saved and shown on the card |
| 4 | **Stored XSS** — names and other fields printed raw into the list and onto the ID card | All output escaped with `htmlspecialchars` (`e()` helper) |
| 5 | **Unrestricted file upload** — any file type, original name, public folder (PHP shell = server takeover); a failed upload still inserted the record | Images only, 2 MB, random names, `.htaccess` blocks PHP in uploads; failed upload stops the save |
| 6 | **Delete worked via a GET link** with no CSRF protection | Delete is POST-only with CSRF token + confirmation, and removes the photo file |
| 7 | **The card printed junk text** — every address got "this is the final long address" appended, and every person was labelled "Web Developer" | Real address only; the card subtitle is the person's actual class/role |
| 8 | **Email & DOB were collected but never shown** on the generated card; a wrong ID search showed a silent empty box | Card now shows email + DOB, expiry state (Valid/Expired), and a clear "No ID card found" message |
| 9 | **Broken/missing assets** — navbar used `assets/images/codingcush-logo.png` and `css/dashboard.css`, neither exists in the repo; favicon path was wrong too | Uses the real `assets/images/logo.jpg`; the missing stylesheet link is gone |
| 10 | **Edit could only change name + ID number** (small modal) | Full edit page for every field, photo preserved when not replaced |
| 11 | Database errors were echoed to visitors (`mysqli_error()`), leaking server details | Generic error messages only |
| 12 | SQL dump shipped a real person's data, on **MyISAM + latin1** (breaks many names), with `dob`/`exp_date` as text | Clean schema-only dump: InnoDB + utf8mb4, real DATE columns, UNIQUE ID number |
| 13 | No input validation | Server-side validation: email format, phone, dates, required fields, address length |

The UI was also rebuilt: the old version mixed Bootstrap 4 + DataTables CDN scripts; the new UI is one self-contained stylesheet with a modern sidebar dashboard.

## Requirements

- PHP 8.0+ with `mysqli` extension
- MySQL 5.7+ / MariaDB 10.3+
- Apache (XAMPP / WAMP / LAMP / shared hosting)

## Author

**Lavkush Kumar** — [Coding Cush](https://codingcush.com) · [YouTube](https://www.youtube.com/@CodingCush)

## License

Free to use for learning and your own projects. A product of Coding Cush.
