<script type="text/javascript">
spinner = new panthera.ajaxLoader($('#langtoolWindow'));

/**
  * Create domain
  *
  * @author Mateusz Warzyński
  */

function createDomain(locale)
{
    var name = $("#domain_name").val();
    panthera.jsonPOST({ url: '{$AJAX_URL}?display=langtool&action=domains&subaction=add_domain&domain_name='+name+'&locale='+locale, data: '', spinner: spinner, messageBox: 'userinfoBox', success: function (response) {
            if (response.status == "success")
                navigateTo('?display=langtool&action=domains&locale='+locale);
        }
    });

    return false;
}

/**
  * Rename domain
  *
  * @author Mateusz Warzyński
  */

function renameDomain(name, locale, n)
{
    var newname = $("#domain_new_name_"+n).val();
    panthera.jsonPOST({ url: '{$AJAX_URL}?display=langtool&action=domains&subaction=rename_domain&domain_name='+name+'&locale='+locale+'&new_domain_name='+newname, data: '', spinner: spinner, success: function (response) {
            if (response.status == "success")
                $("#domain_name_"+n).html('<a href="?display=langtool&action=view_domain&locale='+locale+'&domain='+newname+'.phps">'+newname+'.phps</a>');
                $("#domain_new_name_"+n).val(newname);
        }
    });

    return false;
}


/**
  * Remove domain
  *
  * @author Mateusz Warzyński
  */

function removeDomain(name, locale, n)
{
    panthera.jsonPOST({ url: '{$AJAX_URL}?display=langtool&action=domains&subaction=remove_domain&domain_name='+name+'&locale='+locale, data: '', spinner: spinner, success: function (response) {
            if (response.status == "success")
                jQuery('#domain_row_'+n).remove();
        }
    });

    return false;
}
</script>

        <div class="titlebar">{function="localize('Languages', 'langtool')"} - {function="localize('Manage domains', 'langtool')"}{include="_navigation_panel.tpl"}</div><br>

        <div class="msgSuccess" id="userinfoBox_success"></div>
        <div class="msgError" id="userinfoBox_failed"></div>

        <div class="grid-1" id="langtoolWindow" style="position: relative;">

          <h1><a onclick="navigateTo('?display=langtool');" href="#">{function="localize('Back')"}</a></h1> <br>

          <table class="gridTable">

            <thead>
                <tr>
                    <th>{function="localize('Locale', 'langtool')"}</th>
                    <th>{function="localize('Domain', 'langtool')"}</th>
                    <th>{function="localize('Options')"}</th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <td colspan="3" class="rounded-foot-left">
                      <em> Panthera - {function="localize('List of available domains', 'langtool')"}
                        <input type="button" value="{function="localize('Add domain', 'langtool')"}" onclick="createDomain('{$locale}');" style="float: right;">
                        <input type="text" name="domain_name" id="domain_name" style="float: right; margin-right: 7px;">
                      </em>
                    </td>
                </tr>
            </tfoot>

            <tbody>
              {$j=0}
              {loop="$domains"}
              {$j=$j+1}
                <tr id="domain_row_{$j}">
                    <td style="width: 1%;"><img src="{$PANTHERA_URL}/images/admin/flags/{$locale}.png"></td>
                    <td id="domain_name_{$j}"><a href="#" onclick="navigateTo('?display=langtool&action=view_domain&locale={$locale}&domain={$value}');">{$value}</a></td>
                    <td style="width: 230px;">
                        <input type="text" name="domain_new_name" value="{$value}" id="domain_new_name_{$j}" style="margin-right: 5px;"><input type="button" value="{function="localize('Remove')"}" onclick="removeDomain('{$value}', '{$locale}', '{$j}');">
                    </td>
                </tr>
                
                <script type="text/javascript">
                    $(document).ready(function () { 
                        panthera.inputTimeout({ element: '#domain_new_name_{$j}', interval: 1200, callback: function () { renameDomain('{$value}', '{$locale}', '{$j}'); }});
                    });
                </script>
              {/loop}
            </tbody>
          </table>
        </div>

