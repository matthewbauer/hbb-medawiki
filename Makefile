MEDIAWIKI = /var/lib/mediawiki

install:
	@sudo cp -fr source/extensions/* $(MEDIAWIKI)/extensions
