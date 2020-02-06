<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{

  const STABLE_DRUPAL_BRANCH = '8.6.x';

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
      ->cloneRepo('git@git.drupal.org:project/examples.git', 'examples', '8.x-1.x')
      ->run();
    $this->taskGitStack()
      ->dir('examples')
      ->pull('origin', '8.x-1.x')
      ->run();

    // Symbolic link the examples repo within Drupal.
    $this->_exec('ln -sf $(pwd)/examples $(pwd)/drupal/modules/');

    // Composer.
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

    // Install.
    if ($opts['install']) {
      $this->taskExec('./vendor/bin/drupal site:install standard --db-type="mysql" --db-host="127.0.0.1" --db-name="d8" --db-user="root" --db-pass="root" --db-port="8889" --site-name="foo" --site-mail="admin@mile23.com" --account-name=admin --account-mail="paul@mile23.com" --account-pass="admin" --db-prefix="examples_" --force --no-interaction')
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
