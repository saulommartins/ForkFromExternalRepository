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
* Versao 2.04.1
*
* Fabio Bertoldi - 20150608
*
*/

----------------
-- Ticket #22970
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
     VALUES
     ( 2
     , 30
     , 7
     , 'Relatório Pagamentos Borderô'
     , 'LHRelatorioBorderoPagamento.php'
     );

UPDATE administracao.acao SET ativo = TRUE WHERE cod_acao = 1412;


----------------
-- Ticket #23111
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
     VALUES
     ( 2
     , 9
     , 19
     , 'Demonstração das Variações Patrimoniais'
     , 'demonstrarVariacoesPatrimoniaisEstrutural.rptdesign'
     ); 


----------------
-- Ticket #23111
----------------

CREATE TYPE variacao_patrimonial_estrutural AS (
    cod_estrutural      VARCHAR,
    nivel               INTEGER,
    nom_conta           VARCHAR,
    cod_sistema         INTEGER,
    indicador_superavit CHAR,
    vl_saldo_anterior   NUMERIC,
    vl_saldo_debitos    NUMERIC,
    vl_saldo_creditos   NUMERIC,
    vl_saldo_atual      NUMERIC
);


----------------
-- Ticket #23130
----------------

ALTER TABLE tesouraria.recibo_extra ALTER COLUMN historico TYPE TEXT;

