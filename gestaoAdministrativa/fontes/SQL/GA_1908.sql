/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/*
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: GA_1908.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.90.8
*/

----------------
-- Ticket #12852
----------------

INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 14, 7, 'Ampola'        , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 15, 7, 'Anastubete'    , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 16, 7, 'Balde'         , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 17, 7, 'Barra'         , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 18, 7, 'Bisnaga'       , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 19, 7, 'Blister'       , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 20, 7, 'Bloco'         , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 21, 7, 'Bobina'        , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 22, 7, 'Bombona'       , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 23, 7, 'Cápsula'       , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 24, 7, 'Carga'         , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 25, 7, 'Cartela'       , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 26, 7, 'Cento'         , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES (  5, 4, 'cm/coluna'     , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES (  1, 6, 'Coleção'       , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 27, 7, 'Comprimido'    , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES (  2, 6, 'Conjunto'      , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES (  8, 1, 'Diária'        , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES (  3, 6, 'Diskus'        , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 28, 7, 'Dragea'        , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 29, 7, 'Embalagem'     , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 30, 7, 'Envelope'      , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 31, 7, 'Estojo'        , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 32, 7, 'Flaconete'     , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 33, 7, 'Folha'         , 'fl' );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 34, 7, 'Frasco'        , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 35, 7, 'Frasco-Ampola' , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES (  4, 5, 'Galão'         , 'gal');
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 36, 7, 'Garrafa'       , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 37, 7, 'Grosa'         , 'gr' );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES (  9, 1, 'Hora/Mês'      , 'h/m');
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 38, 7, 'Jogo'          , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 39, 7, 'Kit'           , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 40, 7, 'Maço'          , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES (  6, 4, 'Metro Linear'  , 'm'  );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 10, 1, 'Minutos/Mês'   , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 41, 7, 'Pote'          , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 42, 7, 'Rolo'          , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 43, 7, 'Saca'          , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 44, 7, 'Sache'         , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 45, 7, 'Saco'          , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 46, 7, 'Seringa'       , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES (  4, 6, 'Serviço'       , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 47, 7, 'Tablete'       , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 48, 7, 'Tambor'        , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 49, 7, 'Tubo'          , ''   );
INSERT INTO administracao.unidade_medida (cod_unidade, cod_grandeza, nom_unidade, simbolo) VALUES ( 50, 7, 'Vidro'         , ''   );
