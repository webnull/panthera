<div class="titlebar">{"Menu editor"|localize} - {"Editing item"|localize}: {$item_title}</div><br>

    <div class="msgSuccess" id="userinfoBox_success"></div>
    <div class="msgError" id="userinfoBox_failed"></div>

    <div class="grid-1">
      <form id="save_form" method="POST" action="?display=menuedit&action=save_item">
        <table class="gridTable">
              <thead>
                  <tr>
                      <th scope="col" class="rounded-company" style="width: 250px;">&nbsp;</th>
                      <th>&nbsp;</th>
                  </tr>
              </thead>
              <tfoot>
                  <tr>
                      <td colspan="7" class="rounded-foot-left"><em>Panthera menuedit - {"Editing item"|localize}</em><span>
                      <input type="submit" value="{"Save"|localize}" style="float: right;"> <input type="button" value="{"Back"|localize}" onclick="navigateTo('{navigation::getBackButton()}');" style="float: right;">
                  </tr>
              </tfoot>
              <tbody>
                  <tr>
                      <td>{"Title"|localize}</td>
                      <td><input type="text" name="item_title" value="{$item_title}" style="width: 99%;"></td>
                  </tr>
                  <tr>
                      <td>{"Link"|localize}</td>
                      <td><input type="text" name="item_link" value="{$item_link}" style="width: 99%;"></td>
                  </tr>
                  <tr>
                      <td>{"Language"|localize}</td>
                      <td>
                      <select name="item_language">
                      {foreach from=$item_language key=k item=i}
                          <option value="{$k}"{if $i == True} selected{/if}>{$k}</option>
                      {/foreach}
                      </select>

                      </td>
                  </tr>
                  <tr>
                      <td>{"SEO friendly name"|localize} <small>({"Optional"|localize})</small></td>
                      <td><input type="text" name="item_url_id" value="{$item_url_id}" style="width: 99%;"></td>
                  </tr>
                  <tr>
                      <td>{"Tooltip"|localize} <small>({"Optional"|localize})</small></td>
                      <td><input type="text" name="item_tooltip" value="{$item_tooltip}" style="width: 99%;"></td>
                  </tr>
                  <tr>
                      <td>{"Icon"|localize} <small>({"Optional"|localize})</small></td>
                      <td><input type="text" name="item_icon" value="{$item_icon}" style="width: 99%;"></td>
                  </tr>
                  <tr>
                      <td>{"Attributes"|localize} <small>({"Optional"|localize})</small></td>
                      <td><input type="text" name="item_attributes" value='{$item_attributes}' style="width: 99%;"></td>
                  </tr>
              </tbody>
        </table>
        <input type="hidden" name="item_id" value="{$item_id}">
        <input type="hidden" id="cat_type" name="cat_type" value="{$cat_type}">
       </form>
    </div>