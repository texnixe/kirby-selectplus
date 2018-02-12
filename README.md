# Kirby Selectplus Field

Under development. In general, this seems to work, but has some glitches when a structure field with an add button is used in the same form. If anyone can help with this, let me know.

Special use case Kirby select field with button that allows you to create new subpages as options "on the fly".


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
  
