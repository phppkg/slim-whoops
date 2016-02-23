# inhere/slim-whoops

## Description

Reference [zeuxisoo/php-slim-whoops](https://github.com/zeuxisoo/php-slim-whoops/)


## Install

- use composer

edit `composer.json`

_require-dev_ add

```
"inhere/slim-whoops": "dev-master",
```

_repositories_ add 

```
"repositories": [
        {
            "type": "git",
            "url": "https://git.oschina.net/inhere/slim-whoops"
        }
    ]
```

run: `composer update`

## Usage

- add the middleware into slim application

```
$app->add(new \inhere\whoops\middleware\WhoopsTool());
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

