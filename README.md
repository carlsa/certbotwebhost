# Certbot in docker with HTTP-01 at a remote hosting provider
This project is meant to create a [Let's encypt](https://letsencrypt.org/) certificate for a domain that is hosted by a hosting provider. This is achieved by performing a HTTP-01 challenge in a [Certbot](https://certbot.eff.org/) Docker container with [manual hooks](https://eff-certbot.readthedocs.io/en/stable/using.html#pre-and-post-validation-hooks) that forward the challenge to your web host.

The purpose is to get certificates that can be used on internal servers that aren't exposed to the internet for domains that are hosted by a hosting provider.

Note that this is only useful if you are able to resolve the domain to a different IP on your internal network than on public DNS servers.

If you are able to complete a DNS challenge it is probably always to prefer over this solution.


## How it works
We prove ownership of a domain to Let's encrypt by making a token appear in the `/.well-known/acme-challenge/` folder on the hosting providers webserver.

This is how the scripts work:
* Certbot is started in a docker container with the command line parameters from [cli.ini](https://eff-certbot.readthedocs.io/en/stable/using.html#configuration-file). This file contain the domain name, contact email etc.
* Certbot will initiate a manual HTTP-01 challenge where a token is passed to the authenticator.sh hook script. 
* The hook script will in turn call a PHP script at the hosting provider with the token.
* The PHP script verifies that the token is coming from your system by comparing a SHA256 hash of the token and a shared secret that is present in the PHP script and in the docker compose file.
* The PHP script creates the `/.well-known/acme-challenge/` file at the web host so it can be verified by Let's encrypt.


# Setup
## Hosting provider
Test that your hosting provider will serve a file from this path `http://your-domain.com/.well-known/acme-challenge/`. Some hosting providers block this to prevent you from generating your own certificates.

Copy web_host_files/http01.php to the root of the domain and make sure it is is executable. If you navigate to http://your-domain.com/http01.php you should see `Error. Missing or invalid parameters.`

Change the hash secret in the http01.php file.


## Docker
Change the hash secret in `docker-compose.yml` to the same secret as the PHP file.

Change the [container tag](https://hub.docker.com/r/certbot/certbot/tags) in `docker-compose.yml` to match your architecture, e.g. `certbot/certbot:arm32v6-latest` or `certbot/certbot:latest`. 

Change the domain and other details in `docker_compose_files\data\cli.ini`.

Start the docker compose file with `docker compose up`. 


# Hosting providers that block the HTTP-01 challenge
* one.com

# Todo
* Implement the cleanup script.