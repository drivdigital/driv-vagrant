USE %SLUG;
INSERT INTO `admin_user` (firstname,lastname,email,username,password,created,lognum,reload_acl_flag,is_active,rp_token_created_at)
VALUES ('Vagrant','Admin','vagrant@vagrant.vagrant','vagrant',MD5('vagrant'),NOW(),0,0,1,NOW());

INSERT INTO `admin_role` (parent_id,tree_level,sort_order,role_type,user_id,role_name)
VALUES (1,2,0,'U',(SELECT user_id FROM admin_user WHERE username = 'vagrant'),'Vagrant');
