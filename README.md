# no-framework-rest-api
Challenge to build a Rest API without any existing framework / libraries.
It should: list users, list tracks, add loved tracks of a user in his playlist.

A "mini modulable MVC framework" is implemented in order to cleanly build the API.

### Usage

* PHP 5.6
* Apache 2 *with mod_rewrite*
* Change `/configuration/app.json`
* Import database structure located in `/sql/challenge_api.sql`
* `chmod 666 /logs/log.txt`

### Routes available
* **GET /** : Index page. Only page in html. Displays all entities.
* **GET /users** : Returns all users.
* **POST /users** : Adds a new user. Requires `name` and `mail` parameters.
* **GET /user/{id}** : Returns a single user.
* **DELETE /user/{id}** : Deletes a user.
* **GET /tracks** : Returns all tracks.
* **POST /tracks** : Adds a new track. Requires `name` and `duration` parameters.
* **GET /track/{id}** : Returns a single track.
* **DELETE /track/{id}** : Deletes a track.
* **GET /user/{id}/loved** : Returns the loved tracks of a user.
* **POST /user/{id}/loved** : Adds a new track in the loved list of a user. Requires `track_id` parameter.
* **DELETE /user/{userId}/loved/{trackId}** : Deletes a track from the loved list of a user.

### Creating a new Model
1. Create the class with its attributes (according to naming conventions)
2. Create the related sql table (according to naming conventions)

### Creating a new page
1. Create a new controller (or use an existing one)
3. Describe the route in the configuration file (`/configuration/routes.json`)


### Naming conventions
* **Controller** : singular CamelCaseController
* **Model** : singular CamelCase
* **Model attribute** : singular camelCase
* **SQL table & column** : singular, each part separated by an underscore
* **SQL many to many link table** : both table names separated by an underscore, ordered alphabetically
