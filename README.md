# Democratic Party of Virginia Donor Display Screen Plugin for WordPress
This code was created to display donors from ActBlue for the 2019 Blue Commonwealth Gala.  It allows the developer to set an ActBlue contribution form identifier and display a contribution within a defined auto-refresh time rate.  An ActBlue Webhook notification listener file is included in this package as well.  Please note that it is based on the Webhook schema defined as of May 7, 2019.  Further disclaimers and references available below.

## Stack
This plugin was created and tested on WordPress 5.2.

## Requirements
Per ActBlue's Webhook policy, the secure endpoint (code contained within the listener folder) must be password protected using Basic Authentication.  To make the secure endpoint work, the WordPress administrator must create a user with 'edit other posts' privileges and use the WP-API Basic-Auth plugin available at https://github.com/WP-API/Basic-Auth.

## Disclaimer
Please note that this code is offered as is.  For more information about the ActBlue Webhooks schema, please visit https://secure.actblue.com/docs/webhooks.