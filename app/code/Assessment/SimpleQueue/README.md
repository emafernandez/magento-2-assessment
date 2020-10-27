## How to enable the queue message Module:
run `php bin/magento setup:upgrade` command to make sure the module will get installed.

## How to enable the queue message consumer:
In terminal execute: 

`php bin/magento queue:consumers:start SimpleQueueLogProductSku --max-messages=n`

_replace the n parameter with the amount of messages you would like to test with._

Test scenarios:
1. **Product View Instructions:** Navigate to any Product Detail Page (PDP).

results: if the queue consumer is running, you should see a new entry in the `var/log/consumers.log` file

2. **CLI Instructions:** From a terminal, execute `php bin/magento assessment:simplequeue:dispatch --product_id=<product_id>`
product_id=1 can be used.

results: if the queue consumer is running, you should see a new entry in the `var/log/consumers.log` file

#### How to use the command:
`bin/magento assessment:simplequeue:dispatch --product_id=<integer>`
     
**Description**:
  dispatches a simple queue message given a Product Id.

**Usage**:
  `assessment:simplequeue:dispatch [options]`

*Options*:
```
      --product_id=PRODUCT_ID  Product Id with which to dispatch the message
  -h, --help                   Display this help message
  -q, --quiet                  Do not output any message
  -V, --version                Display this application version
      --ansi                   Force ANSI output
      --no-ansi                Disable ANSI output
  -n, --no-interaction         Do not ask any interactive question
  -v|vv|vvv, --verbose         Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```
