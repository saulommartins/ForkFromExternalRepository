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
* URBEM SoluÃ§Ãµes de GestÃ£o PÃºblica Ltda
* www.urbem.cnm.org.br
*
* $Id: GPC_1914.sql 59612 2014-09-02 12:00:51Z gelson $
*
* VersÃ£o 1.91.4
*/
----------------
-- Ticket #
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2476
          , 387
          , 'FLExportacaoDisposicao.php'
          , 'disposicao'
          , 5
          , ''
          , 'Arquivos a Disposição'
          );

---------------
-- Ticket #14701
----------------

CREATE TABLE tcmgo.orgao_representante (
    exercicio       CHAR(4)                 NOT NULL,
    num_orgao       INTEGER                 NOT NULL,
    numcgm          INTEGER                 NOT NULL,
    CONSTRAINT pk_orgao_representante       PRIMARY KEY             (num_orgao, exercicio),
    CONSTRAINT fk_orgao_representante_1     FOREIGN KEY             (num_orgao, exercicio)
                                            REFERENCES tcmgo.orgao  (num_orgao, exercicio),
    CONSTRAINT fk_orgao_representante_2     FOREIGN KEY             (numcgm)
                                            REFERENCES sw_cgm       (numcgm)
);
GRANT ALL ON tcmgo.orgao_representante TO GROUP urbem;

