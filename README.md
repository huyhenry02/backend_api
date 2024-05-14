### Work with docker
- To start working environment, please run `docker-compose up -d`
- To access laravel app, access address `http://localhost:8099`
>docker exec -it wishcare_api chmod -R 775 storage
- To access database, please use credentials following:
> Hostname: wishcare_postgres
>
> Port: 5432
>
> Username: root
>
> Password: ant.tech.asia


### API documentation 
- To access swagger, access address `http://localhost:8099/api/documentation`

### RBAC
- To do something relate roles, remove middleware 'check.access' in api/role temporarily
- Seed permissions: add new line into database/seeders/SeedFiles/permissions.csv
- Add description in file permissions lang/{lang_name}/permissions. Follow the example. key is module name
- Note: permission_name is unique
### laravel-medialibrary

1. Implement HasMedia in the Module
First, you need to implement the HasMedia interface in your module to manage media related to the module.


    class YourModule extends Model implements HasMedia
    {
    use InteractsWithMedia;

        // ... Other module declarations and methods ...
    }
2. Implement the registerMediaCollections Method
   In your module, implement the registerMediaCollections method to define and register the media collections that the module will use.


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(RawMediaUploadCollectionConstants::COLLECTION1);

        // Register other media collections (if any)
    }

    // ... Other module declarations and methods ...
3. Update the RawMediaUploadCollectionConstants
   For consistency in managing media collections, update the RawMediaUploadCollectionConstants enum with the collection names you have registered.


    class RawMediaUploadCollectionConstants
    {
    const COLLECTION1 = 'collection1';
    const COLLECTION2 = 'collection2';
    // ... Register other media collections here ...
    }
