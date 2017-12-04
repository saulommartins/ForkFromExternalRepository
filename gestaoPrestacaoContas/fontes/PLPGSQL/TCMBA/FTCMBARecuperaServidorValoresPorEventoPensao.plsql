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
/* recuperaServidorValoresPorEventoPensao
 * Data de Criação : 26/10/2015
 * @author Analista : Dagiane Vieira
 * @author Desenvolvedor : Michel Teixeira
 * $Id: FTCMBARecuperaServidorValoresPorEventoPensao.plsql 63946 2015-11-10 21:10:32Z michel $
*/

CREATE OR REPLACE FUNCTION tcmba.recuperaServidorValoresPorEventoPensao(INTEGER, VARCHAR, VARCHAR) RETURNS SETOF RECORD as $$
DECLARE
    inCodPeriodoMovimentacao ALIAS FOR $1;
    stEntidade               ALIAS FOR $2;
    stEvento                 ALIAS FOR $3;
    stSQL                    VARCHAR :='';
    reRecord                 RECORD;
BEGIN
    stSQL := 'SELECT valor_evento.cod_periodo_movimentacao
                   , valor_evento.cod_contrato
                   , sum(valor_evento.valor)
                FROM (
                       SELECT registro_evento_periodo.cod_periodo_movimentacao
                            , registro_evento_periodo.cod_contrato
                            , sum(evento_calculado.valor) as valor
                         FROM folhapagamento'||stEntidade||'.registro_evento_periodo

                   INNER JOIN folhapagamento'||stEntidade||'.registro_evento
                           ON registro_evento.cod_registro = registro_evento_periodo.cod_registro

                   INNER JOIN folhapagamento'||stEntidade||'.ultimo_registro_evento
                           ON ultimo_registro_evento.cod_registro   = registro_evento.cod_registro
                          AND ultimo_registro_evento.timestamp      = registro_evento.timestamp
                          AND ultimo_registro_evento.cod_evento     = registro_evento.cod_evento
                          AND ultimo_registro_evento.cod_evento     IN ('||stEvento||')

                   INNER JOIN folhapagamento'||stEntidade||'.evento_calculado
                           ON evento_calculado.timestamp_registro   = ultimo_registro_evento.timestamp
                          AND evento_calculado.cod_registro         = ultimo_registro_evento.cod_registro
                          AND evento_calculado.cod_evento           = ultimo_registro_evento.cod_evento

                        WHERE registro_evento_periodo.cod_periodo_movimentacao  = '||inCodPeriodoMovimentacao||'

                     GROUP BY registro_evento_periodo.cod_periodo_movimentacao
                            , registro_evento_periodo.cod_contrato

                        UNION

                       SELECT contrato_servidor_complementar.cod_periodo_movimentacao
                            , contrato_servidor_complementar.cod_contrato
                            , sum(evento_complementar_calculado.valor) as valor
                         FROM folhapagamento'||stEntidade||'.contrato_servidor_complementar

                   INNER JOIN folhapagamento'||stEntidade||'.registro_evento_complementar
                           ON registro_evento_complementar.cod_periodo_movimentacao = contrato_servidor_complementar.cod_periodo_movimentacao
                          AND registro_evento_complementar.cod_complementar         = contrato_servidor_complementar.cod_complementar
                          AND registro_evento_complementar.cod_contrato             = contrato_servidor_complementar.cod_contrato

                   INNER JOIN folhapagamento'||stEntidade||'.ultimo_registro_evento_complementar
                           ON ultimo_registro_evento_complementar.cod_registro      = registro_evento_complementar.cod_registro
                          AND ultimo_registro_evento_complementar.timestamp         = registro_evento_complementar.timestamp
                          AND ultimo_registro_evento_complementar.cod_evento        = registro_evento_complementar.cod_evento
                          AND ultimo_registro_evento_complementar.cod_configuracao  = registro_evento_complementar.cod_configuracao
                          AND ultimo_registro_evento_complementar.cod_evento        IN ('||stEvento||')

                   INNER JOIN folhapagamento'||stEntidade||'.evento_complementar_calculado
                           ON evento_complementar_calculado.timestamp_registro  = ultimo_registro_evento_complementar.timestamp
                          AND evento_complementar_calculado.cod_registro        = ultimo_registro_evento_complementar.cod_registro
                          AND evento_complementar_calculado.cod_evento          = ultimo_registro_evento_complementar.cod_evento
                          AND evento_complementar_calculado.cod_configuracao    = ultimo_registro_evento_complementar.cod_configuracao

                        WHERE contrato_servidor_complementar.cod_periodo_movimentacao   = '||inCodPeriodoMovimentacao||'

                     GROUP BY contrato_servidor_complementar.cod_periodo_movimentacao
                            , contrato_servidor_complementar.cod_contrato

                        UNION

                       SELECT registro_evento_ferias.cod_periodo_movimentacao
                            , registro_evento_ferias.cod_contrato
                            , sum(evento_ferias_calculado.valor) AS valor

                         FROM folhapagamento'||stEntidade||'.registro_evento_ferias

                   INNER JOIN folhapagamento'||stEntidade||'.ultimo_registro_evento_ferias
                           ON ultimo_registro_evento_ferias.cod_registro    = registro_evento_ferias.cod_registro
                          AND ultimo_registro_evento_ferias.timestamp       = registro_evento_ferias.timestamp
                          AND ultimo_registro_evento_ferias.cod_evento      = registro_evento_ferias.cod_evento
                          AND ultimo_registro_evento_ferias.desdobramento   = registro_evento_ferias.desdobramento

                   INNER JOIN folhapagamento'||stEntidade||'.evento_ferias_calculado
                           ON evento_ferias_calculado.timestamp_registro    = ultimo_registro_evento_ferias.timestamp
                          AND evento_ferias_calculado.cod_registro          = ultimo_registro_evento_ferias.cod_registro
                          AND evento_ferias_calculado.cod_evento            = ultimo_registro_evento_ferias.cod_evento
                          AND evento_ferias_calculado.desdobramento         = ultimo_registro_evento_ferias.desdobramento 

                        WHERE registro_evento_ferias.cod_periodo_movimentacao   = '||inCodPeriodoMovimentacao||'
                          AND registro_evento_ferias.cod_evento                 IN ('||stEvento||')

                     GROUP BY registro_evento_ferias.cod_periodo_movimentacao
                            , registro_evento_ferias.cod_contrato

                        UNION

                       SELECT registro_evento_decimo.cod_periodo_movimentacao
                            , registro_evento_decimo.cod_contrato
                            , sum(evento_decimo_calculado.valor) AS valor

                         FROM folhapagamento'||stEntidade||'.registro_evento_decimo

                   INNER JOIN folhapagamento'||stEntidade||'.periodo_movimentacao
                           ON registro_evento_decimo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao

                   INNER JOIN folhapagamento'||stEntidade||'.ultimo_registro_evento_decimo
                           ON ultimo_registro_evento_decimo.cod_registro    = registro_evento_decimo.cod_registro
                          AND ultimo_registro_evento_decimo.timestamp       = registro_evento_decimo.timestamp
                          AND ultimo_registro_evento_decimo.cod_evento      = registro_evento_decimo.cod_evento
                          AND ultimo_registro_evento_decimo.desdobramento   = registro_evento_decimo.desdobramento

                   INNER JOIN folhapagamento'||stEntidade||'.evento_decimo_calculado
                           ON evento_decimo_calculado.timestamp_registro    = ultimo_registro_evento_decimo.timestamp
                          AND evento_decimo_calculado.cod_registro          = ultimo_registro_evento_decimo.cod_registro
                          AND evento_decimo_calculado.cod_evento            = ultimo_registro_evento_decimo.cod_evento
                          AND evento_decimo_calculado.desdobramento         = ultimo_registro_evento_decimo.desdobramento

                        WHERE registro_evento_decimo.cod_periodo_movimentacao   = '||inCodPeriodoMovimentacao||'
                          AND registro_evento_decimo.cod_evento                 IN ('||stEvento||')

                     GROUP BY registro_evento_decimo.cod_periodo_movimentacao
                            , registro_evento_decimo.cod_contrato
                     ) AS valor_evento

             GROUP BY valor_evento.cod_periodo_movimentacao
                    , valor_evento.cod_contrato ';

    FOR reRecord IN EXECUTE stSQL
    LOOP
        RETURN NEXT reRecord;
    END LOOP;

END;
$$ LANGUAGE 'plpgsql';