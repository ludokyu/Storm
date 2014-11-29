function admin_event(ev, time) {
    if ($('tr[class=hightlight]').attr('id_cmd') != undefined && ev.keyCode == 46 && confirm('Confirmez vous la suppression de cette commande ?')) {
        location.href = '/admin/chiffre/delete/id_cmd/' + $('tr[class=hightlight]').attr('id_cmd') + time;
    }
}

function getMonthFromYear(year) {
    $.ajax({
        url: "/admin/chiffre/month/year/" + year,
        success: function(text) {
            $("select#month").html(text);
        }
    });
}

function getDayFromMonthYear(year, month) {
    $.ajax({
        url: "/admin/chiffre/day/year/" + year + "/month/" + month,
        success: function(text) {
            $("select#day").html(text);
        }
    });
}

function affich_detail_cmd(id_cmd) {

    var script_cmd = function(text) {
        $("#detail_cmd").html(text);
        $("#detail_cmd").css("display", "block");

    };

    $.ajax({
        url: "/admin/chiffre/detailCmd/",
        data: "id_cmd=" + id_cmd,
        type: "POST",
        success: script_cmd
    });
}

function affich_detail_client(id_client) {

    var script_client = function(text) {
        $("#detail_client").html(text);
        $("#detail_client").css("display", "block");
    };
    ajax("../include/ajax.php?action=detail_client&id_client=" + id_client, 'get', '', true, script_client);
}

function cat_default() {
    id_cat = $("input[name=is_default]:checked").val();
    $.ajax({
        url: "/admin/plat/catDefault/",
        data: "id_cat=" + id_cat,
        type: "POST"
    });
    // ajax("../include/ajax.php?action=cat_default_admin&id_cat="+id_cat,'get','',true);
}

function modif_plat_admin(id_cat, id_plat) {
    $.ajax({
        url: "/admin/plat/plat/id_cat/" + id_cat + "/id_plat/" + id_plat,
        type: "GET",
        success: function(text) {
            $("#form").html(text);
            $("#form").css("display", "block");
        }
    });

}

function modif_ingt_admin(id_ingt) {
    $.ajax({
        url: "/admin/plat/ingt/",
        data: "id_ingt=" + id_ingt,
        type: "GET",
        success: function(text) {
            $("#form").html(text);
            $("#form").css("display", "block");
        }
    });
    //ajax("../include/ajax.php?action=modif_ingt_admin&id_ingt="+id_ingt,'get','',true,ingt);
}

function modif_sauce_admin(id_base) {
    $.ajax({
        url: "/admin/plat/base/",
        data: "id_base=" + id_base,
        type: "GET",
        success: function(text) {
            $("#form").html(text);
            $("#form").css("display", "block");
        }
    });

}

function modif_cat_admin(id_cat) {
    $.ajax({
        url: "/admin/plat/cat/",
        data: "id_cat=" + id_cat,
        type: "GET",
        success: function(text) {
            $("#form").html(text);
            $("#form").css("display", "block");
        }
    });
}

function submit_form_plat(id_form, page, method, div) {
    $('select[id^=list_plat_in_menu_] option').attr("selected", "selected");
    submit_form(id_form, page, method, div);
}

function add_taille(value) {
    last = $("input[name^=tab_taille]:last").attr("id");
    if (last == undefined) {
        for (i = 1; i <= value; i++) {

            html = "<br/><label for='tab_taille-" + i + "'>" + i +
                    "<input type='text' name='tab_taille[]' value='' id='tab_taille-" + i + "'  style='float:none;margin-left:5px;'/></label>";
            if (i > 1) {
                $("input[name^=tab_taille]:last").after(html);
            }
            else {
                html = '<label class="optional" for="tab_taille">Tailles :</label>' + html;
                $("select[name=nb_taille]").after(html);
            }
        }
    }
    else {
        last = last.substring(11);

        if (parseInt(last) <= parseInt(value)) {
            for (i = parseInt(last) + 1; i <= value; i++) {
                html = "<br/><label for='tab_taille-" + i + "'>" + i +
                        "<input type='text' name='tab_taille[]' value='' id='tab_taille-" + i + "'  style='float:none;margin-left:5px;'/></label>";
                $("input[name^=tab_taille]:last").after(html);
            }
        }
        else if (parseInt(last) > parseInt(value)) {
            for (i = last; i > value; i--) {

                $("label[for^=tab_taille]:last").remove();

            }

        }
    }
}

function changeTaille(value) {
    if (value == 0) {
        $('select#nb_taille').attr('disabled', 'disabled');
        $("label[for^=tab_taille]").remove();
    }
    else {
        $('select#nb_taille').removeAttr('disabled', '');
    }
}

function valid_taille() {
    if ($('input[name=is_taille]:checked').val() == 1) {
        $('input[name=nb_taille]').attr('disabled', '');
    }
    else if ($('input[name=is_taille]:checked').val() == 0) {
        $('input[name=nb_taille]').attr('disabled', 'disabled');
    }
}

function modif_line_menu(k) {
    if ($("select#cat_" + k + " option:selected").attr("is_taille") == 1) {
        html = ajax("../include/ajax.php?action=list_taille&id_cat=" + $("select#cat_" + k + " option:selected").val());
        $("select#cat_" + k).after("<select name='taille_menu_" + k + "'>" + html + "</select>");
        $("select[name=taille_menu_" + k + "] option:first").remove();
    }
    else
        $("select[name=taille_menu_" + k + "]").remove();
    html = ajax("../include/ajax.php?action=list_plat&id_cat=" + $("select#cat_" + k + " option:selected").val());
    $("#list_plat_not_in_menu_" + k).html("" + html + "");
}

function list_default(k) {
    html = $("#list_plat_in_menu_" + k).html();
    $("select[name=plat_defaut_" + k + "]").html("<option/>" + html);
}

function listPlat(t, i) {
    $("select#taille_" + i).remove();
    if ($("option:selected", t).attr("is_taille") == 1) {
        //generer select taille
        $.ajax({
            url: "/admin/plat/taille/",
            data: "id=" + t.value + "&i=" + i,
            type: "GET",
            success: function(text) {
                $(t).after(text);
            }
        });
    }
    $.ajax({
        url: "/admin/plat/divPlat/",
        data: "id=" + t.value + "&i=" + i,
        type: "GET",
        success: function(text) {
            $("div#list_plat_" + i).html(text);
            updateListDefault(i);
        }
    });
}

function updateListDefault(i) {
    $("select#id_plat_default_" + i + " option[value!=NULL]").remove();
    $("div#list_plat_" + i + " input:checked").each(function() {
        $("select#id_plat_default_" + i).append("<option label='" + $(this).parent().text() + "' value='" + this.value + "'>" + $(this).parent().text() + "</option>");
    });
}

function delMenu(i) {
    $("#id_menu_" + i).remove();
    $("#qte_" + i).remove();
    $("select#id_cat_" + i).remove();
    $("select#taille_" + i).remove();
    $("#affich_list_plat_" + i).remove();
    $("select#id_plat_default_" + i).remove();
    $("#fieldset-div_list_plat_" + i).remove();
    $("#del_plat_" + i).remove();
}

function addMenu() {
    i = parseInt($("#count_menu").val());
    newi = i + 1;
    $.ajax({
        url: "/admin/plat/addMenu/",
        data: "i=" + newi,
        type: "GET",
        success: function(text) {

            $("#modif_plat #del_plat_" + i).after(text);
            $("#count_menu").val(newi);
        }
    });
}

function search_liv(value, liv) {
    // text=ajax("../include/ajax.php?action=search_liv&value="+value);

    $.ajax({
        url: "/admin/contact/search/",
        data: "search=" + value + "&livreur=" + liv,
        type: "GET",
        success: function(text) {
            $("#contact").html(text);
        }
    });


}


function submit_inventaire(t) {

    var data_form = $(t).serialize();
    // Récupère l'url de l'action
    var url = $(t).attr('action');

    var method = $(t).attr('method');
    if (method == undefined)
        method = "POST";
    // Supprime tous les alertes liées à une validation de formulaire


    // Convertir le Submit du formulaire en Ajax
    $.ajax({
        type: method,
        url: url,
        data: data_form,
        success: function(text) {
            $('#result').html(text);
        }, dataType: "html"
                //, dataType: "json"
    });
}


function print_inventaire() {

    var data_form = $($('form#Inventaire')).serialize();
    // Récupère l'url de l'action



    // Convertir le Submit du formulaire en Ajax
    $.ajax({
        type: "POST",
        url: "admin/inventaire/print",
        data: data_form,
        success: function(text) {
            $('#result').html(text);
        }, dataType: "html"
                //, dataType: "json"
    });
}

function searchClient(name,page){
    $.ajax({
        type: "POST",
        url: "/admin/Client/search",
        data: "ajax=1&name="+name+"&page="+page,
        success: function(text) {
            $('#result').html(text);
        }, dataType: "html"
                //, dataType: "json"
    });
}

function consommationPlat(id_cat,datedeb,datefin){
    $.ajax({
        type: "POST",
        url: "/admin/Plat/statplat",
        data: "id_cat="+id_cat+"&datedeb="+datedeb+"&datefin="+datefin,
        success: function(text) {
            $('li#'+id_cat).append(text);
        }, dataType: "html"
                //, dataType: "json"
    });
}
