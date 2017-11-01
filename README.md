# processor-add-filename-column

[![Build Status](https://travis-ci.org/keboola/processor-add-filename-column.svg?branch=master)](https://travis-ci.org/keboola/processor-add-filename-column)

Takes all CSV files in `/data/in/tables` (except `.manifest` files) and appends column with the filename (column name optional) and stores the files to `/data/out/tables`. 

 - Does not ignores directory structure (for sliced files).
 - Ignores manifests `columns` attribute.
 - Can add column header.
 
## Development
 
Clone this repository and init the workspace with following command:

```
git clone https://github.com/keboola/processor-add-filename-column
cd processor-add-filename-column
docker-compose build
```

Run the test suite using this command:

```
./tests/run.sh
```
 
# Integration
 - Build is started after push on [Travis CI](https://travis-ci.org/keboola/processor-add-filename-column)
 - [Build steps](https://github.com/keboola/processor-add-filename-column/blob/master/.travis.yml)
   - build image
   - execute tests against new image
   - publish image to ECR if release is tagged
   
# Usage
It supports optional parameters:

- `column_name ` -- Name of the column
- `delimiter` -- CSV delimiter, defaults to `,`
- `enclosure` -- CSV enclosure, defaults to `"`
- `escaped_by` -- escape character for the enclosure, defaults to empty

## CSV headers, `column_name` option and sliced files

The `column_name` option will **update the CSV header** only if

- it is not a sliced file and 
- does not have a manifest or has a manifest which does not specify the `columns` property

It will **update the manifest file** only if there already is a manifest file containing the `columns` property.
  
In all other cases the manifest file and CSV headers will remain untouched and only the values will be added.

If the `column_name` option is not specified, the manifest or CSV header will not be modified.

## Sample configurations

Default parameters:

```
{  
    "definition": {
        "component": "keboola.processor-add-filename-column"
    }
}
```

Add column name header:

```
{
    "definition": {
        "component": "keboola.processor-add-filename-column"
    },
    "parameters": {
    	"column_name": "filename"
	}
}

```

Use tab as delimiter and single quote as enclosure:

```
{
    "definition": {
        "component": "keboola.processor-add-filename-column"
    },
    "parameters": {
    	"delimiter": "\t",
    	"enclosure": "'"
	}
}
```
