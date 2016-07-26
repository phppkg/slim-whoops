# inhere/slim-whoops

## Description

PHP whoops error on slim framework 3.
Reference [zeuxisoo/php-slim-whoops](https://github.com/zeuxisoo/php-slim-whoops/)


## Install

- use command

```
composer required inhere/slim-whoops
```

- use composer.json

edit `composer.json` _require-dev_ add

> stable

```
"inhere/slim-whoops": "~1.0",
```

> lastest

```
"inhere/slim-whoops": "dev-master",
```

run: `composer update`

## Usage

- add the middleware into slim application

```
$app->add(new \inhere\whoops\middleware\WhoopsTool($app));
```

## Options

- Opening referenced files with your favorite editor or IDE

```
$app = new App([
    'settings' => [
        'debug'         => true,
        'whoops.editor' => 'sublime' // Support click to open editor
    ]
]);
```

