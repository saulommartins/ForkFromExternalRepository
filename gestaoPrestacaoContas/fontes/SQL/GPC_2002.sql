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
* Versão 2.00.2
*/

----------------
-- Ticket #17300
----------------

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
  ( 2802
  , 471
  , 'FLSuprimentosFundosConcedidos.php'
  , 'suprimentos'
  , 2
  , ''
  , 'Suprimentos de Fundos'
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
     , 57
     , 5
     , 'Suprimentos de Fundos Concedidos'
     , 'relatorioSuprimentoFundosConcedidos.rptdesign'
     );


----------------
-- Ticket #17299
----------------

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
  ( 2803
  , 471
  , 'FLDemonstrativoAplicacoesFinanceiras.php'
  , 'aplicFinac'
  , 3
  , ''
  , 'Demonstrativo de Aplicações Financeiras'
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
     , 57
     , 6
     , 'Demonstrativo de Aplicações Financeiras'
     , 'relatorioAplicacoesFinanceiras.rptdesign'
     );


----------------
-- Ticket #17297
----------------

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
  ( 2804
  , 471
  , 'FLDemonstrativoEmpenhosPagar.php'
  , 'empenhosPagar'
  , 4
  , ''
  , 'Demonstrativo de Empenhos a Pagar'
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
    , 57
    , 7
    , 'Demonstrativo de Empenhos a Pagar'
    , 'relatorioEmpenhosPagar.rptdesign'
    );
  
