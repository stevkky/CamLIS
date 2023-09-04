$.fn.extend({
    treeview: function (o) {

        var openedClass = 'glyphicon glyphicon-plus-sign';
        var closedClass = 'glyphicon glyphicon-minus-sign';
        var collapsed   = true;
        var animated    = false;

        if (typeof o != 'undefined') {
            if (typeof o.openedClass != 'undefined') {
                openedClass = o.openedClass;
            }
            if (typeof o.closedClass != 'undefined') {
                closedClass = o.closedClass;
            }
            if (typeof o.collapsed != 'undefined') {
                collapsed = o.collapsed;
            }
            if (typeof o.animated != 'undefined') {
                animated = o.animated;
            }
        };

        //initialize each of the top levels
        var tree = $(this);
        tree.addClass("tree");
        tree.find('li').has("ul").each(function () {
            var branch = $(this); //li with children ul
            branch.prepend("<i class='indicator " + (collapsed ? openedClass : closedClass) + "'></i>");
            branch.addClass('branch');
            branch.on('click', function (e) {
                if (this == e.target) {
                    var icon = $(this).children('i.indicator');
                    icon.toggleClass(openedClass + " " + closedClass);
                    if (animated) {
                        $(this).find("ul").slideToggle();
                    } else {
                        $(this).find("ul").toggle();
                    }
                }
            })
            
            if (collapsed) {
                branch.find("ul").toggle();
            }
        });
        
        //fire event from the dynamically added icon
        tree.find('.branch .indicator').each(function () {
            $(this).on('click', function () {
                $(this).closest('li').click();
            });
        });
        
        //fire event to open branch if the li contains an anchor instead of text
        tree.find('.branch > a').each(function () {
            $(this).on('click', function (e) {
                $(this).closest('li').click();
                e.preventDefault();
            });
        });
        
        //fire event to open branch if the li contains a button instead of text
        tree.find('.branch > button').each(function () {
            $(this).on('click', function (e) {
                $(this).closest('li').click();
                e.preventDefault();
            });
        });
    }
});
