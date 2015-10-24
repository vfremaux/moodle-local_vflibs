This local component introduces a change in the way we manage non standard additional
librairies needed by plugins from Valery Fremaux (valery.fremaux@gmail.com), lead
independant Moodle Architect in France.

Till now our plugins where holding all the needed extra libs within a __goodie or __other folder.

This could lead to some issues : 

1. Using many plugins of this family would anyway load several time additional recurrently used librairies

2. Loading several identical libraries from distinct plugins could lead to cache collisions, ou include collisions.

3. Multiple possible routes to plugins (distributed in components, or centralized in /lib) was adding unclear 
inclusion resolution.

In spite of our willing to provide selfstanding plugins, we will now centralize all really generic libraries into 
a single /local/vflibs container, as now installable and manageable as a component regarding to dependancies.

/local/vflibs is to be considered as an extension of standard /lib directory.

Library catalog :
#################

JQplot : for plotting smart html5 graphs

Antiword : Used by search engine

XPdf : Used by search engine

