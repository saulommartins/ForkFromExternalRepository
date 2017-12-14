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
* $Id:  $
*
* Versão 1.90.6
*/

update administracao.acao set ordem = 20 where cod_acao = 1574;
update administracao.acao set ordem = 30 where cod_acao = 1575;
update administracao.acao set ordem = 40 where cod_acao = 1589;
update administracao.acao set ordem = 50 where cod_acao = 1590;
update administracao.acao set ordem = 60 where cod_acao = 2185;
update administracao.acao set ordem = 20 where cod_acao = 103 ;

Alter table patrimonio.bem add dt_incorporacao date ;

INSERT INTO administracao.relatorio
            (cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     VALUES (3
          , 37
          , 2
          , 'Julgamento das Propostas'
          , 'julgamentoPropostas.rptdesign') ;


