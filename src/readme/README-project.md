# Project info

## Database
Before you start developing ask a lead developer to export you the latest version of the database.

## Development environment
  * `feature` branch
  * Development is done localy on your computer
  * Create `feature/feature-name` branch from `master` and create pull request to `staging` branch
  ```
  http://dev.boilerplate.com/
  ```

## Staging server
  * `staging` branch
  * QA testing and client tests and approvals
  * Never merge `staging` to `master` branch.
  ```
  https://staging.boilerplate.com/
  ```

## Production server
  * `master` branch
  * Create pull request from `feature` branch to `master` when it is production ready
  * **NO OVERRIDING THE DATABASE**
  ```
  https://boilerplate.com/
  ```
