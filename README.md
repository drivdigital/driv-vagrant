## How to create new WordPress project using Vagrant

1. Create a customer folder (optional) `mkdir vagrant && cd vagrant`

2. Clone Vagrant into this folder: `git clone https://github.com/drivdigital/driv-vagrant`

3. Create a new subfolder for vagrant folder, this folder should have the same name as a dev URL (eg website.dev) `mkdir website.dev`

4. Make sure /etc/hosts is writeable `sudo chmod 777 /etc/hosts`
4.1 (Alternative)  Enter the host file via terminal command: `sudo vim /etc/hosts`
	- Entering the Insert mode, command: `i`
	- Add dev URL for customer list that appears `127.0.0.1      website.dev`
	- Exiting the insert-mode by pressing `ESC`
	- Save and exit the host file, command: `:wq!`

5. Start Vagrant, command: `vagrant up`

6. Go to browser and visit the URL with the dev port 8080: `http://website.dev:8080`

7. Install WordPress site as usual.

8. At the end of the working day, save the database if you want to take care of this, the command: `vagrant ssh -c "cd /vagrant && ./save-db"`

9. To stop the vagrant, use `vagrant halt`. If you don't stop it, the next time your use `vagrant up` you should provision with `vagrant up --provision` 

> **Note:** If using `wp-cli` to manage this wordpress install, you must do so by running `vagrant ssh`, `cd website.dev` then run `wp` commands 

---------------------------------------------------------------------------------


## How to create a WordPress project from an existing project using Vagrant

1. Create a customer folder (optional) `mkdir vagrant && cd vagrant`

2. Clone Vagrant into this folder: `git clone https://github.com/drivdigital/driv-vagrant`

3. Clone into the Vagrant folder, remember to rename the folder to the same name as a dev URL (eg website.dev)

4. Get `wp-config.php` and put it in the root of newly created customer folder or create new.

5. Open `wp-config` and give the correct `db-name`, Change `db_user` to `root` and set `db_password` as empty.

6. Create a subfolder for vagrant folder and name it `config`.
	- Copy database file in this folder
	- Make sure the database file's name is the same as the vagrant URL, eg: `website.sql`

7. Change Site URL and home to correct dev URL by creating `dev.sql` file. Enter update statement in dev.sql file URL. For example:

		USE database_name;
		UPDATE wp_options SET option_value = 'http://website_name:8080' WHERE option_name in ('home', 'siteurl');
		UPDATE wp_users SET user_pass = MD5( 'vagrant' );


8. Make sure /etc/hosts is writeable `sudo chmod 777 /etc/hosts`
8.1 (Alternative) Enter the host file via terminal command: `sudo vim /etc/hosts`
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
