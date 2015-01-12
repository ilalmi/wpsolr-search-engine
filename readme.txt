=== WPSOLR Search Engine ===

Contributors: WPSOLR.COM

Current Version: 1.6

Author:  WPSOLR.COM

Author URI: http://wpsolr.com/ 

Tags: Solr in WordPress, relevance, Solr search, fast search, wpsolr, apache solr, better search, site search, category search, search bar, comment search, faceting, relevant search, custom search, facets, page search, autocomplete, post search, online search, search, spell checking, search integration, did you mean, typeahead, search replacement, suggestions, search results, search by category, multi language, seo, lucene, solr, suggest, apache lucene

Requires at least: 3.7.1

Tested up to: 4.0

Stable tag: 1.6

World class Enterprise Search with facets, autocompletion, suggestions, and optional hosting

== Description ==

The core search is performed by SQL queries directly on the database. So are most of the search plugins.

But SQL is awfully greedy in computer resources, especially when it comes to table joins and wild cards (select * where field like ‘%keyword%’), which are both heavily used by search.
And SQL can’t keep well with natural language: synonyms, language specific plurals, stop-words, …

Fortunately, performance and relevance are features built specifically in full-text search engines.
Using a search engine, you’ll be able to deliver more accurate search to your visitors, for far less computer resources, which means better for cheaper.

The purpose of this plugin is to help you setup your dear Wordpress search to your own Apache Solr server, or to a hosted Apache server.
Apache Solr is the World leading Open source full-text Search Engine. No question on that.

And now, with this plugin, you can get it for free. So, fasten your seat belt, and enjoy the trip.


= Features =

1. Solr server uses indexing for faster search results.
2. Text-analysis to break down search phrases, to search entire phrase or individual words.
3. Advanced faceted search on fields such as tags, categories, author, and page type and custom fields.
4. Highlighted search words in the text.
5. Autocomplete suggestions, correct spelling mistakes
6. Provide a "Did you mean?" query suggestion.
7. Sorting based on time and number of comments.
8. Configuration options allow you to select pages to ignore.
9. Host Solr remotely using gotosolr.
10. Solr configuration made easy.

For more details visit <a href='http://wpsolr.com'>wpsolr.com</a>

For a live demo visit <a href='http://www.gotosolr.com/search-results/?search=solr'>live search page demo</a>. Try the live search (autocompletion), on words like « solr », « cassandra », « security », « indexes », « search ». Notice the facets on the left side with their nice clicked Ajax display, the terms highlighting in the results snippets, the « order by » drop-down list. To test the « did you mean » (suggestions on misspelled words), you can search on « soler » (suggested as « solr »), or « casandra » (suggested as « cassandra »).


== Installation ==

1. Upload the WPSOLR-Search-Engine folder to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the 'WPSOLR' settings page and configure the plugin.
4. Please refer the Installation and User Guide for further reference.


== Changelog ==

= 1.6 =
* Can now index tens of thousands of documents without freezing or timeout

= 1.5 =
* Fixed an issue with older php versions. Should activate and work from PHP 5.2.4 at least.

= 1.4 =
* Fixed warning on search page for self hosted Solr
* Requires to reload yor index with the new config files (solrconfig.xml, schema.xml). Fixed error on autocomplete, and search page with "did you mean" activated, for self hosted Solr 

= 1.3 =
* Speed up search results display.

= 1.2 =
* Speed up autocompletion by 3 times.

= 1.1 =
* Improved error message when Solr port is blocked by hosting provider.
* Bug fix: Solr port used to be 4 digits. Can now be 2 digits and more.

= 1.0 =
* First version.


== Frequently Asked Questions ==

= How do I install and configure Solr? =

Please refer to our detailed <a href='http://wpsolr.com/installation-guide/'>Installation Guide</a>.


= Can I host Solr on my server? =

Yes. But you can also host Solr remotely on gotosolr.


= What version of Solr does the WPSOLR Search Engine plugin need? =

WPSOLR Search Engine plugin is <a href="http://wpsolr.com/releases/#1.0"> compatible with the following Solr versions</a>. But if you were going with a new installation, we would recommend installing Solr version 3.6.x or above.


= Does WPSOLR Search Engine Plugin work with any version of WordPress? =

As of now, the WPSOLR Search Engine Plugin works with WordPress version 3.8 or above.


= Does WPSOLR Search Engine plugin handle custom post type, custom taxonomies and custom fields? =

Yes. The WPSOLR Search Engine plugin provides an option in dashboard, to select custom post types, custom taxonomies and custom fields, which have to be indexed.

 
= Can custom post type, custom taxonomies and custom fields be added faceted search? =

Yes. The WPSOLR Search Engine plugin provides option in dashboard, to select custom post types, custom taxonomies and custom fields, to be added in faceted search.


= Do you offer support? =

You can raise a support question for our plugin from wordpress.org
