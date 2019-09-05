### Eficiency:
 - batch inserting / deleting in Loaders classe instead of single item query
 query to the database
 - single insert query in departuresLoader (Not one per route_stop)

### Code quality:
 - The scripts folder should probably be divided to subfolders
 - Some general trafi loading class
 - Class to parse objects from trafi to custom for easier maintenance

### Fixes:
 - Exclude (tik x-xx) routes probably
 - If not then make sure to replace space with '+'

### New features:
 - script to calculate degree in route_stops
 - script to fill the actual_time columns
