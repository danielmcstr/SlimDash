# SlimDash
SlimDash is a simple dashboard written with Slim Framework.  The goal of this project is to provide a platform for Rapid Application Development (RAD) of internal Single Page Applications (SPAs).

# Goals
The immediate goal of this project is to try to get to a Product as quickly as possible.  The requirements breakdown are as followed:

[] Firebase - so we can quickly get things up and running without a Database.  Also, just because we want to do it as a proof of concept.

[] Modular - for ease of update, maintenance, and for the "What if" questions.
    * What if we want to switch out Firebase?
    * What if we want to add more stuff?
    * What if ...?

[] AdminLTE - this is our dashboard starter theme.

[] Themable - support theming on both a macro (platform) and micro (user) level.

[] SPAs Security - each applications are server-side secured by URL router.

[] Documentations - document how to do things manually and then build the SPA tool to automate it.

# Additionally, SlimDash is not a CMS
Unlike similar projects, SlimDash does not try to be a CMS so we will only implement a backend UI.  If you want a CMS, there are plenty of better choices: OctoberCMS, Concrete5, etc...

To run:
```
php -S 0.0.0.0:8888 -t public
```

