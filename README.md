# MediaWiki Sketchfab Tag Extension

MediaWiki Tag Extension for embedding Sketchfab 3D models.

## Installation

[Download the source code](https://github.com/follesoe/mediawiki-sketchfab-tag-extension/archive/master.zip) and copy the `Sketchfab` directory to your MediaWiki `extension` directory.

## Usage

Sketchfab 3D models can be embedded like this:

```xml
<!-- Object id inside tag -->
<sketchfab>198eff4432cc483ca33b2a648d895b1e</sketchfab>

<!-- Object id extracted from Sketchfab URL -->
<sketchfab>https://sketchfab.com/models/198eff4432cc483ca33b2a648d895b1e</sketchfab>

<!-- Object id as attribute -->
<sketchfab sfid="198eff4432cc483ca33b2a648d895b1e" />

<!-- Set custom width and height -->
<sketchfab sfid="198eff4432cc483ca33b2a648d895b1e" width="320" height="240" />
```

You can also use attributes to set [Sketchfab spesific attributes](https://help.sketchfab.com/hc/en-us/articles/203509907-Embed-Models):

```xml
<sketchfab
  sfid="198eff4432cc483ca33b2a648d895b1e"
  autospin="0.2"
  autostart="1"
  preload="1" />
```


## Development Environment
For easy testing and development the official [MediaWiki Docker Image](https://hub.docker.com/_/mediawiki/) is used. Run the `setup.sh` script, or the following command to configure your development for first time use:

```shell
docker run --rm -p 8080:80 -it -v $PWD/data:/var/www/data mediawiki
```

This will mount the local `/data` directory as a volume at `/var/www/data` inside the container. During setup select SQLite as the database platform, and use the default location of `/var/www/data`. This will create the database outside the container.

Download the `LocalSettings.php` file at the end of configuration, and store it in the root of this repository. Both the `LocalSettings.php` file and `/data` directory are ignored by git.

Close the container, and launch it using the `run.sh` script, or the following command:

```shell
docker run \
  --rm \
  -it \
  -p 8080:80 \
  -v $PWD/data:/var/www/data \
  -v $PWD/LocalSettings.php:/var/www/html/LocalSettings.php \
  -v $PWD/Sketchfab:/var/www/html/extensions/Sketchfab \
  --name devwiki \
  mediawiki
```

This will launch a container mapping in your newly created SQLite database and `LocalSettings.php` configuration file.