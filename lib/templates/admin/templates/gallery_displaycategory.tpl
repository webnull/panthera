{$site_header}

<script type="text/javascript">
    var uploadProgress = new panthera.ajaxLoader($('#addNewImage'));

    function removeGalleryItem(id)
    {
        w2confirm('{function="localize('Are you sure you want delete this item?', 'gallery')"}', function (responseText) {
        
            if (responseText == 'Yes')
            {

                    panthera.jsonGET({ url: '{$AJAX_URL}?display=gallery&cat=admin&action=deleteItem&image_id='+id, success: function (response) {
                            if (response.status == "success")
                            {
                                $('#gallery_item_'+id).remove();
                            }

                        }
                    });
            }
        });
    }

    function toggleItemVisibility(id)
    {
        panthera.jsonPOST({ url: '?display=gallery&cat=admin&action=toggleItemVisibility', data: 'ctgid='+id, success: function (response) {

                    if (response.status == "success")
                    {
                        if (response.visible == 1)
                        {
                            $('#gallery_item_'+id).addClass('galleryItemHidden');
                        } else {
                            $('#gallery_item_'+id).removeClass('galleryItemHidden');
                        }
                    }

                }
        });
    }

    function setAsCategoryThumb(id, ctgid)
    {
        panthera.jsonGET({ 'url': '?display=gallery&cat=admin&action=set_category_thumb&itid='+id+'&ctgid='+ctgid});
    }

    $(document).ready(function () {
        var multiuploadFiles = new Array();

        panthera.multiuploadArea({ id: '#addNewImage', start: function () {
            uploadProgress.ajaxLoaderInit();

        }, callback: function (content, fileName, fileNum, fileCount) {
                panthera.jsonPOST({ url: '?display=upload&cat=admin&action=handle_file&popup=true', isUploading: true, data: { 'image': content, 'fileName': fileName}, success: function (response) {
                        if (response.status == "success")
                        {
                            multiuploadFiles.push(response.upload_id);
                        }

                    }
                });

                // finished
                if (fileNum == fileCount)
                {
                    panthera.jsonPOST({ url: '?display=gallery&cat=admin&action=adduploads&gid={$category_id}', isUploading: true, data: { 'ids': JSON.stringify(multiuploadFiles) }});
                    uploadProgress.stop();
                    navigateTo(window.location);
                }
            }
        });
        
        var saveCategoryDetailsDiv = new panthera.ajaxLoader($('#saveCategoryDetailsDiv'));
        
        /**
          * Save category details
          *
          * @author Damian Kęska
          */
        
        $('#saveCategoryDetails').submit(function () {
            panthera.jsonPOST({ data: '#saveCategoryDetails', messageBox: 'w2ui', spinner: saveCategoryDetailsDiv, success: function (response) {
                
                    // refresh the page
                    if (response.status == "success")
                        setTimeout("navigateTo('?display=gallery&cat=admin&action=displayCategory&unique="+response.unique+"&language="+response.language+"');", 800);

                } 
            });
            return false;
        });
        
        /*$('#removeImage').bind('drop', function (e) {
                console.log("Drop event");
        });
        
        $('.draggableGalleryItem').draggable({ addClasses: false, drag: function (event, ui) {
                if (panthera.inDropRange('#removeImage', 'cursor', event))
                {
                    console.log('In drop range of trash');
                }
        
            } 
        });*/
        
        panthera.forms.checkboxToggleLayer({ input: '#all_langs_checkbox', layer: '#language_input', reversed: true });

    });
</script>

{include="ui.titlebar"}

<div id="topContent">
    {$uiSearchbarName="uiTop"}
    {include="ui.searchbar"}
    
    <div class="searchBarButtonArea">
        <input type="button" value="{function="localize('Back')"}" onclick="navigateTo('?display=gallery&cat=admin');" style="float: left; margin-left: 10px;">
       {if="!$all_langs"} <input type="button" value="{function="localize('Other languages', 'gallery')"}" onclick="panthera.popup.toggle('element:#languagePopup')"> {/if}
        <input type="button" value="{function="localize('Settings')"}" onclick="panthera.popup.toggle('element:#settingsPopup')">
    </div>
</div>

{if="!$all_langs"}
<!-- Language popup -->

<div id="languagePopup" style="display: none;">
    <table class="formTable" style="margin: 0 auto; margin-bottom: 30px;">
        
        <thead>
            <tr>
                <td colspan="2" class="formTableHeader" style="padding-top: 0px; padding-bottom: 30px;">
                    <p style="color: #e5ebef; padding: 0px; margin: 0px; margin-left: 15px; font-size: 14px;">{function="localize('Select language name to edit or create this gallery in other language', 'gallery')"}</p>
                </td>
            </tr>
        </thead>
        
        <tbody>
           {loop="$languages"}
            <tr>
                <th style="padding: 4px; padding-left: 90px;"><a href="#{$key}" onclick="navigateTo('?display=gallery&cat=admin&action=displayCategory&unique={$unique}&language={$key}');">{$key}</a></th>
                <th style="padding: 4px; padding-left: 90px;"></th>
            </tr>
           {/loop}
        </tbody>
        
        <tfoot>
          <tr>
            <td colspan="2" style="padding-top: 35px;">
                <input type="button" value="{function="localize('Cancel')"}" onclick="panthera.popup.close()" style="float: right; margin-right: 180px;">
            </td>
          </tr>
        </tfoot>
        
    </table>
</div>
{/if}


<!-- Settings popup -->

<div id="settingsPopup" style="display: none;">
   <form action="?display=gallery&cat=admin&action=saveCategoryDetails&id={$galleryObject->id}" method="POST" id="saveCategoryDetails">
    <table class="formTable" style="margin: 0 auto; margin-bottom: 30px;">
        
        <tbody>
            <tr id="title_tr">
                <th>{function="localize('Title', 'gallery')"}:</th>
                <th><input type="text" style="width: 95%;" name="title" value="{$galleryObject->title}"></th>
            </tr>
            <tr id="created_tr">
                <th>{function="localize('Created', 'gallery')"}:</th>
                <th>{$galleryObject->created} ({$galleryObject->author_login})</th>
            </tr>
            <tr id="all_langs_tr">
                <th>{function="localize('Make this gallery same for all languages', 'gallery')"}:</th>
                <th><input type="checkbox" name="all_langs" value="1"{if="$all_langs"} checked{/if} id="all_langs_checkbox"></th>
            </tr>
            
            <tr {if="$all_langs"}style='display: none;'{/if} id="language_input">
                <th>{function="localize('Save this gallery in', 'gallery')"}:</th>
                <th>
                    <select name="language">
                    {loop="$languages"}
                    <option value="{$key}"{if="$value->language == $key"} selected{/if}>{$key}</option>
                    {/loop}
                    </select>
                </th>
            </tr>
        </tbody>
        
        <tfoot>
          <tr>
            <td colspan="2" style="padding-top: 35px;">
                <input type="button" value="{function="localize('Cancel')"}" onclick="panthera.popup.close()" style="float: left; margin-left: 30px;">
                <input type="submit" value="{function="localize('Save')"}" style="float: right; margin-right: 30px;">
            </td>
          </tr>
        </tfoot>
        
    </table>
   </form>
   
   <script type="text/javascript">
    var saveCategoryDetailsDiv = new panthera.ajaxLoader($('#saveCategoryDetailsDiv'));
    
      /**
        * Save category details
        *
        * @author Damian Kęska
        */
   
    $('#saveCategoryDetails').submit(function () {
        panthera.jsonPOST({ data: '#saveCategoryDetails', messageBox: 'w2ui', spinner: saveCategoryDetailsDiv, success: function (response) {
            // refresh the page
            if (response.status == "success")
                setTimeout("navigateTo('?display=gallery&cat=admin&action=displayCategory&unique="+response.unique+"&language="+response.language+"');", 800);
            } 
        });
        return false;
    });
   </script>
</div>


<div id="popupOverlay" style="text-align: center; padding-top: 20px; padding-bottom: 0px;"></div>

<!-- Content -->
<div class="ajax-content" style="text-align: center;">
  <div id="items_list">
    {loop="$item_list"}
    <div class="galleryItem{if="$value->visibility == 1"} galleryItemHidden{/if} draggableGalleryItem" id="gallery_item_{$value->id}">
        <div class="galleryImageFrame">
            <div class="paGalleryFrameContent">
                <img src="{$value->getThumbnail(300, True, True)}" class="galleryImage">
            </div>
            <div class="paGalleryFrameOverlay">
                <small>
                <br>
                <b>{$value->title}</b>
                <br><br>
                <i>{$value->description}</i>
                <br><br>{function="localize('Created', 'gallery')"}: {$value->created}
                </small>
                <br><br>
                {function="localize('url', 'gallery')"}: <input type="text" value="{$value->link}" style="width: 200px; height: 20px;">
            </div>
        </div>
        <div class="galleryItemDetails">
            <div style="text-align: center;">
                <a href="#edit" onclick="navigateTo('?display=gallery&cat=admin&action=edit_item_form&itid={$value->id}');"><img src="{$PANTHERA_URL}/images/admin/tango-icon-theme/Text-x-generic_with_pencil.svg" class="galleryIcon" title="{function="localize('Edit', 'messages')"}"></a>
                <a href="#delete" onclick="removeGalleryItem({$value->id});"><img src="{$PANTHERA_URL}/images/admin/menu/Actions-process-stop-icon.png" class="galleryIcon" title="{function="localize('Delete', 'messages')"}"></a>
                <a href="#toggle-visibility" onclick="toggleItemVisibility({$value->id});"><img src="{$PANTHERA_URL}/images/admin/tango-icon-theme/System-search.svg" class="galleryIcon" title="{function="localize('Toggle visibility', 'gallery')"}"></a>
                <a href="#rights" onclick="createPopup('_ajax.php?display=acl&cat=admin&popup=true&name=can_manage_gallery_{$value->id}', 1024, 550);"><img src="{$PANTHERA_URL}/images/admin/menu/users.png" class="galleryIcon" title="{function="localize('Manage permissions', 'messages')"}" id="permissionsButton"></a>
                <a href="#thumbnail" onclick="setAsCategoryThumb({$value->id}, {$category_id});"><img src="{$PANTHERA_URL}/images/admin/tango-icon-theme/Image-x-generic.svg" class="galleryIcon" title="{function="localize('Set as thumbnail', 'gallery')"}"></a>
            </div>
        </div>
    </div> 
    {/loop}
    
    <div class="galleryItem" style="height: 100px; width: 100px; position: relative; border-radius: 2px;" id="addNewImage" ondragover="return false;">
        <div class="paGalleryFrameOverlay" style="display: block; border-radius: 2px; opacity: 0.4; -moz-opacity: 0.4; -khtml-opacity: 0.4;">
            <a href="#" onclick="navigateTo('?display=gallery&cat=admin&action=add_item&ctgid={$category_id}');"><span class="tooltip">{function="localize('Drag and drop files to this area to start uploading', 'gallery')"}</span><img src="{$PANTHERA_URL}/images/admin/cross_icon.png" style="position: absolute; top: 30px; left: 30px; opacity: 0.8;"></a>
        </div>
    </div>
    <div style="width: 100%; display: inline-block;">&nbsp;</div>
  </div>
</div>