# Barrel WordPress Upstream

This is a WordPress repository configured to run on the [Pantheon platform](https://pantheon.io).

Pantheon is website platform optimized and configured to run high performance sites with an amazing developer workflow. There is built-in support for features such as Varnish, Redis, Apache Solr, New Relic, Nginx, PHP-FPM, MySQL, PhantomJS and more. 

This image will also work on any other platform provided a `wp-config-local.php` file is included rather than by modifying `wp-config.php`. Certain mu-plugins for Pantheon should be replaced with platform-specific ones.

## Getting Started with Pantheon 

The below pertains mostly to setting up a brand new site. For most developers, these steps will be completed. 

### 1. Spin-up a site

If you do not yet have a Pantheon account, you can create one for free. Once you've verified your email address, you will be able to add sites from your dashboard. 

- Create a new site. The name is important. The name of the site will be handleized (converted to lower case with non-alphanumerics replaced with hyphens), which will then be used for the pantheon environment urls as well as the gitlab repo, which must next be created.
- Choose "Barrel Base WordPress" as the Custom Upstreams for Barrel to use this distribution. If you do not see this option, you are not creating the site within our organization, which will lack upstream changes including gitlab-ci integration, common plugins, and updates to the base theme.
- Create a new private gitlab repo in the Barrel group’s namespace, using the same name as handleized by pantheon. For instance, “new-site” will be both the gitlab repo name and the pantheon project name, which would come in the form of `dev-new-site.pantheonsite.io`. Go back to Pantheon's dashboard for the site.

### 2. Load up the site

When the spin-up process is complete, you will be redirected to the site's dashboard. Click on the link under the site's name to access the Dev environment.

![alt](http://i.imgur.com/2wjCj9j.png?1, '')

### 3. Run the WordPress installer 

Follow steps to setup the new WordPress site by visiting the admin for the first time.

- Populate the Site Title using the project or client's name.
- The initial user should be `barreladmin`.
- The initial user’s email should be `admin@barrelny.com`.
- The password should be random-generated.
- Immediately commit these details to the shared 1Password team vault.

![alt](http://i.imgur.com/4EOcqYN.png, '')

If you would like to keep a separate set of configuration for local development, you can use a file called `wp-config-local.php`, which is already in our `.gitignore` file.

### 4. Developer: Repo Synchronization

While the gitlab repo has already been created, it needs to be synchronized with Pantheon's codebase.

- Clone the site from Pantheon.
- Follow steps in our local development setup doc.


Ongoing Updates and Deployments

Continuous Integration Notes

This image employs GitLab's CI Tooling.

- The following environment variables must be set in GitLab in order for them to work:
-- $THEME_NAME

The scripts assumes only theme development
