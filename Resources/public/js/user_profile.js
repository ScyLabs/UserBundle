(function(){
    $('.login_register_forms').on('click','.tabs li:not(.active)',function(){
        var tabs = $(this).parents('.tabs');
        var login_register_forms = $(this).parents('.login_register_forms');
 
        tabs.find('li').removeClass('active');
        $(this).addClass('active');
        login_register_forms.find('.tab').removeClass('active');
        login_register_forms.find('.tab[data-index='+$(this).data('index')+']').addClass('active');
    })
 })();