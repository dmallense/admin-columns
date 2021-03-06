import Actions from "./actions";
import Cells from "./cells";
import Columns from "./columns";
import Cell from "./cell";
import RowSelection from "./row-selection";
import {getIdFromTableRow, getRowCellByName} from "../helpers/table";
import {AdminColumnsInterface} from "../admincolumns";
import {EventConstants} from "../constants";

declare const AdminColumns: AdminColumnsInterface;

export type TableEventPayload = {
    table: Table
}

export default class Table {

    private el: HTMLTableElement
    Columns: Columns
    Cells: Cells
    Actions: Actions
    Selection: RowSelection
    _ids: Array<number>

    constructor(el: HTMLTableElement) {
        this.el = el;
        this.Columns = new Columns(el);
        this.Cells = new Cells();
        this.Actions = document.getElementById('ac-table-actions') ? new Actions(document.getElementById('ac-table-actions')) : null;
        this.Selection = new RowSelection(this);
    }

    getElement(): HTMLTableElement {
        return this.el;
    }

    getIdsFromTable(): Array<number> {
        let result: Array<number> = [];

        this.el.getElementsByTagName('tbody')[0].querySelectorAll('tr').forEach(row => {
            result.push(getIdFromTableRow(row));
        });

        return result;
    }

    init(): void {
        this.initTable();
        this.addCellClasses();

        document.dispatchEvent(new CustomEvent('AC_Table_Ready', {detail: {table: this}}));
        AdminColumns.events.emit(EventConstants.TABLE.READY, {table: this});
    }

    addCellClasses() {
        this.Columns.getColumnNames().forEach((name) => {
            let type = this.Columns.get(name).type;
            let cells = this.Cells.getByName(name);

            cells.forEach((cell: Cell) => {
                cell.getElement().classList.add(type);
            });
        });
    }

    private initTable() {
        this.el.getElementsByTagName('tbody')[0].querySelectorAll<HTMLTableRowElement>('tr').forEach(row => {
            this.updateRow(row);
        });
    }

    updateRow(row: HTMLTableRowElement): void {
        let id = getIdFromTableRow(row);

        row.dataset.id = id.toString();
        this.setCellsForRow(row);
    }

    private setCellsForRow(row: HTMLTableRowElement) {
        let id = getIdFromTableRow(row);

        this.Columns.getColumnNames().forEach((name) => {
            let selector = name.replace(/\./g, '\\.');
            let td = row.querySelector<HTMLTableCellElement>("td.column-" + selector);

            if (td) {
                let cell = new Cell(id, name, td);
                this.Cells.add(id, cell);
            }
        });
    }

    /**
     * @deprecated use Helper function instead
     * TODO remove once IE uses the helper
     */
    getRowCellByName(row: HTMLTableRowElement, column_name: string): HTMLTableCellElement {
        return getRowCellByName(row, column_name);
    }

}