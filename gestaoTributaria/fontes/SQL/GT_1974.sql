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
* $Id:  $
*
* Versão 1.97.4
*/

--correcao para Ticket #14881
grant all on fiscalizacao.notificacao_termo to group urbem;
grant all on fiscalizacao.notificacao_termo_num_notificacao_seq to group urbem;


----------------
-- Ticket #14978
----------------

INSERT INTO administracao.configuracao
          ( cod_modulo
          , exercicio
          , parametro
          , valor
          )
     VALUES ( 25
          , '2009'
          , 'vias_nota_avulsa'
          , '2'
          );


---------------------------------------------------------------
-- CORRECAO DA COLUNA timestamp EM imobiliario.matricula_imovel
---------------------------------------------------------------

ALTER TABLE imobiliario.matricula_imovel ALTER COLUMN timestamp SET DEFAULT ('now'::text)::timestamp(3) with time zone;
