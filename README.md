TSCF - Tarosky Custom Field manager
==================================

Contributors: Takahashi_Fumiki, tarosky  
Tags: custom field  
Requires at least: 5.9.0  
Requires PHP: 7.4  
Tested up to: 6.5  
Stable tag: 1.2.0  
License: GPLv3 or later  
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

TaroSky's Custom Field manager. Scalable, Well-Structured and Maintainable. Requires PHP5.4 or later.

## Description

Custom fields are stuff box for WordPress. They can store almost everything. Number,  Date, CSV and nested array.

Besides that, custom fields can be used for many purpose. 

* To display extra information for posts.
* To sort posts by their event date.
* To hide posts from unregistered user.

Thus, they can be easily uncontrollable.

**TSCF** provides scalable way. A JSON file indicates what custom fields should be and you can get their value.

You can maintain it on <abbr title="Version Control System">VCS</abbr> like git or svn.

Additional features:

- Supports child theme. you can override config file.
- JSON editor. But we don't recommend it. Don't edit it directly on production environment.

## Installation

1. Upload the plugin files to the `/wp-content/plugins/tscf` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. If JSON doesn't exist, put it or create from `Appearance > Custom Fields Config`.

We also host this plugin on [github](https://github.com/tarosky/tscf), build like below. composer and npm are required.

<pre>
# Move to your plugin directory.
cd wp-content/plugins
# Clone repo.
git clone git@github.com:tarosky/tscf.git
# Buidl plugin.
cd tscf
composer install
npm install
npm run package
# If you want watch assets, run watch.
npm run watch
</pre>

Of course, any pull requests are welcomed :)


## Frequently Asked Questions

### I founded a bug.

Please make issue on [github](https://github.com/tarosky/tscf/issues).

## Screenshots

W.I.P

## Changelog

### 1.2.0

* Add support for term meta (taxonomy).

### 1.1.1

* Fix fatal error on composer loading.

### 1.1.0

* Composer ready.

### 1.0.0

* Initial release.
