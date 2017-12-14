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
* Versao 2.03.0
*
* Fabio Bertoldi - 20140901
*
*/

----------------
-- Ticket #20764
----------------

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
     ( 2983
     , 215
     , 'FLLicencasAlvaras.php'
     , 'consultar'
     , 6
     , ''
     , 'Licenças/Alvarás'
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
     ( 5
     , 14
     , 1
     , 'Relatório de Licenças e Alvarás'
     , 'LHLicencasAlvaras.php'
     );


----------------
-- Ticket #20763
----------------

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
     ( 2984
     , 211
     , 'FLLicencas.php'
     , 'consultar'
     , 11
     , ''
     , 'Licenças'
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
     ( 5
     , 12
     , 1
     , 'Relatório de Licenças'
     , 'LHLicencas.php'
     );


----------------
-- Ticket #22132
----------------

ALTER TABLE imobiliario.licenca_baixa DROP CONSTRAINT pk_licenca_baixa;
ALTER TABLE imobiliario.licenca_baixa ADD  CONSTRAINT pk_licenca_baixa PRIMARY KEY (cod_licenca, exercicio, timestamp);


----------------
-- Ticket #16010
----------------

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
     ( 2986
     , 215
     , 'FLRelatorioDomicilioFiscal.php'
     , 'emitir'
     , 7
     , ''
     , 'Domicílio Fiscal'
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
     ( 5
     , 14
     , 2
     , 'Relatório de Domicílio Fiscal'
     , 'LHRelatorioDomicilioFiscal.php'
     );
 


----------------
-- Ticket #21930
----------------

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
     ( 2987
     , 366
     , 'FLRelatorioInscricaoDividaAtiva.php'
     , 'emitir'
     , 5
     , ''
     , 'Inscrição em Dívida Ativa'
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
     ( 5
     , 33
     , 7
     , 'Relatório de Inscrição em Dívida Ativa'
     , 'LHRelatorioInscricaoDividaAtiva.php'
     );
 

----------------
-- Ticket #21782
----------------

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
     ( 2990
     , 366
     , 'FLRelatorioDevedores.php'
     , 'emitir'
     , 13
     , ''
     , 'Relatório de Devedores'
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
     ( 5
     , 33
     , 8
     , 'Relatório de Devedores'
     , 'LHRelatorioDevedores.php'
     );
 

