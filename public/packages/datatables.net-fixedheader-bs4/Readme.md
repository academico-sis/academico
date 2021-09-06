# FixedHeader for DataTables with styling for [Bootstrap4](https://getbootstrap.com/docs/4.6/getting-started/introduction/)

This package contains a built distribution of the [FixedHeader extension](https://datatables.net/extensions/FixedHeader) for [DataTables](https://datatables.net/) with styling for [Bootstrap4](https://getbootstrap.com/docs/4.6/getting-started/introduction/).

When displaying large amounts of data in a table, it can often be useful for the end user to have the column titles always visible. This is particularly true if using DataTables with pagination disabled, or the display length is set to a high value. The FixedHeader extension provides this ability.


## Installation

### Browser

For inclusion of this library using a standard `<script>` tag, rather than using this package, it is recommended that you use the [DataTables download builder](//datatables.net/download) which can create CDN or locally hosted packages for you, will all dependencies satisfied.

### npm

```
npm install datatables.net-fixedheader-bs4
```

ES3 Syntax
```
var $ = require( 'jquery' );
var dt = require( 'datatables.net-fixedheader-bs4' )( window, $ );
```

ES6 Syntax
```
import 'datatables.net-fixedheader-bs4'
```

### bower

```
bower install --save datatables.net-fixedheader-bs4
```



## Documentation

Full documentation and examples for FixedHeader can be found [on the website](https://datatables.net/extensions/fixedheader).


## Bug / Support

Support for DataTables is available through the [DataTables forums](//datatables.net/forums) and [commercial support options](//datatables.net/support) are available.


### Contributing

If you are thinking of contributing code to DataTables, first of all, thank you! All fixes, patches and enhancements to DataTables are very warmly welcomed. This repository is a distribution repo, so patches and issues sent to this repo will not be accepted. Instead, please direct pull requests to the [DataTables/FixedHeader](http://github.com/DataTables/FixedHeader). For issues / bugs, please direct your questions to the [DataTables forums](//datatables.net/forums).


## License

This software is released under the [MIT license](//datatables.net/license). You are free to use, modify and distribute this software, but all copyright information must remain.

