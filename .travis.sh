#!/bin/bash

echo "After Script"
echo "-- Repo Slug: $TRAVIS_REPO_SLUG"
echo "-- Repo Tag: $TRAVIS_TAG"
echo "-- PHP Version: $TRAVIS_PHP_VERSION"
echo "-- PULL Request: $TRAVIS_PULL_REQUEST"

if [ "$TRAVIS_REPO_SLUG" == "bluzphp/framework" ] && [ "$TRAVIS_PHP_VERSION" == "7.0" ]; then
  echo "Send code coverage report"
  wget https://scrutinizer-ci.com/ocular.phar
  php ocular.phar code-coverage:upload --format=php-clover tests/_output/coverage.xml
fi

if [ "$TRAVIS_REPO_SLUG" == "bluzphp/framework" ] && [ "$TRAVIS_TAG" != "" ] && [ "$TRAVIS_PULL_REQUEST" == "false" ] && [ "$TRAVIS_PHP_VERSION" == "7.0" ]; then
  echo "Generate Documentation"
  wget http://phpdox.de/releases/phpdox.phar
  php phpdox.phar

  echo "Publishing"
  # move docs to `home` directory
  cp -R docs/html $HOME/docs-latest

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
  git commit -m "PHPDocumentor (Travis Build: $TRAVIS_BUILD_NUMBER@$TRAVIS_TAG)"
  git push -fq origin > /dev/null

  echo -e "Published to github.io\n"
fi
