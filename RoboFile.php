<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{

  const STABLE_DRUPAL_BRANCH = '7.x';
  const DEVELOPMENT_MODULE = 'bean';
  const DEVELOPMENT_MODULE_BRANCH = '7.x-1.x';
  const COMPOSER_INSTALL = FALSE;

  public function build($opts = ['install|i' => false]) {
    // Repo.
    $this->taskGitStack()
      ->cloneShallow('https://git.drupal.org/project/drupal.git', 'drupal', static::STABLE_DRUPAL_BRANCH)
      ->run();
    $this->taskGitStack()
      ->dir('drupal')
      ->pull('origin', static::STABLE_DRUPAL_BRANCH)
      ->run();

    $this->taskGitStack()
      ->cloneRepo('git@git.drupal.org:project/' . static::DEVELOPMENT_MODULE . '.git', 'bean', static::DEVELOPMENT_MODULE_BRANCH)
      ->run();
    $this->taskGitStack()
      ->dir(static::DEVELOPMENT_MODULE)
      ->pull('origin', static::DEVELOPMENT_MODULE_BRANCH)
      ->run();

    // Symbolic link the contrib repo within Drupal.
    $this->_exec('ln -sf $(pwd)/' . static::DEVELOPMENT_MODULE . ' $(pwd)/drupal/sites/all/modules/');

    // Composer.
    if (static::COMPOSER_INSTALL) {
      $this->taskComposerInstall()
        ->dir('drupal')
        ->run();
      $this->taskExec('composer run-script drupal-phpunit-upgrade')
        ->dir('drupal')
        ->run();
      $this->taskComposerRequire()
        ->dir('drupal')
        ->dependency('digipolisgent/robo-drupal-console', '@stable')
        ->run();
    }

    // Install.
    // @todo Use drush with d7.
    if ($opts['install']) {
      $this->taskExec('./vendor/bin/drupal site:install standard --db-type="mysql" --db-host="127.0.0.1" --db-name="d8" --db-user="root" --db-pass="root" --db-port="8889" --site-name="foo" --site-mail="admin@mile23.com" --account-name=admin --account-mail="some@example.com" --account-pass="admin" --db-prefix="dev_" --force --no-interaction')
        ->dir('drupal')
        ->run();
      $this->taskExec('./vendor/bin/drupal site:status')
        ->dir('drupal')
        ->run();
      $this->taskExec('./vendor/bin/drupal uli 1')
        ->dir('drupal')
        ->run();
    }
  }

}
