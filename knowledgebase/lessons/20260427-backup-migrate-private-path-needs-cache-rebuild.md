# Backup and Migrate private path changes need a Drupal cache rebuild

## Context

Several live Drupal sites had `backup_migrate` enabled but were missing
`$settings['file_private_path']`, which caused `private://` warnings during
backup execution.

## What happened

Adding the missing `file_private_path` setting and creating the backing
directory was not sufficient by itself. Drupal loaded the new setting value, but
the `private` stream wrapper still remained unregistered until the site cache
was rebuilt.

Observed pattern during verification:

- `\Drupal\Core\Site\Settings::get('file_private_path')` returned the new
  absolute path.
- `\Drupal::service('stream_wrapper_manager')->isValidScheme('private')`
  remained `false` until `drush cr`.
- After cache rebuild, `private://backup_migrate` resolved correctly into the
  expected `/var/private/<site>/backup_migrate` directory.

## Operational rule

When remediating Backup and Migrate private-path warnings on live Drupal sites:

1. Add `file_private_path` in `settings.php`.
2. Create the backing directory outside the web root with `www-data` ownership.
3. Run `drush cr`.
4. Verify both:
   - `stream_wrapper_manager()->isValidScheme('private') === true`
   - `file_system()->realpath('private://backup_migrate')` resolves correctly

## Why it matters

Without the cache rebuild, the setting can appear correct in `settings.php`
while Backup and Migrate still behaves as if `private://` is not configured.
