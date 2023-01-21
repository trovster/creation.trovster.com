# Creation Website

This was the final website for my first employer.

Built using PHP.

## Installation

You can setup the site using [Docker](https://www.docker.com);

    $ git clone https://github.com/trovster/creation.trovster.com.git creation
    $ cd creation
    $ npm install
    $ npm run start -- --build

To stop the Docker container run the following;

    $ npm run stop

## Deployment

To deploy the site via GitHub pages, run the following;

    $ npm run build
    $ npm run deploy
