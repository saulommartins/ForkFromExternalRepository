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
* Versao 2.01.6
*
* Fabio Bertoldi - 20130508
*
*/

----------------
-- Ticket #19989
----------------

UPDATE administracao.modelo_documento SET nome_arquivo_agt = 'termoInscricaoDAUrbem.agt'     WHERE nome_arquivo_agt = 'termoInscricaoDASiamWeb.agt';
UPDATE administracao.modelo_documento SET nome_arquivo_agt = 'certidaoDAUrbem.agt'           WHERE nome_arquivo_agt = 'certidaoDASiamWeb.agt';
UPDATE administracao.modelo_documento SET nome_arquivo_agt = 'memorialCalculoDAUrbem.agt'    WHERE nome_arquivo_agt = 'memorialCalculoDASiamWeb.agt';
UPDATE administracao.modelo_documento SET nome_arquivo_agt = 'termoConsolidacaoDAUrbem.agt'  WHERE nome_arquivo_agt = 'termoConsolidacaoDASiamWeb.agt';
UPDATE administracao.modelo_documento SET nome_arquivo_agt = 'notificacaoDAUrbem.agt'        WHERE nome_arquivo_agt = 'notificacaoDASiamWeb.agt';
UPDATE administracao.modelo_documento SET nome_arquivo_agt = 'termoParcelamentoDAUrbem.agt'  WHERE nome_arquivo_agt = 'termoParcelamentoDASiamWeb.agt';
UPDATE administracao.modelo_documento SET nome_arquivo_agt = 'remissaoAutomaticaDAUrbem.odt' WHERE nome_arquivo_agt = 'remissaoAutomaticaDASiamWeb.odt';

