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
--/**
--    * Função PLSQL
--    * Data de Criação: 00/00/0000
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Vandré Miguel Ramos
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23402 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-20 16:57:16 -0300 (Qua, 20 Jun 2007) $
--
--    * Casos de uso: uc-04.05.09
--    * Casos de uso: uc-04.05.10
--*/

CREATE OR REPLACE FUNCTION pegaPagamentoUmTercoFerias(INTEGER,INTEGER) RETURNS VARCHAR AS $$
DECLARE
    inCodContrato             ALIAS FOR $1;
    inCodPeriodoMovimentacao  ALIAS FOR $2;
    stSql                     VARCHAR := '';
    crCursor                  REFCURSOR;
    boPaga13                  BOOLEAN := FALSE;
    stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
BEGIN

    boPaga13 := selectIntoBoolean(' SELECT pagar_13 
                            FROM pessoal'||stEntidade||'.ferias                 as ferias
                            JOIN pessoal'||stEntidade||'.lancamento_ferias      as lancamento_ferias
                              ON ferias.cod_ferias = lancamento_ferias.cod_ferias
          
                            JOIN pessoal'||stEntidade||'.forma_pagamento_ferias  as forma_pagamento_ferias
                              ON forma_pagamento_ferias.cod_forma = ferias.cod_forma
          
                           WHERE cod_contrato = '||inCodContrato||'
                             AND ((SELECT SUBSTR(dt_final::VARCHAR,1,7) as dt_final
                                    FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                                   WHERE cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||')
                                 BETWEEN SUBSTR(lancamento_ferias.dt_inicio::VARCHAR,1,7)
                                     AND SUBSTR(lancamento_ferias.dt_fim::VARCHAR,1,7)
                             OR (SELECT SUBSTR(dt_final::VARCHAR,1,7) as dt_final
                                    FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                                   WHERE cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||') = lancamento_ferias.ano_competencia||''-''||lancamento_ferias.mes_competencia)');
             
    IF boPaga13 IS NULL THEN
        boPaga13 := FALSE;
    END IF;

    RETURN boPaga13;
END;
$$ LANGUAGE 'plpgsql';
