# Kirby Selectplus Field

This field adds an add button to a select field that allows the user to add new options to a select field on the fly. It is only intended to work with select fields that query subpages of a given folder.

Example use case: In an event page, you want to have a select field that allows the user to select a location. Locations are stored as subpages of the locations page. It may happen that when you create a new event, the location you want to select doesn't exist yet. Usually, you would then have to go to the locations page and add a new location, then go back to your event page and select the new location. With this field, you can do this right from the event page.


## Use in blueprint:

```
locations:
    label: Locations
    type: selectplus
    formfields:
      title:
        placeholder: Location Name
      city:
        placeholder: City
      address
        placeholder: Address
    parent: locations
    template: location
    width: 1/3
    options: query
    query:
      page: locations
      fetch: children
      value: '{{uid}}'
      text: '{{title}}'
 ```
