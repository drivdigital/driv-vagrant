# Hvordan opprette nytt WordPress-prosjekt ved bruk av Vagrant

1. Lag en kundemappe (valgfritt)

2. Klon Vagrant via https fra side https://github.com/drivdigital/driv-vagrant (denne skal ligge i den nyopprettede kundemappen)

3. Lag en ny undermappe for vagrant-mappen, denne mappen skal ha samme navn som lokal URL (eks: kundenavn.dev)

4. Gå inn i host-fil via terminal, kommando: sudo vim /etc/hosts
  4.1 Gå inn i insert-mode, kommando: i
  4.2 Legg til lokal URL for kunde i listen som vises
  4.3 Gå ut av insert-mode ved å trykke ESC
  4.4 Lagre og gå ut av host-filen, kommando: :wq!

5. Gå inn i Vagrant-mappen og start Vagrant, kommando: vagrant up

6. Gå i browser og besøk URL, legg til port 8080 i adresselinjen, eks: kundenavn.dev:8080/

7. Installer WordPress-siten på vanlig måte.

8. Når du er ferdig med prosjektet, lagre databasen dersom du ønsker å ta vare på denne, kommando: vagrant ssh -c "cd /vagrant && ./save-db"


---------------------------------------------------------------------------------


# Hvordan opprette et WordPress-prosjekt fra et eksisterende prosjekt ved bruk av Vagrant

1. Lag en kundemappe (valgfritt)

2. Klon Vagrant via https fra https://github.com/drivdigital/driv-vagrant (inn i kundemappen)

3. Klon Repository fra Bitbucket inn i Vagrant-mappen, husk å rename mappen til samme navn som lokal URL (eks: kundenavn.dev)

4. Hent wp-config.php og legg den i root for nyopprettet kundemappe eller lag ny.

5. Åpne wp-config og gi korrekt db-navn, db-bruker: root og sett db-passord tomt.

6. Opprett en undermappe for vagrant-mappen og kall den "database"
  6.1 Legg databasefilen i denne mappen

7. Endre Site URL og home til korrekt lokal URL ved å lage dev.sql fil. Skriv inn update statement i dev.sql-filen for URL. F.eks:
  update ##databasenavn##.##prefiks##_options set option_value = 'http://##sitenavn##.dev:8080/' where option_name in ('siteurl', 'home');

8. Gå inn i host-fil via terminal, kommando: sudo vim /etc/hosts
  4.1 Gå inn i insert-mode, kommando: i
  4.2 Legg til lokal URL for kunde i listen som vises
  4.3 Gå ut av insert-mode ved å trykke ESC
  4.4 Lagre og gå ut av host-filen, kommando: :wq!

9. Start virtuell maskin, kommando: vagrant up

10. Gå i browser og besøk URL, legg til port 8080 i adresselinjen, eks: kundenavn.dev:8080/

11. Besøk wp-admin -> settings -> permalinks (så permalinks blir riktig).

12. Når du er ferdig med prosjektet, lagre databasen dersom du ønsker å ta vare på denne, kommando: vagrant ssh -c "cd /vagrant && ./save-db"
