(function (window, document, $, goodStarConfig) {
    var goodStar = goodStar || {};

    goodStar.initialize = function () {
        goodStar.star = $(goodStarConfig.selector);
        goodStar.star.each(function(index, el) {
            var $El = $(el);
            $El.barrating({
                theme: goodStarConfig.theme,
                initialRating: $El.attr('data-current-rating'),
                allowEmpty: false,
                readonly: $El.attr('data-readonly'),

                onSelect: function (value, text,event) {
                    var data = $(event.currentTarget).parents('.br-wrapper').find(goodStarConfig.selector);
                    /*console.log(data)*/
                    
                    var thread = parseInt(data.data('thread'));
                    var vote = parseInt(value);
                    var group = data.data('group') + '';
                    
                    var newgroup = group.replace(/[^a-z0-9]+/g, '');
                    
                    if (newgroup != '' && !isNaN(thread) && !isNaN(vote) && thread > 1 && vote > 0) {
                        
                        $.ajax({
                            type: 'POST',
                            url: goodStarConfig.connectorUrl,
                            data:{
                                thread: thread,
                                group: newgroup,
                                vote: vote
                            },
                            success: function(response){
                                var newData = eval(response);
                                $('.page-rating-select').each(function() {
                                    $(this).attr('data-current-rating', newData[0]);
                                });
                                $('.current-rating').each(function() {
                                    $(this).text(newData[0]);
                                });
                                $('.current-voite').each(function() {
                                    $(this).text(newData[1]);
                                });
                                $('.user-current-rating').each(function() {
                                    $(this).text(value);
                                    $(this).closest('.text-red').css('display', 'inline');
                                });
                                $El.barrating('readonly', true);
                            }
                        });
                    }
                }
            });
        });

    };


    $(document).ready(function () {
        goodStar.initialize();
    });

    $(document).ajaxStop(function(){
        goodStar.initialize();
    });

    window.goodStar = goodStar;

})(window, document, jQuery, goodStarConfig);
