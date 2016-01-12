(function($){ 
    var defaults = {
        tabBox : ".j-tab .gl-service-tab_box",
        tabEvent : "mouseover",
        tabStyle : "",
        direction : "",
        aniMethod : "swing",
        aniSpeed : "fast",
        onStyle : "z-service-cur",
        autoScroll : false,
        autoSpeed : 4000,
        setTimeObj : null,
        moveNum : 0,
        menuChildSel : "*",
        cntChildSel : "*"
    };


    $.fn.tab = function( options ){
        var opts = $.extend({}, defaults, options),
            $container = null,
            $menus = null,
            moveObj = {};
        var step = 0;

        this.each(function(i){

            var _this   = $(this);

            $menus  = _this.children( opts.menuChildSel );

            autoLen = $menus.length;

            $container  = $( opts.tabBox );

            if( !$container) return;


            if( opts.direction == "left"){
                step = $container.children().outerWidth(true);
                $container.css({ 'width' : step * $container.children().length }).children().css({ 'width' : step });
                bindAutoWidth();
            }

            $menus[ opts.tabEvent ]( function(){
                var index = $menus.index( $(this) );
                $( this).addClass( opts.onStyle ).siblings().removeClass( opts.onStyle );
                switch( opts.tabStyle ){
                    case "move":
                        moveObj[opts.direction] = - step * index + "px";
                        $container.stop(true, false).animate( moveObj, opts.aniSpeed, opts.aniMethod );
                        break;
                    default:
                        $container.eq(index).css( "display", "block")
                            .siblings(opts.siblings).css( "display","none" );
                }

                opts.moveNum = index;
            });

            $menus.eq(0)[ opts.tabEvent ]();
            if( !opts.autoScroll ) return;
            autoScroll();
            $container.hover(
                function(){
                    clearTimeout(opts.setTimeObj);
                },
                function(){
                    autoScroll();
                }
            );
            $menus.hover(
                function(){
                    clearTimeout(opts.setTimeObj);
                },
                function(){
                    autoScroll();
                }
            );
            function autoScroll(){
                opts.setTimeObj = setTimeout(function(){
                    var num = ($menus.length - 1 > opts.moveNum) ? opts.moveNum + 1 : opts.moveNum = 0;
                    $menus.eq(num)[ opts.tabEvent ]();
                    autoScroll();
                }, opts.autoSpeed);
            }
        });

        function bindAutoWidth(){
            $(window).resize(function(){
                step = $container.parent().outerWidth(true);
                $container.css({ 'width' : step * $container.children().length, 'left' : 0 }).children().css({ 'width' : step });
                $menus.removeClass(opts.onStyle).eq(0).addClass(opts.onStyle);
                opts.moveNum = 0;
            });
        }
    };

}(jQuery));