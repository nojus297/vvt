### Eficiency:
 - batch inserting / deleting in Loaders classe instead of single item query
 query to the database
 - single insert query in departuresLoader (Not one per route_stop)

### Code quality:
 - The scripts folder should probably be divided to subfolders
 - Rename StopLoader2
 - Some general trafi loading class
 - Make use of the config file vvt.php

### Fixes:
 - Exclude (tik x-xx) routes probably

### New features:
 - script to calculate degree in route_stops
 - script to fill the actual_time columns
