TYPO3 CMS Extension "simpleonetimeaccount"
==========================================

Requires TYPO3 6.0+

### Overview

This extension adds a simple and FlexForm-configurable plugin that enables the creation of one-time Frontend User accounts and immediate login. The extension is inspired by EXT:onetimeaccount but has less features, but also less dependencies. It is based on Extbase and Fluid.

A new user is created on each login, even if the user is using the same login information.


### Installation

Clone this repository to your `typoconf/ext` folder like that:

    git clone https://github.com/visol/ext-simpleonetimeaccount simpleonetimeaccount

### Configuration

Apart from the template paths that are configured in TypoScript, the extension can be configured in the FlexForm.

Parameters that can be configured:
 
 * **User fields to display** (mandatory): Choose which fields are displayed in the registration form.
 * **Mandatory fields**: Choose the fields that need to be filled by the user.
 * **User groups to assign**: Select the user group(s) that the created users will be assigned to.
 * **Target page after registration** (mandatory): After successful registration, the user will be redirected to this page.
 * **User storage folder** (mandatory): The page that contains the Frontend Users.


### Technical background

After posting the login form, the extension checks validates all mandatory fields and is doing a special check for a valid e-mail address, if the e-mail field is amongst the configured field. If there are errors, there is a message and the CSS class "has-error" is added to the respective form group.

If the check is successful, a random username and a random, secure password are generated (using the iSecurity PHP library shipped with TYPO3 Flow) and a new Frontend User is added. After successfully adding the Frontend User, this user is authenticated and the user is redirected to the configured "targetPage".

The markup in the default templates is compatible to Bootstrap 3. There is no default styling.

### Development ideas

 * Use Extbase (non-domain model) validation instead of the own solution
 * Re-use an existing account if the same user information is used.
 * Cleanup command to remove old Frontend Users.
 * Make the plugin configurable by TypoScript.

### Contribute

Feel free to fork the extension and create pull requests for new features (that are respecting backwards compatibility).