# Mycelium-Opencart-plugin
An extension for Opencart to receive Bitcoin payments through Mycelium

<h3>Introduction</h3>

<p>
So yeah, the title is self explaining. A plugin to be able to accept Bitcoin payments in an 
<a href="https://www.opencart.com/" target="_blank">OpenCart</a> online store. 

Not only Bitcoin but almost any Altcoin can be accepted too, thanks to <a href="https://shapeshift.io/#/coins" target="_blank">ShapeShift</a>.
</p>
<hr/>
<h3>How to install</h3>

<p>
Download the "Upload" folder and zip it in a file called <code>whatever-youwant.ocmod.zip</code>.
Go to the Opencart module installer and upload the .zip there.
Done :D

If it doesn't work for any reason, like FTP not enabled or because of the difficulty of the previous steps ( :D ) you can manually install
it by placing the files in the equivalent directories of your website. With the equivalent directories I mean:
<ul>
  <li>Admin/Controller/Payment/Mycelium.php ----> Goes here ----> Admin/Controller/Payment/Mycelium.php</li>
  <li>Admin/Model/Payment/Mycelium.php      ----> Goes here ----> Admin/Model/Payment/MYcelium.php</li>
  <li>And so on... :)</li>
</ul>
<b>NOTE:</b> Check the compatibility section below :|
</p>
<hr/>

<h3>Compatibility</h3>
The plugin has been built on OpenCart Version 2.0.3.1. And has been tested on versions:
<ul>
  <li>...</li>
  <li>More coming soon</li>
</ul>

<hr/>

<h3>Contributing and Maintenance</h3>
<p>
Like always :)
<ul>
  <li>Open issues if you find something which needs to be fixed, or upgraded, or if you have any idea on how to improve the plugin.</li>
  <li>If there are issues open check them out to see if you can help with that. If you start working on a fix write a reply to the issue so
  everyone knows someone is already working on it (so we don't waste resources/time/things)<li/>
  <li>Fork it, Fix it, Pull-request-it</li>
  <li>There will be a TODO list with things which are pending, you can check it out too (when it exists :D )</li>
  <li>If you want to be an active maintainer of this repository let me know</li>
</ul>

<p>
Mmmm... Right now the priorities are testing the plugin on different OpenCart versions aaaand... well, wait for issues :) <br/>
One thing I wanted to do at first was to link the Plugin Admin Panel with the Mycelium Gear Admin Panel. This way we could change
the settings from the store backend and get them automatically changed at Mycelium. This can be done with the Mycelium developer API.
Then, with a single API key all the gateway setup can be automatically done. A new gateway would be created, all the settings set, everything.
We would even be able to import a gateway settings from Mycelium so the end user doesn't have to copy and paste everything from panel
to panel.
<br/>
As I said, it can be done but I haven't been able to generate a valid signature for that endpoint. The authentication system is exactly the
same as the used by the gateways but.. I don`t know, help me :(<br/>
hehe
I'll leave the documentation here <a href="https://admin.gear.mycelium.com/docs">https://admin.gear.mycelium.com/docs</a>
<hr/>
<b>OH!</b> And special thanks to the <a href="https://bitpay.com/">BitPay</a> guys, your <a href="https://github.com/bitpay/opencart-plugin">OpenCart Module</a> helped me to find the correct path when I started developing this one ;)
