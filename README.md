**Note for winodws users:** This project has been created for use on osx and linux systems. It should still work on a windows system, but might require adjustments to make it work.

## How to create new WordPress project using Vagrant

1. Create a customer folder (optional) `mkdir vagrant && cd vagrant` (Replace vagrant with the name of the customer. eg `mkdir DrivDigital && cd DrivDigital` )

2. Clone this repo into that folder: `git clone https://github.com/drivdigital/driv-vagrant .`

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

1. Clone this repo and name it after the customer or project `git clone https://github.com/drivdigital/driv-vagrant drivdigital`.
2. CD into the project `cd drivdigital`

3. Clone the project as "projectname.dev" into the customer directory `git clone git@github.com:path/to/project.git projectname.dev`. Replace projectname with the name of your project. It's important to not use dashes and the directory must end in ".dev".

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

---------------------------------------------------------------------------------

## Database and config files

A config folder will be created in the root of this project. Each `projectname.dev` folders found in the root will be given its own folder within the config directory which contains the apache.conf file and the database along with an optional provision.php for customisation during provisioning. To include these config files in each project repo you can move the projects config dir into the project itself and name it vagrant-config. `mv config/projectname projectname.dev/vagrant-config`.

To import an existing database it should be given the same name as the project and placed in the config dir. Eg `config/projectname.sql`.

When you start vagrant, it's going to import your database exactly how it is.
Which 9/10 is wrong, because you're working locally and don't want to be redirected to the live site.

So to combat this, put a file called `dev.sql` in your `config` folder, then edit it:

If you're running **Wordpress**, replace the **prefix** and **Database name** and place this in the file:

		USE website;
		UPDATE wp_options SET option_value = 'http://website.dev:8080' WHERE option_name in ('home', 'siteurl');
		UPDATE wp_users SET user_pass = MD5('vagrant');

__Bonus round!__ Set all products in a woocommerce shop to active and give them a price:

		update wp_postmeta set meta_value = 'instock' where meta_key = '_stock_status';
		update wp_postmeta set meta_value = 19.99 where meta_key = '_price';
		update wp_postmeta set meta_value = 19.99 where meta_key = '_regular_price';
		update wp_posts set post_status   = 'publish' where post_type = 'product';

...and for __Magento__, remember to change the same detial for URL and database name:

		USE website;
		UPDATE core_config_data SET value = "http://website.dev:8080/" where path like 'web/%secure/base_url';
		UPDATE admin_user SET password    = MD5('vagrant');
		UPDATE core_config_data SET value = '0' WHERE path = 'admin/security/use_form_key';
		UPDATE core_config_data SET value = '0' WHERE path = 'google/analytics/active';

---

## Aliases

It's nice to have aliases

- Start the project, regardless of if it's shutdown or halted: `alias vup='vagrant up --provision'`
- Check if any other boxes are running: `alias vgs='vagrant global-status'`
- Reimport the `dev.sql` into the database: `alias devdb='vagrant ssh -c "mysql -u root < /vagrant/config/dev.sql"'`
- Save the current database to the config folder: `alias savedb='vagrant ssh -c "cd /vagrant && ./save-db"'`
