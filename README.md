# JW Event Manager

**Requires at least:** 6.0  
**Tested up to:** 6.9.1  
**Requires PHP:** 8.2  
**License:** GPLv2 or later  

A robust, object-oriented WordPress plugin developed to manage custom events, RSVPs, and automated email notifications. Built specifically for the Jack Westin technical assessment.

## 🚀 Key Features

* **Custom Post Type & Taxonomy:** Registers a secure `jw_event` CPT and an `Event Type` taxonomy.
* **WP-CLI Integration:** Includes custom commands to generate dummy data with taxonomies and meta fields. Run `wp jw_event generate --count=10` to test.
* **Advanced Admin Interface:** Features secure Meta Boxes (utilizing Nonces and Data Sanitization) and custom, sortable admin columns for Date and Location.
* **Front-End Filtering & UI:** Use the `[jw_events]` shortcode to display an advanced, styled filtering system. Users can query events by taxonomy and precise date ranges (Meta Query).
* **RSVP System & Email Notifications:** Secure, nonce-protected RSVP form. The system triggers targeted emails via `wp_mail()` upon event publication (to admin) and event updates (to attendees).
* **REST API Integration:** Event metadata (Date and Location) is seamlessly exposed via the native WordPress REST API at the `/wp-json/wp/v2/jw_event` endpoint.
* **Unit Testing:** Architecture prepared for PHPUnit testing (`/tests` directory).

## 🛠️ Installation & Setup

1. Upload the `jw-event-manager` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Flush permalinks (Settings > Permalinks > Save Changes) to ensure the Custom Post Type URLs route correctly.
4. Place the `[jw_events]` shortcode on any page to render the front-end event list and filtering system.

## 💻 Architecture

The plugin follows a strict Object-Oriented Programming (OOP) paradigm, separating concerns into logical directories:
* `/admin`: Backend interfaces, columns, and meta boxes.
* `/includes`: Core logic, WP-CLI commands, REST API, and Email communications.
* `/public`: Front-end shortcodes and styles.
* `/templates`: Overridable HTML views for single events and archives.

## 👨‍💻 Author
**Anselme Atchogou**