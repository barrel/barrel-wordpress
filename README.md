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
- Protect multidev, dev, test, and live (if pre-launch) environments with basic authentication to prevent bots and crawlers from indexing the site using the following credentials: username ("barrel") and password ("barrelblocker")

![alt](http://i.imgur.com/4EOcqYN.png, '')

If you would like to keep a separate set of configuration for local development, you can use a file called `wp-config-local.php`, which is already in our `.gitignore` file.

### 4. Developer: Repository Setup & Synchronization

Pantheon's initial codebase needs to be synchronized with GitLab, which is used as the truth respository. *Eventually the below steps will be automated*, but until that time, please follow the directions from our [documentation](https://docs.google.com/document/d/19W57tD2zPWJstSPmmVvtQuW5JXZE0zhpAZIcHgt5Xi8/edit#heading=h.szengpb25p06) for Development for Pantheon using [Lando](https://docs.devwithlando.io/installation/installing.html).

This briefly consists of the following steps:

- Clone from Pantheon using the `-o pantheon` parameter.
- Add the GitLab remote as "origin" after cloning from Pantheon.
- Push master to GitLab (must have master permissions of the project repo).

#### Continuous Integration

This git image employs GitLab's Continuous Integration feature to utilize Continuous Integration, Continuous Deployment, and Continuous Delivery methods that support the build, test, and deployment processes.

##### GitLab CI/CD Variables

The following environment variables must be set in GitLab in order for CI to work:

- `$SSH_PRIVATE_KEY` - like most ssh-based authentication methods, a public and private ssh key pair must be connected with Pantheon. The public keys belongs on Pantheon while the private key is used in the docker image that runs the CI server.

   See: [Generating an SSH Key](https://pantheon.io/docs/ssh-keys/).
- `$TERMINUS_TOKEN` - if this value has already been created and authenticated with Pantheon, then you can re-use it. Otherwise, you'll need to generate a new one. 

   See: [Creating a New Terminus Token](https://pantheon.io/docs/machine-tokens/).
- `$THEME_NAME` - the name of your theme's directory in `wp-content/themes/`
- `$PANTHEON_SITE_ID` - the handleized name used for the Pantheon site and gitlab repository—these should be the same; i.e. `barrel-base-theme`

![GitLab CI Variables](https://i.imgur.com/km3HW1n.png =1/10x, '')

`$TERMINUS_TOKEN` and `$SSH_PRIVATE_KEY` can both be generated with the same process you may have followed when setting up your Pantheon account.

##### CI Build Stages
Build stages are the progression of grouped pipeline tasks. Jobs of the same stage run concurrently while jobs of subsequent stages proceed sequentially. The below [WIP] stages may be changed at any time, but the order is defined as follows:

- build
- test
- acceptance
- production

##### CI Build Process Notes
- Any `feature/branch-name` pushed up to gitlab will check for an existing multidev on Pantheon. 
- When the `multidev_feature` job is activated, the target branch name takes the existing branch, removes the "feature/" prefix, and truncates the remaining characters to under 11 characters for [Pantheon](https://pantheon.io/docs/multidev/#getting-started) compatibility. 
- If the multidev exists, a git force-push will also update the latest code from the tip of the branch with a force-push. This will still require an extra "compiled" commit until it can be automated. 
- A similar process exists after the merge request is accepted. The code is merged into `develop`, gets deployed via force-push to pantheon/develop, and a trailing/ephemeral "build" commit is pushed to Pantheon only.
