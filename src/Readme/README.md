# {Project name}

This is the official repository of the {Project name}.

## Requirements

1. PHP 7.4 or higher
2. Node 16+
3. [Node.js](https://nodejs.org/en/)
4. [Composer](https://getcomposer.org/)
5. [(Optional) WP cli](https://wp-cli.org/)

## Installation

The project is built using [eightshift-boilerplate](https://github.com/infinum/eightshift-boilerplate), [eightshift-libs](https://github.com/infinum/eightshift-libs), and [eightshift-frontend-libs](https://github.com/infinum/eightshift-frontend-libs).

For more details on how to use them, check out the [official documentation](https://eightshift.com/).

Once you clone this repository, you'll need to build it:

```bash
cd wp-content/themes/{theme-name} // Or plugins/{plugin-name}

composer install
npm install
npm run build
```

## Development

Using the latest boilerplate means that you have WP-CLI scripts available. To use them just type: 

```bash
wp boilerplate --help
```

The project uses PRS-4 autoloading and follows PSR-12 coding standards. Until the DevOps set up the staging site, all the features should be merged to `staging` branch.
 
### Environments

We have the following environments (you can also check this in the `setup.json` file):

#### Development

Local development environment. 

URL: `{local-project.test}`

#### Staging 

Built and deployed automatically from the `staging` branch using CI.

URL: `{staging URL}`

#### Production 

Built automatically from the `main` branch using CI. Manually deployed lead developer.

URL: `{production URL}`

## Extra dev notes
