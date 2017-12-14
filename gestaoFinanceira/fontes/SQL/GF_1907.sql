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
* $Id: GF_1907.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.90.7.
*/

----------------   -------------
-- Ticket #12568 - Ticket #13297
----------------   -------------

INSERT INTO administracao.acao 
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
   VALUES ( 2050
          , 82
          , 'FLManterVinculoEmpenhoContrato.php'
          , 'incluir'
          , 11
          , ''
          , 'Vincular Empenho a um Contrato' );


insert into administracao.acao 
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     values ( 2175
          , 82
          , 'FLManterVinculoEmpenhoConvenio.php'
          , 'incluir'
          , 12
          , ''
          , 'Vincular Empenho a um Convênio' );


----------------
-- Ticket #13461
----------------

 -- ticket #13003 --

delete from administracao.auditoria where cod_acao in (843,893,894);
delete from administracao.permissao where cod_acao in (843,893,894);
delete from administracao.acao where cod_acao in (843,893,894);

-- ticket #13004 --

delete from administracao.auditoria where cod_acao in (233,232,908);
delete from administracao.permissao where cod_acao in (233,232,908);
delete from administracao.acao where cod_acao in (233,232,908);

-- ticket #11655 --

INSERT INTO administracao.acao (
 cod_acao
 , cod_funcionalidade
 , nom_arquivo
 , parametro
 , ordem
 , complemento_acao
 , nom_acao
) VALUES (
   2318
 , 209
 , 'FLRestosPagarAnuladoPagamentoEstorno.php'
 , ''
 , 3
 , ''
 , 'Restos a Pagar'
);

INSERT INTO administracao.relatorio (
 cod_gestao
 , cod_modulo
 , cod_relatorio
 , nom_relatorio
 , arquivo
) VALUES (
 2
 , 10
 , 3
 , 'Restos a Pagar'
 , 'restosPagarAnuladoPagamentoEstorno.rptdesign'
);

DELETE FROM administracao.auditoria where cod_acao in (877, 878);
DELETE FROM administracao.permissao where cod_acao in (877, 878);
DELETE FROM administracao.acao where cod_acao in (877, 878);


----------------
-- Ticket #13479
----------------

INSERT INTO administracao.relatorio (
    cod_gestao , cod_modulo , cod_relatorio , nom_relatorio , arquivo
) VALUES (
    2 , 10 , 4 , 'Restos a Pagar' , 'restosPagarPagamentoEstorno.rptdesign'
); 
