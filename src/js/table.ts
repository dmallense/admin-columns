import Table from "./table/table";
import Tooltip from "./modules/tooltips";
import ScreenOptionsColumns from "./table/screen-options-columns";
import ToggleBoxLink from "./modules/toggle-box-link";
// @ts-ignore
import $ from 'jquery';
import {AdminColumnsInterface, LocalizedScriptAC} from "./admincolumns";
import {auto_init_show_more} from "./plugin/show-more";
import {init_actions_tooltips} from "./table/functions";
import {EventConstants} from "./constants";
import {getIdFromTableRow, resolveTableBySelector} from "./helpers/table";
import {initAdminColumnsGlobalBootstrap} from "./helpers/admin-columns";

declare let AC: LocalizedScriptAC

let AdminColumns: AdminColumnsInterface = initAdminColumnsGlobalBootstrap();

$(document).ready(() => {
    let table = resolveTableBySelector(AC.table_id);

    if (table) {
        AdminColumns.Table = new Table(table);
        AdminColumns.Table.init();
        AdminColumns.ScreenOptionsColumns = new ScreenOptionsColumns(AdminColumns.Table.Columns);
    }

    AdminColumns.Tooltips = new Tooltip();

    document.querySelectorAll<HTMLLinkElement>('.ac-toggle-box-link').forEach(el => {
        new ToggleBoxLink(el);
    });

    $('.wp-list-table').on('updated', 'tr', function () {
        AdminColumns.Table.addCellClasses();
        auto_init_show_more();
    });

    // TODO use more global event name instead of IE
    $('.wp-list-table td').on('ACP_InlineEditing_After_SetValue', function () {
        auto_init_show_more();
    });

});

AdminColumns.events.addListener(EventConstants.TABLE.READY, (e) => {
    auto_init_show_more();
    init_actions_tooltips();

    let observer = new MutationObserver(mutations => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node: HTMLElement) => {
                if (node.tagName === 'TR' && node.classList.contains('iedit')) {
                    $(node).trigger('updated', {id: getIdFromTableRow((<HTMLTableRowElement>node)), row: node})
                }
            });
        });
    })

    observer.observe(e.table.getElement(), {childList: true, subtree: true});
});


window.ac_load_table = function (el: HTMLTableElement) {
    AdminColumns.Table = new Table(el);
};
