# magento2-aws-sns
AWS SNS pushing service for magento2

# Copyright
This project is copyright by VinhCD Co,Ltd. (owned by hungnt)

# Contact
vinhcd.co.ltd@gmail.com

# User Guide
  
 1. Installation
 
 - It requires AWS PHP SDK to push message into SNS Service:
 
   composer require aws/aws-sdk-php
 
 - Simply put all files in app/code/ and run setup:upgrade to install it
 
 
 2. Configuration
 
 - Go to Admin → Stores → Configuration → VinhCD → AWS Configuration
 
 - Put your AWS Access / Secret key in General. (It’s highly recommend that you put your keys in env file by setting command: config:set vinhcd_aws/general/access_key your_key & config:set vinhcd_aws/general/secret your_secret). Then choose your default region.
 
 - Choose an event type and topic ARN to push into. Currently it just supports one event type: After Placing Order. There will be more events in future release.
 
 - Configure max number message send per queue and number of days old it will not be send.
 
 
 3. How it works
 
 - It works by pushing message asynchronously so it will not affect customer experience when buying your products.
 
 - The message will be pushed by cronjob belongs to Index group. By default it will run per minute to check and push message if available. You can change cron period for that group in Admin panel.
