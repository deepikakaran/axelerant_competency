# axelerant_competency

CONTENTS OF THIS FILE
---------------------

 * About Module
 * Installation
 * Features

# About Module
--------------

This module contains the form alter for Site Information form to include additional fields. Provides a custom route to expose json data based on the arguments passed.

# Installation
--------------

1. Enable Customize Site Information Form module in:
       admin/modules

2. Administration page available at:
    admin/config/system/site-information

4. Input the "Site API key" in System site settings.

# Features
----------

1. Allows to add Site API Key
2. Returns the node details as JSON format
   Sample Path: http://localhost/page_json/{site_api_key}/{nid}
