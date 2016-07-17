# Hera Drag'n drop  

## Install

````html
<!-- In your <body> HTML tag -->
<div class="items">
    <div class="dragndrop">Item 1</div>
    <div class="dragndrop">Item 2</div>
    <div class="dragndrop">Item 3</div>
</div>
````

````javascript
// In your main JS file with default options
$('.items').dragndrop({
    handle: false,
    items: '.dragndrop'
});
````

## Settings

Option  | Type      | Default       | Description
------  | ----      | -------       | -----------
handle  | boolean   | false         | Create sortable lists with handles
items   | string    | '.movendrop'  | Specifiy which items inside the element should be sortable

## Dependencies

+ jQuery **latest version**
+ WordPress Sortable

---

**Built with ♥ by [Achraf Chouk](http://github.com/crewstyle "Achraf Chouk") ~ (c) since 2015.**
