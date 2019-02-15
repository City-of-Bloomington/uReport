# Testing Plan
These are the things to check to make sure everything is working.  While doing these checks, it is best to tail the Apache error log.  uReport should not ever write any errors to the error log.  Any message that shows up in the Apache error log is an unforseen mistake that should be fixed.

```bash
tail -f /var/log/apache2/error.log
```

## Authentication
Make sure users can log, even behind a reverse proxy

* Deploy the application and host it behind a reverse proxy
    * Make sure you can log in at the host URL
    * Make sure you can log in at the proxy URL

## Make sure search is working
Solr is responsible for the data displayed in the search results.

* Load the homepage
    * Make sure tickets are listed
    * Make sure the map results are displaying tickets and clusters
    * Search total should be smaller when not logged in
* Filter the search
    * Make sure the buttons work to remove each filter
    * Make sure sorting is working
        * Sorting must preserve the filters
* Do a keyword search
    * Make sure the text results reflect the search
    * Make sure the map  results reflect the search

The text and map result counts may not show the same number.  This is expected, as not all tickets have location information.   Tickets can only be mapped if we have a lat/long.

The total search results should only include tickets you are permitted to view.  If you are not logged in, you should see a significantly smaller number than when you are.

## Saved Searches
Make sure users can save a custom search
* Do a search
* Save the search results as something
    * Make sure the saved search shows up in the "Saved Searches" drop down
    * Make sure the saved search parameters match the original search
* Go to "My Account"
    * Make sure you can delete the newly saved search

## Ticket View
* Make sure media thumbnails are showing up
* Make sure media can be viewed

## Ticket Creation and Editing
Create a new ticket and make sure everything gets saved correctly

* Create a new ticket
    * Choose a location
    * Assign it to someone you can ask if they received the email notification
    * Add yourself as the Reporting person so you'll receive an email
    * Make sure the assigned person receives an email
    * Make sure you receive an email
    * Make sure the URL in the email is to the correct server
        * The URL should match how you accessd the server (proxy or not)
    * Make sure the map displays the location correctly
* Upload a photo
    * Make sure the thumbnail shows up
    * Make sure the full size photo can be browsed to
* Comment on the new ticket
    * Make sure the cancel button returns you to the ticket view screen
    * Make sure the comment shows up in the ticket history
    * Make sure everyone receives an email
* Reassign the ticket to yourself
    * Make sure the cancel button returns you to the ticket view screen
    * Make sure the comment shows up in the ticket history
    * Make sure everyone receives an email
* Update the ticket
    * Make sure the cancel button returns you to the ticket view screen
    * Make sure the changes are reflected in the ticket
    * Make sure the comment shows up in the ticket history
    * Make sure everyone receives an email
* Change the ticket category
    * Make sure the cancel button returns you to the ticket view screen
    * Make sure the comment shows up in the ticket history
* Change the ticket location
    * Make sure the cancel button returns you to the ticket view screen
    * Make sure you can type a search to select an address
    * Make sure you can use the map chooser to select an address
    * Make sure the change is reflected
        * Make sure the map shows the new location
    * Make sure the comment shows up in the ticket history
* Do Action: Follow up
    * Make sure the cancel button returns you to the ticket view screen
    * Make sure the comment shows up in the ticket history
    * Make sure everyone receives an email
* Delete the ticket
    * Make sure the ticket is removed


## Open311

* Check GET services list
    * Make sure HTML works
    * Make sure json works
    * Make sure XML  works

* Check GET service definition
    * Make sure HTML works
    * Make sure json works
    * Make sure XML  works

* Check GET service request
    * Make sure HTML works
    * Make sure json works
    * Make sure XML  works

* Check POST service request
    * Use the open311-nodejs client to test posting
        * Make sure you can upload an image
        * Make sure any additional attributes are saved

# Reports
Make sure all the reports are working.  Staff and Volume results should be downloadble as CSV.

* Choose a report
    * Make sure the filtering works
        * Make sure date filtering is working
    * Make sure CSV download works
