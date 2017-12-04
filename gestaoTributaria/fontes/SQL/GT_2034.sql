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
* Versao 2.03.4
*
* Fabio Bertoldi - 20141218
*
*/

----------------
-- Ticket #22518
----------------

UPDATE arrecadacao.motivo_devolucao SET descricao_resumida = 'Recusado'          WHERE cod_motivo = 1;
UPDATE arrecadacao.motivo_devolucao SET descricao_resumida = 'Ausente'           WHERE cod_motivo = 2;
UPDATE arrecadacao.motivo_devolucao SET descricao_resumida = 'End. insuficiente' WHERE cod_motivo = 3;
UPDATE arrecadacao.motivo_devolucao SET descricao_resumida = 'Dados incorretos'  WHERE cod_motivo = 4;
UPDATE arrecadacao.motivo_devolucao SET descricao_resumida = 'Falecido'          WHERE cod_motivo = 5;
UPDATE arrecadacao.motivo_devolucao SET descricao_resumida = 'Fora do prazo'     WHERE cod_motivo = 6;
UPDATE arrecadacao.motivo_devolucao SET descricao_resumida = 'Parcela já paga'   WHERE cod_motivo = 7;

