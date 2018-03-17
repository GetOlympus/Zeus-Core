# Zeus Tooltip  

## Install

````html
<!-- In your <body> HTML tag -->
<a href="https://github.com/crewstyle " title="Achraf Chouk's profile on Github.com" class="tooltip">
    Click here.
</a>
````

````javascript
// In your main JS file with default options
$('.tooltip').tooltip({
    css: 'tooltip',
    position: 'bottom'
});
````

## Settings

Option      | Type      | Default   | Description                                   | Accepted values
------      | ----      | -------   | -----------                                   | ---------------
css         | string    | 'tooltip' | CSS class name assigned to Tooltip            | 
delayIn     | integer   | 0         | Delay in milliseconds before opening tooltip  | 
delayOut    | integer   | 0         | Delay in milliseconds before closing tooltip  | 
fade        | boolean   | false     | Transition animation                          | `true` `false`
position    | string    | 'top'     | Tooltip position                              | `top` `bottom` `left` `right`
offset      | integer   | 0         | Tooltip offset between element and itself     | 
onHidden    | function  | null      | Callback called when the tooltip is hidden    | 
onShown     | function  | null      | Callback called when the tooltip is shown     | 
trigger     | string    | 'hover'   | Event to bind to open or close tooltip        | `hover` `click` `focus`

## Dependencies

+ jQuery **latest version**

---

**Built with ♥ by [Achraf Chouk](http://github.com/crewstyle "Achraf Chouk") ~ (c) since 2015.**
