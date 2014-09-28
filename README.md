Wbty Accordion
==============

Wbty Accordion is a simple Joomla plugin to make it easy to add jQuery UI Accordion interface into your article content.

Implementation is simple, after installing the plugin and making sure that it is enabled, just wrap the content in tags like the following:

    {wbty_accordion}
    <h3>Title for Accordion</h3>
    <p>Accordion content.</p>
    {/wbty_accordion}

When the page loads, the h3 elements will be shown on the page, with the content below each h3 element tied to that element.

### Don't want to use h3 elements?

Use the settings in the plugin manager to change which header element will be used. (This would allow you to put h3 elements in the content that is hidden as part of the accordion.)

*Note: This setting is site wide. There is currently no way to set this in individual instances (yet!).
