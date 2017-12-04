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
* $Id: GA_1922.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.92.2
*/

-----------------------------------------------
-- RECRIANDO PK EM pk_andamento_padrao (VIRADA)
-----------------------------------------------

ALTER TABLE public.sw_andamento_padrao DROP CONSTRAINT pk_andamento_padrao;
ALTER TABLE public.sw_andamento_padrao ADD  CONSTRAINT pk_andamento_padrao PRIMARY KEY (num_passagens, cod_classificacao, cod_assunto, cod_orgao, ordem);

--------------------------------------------------------------------------------
-- ALTERANDO COLUNA nom_arquivo DE administracao.acao P/ COMPORTAR 85 CARACTERES
-- BRUNO FERREIRA - 20090113 ---------------------------------------------------

ALTER TABLE administracao.acao ALTER COLUMN nom_arquivo TYPE VARCHAR(85);


---------------------------------------------------------------
-- POVOANDO DESCRICAO DE ORGAOS CRIADOS ENTE GA 1.91.8 e 1.92.2
---------------------------------------------------------------

INSERT
  INTO organograma.orgao_descricao
     ( cod_orgao
     , timestamp
     , descricao )
SELECT cod_orgao
     , criacao::timestamp(3)
     , descricao
  FROM organograma.orgao
 WHERE cod_orgao NOT IN ( SELECT cod_orgao
                            FROM organograma.orgao_descricao
                        );

---------------------------------------------------------------------
-- ALTERANDO VALOR DEFAULT NA COLUNA ativo EM organograma.organograma
---------------------------------------------------------------------

ALTER TABLE organograma.organograma ALTER COLUMN ativo SET DEFAULT false;


---------------------------------------------------------------
-- CONCEDENDO PERMISSAO P/ USUARIO admin - MIGRACAO ORGANOGRAMA
---------------------------------------------------------------

INSERT
  INTO administracao.permissao
SELECT numcgm
     , 2429   AS cod_acao
     , '2009' AS ano_exercicio
  FROM administracao.usuario
 WHERE username = 'admin';
