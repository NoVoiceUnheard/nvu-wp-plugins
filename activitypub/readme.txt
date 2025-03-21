=== ActivityPub ===
Contributors: automattic, pfefferle, mattwiebe, obenland, akirk, jeherve, mediaformat, nuriapena, cavalierlife, andremenrath
Tags: OStatus, fediverse, activitypub, activitystream
Requires at least: 6.4
Tested up to: 6.7
<<<<<<< HEAD
Stable tag: 5.4.1
=======
Stable tag: 5.4.0
>>>>>>> 57ee6aa7e9c74bd23b34597408002fb25c196d12
Requires PHP: 7.2
License: MIT
License URI: http://opensource.org/licenses/MIT

The ActivityPub protocol is a decentralized social networking protocol based upon the ActivityStreams 2.0 data format.

== Description ==

Enter the fediverse with **ActivityPub**, broadcasting your blog to a wider audience! Attract followers, deliver updates, and receive comments from a diverse user base of **ActivityPub**\-compliant platforms.

https://www.youtube.com/watch?v=QzYozbNneVc

With the ActivityPub plugin installed, your WordPress blog itself function as a federated profile, along with profiles for each author. For instance, if your website is `example.com`, then the blog-wide profile can be found at `@example.com@example.com`, and authors like Jane and Bob would have their individual profiles at `@jane@example.com` and `@bobz@example.com`, respectively.

An example: I give you my Mastodon profile name: `@pfefferle@mastodon.social`. You search, see my profile, and hit follow. Now, any post I make appears in your Home feed. Similarly, with the ActivityPub plugin, you can find and follow Jane's profile at `@jane@example.com`.

Once you follow Jane's `@jane@example.com` profile, any blog post she crafts on `example.com` will land in your Home feed. Simultaneously, by following the blog-wide profile `@example.com@example.com`, you'll receive updates from all authors.

**Note**: If no one follows your author or blog instance, your posts remain unseen. The simplest method to verify the plugin's operation is by following your profile. If you possess a Mastodon profile, initiate by following your new one.

The plugin works with the following tested federated platforms, but there may be more that it works with as well:

* [Mastodon](https://joinmastodon.org/)
* [Pleroma](https://pleroma.social/)/[Akkoma](https://akkoma.social/)
* [friendica](https://friendi.ca/)
* [Hubzilla](https://hubzilla.org/)
* [Pixelfed](https://pixelfed.org/)
* [Socialhome](https://socialhome.network/)
* [Misskey](https://join.misskey.page/)

Some things to note:

1. The blog-wide profile is only compatible with sites with rewrite rules enabled. If your site does not have rewrite rules enabled, the author-specific profiles may still work.
1. Many single-author blogs have chosen to turn off or redirect their author profile pages, usually via an SEO plugin like Yoast or Rank Math. This is usually done to avoid duplicate content with your blog’s home page. If your author page has been deactivated in this way, then ActivityPub author profiles won’t work for you. Instead, you can turn your author profile page back on, and then use the option in your SEO plugin to noindex the author page. This will still resolve duplicate content issues with search engines and will enable ActivityPub author profiles to work.
1. Once ActivityPub is installed, *only new posts going forward* will be available in the fediverse. Likewise, even if you’ve been using ActivityPub for a while, anyone who follows your site will only see new posts you publish from that moment on. They will never see previously-published posts in their Home feed. This process is very similar to subscribing to a newsletter. If you subscribe to a newsletter, you will only receive future emails, but not the old archived ones. With ActivityPub, if someone follows your site, they will only receive new blog posts you publish from then on.

So what’s the process?

1. Install the ActivityPub plugin.
1. Go to the plugin’s settings page and adjust the settings to your liking. Click the Save button when ready.
1. Make sure your blog’s author profile page is active if you are using author profiles.
1. Go to Mastodon or any other federated platform, and search for your profile, and follow it. Your new profile will be in the form of either `@your_username@example.com` or `@example.com@example.com`, so that is what you’ll search for.
1. On your blog, publish a new post.
1. From Mastodon, check to see if the new post appears in your Home feed.

**Note**: It may take up to 15 minutes or so for the new post to show up in your federated feed. This is because the messages are sent to the federated platforms using a delayed cron. This avoids breaking the publishing process for those cases where users might have lots of followers. So please don’t assume that just because you didn’t see it show up right away that something is broken. Give it some time. In most cases, it will show up within a few minutes, and you’ll know everything is working as expected.

== Frequently Asked Questions ==

= tl;dr =

This plugin connects your WordPress blog to popular social platforms like Mastodon, making your posts more accessible to a wider audience. Once installed, your blog can be followed by users on these platforms, allowing them to receive your new posts in their feeds.

= What is "ActivityPub for WordPress" =

*ActivityPub for WordPress* extends WordPress with some Fediverse features, but it does not compete with platforms like Friendica or Mastodon. If you want to run a **decentralized social network**, please use [Mastodon](https://joinmastodon.org/) or [GNU social](https://gnusocial.network/).

= What if you are running your blog in a subdirectory? =

In order for webfinger to work, it must be mapped to the root directory of the URL on which your blog resides.

**Apache**

Add the following to the .htaccess file in the root directory:

	RedirectMatch "^\/\.well-known/(webfinger|nodeinfo)(.*)$" /blog/.well-known/$1$2

Where 'blog' is the path to the subdirectory at which your blog resides.

**Nginx**

Add the following to the site.conf in sites-available:

	location ~* /.well-known {
		allow all;
		try_files $uri $uri/ /blog/?$args;
	}

Where 'blog' is the path to the subdirectory at which your blog resides.

If you are running your blog in a subdirectory, but have a different [wp_siteurl](https://wordpress.org/documentation/article/giving-wordpress-its-own-directory/), you don't need the redirect, because the index.php will take care of that.

= What if you are running your blog behind a reverse proxy with Apache? =

If you are using a reverse proxy with Apache to run your host you may encounter that you are unable to have followers join the blog. This will occur because the proxy system rewrites the host headers to be the internal DNS name of your server, which the plugin then uses to attempt to sign the replies. The remote site attempting to follow your users is expecting the public DNS name on the replies. In these cases you will need to use the 'ProxyPreserveHost On' directive to ensure the external host name is passed to your internal host.

If you are using SSL between the proxy and internal host you may also need to `SSLProxyCheckPeerName off` if your internal host can not answer with the correct SSL name. This may present a security issue in some environments.

= Constants =

The plugin uses PHP Constants to enable, disable or change its default behaviour. Please use them with caution and only if you know what you are doing.

* `ACTIVITYPUB_REST_NAMESPACE` - Change the default Namespace of the REST endpoint. Default: `activitypub/1.0`.
* `ACTIVITYPUB_EXCERPT_LENGTH` - Change the length of the Excerpt. Default: `400`.
* `ACTIVITYPUB_SHOW_PLUGIN_RECOMMENDATIONS` - show plugin recommendations in the ActivityPub settings. Default: `true`.
* `ACTIVITYPUB_MAX_IMAGE_ATTACHMENTS` - Change the number of attachments, that should be federated. Default: `3`.
* `ACTIVITYPUB_HASHTAGS_REGEXP` - Change the default regex to detect hashtext in a text. Default: `(?:(?<=\s)|(?<=<p>)|(?<=<br>)|^)#([A-Za-z0-9_]+)(?:(?=\s|[[:punct:]]|$))`.
* `ACTIVITYPUB_USERNAME_REGEXP` - Change the default regex to detect @-replies in a text. Default: `(?:([A-Za-z0-9\._-]+)@((?:[A-Za-z0-9_-]+\.)+[A-Za-z]+))`.
* `ACTIVITYPUB_URL_REGEXP` - Change the default regex to detect urls in a text. Default: `(www.|http:|https:)+[^\s]+[\w\/]`.
* `ACTIVITYPUB_CUSTOM_POST_CONTENT` - Change the default template for Activities. Default: `<strong>[ap_title]</strong>\n\n[ap_content]\n\n[ap_hashtags]\n\n[ap_shortlink]`.
* `ACTIVITYPUB_AUTHORIZED_FETCH` - Enable AUTHORIZED_FETCH.
* `ACTIVITYPUB_DISABLE_REWRITES` - Disable auto generation of `mod_rewrite` rules. Default: `false`.
* `ACTIVITYPUB_DISABLE_INCOMING_INTERACTIONS` - Block incoming replies/comments/likes. Default: `false`.
* `ACTIVITYPUB_DISABLE_OUTGOING_INTERACTIONS` - Disable outgoing replies/comments/likes. Default: `false`.
* `ACTIVITYPUB_SHARED_INBOX_FEATURE` - Enable the shared inbox. Default: `false`.
* `ACTIVITYPUB_SEND_VARY_HEADER` - Enable to send the `Vary: Accept` header. Default: `false`.

= Where can you manage your followers? =

If you have activated the blog user, you will find the list of his followers in the settings under `/wp-admin/options-general.php?page=activitypub&tab=followers`.

The followers of a user can be found in the menu under "Users" -> "Followers" or under `wp-admin/users.php?page=activitypub-followers-list`.

For reasons of data protection, it is not possible to see the followers of other users.

== Screenshots ==

1. The "Follow me"-Block in the Block-Editor
2. The "Followers"-Block in the Block-Editor
3. The "Federated Reply"-Block in the Block-Editor
4. A "Federated Reply" in a Post
5. A Blog-Profile on Mastodon

== Changelog ==

<<<<<<< HEAD
= 5.4.1 =

* Fixed: Transition handling of posts to ensure that `Create` and `Update` activities are properly processed.

=======
>>>>>>> 57ee6aa7e9c74bd23b34597408002fb25c196d12
= 5.4.0 =

* Added: Upgrade script to fix Follower json representations with unescaped backslashes.
* Added: Centralized place for sanitization functions.
* Changed: Bumped minimum required WordPress version to 6.4.
* Changed: Use a later hook for Posts to get published to the Outbox, to get sure all `post_meta`s and `taxonomy`s are set stored properly.
* Changed: Use webfinger as author email for comments from the Fediverse.
* Fixed: Do not redirect `/@username` URLs to the API any more, to improve `AUTHORIZED_FETCH` handling.

= 5.3.2 =

* Fixed: Remove `activitypub_reply_block` filter after Activity-JSON is rendered, to not affect the HTML representation.
* Fixed: Remove `render_block_core/embed` filter after Activity-JSON is rendered, to not affect the HTML representation.

= 5.3.1 =

* Fixed: Blog profile settings can be saved again without errors.
* Fixed: Followers with backslashes in their descriptions no longer break their actor representation.

= 5.3.0 =

* Added: A fallback `Note` for `Article` objects to improve previews on services that don't support Articles yet.
* Added: A reply `context` for Posts and Comments to allow relying parties to discover the whole conversation of a thread.
* Added: Allow Activities on URLs instead of requiring Activity-Objects. This is useful especially for sending Announces and Likes.
* Added: Undo API for Outbox items.
* Added: Setting to adjust the number of days Outbox items are kept before being purged.
* Added: Failed Follower notifications for Outbox items now get retried for two more times.
* Added: Support incoming `Move` activities and ensure that followed persons are updated accordingly.
* Added: Show metadata in the New Follower E-Mail.
* Added: Outbox Activity IDs can now be resolved when the ActivityPub `Accept header is used.
* Added: Labels to add context to visibility settings in the block editor.
* Added: WP CLI command to reschedule Outbox-Activities.
* Changed: Properly process `Update` activities on profiles and ensure all properties of a followed person are updated accordingly.
* Changed: Outbox now precesses the first batch of followers right away to avoid delays in processing new Activities.
* Changed: Post bulk edits no longer create Outbox items, unless author or post status change.
* Changed: Outbox processing accounts for shared inboxes again.
* Changed: Improved check for `?activitypub` query-var.
* Changed: Rewrite rules: be more specific in author rewrite rules to avoid conflicts on sites that use the "@author" pattern in their permalinks.
* Changed: Deprecate the `activitypub_post_locale` filter in favor of the `activitypub_locale` filter.
* Fixed: The Outbox purging routine no longer is limited to deleting 5 items at a time.
* Fixed: An issue where the outbox could not send object types other than `Base_Object` (introduced in 5.0.0).
* Fixed: Ellipses now display correctly in notification emails for Likes and Reposts.
* Fixed: Send Update-Activity when "Actor-Mode" is changed.
* Fixed: Added delay to `Announce` Activity from the Blog-Actor, to not have race conditions.
* Fixed: `Actor` validation in several REST API endpoints.
* Fixed: Bring back the `activitypub_post_locale` filter to allow overriding the post's locale.

= 5.2.0 =

* Added: Batch Outbox-Processing.
* Added: Outbox processed events get logged in Stream and show any errors returned from inboxes.
* Added: Outbox items older than 6 months will be purged to avoid performance issues.
* Added: REST API endpoints for likes and shares.
* Changed: Increased probability of Outbox items being processed with the correct author.
* Changed: Enabled querying of Outbox posts through the REST API to improve troubleshooting and debugging.
* Changed: Updated terminology to be client-neutral in the Federated Reply block.
* Changed: Refactored settings to use the WordPress Settings API
* Fixed: Enforce 200 status header for valid ActivityPub requests.
* Fixed: `object_id_to_comment` returns a commment now, even if there are more than one matching comment in the DB.
* Fixed: Integration of content-visibility setup in the block editor.
* Fixed: Update CLI commands to the new scheduler refactorings.
* Fixed: Do not add an audience to the Actor-Profiles.
* Fixed: `Activity::set_object` falsely overwrites the Activity-ID with a default.

= 5.1.0 =

* Added: Cleanup of option values when the plugin is uninstalled.
* Added: Third-party plugins can filter settings tabs to add their own settings pages for ActivityPub.
* Added: Show ActivityPub preview in row actions when Block Editor is enabled but not used for the post type.
* Changed: Manually granting `activitypub` cap no longer requires the receiving user to have `publish_post`.
* Changed: Allow Base Transformer to handle WP_Term objects for transformation.
* Changed: Allow omitting replies in ActivityPub representations instead of setting them as empty.
* Changed: Improved Query extensibility for third party plugins.
* Fixed: Negotiation of ActivityPub requests for custom post types when queried by the ActivityPub ID.
* Fixed: Avoid PHP warnings when using Debug mode and when the `actor` is not set.
* Fixed: No longer creates Outbox items when importing content/users.
* Fixed: NodeInfo 2.0 URL to be HTTP instead of HTTPS.

= 5.0.0 =

* Added: Outbox queue
* Changed: Rewrite the current dispatcher system, to use the Outbox instead of a Scheduler.
* Changed: Improved content negotiation and AUTHORIZED_FETCH support for third-party plugins.
* Changed: Moved password check to `is_post_disabled` function.
* Fixed: Handle deletes from remote servers that leave behind an accessible Tombstone object.
* Fixed: No longer parses tags for post types that don't support Activitypub.
* Fixed: rel attribute will now contain no more than one "me" value.

See full Changelog on [GitHub](https://github.com/Automattic/wordpress-activitypub/blob/trunk/CHANGELOG.md).

== Upgrade Notice ==

= 5.4.0 =

Note: This update requires WordPress 6.4+. Please ensure your site meets this requirement before upgrading.

== Installation ==

Follow the normal instructions for [installing WordPress plugins](https://wordpress.org/support/article/managing-plugins/).

= Automatic Plugin Installation =

To add a WordPress Plugin using the [built-in plugin installer](https://codex.wordpress.org/Administration_Screens#Add_New_Plugins):

1. Go to [Plugins](https://codex.wordpress.org/Administration_Screens#Plugins) > [Add New](https://codex.wordpress.org/Plugins_Add_New_Screen).
1. Type "`activitypub`" into the **Search Plugins** box.
1. Find the WordPress Plugin you wish to install.
    1. Click **Details** for more information about the Plugin and instructions you may wish to print or save to help setup the Plugin.
    1. Click **Install Now** to install the WordPress Plugin.
1. The resulting installation screen will list the installation as successful or note any problems during the install.
1. If successful, click **Activate Plugin** to activate it, or **Return to Plugin Installer** for further actions.

= Manual Plugin Installation =

There are a few cases when manually installing a WordPress Plugin is appropriate.

* If you wish to control the placement and the process of installing a WordPress Plugin.
* If your server does not permit automatic installation of a WordPress Plugin.
* If you want to try the [latest development version](https://github.com/pfefferle/wordpress-activitypub).

Installation of a WordPress Plugin manually requires FTP familiarity and the awareness that you may put your site at risk if you install a WordPress Plugin incompatible with the current version or from an unreliable source.

Backup your site completely before proceeding.

To install a WordPress Plugin manually:

* Download your WordPress Plugin to your desktop.
    * Download from [the WordPress directory](https://wordpress.org/plugins/activitypub/)
    * Download from [GitHub](https://github.com/pfefferle/wordpress-activitypub/releases)
* If downloaded as a zip archive, extract the Plugin folder to your desktop.
* With your FTP program, upload the Plugin folder to the `wp-content/plugins` folder in your WordPress directory online.
* Go to [Plugins screen](https://codex.wordpress.org/Administration_Screens#Plugins) and find the newly uploaded Plugin in the list.
* Click **Activate** to activate it.
