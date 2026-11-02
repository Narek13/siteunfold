# 5. Name: Siteunfold – Convert Your Site to Multisite

- **Status:** Accepted
- **Date:** 2026-11-02

## Context
The WordPress.org review pended the first submission, "One Click Multisite" (slug `one-click-multisite`): the name is a generic descriptive phrase with no distinctive identifier, and "Multisite" was flagged as a potential trademark. The reviewers ask for a distinctive term at the start and any project name at the end, after a word like "for" or "to". Coldstash went through the same review ("Order Archive for WooCommerce" became "Coldstash – Archive Old Orders for WooCommerce").

## Decision
Display name **Siteunfold – Convert Your Site to Multisite**; slug, text domain and code prefix `siteunfold` / `Siteunfold\` / `SITEUNFOLD_`. "Siteunfold" is a coined word (a site unfolding into a network), and the description says what the plugin does, with "Multisite" at the end.

Checked before choosing (2026-11-02): slug free, no plugin or theme on WordPress.org matching the name, nothing at siteunfold.com/.app/.io. Rejected along the way:
- Sitefold: sitefold.app is a live website editor.
- Sitebranch, Branchsite, Siteshift, Sitesplit, Netshift: existing products.
- Multiply: taken on WordPress.org.

The GitHub repo, local folder and DDEV project were renamed to `siteunfold` as well.

## Consequences
- Version 1.2.0 ships the rename; 1.0.0 and 1.1.0 stay on GitHub under the old name.
- Networks converted by 1.0/1.1 carry the old wp-config.php marker comment, so `WpConfigEditor::LEGACY_BLOCK_COMMENTS` keeps recognising it, and `uninstall.php` also clears the old transient prefix.
- Never reintroduce "One Click Multisite" or another generic "<adjective> Multisite" name.
