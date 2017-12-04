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
* $Id: GF_1908.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.90.8.
*/

----------------
-- Ticket #13501
----------------

alter table contabilidade.plano_analitica add column natureza_saldo char(1);

update contabilidade.plano_analitica set natureza_saldo = 'D' where cod_conta in (select DISTINCT pc.cod_conta from contabilidade.plano_conta pc join contabilidade.plano_analitica pa on (pc.cod_conta=pa.cod_conta) where pc.cod_estrutural like '1%');

update contabilidade.plano_analitica set natureza_saldo = 'C' where cod_conta in (select DISTINCT pc.cod_conta from contabilidade.plano_conta pc join contabilidade.plano_analitica pa on (pc.cod_conta=pa.cod_conta) where pc.cod_estrutural like '2%');

update contabilidade.plano_analitica set natureza_saldo = 'D' where cod_conta in (select DISTINCT pc.cod_conta from contabilidade.plano_conta pc join contabilidade.plano_analitica pa on (pc.cod_conta=pa.cod_conta) where pc.cod_estrutural like '3%');

update contabilidade.plano_analitica set natureza_saldo = 'C' where cod_conta in (select DISTINCT pc.cod_conta from contabilidade.plano_conta pc join contabilidade.plano_analitica pa on (pc.cod_conta=pa.cod_conta) where pc.cod_estrutural like '4%');

update contabilidade.plano_analitica set natureza_saldo = 'D' where cod_conta in (select DISTINCT pc.cod_conta from contabilidade.plano_conta pc join contabilidade.plano_analitica pa on (pc.cod_conta=pa.cod_conta) where pc.cod_estrutural like '5%');

update contabilidade.plano_analitica set natureza_saldo = 'C' where cod_conta in (select DISTINCT pc.cod_conta from contabilidade.plano_conta pc join contabilidade.plano_analitica pa on (pc.cod_conta=pa.cod_conta) where pc.cod_estrutural like '6%');

update contabilidade.plano_analitica set natureza_saldo = 'D' where cod_conta in (select DISTINCT pc.cod_conta from contabilidade.plano_conta pc join contabilidade.plano_analitica pa on (pc.cod_conta=pa.cod_conta) where pc.cod_estrutural like '9%');

alter table contabilidade.plano_analitica alter column natureza_saldo SET not null;
