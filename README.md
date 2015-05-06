# SilverStripe Google Sitemap Auto Update

This module simply passes the responsibility of physically generating a `sitemap.xml` file
to the [QueuedJobs Module](https://github.com/silverstripe-australia/silverstripe-queuedjobs) which it
does after each write.

The advantage of handing off this procedure to a message queue rather than just doing it
immediately, will likely be more apparent on busy, content-heavy sites, who's content authors
may already experience time-lags when saving content in the CMS.

If you need anything more complicated such as auto-alerting Google of changes to your
sitemap, you might consider the standard [Google SiteMaps Module](https://github.com/silverstripe-labs/silverstripe-googlesitemaps).