# RichLists

_This repository is a work in progress._

This application is aimed to do the following:
* Show a table per NFT project / per chain with holders who have the most NFTs from one or more collections within the same project and chain.
* Cronjob: XRPL: fetch NFT data via Clio server from configured collections and store it in a local database
* Public API endpoints that returns same "richlist data" per project/chain: 
    * HTML endpoint (table)
    * JSON endpoint
* Login and subscription feature for new customers