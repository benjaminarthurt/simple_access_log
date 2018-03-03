# Simple Access Log
Drupal 8 Simple Access Log replicates the basic features/functions of the Core Access Log functions that were included as part of the statisitics module, that were not ported to Drupal 8.

Read more about the features and use of Simple Access Log at its Drupal.org project page at ...?

## Installation and use

The Simple Access Log module can be installed like other Drupal modules by placing this directory
in the Drupal file system (modules directory) and enabling on
the Drupal Modules page (/admin/modules).


## Configuration

Simple Access Log can be configured on your Drupal site at Administration - Configuration (/admin/config/system/simple_access_log).

### Options
* Don`t log UID 0 - Do not log Anonymous page visits
* Don`t log UID 1 - Do not log Super User (UID 1) page visits
* Don`t log Admin users -Do not log for users with "Administrator" role
* Don`t log Admin paths - Skip logging for any admin paths. i.e. those paths that start with "/admin/*".
* Log retention period - Length of time to keep access logs.
** 4 Months (16 Weeks)
** 4 Weeks
** 2 Weeks
** 1 Week
** 3 Days
** 1 Day
