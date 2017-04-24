(function ($) {
    "use strict";
    var mainApp = {
        slide_fun: function () {
            $('#carousel-div').carousel({
                interval: 4000 //TIME IN MILLI SECONDS
            });
        },
        wow_fun: function () {
            new WOW().init();
        },
        gallery_fun: function () {
            /*====================================
    FOR IMAGE/GALLERY POPUP
    ======================================*/
            $("a.preview").prettyPhoto({
                social_tools: false
            });
            /*====================================
          FOR IMAGE/GALLERY FILTER
          ======================================*/

            // MixItUp plugin
            // http://mixitup.io

            $('#port-folio').mixitup({
                targetSelector: '.portfolio-item',
                filterSelector: '.filter',
                effects: ['fade'],
                easing: 'snap',
            });
        },
       
        custom_fun:function()
        {
            
            /*====================================
             WRITE YOUR   SCRIPTS  BELOW
            ======================================*/




        },
       

    }
   
   
    $(document).ready(function () {
        mainApp.slide_fun();
        mainApp.wow_fun();
        mainApp.gallery_fun();
        mainApp.custom_fun();
       
    });
}(jQuery));

//CLIENTS SECTION SCRIPTS
$(window).load(function () {
$('.flexslider').flexslider({
    animation: "slide",
    animationLoop: false,
    itemWidth: 200,
    itemMargin: 15,
    pausePlay: false,
    start: function (slider) {
        $('body').removeClass('loading');
    }
});
});
// 产品详情
$('.product-detail').on('click',function(e){
    var node = $(e.target);
    if(node[0].className == 'product-intro'){
        return false;
    }
    if(node[0].nodeName == 'SPAN'){
        node = node.parents('dt');
    }
    if(node[0].nodeName == 'A'){
        node = node.parent('dd');
    }
    if(node.find('span')[0]){
        var icon = node.find('span');
        if(icon.css('transform') == 'matrix(6.12323e-17, 1, -1, 6.12323e-17, 0, 0)'){
            icon.css('transform','rotate(0deg)');
        }else{
            icon.css('transform','rotate(90deg)');
        }
    }

    if(node[0].nodeName == 'DT'){
        node.siblings('dd').slideToggle(200);
    }
    if(node[0].nodeName == 'DD'){
        var url = node.find('a').attr('href');

        node.parents('.product-detail').find('dd').css('background-color','#fafafa');
        node.css('background-color','#eee');

        $.ajax({
            url:url,
            type:'get',
            dataType:'json',
            success:function(data){
                $('.product-title').empty().append(data.data.title);
                $('.product-content').empty().append(data.data.content);
            }
        });
    }
});
// 新闻评论功能
$('.commit-comment').on('click',function(){
    var comment = $('.news-comment textarea').val();
    var node_id = $('.news-detail').data('id');
    var user_id = $('.news-comment-user').data('id');
    console.log(comment);
    if(comment){
        $.ajax({
            url:'/index/comment',
            type:'post',
            data:{node_id:node_id,comment:comment,user_id:user_id},
            success:function(){
                $('.news-comment textarea').val('');
            }
        });
    }else{
        win.alert('请填写评论内容！','error')
    }
});
// 验证码
$('.btn-vercode').on('click',function(e){
    var mobile = $('.mobile').val();
    $.ajax({
        url:'/index/getVercode',
        type:'post',
        data:{mobile:mobile},
        success:function(data){
            console.log(data);
            if(data){
                var wait=60;
                var btn = $(e.target)
                function time(btn) {
                    if (wait == 0) {
                        btn.removeAttr("disabled");
                        btn.value="获取验证码";
                        wait = 60;
                    } else { 
                        btn.Attr("disabled", true);
                        btn.value="重新发送(" + wait + ")";
                        wait--;
                        setTimeout(function() {
                            time(o)
                        }, 1000)
                    }
                }
            }else{
                win.alert('获取验证码失败！','error');
            }
        }
    });
});