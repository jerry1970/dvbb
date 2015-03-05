-- Adminer 4.2.0 SQLite 3 dump

DROP TABLE IF EXISTS "avatar";
CREATE TABLE "avatar" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "user_id" integer NULL,
  "data" blob NULL,
  "type" text NULL
);


DROP TABLE IF EXISTS "category";
CREATE TABLE "category" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "title" text NULL,
  "sort" integer NULL
);


DROP TABLE IF EXISTS "forum";
CREATE TABLE "forum" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "parent_id" integer NULL,
  "category_id" integer NULL,
  "title" text NULL,
  "description" text NULL,
  "sort" integer NULL
);


DROP TABLE IF EXISTS "password";
CREATE TABLE "password" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "user_id" integer NULL,
  "password" text NULL
);


DROP TABLE IF EXISTS "post";
CREATE TABLE "post" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "forum_id" integer NULL,
  "parent_id" integer NULL,
  "user_id" integer NULL,
  "updated_user_id" integer NULL,
  "title" text NULL,
  "body" text NULL,
  "closed" integer NULL,
  "sticky" integer NULL,
  "created_at" text NULL,
  "updated_at" text NULL,
  "last_post_at" text NULL
);


DROP TABLE IF EXISTS "rights";
CREATE TABLE "rights" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "parent_type" text NULL,
  "parent_id" text NULL,
  "child_type" text NULL,
  "child_id" integer NULL,
  "create" integer NULL,
  "read" integer NULL,
  "update" integer NULL,
  "delete" integer NULL,
  "mod" integer NULL,
  "admin" integer NULL
);

INSERT INTO "rights" ("id", "parent_type", "parent_id", "child_type", "child_id", "create", "read", "update", "delete", "mod", "admin") VALUES (1,	'default',	'',	'group',	1,	3,	3,	3,	3,	3,	3);
INSERT INTO "rights" ("id", "parent_type", "parent_id", "child_type", "child_id", "create", "read", "update", "delete", "mod", "admin") VALUES (2,	'default',	'',	'group',	2,	'0',	1,	'0',	'0',	'0',	'0');

DROP TABLE IF EXISTS "setting";
CREATE TABLE "setting" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "user_id" integer NULL,
  "key" text NULL,
  "value" text NULL
);


DROP TABLE IF EXISTS "token";
CREATE TABLE "token" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "user_id" integer NULL,
  "token" text NULL,
  "context" text NULL
);


DROP TABLE IF EXISTS "unread";
CREATE TABLE "unread" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "user_id" integer NULL,
  "forum_id" integer NULL,
  "post_id" integer NULL,
  "created_at" integer NULL
);


DROP TABLE IF EXISTS "user";
CREATE TABLE "user" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "group_id" integer NULL,
  "sub_group_id" integer NULL,
  "username" text NULL,
  "email" text NULL,
  "validated_at" text NULL,
  "created_at" text NULL,
  "updated_at" text NULL
);


DROP TABLE IF EXISTS "user_group";
CREATE TABLE "user_group" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "title" text NOT NULL
);

INSERT INTO "user_group" ("id", "title") VALUES (1,	'Admin');
INSERT INTO "user_group" ("id", "title") VALUES (2,	'Guest');

-- 
