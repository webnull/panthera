{if="$action == ''"}
<script type="text/javascript">
$('.ajax_link').click(function (event) { event.preventDefault(); navigateTo(jQuery(this).attr('href')); return false;});

function removeSubscriber(id, elementID)
{
    panthera.jsonPOST({ url: '{$AJAX_URL}?display=newsletter_users&cat=admin&nid={$nid}&action=removeSubscriber', data: 'id='+id, messageBox: 'w2ui', success: function (response) {
            if (response.status == "success")
            {
                $("#sub_"+elementID).remove();
            }
        }
    });
}

function addSubscriber()
{
    panthera.jsonPOST({ url: '{$AJAX_URL}?display=newsletter_users&cat=admin&nid={$nid}&action=addSubscriber', data: 'email='+$("#add_user_email").val(), messageBox: 'w2ui', success: function (response) {
            if (response.status == 'success')
            {
                $('#newsletterUsers').prepend('<tr id="sub_'+response.id+'"><td>'+response.type+'</td><td>'+response.address+'</td><td>'+response.added+'</td></tr>');
            }
        }
    });
}
</script>

<div class="text-section" id="newsletter_users_window">
{/if}
{if="$action == '' OR $action == 'show_table'"}
          <br>
          <table class="gridTable">
            <thead>
                <tr><th>{function="localize('Type')"}</th><th>{function="localize('Address', 'newsletter')"}</th><th>{function="localize('Added', 'newsletter')"}</th><th>{function="localize('Options', 'newsletter')"}</th></tr>
             </thead>            
            
            <tfoot>
                <tr>
                    <td colspan="4" class="rounded-foot-left">{$uiPagerName="adminNewsletter"}{include="ui.pager"}</td>
                </tr>
            </tfoot>
            
            <tbody id="newsletterUsers">
              {loop="$newsletter_users"}
                <tr id="sub_{$value.id}">
                	<td>{$value.type}</td>
                	<td>{$value.address}</td>
                	<td>{$value.added}</td>
                	<td>
	                	<a href="#" onclick="removeSubscriber('{$value.id}', '{$value.id}');">
	                        <img src="{$PANTHERA_URL}/images/admin/ui/delete.png" style="max-height: 20px;" alt="{function="localize('Remove')"}">
	                    </a>
	                </td>
                </tr>
              {/loop}
                <tr>
                    <td colspan="2"> </td><td><input type="text" id="add_user_email" placeholder="{function="localize('Address', 'newsletter')"}" style="width: 95%;"></td>                  
                    <td>
                        <a onclick="addSubscriber();" style="cursor: pointer;">
                            <img src="{$PANTHERA_URL}/images/admin/list-add.png" style="height: 20px;">
                        </a>
                    </td>
                </tr>
            </tbody>
           </table>
{/if}
{if="$action == ''"}
         </div>
{/if}
