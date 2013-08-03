{$site_header}
<script>
$('.ajax_link').click(function (event) { event.preventDefault(); navigateTo(jQuery(this).attr('href')); return false;});

var spinner = new panthera.ajaxLoader($('#conftoolTable'));

/**
  * Save variable to database
  *
  * @author Mateusz Warzyński
  */

function saveVariable(id)
{
    value = $('#value_'+id).val();
    section = $('#section_'+id).val();

    panthera.jsonPOST({ url: '{$AJAX_URL}?display=conftool&cat=admin&action=change', data: 'id='+id+'&value='+value+'&section='+section, messageBox: 'userinfoBox', spinner: spinner, success: function (response) {
            if (response.status == "success")
            {
               jQuery('#button_'+id).attr("disabled", "disabled");
               jQuery('#button_'+id).animate({ height:'toggle'});
               setTimeout("jQuery('#button_"+id+"').removeAttr('disabled');", 2500);
               setTimeout("jQuery('#button_"+id+"').animate({ height:'toggle' });", 2500);
            }
        }
    });
}

function removeKey(id)
{
    w2confirm('{function="localize('Are you sure you want to delete this key?', 'conftool')"}', function callbackBtn(btn) { 
        if (btn == "Yes")
        {
            panthera.jsonPOST({ url: '{$AJAX_URL}?display=conftool&cat=admin&action=remove', data: 'key='+id, messageBox: 'userinfoBox', spinner: spinner, success: function (response) {
                    if (response.status == "success")
                    {
                       $('#tr_'+id).remove();
                    }
                }
            });
        }
     });
}

/**
  * Callback function for array edition
  *
  * @param string k
  * @param string value
  * @return void 
  * @author Damian Kęska
  */

function conftool_array(k, value)
{
    $('#value_'+k).val(value);
}

</script>

        <div class="titlebar">{function="localize('Configuration editor', 'conftool')"} - {function="localize('Administration tool for developers and administrators.', 'conftool')"}{include="_navigation_panel"}</div><br>

        <div class="msgSuccess" id="userinfoBox_success"></div>
        <div class="msgError" id="userinfoBox_failed"></div>

        <div class="grid-1" style="position: relative;" id="conftoolTable">
         <table class="gridTable">

            <thead>
                <tr>
                    <th scope="col" class="rounded-company" style="width: 250px;">{function="localize('Key')"}</th>
                    <th>{function="localize('Value')"}</th>
                    <th colspan="2">{function="localize('Section', 'conftool')"}</th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <td colspan="5" class="rounded-foot-left"><em>Panthera - {function="localize('Configuration editor', 'conftool')"} <input type="button" value="{function="localize('Manage permissions')"}" id="permissionsButton" onclick="createPopup('_ajax.php?display=acl&cat=admin&popup=true&name=can_update_config_overlay', 1024, 'upload_popup');" style="float: right; margin-right: 7px;"></em></td>
                </tr>
            </tfoot>

            <tbody>
              {loop="$a"}
                <tr id="tr_{$key}">
                    <td>

                        <small>{$value[0]}</small> &nbsp;<b>{if="isset($value[2])"}{$value[2]}{else}{$key}{/if}</b>

                    </td>
                    <td>
                        {if="$value[0] == 'bool'"}
                            <select id="value_{$key}" style="width: 95%;"><option value="0">{function="localize('No')"}</option><option value="1"{if="$value[1] == '1'"} selected{/if}>{function="localize('Yes')"}</option></select>
                        {else}
                        
                            {if="$value[0] == 'int'"}
                                {$type = "number"}
                            {else}
                                {$type = "text"}
                            {/if}
                            
                            {if="$value[0] == 'array'"}
                            <input type="{$type}" value='{$value[1]}' id="value_{$key}" readonly style="width: 80%;">
                            <input type="button" value="{function="localize('Edit')"}" style="width: 49px;" onclick="createPopup('?display=_popup_jsonedit&cat=admin&popup=True&input={$value['b64']}&output=serialize&callback=conftool_array&callback_arg={$key}', 1024, 550);">
                            {else}
                            <input type="{$type}" value='{$value[1]}' id="value_{$key}" style="width: 95%;">
                            {/if}
                        {/if}
                        
                    </td>
                    
                    <td>
                        <input type="text" value="{$value['section']}" id="section_{$key}" style="width: 95%;">
                    </td>
                    
                    <td>
                        <input type="button" value="{function="localize('Remove', 'conftool')"}" onclick="removeKey('{$key}');">
                        <input type="button" value="{function="localize('Save', 'messages')"}" id="button_{$key}" onclick="saveVariable('{$key}');">
                        <span style="font-color: red;"><div id="errmsg_{$key}" style="display: none;"></div></span>
                    </td>
                </tr>
              {/loop}
            </tbody>
           </table>

      </div>
</article>

