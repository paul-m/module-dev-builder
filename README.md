Drupal Contrib Module Dev Builder
=================================

This is a handy project for building a Drupal environment around a module you're
working on.

Setup
-----

* Clone the repo and `cd` into it.
* Type `composer install`.
* Edit RoboFile.php so the constants refer to the contrib project you'll be working on.
* Type `./bin/robo build`.
* Optionally, you can type `./bin/robo build -i` to try and install a site.

TODO
----

* Add flexibility for D7, D8, D9.
