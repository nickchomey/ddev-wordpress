
## DDEV-Wordpress
This ddev add-on both simplifies getting started with a WordPress project in DDEV, as well as adds some crucial functionality that addresses fundamental shortcomings with WordPress in a development environment - namely with regards to accessing it via a non-production URL.

## Challenges of running WordPress in DDEV
### Different Database credentials
WordPress sets its database credentials in the wp-config.php file, rather than through any sort of environment variables. So, when we copy a wordpress installation from production to development, we need to change these credentials

An eventual goal (see issue #1) will be to load everything through environment variables and .env files, but for now we simply prepend the database constant definitions with `defined('CONSTANT') ||` so that the credentials can be pre-emptively loaded from a new `wp-config-development.php` file. This is used, rather than the existing `wp-config-ddev.php` file because 1) `development` is one of the natively supported wordpress [environment types](https://make.wordpress.org/core/2020/08/27/wordpress-environment-types/). Also, it is just generally best to distance ourselves from DDEV's default WordPress configurations.

### Absolute URL usage
Wordpress does not have any support for using relative paths. Instead, everything is done with absolute urls - be it internal a href links to other pages, or the urls for loading static assets.

Moreover, it hardcodes the site url in the wp_options table in the database, which can also be configured in wp-config.php.

There have been various attempts over the decades to implement relative paths.
1. Using a slew of ever-changing and always-insufficient wordpress hooks (e.g. [This plugin](https://wordpress.org/plugins/root-relative-urls/))
2. Wrapping the request early-on with php output buffers and rewriting the URLs. (e.g. https://stackoverflow.com/questions/17187437/relative-urls-in-wordpress#comment88928219_18516783)

But these have always been insufficient - they don't handle all headers, maybe don't work early or late enough in the request, don't handle redirects perfectly, etc...

Pushes for changes in WP Core (https://core.trac.wordpress.org/ticket/17048) have been resoundingly rejected. [Wordpress will always only ever work with absolute URLs](https://make.wordpress.org/core/handbook/contribute/design-decisions/#absolute-versus-relative-urls).

As is always prudent with WP, don't swim against the tide! Instead, the only options for doing any local development on a Wordpress site have been to:
1. Set your hosts file so that requests to the production domain are fulfilled locally. This prevents access to the production site, and also inevitably causes errors when you are turning hosts on and off. DNS caching also gets in the way.
2. Set a new hostname/fully-qualified DNS (which DDEV makes easy) and do a global search/replace in the database to change the production URL to something like `project.ddev.site`. This is also a hassle. It also prevents you from using DDEV's native capacity for routing *multiple* hostnames and fully-qualified DNS to the same project.

This add-on finally solves this problem *completely*, by leveraging the dynamic middleware capabilities of DDEV's Traefik-powered router. It does this by accepting WordPress as it is, and using Traefik's middlewares capabilities to converts any instance of the production URL to a relative path. It does this for Request Headers, Response Headers and the entire contents of the Response Body. This allows you to set as many hostnames/fqdns within DDEV as you like.

Because Traefik operates both before and after PHP, there's no possibility of it "missing" anything. In fact, WordPress doesn't have any knowledge, whatsoever, that it isn't actually being accessed from something other than the production URL in the database!

## Commands
* `ddev wordpress install`
    - Creates a new DDEV project containing a fresh WordPress installation. The project and its URL will be named after the current working directory (test/ -> test.ddev.site), but you can set the production URL that is stored in the database to be something different
    - Updates `wp-config.php` to read from a new `wp-config-development.php`
    - Creates the traefik dynamic config for rewriting absolute urls to relative paths
    - Sets required environment variables in the project's `config.yaml` file
* `ddev wordpress import`
    * Does the same as `install`, except for it imports an existing wordpress site that you have placed in the current directory
    * The current directory must contain a sql dump of the site's database (named *.sql.*).
* `ddev wordpress wpconfig` - Only runs the `wp-config.php` compatibility mechanism used above
* `ddev wordpress traefik` - Creates a traefik middleware extension config, which allows for converting WordPress' absolute urls to relative paths.

## Testing it out
1. Open an existing DDEV Project and install this add-on with `ddev addon get nickchomey/ddev-wordpress`. It will be installed *globally* rather than in the project. This only needs to be done once.
2. Create a new directory in which you want to create a new DDEV WordPress project. Enter the directory.
3. run `ddev wordpress install` - you will be prompted for some details. Use whatever you like for all of it, but try using a different URL from the ddev project name/directory name. (e.g. if the directory is named `test`, the url will be `test.ddev.site`. But you can set the production URL to `ddev.isamazing.com`)
4. It will do everything automatically and restart the ddev container, after which you can open the browser to `test.ddev.site` and it will be browsing and behaving as if you are accessing `ddev.isamazing.com`.
5. Try uploading some media or creating links between internal pages.
6. Create new hostnames/fqdns in the ddev project's `config.yaml` file. Restart DDEV.
7. Access the site with any of these URLs. Notice that the media and links point to `/wp-content/uploads/2024/09/media.jpg` rather than `ddev.isamazing.com/wp-content/uploads/2024/09/media.jpg`
8. If you eventually deploy the site to production, you can simply point your `ddev.isamazing.com` DNS record to the server and it will work.

## Troubleshooting
It is most likely that you would get errors related to the `wp-config.php` and `wp-config-development.php` mechanism. Inspect those files for errors. Also, if you use something like Bedrock, it may not work at all. Please feel free to report any issues here.


# Reference Notes from the Add-on Template for outstanding tasks
## Components of the repository

* The fundamental contents of the add-on service or other component. For example, in this template there is a [docker-compose.ddev-wordpress.yaml](docker-compose.ddev-wordpress.yaml) file.
* An [install.yaml](install.yaml) file that describes how to install the service or other component.
* A test suite in [test.bats](tests/test.bats) that makes sure the service continues to work as expected.
* [Github actions setup](.github/workflows/tests.yml) so that the tests run automatically when you push to the repository.

## Getting started

1. Choose a good descriptive name for your add-on. It should probably start with "ddev-" and include the basic service or functionality. If it's particular to a specific CMS, perhaps `ddev-<CMS>-servicename`.
2. Create the new template repository by using the template button.
3. Globally replace "ddev-wordpress" with the name of your add-on.
4. Add the files that need to be added to a DDEV project to the repository. For example, you might replace `docker-compose.ddev-wordpress.yaml` with the `docker-compose.*.yaml` for your recipe.
5. Update the `install.yaml` to give the necessary instructions for installing the add-on:

   * The fundamental line is the `project_files` directive, a list of files to be copied from this repo into the project `.ddev` directory.
   * You can optionally add files to the `global_files` directive as well, which will cause files to be placed in the global `.ddev` directory, `~/.ddev`.
   * Finally, `pre_install_commands` and `post_install_commands` are supported. These can use the host-side environment variables documented [in DDEV docs](https://ddev.readthedocs.io/en/latest/users/extend/custom-commands/#environment-variables-provided).

6. Update `tests/test.bats` to provide a reasonable test for your repository. Tests are triggered either by manually executing `bats ./tests/test.bats`, automatically on every push to the repository, or periodically each night. Please make sure to attend to test failures when they happen. Others will be depending on you. Bats is a simple testing framework that just uses Bash. To run a Bats test locally, you have to [install bats-core](https://bats-core.readthedocs.io/en/stable/installation.html) first. Then you download your add-on, and finally run `bats ./tests/test.bats` within the root of the uncompressed directory. To learn more about Bats see the [documentation](https://bats-core.readthedocs.io/en/stable/).
7. When everything is working, including the tests, you can push the repository to GitHub.
8. Create a [release](https://docs.github.com/en/repositories/releasing-projects-on-github/managing-releases-in-a-repository) on GitHub.
9. Test manually with `ddev get <owner/repo>`.
10. You can test PRs with `ddev get https://github.com/<user>/<repo>/tarball/<branch>`
11. Update the `README.md` to describe the add-on, how to use it, and how to contribute. If there are any manual actions that have to be taken, please explain them. If it requires special configuration of the using project, please explain how to do those. Examples in [ddev/ddev-solr](https://github.com/ddev/ddev-solr), [ddev/ddev-memcached](https://github.com/ddev/ddev-memcached), and (advanced) [ddev-platformsh](https://github.com/ddev/ddev-platformsh).
12. Update the `README.md` header in Title Case format, for example, use `# DDEV Redis`, not `# ddev-redis`.
13. Add a good short description to your repo, and add the [topic](https://docs.github.com/en/repositories/managing-your-repositorys-settings-and-features/customizing-your-repository/classifying-your-repository-with-topics) "ddev-get". It will immediately be added to the list provided by `ddev get --list --all`.
14. When it has matured you will hopefully want to have it become an "official" maintained add-on. Open an issue in the [DDEV queue](https://github.com/ddev/ddev/issues) for that.

Add-ons were covered in [DDEV Add-ons: Creating, maintaining, testing](https://www.dropbox.com/scl/fi/bnvlv7zswxwm8ix1s5u4t/2023-11-07_DDEV_Add-ons.mp4?rlkey=5cma8s11pscxq0skawsoqrscp&dl=0) (part of the [DDEV Contributor Live Training](https://ddev.com/blog/contributor-training)).

Note that more advanced techniques are discussed in [DDEV docs](https://ddev.readthedocs.io/en/latest/users/extend/additional-services/#additional-service-configurations-and-add-ons-for-ddev).

## How to debug tests (Github Actions)

1. You need an SSH-key registered with GitHub. You either pick the key you have already used with `github.com` or you create a dedicated new one with `ssh-keygen -t ed25519 -a 64 -f tmate_ed25519 -C "$(date +'%d-%m-%Y')"` and add it at `https://github.com/settings/keys`.

2. Add the following snippet to `~/.ssh/config`:

```
Host *.tmate.io
    User git
    AddKeysToAgent yes
    UseKeychain yes
    PreferredAuthentications publickey
    IdentitiesOnly yes
    IdentityFile ~/.ssh/tmate_ed25519
```
3. Go to `https://github.com/<user>/<repo>/actions/workflows/tests.yml`.

4. Click the `Run workflow` button and you will have the option to select the branch to run the workflow from and activate `tmate` by checking the `Debug with tmate` checkbox for this run.

![tmate](images/gh-tmate.jpg)

5. After the `workflow_dispatch` event was triggered, click the `All workflows` link in the sidebar and then click the `tests` action in progress workflow.

7. Pick one of the jobs in progress in the sidebar.

8. Wait until the current task list reaches the `tmate debugging session` section and the output shows something like:

```
106 SSH: ssh PRbaS7SLVxbXImhjUqydQBgDL@nyc1.tmate.io
107 or: ssh -i <path-to-private-SSH-key> PRbaS7SLVxbXImhjUqydQBgDL@nyc1.tmate.io
108 SSH: ssh PRbaS7SLVxbXImhjUqydQBgDL@nyc1.tmate.io
109 or: ssh -i <path-to-private-SSH-key> PRbaS7SLVxbXImhjUqydQBgDL@nyc1.tmate.io
```

9. Copy and execute the first option `ssh PRbaS7SLVxbXImhjUqydQBgDL@nyc1.tmate.io` in the terminal and continue by pressing either <kbd>q</kbd> or <kbd>Ctrl</kbd> + <kbd>c</kbd>.

10. Start the Bats test with `bats ./tests/test.bats`.

For a more detailed documentation about `tmate` see [Debug your GitHub Actions by using tmate](https://mxschmitt.github.io/action-tmate/).

**Contributed and maintained by [@CONTRIBUTOR](https://github.com/CONTRIBUTOR)**
