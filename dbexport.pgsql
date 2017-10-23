--
-- PostgreSQL database dump
--

-- Dumped from database version 9.6.5
-- Dumped by pg_dump version 9.6.5

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: postgres; Type: COMMENT; Schema: -; Owner: mattwebley
--

COMMENT ON DATABASE postgres IS 'default administrative connection database';


--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: answers; Type: TABLE; Schema: public; Owner: mattwebley
--

CREATE TABLE answers (
    question_id bigint NOT NULL,
    answer text,
    user_id bigint,
    id bigint NOT NULL,
    site_id character varying
);


ALTER TABLE answers OWNER TO mattwebley;

--
-- Name: answers_id_seq; Type: SEQUENCE; Schema: public; Owner: mattwebley
--

CREATE SEQUENCE answers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE answers_id_seq OWNER TO mattwebley;

--
-- Name: answers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mattwebley
--

ALTER SEQUENCE answers_id_seq OWNED BY answers.id;


--
-- Name: nameids; Type: TABLE; Schema: public; Owner: mattwebley
--

CREATE TABLE nameids (
    question_set_name character varying,
    question_set_id smallint NOT NULL,
    site_id bigint,
    id bigint NOT NULL
);


ALTER TABLE nameids OWNER TO mattwebley;

--
-- Name: nameids_id_seq; Type: SEQUENCE; Schema: public; Owner: mattwebley
--

CREATE SEQUENCE nameids_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nameids_id_seq OWNER TO mattwebley;

--
-- Name: nameids_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mattwebley
--

ALTER SEQUENCE nameids_id_seq OWNED BY nameids.id;


--
-- Name: nameids_question_set_id_seq; Type: SEQUENCE; Schema: public; Owner: mattwebley
--

CREATE SEQUENCE nameids_question_set_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nameids_question_set_id_seq OWNER TO mattwebley;

--
-- Name: nameids_question_set_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mattwebley
--

ALTER SEQUENCE nameids_question_set_id_seq OWNED BY nameids.question_set_id;


--
-- Name: options; Type: TABLE; Schema: public; Owner: mattwebley
--

CREATE TABLE options (
    answer_option character varying,
    question_id bigint NOT NULL,
    id smallint NOT NULL,
    site_id bigint
);


ALTER TABLE options OWNER TO mattwebley;

--
-- Name: options_id_seq; Type: SEQUENCE; Schema: public; Owner: mattwebley
--

CREATE SEQUENCE options_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE options_id_seq OWNER TO mattwebley;

--
-- Name: options_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mattwebley
--

ALTER SEQUENCE options_id_seq OWNED BY options.id;


--
-- Name: questions; Type: TABLE; Schema: public; Owner: mattwebley
--

CREATE TABLE questions (
    id bigint NOT NULL,
    question character varying,
    input_type character varying,
    "position" integer DEFAULT 0 NOT NULL,
    question_set integer NOT NULL,
    site_id bigint
);


ALTER TABLE questions OWNER TO mattwebley;

--
-- Name: questions_id_seq; Type: SEQUENCE; Schema: public; Owner: mattwebley
--

CREATE SEQUENCE questions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE questions_id_seq OWNER TO mattwebley;

--
-- Name: questions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mattwebley
--

ALTER SEQUENCE questions_id_seq OWNED BY questions.id;


--
-- Name: questions_question_set_seq; Type: SEQUENCE; Schema: public; Owner: mattwebley
--

CREATE SEQUENCE questions_question_set_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE questions_question_set_seq OWNER TO mattwebley;

--
-- Name: questions_question_set_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mattwebley
--

ALTER SEQUENCE questions_question_set_seq OWNED BY questions.question_set;


--
-- Name: answers id; Type: DEFAULT; Schema: public; Owner: mattwebley
--

ALTER TABLE ONLY answers ALTER COLUMN id SET DEFAULT nextval('answers_id_seq'::regclass);


--
-- Name: nameids question_set_id; Type: DEFAULT; Schema: public; Owner: mattwebley
--

ALTER TABLE ONLY nameids ALTER COLUMN question_set_id SET DEFAULT nextval('nameids_question_set_id_seq'::regclass);


--
-- Name: nameids id; Type: DEFAULT; Schema: public; Owner: mattwebley
--

ALTER TABLE ONLY nameids ALTER COLUMN id SET DEFAULT nextval('nameids_id_seq'::regclass);


--
-- Name: options id; Type: DEFAULT; Schema: public; Owner: mattwebley
--

ALTER TABLE ONLY options ALTER COLUMN id SET DEFAULT nextval('options_id_seq'::regclass);


--
-- Name: questions id; Type: DEFAULT; Schema: public; Owner: mattwebley
--

ALTER TABLE ONLY questions ALTER COLUMN id SET DEFAULT nextval('questions_id_seq'::regclass);


--
-- Data for Name: answers; Type: TABLE DATA; Schema: public; Owner: mattwebley
--

COPY answers (question_id, answer, user_id, id, site_id) FROM stdin;
417	John	4	226	\N
424	John Winston Lennon	4	227	\N
419	Amazing, Cunning, Sexual	4	228	\N
418	I play the fool	4	229	\N
416	Orange	4	230	\N
423	dont_have_a_dog	4	231	\N
417	Paul	3	232	\N
424	Paul MaCartney	3	233	\N
419	Amazing, Boring	3	234	\N
418	I play bass	3	235	\N
416	Red	3	236	\N
423	white	3	237	\N
417	George	2	238	\N
424	George Harrison	2	239	\N
419	Boring, Cunning	2	240	\N
418	I play lead	2	241	\N
416	Blue	2	242	\N
423	black	2	243	\N
417	Ringo	1	244	\N
424	Richard Starkey	1	245	\N
419	Amazing, Boring, Sexual	1	246	\N
418	I play drums	1	247	\N
416	Blue	1	248	\N
423	white	1	249	\N
\.


--
-- Name: answers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mattwebley
--

SELECT pg_catalog.setval('answers_id_seq', 249, true);


--
-- Data for Name: nameids; Type: TABLE DATA; Schema: public; Owner: mattwebley
--

COPY nameids (question_set_name, question_set_id, site_id, id) FROM stdin;
Coffee Quiz	229	\N	128
New Form	228	\N	127
\.


--
-- Name: nameids_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mattwebley
--

SELECT pg_catalog.setval('nameids_id_seq', 128, true);


--
-- Name: nameids_question_set_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mattwebley
--

SELECT pg_catalog.setval('nameids_question_set_id_seq', 229, true);


--
-- Data for Name: options; Type: TABLE DATA; Schema: public; Owner: mattwebley
--

COPY options (answer_option, question_id, id, site_id) FROM stdin;
Tea	420	469	\N
Coffee	420	470	\N
0	421	471	\N
1	421	472	\N
2	421	473	\N
3+	421	474	\N
Yes	422	475	\N
No	422	476	\N
John	417	522	\N
Paul	417	523	\N
George	417	524	\N
Ringo	417	525	\N
Red	416	512	\N
Orange	416	527	\N
Green	416	528	\N
Blue	416	529	\N
Elton John	425	533	\N
John Lennon	425	534	\N
Amazing	419	535	\N
Boring	419	536	\N
Cunning	419	537	\N
Sexual	419	538	\N
white	423	539	\N
\N	418	504	\N
black	423	540	\N
golden	423	541	\N
dont have a dog	423	542	\N
\N	420	543	\N
\N	420	544	\N
\.


--
-- Name: options_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mattwebley
--

SELECT pg_catalog.setval('options_id_seq', 544, true);


--
-- Data for Name: questions; Type: TABLE DATA; Schema: public; Owner: mattwebley
--

COPY questions (id, question, input_type, "position", question_set, site_id) FROM stdin;
421	How many sugars?	checkbox	0	229	\N
420	Choose a favouirte	checkbox	1	229	\N
425	Goodbye yellow brick road	radio	2	229	\N
422	Do you have milk?	select	3	229	\N
417	What is your name?	radio	0	228	\N
424	What is your real name	text	1	228	\N
419	Pick a few of these words that best describes you	checkbox	2	228	\N
418	Tell us a little about yourself	textarea	3	228	\N
416	What is your fav colour?	select	4	228	\N
423	Whay colour is your dog	select	5	228	\N
\.


--
-- Name: questions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mattwebley
--

SELECT pg_catalog.setval('questions_id_seq', 426, true);


--
-- Name: questions_question_set_seq; Type: SEQUENCE SET; Schema: public; Owner: mattwebley
--

SELECT pg_catalog.setval('questions_question_set_seq', 4, true);


--
-- Name: answers answers_pk; Type: CONSTRAINT; Schema: public; Owner: mattwebley
--

ALTER TABLE ONLY answers
    ADD CONSTRAINT answers_pk PRIMARY KEY (id);


--
-- Name: nameids nameids_pk; Type: CONSTRAINT; Schema: public; Owner: mattwebley
--

ALTER TABLE ONLY nameids
    ADD CONSTRAINT nameids_pk PRIMARY KEY (id);


--
-- Name: options options_pk; Type: CONSTRAINT; Schema: public; Owner: mattwebley
--

ALTER TABLE ONLY options
    ADD CONSTRAINT options_pk PRIMARY KEY (id);


--
-- Name: questions questions_pk; Type: CONSTRAINT; Schema: public; Owner: mattwebley
--

ALTER TABLE ONLY questions
    ADD CONSTRAINT questions_pk PRIMARY KEY (id);


--
-- PostgreSQL database dump complete
--

