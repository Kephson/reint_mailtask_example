# TYPO3 Extension `reint_mailtask_example`

> An example scheduler task with mail send out with Fluid-templates and multilanguage support.

## 1 Features

* Show how to generate a scheduler task with option to sendout mails in different languages

## 2 Usage

### 2.1 Installation

#### Installation using Composer

The recommended way to install the extension is using [Composer][1].

Run the following command within your Composer based TYPO3 project:

```
composer require renolit/reint-mailtask-example
```

#### Installation as extension from TYPO3 Extension Repository (TER) - not recommended

Download and install the [extension][2] with the extension manager module.

### 2.2 Minimal setup

1) Just install the extension and you are done

## 3 Report issues

Please report issue directly in the [issue tracker in the Github repository][3].

## 4 Administration corner

### 4.1 Test the scheduler commands

You can test the scheduler command via CLI or in the scheduler in TYPO3 backend.

### 4.2 Changelog

Please have a look into the [Github repository][3].

### 4.3 Release Management

Mailtask example uses [**semantic versioning**][4], which means, that
* **bugfix updates** (e.g. 1.0.0 => 1.0.1) just includes small bugfixes or security relevant stuff without breaking changes,
* **minor updates** (e.g. 1.0.0 => 1.1.0) includes new features and smaller tasks without breaking changes,
* and **major updates** (e.g. 1.0.0 => 2.0.0) breaking changes wich can be refactorings, features or bugfixes.

### 4.4 Contribution

**Pull Requests** are gladly welcome! Nevertheless please don't forget to add an issue and connect it to your pull requests.
This is very helpful to understand what kind of issue the **PR** is going to solve.

Bugfixes: Please describe what kind of bug your fix solve and give us feedback how to reproduce the issue. We're going
to accept only bugfixes if we can reproduce the issue.

Features: Not every feature is relevant for the bulk of `reint_mailtask_example` users. In addition: We don't want to make `reint_mailtask_example`
even more complicated in usability for an edge case feature. It helps to have a discussion about a new feature before you open a pull request.


[1]: https://getcomposer.org/
[2]: https://extensions.typo3.org/extension/reint_mailtask_example/
[3]: https://github.com/Kephson/reint_mailtask_example
[4]: https://semver.org/
