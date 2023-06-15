# JFilters - YOOtheme Pro Integration

The plugin makes possible for [Yootheme Pro](https://yootheme.com/page-builder) page builder, to customize the results of [JFilters](https://blue-coder.com/jfilters).

## Setup 
1. Install it through the Joomla installer.
2. Go to **plugins > System**, find the plugin named **System - JFilters for YOOtheme Pro** and publish it.

After doing that you should be able to customize the JFilters results form Yootheme Pro.

## License
[GNU General Public License v.3](https://www.gnu.org/licenses/gpl-3.0.en.html)

## Developer Information

### General Functionality
YOOtheme uses events massively. The plugin is fetching data to YT based on the triggered event each time.
The plugin's *bootstrap.php* declares the function that will be executed in each event triggered from YT.
Example:
```
'builder.template' => [
TemplateListener::class => 'matchTemplate',
]
```

The event `'builder.template'` will trigger the function `matchTemplate` from the class `TemplateListener`.
YOOtheme uses reflection to pass the proper arguments for these functions. 
But make sure that you check the classes where they are originally called to decide the proper arguments.

### Plugin declared functions
1. 
    ```
    SourceListener::initCustomizerYT3(),
    SourceListener::initCustomizerYT4()
    ```
    These are declaring the JFilters template to the YT customizer (the overall YT page) by adding it to the list of templates.
    As templates here, we mean the type of page that can be build/designed (NOT the already saved templates/pages).
    Declaring the template properly, has an effect both in finding the proper page (when a new template is created) 
    and in matching the already saved templates with the loaded page.

2. 
   ```
   TemplateListener::matchTemplate()
   ```
   + Matches a saved template with the currently loaded page 
   + Also matches the type of the template with the proper one (under pages) when a new YT template is created.



## Copyright
Copyright © 2023 [blue-coder.com](https://blue-coder.com/) / Athanasios Terzis
