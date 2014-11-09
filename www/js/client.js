   ///CLIENT
function Searchtel(value)
{
    if($('#id_client').attr("value")==""){
        $.ajax({
            url:"/caisse/client/telclient/tel/"+value,
            success:function(text){
                if(text!="no"){
                    $.ajax({url:"/caisse/client/index/id_client/"+text,
                        success:function(text2){
                            cmd=$("input[name=type_cmd]:checked").val();
                            $("#form_client").html($(text2).html());
                            complete_form_client();
                            $("input[name=type_cmd][value="+cmd+"]").attr("checked","checked");
                            $("#societe").focus();
                        }
                    });
                }
                else{
                    $("#societe").focus();
                }
            }
        });
    }
}

function Searchrue(t,ev)
{

    var key = ev.keyCode || ev.charCode;
    
    switch(key){
    /*case 34:

        if($('tr.hightlight').text()==""){

            $("#liste_rue tr:first").attr("class","hightlight");
        }
        else{
            if($("#liste_rue tr.hightlight+tr").text()!=""){
                $("#liste_rue tr.hightlight+tr").attr("class","hightlight")

                $("#liste_rue tr.hightlight:first").attr("class","");
            }
        }
    break;
  case 40:
    break;
  case 38:
    break;
    case 33:
        if($('tr.hightlight').text()==""){

            $("#liste_rue tr:last").attr("class","hightlight");
        }
        else{
            if($("#liste_rue tr.hightlight").prev().text()!=""){
                $("#liste_rue tr.hightlight").prev().attr("class","hightlight")

                $("#liste_rue tr.hightlight:last").attr("class","");
            }
        }
    break;*/
    case 13:

           // if($('#liste_rue tr.hightlight').text()==""){
                suivant(t,ev);
            /*}
            else{
                rue=$('#liste_rue tr.hightlight td').html();
                $("#adresse_client").val(rue);
                $("#cp").focus();
            }
            $("#liste_rue").css("display","none");*/
    break;
    default:
       // alert(key);
       /* $.ajax({
            url:"/caisse/client/searchrue/type_rue/"+$("#type_rue").val()+"/rue/"+$(t).val(),
            success:function(text){
                        $("#liste_rue").html(text);
                        //$("#liste_rue").css("display","block");
                    }
            });*/
        }
}


function Searchrue_new()
{

 $.ajax({
            url:"/caisse/client/searchrue/type_rue/"+$("#type_rue").val(),
            success:function(text){
                        $("#liste_rue").html(text);
                        //$("#liste_rue").css("display","block");
                    }
            });

}

function suivant_ville(t,ev)
{
    var key = ev.keyCode || ev.charCode;
    parent=$("#nom_ville+p");
    switch(key){
        case 38://haut
           /* if($("li.highlight",parent).html()!=null){
                $("li.highlight",parent).prev().addClass("highlight");
                $("li.highlight:last",parent).removeClass("highlight");
                pos=$(".highlight").index("li");
                $(parent).scrollTop(pos*15);
            }
            else{*/
                suivant(t,ev);
            //}
        break;
   /* case 40://bas
        //alert($("#nom_ville+p li.highlight").html());
        if($("li.highlight",parent).html()==null)
            $("li:first",parent).addClass("highlight");
        else{
            $("li.highlight+li",parent).addClass("highlight");
            $("li.highlight:first",parent).removeClass("highlight");
        }
        pos=$(".highlight").index("li");
        $(parent).scrollTop(pos*15);
        break;*/
    case 13:// entrer
       /* if($("li.highlight",parent).html()!=null){
            $("#nom_ville").val($("#nom_ville+p li.highlight").html());
            $(parent).css("display","none");
        }*/
        suivant(t,ev);
        break;

/*
    default:

        if(key>=65 && key<=90){
            var l=new Array;
            val=$("#nom_ville").val();
            val=val.toLowerCase();

            if($('li[title^='+val+']',parent).length>0){
                $("li.highlight",parent).removeClass("highlight");
                if(!$('li[title^='+val+']',parent).hasClass('highlight'))
                    $('li[title^='+val+']:first',parent).addClass("highlight");

                else{
                    if(!$('li[title^='+val+']:last',parent).hasClass('highlight')){
                        $('li[title^='+val+'].highlight',parent).next().addClass("highlight");
                        $('li[title^='+val+']:first',parent).removeClass("highlight");
                    }
                    else{
                        $('li[title^='+val+'].first',parent).addClass("highlight");
                        $('li[title^='+val+']:last',parent).removeClass("highlight");
                    }
                }
                pos=$(".highlight").index("li");

                $(parent).scrollTop(pos*15);
            }

        }
        break;*/
    }
}

function search_ville(value)
{

        if(value!="" ){

            $.ajax({
                url:"/caisse/client/searchville/cp/"+value,
                success:function(text){
                    if(text!=""){
                        $("#nom_ville+p").html(text);
                       // $("#nom_ville+p").css("display","block");
                    }
                }
            });
    }
}
function complete_form_client(){
    $.ajax({
                url:"/caisse/client/totalcmd/id_client/"+$("#id_client").val(),
                success:function(html){

                  if($("#totalclient").html()===null)
                    $("input#tel_client").after(html);

                }
            });
        //	$("label[for=no_addr]+div+span+span").after('<span id="liste_rue" ></span>');
        if($("#total_cmd").html()===null)
            $("#form_client textarea#rmq").after('<div id="total_cmd"><label>Total</label><span id="total_cmd">0</span>€</div>');

}
function searchname(ev,name){
    var key = ev.keyCode || ev.charCode;
    switch(key){
    case 13:
        if($("#div_searchclient tr.hightlight").attr("id")!=undefined){
            $.ajax({url:"/caisse/client/index/id_client/"+$("#div_searchclient tr.hightlight").attr("id"),
                success:function(text2){
                    cmd=$("input[name=type_cmd]:checked").val();
                    $("#form_client").html($(text2).html());
                    complete_form_client();
                    $("input[name=type_cmd][value="+cmd+"]").attr("checked","checked");

                    $("#societe").focus();
                }
            });
        }
        $("#div_searchclient").css("display","none");

        $("#tel_client").focus();
    break;

    case 34:

        if($('tr.hightlight').text()==""){

            $("#div_searchclient tr:first+tr").attr("class","hightlight");
        }
        else{

            if($("#div_searchclient tr.hightlight+tr").text()!=""){
                $("#div_searchclient tr.hightlight+tr").attr("class","hightlight")

                $("#div_searchclient tr.hightlight:first").attr("class","");
            }
        }
    break;
    case 33:
        if($('tr.hightlight').text()==""){

            $("#div_searchclient tr:last").attr("class","hightlight");
        }
        else{
            if($("#div_searchclient tr.hightlight").prev().text()!=""){
                $("#div_searchclient tr.hightlight").prev().attr("class","hightlight")

                $("#div_searchclient tr.hightlight:last").attr("class","");
            }
        }
    break;
    case 27://ECHAP

        $("#div_searchclient").css("display","none");
        $("body").attr("onKeyup","cmd_Event(event)");
        $("#tel_client").focus();


        break;
    default:

        post="";
        if($("#name").val()!=undefined)
                post="name="+$("#name").val();

            $.ajax({
                    type:"POST",
                    data:post,
                    url:"/caisse/client/searchname",
                    success:function(text){
                        $("div.div_over").attr("id","div_searchclient");
                        $("#div_searchclient").html(text);
                        $("#div_searchclient").css("display","block");
                        $("#name").focus();
                        val=$("#name").val();
                        if ($("#name").get(0).setSelectionRange) {
                             $("#name").get(0).setSelectionRange(val.length, val.length);
                        }
                        else if ($("#name").get(0).createTextRange) {
                            var range = $("#name").get(0).createTextRange();
                            range.collapse(true);
                            range.moveEnd('character', val.length);
                            range.moveStart('character', val.length);
                            range.select();
                        }

                    }
            });


        }
}
function valid_client(ev)
{
    var key = ev.keyCode || ev.charCode;

    if(key==13){

        start_cmd(ev);
        result=submit_form("#form_client");



    }
    else if(key==38){
        $("#digi").focus();
    }

}

