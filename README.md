Opauth-CampaignMonitor
=============
[Opauth][1] strategy for CampaignMonitor authentication.

This strategy relies on an API endpoint that is currently stable, but undocumented (```https://api.createsend.com/api/v3.1/me.json```).

Getting started
----------------
1. Install Opauth-CampaignMonitor:

   Using git:
   ```bash
   cd path_to_opauth/Strategy
   git clone https://github.com/t1mmen/opauth-campaignmonitor.git CampaignMonitor
   ```

  Or, using [Composer](https://getcomposer.org/), just add this to your `composer.json`:

   ```bash
   {
       "require": {
           "t1mmen/opauth-campaignmonitor": "*"
       }
   }
   ```
   Then run `composer install`.


2. Create a CampaignMonitor application ([see instructions][2])

3. Configure Opauth-CampaignMonitor strategy with ```client_id```, ```client_secret``` and ```scope``` (See [permissions][2])

4. Direct user to `http://path_to_opauth/campaignmonitor` to authenticate

Strategy configuration
----------------------

Required parameters:

```php
<?php
'CampaignMonitor' => array(
  'client_id' => 'YOUR CLIENT ID',
  'client_secret' => 'YOUR CLIENT SECRET',
  'scope' => 'YOUR,SCOPES,HERE'
)
```

License
---------
Opauth-CampaignMonitor is MIT Licensed
Copyright © 2014 Timm Stokke (http://timm.stokke.me)

[1]: https://github.com/opauth/opauth
[2]: https://www.campaignmonitor.com/api/getting-started/#authenticating_with_oauth
