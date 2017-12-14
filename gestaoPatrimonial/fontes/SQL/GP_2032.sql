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
* Versao 2.03.2
*
* Fabio Bertoldi - 20141001
*
*/

----------------
-- Ticket #20390
----------------
UPDATE administracao.acao SET ordem = ordem + 1 WHERE cod_funcionalidade = 28 and ordem > 11;

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
     ( 2991
     , 28
     , 'FLRelatorioReavaliacoes.php'
     , 'imprimir'
     , 12
     , ''
     , 'Relatório de Reavaliações'
     , TRUE
     );

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
     VALUES
     ( 3
     , 6
     , 21
     , 'Relatório de Reavaliações'
     , 'relatorioReavaliacoes.rptdesign'
     );


----------------
-- Ticket #22084
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
     VALUES
     ( 3
     , 37
     , 4
     , 'Relatório Dados Licitação'
     , 'LHRelatorioDadosLicitacao.php'
     );
 


----------------
-- Ticket #22087
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
     VALUES
     ( 3
     , 35
     , 5
     , 'Relatório Dados da Compas Direta'
     , 'LHRelatorioDadosCompraDireta.php'
     );
 


--------------------------------------------------------------------------
-- CORRECAO DE FK EM almoxarifado.catalogo_classificacao - Gelson 20141023
--------------------------------------------------------------------------

ALTER TABLE almoxarifado.catalogo_classificacao DROP CONSTRAINT fk_catalogo_clasificacao_1;
 
