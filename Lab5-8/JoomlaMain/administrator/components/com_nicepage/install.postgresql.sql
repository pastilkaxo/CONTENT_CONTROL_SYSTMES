CREATE TABLE IF NOT EXISTS #__nicepage_sections (
    id serial NOT NULL,
    page_id integer DEFAULT '0' NOT NULL,
    props text DEFAULT '',
    preview_props text DEFAULT '',
    autosave_props text DEFAULT '',
    templateKey character varying(255) DEFAULT '' NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS #__nicepage_params (
    id serial NOT NULL,
    name character varying(50) DEFAULT '' NOT NULL,
    params text NOT NULL,
    PRIMARY KEY (id)
);