# Kirby Selectplus Field

This field adds an add button to a select field that allows you to add new options to a select field on the fly. It is only intended to work with select fields that query subpages of a given folder.

Example use case: In an event page, you have a select field that allows you to select a location. Locations are stored as subpages of the locations page. It may happen that when you create a new event, the location you want to select doesn't exist yet. Usually, you would then have to go to the locations page and add a new location, then go back to your event page and select the new location. With this field, you can do this right from the event page.

## Installation

### Download

[Download the files](https://github.com/texnixe/kirby-selectplus/archive/master.zip) and place them inside `site/plugins/selectplus`.

### Kirby CLI
Installing via Kirby's [command line interface](https://github.com/getkirby/cli):

    $ kirby plugin:install texnixe/kirby-selectplus

To update the Selectplus plugin, run:

    $ kirby plugin:update texnixe/kirby-selectplus

### Git Submodule
You can add the Selectplus plugin as a Git submodule.

    $ cd your/project/root
    $ git submodule add https://github.com/texnixe/kirby-selectplus.git site/plugins/selectplus
    $ git submodule update --init --recursive
    $ git commit -am "Add Kirby Selectplus plugin"

Run these commands to update the plugin:

    $ cd your/project/root
    $ git submodule foreach git checkout master
    $ git submodule foreach git pull
    $ git commit -am "Update submodules"
    $ git submodule update --init --recursive



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

### Formfields option

The formfield option accepts a set of fields that you want to have in your form. Currently, the first field must be the title field. Each field should also have a placeholder.

Todo:

- add field options (required etc.)
