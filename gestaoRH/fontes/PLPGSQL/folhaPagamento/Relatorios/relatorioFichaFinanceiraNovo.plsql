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
/* recuperarContratoServidor
 *
 * Data de Criação : 03/04/2009


 * @author Analista : Dagiane Vieira
 * @author Desenvolvedor : Diego Lemos de Souza

 * @package URBEM
 * @subpackage

 */

CREATE OR REPLACE FUNCTION recuperaRelatorioFichaFinanceiraNovo(VARCHAR,INTEGER,INTEGER,INTEGER,INTEGER,INTEGER,VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stEntidade                              ALIAS FOR $1;
    inCodConfiguracao                       ALIAS FOR $2;
    inCodComplementar                       ALIAS FOR $3;
    inCodPeriodoMovimentacaoInicial         ALIAS FOR $4;
    inCodPeriodoMovimentacaofinal           ALIAS FOR $5;
    inCodContrato                           ALIAS FOR $6;
    stOrdenacaoEventos                      ALIAS FOR $7;
    stSql                                   VARCHAR;
    stSqlAux                                VARCHAR := '';
    reRegistro                              RECORD;
    reRegistroAux                           RECORD;
    --rwOcorrenciasCalculoFichaFinanceira     colunasOcorrenciasCalculoRelatorioFichaFinanceira%ROWTYPE;

BEGIN

    stSql := '';

    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 0 THEN

        stSql := stSql ||'   SELECT cod_periodo_movimentacao
                                 , 0 as cod_configuracao
                                 , cod_complementar
                              FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
                             WHERE cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal ||'
                               AND cod_contrato = '|| inCodContrato;

        IF inCodConfiguracao = 0 THEN
            stSql := stSql ||'  AND cod_complementar = '|| inCodComplementar;
        END IF;

        stSql := stSql ||'      AND EXISTS (SELECT 1
                                             FROM folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                                            WHERE evento_complementar_calculado.cod_registro       = registro_evento_complementar.cod_registro
                                              AND evento_complementar_calculado.cod_evento         = registro_evento_complementar.cod_evento
                                              AND evento_complementar_calculado.cod_configuracao   = registro_evento_complementar.cod_configuracao
                                              AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp )
                          GROUP BY cod_periodo_movimentacao
                                 , cod_complementar';

        IF inCodConfiguracao IS NULL THEN
            stSql := stSql ||' UNION ';
        END IF;
    END IF;

    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 1 THEN

        stSql := stSql ||'   SELECT registro_evento_periodo.cod_periodo_movimentacao
                                 , 1 as cod_configuracao
                                 , null::integer as cod_complementar
                              FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
                             WHERE cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal ||'
                               AND cod_contrato = '|| inCodContrato ||'
                               AND EXISTS (SELECT 1
                                             FROM folhapagamento'|| stEntidade ||'.evento_calculado
                                            WHERE evento_calculado.cod_registro = registro_evento_periodo.cod_registro )
                          GROUP BY cod_periodo_movimentacao';

        IF inCodConfiguracao IS NULL THEN
            stSql := stSql ||' UNION ';
        END IF;
    END IF;


    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 2 THEN

        stSql := stSql ||'   SELECT cod_periodo_movimentacao
                                 , 2 as cod_configuracao
                                 , null::integer as cod_complementar
                              FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias
                             WHERE cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal ||'
                               AND cod_contrato = '|| inCodContrato ||'
                               AND EXISTS ( SELECT 1
                                              FROM folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                                             WHERE evento_ferias_calculado.cod_registro       = registro_evento_ferias.cod_registro
                                               AND evento_ferias_calculado.cod_evento         = registro_evento_ferias.cod_evento
                                               AND evento_ferias_calculado.desdobramento      = registro_evento_ferias.desdobramento
                                               AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp )
                          GROUP BY cod_periodo_movimentacao';

        IF inCodConfiguracao IS NULL THEN
            stSql := stSql ||' UNION ';
        END IF;
    END IF;


    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 3 THEN

        stSql := stSql ||'   SELECT cod_periodo_movimentacao
                                 , 3 as cod_configuracao
                                 , null::integer as cod_complementar
                              FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo
                             WHERE cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal ||'
                               AND cod_contrato = '|| inCodContrato ||'
                               AND EXISTS ( SELECT 1
                                              FROM folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                                             WHERE evento_decimo_calculado.cod_registro        = registro_evento_decimo.cod_registro
                                               AND evento_decimo_calculado.cod_evento          = registro_evento_decimo.cod_evento
                                               AND evento_decimo_calculado.desdobramento       = registro_evento_decimo.desdobramento
                                               AND evento_decimo_calculado.timestamp_registro  = registro_evento_decimo.timestamp )
                          GROUP BY cod_periodo_movimentacao';

        IF inCodConfiguracao IS NULL THEN
            stSql := stSql ||' UNION ';
        END IF;
    END IF;

    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 4 THEN

        stSql := stSql ||'   SELECT cod_periodo_movimentacao::integer AS cod_periodo_movimentacao
                                 , 4 as cod_configuracao
                                 , null::integer as cod_complementar
                              FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                             WHERE cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal ||'
                               AND cod_contrato = '|| inCodContrato ||'
                               AND EXISTS ( SELECT 1
                                              FROM folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                                             WHERE evento_rescisao_calculado.cod_registro        = registro_evento_rescisao.cod_registro
                                               AND evento_rescisao_calculado.cod_evento          = registro_evento_rescisao.cod_evento
                                               AND evento_rescisao_calculado.desdobramento       = registro_evento_rescisao.desdobramento
                                               AND evento_rescisao_calculado.timestamp_registro  = registro_evento_rescisao.timestamp )
                          GROUP BY cod_periodo_movimentacao';
    END IF;


    stSql := 'SELECT *
                   , publico.fn_mes_extenso(periodo_movimentacao.dt_final)||''/''|| to_char(periodo_movimentacao.dt_final, ''yyyy'') AS descricao_periodo
                   , CASE WHEN ocorrencias_calculo_periodo.cod_configuracao <> 0 THEN
                          (SELECT ''Folha ''|| descricao FROM folhapagamento'|| stEntidade ||'.configuracao_evento WHERE cod_configuracao = ocorrencias_calculo_periodo.cod_configuracao)
                     ELSE
                          ''Folha Complementar - ''|| ocorrencias_calculo_periodo.cod_configuracao
                     END AS descricao_configuracao
                FROM (
                       '|| stSql ||'
                     ) as ocorrencias_calculo_periodo
          INNER JOIN folhapagamento'|| stEntidade ||'.periodo_movimentacao
                  ON periodo_movimentacao.cod_periodo_movimentacao = ocorrencias_calculo_periodo.cod_periodo_movimentacao
            ORDER BY ocorrencias_calculo_periodo.cod_periodo_movimentacao
                   , ocorrencias_calculo_periodo.cod_configuracao
                   , ocorrencias_calculo_periodo.cod_complementar';

    --CREATE TABLE tmp_periodo (
    --    cod_periodo_movimentacao    INTEGER,
    --    cod_configuracao            INTEGER,
    --    cod_complementar            INTEGER,
    --    descricao_periodo           VARCHAR,
    --    descricao_configuracao      VARCHAR
    --)

    FOR reRegistro IN EXECUTE stSql LOOP
        --INSERT INTO tmp_periodo (cod_periodo_movimentacao, cod_configuracao, cod_complementar, descricao_periodo, descricao_configuracao)
        --    VALUES (reRegistro.cod_periodo_movimentacao, reRegistro.cod_configuracao, reRegistro.cod_complementar, reRegistro.descricao_periodo, reRegistro.descricao_configuracao);

            stSqlAux := 'SELECT  codigo as codigo_evento
                               , descricao as descricao_evento
                               , natureza::varchar as natureza_evento
                               , desdobramento_texto as desdobramento
                               , quantidade
                               , CASE WHEN natureza = ''P'' THEN
                                   valor
                               ELSE
                                   0
                               END AS proventos
                               , CASE WHEN natureza = ''D'' THEN
                                   valor
                               ELSE
                                   0
                               END AS descontos
                               , CASE WHEN natureza IN (''B'',''I'') THEN
                                   valor
                               ELSE
                                   0
                               END AS valor
                               , CASE WHEN natureza IN (''P'',''D'') THEN
                                   1
                               ELSE
                                   2
                               END AS ordem_por_natureza
                               , 0 AS cod_periodo_movimentacao
                               , 0 AS cod_configuracao
                               , 0 AS cod_complementar
                               , ''''::varchar AS descricao_periodo
                               , ''''::varchar AS descricao_configuracao
                           FROM recuperarEventosCalculados('||reRegistro.cod_configuracao||',
                                                           '||reRegistro.cod_periodo_movimentacao||',
                                                           '||inCodContrato||',';

                            IF (reRegistro.cod_complementar != NULL) THEN
                                stSqlAux := stSqlAux||reRegistro.cod_complementar||',';
                            ELSE
                                stSqlAux := stSqlAux||'NULL,';
                            END IF;

                stSqlAux := stSqlAux||'
                                                           '''||stEntidade||''',
                                                           '''||stOrdenacaoEventos||''')
                           ORDER BY desdobramento DESC ,ordem_por_natureza,'||stOrdenacaoEventos||'';

        FOR reRegistroAux IN EXECUTE stSqlAux LOOP
            reRegistroAux.cod_periodo_movimentacao  := reRegistro.cod_periodo_movimentacao;
            reRegistroAux.cod_configuracao          := reRegistro.cod_configuracao;
            reRegistroAux.cod_complementar          := reRegistro.cod_complementar;
            reRegistroAux.descricao_periodo         := reRegistro.descricao_periodo;
            reRegistroAux.descricao_configuracao    := reRegistro.descricao_configuracao;

            RETURN NEXT reRegistroAux;
        END LOOP;
    END LOOP;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';
