# deims-knz-custom
DEIMS customizations to serve the Konza Prairie LTER needs

Repo name : deims-knz-custom

This repo build on the DEIMS repo, and contains a series of enhancements 
and tweaks over DEIMS, specifically crafted for the Konza LTER.

Tweaks may include modules, some css script for your DEIMS Theme and a set of features, 
(like modules) to extend existing content types and/or create new ones.  

There are a number of custom modules to migrate the Konza assets into DEIMS. 
In general, for the Konza there are two main sources of data and content.  
The current system is powered by a MSSQL database, some ASP scripts to retrieve
and scaffold the database, and then, being an LTER site, most of the dataset
content is well expressed in an EML (XML) subset (flavor). There is also a 
great geo (GIS) based interface to display the data holdings.  Most data are
stored in the filesystem, in Comma delimited files, and like other LTER sites,
there is a scholarly publication list to manage, news, galleries.  

The preliminary approach consisted of migrating the files (images, CSVs, pdfs)
with enough context to be tied in to their related content resources (news tidbits,
events, pages, projects or datasets.). Along with the files, and from the MSSQL
database, we use 5 tables to populate DEIMS.

Personnel directory, (person)
Pages (basic pages) 
News (articles), 
Events (more articles qualified by controlled terms from the Section vocabulary)
Related Projects.

Also, from the EML work (the MSSQL counterpart is not as rich and up-to-date), we
populate DEIMS
Data-sources
Data Files
Data-set Metadata.

THe locations related for the dataset are in the works.  Presumably, these will
come either from a well crafted CSV, or a shapefile to WKT.  THe relationships
to the dataset holdings may have to be constructed after migration, unless we
figure the connectors within the MSSQL. Still discovery-in-progress.

We use the migrate framework and code that extends by Acquia'sMike Ryan and other
great drupal contributors.

Here we expand DEIMS adding fields to existing content types and 
vocabularies to accomodate the Konza LTER specific configurations.

## Instructions ##

Clone 
* `git clone --branch 7.x-1.x git@github.com:lter/deims-knz-custom.git` 
into a place of your choice (current directory, Desktop, etc)

Or download this module from: 

* `https://github.com/lter/deims-knz-custom/archive/7.x-1.x.zip`
Extract the contents in a local directory, you will copy the parts inside to different
places in your DEIMS install, as we explain now.

Once you have the repository locally, create a folder named _modules_ under your
DEIMS root _sites/default_ (unless you have already made it)

Under the _sites/default/modules_, place the modules you see in the repo, and 
enable them afterwards, for example the folder and module 'eml_2_deims'.

Also, we need new content types for the konza prairie LTER. In the local github download, you also
see a _features_ folder.  Inside, there are at least two folders that begin with the word
_knz_, (for slides, etc).  Move these folders to the modules folder,
like this:

* `mv deims-knz-custom/features DEIMSROOT/sites/default/modules/`

We are also extending some existing DEIMS content types. Lets override the existing ones.
We are replacing the original DEIMS profile features with the ones we downloaded, which
are inside the _features_ folder of this repo, and being with the word _deims_.

* `mv deims-knz-custom/features/deims_research_project profiles/deims/modules/features`
similar move command (you can use cp -fr) for the deims Research Site feature.

Using your browser visit your modules admin page, something like this URL 
`http://new-konza-url/admin/modules`

In that page, find and enable the new _knz_ modules. Mark the checkbox to 
the side, and hit _Save_. 

If you prefer speed, use drush to enable the module(s). For example:
* `drush en eml_2_deims`

Some other components are not installed by default using the method described below under the
_instructions_ section. Here are additional instructions for these other components.

### The Deims Theme CSS ###
Inside the _miscellaneous_ tree needs to be moved to the corresponding place in
your DEIMS install to warranty your slideshow functions correctly.  The Slideshow
will require you to install two additional modules, the 'views_slideshow' and the
'flexslider_views_slideshow'.


### Migration Overview ###
At least three sources.  
One is the EMLs (about 100).  
Second, the KNZMETA database, and specifically, 5 tables with content about personnel, pageContents (stories, static HTML), newsItems-newsTicker, relatedProjects.
Third is the EndNote publications.

FILES, IMAGES?
###  For the custom migrations to work ###
This applies to the DEIMS D6 migration and customizations. You need 
to have a _settings.local.php_ file in the same place you have the _settings.php_ file.
(that is, _sites/default_).

The _settings.local.php_ file has a database connector to the DEIMS D6 database. Here is
an example

* `<?php `
* ` $databases['drupal6']['default'] = array(`
* `  'database' => 'deims-drupal6-knz',`
* `  'username' => 'deims-mysql-user',`
* `  'password' => 'deims-mysql-user-password',`
* `  'host' => 'localhost',`
* `  'port' => '',`
* `  'driver' => 'mysql',`
* `  'prefix' => '',`
* ` );`


For more documentation on this customizations visit the DEIMS project pages help, and blog posts.

For DEIMS help, visit the DEIMS project page at drupal.org, read the papers in databits.lternet.edu
or contact us.

Other notes on the migration: The biblio nodes (the citations, references, publication list) are imported 
using the export/import built-in the module.  Export the latest version in EndNote Tagged, and re-import that file.  
You need to connect publications to authors and the likes. 

We now associate Authors with People using the new view, that solves part of it. 

The slideshow will require you to download two additional modules (views-slideshow and flexslider-views-
slideshow). In addition, visit the "blocks" admin page, and mode the Views-slideshow block to the header
area, perhaps configure to show only in the _frontpage_ .  

