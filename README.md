## How to create new WordPress project using Vagrant

1. Create a customer folder (optional) `mkdir vagrant && cd vagrant`

2. Clone Vagrant into this folder: `git clone https://github.com/drivdigital/driv-vagrant`

3. Create a new subfolder for vagrant folder, this folder should have the same name as a dev URL (eg kundenavn.dev) `mkdir website.dev`

4. Enter the host file via terminal command: `sudo vim /etc/hosts`
	- Entering the Insert mode, command: `i`
	- Add dev URL for customer list that appears `127.0.0.1      website.dev`
	- Exiting the insert-mode by pressing `ESC`
	- Save and exit the host file, command: `:wq!`

5. Start Vagrant, command: `vagrant up`

6. Go to browser and visit the URL with the dev port 8080: `http://website.dev:8080`

7. Install WordPress site as usual.

8. At the end of the working day, save the database if you want to take care of this, the command: `vagrant ssh -c "cd /vagrant && ./save-db"`

9. To stop the vagrant, use `vagrant halt`. If you don't stop it, the next time your use `vagrant up` you should provision with `vagrant up --provision` 

> **Note:** If using `wp-cli` to manage this wordpress install, you must do so by running `vagrant ssh`, `cd your folder.dev` then run `wp` commands 

---------------------------------------------------------------------------------


## How to create a WordPress project from an existing project using Vagrant

1. Create a customer folder (optional) `mkdir vagrant && cd vagrant`

2. Clone Vagrant into this folder: `git clone https://github.com/drivdigital/driv-vagrant`

3. Clone Repository from Bitbucket into Vagrant folder, remember to rename the folder to the same name as a dev URL (eg kundenavn.dev)

4. Get `wp-config.php` and put it in the root of newly created customer folder or create new.

5. Open `wp-config` and give the correct `db-name`, Change `db_user` to `root` and set `db_password` as empty.

6. Create a subfolder for vagrant folder and name it `database`.
	- Copy database file in this folder
	- Make sure the database file's name is the same as the vagrant URL, eg: `website.db`

7. Change Site URL and home to correct dev URL by creating `dev.sql` file. Enter update statement in dev.sql file URL. For example:

		USE database_name;
		UPDATE ms_options SET option_value = 'http://website_name:8080' WHERE option_name in ('home', 'siteurl');
		UPDATE ms_users SET user_pass = MD5( 'vagrant' );


8. Enter the host file via terminal command: `sudo vim /etc/hosts`
	- Entering the Insert mode, command: `i`
	- Add dev URL for customer list that appears `127.0.0.1      website.dev`
	- Exiting the insert-mode by pressing `ESC`
	- Save and exit the host file, command: `:wq!`


9. Start Vagrant, command: `vagrant up`

10. Go to browser and visit the URL with the dev port 8080: `http://website.dev:8080`

11. Visit `wp-admin -> settings -> permalinks` to flush permalinks

12. At the end of the working day, save the database if you want to take care of this, the command: `vagrant ssh -c "cd /vagrant && ./save-db"`

13. To stop the vagrant, use `vagrant halt`. If you don't stop it, the next time your use `vagrant up` you should provision with `vagrant up --provision` 

> **Note:** If using `wp-cli` to manage this wordpress install, you must do so by running `vagrant ssh`, `cd your folder.dev` then run `wp` commands 


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
