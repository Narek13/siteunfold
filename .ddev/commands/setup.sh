#!/bin/bash
## Description: Install WordPress and activate Siteunfold (post-start).
## Usage: setup
## Example: "ddev setup"

set -e

if wp core is-installed 2>/dev/null; then
  echo "WordPress already installed."
else
  echo "Installing WordPress..."
  # Core files survive a project rename (they live in the repo folder); the database does not.
  [ -f .ddev/wordpress/wp-load.php ] || wp core download

  wp core install \
    --title="${WP_TITLE}" \
    --admin_user="${ADMIN_USER}" \
    --admin_password="${ADMIN_PASS}" \
    --url="${DDEV_PRIMARY_URL}" \
    --admin_email="admin@example.com" \
    --skip-email

  wp rewrite structure '/%postname%/' --hard
  wp plugin delete hello akismet 2>/dev/null || true
fi

# The plugin is mounted read-only at wp-content/plugins/${DDEV_PROJECT}.
wp plugin activate "${DDEV_PROJECT}" || true

echo "Siteunfold dev site ready: ${DDEV_PRIMARY_URL}/wp-admin/ (${ADMIN_USER} / ${ADMIN_PASS})"
echo "  Tools -> Convert to Multisite     the plugin screen"
echo "  ddev reset-single-site            undo a conversion to test it again"
