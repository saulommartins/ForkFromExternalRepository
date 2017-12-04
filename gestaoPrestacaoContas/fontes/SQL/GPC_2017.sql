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
*
* Script de DDL e DML
*
* Versao 2.01.7
*
* Fabio Bertoldi - 20130605
*
*/

----------------
-- Ticket #20368
----------------

        INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES (2013, 25, 'grupo_credito_iptu', '');

        INSERT
          INTO administracao.modulo
          ( cod_modulo
          , cod_responsavel
          , nom_modulo
          , nom_diretorio
          , ordem
          , cod_gestao
          , ativo
          )
          VALUES
          ( 60
          , 0
          , 'ITBI/IPTU'
          , 'itbi_iptu/'
          , 95
          , 6
          , TRUE
          );

        INSERT
          INTO administracao.funcionalidade
          ( cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem
          , ativo
          )
          VALUES
          ( 478
          , 60
          , 'Exportação'
          , 'instancias/exportacao/'
          , 1
          , TRUE
          );

        INSERT
          INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao
          , ativo
          )
          VALUES
          ( 2889
          , 478
          , 'FLExportacaoGT.php'
          , 'exportar'
          , 1
          , ''
          , 'Exportação ITBI/IPTU'
          , TRUE
          );


        INSERT
          INTO sw_municipio
             ( cod_municipio
             , cod_uf
             , nom_municipio
             )
        SELECT 468
             , 23
             , 'Aceguá'
         WHERE 0 = (
                     SELECT COUNT(1)
                       FROM sw_municipio
                      WHERE cod_municipio = 468
                        AND cod_uf        = 23
                   );

        UPDATE sw_municipio SET cod_municipio = 468 WHERE cod_uf = 23 AND nom_municipio = 'Aceguá'      ;
        UPDATE sw_municipio SET cod_municipio = 469 WHERE cod_uf = 23 AND nom_municipio = 'Pedras Altas';


        CREATE SCHEMA sefazrs;
        GRANT ALL ON SCHEMA sefazrs TO siamweb;

        CREATE TABLE sefazrs.municipios_iptu(
            cod_sefaz       INTEGER     NOT NULL,
            cod_uf          INTEGER     NOT NULL,
            cod_municipio   INTEGER             ,
            CONSTRAINT pk_municipios_iptu   PRIMARY KEY             (cod_sefaz),
            CONSTRAINT fk_municipios_iptu_1 FOREIGN KEY             (cod_uf, cod_municipio)
                                            REFERENCES sw_municipio (cod_uf, cod_municipio)
        );
        GRANT ALL ON sefazrs.municipios_iptu TO siamweb;

        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 468, 23,  468);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 245, 23,    1);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (   1, 23,    2);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 162, 23,    3);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 163, 23,    4);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (   2, 23,    5);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 246, 23,    6);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 469, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 164, 23,    7);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 247, 23,    8);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 334, 23,    9);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 165, 23,   10);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 248, 23,   11);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 335, 23,   12);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 249, 23,   13);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 166, 23,   14);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (   3, 23,   15);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 336, 23,   16);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 428, 23,   17);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (   4, 23,   18);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (   5, 23,   19);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 470, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 250, 23,   20);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 168, 23,   21);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 167, 23,   22);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (   6, 23,   23);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (   7, 23,   24);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 169, 23,   25);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 251, 23,   26);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (   8, 23,   27);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 429, 23,   28);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 252, 23,   29);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 170, 23,   30);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 337, 23,   31);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 338, 23,   32);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 430, 23,   33);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (   9, 23,   34);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 339, 23,   35);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 340, 23,   36);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 171, 23,   37);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 172, 23,   38);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 431, 23,   39);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  10, 23,   40);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 341, 23,   41);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 173, 23,   42);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 471, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 472, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 432, 23,   43);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  11, 23,   44);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 233, 23,   45);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 342, 23,   46);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  12, 23,   47);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 253, 23,   48);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 174, 23,   49);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 473, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 175, 23,   50);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 254, 23,   51);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 176, 23,   52);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  13, 23,   53);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  14, 23,   54);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  15, 23,   55);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 177, 23,   56);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 178, 23,   57);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 179, 23,   58);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 180, 23,   59);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  17, 23,   60);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 255, 23,   61);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 181, 23,   62);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 343, 23,   63);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 182, 23,   64);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  18, 23,   65);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  19, 23,   66);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  20, 23,   67);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 256, 23,   68);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  21, 23,   69);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 183, 23,   70);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 344, 23,   71);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  22, 23,   72);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  23, 23,   73);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  24, 23,   74);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 474, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 475, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 234, 23,   75);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 476, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 235, 23,   76);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 257, 23,   77);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 345, 23,   78);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 433, 23,   79);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 434, 23,   80);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  25, 23,   81);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  26, 23,   82);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 346, 23,   83);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  27, 23,   84);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 258, 23,   85);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  28, 23,   86);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  29, 23,   87);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 347, 23,   88);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 435, 23,   89);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 259, 23,   90);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 260, 23,   91);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 261, 23,   92);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  30, 23,   93);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  31, 23,   94);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 236, 23,   95);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 348, 23,   96);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 184, 23,   97);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 436, 23,   98);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 437, 23,   99);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 262, 23,  100);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 185, 23,  101);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 349, 23,  102);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 160, 23,  103);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 186, 23,  104);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  32, 23,  105);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 478, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 350, 23,  106);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 351, 23,  107);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 187, 23,  108);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 477, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 237, 23,  109);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 352, 23,  110);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  33, 23,  111);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 263, 23,  112);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 438, 23,  113);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  34, 23,  114);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 479, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (188 , 23,  15 );
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 189, 23,  116);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 353, 23,  117);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 264, 23,  118);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 439, 23,  119);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  35, 23,  120);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 354, 23,  121);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 265, 23,  122);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 190, 23,  123);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  36, 23,  124);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 440, 23,  125);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 191, 23,  126);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 266, 23,  127);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 441, 23,  128);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 267, 23,  129);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  37, 23,  130);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 38 , 23,  131);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 355, 23,  132);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 269, 23,  134);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 268, 23,  133);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 270, 23,  135);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  39, 23,  136);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 271, 23,  137);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  40, 23,  138);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 192, 23,  139);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  16, 23,  140);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 442, 23,  141);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  41, 23,  142);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 272, 23,  143);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  42, 23,  144);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  43, 23,  145);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  44, 23,  146);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 443, 23,  147);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 273, 23,  148);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 274, 23,  149);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  45, 23,  150);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  46, 23,  151);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 275, 23,  152);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 444, 23,  153);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  47, 23,  154);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  48, 23,  155);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 445, 23,  156);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 193, 23,  157);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 194, 23,  158);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 480, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 238, 23,  159);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  49, 23,  160);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  50, 23,  161);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 356, 23,  162);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  51, 23,  163);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  52, 23,  164);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 357, 23,  165);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  54, 23,  166);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  55, 23,  167);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 276, 23,  168);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  56, 23,  169);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 358, 23,  170);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 359, 23,  171);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  57, 23,  172);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 277, 23,  173);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  58, 23,  174);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  59, 23,  175);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  60, 23,  176);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 278, 23,  177);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  61, 23,  178);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 446, 23,  179);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  62, 23,  180);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 360, 23,  181);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  63, 23,  182);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 279, 23,  183);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 195, 23,  184);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 196, 23,  185);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 280, 23,  186);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  64, 23,  187);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 161, 23,  188);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  65, 23,  189);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 197, 23,  190);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 281, 23,  191);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 282, 23,  192);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 198, 23,  193);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 361, 23,  194);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 283, 23,  195);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 284, 23,  196);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  66, 23,  197);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 447, 23,  198);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 285, 23,  199);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 362, 23,  200);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  67, 23,  201);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 481, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 199, 23,  202);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 286, 23,  203);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 200, 23,  204);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 287, 23,  205);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 482, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 201, 23,  206);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  68, 23,  207);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  69, 23,  208);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 288, 23,  209);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 448, 23,  210);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 239, 23,  211);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  70, 23,  212);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 483, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 363, 23,  213);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  71, 23,  214);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 289, 23,  215);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  72, 23,  216);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 364, 23,  217);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  73, 23,  218);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 202, 23,  219);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 365, 23,  220);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 366, 23,  221);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 449, 23,  222);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  74, 23,  223);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 450, 23,  224);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 367, 23,  225);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 368, 23,  226);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 369, 23,  227);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  75, 23,  228);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  76, 23,  229);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 370, 23,  230);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 203, 23,  231);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 451, 23,  232);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 204, 23,  233);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 371, 23,  234);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 372, 23,  235);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 484, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  77, 23,  236);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 373, 23,  237);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 205, 23,  238);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 290, 23,  239);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 452, 23,  240);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 374, 23,  241);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  78, 23,  242);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 375, 23,  243);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 376, 23,  244);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 291, 23,  245);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 377, 23,  246);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  79, 23,  247);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  80, 23,  248);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 453, 23,  249);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 378, 23,  250);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  81, 23,  251);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 379, 23,  252);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  82, 23,  253);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 292, 23,  254);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 206, 23,  255);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 207, 23,  256);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 380, 23,  257);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 208, 23,  258);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 454, 23,  259);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 293, 23,  260);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 294, 23,  261);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 381, 23,  262);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  83, 23,  263);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  84, 23,  264);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  85, 23,  265);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 455, 23,  266);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 295, 23,  267);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 382, 23,  268);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 383, 23,  269);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 456, 23,  270);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  86, 23,  271);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 384, 23,  272);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 385, 23,  273);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 485, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  87, 23,  274);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  88, 23,  275);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 240, 23,  276);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  89, 23,  277);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 209, 23,  278);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  90, 23,  279);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 296, 23,  280);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 210, 23,  281);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 297, 23,  282);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 386, 23,  283);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 241, 23,  284);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 457, 23,  285);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 387, 23,  286);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  91, 23,  287);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 486, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 298, 23,  288);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 487, 23,  469);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  92, 23,  289);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 211, 23,  290);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  93, 23,  291);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 388, 23,  292);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 299, 23,  293);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 488, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 389, 23,  294);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 390, 23,  295);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  94, 23,  296);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 489, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 300, 23,  297);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  95, 23,  298);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 212, 23,  299);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 301, 23,  300);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 391, 23,  301);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 392, 23,  302);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 213, 23,  303);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  96, 23,  304);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  97, 23,  305);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 393, 23,  306);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 394, 23,  307);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 214, 23,  308);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 302, 23,  309);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 395, 23,  310);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 303, 23,  311);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 304, 23,  312);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 215, 23,  313);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  98, 23,  314);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 490, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 396, 23,  315);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 305, 23,  316);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 216, 23,  317);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 306, 23,  318);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  99, 23,  319);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 397, 23,  320);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 100, 23,  321);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 101, 23,  322);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 307, 23,  323);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 102, 23,  324);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 217, 23,  325);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 491, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 103, 23,  326);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 218, 23,  327);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 219, 23,  328);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 220, 23,  329);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 104, 23,  330);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 398, 23,  331);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 308, 23,  332);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 242, 23,  333);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 399, 23,  334);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 221, 23,  335);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 105, 23,  336);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 107, 23,  337);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 494, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 400, 23,  338);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 108, 23,  339);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 495, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 109, 23,  340);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 309, 23,  341);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 110, 23,  342);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 401, 23,  343);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 111, 23,  344);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 222, 23,  345);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 106, 23,  346);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 112, 23,  347);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 113, 23,  348);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 114, 23,  349);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 223, 23,  350);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 402, 23,  351);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 403, 23,  352);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 115, 23,  353);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 116, 23,  354);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 404, 23,  355);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 117, 23,  356);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 310, 23,  357);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 118, 23,  358);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 119, 23,  359);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 120, 23,  360);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 121, 23,  361);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 311, 23,  362);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 405, 23,  363);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 312, 23,  364);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 406, 23,  365);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 313, 23,  366);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 314, 23,  367);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 407, 23,  368);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 122, 23,  369);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 123, 23,  370);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 492, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 408, 23,  371);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 124, 23,  372);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 125, 23,  373);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 126, 23,  374);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 224, 23,  375);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 225, 23,  376);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 409, 23,  377);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 315, 23,  378);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 226, 23,  379);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 227, 23,  380);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 410, 23,  381);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 493, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 411, 23,  382);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 127, 23,  383);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 128, 23,  384);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 129, 23,  385);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 130, 23,  386);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 412, 23,  387);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 413, 23,  388);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 333, 23,  389);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES (  53, 23,  390);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 131, 23,  391);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 132, 23,  392);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 133, 23,  393);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 134, 23,  394);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 316, 23,  395);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 317, 23,  396);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 228, 23,  397);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 458, 23,  398);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 414, 23,  399);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 135, 23,  400);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 415, 23,  401);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 229, 23,  402);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 416, 23,  403);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 459, 23,  404);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 230, 23,  405);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 318, 23,  406);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 417, 23,  407);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 136, 23,  408);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 137, 23,  409);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 460, 23,  410);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 138, 23,  411);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 139, 23,  412);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 140, 23,  413);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 141, 23,  414);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 142, 23,  415);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 319, 23,  416);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 243, 23,  417);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 143, 23,  418);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 320, 23,  419);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 244, 23,  420);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 496, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 418, 23,  421);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 461, 23,  422);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 144, 23,  423);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 145, 23,  424);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 419, 23,  425);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 321, 23,  426);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 322, 23,  427);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 146, 23,  428);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 147, 23,  429);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 420, 23,  430);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 323, 23,  431);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 148, 23,  432);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 324, 23,  433);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 149, 23,  434);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 150, 23,  435);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 325, 23,  436);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 421, 23,  437);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 151, 23,  438);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 326, 23,  439);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 152, 23,  440);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 462, 23,  441);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 463, 23,  442);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 422, 23,  443);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 464, 23,  444);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 153, 23,  445);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 154, 23,  446);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 423, 23,  447);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 424, 23,  448);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 465, 23,  449);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 327, 23,  450);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 155, 23,  451);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 156, 23,  452);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 157, 23,  453);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 466, 23,  454);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 158, 23,  455);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 159, 23,  456);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 231, 23,  457);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 232, 23,  458);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 328, 23,  459);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 467, 23,  460);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 329, 23,  461);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 425, 23,  462);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 330, 23,  463);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 331, 23,  464);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 332, 23,  465);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 426, 23,  466);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 497, 23, NULL);
        INSERT INTO sefazrs.municipios_iptu (cod_sefaz, cod_uf, cod_municipio) VALUES ( 427, 23,  467);


----------------
-- Ticket #20349
----------------

--passa do anexo 5 para o 4
UPDATE administracao.relatorio 
   SET arquivo       = 'RREOAnexo4.rptdesign'
     , nom_relatorio = 'RREO - Anexo 4 - Demonstrativo das Receitas e Despesas Previdenciárias do Regime Próprio dos Servidores Públicos'
 WHERE cod_gestao    =  6 
   AND cod_modulo    = 36 
   AND cod_relatorio = 28;

--passa do anexo 6 para o 5
UPDATE administracao.relatorio
   SET arquivo       = 'RREOAnexo5.rptdesign'
     , nom_relatorio = 'Anexo 5'
 WHERE cod_gestao    =  6
   AND cod_modulo    = 36
   AND cod_relatorio = 42;

--passa do anexo 7 para o 6
UPDATE administracao.relatorio
   SET arquivo       = 'RREOAnexo6Novo.rptdesign'
     , nom_relatorio = 'RREO - Anexo 6 - Demonstrativo do Resultado Primário'
 WHERE cod_gestao    =  6
   AND cod_modulo    = 36
   AND cod_relatorio = 25;

--passa do anexo 9 para 7
UPDATE administracao.relatorio
   SET arquivo       = 'RREOAnexo7.rptdesign'
     , nom_relatorio = 'RREO - Anexo 7 -Demonstrativo dos Restos a Pagar por Poder e Órgão'
 WHERE cod_gestao    =  6
   AND cod_modulo    = 36
   AND cod_relatorio = 26;

--passa do anexo 13 para 10
UPDATE administracao.relatorio 
   SET arquivo       = 'RREOAnexo10.rptdesign'
     , nom_relatorio = 'RREO - Anexo 10 - Dem. Projeção Atuarial do RPPS'
 WHERE cod_gestao    =  6
   AND cod_modulo    = 36
   AND cod_relatorio = 35;

--passa do anexo 14 para 11
UPDATE administracao.relatorio
   SET arquivo       = 'RREOAnexo11.rptdesign'
     , nom_relatorio = 'RREO - Anexo 11 - Demonstrativo da Receita de Alienação de Ativos e Aplicação dos Recursos'
 WHERE cod_gestao    =  6
   AND cod_modulo    = 36
   AND cod_relatorio = 44;

--passa do anexo 18 para o 14
UPDATE administracao.relatorio
   SET arquivo       = 'RREOAnexo14Novo.rptdesign'
     , nom_relatorio = 'RREO - Anexo XVIII - Demonstrativo Simplificado do Relatório Resumido da Execução Orçamentária'
 WHERE cod_gestao    =  6
   AND cod_modulo    = 36
   AND cod_relatorio = 34;


----------------
-- Ticket #20009
----------------

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2013'
     , 8
     , 'pao_digitos_id_nao_orcamentarios'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE cod_modulo = 8
                AND exercicio  = '2013'
                AND parametro = 'pao_digitos_id_nao_orcamentarios'
           );


----------------
-- Ticket #20380
----------------

CREATE       SCHEMA transparencia;
GRANT ALL ON SCHEMA transparencia TO siamweb;


----------------
-- Ticket #20349
----------------

INSERT
  INTO administracao.relatorio
  ( cod_gestao
  , cod_modulo
  , cod_relatorio
  , nom_relatorio
  , arquivo )
  VALUES
  ( 6
  , 36
  , 54
  , 'RREO - Anexo 8 - Demonstrativo das Receitas e Despesas com Manutenção e Desenvolvimento do Ensino - MDE'
  , 'RREOAnexo8.rptdesign'
  );

INSERT
  INTO administracao.relatorio
  ( cod_gestao
  , cod_modulo
  , cod_relatorio
  , nom_relatorio
  , arquivo )
  VALUES
  ( 6
  , 36
  , 55
  , 'RREO - Anexo 10 - Dem. Projeção Atuarial do RPPS'
  , 'RREOAnexo10Novo.rptdesign'
  );

UPDATE administracao.relatorio
   SET nom_relatorio = 'Anexo 12 - Demonstrativo as Despesas com Ações e Serviços Públicos de Saúde'
 WHERE cod_gestao    = 6
   AND cod_modulo    = 36
   AND cod_relatorio = 48
     ;
     
UPDATE administracao.acao
   SET complemento_acao = 'Demonstrativo das Despesas com Saúde'
 WHERE cod_acao = 2878
     ;

