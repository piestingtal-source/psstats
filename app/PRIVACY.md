# Privacy 
This is a summary of all of the components within Psstats which may affect your privacy in some way. Please keep in mind
third party Themes, Plugins or Apps may introduce privacy concerns not listed here.

## Privacy for users being tracked by Psstats
In this section we document how to protect the privacy of visitors who are tracked by your Psstats analytics service.

### Anonymise visitor IP addresses
By default, Psstats stores the visitor IP address (IPv4 or IPv6 format) in the database for each new visitor. 
If a visitor has a static IP address this means their browsing history can be easily identified across several days and
even across several websites tracked within the same Psstats server. You can anonymize IP addresses to ensure visitors cannot
be tracked this way: [How to anonymise IP addresses.](https://n3rds.work/docs/privacy/#step-1-automatically-anonymize-visitor-ips)

### Delete old visitors logs
By default, Psstats stores tracked data forever. To better respect the privacy of your users, it is recommended to regularly
purge old data. You can configure Psstats to automatically delete log data older than a specified number of months: 
[How to delete old visitors log data.](https://n3rds.work/docs/privacy/#step-2-delete-old-visitors-logs)

### Include a tracking Opt-Out feature on your site
In your website, we recommended providing an easy way for your visitors to “opt-out” of being tracked by Psstats. 
You can use the Opt-Out feature to display a link your website that sets a special browser cookie (`psstats_ignore`) when
clicked. Visitors that click that link will be ignored by Psstats in the future: 
[How to include a tracking opt-out iframe.](https://n3rds.work/docs/privacy/#step-3-include-a-web-analytics-opt-out-feature-on-your-site-using-an-iframe)

### Respect DoNotTrack preference
Do Not Track is a browser-level technology and policy proposal that lets visitors opt out of tracking by websites they
do not visit. Visitors can enable this preference in their browser, and then it's up to Psstats to respect it. By default,
Psstats is configured to ignore visitors that have enabled it: 
[How to check if your Psstats respects DoNotTrack.] (https://n3rds.work/docs/privacy/#step-4-respect-donottrack-preference)

### Disable tracking cookies
A cookie is a collection of information that a website stores on a visitor’s computer and accesses each time the visitor
returns. By default, Psstats uses cookies to aid in tracking visitor behavior. If someone gains access to a visitor's
computer, they could learn a few things about how the visitor visited your website. For many websites, this isn't a
problem, but for others where a strong level of privacy is required (like online banking), disabling tracking cookies may
be a good idea: [How to disable tracking cookies.](https://n3rds.work/faq/general/faq_157/)

### Keep your visitors details private
Any user that has at least `view` access (the default access level) to Psstats can view detailed information for all users
tracked in Psstats (such as their IP addresses, visitor IDs, details of all past visits and actions, etc.) through features
provided by the `Live` plugin (such as the Visitor Log and Visitor Profile). As the Psstats administrator, you may decide
that not all of your users need access to this data. You can deactivate the `Live` plugin to prevent users from viewing
visitor details in the Administration > Plugins page.

## Privacy for Psstats admins and website owners
In this section we document how a Psstats administrator can better protect their own privacy.

### Keep your Psstats server URL private
By default, the Psstats Javascript code on all tracked websites contains the Psstats server URL. In some cases you might
want to hide this Psstats URL completely while still tracking all websites in your Psstats instance. To hide your Psstats
server's URL, you can modify the Javascript Tracking code and point it to a proxy piwik.php script instead of your actual
Psstats server: [How to keep Psstats server URL private.](https://n3rds.work/faq/how-to/faq_132/)

### Automatic update check
From time to time, Psstats uses `api.psstats.org` to check if the current version of Psstats is the latest version of Psstats.
If an update is available, a notification is displayed allowing you to upgrade Psstats. To disable the update check,
and stop your instance from sending HTTP requests to `api.psstats.org`, deactivate the "Automatic update" feature by
setting `enable_auto_update = 0` in your configuration file `config/config.ini.php`.

Learn more about [Privacy in Psstats](https://n3rds.work/privacy/).
