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
/**
    * Busca valor da meta por ano da PPA
    * Data de Criação: 08/01/2015

    * @author Desenvolvedor: Evandro Melos

    * $Id: ppaBuscaValorMetaAnoPPA.plsql 61337 2015-01-08 16:46:28Z evandro $
*/

CREATE OR REPLACE FUNCTION ppa.busca_valor_meta_ano_ppa(INTEGER, VARCHAR, TIMESTAMP) RETURNS NUMERIC AS $$
DECLARE
    inCodAcao           ALIAS FOR $1;
    stAno               ALIAS FOR $2;
    ultimoTimestampAcao ALIAS FOR $3;
    nuRetorno           NUMERIC;
    
BEGIN
    
    SELECT SUM(quantidade)
        INTO nuRetorno
        FROM ppa.acao_quantidade 
        WHERE acao_quantidade.cod_acao = inCodAcao
        AND acao_quantidade.ano = stAno
        AND acao_quantidade.timestamp_acao_dados = ultimoTimestampAcao;

    RETURN nuRetorno;

END;

$$ LANGUAGE 'plpgsql';
