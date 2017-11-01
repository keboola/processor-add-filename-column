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
docker-compose run dev composer install
```

Run the test suite using this command:

```
docker-compose run tests
```
 
# Integration
 - Build is started after push on [Travis CI](https://travis-ci.org/keboola/processor-add-filename-column)
 - [Build steps](https://github.com/keboola/processor-add-filename-column/blob/master/.travis.yml)
   - build image
   - execute tests against new image
   - publish image to ECR if release is tagged
   
# Usage
It supports optional parameters:

- `column_name ` -- Name of the column, defaults to `filename`

# Prerequisites

All CSV files must

- not have headers
- have a manifest file attached with `columns`, `delimiter` and `enclosure` properties

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
    	"column_name": "myFileNameColumn"
	}
}

```

