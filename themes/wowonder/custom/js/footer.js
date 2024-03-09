/* 

The code entered here will be added in <footer> tag 

*/

// Modified By A.Y
// Used to retweet | reneo post
function AY_RetweetPostOn(post_id, user_id){

    if (!post_id || !user_id) {
        return false;
    }
    $.ajax({
        url: Wo_Ajax_Requests_File(),
        type: 'GET',
        dataType: 'json',
        data: {f: 'posts',s:'retweet-post',post_id:post_id,user_id:user_id},
    })
    .done(function(data) {
    if (data.status == 200) {
        $("#post-retweet").modal('show');
        setTimeout(function(){
            $("#post-retweet").modal('hide');
            $('#post-' + post_id).find('.post-share').slideUp('fast')
        },3000);
    }
    })
    .fail(function() {
    console.log("error");
    })
  
}
// Created By A.Y
// This Function is used to copy data to the clupboard
function copy(string) 
{
    if(navigator.clipboard && window.isSecureContext)
    {
        return navigator.clipboard.writeText(string);
    }else{
        let textArea = document.createElement("textarea");
        textArea.value = string;
        textArea.style.position = "fixed";
        textArea.style.left ="-999999px";
        textArea.style.top ="-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        return new Promise((res, rej) => {
            document.execCommand('copy') ? res() : rej();
            textArea.remove();
        });
    }
}
// Created By A.Y
// Used to copy post link
function customCopyPost(val) 
{
    copy(val);
    $("#copy_model").modal('show');
    setTimeout(function(){
        $("#copy_model").modal('hide');
    },3000);
}
// Created By A.Y
// Used to share post to outer sites
function customOuterSharePost(post_id, owner_id) 
{
    if (!post_id) {
        return false;
    }
    $('#custom_share_post_modal').modal('hide');
    $('#custom_share_post_modal').remove();
    $.ajax({
        url: Wo_Ajax_Requests_File(),
        type: 'GET',
        dataType: 'json',
        data: {f: 'get_custom_share_post',post_id:post_id},
    })
    .done(function(data) {
        if (data.status == 200) {
            $('body').append(data.html);
            $('#SearchForInputPostId').val(post_id);
            $('#SearchForInputTypeId').val(owner_id);
            $('#custom_share_post_modal').modal('show');
        }
    })
    .fail(function() {})
    .always(function() {})
}

// Used to retweet post
function customQuotePost(post_id, owner_id) 
{
    if (!post_id) {
        return false;
    }
    $('#custom_quote_post_modal').modal('hide');
    $('#custom_quote_post_modal').remove();
    $.ajax({
        url: Wo_Ajax_Requests_File(),
        type: 'GET',
        dataType: 'json',
        data: {f: 'get_custom_quote_post',post_id:post_id},
    })
    .done(function(data) {
        if (data.status == 200) {
            $('body').append(data.html);
            $('#SearchForInputPostId').val(post_id);
            $('#SearchForInputTypeId').val(owner_id);
            $('#custom_quote_post_modal').modal('show');
        }
    })
    .fail(function() {})
    .always(function() {})
}
