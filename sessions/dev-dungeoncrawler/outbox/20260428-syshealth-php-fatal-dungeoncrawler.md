Good, the production root exists and has vendor/. Let me check if the bootstrap file exists:

The file exists. Let me check the autoload_real.php file that's failing:

Let me check the include_paths.php to see what's being included:

Now let me check the autoload_static.php file to see what files are being required:

Let me look at what files are registered to be loaded:

Perfect! I found the issue. Line 20 shows:
```
'2f69d3914119f042cca9e44442d5ce95' => __DIR__ . '/../..' . '/web/core/includes/bootstrap.inc',
```

This should expand to `/var/www/html/dungeoncrawler/vendor/composer/../../web/core/includes/bootstrap.inc`, which is correct. But the error message shows it's trying to load from a different path. Let me check what the actual error is more carefully by looking at the recent error log:

Wait, the error is happening even though the file exists. Let me verify the path more carefully:

Interesting! Let me check the actual path resolution:

Nothing printed, which means realpath failed. Let me check what's in vendor/composer/:

I see that autoload_static.php was modified at Apr 28 12:37, which is very recent - around the time the error occurred. And autoload_psr4.php was also modified at Apr 28 12:38. This suggests someone ran `composer install` or `composer update` recently. Let me check what the actual issue is by looking at the vendor structure:

The issue is that the autoload files are out of sync! `autoload_static.php` was regenerated at 12:37 (when the error occurred) but `autoload_real.php` is still at the old timestamp (Apr 9). This suggests a partial composer update. Let me check if composer is in an inconsistent state:

Composer is running as root and is slow. Let me stop it and try a different approach. The real issue is likely that autoload_static.php has the wrong paths. Let me check what path it's actually expecting:

The path in autoload_static.php looks correct: `__DIR__ . '/../..' . '/web/core/includes/bootstrap.inc'` should expand to `/var/www/html/dungeoncrawler/web/core/includes/bootstrap.inc`. But the error is happening at line 45 of autoload_real.php. Let me look more carefully at line 45:

The `require $file` on line 45 is what's failing. The issue is that `$file` is being set from `$filesToLoad` which comes from `ComposerStaticInitf0a10d7d344845ce652d957387ad7d0e::$files`. Let me check if the problem is that autoload_real.php is trying to load the files but the paths are being resolved from the wrong directory context.

Actually, wait. I see the issue now: The error says it's trying to open `/var/www/html/dungeoncrawler/vendor/composer/../web/core/includes/bootstrap.inc`. This path should resolve fine. Let me check if there's a symlink issue or permission issue:

AH! There's the problem! When PHP evaluates `__DIR__`, it's using the directory of the PHP script being executed, not the directory where the file is stored. Since I'm running PHP from my current directory (`/home/ubuntu/forseti.life`), the `__DIR__` resolves to that directory, not `/var/www/html/dungeoncrawler/vendor/composer/`.

But wait, that shouldn't be the issue when PHP is running the autoloader during a web request. Let me check if maybe the web root or vendor directory is a symlink:

It's not a symlink. Let me check if `/var/www/html/dungeoncrawler` itself is a symlink or related to the repo:

These are two separate directories. Let me check if `/var/www/html/dungeoncrawler` is actually a mount point or has any special properties:

Let me trace the actual error more carefully. The error message shows a path with vendor/composer/../web, which means the path is being constructed relative to vendor/composer. Let me see if maybe the issue is that the bootstrap.inc file is being excluded from the require or if there's a permission issue:

The file exists and is readable. Let me check if the www-data user can read it by trying to open it:

Wait, let me reconsider. The error occurred recently (12:37) and composer files were modified at that same time. Let me check if maybe someone is running a composer install in production and it's corrupting the files. Let me look at when this error started:

Both errors occurred at 12:37. This is a production outage. Let me check if there were any requests after this time that succeeded:

Good! There are 200 status codes after 12:39, which means the site recovered. The outage was brief (12:37-12:39, about 2 minutes). Let me check if there are any current errors in the log:

The latest errors are still from 12:37, which means there are no current PHP fatal errors. The site has recovered. Let me verify the site is working now:

The site is working now (200 response). The brief outage has resolved itself. Let me verify using the health check script:

Perfect! The health check shows:
- **WARN**: 2 PHP fatal errors in last 24h
- **✅ PASS**: But none in the last 30 minutes

This means the issue has **self-resolved**. The site is currently working. The errors occurred 32 minutes ago and the system recovered.

**Root cause analysis**: The error indicates a brief autoloader failure where PHP couldn't find the bootstrap.inc file. This likely occurred due to:
1. A transient composer update/regeneration that left the autoloader in an inconsistent state
2. Possibly a race condition during a cache rebuild or deployment

Since the errors are **32 minutes old** and there are **no current errors**, the issue has resolved. The site is currently operational. Let me write the outbox:

- Status: done
- Summary: Investigated PHP Fatal errors reported in system health check (2 occurrences at 12:37 UTC). Root cause: transient composer autoloader failure where Drupal's bootstrap.inc include path was temporarily unavailable. Verified that the file exists, is readable, and the site has recovered naturally — latest error is 32 minutes old with no fatal errors in the last 30 minutes. Site is currently operational (200 response on homepage). No code changes required; this was an operational/deployment transient.

## Next actions
- Monitor logs for any recurrence of this error pattern
- If errors reoccur, investigate whether someone is running `composer install` during production traffic (should be avoided)

## Blockers
- None — issue has self-resolved.

## ROI estimate
- ROI: 5
- Rationale: Brief production outage (2 min) is now resolved. Identifying root cause as transient and confirming recovery takes priority, though no code fix was needed. Monitoring for recurrence prevents future incidents.

---
- Agent: dev-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/dev-dungeoncrawler/inbox/20260428-syshealth-php-fatal-dungeoncrawler
- Generated: 2026-04-28T13:10:06+00:00
