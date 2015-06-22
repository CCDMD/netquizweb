<?php

/**
 * Fichier de configuration pour Netquiz Web.
 *
 * @author CCDMD <netquizweb@ccdmd.qc.ca>
 * @version 1.0
 * @package NetquizWeb
 * @license Lisence MIT https://github.com/CCDMD/netquizweb/blob/master/LICENSE
 *
 */

/** INSCRIRE ENTRE LES DEUX APOSTROPHES ('') l'adresse réseau du serveur de base de données (ex. 127.0.0.1 ou localhost) */
define('DB_HOST', '');

/** INSCRIRE ENTRE LES DEUX APOSTROPHES  ('') le nom de la base de données de Netquiz Web */
define('DB_NAME', '');

/** INSCRIRE ENTRE LES DEUX APOSTROPHES  ('') le nom d’utilisateur de la base de données de Netquiz Web */
define('DB_USER', '');

/** INSCRIRE ENTRE LES DEUX APOSTROPHES  ('') le mot de passe de la base de données de Netquiz Web */
define('DB_PASSWORD', '');

/** INSCRIRE ENTRE LES DEUX APOSTROPHES  ('') l’adresse de courriel qui figurera à titre d’expéditeur dans les courriels envoyés automatiquement par Netquiz Web aux utilisateurs (idéalement, cette adresse ne devrait pas être votre adresse personnelle, mais une adresse de courriel dédiée à Netquiz Web) */
define('EMAIL_FROM', '');

/** INSCRIRE ENTRE LES DEUX APOSTROPHES  ('') l’adresse de courriel qui figurera dans l'entête Return-Path indiquant à quelle adresse doivent être envoyées les réponses automatiques du serveur (no delivery par exemple). */
define('RETURN_PATH', '');

?>