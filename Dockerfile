# Image source
FROM ubuntu

# Installation des dependances partgen
RUN apt-get update -yqq && apt-get install -yqq \
	lame \
	fluidsynth \
	lilypond \
	php

# Copie de l'applicatif
RUN mkdir /app
COPY application /app

# Ajout des binaires dans le path
ENV PATH="/app/bin:${PATH}"

# Commande par defaut
CMD bash