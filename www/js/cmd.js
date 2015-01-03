///COMMANDE


function cmd_Event(ev) {
  var key = ev.keyCode || ev.charCode;
  switch (key) {
    case 113://F2 nouvelle commande
      if (confirm("Confirmez vous annuler cette commande pour en débuter une autre ?")) {
        $.ajax({
          url: "/caisse/client",
          success: function(text) {
            $.ajax({
              url: "/caisse/panier/truncate"
            });
            $("#client").html(text);
            complete_form_client();
            $("input[name=type_cmd]").focus();
            $("#list_panier tr[id!=header]").remove();

          }
        });
      }
      break;
    case 0:
    case 222:
    case 114://F3 rechercher client remplacer par ²

      searchname(ev);

      break;
    case 115://F4 retour client
      $("#tel_client").focus();
      break;
    case 120://F9 double commande
      $('body').attr("onKeyup", "cmd_Event2(event)");
      break;
    case 46:// touche suppr
      var id = $('table#list_panier tr.trhighlight').attr("id");
      var id_panier = $('table#list_panier tr.trhighlight').attr("id_panier");

      if (id_panier != undefined) {
        if (confirm("Confirmez vous la suppression de cette ligne ?")) {
          if ($('tr[id=' + id + '] #prix').is(":focus") == false) {
            $('tr[id=' + id + ']').remove();

            $.ajax({
              url: "/caisse/panier/delPanier/id_panier/" + id_panier,
              type: "GET"
            });
            new_id = parseInt(id) + 1;
            if ($('tr[id=' + new_id + ']').attr("id") != undefined) {
              $('tr[id=' + new_id + ']').attr("class", "trhighlight");
              $("#qte" + new_id + "").focus();
            }
            else if (id == 1) {
              add_line();
              $('tr[id=1]').attr("class", "trhighlight");
              $("select#qte1").focus();
            }
            else {
              new_id = $('table#list_cmd tr:last').attr("id");
              $('tr[id=' + new_id + ']').attr("class", "trhighlight");
              $("#qte" + new_id + "").focus();
            }
          }
        }
      }
      else {

        $.ajax({
          url: "/caisse/panier/delPanier/id_panier/0",
          type: "GET"
        });
      }
      break;
      /*default:
       alert(ev.keyCode);*/
  }
}

function cmd_Event2(ev) {
  var key = ev.keyCode || ev.charCode;
  switch (key) {
    case 119://F8  enregistrer COMMANDE
      if (confirm("Confirmez vous l'enregistrement de la commande ?")) {
        save_cmd(0);
        $("body").attr("onKeyup", "listcmd_Event(event)");

      }
      else {
        $("body").attr("onKeyup", "cmd_Event(event)");
      }
      break;
    case 120://F9 enregistrer/imprimer commande
      if (confirm("Confirmez vous l'enregistrement et l'impression de la commande ?")) {
        save_cmd(1);
        $("body").attr("onKeyup", "listcmd_Event(event)");
      }
      else {
        $("body").attr("onKeyup", "cmd_Event(event)");
      }
      break;
    case 27://ECHAP
      if (confirm("Confirmez vous ne pas enregistrer la commande ?")) {
        $("#list_cmd").css("display", "block");
        $("#exe_cmd").css("display", "none");
        $("body").attr("onKeyup", "listcmd_Event(event)");
      }
      else {
        $("body").attr("onKeyup", "cmd_Event(event)");
      }
      break;

    default:
      $("body").attr("onKeyup", "cmd_Event(event)");
  }

}

function add_line() {
  if ($('table#list_panier tbody tr').attr("id") == undefined) {
    no = 1;
  }
  else {
    no = parseInt($('table#list_panier tbody tr:last').attr("id")) + 1;
  }

  $.ajax({
    url: "/caisse/panier/addline/no/" + no,
    async: false,
    success: function(text) {

      $('table#list_panier tbody').append(text);
      tr = $('tbody tr:last');

      $('select#qte', tr).focus();
      $('table#list_panier tr[id!=' + no + ']').attr('class', 'null');

    }});

}

function print_cmd(id_cmd) {
  window.open('/caisse/index/print/id_cmd/' + id_cmd, '', 'location=no,width=10,height=10');
  /*$.ajax({
   type:'GET',
   url:"/caisse/index/print",
   data:'id_cmd='+id_cmd,
   async:false
   });
   */
}

function save_cmd(print) {

  //verification dernieree ligne
  $("#wait").css("display", "block");

  if ($('table#list_panier tr:last input#prix').val() != ""
          && $('table#list_panier tr:last').attr('id_panier') != "" && $('table#list_panier tr:last').attr('id_panier') != "undefined") {

    tr = $('table#list_panier tr:last');

    var data_form = $("#form_panier", tr).serialize();

    affich_total();

    $.ajax({
      type: 'POST',
      url: "/caisse/panier/insert",
      data: data_form,
      async: false,
      success: function(text) {
        if ($(tr).attr("id_panier") == undefined)
          $(tr).attr("id_panier", text);
      }
    });
  }
  //insert cmd
  total = getTotal();

  nb_panier = $('table#list_panier tr[id_panier]').length;

  if (!(total == 0 && nb_panier == 0)) {

    if ($('#id_cmd').val() == "" || $('#id_cmd').val() == undefined) {
      id_cmd = $.ajax({
        url: "/caisse/index/insertcmd/id_client/" + $('#id_client').val() + "/type_cmd/" + $("input[name=type_cmd]:checked").val(),
        type: 'get',
        async: false
      }).responseText;
    }
    else {
      id_cmd = $('#id_cmd').val();
      $.ajax({
        url: "/caisse/index/updatecmd/id_client/" + $('#id_client').val() + "/type_cmd/" + $("input[name=type_cmd]:checked").val(),
        type: 'get',
        async: false
      })
    }


    $.ajax({
      url: "/caisse/index/totalcmd/id_cmd/" + id_cmd,
      type: "get",
      success: function(result) {

        if (print == 1)
          print_cmd(id_cmd);
        $('#id_cmd').val("");
        $("#wait").css("display", "none");
        $("#exe_cmd").css("display", "none");
        retour_list_cmd();

      }
    });

  }
  else {
    $.ajax({
      url: "/caisse/index/totalcmd/id_cmd/" + id_cmd,
      type: "get",
      success: function(result) {

        if (print == 1)
          print_cmd(id_cmd);
        $('#id_cmd').val("");
        $("#wait").css("display", "none");
        $("#exe_cmd").css("display", "none");
        retour_list_cmd();
      }
    });
  }
}

function retour_list_cmd() {

  /*$.ajax({
   url:"/caisse/index/listcmd",
   type:"GET",
   success:function(page){
   $("#list_cmd").html(page);
   $("#exe_cmd").css("display","none");
   $("#list_cmd").css("display","block");
   $("body").attr("onKeyup","listcmd_Event(event)");
   //reinit();
   }
   });*/
  location.reload();
}

function suivant(t, ev) {
  tmp = $(t).attr("tabindex");
  tabindex = parseInt(tmp);
  var key = ev.keyCode || ev.charCode;

  if (key == 13) {
    if ($("*[tabindex=" + (tabindex + 1) + "]").length > 0 && $("*[tabindex=" + (tabindex + 1) + "]").get(0).tagName == "INPUT" && $("*[tabindex=" + (tabindex + 1) + "]").attr("type")) {
      f = function() {
        val = $("*[tabindex=" + (tabindex + 1) + "]").val();
        $("*[tabindex=" + (tabindex + 1) + "]").focus();
        $("*[tabindex=" + (tabindex + 1) + "]").val('');
        $("*[tabindex=" + (tabindex + 1) + "]").val(val);

      }
      setTimeout(f, 10);
    }
    else {
      setTimeout('$("*[tabindex=' + (tabindex + 1) + ']").focus()', 10);
    }
  }
  else if (key == 38) {

    if (t.tagName == "SELECT") {
      if ($('option:selected', t).prev('option').val() != undefined)// on verifie si on n'est pas sur le premier option
        $('option:selected', t).removeAttr('selected').next('option').attr('selected', 'selected');
    }

    if ($("*[tabindex=" + (tabindex - 1) + "]").val() == undefined) {
      tmp = tmp + "";
      line = parseInt(tmp.substr(0, 1));
      if ($("*[tabindex=" + (line - 1) + "01]").val() != undefined) {

        $("*[tabindex=" + (line - 1) + "01]").focus();
        $("*[tabindex=" + (line - 1) + "01]").parents("tr").addClass("trhighlight");
        $("tr.trhighlight:last").removeClass("trhighlight");
      }
      else {
        if ($("#form_client #id_client").val() != "")
          $("#form_client textarea").focus();
        else
          $("#form_client input[type=radio]:checked").focus();
      }
    }
    else {
      $("*[tabindex=" + (tabindex - 1) + "]").focus();
    }
  }

}

function cat_Suivant(t, ev) {
  var key = ev.keyCode || ev.charCode;

  if (key == 13) {

    var parent = $(t).parent();

    tabindex = parseInt($(t).attr("tabindex"));// on recupere le tabindex

    objet = $("*[tabindex=" + (tabindex + 1) + "]");// onstocke le select suivant

    $.ajax({
      url: "/caisse/plat/listplat/id_cat/" + $(t).val(),
      success: function(text) {
        val = $(objet).val();
        $(objet).html(text);
        $(objet).val(val);
        $(objet).focus();
        $.ajax({
          url: "/caisse/categorie/listtaille/id_cat/" + $(t).val(),
          success: function(list_taille) {
            parent = $(parent).parent().parent().parent();
            val = $("#taille", parent).val();
            $("#taille", parent).html(list_taille);
            $("#taille", parent).val(val);
          }
        });
      }
    });
  }
  else {
    suivant(t, ev);
  }
}

function verif_client(ev) {
  retour = true;

  if ($("form#form_client input[name=type_cmd]:checked").val() == 3 && $("form#form_client input#id_client").val() == "") {
    valid_client(ev)
    retour = false;
  }
  return retour;
}

function modif_plat(t, ev) {
  pizza = parseInt($('tr.trhighlight #cat option:selected').attr('is_pizza'));
  base = parseInt($('tr.trhighlight #cat option:selected').attr('is_base'));
  menu = parseInt($('tr.trhighlight #cat option:selected').attr('is_menu'));
  var key = ev.keyCode || ev.charCode;

  if (pizza == 1) {

    if (key == 111 || key == 191) { // touche "/"
      if ($("tr.trhighlight #plat_2").val() == undefined || $("tr.trhighlight #plat_2 option").length <= 1) {
        $.ajax({
          url: "/caisse/plat/listplat/id_cat/" + $('tr.trhighlight #cat option:selected').val(),
          success: function(text) {
            if ($("tr.trhighlight #plat_2").val() == undefined)
              $("tr.trhighlight #plat").after("<label for='plat_2' style='clear:none;width:5px;font-size:11pt'> / </label><select name='plat_2' id='plat_2' onkeyup=\"modif_plat($('tr.trhighlight #plat'),event);\">" + text + "</select>");
            $("tr.trhighlight #plat_2").focus();

          }
        });
      }
      $("tr.trhighlight #plat_2").focus();
    }
    suivant(t, ev);
  }
  if (base == 1) {
    modif_base(ev);
    suivant(t, ev);
  }
  if (menu == 1) {
    modif_menu(t, ev);
  }

  if (pizza == 0 && base == 0 && menu == 0) {
    suivant(t, ev);
  }
}

function modif_base(ev) {
  var key = ev.keyCode || ev.charCode;

  if (key == 106 || key == 56) {// *
    if ($('tr.trhighlight #base').val() == undefined) {
      $.ajax({
        url: "/caisse/plat/listbase/",
        success: function(text) {
          if ($('tr.trhighlight #plat_2').val() == undefined)
            $('tr.trhighlight #plat').after("<label for='base' style='margin-left:180px;width:40px;'>Base : </label><select name='base' id='base' onkeyup=\"modif_plat($('tr.trhighlight #plat'),event);\">" + text + "</select>");
          else
            $('tr.trhighlight #plat_2').after("<label for='base' style='margin-left:180px;width:40px;'>Base : </label><select name='base' id='base' onkeyup=\"modif_plat($('tr.trhighlight #plat'),event);\">" + text + "</select>");
          $(" tr.trhighlight #base").focus();
        }
      });
    }
    $(" tr.trhighlight #base").focus();
  }
}

function modif_menu(t, ev) {
  var key = ev.keyCode || ev.charCode;

  if (key == 13) {
    $.ajax({
      url: "/caisse/menu/necessary/plat/" + $("tr.trhighlight #plat").val(),
      success: function(ok) {
        if (key == 106 || (ok >= 1 && key == 13)) {
          $('.trhighlight select#plat').attr("onKeyup", "");
          $('body').attr("onKeyup", "event_menu(event)");
          id_panier = "";

          if ($('tr.trhighlight #id_panier').val() != "") {
            id_panier = "/id_panier/" + $('.trhighlight #id_panier').val();
          }
          $.ajax({
            url: "/caisse/menu/index/plat/" + $(".trhighlight #plat").val() + id_panier,
            success: function(html) {
              $('#over1.div_over').attr("id", "div_menu");
              $('#div_menu').html(html);

              $('#div_menu').css("display", "block");
              $('#div_menu  select:first').focus();
              setTimeout('$("select:focus").parent("dl").attr("class","highlight")', 10);
            }
          });
        }
        else {
          suivant(t, ev);
        }
      }
    });
  }
}

function event_menu(ev) {
  var key = ev.keyCode || ev.charCode;

  if (key == 13) {

    if ($("#submit_menu").is(":focus")) {
      valid_menu();
    }
    else {
      $("dl").removeClass("highlight");
      setTimeout('$("select:focus").parent("dl").attr("class","highlight")', 10);
    }

  }
  else if (key == 38) {//haut

    $("dl").removeClass("highlight");
    setTimeout('$("select:focus").parent("dl").attr("class","highlight")', 10);

  }

  if (key == 111 || key == 59) {//   /

    if ($('#div_menu select:focus').attr("is_pizza") == 1) {

      name = $('#div_menu  dl.highlight select:first').attr("name");
      id = name.substring(8);

      if ($(".highlight #plat_2_" + id).val() == undefined) {
        $.ajax({
          url: "/caisse/plat/listplat/id_cat/" + $('#div_menu select:focus').attr("id_cat"),
          success: function(text) {
            $('#div_menu dl.highlight select:first').after("<label for='plat_2_" + id + "' style='min-width:10px;width:10px;clear:none'> / </label><select name='plat_2_" + id + "' id='plat_2_" + id + "' onkeyup='suivant($(\"#div_menu dd.highlight select:first\"),event)'>" + text + "</select>");
            $("#div_menu dl.highlight select[id^=plat_2_]").focus();
          }
        });
      }
      $("#div_menu dl.highlight select[id^=plat_2_]").focus();
    }
  }
  if (key == 106 || key == 56) {// *

    if ($('#div_menu dl.highlight select:first').attr("is_base") == 1) {

      name = $('#div_menu  dl.highlight select:first').attr("name");
      id = name.substring(8);

      if ($("#div_menu #base_" + id).val() == undefined) {
        $.ajax({
          url: "/caisse/plat/listbase/",
          success: function(text) {
            $('dl.highlight').append(" <label for='base_" + id + "' style='margin-left:200px;'>Base : </label><select name='base_" + id + "' id='base" + id + "' onkeyup='suivant($(\"#div_menu div.highlight select:first\"),event)'>" + text + "</select>");
            $("#div_menu .highlight select:last").focus();
          }
        });
        $("#div_menu .highlight select:last").focus();
      }
    }
  }
  if (key == 107 || key == 61) {  // +
    console.log($('#div_menu  dl.highlight select:first').attr("is_compo"));
    if ($('#div_menu  dl.highlight select:first').attr("is_compo") == 1) {
      name = $('#div_menu  dl.highlight select:first').attr("name");
      id = name.substring(8);
      plat = $('#div_menu select#id_plat_' + id).val();
      $('#div_menu select[id=' + id + ']').blur();

      p = $("#ingt_plus_" + id).val();
      m = $("#ingt_moins_" + id).val();
      //text= ajax("include/ajax.php?action=div_ingt&plat="+plat+"&p="+p+"&m="+m);
      $.ajax({
        url: "/caisse/plat/listingt/plat/" + plat + "/p/" + p + "/m/" + m,
        success: function(div_ingt) {
          $("#over2.div_over").attr("id", "div_ingt");
          $('#div_ingt').html(div_ingt);
          $('#div_ingt').css("display", "block");
          $('#div_ingt label:first').attr("class", "label_highlight");
          $('#div_menu select#id_plat_' + id).blur();
          setTimeout('$("body").attr("onKeyup","event_ingt_menu(event)");', 100);
          $('#div_menu').css("visibility", "hidden");
        }
      });
    }
  }
}

function valid_menu() {
  $("#submit_menu").blur();
  tr_id = $('.trhighlight').attr('id');
  submit_form("#form_menu");
  $('.trhighlight select#plat').attr("onKeyup", "modif_plat(this,event)");

  $('body').attr("onKeyup", "cmd_Event(event)");
  $('#div_menu').css("display", "none");
  $('#div_menu').attr("id", "over1");
  $('.trhighlight select#select_ingt').focus();
}

function add_supp(t, ev) {
  var key = ev.keyCode || ev.charCode;

  if (key == 13 || key == 38) {
    suivant(t, ev);
  }
  else if ((key >= 65 && key <= 90) || key == 107 || key == 61 || key == 187) {

    if ($('tr.trhighlight select#cat option:selected').attr("is_compo") == 1) {

      p = $("tr.trhighlight #list_plus").val();
      m = $("tr.trhighlight #list_moins").val();
      k = "";
      var l = new Array;
      l[65] = "a";
      l[66] = "b";
      l[67] = "c";
      l[68] = "d";
      l[69] = "e";
      l[70] = "f";
      l[71] = "g";
      l[72] = "h";
      l[73] = "i";
      l[74] = "j";
      l[75] = "k";
      l[76] = "l";
      l[77] = "m";
      l[78] = "n";
      l[79] = "o";
      l[80] = "p";
      l[81] = "q";
      l[82] = "r";
      l[83] = "s";
      l[84] = "t";
      l[85] = "u";
      l[86] = "v";
      l[87] = "w";
      l[88] = "x";
      l[89] = "y";
      l[90] = "z";

      if (key >= 65 && key <= 90)
        k = "/k/" + l[key];
      if ($('#div_ingt').css("display") != "block") {
        $.ajax({
          url: "/caisse/plat/listingt/plat/" + $("tr.trhighlight #plat").val() + "/p/" + p + "/m/" + m + k,
          success: function(div_ingt) {
            $("#over2.div_over").attr("id", "div_ingt");
            $('#div_ingt').html(div_ingt);
            $('#div_ingt').css("display", "block");
            $('#div_ingt label:first').attr("class", "label_highlight");
            $('.trhighlight select#select_ingt').attr("onkeyup", "");
            $('.trhighlight select#select_ingt').blur();
            setTimeout('$("body").attr("onKeyup","event_ingt(event)");', 10);


          }
        });
      }
    }
  }
}

function valid_ingt() {
  var data_form = $("#form_ingt").serialize();
  // RÃ©cupÃ¨re l'url de l'action
  var url = $("#form_ingt").attr('action');
  $("#form_ingt").attr('onsubmit', 'return false')

  // Convertir le Submit du formulaire en Ajax
  $.ajax({
    type: "POST",
    url: url,
    data: data_form,
    success: function(text) {
      if ($('body').attr("onKeyup") == "event_ingt(event)") {
        tab = text.split("|");

        $(".trhighlight #list_plus").val(tab[0]);

        /*if($(".trhighlight #list_plus-label").attr("class")==undefined)
         $(".trhighlight #list_plus-element").before("<label id='list_plus-label' class='pre'>"+tab[2]+"</label>");
         else*/ $(".trhighlight #list_plus +p.pre").html(tab[2]);
        $(".trhighlight #list_plus +p.pre").css("visibility", "visible");
        $(".trhighlight #list_moins").val(tab[1]);
        /*if($(".trhighlight #list_moins-label").attr("class")==undefined)
         $(".trhighlight #list_moins-element").before("<label id='list_moins-label' class='pre'>"+tab[3]+"</label>");
         else*/ $(".trhighlight #list_moins+p.pre").html(tab[3]);
        $(".trhighlight #list_moins +p.pre").css("visibility", "visible");
        $('.trhighlight select#select_ingt').attr("onkeyup", "add_supp(this,event)");
        $('.trhighlight select#select_ingt').focus();
        $('body').attr("onKeyup", "cmd_Event(event)");
        $("#div_ingt").css("display", "none");
        $('#div_ingt').attr("id", "over2");
      }
      else if ($('body').attr("onKeyup") == "event_ingt_menu(event)") {

        name = $('#div_menu  dl.highlight select:first').attr("name");
        tdid = name.substring(8);
        tab = text.split("|");
        $("#div_menu  dl.highlight #ingt_plus_" + tdid).val(tab[0]);
        $("#div_menu  dl.highlight #ingt_moins_" + tdid).val(tab[1]);
      
        if (tab[2].length > 0) {
          if ($("#div_menu  #ingt_plus_" + tdid + " +p").length == 0)
            $("#div_menu  #ingt_plus_" + tdid).after("<p class='description'></p>");
        
          $("#div_menu   #ingt_plus_" + tdid + " +p").html(tab[2]);
          $("#div_menu   #ingt_plus_" + tdid + " +p").css("display", "block");
        }
        if (tab[3].length > 0) {
          if ($("#div_menu  dl #ingt_moins_" + tdid + " +p").length == 0){
            $("#div_menu  dl #ingt_moins_" + tdid).after("<p class='description'></p>");
            cons
          }
          $("#div_menu  dl #ingt_moins_" + tdid + " +p").html(tab[3]);
          $("#div_menu  dl #ingt_moins_" + tdid + " +p").css("display", "block");
        }
        $('body').attr("onKeyup", "event_menu(event)");
        $('#div_ingt').css("display", "none");
        $('#div_ingt').attr("id", "over2");
        $('#div_menu').css("visibility", "visible");

        $('#div_menu  dl.highlight select:first').focus();
      }
    },
    dataType: "html"
  });
}

function event_ingt(ev) {

  id = $('#div_ingt .label_highlight').attr("for");
  var key = ev.keyCode || ev.charCode;

  switch (key) {
    case 13:
      if (id != undefined) {
        $('#div_ingt label[for=' + id + ']').attr("class", '');
        $("#div_ingt input#submit").focus();
      }
      else {
        valid_ingt();
      }
      break;

    case 38://haut

      if (id != undefined) {
        if ($('#div_ingt .label_highlight').prev("label").prev("label").prev("label").prev("label").attr("for") != undefined) {
          $('#div_ingt .label_highlight').prev("label").prev("label").prev("label").prev("label").attr("class", "label_highlight");
          $('#div_ingt .label_highlight:last').attr("class", "");
        }
      }
      else {
        $('#div_ingt label:last').attr("class", 'label_highlight');
        $("#div_ingt #submit").blur();
      }
      break;

    case 39://droite
      if (id != undefined) {
        if ($('#div_ingt .label_highlight').next("label").attr("for") != undefined) {
          $('#div_ingt .label_highlight').next("label").attr("class", "label_highlight");
          $('#div_ingt .label_highlight:first').attr("class", "");
        }
        else {
          $('#div_ingt .label_highlight').attr("class", "");
          $("#div_ingt #submit").focus();
        }
      }
      break;

    case 40://bas
      if (id != undefined) {
        if ($('#div_ingt .label_highlight').next("label").next("label").next("label").next("label").attr("for") != undefined) {
          $('#div_ingt .label_highlight').next("label").next("label").next("label").next("label").attr("class", "label_highlight");
          $('#div_ingt .label_highlight:first').attr("class", "");
        }
        else {
          $('#div_ingt .label_highlight').attr("class", "");
          $("#div_ingt #submit").focus();
        }
      }
      break;
    case 37://gauche
      if (id != undefined) {
        if ($('#div_ingt .label_highlight').prev("label").attr("for") != undefined) {
          $('#div_ingt .label_highlight').prev("label").attr("class", "label_highlight");
          $('#div_ingt .label_highlight:last').attr("class", "");
        }
      }
      else {
        $('#div_ingt label:last').attr("class", 'label_highlight');
        $("#div_ingt #submit").blur();
      }
      break;

    case 107 :
    case 187 :
    case 61:/// +
      if ($('#div_ingt .label_highlight input:first').is(':checked'))
        $('#div_ingt .label_highlight input:last').attr("checked", "checked");
      else
        $('#div_ingt .label_highlight input:first').attr("checked", "checked");
      break;
    case 189:
    case 109: //// -

      if ($('#div_ingt .label_highlight input:last').is(':checked'))
        $('#div_ingt .label_highlight input:last').removeAttr("checked");
      else
        $('#div_ingt .label_highlight input:first').removeAttr("checked");
      break;
    default:

      if (key >= 65 && key <= 90) {
        var l = new Array;
        l[65] = "a";
        l[66] = "b";
        l[67] = "c";
        l[68] = "d";
        l[69] = "e";
        l[70] = "f";
        l[71] = "g";
        l[72] = "h";
        l[73] = "i";
        l[74] = "j";
        l[75] = "k";
        l[76] = "l";
        l[77] = "m";
        l[78] = "n";
        l[79] = "o";
        l[80] = "p";
        l[81] = "q";
        l[82] = "r";
        l[83] = "s";
        l[84] = "t";
        l[85] = "u";
        l[86] = "v";
        l[87] = "w";
        l[88] = "x";
        l[89] = "y";
        l[90] = "z";

        id = $('#div_ingt label[title^=' + l[key] + ']:first').attr('for');

        if (id == undefined) {
          l[key] = l[key].toUpperCase();
          id = $('#div_ingt label[title^=' + l[key] + ']:first').attr('for');
        }
        if (id != undefined) {
          $('#div_ingt .label_highlight').attr("class", "");
          $('#div_ingt label[for=' + id + ']').attr("class", "label_highlight");

          $("#submit").blur();
        }
      }
  }
}

function event_ingt_menu(ev) {
  event_ingt(ev);
}

function get_prix(t, ev) {
  var key = ev.keyCode || ev.charCode;

  if (key == 13) {

    //if(!($(".trhighlight #taille").val()=='' && $('.trhighlight select#cat option:selected').attr('is_taille')==1) || $('select#cat'+no+' option:selected').attr('is_taille')!=1){
    if ($(".trhighlight #taille").val() != '' || $('.trhighlight select#cat option:selected').attr('is_taille') == 0) {
      url = "/caisse/panier/getprix/type_cmd/";
      type_cmd = $("input[name=type_cmd]:checked").val();

      url += type_cmd + "/qte/" + $(".trhighlight #qte").val() + "/prod/" + $(".trhighlight #plat").val();

      if ($(".trhighlight #cat option:selected").attr("is_taille") == 1) {
        url += "/cat/" + $(".trhighlight #cat").val() + "/taille/" + $(".trhighlight #taille").val();
      }

      if ($(".trhighlight #cat option:selected").attr("is_compo") == 1) {
        url += "/ingt_p/" + $(".trhighlight #list_plus").val() + "/ingt_m/" + $(".trhighlight #list_moins").val();
      }
      $.ajax({
        type: 'GET',
        url: url,
        success: function(prix) {
          $(".trhighlight #prix").val(prix);
          suivant(t, ev);
        }
      });
    }
  }
  else if (key == 38) {
    suivant(t, ev);
  }

}

function affich_total() {
  total = getTotal();
  $('span[id=total_cmd]').html(total);
}

function getTotal() {
  total = 0;
  $('input#prix').each(function() {
    if (this.value != '') {
      total += parseFloat(this.value);
    }
  });
  return Math.round(total * 100) / 100;
}

function valid_panier(t, ev) {
  tr = $('table#list_panier tr.trhighlight');

  var key = ev.keyCode || ev.charCode;

  if (key == 13) {
    last_id = $('table#list_panier tr:last').attr("id");

    var data_form = $("#form_panier", tr).serialize();

    new_id = parseInt($(tr).attr("id")) + 1;

    affich_total();

    $.ajax({
      type: 'POST',
      url: "/caisse/panier/insert",
      data: data_form,
      async: false,
      success: function(text) {

        if ($("#id_panier", tr).val() == "") {
          $(tr).attr("id_panier", text);
          $("#id_panier", tr).val(text);
        }
        if (last_id == $(tr).attr("id")) {
          add_line();
        }
        else {
          $('table#list_panier tr.trhighlight').removeClass('trhighlight');
          $(tr).next("tr").attr('class', 'trhighlight');
          $("tr.trhighlight select#qte").focus();
        }
        $('div#cmd').scrollTop(1000000000000);
      }
    });
  }
  else if (key == 38) {
    suivant(t, ev);
  }
}

function start_cmd(ev) {
  var key = ev.keyCode || ev.charCode;

  if (key == 13) {
    if ($('table[id=list_panier] tr[id=1]').attr('id') == undefined)
      add_line();
    else {
      $('table[id=list_panier] tr[id=1]').addClass("trhighlight");
      $("#qte[tabindex=101]").focus();
    }
    $('div#cmd').scrollTop(0);
  }
}

function calcul_encaissement(id_liv) {
  total = 0;
  $("#form_encaissement_" + id_liv + " input[type=text]").each(function() {
    // console.log(this.name+" "+this.value);
    if (isNaN(parseFloat(this.value)) === false) {
      total += parseFloat(this.value);
    }
  });
  // console.log(total);
  $("#calcul_" + id_liv).html(total + " &euro;");
}

function encaissement(id_liv) {

  tr = $("#form_encaissement_" + id_liv + "");
  var data_form = $(tr).serialize();
  // Récupère l'url de l'action
  var url = $(tr).attr('action');

  var method = $(tr).attr('method');

  if (typeof(method) === "undefined")
    method = "POST";
  // Supprime tous les alertes liées à une validation de formulaire
  // Convertir le Submit du formulaire en Ajax
  return $.ajax({
    type: method,
    url: url,
    data: data_form,
    success: function(text) {
      // verif_form = text.substring(text.indexOf("|", 0) + 1);
      var result = $.parseJSON(text);
      //result = $.parseJSON(result);
      //data_post = text.substring(0, text.indexOf("|", 0));
      if (result === "true") {
        $("#recette_" + id_liv).toggle();
      } else {
        // Pour chaque champs en erreur

        $.each(result, function(champ, erreurs) {

          // Pour chacune des erreurs
          $.each(erreurs, function(type, msg) {
            console.log(type);
            $('#' + champ, tr).after('<div class="formValidation ' + type + '" style="display:block;top:-7px;">' + msg + '</div>');
            $('#' + champ + '+div.formValidation', tr).fadeOut(2200, "linear", function() {
            });
          });
        });
        $("input", $(".formValidation:first").parent()).focus();
        return false;
      }
    }, dataType: "json"
            //, dataType: "json"
  });

}