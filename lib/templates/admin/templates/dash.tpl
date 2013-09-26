<script>
var widgetsUnlocked = 0;

/**
  * Toogle lock
  *
  * @author Damian Kęska
  */

function toggleWidgetsLock(lock)
{
    if (widgetsUnlocked == 1 || lock == 1)
    {
        $('#widgetsLockedImg').attr('src', $('#widgetsLockedImg').attr('src').replace('-locked.png', '-unlocked.png'));
        $('#widgetsLockedSpan').html('{function="localize('Unlock widgets', 'dash')"}');
        $('.widgetRemoveButtons').hide();
        $('#newWidgetIcon').hide();
        widgetsUnlocked = 9;
    } else {
        $('#widgetsLockedImg').attr('src', $('#widgetsLockedImg').attr('src').replace('-unlocked.png', '-locked.png'));
        $('#widgetsLockedSpan').html('{function="localize('Lock widgets', 'dash')"}');
        $('.widgetRemoveButtons').show();
        $('#newWidgetIcon').show();
        widgetsUnlocked = 1;
    }
}
 
/**
  * Remove widget
  *
  * @author Damian Kęska
  */

function removeWidget(widgetName)
{
    navigateTo('?display=dash&cat=admin&widget='+widgetName+'&action=remove');
}

/**
  * Add new widget
  *
  * @author Damian Kęska
  */

$(function(){
	$('.contextMenu').styleddropdown(function (value) {
	    navigateTo('?display=dash&cat=admin&widget='+value+'&action=add');
	});

	if ({$widgetsUnlocked} == 1 && $('#widgetsLockedImg').length > 0)
	    toggleWidgetsLock(0);
});

</script>

<style type="text/css">
#widgetRemoveButtons {
    display: none;
}
</style>

{include="ui.titlebar"}

        {loop="$dash_messages"}
            {if="$value.type == 'warning'"}
                <div class="msgWarning" style="display: block;">{$value.message}</div>
            {/if}

            {if="$value.type == 'error'"}
                <div class="msgError" style="display: block;">{$value.message}</div>
            {/if}

            {if="$value.type == 'info'"}
                <div class="msgInfo" style="display: block;">{$value.message}</div>
            {/if}

            {if="$value.type == 'success'"}
                <div class="msgSuccess" style="display: block;">{$value.message}</div>
            {/if}
        {/loop}

        <div class="grid-1">
               <ul class="picturesBox">
                   {loop="$dash_menu"}
            	   <li><a style="cursor: pointer;" class="{if="$value.linkType == 'ajax'"}ajax_link{/if}"{if="$value.linkType == 'onclick'"} onclick="{$value.link}"{else} href="{$value.link|pantheraUrl}"{/if}>  <img src="{$value.icon|pantheraUrl}" alt=""></a>
            	         <ul class="picturesBoxItem">
                         		<a style="cursor: pointer;" class="{if="$value.linkType == 'ajax'"}ajax_link{/if}"{if="$value.linkType == 'onclick'"} onclick="{$value.link}"{else} href="{$value.link|pantheraUrl}"{/if}>{$value.name}</a>
                         </ul>
                   </li>
                   {/loop}
				</ul>
				 <div class="clear"></div>
        </div>

        {if="isset($showWidgets)"}
        <div style="float: right; margin-right: 40px; height: 20px;">
            <!-- add new widget button -->
            <a href="#" id="newWidgetIcon" {if="$widgetsUnlocked == 0"}style="display: none;"{/if}>
                <span class="tooltip">{function="localize('Add new widget', 'dash')"}</span>

                <div class="contextMenu" style="display: inline;">
                    <img src="{$PANTHERA_URL}/images/admin/list-add.png" style="height: 20px; margin-right: 5px;" class="field">

	                <ul class="list" style="left: -300px;">
	                    <b>{function="localize('Add new widget', 'dash')"}</b>
		                {loop="$dashAvaliableWidgets"}
		                  {if="!$value"}
		                    <li>{$key}</li>
		                  {/if}
		                {/loop}
	                </ul>
                </div>
            </a>

            <!-- lock and unlock widgets button -->
            <a href="#" onclick="toggleWidgetsLock();">
                <span class="tooltip" id="widgetsLockedSpan">{if="$widgetsUnlocked == 0"}{function="localize('Unlock widgets', 'dash')"}{else}{function="localize('Lock widgets', 'dash')"}{/if}</span>
                <img src="{$PANTHERA_URL}/images/admin/object-{if="$widgetsUnlocked == 0"}unlocked{else}locked{/if}.png" style="height: 20px; margin-right: 5px;" id="widgetsLockedImg">
            </a>
            
            <!-- permissions popup -->
            <a href="#" onclick="createPopup('?display=acl&cat=admin&popup=true&name=can_see_dash', 1300, 550);">
                <span class="tooltip" id="widgetsLockedSpan">{function="localize('Manage permissions')"}</span>
                <img src="{$PANTHERA_URL}/images/admin/menu/users.png" style="height: 20px;" id="widgetsLockedImg">
            </a>
        </div>

        <div style="width: 100%; height: 20px;">&nbsp;</div>
        {/if}

      {if="isset($galleryItems)"}
        {if="count($galleryItems) == 0"}
        <div class="grid-2">
        	<div class="title-grid">{function="localize('Gallery')"}<span id="widgetRemoveButtons" class="widgetRemoveButtons"><a href="#" onclick="removeWidget('gallery')"><img src="{$PANTHERA_URL}/images/admin/list-remove.png" style="height: 15px;"></a></span></div>
        	<div class="content-gird">
        		<p style="text-align: center;">{function="localize('No any items found, nothing to display', 'dash')"}.</p>
        	</div>
        </div>
        {else}
        <div class="grid-2">
           <div class="title-grid">{function="localize('Gallery')"}<span id="widgetRemoveButtons" class="widgetRemoveButtons"><a href="#" onclick="removeWidget('gallery')"><img src="{$PANTHERA_URL}/images/admin/list-remove.png" style="height: 15px;"></a></span></div>
           <div class="content-gird">
           <ul class="picturesBox">
                   {loop="$galleryItems"}
            	   <li><a href="#edit" onclick="navigateTo('?display=gallery&cat=admin&action=edit_item_form&itid={$value->id}');">  <img src="{$value->thumbnail|pantheraUrl}" alt="" style="max-width: 110px;"></a>
            	         <ul class="picturesBoxItem">
                         		<a href="#edit" onclick="navigateTo('?display=gallery&cat=admin&action=edit_item_form&itid={$value->id}');">{$value->title}</a>
                         </ul>
                   </li>
                   {/loop}
		   </ul>
                <div class="clear"></div>
           </div>
        </div>
        {/if}
      {/if}

        {if="isset($lastLogged) and count($lastLogged) > 0"}
        <div class="grid-2">
           <div class="title-grid">{function="localize('Recently logged in users', 'dash')"}<span id="widgetRemoveButtons" class="widgetRemoveButtons"><a href="#" onclick="removeWidget('lastLogged')"><img src="{$PANTHERA_URL}/images/admin/list-remove.png" style="height: 15px;"></a></span></div>
           <div class="content-table-grid">
              <table class="insideGridTable">
                   {loop="$lastLogged"}
                   <tr>
            	        <td><a href="?display=users&cat=admin&action=account&uid={$value.uid}" class="ajax_link"><img src="{$value.avatar}" style="width: 20px"></a></td><td><a href="?display=users&cat=admin&action=account&uid={$value.uid}" class="ajax_link">{$value.login}</a></td><td> {$value.time} {function="localize('ago', 'dash')"}</td>
            	   </tr>
                   {/loop}
               </table>
                <div class="clear"></div>
           </div>
        </div>
        {/if}

        {loop="$dashCustomWidgets"}
            {include="$value"}
        {/loop}
