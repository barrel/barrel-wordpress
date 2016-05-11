# Contributing to BarrelBaseTheme

We'd love for you to contribute to our source code and to make BarrelBaseTheme even better than it is
today! Here are the guidelines we'd like you to follow:

 - [Issues and Bugs](#issue)
 - [Feature Requests](#feature)
 - [Submission Guidelines](#submit)
 - [Coding Rules](#rules)
 - [Commit Message Guidelines](#commit)
 - [Further Info](#info)

## <a name="issue"></a> Found an Issue?
If you find a bug in the source code or a mistake in the documentation, you can help us by
submitting an issue to our [GitLab Repository][gitlab]. Even better you can submit a Merge Request
with a fix.

## <a name="feature"></a> Want a Feature?
You can request a new feature by submitting an issue to our [GitLab Repository][gitlab].  If you
would like to implement a new feature then consider what kind of change it is:

* **Major Changes** that you wish to contribute to the project should be discussed first in a dev team meeting.
* **Small Changes** can be crafted and submitted to the [GitLab Repository][gitlab] as a Merge Request.

## <a name="submit"></a> Submission Guidelines

### Submitting an Issue
Before you submit your issue search the archive, maybe your question was already answered.

If your issue appears to be a bug, and hasn't been reported, open a new issue.
Help us to maximize the effort we can spend fixing issues and adding new
features, by not reporting duplicate issues.  Providing the following information will increase the
chances of your issue being dealt with quickly:

* **Overview of the Issue** - if an error is being thrown a non-minified stack trace helps
* **Motivation for or Use Case** - explain why this is a bug for you
* **Theme Version(s)** - is it a regression?
* **Browsers and Operating System** - is this a problem with all browsers or only IE8?
* **Reproduce the Error** - provide a live example (using [Pantheon][pantheon] multidev) or an unambiguous set of steps.
* **Related Issues** - has a similar issue been reported before?
* **Suggest a Fix** - if you can't fix the bug yourself, perhaps you can point to what might be
  causing the problem (line of code or commit)

### Submitting a Merge Request
Before you submit your merge request consider the following guidelines:

* Search [GitLab](https://gitlab.com/barrel/barrel-base-theme/merge_requests) for an open or closed Merge Request
  that relates to your submission. You don't want to duplicate effort.
* Make your changes in a new git branch:

     ```shell
     git checkout -b my-fix-branch master
     ```

* Create your patch, **including appropriate test cases**.
* Follow our [Best Practices](#rules).
* *Run the full Theme test suite, as described in the [Theme Readme],
  and ensure that all tests pass.* **TBD**
* Commit your changes using a descriptive commit message that follows our
  [commit message conventions](#commit-message-format) and passes our commit message presubmit hook
  `validate-commit-msg.js`. Adherence to the [commit message conventions](#commit-message-format)
  is required because it makes any reviewer's life easier and brightens their day.

     ```shell
     git commit -a
     ```
  Note: the optional commit `-a` command line option will automatically "add" and "rm" edited files.

* Build your changes locally to ensure all the tests and validations pass:

    ```shell
    gulp build
    gulp validate
    gulp dev
    ```

* Push your branch to GitLab:

    ```shell
    git push origin my-fix-branch
    ```

* In GitLab, send a merge request to `barrel-base-theme:master`.
* If we suggest changes then:
  * Make the required updates.
  * Re-run the Theme test suite to ensure tests are still passing.
  * Commit your changes to your branch (e.g. `my-fix-branch`).
  * Push the changes to your GitLab repository (this will update your Merge Request).

If the MR gets too outdated we may ask you to rebase and force push to update the MR:

    ```shell
    git rebase master -i
    git push origin my-fix-branch -f
    ```

*WARNING. Squashing or reverting commits and forced push thereafter may remove GitLab comments
on code that were previously made by you and others in your commits.*

That's it! Thank you for your contribution!

#### After your merge request is merged

After your merge request is merged, you can safely delete your local branch (it will be deleted on our remote) and pull the changes
from the main (upstream) repository:

* If not deleted by the merge request, delete the remote branch on GitLab either through the GitLab web UI or your local shell as follows:

    ```shell
    git push origin --delete my-fix-branch
    ```

* Check out the master branch:

    ```shell
    git checkout master -f
    ```

* Delete the local branch:

    ```shell
    git branch -D my-fix-branch
    ```

* Update your master with the latest upstream version:

    ```shell
    git pull --ff upstream master
    ```

## <a name="rules"></a> Coding Rules
To ensure consistency throughout the source code, keep these rules in mind as you are working:

* All features or bug fixes **must be tested** by one or more [specs][unit-testing].
* All base theme code **must be documented** with phpdoc and jsdoc where relevant.
* We follow the rules contained in our 
  [Best Practices][barrel-dev-best-practices], but take note to the following:
    * Wrap all code at **120 characters**.
		* Do not mix spaces with tabs while indenting code.
    * Instead of complex inheritance hierarchies, we **prefer simple objects**. We use prototypal
      inheritance only when absolutely necessary.

## <a name="commit"></a> Git Commit Guidelines

We have very precise rules over how our git commit messages can be formatted.  This leads to **more
readable messages** that are easy to follow when looking through the **project history**.  But also,
we use the git commit messages to **integrate with JIRA and HipChat**. *This is not yet enforced.*

The commit message formatting can be added using a typical git workflow or through the use of a CLI wizard ([Commitizen](https://github.com/commitizen/cz-cli)). To use the wizard, run `npm run commit` in your terminal after staging your changes in git.

### Commit Message Format
Each commit message consists of a **header**, a **body** and a **footer**.  The header has a special
format that includes a **type** and a **subject**:

```
<type>: <subject>
<BLANK LINE>
<body>
<BLANK LINE>
<footer>
```

The **header** is mandatory and the **footer** is optional.

Any line of the commit message cannot be longer 100 characters! This allows the message to be easier
to read on GitLab as well as in various git tools.

### Revert
If the commit reverts a previous commit, it should begin with `revert: `, followed by the header of the reverted commit. In the body it should say: `This reverts commit <hash>.`, where the hash is the SHA of the commit being reverted.

### Type
Must be one of the following:

* **feat**: A new feature
* **fix**: A bug fix
* **docs**: Documentation only changes
* **style**: Changes that do not affect the meaning of the code (white-space, formatting, missing
  semi-colons, etc)
* **refactor**: A code change that neither fixes a bug nor adds a feature
* **perf**: A code change that improves performance
* **test**: Adding missing tests
* **chore**: Changes to the build process or auxiliary tools and libraries such as documentation
  generation

### Subject
The subject contains succinct description of the change:

* use the imperative, present tense: "change" not "changed" nor "changes"
* do not capitalize first letter
* no dot (.) at the end

### Body
Just as in the **subject**, use the imperative, present tense: "change" not "changed" nor "changes".
The body should include the motivation for the change and contrast this with previous behavior. The body should also reference any JIRA tickets by key.

* use (* or -) to delineate line items
* 

### Footer
The footer should contain any information about **Breaking Changes** and is also the place to
reference GitLab issues or merge requests that this commit **Closes**.

**Breaking Changes** should start with the word `BREAKING CHANGE:` with a space or two newlines. The rest of the commit message is then used for this.

A detailed explanation can be found in this [document][commit-message-format].

## <a name="info"></a> Further Information
You can find out more detailed information about contributing in the
[BarrelBaseTheme documentation][contributing].