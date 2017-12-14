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
* $Id:$
*
* Versão 1.98.2
*/

------------------------------------------------------------------------------
-- ADICIONANDO RELATORIOS Comparativo de Despesa X Receita E Demonstrativo III
------------------------------------------------------------------------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao )
VALUES
     ( 2773
     , 168
     , 'FLComparativoDespesaReceita.php'
     , 'imprimir'
     , 20
     , ''
     , 'Comparativo de Despesa X Receita'
     );

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
VALUES
     ( 2
     , 8
     , 8
     , 'Comparativo de Despesa X Receita'
     , 'comparativoDespesaReceita.rptdesign'
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao )
VALUES
     ( 2774
     , 411
     , 'FLModelosAMF.php'
     , 'demons3'
     , 3
     , ''
     , 'Demonstrativo III'
     );


----------------
-- Ticket #13756
----------------

UPDATE administracao.acao SET parametro='incluirRREO13' where cod_acao = 2421;


----------------
-- Ticket #15358
----------------

update administracao.acao set nom_acao='Vincular Receita Corrente Líquida' where cod_acao=2428;


-------------------------------------
-- CONFERINDO PERMISSAO P/ SCHEMA stn
-------------------------------------

GRANT ALL ON SCHEMA stn TO GROUP urbem;
