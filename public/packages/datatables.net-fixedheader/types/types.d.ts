// Type definitions for datatables.net-fixedheader 3.1
// Project: https://datatables.net/extensions/fixedheader/, https://datatables.net
// Definitions by: Jared Szechy <https://github.com/szechyjs>, Kiarash Ghiaseddin <https://github.com/Silver-Connection>
// Definitions: https://github.com/DefinitelyTyped/DefinitelyTyped
// TypeScript Version: 2.4

/// <reference types="jquery" />
/// <reference types="datatables.net"/>

declare namespace DataTables {
    interface Settings {
        /*
         * FixedHeader extension options
         */
        fixedHeader?: boolean | FixedHeaderSettings;
    }

    interface StaticFunctions {
        FixedHeader: FixedHeaderStaticFunctions;
    }

    interface FixedHeaderStaticFunctions {
        new (dt: Api, settings: boolean | FixedHeaderSettings): undefined;
        version: string;
        defaults: FixedHeaderSettings;
    }

    interface Api {
        fixedHeader: FixedHeaderApi;
    }

    interface FixedHeaderApi {
        /**
         * Recalculate the position of the DataTable on the page and adjust the fixed element as appropriate.
         * 
         * @returns The DataTables API for chaining
         */
        adjust(): Api;

        /**
         * Disable the fixed elements
         * 
         * @returns The DataTables API for chaining
         */
        disable(): Api;

        /**
         * Enable / disable the fixed elements
         * 
         * @param enable Flag to indicate if the FixedHeader elements should be enabled or disabled, default true.
         * @returns The DataTables API for chaining
         */
        enable(enable?: boolean): Api;

        /**
         * Simply gets the status of FixedHeader for this table.
         * 
         * @returns true if FixedHeader is enabled on this table. false otherwise.
         */
        enabled(): boolean;

        /**
         * Get the fixed footer's offset.
         * 
         * @returns The current footer offset
         */
        footerOffset(): number;

        /**
         * Set the fixed footer's offset
         * 
         * @param offset The offset to be set
         * @returns DataTables Api for chaining
         */
        footerOffset(offset: number): Api;

        /**
         * Get the fixed header's offset.
         * 
         * @returns The current header offset
         */
        headerOffset(): number;

        /**
         * Set the fixed header's offset
         * 
         * @param offset The offset to be set
         * @returns The DataTables API for chaining
         */
        headerOffset(offset: number): Api;
    }

    /*
     * FixedHeader extension options
     */
    interface FixedHeaderSettings {
        /*
         * Enable / disable fixed footer
         */
        footer?: boolean;

        /*
         * Offset the table's fixed footer
         */
        footerOffset?: number;

        /*
         * Enable / disable fixed header
         */
        header?: boolean;

        /*
         * Offset the table's fixed header
         */
        headerOffset?: number;
    }
}
