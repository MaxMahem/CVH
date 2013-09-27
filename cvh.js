var CVH = {
    questionArrowClick : function(event) {
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
    },
    answerArrowClick : function(event) {
        var href    = $(this).attr('href');
        var $parent = $(this).parent();
        $parent.load(href);
        
        event.preventDefault();
    },
    voteClick : function(event) {
        $(this).siblings('form').submit();
        event.preventDefault();
    },
    dropReplace : function(data) {
        var dataClass = $('section', data).attr('class');
        console.log($('section', data));
        alert(dataClass);
        $(this).siblings('article.card').hide('drop', { direction: 'down' }, 'slow', function() {
                $(this).parent().replaceWith(data);
        });                    
    }
}

$(document).on('click', 'section.questions a.arrow', CVH.questionArrowClick);
$(document).on('click', 'section.answers a.arrow', CVH.answerArrowClick);
$(document).on('click', 'section.answers article.vote', CVH.voteClick);
