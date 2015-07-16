#!/bin/bash

echo "After Script"
echo "-- Repo Slug: $TRAVIS_REPO_SLUG"
echo "-- PHP Version: $TRAVIS_PHP_VERSION"
echo "-- PULL Request: $TRAVIS_PULL_REQUEST"
printenv

#if [ "$TRAVIS_REPO_SLUG" == "bluzphp/framework" ] && [ "$TRAVIS_PULL_REQUEST" == "false" ] && [ "$TRAVIS_PHP_VERSION" == "5.6" ]; then
if [ "$TRAVIS_REPO_SLUG" == "bluzphp/framework" ] && [ "$TRAVIS_PHP_VERSION" == "5.6" ]; then

  echo -e "Publishing PHPDoc..."

  # move docs to `home` directory
  cp -R docs $HOME/docs-latest

  cd $HOME
  git config --global user.email "travis@travis-ci.org"
  git config --global user.name "travis-ci"
  git config --global push.default simple
  git clone --quiet https://${GITHUB_TOKEN}@github.com/bluzphp/bluzphp.github.io > /dev/null

  cd bluzphp.github.io
  echo "-- Clean"
  git rm -rf ./ > /dev/null

  echo "-- Copy"
  cp -Rf $HOME/docs-latest/* ./

  echo "-- Push"
  git add -f .
  git commit -m "PHPDocumentor (Travis Build : $TRAVIS_BUILD_NUMBER  - Branch : $TRAVIS_BRANCH)"
  git push -fq origin > /dev/null

  echo -e "Published PHPDoc to github.io\n"

fi