# Test Zelty

An API built with API Platform on a Symfony 6 with php 8 application.

The database is built with MySQL8.

You need **docker** installed on yout computer to run the project.

### Install the project
run command:

    ./install

### Load the fixtures
run commands:

    docker-compose run --rm php8-service php bin/console d:f:l --no-interaction
	docker-compose run --rm php8-service php bin/console hautelook:fixtures:load --no-interaction

### Load the tests
run commands:

    docker-compose run --rm php8-service vendor/bin/phpunit

## How to use the API
You have collect the API Token I voluntary left in the code to be able to test the API more easier. The API Token is located at:

    http://localhost:8888

    root / secret

    Zelty > api_token > token

Since you collected the token, you have to use **Postman** and add in your request, 2 keys in **Headers**:

    Accept => application/json
    x-api-token => YOUR_TOKEN

Now you are able to make some tests on the API.

## Some examples

    GET http://localhost:8080/api/posts
    or
    GET http://localhost:8080/api/authors
    
With these urls you are able to get any id object on the tables, you can now test with a create

    POST http://localhost:8080/api/posts

    BODY / JSON

    {
        "title": "Mon super article",
        "content": "Le contenu de mon super article",
        "status": "deleted",
        "author": "/api/authors/1"
    }

You have the possibility to create a new author when you are creating a new post:

    {
        "title": "Mon super article",
        "content": "Le contenu de mon super article",
        "status": "deleted",
        "author": {
            "name": "Jeanmich"
        }
    }

For the property **status**, you have three choises

    ['published', 'draft', 'deleted']

If you push on published status, the dateTime is automatically provided.

If deleted, dateTime is null.

In draft, if you don't provide any dateTime, it is provided automatically.

Otherwise you can provide a future dateTime with a draft status:

    {
        "title": "Mon super article",
        "content": "Le contenu de mon super article",
        "publishedAt": "2023-06-17T22:01:26+00:00",
        "status": "draft",
        "author": "/api/authors/1"
    }

## Others
If you get an issue with fixtures, rerun the command.