# Contributing

``` bash
# Clone repo
git clone git@github.com:lequipefr/mockserver-behat-context.git
cd mockserver-behat-context/

# Install dependencies
composer install
```

## Running tests

``` bash
vendor/bin/phpunit
vendor/bin/behat
```

## Adding Behat phrases

Add Behat phrases in [src/Behat/MockServerContext.php](src/Behat/MockServerContext.php).

Then add a functional test in `features/`,
and once all tests passes, automatically update the Behat phrases documentation
in [docs/behat-phrases.md](docs/behat-phrases.md) by running:

``` bash
php rebuild-features-to-doc.php
```

This will extract your behat test to this markdown reference documentation.

## Adding methods to builder

Add methods in `src/Builder/` classes.

Then add a phpunit test to assert this new methods generated expected json.
