//fonction general
waypoint = new Array();

function affich_wait() {
  $('#wait').css('display', 'block');
}

function affich_help() {
  $('#help').slideToggle("normal");
}

function get_constant(val) {
  text = ajax("/include/ajax.php?action=constant&c=" + val);
  return text;
}


function promptHTML(string, f) {
  $('#confirm #confirm_title').html(string);
  var submit_f = function() {
    f($('input[name=mdp]').val());
    return false;
  };
  $('#confirm form#form_confirm').submit(submit_f);
  $('#confirm').css("display", "block");
  $('input[name=mdp]').focus();

}
function submit_form(t) {

  var data_form = $(t).serialize();
  // Récupère l'url de l'action
  var url = $(t).attr('action');

  var method = $(t).attr('method');

  if (typeof(method) === "undefined")
    method = "POST";
  // Supprime tous les alertes liées à une validation de formulaire
  $('.formValidation').remove();

  // Convertir le Submit du formulaire en Ajax
  return $.ajax({
    type: method,
    url: url,
    data: data_form,
    success: function(text) {
      verif_form = text.substring(text.indexOf("|", 0) + 1);
      var result = jQuery.parseJSON(verif_form);
      result = jQuery.parseJSON(result);
      data_post = text.substring(0, text.indexOf("|", 0));
      if (result === true) {
        $(t).before('<div class="formValidation success">Vos modifications ont bien été prises en compte</div>');
        setTimeout("$('div.formValidation.success').fadeOut()", 4000);
        $("#" + data_post.substring(0, data_post.indexOf("=", 0))).val(data_post.substring(data_post.indexOf("=") + 1));
        if (data_post.substring(0, data_post.indexOf("=", 0)) === "admin") {
          if (data_post.substring(data_post.indexOf("=") + 1) === "sauce")
            location.reload();
          if (data_post.substring(data_post.indexOf("=") + 1) === "ingt")
            location.reload();
          if (data_post.substring(data_post.indexOf("=") + 1) === "cat")
            location.reload();
          if (data_post.substring(data_post.indexOf("=") + 1) === "plat")
            location.reload();
          if (parseInt(data_post.substring(data_post.indexOf("=") + 1)) === 1)
            location.reload();
        }
        else
          return false;
      } else {
        // Pour chaque champs en erreur
        jQuery.each(result, function(champ, erreurs) {
          // Pour chacune des erreurs
          jQuery.each(erreurs, function(type, msg) {
            $(t + ' #' + champ).after('<div class="formValidation ' + type + '">' + msg + '</div>');
          });
        });
        $("input", $(".formValidation:first").parent()).focus();
        return false;
      }
    }, dataType: "html"
            //, dataType: "json"
  });
}

///LISTE COMMANDE
function listcmd_Event(ev) {
  $('#help').hide('normal');
  var key = ev.keyCode || ev.charCode;
  switch (key) {
    case 113://F2 nouvelle commande
      $.ajax({
        url: "/caisse/client",
        success: function(text) {
          $.ajax({
            url: "/caisse/panier/truncate"
          });
          $('body').attr("onKeyup", "cmd_Event(event)");
          $("#client").html(text);
          complete_form_client();

          $("#list_cmd").css("display", "none");
          $("input[name=type_cmd]").focus();
          $("#list_panier tr[id!=header]").remove();

          $("#exe_cmd").css("display", "block");
        }
      });
      $("#exe_cmd").css("display", "block");
      break;
    case 119://F8  modifier COMMANDE
      var verif = function(value) {
        verif_password(value);
      };
      promptHTML("Modifier la commande", verif);

      break;
    case 120://F9 double commande
      $('body').attr("onKeyup", "listcmd_Event2(event)");
      break;
    case 27://ECHAP
      window.close();
      opener.focus();
      break;
    case 38://haut
      focus = "0";
      $("select[name=livreur]").each(function() {
        if ($(this).attr("etat_focus") === 'true')
          focus = "1";
      });
      if (focus === "0") {
        if (typeof($('#list_cmd_global tr[id_cmd].hightlight').attr("id_cmd")) !== "undefined") {
          $('#list_cmd_global tr[id_cmd].hightlight').prev().addClass("hightlight");
          $('#list_cmd_global tr[id_cmd].hightlight:last').removeClass("hightlight");
        }
      }
      break;
    case 40://bas

      var focus = "0";
      $("select[name=livreur]").each(function() {
        if ($(this).attr("etat_focus") === 'true')
          focus = "1";
      });
      if (focus !== "1") {
        if (typeof($('#list_cmd_global tr[id_cmd].hightlight').attr("id_cmd")) !== "undefined") {
          $('#list_cmd_global tr[id_cmd].hightlight').next().addClass("hightlight");
          $('#list_cmd_global tr[id_cmd].hightlight:first').removeClass("hightlight");
        }
        else
          $('#list_cmd_global tr[id_cmd]:first').attr("class", "hightlight");
      }
      break;
    case 13:// entrer
      if ($('#confirm').css("display") !== "block") {
        $('table#list_cmd_global tr.hightlight select').focus();
      }
      break;

      /*default:
       alert(ev.keyCode);*/
  }
}

function listcmd_Event2(ev)
{
  var key = ev.keyCode || ev.charCode;
  switch (key) {
    case 73://itineraire  i
      if (typeof($('#list_cmd_global tr.hightlight td[class=client] span').attr("id")) !== "undefined") {

        var dpt = get_constant("ADDR");

        var addr = $('#list_cmd_global tr.hightlight td[class=client] span').text();
        rechercher(dpt, 1);
        setTimeout("rechercher($('#list_cmd_global tr.hightlight td[class=client] span').text(),0)", 1000);
      }
      break;
    case 80://plan p
      if (typeof($('#list_cmd_global tr.hightlight td[class=client] span').attr("id")) !== "undefined") {
        rechercher($('#list_cmd_global tr.hightlight td[class=client] span').text(), 1);
      }
      break;
    case 96://reinitiale plan 0
      reinit();
      break;
    case 72://affiche l'aide H
      affich_help();
      break;
    case 120://F9 reimprimer commande
      var id_cmd = $('tr.hightlight').attr("id_cmd");
      print_cmd(id_cmd);
      break;
    case 46:// touche suppr annuler commande
      var id_cmd = $('tr.hightlight').attr("id_cmd");

      if (typeof(id_cmd) !== "undefined") {
        if (confirm("Voulez vous vraiment annuler cette commande ?")) {
          var verif = function(value) {
            CancelCmd(value);
          };
          promptHTML("Annulation de la commande", verif);
        }
      }
      break;
    default:
      $('body').attr("onKeyup", "listcmd_Event(event)");
  }
}

function verif_password(value) {
  $.ajax({
    url: "/caisse/index/verifpwd/value/" + value,
    type: "get",
    success: function(text) {
      $('input[name=mdp]').blur();
      if (parseInt(text) === 1) {
        var id = $('tr.hightlight').attr("id_cmd");
        if (typeof(id) !== "undefined") {
          //text= $.ajax({url:"/caisse/cmd.php?id_cmd="+id});
          $.ajax({
            url: "/caisse/client/index/id_cmd/" + id,
            success: function(text) {
              $.ajax({
                url: "/caisse/panier/truncate"
              });
              $('body').attr("onKeyup", "cmd_Event(event)");
              $("#client").html(text);

              complete_form_client();

              $("#exe_cmd").css("display", "block");
              $("#list_cmd").css("display", "none");
              $("input[name=type_cmd]:checked").focus();
              $("#list_panier tr[id!=header]").remove();
              $.ajax({
                url: "/caisse/panier/affichcmd/id_cmd/" + id,
                type: "get",
                success: function(html_cmd) {
                  $('table#list_panier tbody').html(html_cmd);
                  tr = $('tr:first').next();
                  $('select#qte', tr).focus();
                  $(tr).addClass('trhighlight');
                  $("#exe_cmd #id_cmd:first").val(id);
                  affich_total();
                }
              });
            }
          });

        }
        $('#confirm').css("display", "none");
        $('input[name=mdp]').val("");
        $('body').attr("onKeyup", "cmd_Event(event)");
      }
      else {
        if (confirm("Mot de passer erroné, désirez vous recommencer ?")) {
          $('tr.hightlight select').blur();
          $('input[name=mdp]').val("");
          setTimeout("$('#confirm input[name=mdp]').focus();", 100);
        }
        else {
          $('#confirm').css("display", "none");
          $('input[name=mdp]').val("");
        }
      }
    }
  });

}

function CancelCmd(value) {
  $.ajax({
    url: "/caisse/index/verifpwd/value/" + value,
    type: "get",
    success: function(text) {
      $('input[name=mdp]').blur();
      if (parseInt(text) === 1) {
        var id = $('tr.hightlight').attr("id_cmd");
        if (typeof(id) !== "undefined") {
          //text= $.ajax({url:"/caisse/cmd.php?id_cmd="+id});
          $.ajax({
            url: "/caisse/index/cancel/id_cmd/" + id,
            success: function(text) {
              location.reload();
            }
          });

        }
        $('#confirm').css("display", "none");
        $('input[name=mdp]').val("");
        $('body').attr("onKeyup", "cmd_Event(event)");
      }
      else {
        if (confirm("Mot de passer erroné, désirez vous recommencer ?")) {
          $('tr.hightlight select').blur();
          $('input[name=mdp]').val("");
          setTimeout("$('#confirm input[name=mdp]').focus();", 100);
        }
        else {
          $('#confirm').css("display", "none");
          $('input[name=mdp]').val("");
        }
      }
    }
  });

}

function livreur(ev, id_cmd, value) {
  var key = ev.keyCode || ev.charCode;

  if (parseInt(key) === 45) {
    $.ajax({
      url: "/caisse/Index/insertLivreur",
      data: "id_cmd=" + id_cmd + "&value=" + value,
      type: 'POST',
      success: function(text) {
        $("#div_recettes").html(text);
        $("input#focus_" + id_cmd).focus();
      }
    });
  }
}

function reglement(ev, id_cmd, value) {
  var key = ev.keyCode || ev.charCode;

  if (parseInt(key) === 82) {
    $.ajax({
      url: "/caisse/Index/insertPaiement",
      data: "id_cmd=" + id_cmd + "&value=" + value,
      dataType: "json",
      type: 'POST',
      success: function(json) {
        $("input#focus_" + id_cmd).focus();

        for (var i in json) {
          $("#detail_recette_" + i).empty();
          for (var j in json[i]) {
            $("#detail_recette_" + i).append(json[i][j] + "<br/>");
          }
        }
      }
    });
  }
}
//googlemap



function trouveRoute() {
  /*test si les variables sont bien initialisés*/

  if (depart && arrivee)
  {
    /*mode de transport*/

    var choixMode = "DRIVING";
    var request = {
      origin: depart,
      destination: arrivee,
      travelMode: google.maps.DirectionsTravelMode[choixMode],
      waypoints: waypoint,
      optimizeWaypoints: true
    };
    /*appel à l'API pour tracer l'itinéraire*/
    directionsService.route(request, function(response, status) {
      if (status === google.maps.DirectionsStatus.OK) {
        directionsDisplay.setDirections(response);
      }
    });
    $("#divRoute").css("display", "block");

  }
}

function rechercher(src, code) {


  ptCheck = code; /*adresse de départ ou arrivée ? */
  if (geocoder) {
    // geocoder.geocode( { 'address': document.getElementById(src).value}, function(results, status) {
    geocoder.geocode({'address': src}, function(results, status) {
      if (status === google.maps.GeocoderStatus.OK) {

        /*ajoute un marqueur à l'adresse choisie*/
        map.setCenter(results[0].geometry.location);
        if (marker) {
          marker.setMap(null);
        }
        marker = new google.maps.Marker({
          map: map,
          position: results[0].geometry.location
        });
        /*on remplace l'adresse par celle fournie du geocoder*/
        document.getElementById(src).value = results[0].formatted_address;
        src = results[0].formatted_address;
        if (ptCheck) {
          depart = results[0].formatted_address;

        }
        else {
          if (arrivee) {
            waypoint.push({
              location: arrivee,
              stopover: true}
            );
            waypoint.push({
              location: results[0].formatted_address,
              stopover: true}
            );
            arrivee = depart;
          }
          else {
            arrivee = results[0].formatted_address;
          }
        }
        /*trace la route*/
        trouveRoute();
      }
      else {
        alert("Geocode n'a rien trouvé !\n raison : " + status);
      }
    });
  }

}


function trim(myString) {
  return myString.replace(/^\s+/g, '').replace(/\s+$/g, '');
}
