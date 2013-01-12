$(document).ready(function(){
    $('section.questions a.arrow').live('click', function(event) {
        var href = $(this).attr('href');
        var $parent = $(this).parent();

        $.get(href, function(data) {
            $('section.questions article.card').hide('drop', { direction: 'down' }, 'slow', function() {
                $parent.replaceWith(data);
            });
            
            var qId         = $(data).find('article.question').attr('id');
            var answerRegex = new RegExp('Q\\d+', '');
            
            $('section.answers article.card a.answerlink').each(function() {
                var oldAnswerCardHref = $(this).attr('href');
                var newAnswerCardHref = oldAnswerCardHref.replace(answerRegex, qId);
                
                $(this).attr('href', newAnswerCardHref);
            });
            
            var $answerArrow       = $('section.answers a.arrow');
            var oldAnswerArrowHref = $answerArrow.attr('href');
            var newAnswerArrowHref = oldAnswerArrowHref.replace(answerRegex, qId);
            $answerArrow.attr('href', newAnswerArrowHref);
        });

        event.preventDefault();
    });

    $('section.answers a.arrow').live('click', function(event) {
        var href = $(this).attr('href');
        var $parent = $(this).parent();

        $.get(href, function(data) {
            $('section.answers article.card').hide('drop', { direction: 'down' }, 'slow', function() {
                $parent.replaceWith(data);
            });                    
        });

        event.preventDefault();
    });
    
    $('section.answers article.vote').each(function() {
        var aId = $(this).attr('id');
        var selector = 'form[name="' + aId + '"]';
        
        $(this).live('click', function(){
            $('section.answers ' + selector).submit();
        });
    });
});