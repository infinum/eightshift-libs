#!/usr/bin/env sh

set -e

setupFile='setup.json';

# Check if dump exists
if [ ! -f $setupFile ]; then
  echo "${RED}Fail! File $setupFile doesn't exist i the root of your project!${NC}"
  exit 1
fi

# Check if core key exists in config.
core=$(jq "select(.core) | .core" $setupFile);

# Install core version.
if [[ ! -z "$core" ]]; then
  wp core update --version=$core --force

  echo "-------------------------------------"
fi

# Check if plugins key exists in config.
plugins=$(jq "select(.plugins) | .plugins" $setupFile);

if [[ ! -z "$plugins" ]]; then

  # Check if plugins core key exists in config.
  pluginsCore=$(jq "select($plugins.core) | $plugins.core" $setupFile);

  # Instale core plugins.
  for k in $(jq "$pluginsCore | keys | .[]" $setupFile); do
      name=$(jq -r "$k" $setupFile);
      version=$(jq -r "$pluginsCore[$k]" $setupFile);

      wp plugin install $name --version=$version --force;
      echo "-------------------------------------"
  done

  # Check if plugins github key exists in config.
  pluginsGithub=$(jq "select($plugins.github) | $plugins.github" $setupFile);

  # Instale github plugins.
  for k in $(jq "$pluginsGithub | keys | .[]" $setupFile); do
      name=$(jq -r "$k" $setupFile);
      version=$(jq -r "$pluginsGithub[$k]" $setupFile);

      wp plugin install https://github.com/$name/archive/$version.zip --force;
      echo "-------------------------------------"
  done
fi

# Check if themes key exists in config.
themes=$(jq "select(.themes) | .themes" $setupFile);

if [[ ! -z "$themes" ]]; then

  # Instale themes.
  for k in $(jq "$themes | keys | .[]" $setupFile); do
      name=$(jq -r "$k" $setupFile);
      version=$(jq -r "$themes[$k]" $setupFile);

      wp theme install $name --version=$version --force;
      echo "-------------------------------------"
  done
fi
