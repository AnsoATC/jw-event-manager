# JW Event Manager

A robust, object-oriented WordPress plugin developed to manage custom events, RSVPs, and automated email notifications.

## Features
* **Custom Post Type & Taxonomy:** Registers an `Event` CPT and an `Event Type` taxonomy.
* **WP-CLI Integration:** Includes custom commands to generate dummy data (`wp jw_event generate --count=10`).
* **Advanced Admin Interface:** Secure Meta Boxes (Nonces, Sanitization) and custom sortable admin columns.
* **Front-End Shortcode:** Use `[jw_events]` to display an advanced filtering system (AJAX-ready structure) querying events by taxonomy and date range.
* **RSVP & Automated Notifications:** Users can confirm attendance. The system triggers emails on event publication and updates.
* **REST API:** Event metadata is exposed via the native WordPress REST API (`/wp/v2/jw_event`).
* **Security & Performance:** Strict data sanitization, OOP architecture, and optimized database queries via WP_Query.

## Installation
1. Upload the `jw-event-manager` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Flush permalinks (Settings > Permalinks > Save Changes) if required.

## Author
Anselme Atchogou