# inhere/slim-whoops

## Description

PHP whoops error on slim framework 3.

Reference [zeuxisoo/php-slim-whoops](https://github.com/zeuxisoo/php-slim-whoops/)

- show pretty error page.
- record error/exception log.

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

> latest

```
"inhere/slim-whoops": "dev-master",
```

run: `composer update`

## Usage

- add the middleware into slim application

```
$app->add(new \inhere\whoops\WhoopsMiddleware($app));
```

## Options

- Opening referenced files with your favorite editor or IDE

```
$app = new App([
    'settings' => [
        'whoops' => [
            'debug'  => true,
            'editor' => 'sublime' // Support click to open file in the editor
        ]
    ]
]);
```

