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
/* recuperarSituacaoDoContrato
 *
 * Data de Criação : 15/01/2009


 * @author Analista : Dagiane
 * @author Desenvolvedor : Rafael Garbin

 * @package URBEM
 * @subpackage

 $Id: $
 */

-- A - Ativo
-- P - Aposentado
-- R - Rescindido
-- E - Pensionista

CREATE OR REPLACE FUNCTION recuperarSituacaoDoContrato(INTEGER, INTEGER, VARCHAR) RETURNS VARCHAR as $$
DECLARE
    inCodContrato                   ALIAS FOR $1;
    inCodPeriodoMovimentacao        ALIAS FOR $2;
    stEntidade                      ALIAS FOR $3;
    stSQL                           VARCHAR:='';
    stRetorno                       VARCHAR:='';
    stFiltro                        VARCHAR:='';
BEGIN

    -- Caso o IncodPeriodoMovimentacao = 0 busca ultimo periodo

    IF inCodPeriodoMovimentacao = 0 THEN
        stSql := 'SELECT cod_periodo_movimentacao
                    FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                ORDER BY cod_periodo_movimentacao desc
                   LIMIT 1';
        stFiltro := ' JOIN (
                SELECT MAX(timestamp) AS timestamp
                            , cod_contrato
                         FROM pessoal'||stEntidade||'.contrato_servidor_situacao
                        WHERE cod_contrato = contrato_servidor_situacao.cod_contrato
                     GROUP BY cod_contrato
                       ) AS max_timestamp
                   ON max_timestamp.timestamp = contrato_servidor_situacao.timestamp
                  AND max_timestamp.cod_contrato = contrato_servidor_situacao.cod_contrato ';           
        inCodPeriodoMovimentacao = selectIntoInteger(stSql);
    END IF;

    stSql := '
        SELECT contrato_servidor_situacao.situacao
          FROM pessoal'||stEntidade||'.contrato_servidor_situacao
          '||stFiltro||'
         WHERE contrato_servidor_situacao.cod_contrato = '||inCodContrato||'
           AND contrato_servidor_situacao.cod_periodo_movimentacao <= '||inCodPeriodoMovimentacao||'
           ORDER BY contrato_servidor_situacao.timestamp DESC
           LIMIT 1';
           
    -- Verifica se é pensionista
    stRetorno := selectIntoVarchar(stSql);

    RETURN stRetorno;

END;
$$ LANGUAGE 'plpgsql';