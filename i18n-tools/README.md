# Spanish language pack build pipeline

osTicket's own `manage.php i18n build` command requires a Crowdin API key we
don't have. This directory builds `include/i18n/es.phar` locally instead,
using osTicket's own `Translation::buildHashFile()` so the runtime format
matches exactly.

## Rebuild / extend the translation

1. Build the isolated builder image (has PHP, gettext, phar):
   ```
   docker build -t devlov-i18n-builder i18n-tools/
   ```
2. Extract translatable strings from the current source (writes new/changed
   `__()` calls to stdout — diff against `messages.po` to find missing ones):
   ```
   docker run --rm -v "$PWD:/src" devlov-i18n-builder php extract.php
   ```
3. Add/edit `msgid`/`msgstr` pairs in `messages.po`.
4. Compile and build the phar (run inside a container with the repo copied
   in — see the main repo's `gotcha_osticket_docker_eval.md`/`gotcha_osticket_reskin.md`
   memory files for the exact container workflow used originally):
   ```
   msgfmt -o messages.mo messages.po
   php -d phar.readonly=0 build_phar.php   # needs include/class.translation.php from the repo root
   ```
5. Copy the resulting `es.phar` into `include/i18n/es.phar`.
6. **Restart Apache/PHP-FPM after deploying** — the phar gets cached in
   memory by running workers (see `gotcha_osticket_phar_cache.md`).

## Content that ISN'T in the phar

Several user-visible strings are stored as **data in the database**, not as
gettext strings, because they're meant to be admin-editable content:
- Landing page welcome text, login banner (`ost_content` table)
- Ticket form field labels/section titles (`ost_form`, `ost_form_field`)
- Ticket status/priority names (`ost_ticket_status`, `ost_ticket_priority` —
  not yet translated as of 2026-08-06)

See `../i18n-content-es.sql` at the repo root for the Spanish content
already applied — re-run it against any fresh database (it's idempotent,
plain `UPDATE`s by id).
