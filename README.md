<img src="https://raw.githubusercontent.com/pagaio-lab/magento1/master/pagaio.jpg" alt="Pagaio logo with coins" width="200px" height="200px" />

# Pagaio - Module for Magento 1

This module adds capabilities of Pagaio into your own shop running with Magento 1.

## Installation

You have many ways to install the module:

### Using composer

First you need to install the `magento-hackathon/magento-composer-installer` package as a requirement. [Please follow the README to install it](https://github.com/Cotya/magento-composer-installer#readme).

Then you just have to install the package using composer:

```bash
composer require pagaio/magento1-module
```

*You may need to install `monsieurbiz/mbiz_iwantmysymlinksback` ([see the module](https://github.com/monsieurbiz/Mbiz_IWantMySymlinksBack#readme)) too in case you use symlinks as deployment method for Magento modules using composer.*

### Using modman

Requirements: [follow installation instructions for modman](https://github.com/colinmollenhour/modman#requirements). Do not patch the `Template.php` file if you need it, please install the module `Mbiz_IWantMySymlinksBack` instead (see below).

To install using modan:

```bash
modman clone pagaio_connect https://github.com/pagaio-lab/magento1.git
```

*You may need to install `https://github.com/monsieurbiz/Mbiz_IWantMySymlinksBack.git` the same way ([see the module](https://github.com/monsieurbiz/Mbiz_IWantMySymlinksBack#readme)) in case you use symlinks as deployment method for Magento modules using modman.*

<!-- @TODO -->
<!-- ### Using the Magento Connect -->

### Using the good old Copy/Paste way

You just have to copy (using merge) the folders `app/`, `js/` and `skin/` into your Magento main folder.

```bash
cp -Rv app js skin my-magento-folder/
```

<!-- @TODO -->
<!-- ## Configuration -->

## Contribution

Please feel free to contribute by [sending a Pull Request](https://github.com/pagaio-lab/magento1/pulls)!

## Author & Contributors

This source code is provided by [Pagaio](https://pagaio.com/) itself.

Thank you to all [the contributors](https://github.com/pagaio-lab/magento1/graphs/contributors)!

## License

Please see the [LICENSE](https://github.com/pagaio-lab/magento1/blob/master/LICENSE) file.