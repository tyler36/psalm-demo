# Psalm <!-- omit in toc -->

- [Overview](#overview)
- [Installation](#installation)
- [Usage](#usage)
- [Configuration](#configuration)
  - [Plugins](#plugins)
- [Code Issues](#code-issues)
  - [Ignoring issues](#ignoring-issues)
    - [Generate a baseline](#generate-a-baseline)
- [Security analysis](#security-analysis)
- [VsCode](#vscode)

## Overview

Homepage: <https://psalm.dev/>
Requires:

- PHP >= 7.4
- Composer

## Installation

1. Install via composer

```shell
composer require --dev vimeo/psalm
```

1. Generate config file.

```shell
./vendor/bin/psalm --init
```

## Usage

- To scan files

```shell
vendor/bin/psalm
```

- To do a dry run of changes that can be fixed:

```shell
# Diff of fixable errors using psalter
vendor/bin/psalter --issues=all --dry-run
# Diff of fixable errors using psalm
vendor/bin/psalm --alter --issues=all --dry-run
```

- To fix errors, specify `--issues=all` to file all issues

```shell
# Fix issues with psalter
vendor/bin/psalter --issues=all
# Fix issues with Psalm's binary
psalm --alter --issues=all
```

## Configuration

```xml
<?xml version="1.0"?>
<psalm>
    <projectFiles>
        <directory name="src" />
    </projectFiles>
</psalm>
```

### Plugins

Plugins list: <https://packagist.org/?type=psalm-plugin>

- <https://github.com/psalm/psalm-plugin-laravel>
- <https://github.com/psalm/psalm-plugin-phpunit>
- <https://github.com/mortenson/psalm-plugin-drupal>
- <https://github.com/LordSimal/cakephp-psalm>
- <https://github.com/BafS/psalm-typecov>

## Code Issues

- There are 8 levels (1-8), where `1` is most strict and `8` is least strict.
- Default is `2`.

2 types of issues:

- `error`: Code is problematic. Psalm prints a message and returns a non-zero exit status.
- `info`: Psalm prints a message.
- `suppress`: Psalm ignores code issue

### Ignoring issues

- Add docblock or directly before the code issue.

```php
/**
 * @psalm-suppress InvalidReturnType
 */
function (int $a) : string {
  return $a;
}
```

- To ignore any error, comment as below:

```php
/** @phpstan-ignore-next-line */
echo $foo;

echo $foo; /** @phpstan-ignore-line */
```

#### Generate a baseline

A baseline tells Psalm to ignore all current code issues.
Commit the baseline for re-usability.

- Generate a baseline.

```shell
vendor/bin/psalm --set-baseline=psalm-baseline.xml
```

- Use baseline via CLI

```shell
vendor/bin/psalm --use-baseline=psalm-baseline.xml
```

- Or set baseline via configuration file.

```xml
<?xml version="1.0"?>
<psalm
       ...
       errorBaseline="./path/to/your-baseline.xml"
>
   ...
</psalm>
```

- After fixing errors, update the baseline to remove the error

```shell
vendor/bin/psalm --update-baseline
```

To ignore the current baseline:

```shell
vendor/bin/psalm --ignore-baseline
```

## Security analysis

Psalm can scan your code for possible insecure vectors.

- Tainted input: untrusted data sources influenced by users (`$_GET['id']`, `$_POST['email']` ...).
- Tainted sinks: output areas that should NOT receive untrusted data (`HTML templates`, `PDO`).

For example: Tainted HTML

```php
<?php

class A {
    public function deleteUser(PDO $pdo) : void {
        $userId = self::getUserId();
        $pdo->exec("delete from users where user_id = " . $userId);
    }

    public static function getUserId() : string {
        return (string) $_GET["user_id"];
    }
}
```

@see <https://psalm.dev/docs/security_analysis/>

Run analysis:

```shell
vendor/bin/psalm --taint-analysis
```

If you are using a baseline, disable it or set a different baseline file:

```shell
# Disable baseline
vendor/bin/psalm --taint-analysis --ignore-baseline
# Use a different tainted baseline
vendor/bin/psalm --taint-analysis --set-baseline=psalm-tainted-baseline.xml
```

## VsCode

[getpsalm.psalm-vscode-plugin](https://marketplace.visualstudio.com/items?itemName=getpsalm.psalm-vscode-plugin)
