const date = new Date().toLocaleString("en-US", {timeZone: "Europe/Paris"});
const unixDate = parseInt(new Date(date).getTime() / 1000);

function escapeHtml(text) {
    return text.replace('<', '&lt;').replace('>', '&gt;');
}

function decodeHTML(html) {
    var txt = document.createElement('textarea');
    txt.innerHTML = html;
    return txt.value;
}

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#img_preview')
                .attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function sendPost() {  
    NodeList.prototype.forEach = Array.prototype.forEach;
    var els = document.querySelectorAll("div#post_container > span.text_post");
    var i = 0;
    $('br').remove();
    var content = "";
    els.forEach(function(el) {
        if (el.innerHTML == "<br>" || el.innerHTML == "") {
            el.remove();
        } else {
            el.innerText = el.innerText.trim();
            el.innerHTML = escapeHtml(el.innerHTML);
            var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
            el.innerHTML = el.innerHTML.replace(exp, '<a style="color:#82b8d6;text-decoration:underline;" target="_blank" href=\"$1\">$1</a>');
            i++;
            content += '<div>'+el.innerHTML+'</div>';
        }
    });
    $("#post_content").val(content);
}

var loadPosts = false;

function fetchPosts() {
    if (loadPosts == false) {
        loadPosts = true;
        $('#more_post').html('<img src="/images/feed/ajax-loader.gif">');
        $.ajax({
            url : "/ajax/feed/posts",
            type : 'POST',
            data : {
                'date' :  encodeURIComponent(unixDate),
                'offset' :  encodeURIComponent($('#more_post').data('offset'))
            },
            dataType : 'html',              
        }).done(function(html) {
            $('#posts').append(html); 
            if (html == "") {
                $('#more_post').remove(); 
            } else {
                $('#more_post').html('Afficher plus de publications');
                commentsBox = document.getElementsByClassName('comment-box');
                for (var i = 0; i < commentsBox.length; i++) {
                    commentsBox[i].addEventListener('keypress', sendComment, false);
                    commentsBox[i].addEventListener('paste', handlePasteCom, false);
                }
                $('#more_post').data('offset', $('#more_post').data('offset') + 5);
                loadPosts = false;
            }
        });
    }
}

function deletePost(id) {
    $.ajax({
        url : "/ajax/deletepost",
        type : 'POST',
        data : {
            'id' :  encodeURIComponent($(id).data('id'))
        },
        dataType : 'html',              
    }).done(function(html) {
        if (html == 1) {
            $('#post_'+$(id).data('id')).remove();
        }
    }); 
    alert('La publication a bien été supprimée.');
}

function deleteComment(id) {
    $.ajax({
        url : "/ajax/deletecomment",
        type : 'POST',
        data : {
            'id' :  encodeURIComponent($(id).data('id')),
            'post' :  encodeURIComponent($(id).data('post'))
        },
        dataType : 'html',              
    }).done(function(html) {
        if (html == 1) {
            $('#comment_'+$(id).data('id')).remove();
        }
    }); 
    alert('Le commentaire a bien été supprimé.');
}

var loadComs = false;

function fetchComments(id) {
    if (loadComs == false) {
        loadComs = true;
        $.ajax({
            url : "/ajax/fetchcomments",
            type : 'POST',
            data : {
                'offset' : encodeURIComponent($(id).data('offset')),
                'idPost' : encodeURIComponent($(id).data('id')),
                'date' : encodeURIComponent(unixDate)
            },
            dataType : 'html',              
        }).done(function(html) {
            if (html == "") {
                $(id).remove();
            } else {
                $('#comments_'+$(id).data('id')).append(html);
                $(id).data('offset', $(id).data('offset') + 5);
                loadComs = false;
            }
        });
    }
}

function sendLike(id) {
    $.ajax({
      url : "/ajax/sendlike",
      type : 'POST',
      data : {
          'idPost' : encodeURIComponent($(id).data('id'))
      },
      dataType : 'html',              
    }).done(function(html) {
      if (html == 0) {
        $(id).html('<img src="/images/feed/like2.png" width="22" /><div class="pl-2 font-weight-bold">Aimer</div>')
      } else {
        $(id).html('<img src="/images/feed/break.png" width="22" /><div class="pl-2 font-weight-bold">Ne plus aimer</div>')
      }
    });
}

var sendCom = false;

function sendComment(e) {
    if (e.keyCode == 13 && sendCom == false) {
        if (e.target.innerText.trim().length == "") {
          e.preventDefault();
        } else {
          sendCom = true;
          $('br').remove();
          e.target.innerText = e.target.innerText.trim();
          var com = escapeHtml(e.target.innerHTML);
          var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
          var exp2 = /@([-A-Z0-9+&#\/%?=~_|!:,.;]*)/ig;
          com = com.replace(exp, '<a style="color:#82b8d6;text-decoration:underline;" target="_blank" href=\"$1\">$1</a>').replace(exp2, '<a class="font-weight-bold" style="color:#82b8d6;" href="/profile/$1">@$1</a>');
          e.preventDefault();
          $.ajax({
            url : "/ajax/sendcomment",
            type : 'POST',
            data : {
                'content' : encodeURIComponent(com),
                'idPost' : encodeURIComponent(e.target.getAttribute('data-id'))
            },
            dataType : 'html',              
          }).done(function(html) {
            $('#comments_'+e.target.getAttribute('data-id')).append(html);
            e.target.innerHTML = "";
            sendCom = false;
          });
        }
    } else if (e.keyCode == 13 && sendCom != false) {
        e.preventDefault();
    }
}

function sharePost(id) {
    $.ajax({
      url : "/ajax/sharepost",
      type : 'POST',
      data : {
          'idPost' : encodeURIComponent($(id).data('id'))
      },
      dataType : 'html',              
    }).done(function(html) {
    });
    alert('La publication a été partagée sur votre profil.');
}

function subscribe(id) {
    $.ajax({
      url : "/ajax/subscribe",
      type : 'POST',
      data : {
          'id' : encodeURIComponent($(id).data('id'))
      },
      dataType : 'html',              
    }).done(function(html) {
        location.reload();
    });
}

function participate(id) {
    $.ajax({
      url : "/ajax/participate",
      type : 'POST',
      data : {
          'id' : encodeURIComponent($(id).data('id'))
      },
      dataType : 'html',              
    }).done(function(html) {
        location.reload();
    });
}

function reqPrvResponse(id) {
    $.ajax({
      url : "/ajax/reqprvresponse",
      type : 'POST',
      data : {
          'id' : encodeURIComponent($(id).data('id')),
          'response' : encodeURIComponent($(id).data('response'))
      },
      dataType : 'html',              
    }).done(function(html) {
    });
}

function commentClick(id) {
    $('#comment-box_'+$(id).data('id')).focus();
}

var loadDisc = false;

function fetchDiscussions() {
    if (loadDisc == false) {
        loadDisc = true;
        $('#more_disc').html('<img src="/images/feed/ajax-loader.gif" />');
        $.ajax({
            url : "/ajax/fetchdiscussions",
            type : 'POST',
            data : {
                'offset' : encodeURIComponent($('#more_disc').data('offset')),
                'date' : encodeURIComponent(unixDate)
            },
            dataType : 'html',              
        }).done(function(html) {
            if (html == "") {
                $('#more_disc').remove();
            } else {
                $('#discussions').append(html);
                $('#more_disc').html('Afficher plus de résultats');
                $('#more_disc').data('offset', $('#more_disc').data('offset') + 10);
                loadDisc = false;
            }
        });
    }
}


var loadEvents = false;

function fetchEvents() {
    if (loadEvents == false) {
        loadEvents = true;
        $('#more_event').html('<img src="/images/feed/ajax-loader.gif" />');
        $.ajax({
            url : "/ajax/fetchevents",
            type : 'POST',
            data : {
                'offset' :  encodeURIComponent($('#more_event').data('offset')),
                'date' : encodeURIComponent(unixDate)
            },
            dataType : 'html',              
        }).done(function(html) {
            if (html == "") {
                $('#more_event').remove(); 
            } else {
                $('#events').append(html); 
                $('#more_event').html("Afficher plus d'événements ");
                $('#more_event').data('offset', $('#more_event').data('offset') + 5);
                loadEvents = false;
            }
        });
    }
}

function handlePaste (e) {
    var clipboardData, pastedData;	
    var i = 0;
	e.stopPropagation();
    e.preventDefault();	
    clipboardData = e.clipboardData || window.clipboardData;
    pastedData = clipboardData.getData('Text');
    if (pastedData == "") {
        return false;
    }
    var select = document.querySelector('div#post_container > span.text_post:last-child');
    if (select.innerHTML == "<br>") {
        select.innerHTML = pastedData; 
        select.innerHTML = select.innerText;
    } else {
        select.innerHTML += pastedData; 
        select.innerHTML = select.innerText;
    } 
    if (document.getElementById("post_container").innerHTML != "") {
        $('#post_contenteditable_placeholder').hide();
    }   
}

function handlePasteCom (e) {
    var clipboardData, pastedData;	
    var i = 0;
	  e.stopPropagation();
    e.preventDefault();	
    clipboardData = e.clipboardData || window.clipboardData;
    pastedData = clipboardData.getData('Text');
    if (pastedData == "") {
        return false;
    }
    e.target.innerHTML = pastedData;
}