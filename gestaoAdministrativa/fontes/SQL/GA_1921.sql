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
* $Id: GA_1921.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.92.1
*/


----------------
-- Ticket #114205
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2429
          , 170
          , 'FMProcessaMigracaoOrganograma.php'
          , 'migra'
          , 7
          , ''
          , 'Processar Migração do Organograma'
          );


-------------------------------------------------------
-- INDICES P/ AGILIZAR BUSCA DE ANDAMENTOS RE PROCESSOS
-- FABIO BERTOLDI - 20081229 --------------------------

CREATE INDEX ix_sw_andamento_1        ON sw_andamento (ano_exercicio, cod_orgao, cod_unidade, cod_departamento, cod_setor);
CREATE INDEX ix_sw_ultimo_andamento_1 ON sw_ultimo_andamento (ano_exercicio, cod_processo, cod_andamento);


----------------
-- Ticket #
----------------

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor)
VALUES ( 2009
     , 19
     , 'migra_organograma'
     , 'false'
     );
