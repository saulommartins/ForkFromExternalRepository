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
* Versao 2.03.3
*
* Fabio Bertoldi - 20141112
*
*/

----------------
-- Ticket #22408
----------------

CREATE SCHEMA tceto;

CREATE TABLE tceto.norma_detalhe(
    cod_norma                       INTEGER         NOT NULL,
    cod_lei_alteracao               INTEGER         NOT NULL,
    percentual_credito_adicional    NUMERIC(3)      NOT NULL,
    CONSTRAINT pk_norma_detalhe                     PRIMARY KEY             (cod_norma),
    CONSTRAINT fk_norma_detalhe_1                   FOREIGN KEY             (cod_norma)
                                                    REFERENCES normas.norma (cod_norma),
    CONSTRAINT fk_norma_detalhe_2                   FOREIGN KEY             (cod_lei_alteracao)
                                                    REFERENCES normas.lei   (cod_lei)
);
GRANT ALL ON tceto.norma_detalhe TO urbem;


----------------
-- Ticket #20862
----------------

INSERT
  INTO sw_pais
     ( cod_pais
     , cod_rais
     , nom_pais
     , nacionalidade
     )
SELECT 51
     , 51
     , 'Áustria'
     , 'Austríaca'
 WHERE 0 = (
             SELECT COUNT(1)
               FROM sw_pais
              WHERE cod_pais = 51
           )
     ;

INSERT
  INTO sw_pais
     ( cod_pais
     , cod_rais
     , nom_pais
     , nacionalidade
     )
SELECT 52
     , 52
     , 'Holanda'
     , 'Holandesa'
 WHERE 0 = (
             SELECT COUNT(1)
               FROM sw_pais
              WHERE cod_pais = 52
           )
     ;

UPDATE sw_pais SET cod_rais = 80 WHERE cod_pais = 50;

ALTER TABLE sw_pais ADD   COLUMN sigla_3 CHAR(3);
UPDATE      sw_pais SET          sigla_3 = '   ' WHERE cod_pais =  0;
UPDATE      sw_pais SET          sigla_3 = 'BRA' WHERE cod_pais =  1;
UPDATE      sw_pais SET          sigla_3 = 'ARG' WHERE cod_pais =  3;
UPDATE      sw_pais SET          sigla_3 = 'BOL' WHERE cod_pais =  4;
UPDATE      sw_pais SET          sigla_3 = 'CHL' WHERE cod_pais =  5;
UPDATE      sw_pais SET          sigla_3 = 'PRY' WHERE cod_pais =  6;
UPDATE      sw_pais SET          sigla_3 = 'URY' WHERE cod_pais =  7;
UPDATE      sw_pais SET          sigla_3 = 'DEU' WHERE cod_pais =  8;
UPDATE      sw_pais SET          sigla_3 = 'BEL' WHERE cod_pais =  9;
UPDATE      sw_pais SET          sigla_3 = 'GBR' WHERE cod_pais = 10;
UPDATE      sw_pais SET          sigla_3 = 'CAN' WHERE cod_pais = 11;
UPDATE      sw_pais SET          sigla_3 = 'ESP' WHERE cod_pais = 12;
UPDATE      sw_pais SET          sigla_3 = 'USA' WHERE cod_pais = 13;
UPDATE      sw_pais SET          sigla_3 = 'FRA' WHERE cod_pais = 14;
UPDATE      sw_pais SET          sigla_3 = 'CHE' WHERE cod_pais = 15;
UPDATE      sw_pais SET          sigla_3 = 'ITA' WHERE cod_pais = 16;
UPDATE      sw_pais SET          sigla_3 = 'JPN' WHERE cod_pais = 17;
UPDATE      sw_pais SET          sigla_3 = 'CHN' WHERE cod_pais = 18;
UPDATE      sw_pais SET          sigla_3 = 'KOR' WHERE cod_pais = 19;
UPDATE      sw_pais SET          sigla_3 = 'PRT' WHERE cod_pais = 20;
UPDATE      sw_pais SET          sigla_3 = '   ' WHERE cod_pais = 21;
UPDATE      sw_pais SET          sigla_3 = '   ' WHERE cod_pais = 22;
UPDATE      sw_pais SET          sigla_3 = '   ' WHERE cod_pais = 50;
UPDATE      sw_pais SET          sigla_3 = 'AUT' WHERE cod_pais = 51;
UPDATE      sw_pais SET          sigla_3 = 'NLD' WHERE cod_pais = 52;
ALTER TABLE sw_pais ALTER COLUMN sigla_3 SET NOT NULL;


----------------
-- Ticket #22412
----------------

UPDATE administracao.funcionalidade
   SET nom_funcionalidade = 'Relatórios'
     , ordem              = 9999
 WHERE cod_funcionalidade IN (  10,  40,  41,  28, 274, 168,  63, 194, 214, 181, 217, 233
                             , 239, 305, 215, 287, 361, 209, 350, 276, 304,  39, 381, 402
                             , 211, 407, 366, 426, 417, 444, 461, 438, 471, 479,  95, 484
                             );

UPDATE administracao.modulo SET ordem = 12 WHERE cod_modulo = 29;
UPDATE administracao.modulo SET ordem = 10 WHERE cod_modulo =  6;
UPDATE administracao.modulo SET ordem = 11 WHERE cod_modulo =  7;

UPDATE administracao.funcionalidade SET nom_funcionalidade = 'Motivo do Arquivamento'            WHERE cod_funcionalidade = 107;
UPDATE administracao.funcionalidade SET nom_funcionalidade = 'Cadastro Internacional de Doenças' WHERE cod_funcionalidade = 283;
UPDATE administracao.funcionalidade SET nom_funcionalidade = 'Padrão Salarial'                   WHERE cod_funcionalidade = 243;
UPDATE administracao.funcionalidade SET nom_funcionalidade = 'Vale-Transporte'                   WHERE cod_funcionalidade = 238;
UPDATE administracao.funcionalidade SET nom_funcionalidade = 'Vale-Transporte Servidor'          WHERE cod_funcionalidade = 247;

UPDATE administracao.modulo SET nom_modulo = 'Lei de Responsabilidade Fiscal'        WHERE cod_modulo = 24;
UPDATE administracao.modulo SET nom_modulo = 'Plano Plurianual'                      WHERE cod_modulo = 43;
UPDATE administracao.modulo SET nom_modulo = 'Lei de Diretrizes Orçamentárias'       WHERE cod_modulo = 44;
UPDATE administracao.modulo SET nom_modulo = 'Manual Normativo de Arquivos Digitais' WHERE cod_modulo = 59;
UPDATE administracao.modulo SET nom_modulo = 'Informações Mensais e Anuais'          WHERE cod_modulo = 40;

UPDATE administracao.funcionalidade SET ordem =  8 WHERE cod_funcionalidade = 281;
UPDATE administracao.funcionalidade SET ordem =  7 WHERE cod_funcionalidade = 310;

UPDATE administracao.funcionalidade SET ordem = 10 WHERE cod_funcionalidade =  163;
UPDATE administracao.funcionalidade SET ordem =  7 WHERE cod_funcionalidade =  210;
UPDATE administracao.funcionalidade SET ordem =  8 WHERE cod_funcionalidade =  218;
UPDATE administracao.funcionalidade SET ordem =  9 WHERE cod_funcionalidade =  235;


UPDATE administracao.acao SET nom_acao = 'Incluir Motivo' WHERE cod_acao = 155;
UPDATE administracao.acao SET nom_acao = 'Alterar Motivo' WHERE cod_acao = 156;
UPDATE administracao.acao SET nom_acao = 'Excluir Motivo' WHERE cod_acao = 157;

UPDATE administracao.acao SET nom_acao = 'Incluir Padrão Salarial' WHERE cod_acao = 1040;
UPDATE administracao.acao SET nom_acao = 'Alterar Padrão Salarial' WHERE cod_acao = 1041;
UPDATE administracao.acao SET nom_acao = 'Excluir Padrão Salarial' WHERE cod_acao = 1042;

UPDATE administracao.acao SET nom_acao = 'Inicializar Concessão de Vale-Transporte'              WHERE cod_acao = 1105;
UPDATE administracao.acao SET nom_acao = 'Excluir Inicialização da Concessão de Vale-Transporte' WHERE cod_acao = 1106;

UPDATE administracao.funcionalidade SET ativo = FALSE where cod_funcionalidade = 310;


UPDATE administracao.funcionalidade SET ordem =    1 WHERE cod_funcionalidade = 158;
UPDATE administracao.funcionalidade SET ordem =    2 WHERE cod_funcionalidade = 159;
UPDATE administracao.funcionalidade SET ordem =    3 WHERE cod_funcionalidade = 160;
UPDATE administracao.funcionalidade SET ordem =    4 WHERE cod_funcionalidade = 161;
UPDATE administracao.funcionalidade SET ordem =    5 WHERE cod_funcionalidade = 163;
UPDATE administracao.funcionalidade SET ordem =    7 WHERE cod_funcionalidade = 235;
UPDATE administracao.funcionalidade SET ordem =    8 WHERE cod_funcionalidade = 210;
UPDATE administracao.funcionalidade SET ordem =    9 WHERE cod_funcionalidade = 218;
UPDATE administracao.funcionalidade SET ordem =   10 WHERE cod_funcionalidade = 162;
UPDATE administracao.funcionalidade SET ordem =   11 WHERE cod_funcionalidade = 395;
UPDATE administracao.funcionalidade SET ordem = 9999 WHERE cod_funcionalidade = 168;

UPDATE administracao.acao SET nom_acao = 'Consultar Plano Plurianual' WHERE cod_acao = 2730;
UPDATE administracao.acao SET nom_acao = 'Incluir Plano Plurianual'   WHERE cod_acao = 2350;
UPDATE administracao.acao SET nom_acao = 'Excluir Plano Plurianual'   WHERE cod_acao = 2432;
UPDATE administracao.acao SET nom_acao = 'Homologar Plano Plurianual' WHERE cod_acao = 2360;

