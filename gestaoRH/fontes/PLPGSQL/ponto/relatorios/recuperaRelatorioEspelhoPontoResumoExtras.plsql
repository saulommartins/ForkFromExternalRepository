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
/* recuperaRelatorioEspelhoPontoResumoExtras
 * 
 * Data de Criação   : 23/10/2008


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */
CREATE OR REPLACE FUNCTION recuperaRelatorioEspelhoPontoResumoExtras(INTEGER,VARCHAR) RETURNS SETOF colunasRelatorioCartaoPontoResumoExtra AS $$
DECLARE
    inCodConfiguracao   ALIAS FOR $1;
    stEntidade          ALIAS FOR $2;
    stSql               VARCHAR;
    reFaixas            RECORD;
    reRelatorio         RECORD;
    reDias              RECORD;
    stHora              VARCHAR:='00:00';
    stHorasMensal       VARCHAR:='00:00';    
    stHorasSemana       VARCHAR:='00:00';    
    stHorasDia          VARCHAR:='00:00';   
    stUltimaFaixa       VARCHAR:='00:00'; 
    arTemp              VARCHAR[];
    arExtras            VARCHAR[][];
    arExtrasSemana      VARCHAR[];    
    stDias              VARCHAR;
    stCodDias           VARCHAR := '';
    stCodFixas          VARCHAR := '0';
    inIndex             INTEGER:=1;
    inContador          INTEGER:=0;
    rwExtras            colunasRelatorioCartaoPontoResumoExtra%ROWTYPE;
BEGIN
    --MONTA ARRAY PARA RETORNO
    stSql := '    SELECT faixas_horas_extra.*
                    FROM ponto'|| stEntidade ||'.faixas_horas_extra
              INNER JOIN ponto'|| stEntidade ||'.configuracao_relogio_ponto
                      ON configuracao_relogio_ponto.cod_configuracao = faixas_horas_extra.cod_configuracao
                     AND configuracao_relogio_ponto.ultimo_timestamp = faixas_horas_extra.timestamp
                WHERE NOT EXISTS (SELECT 1
                                    FROM ponto'|| stEntidade ||'.configuracao_relogio_ponto_exclusao
                                    WHERE configuracao_relogio_ponto_exclusao.cod_configuracao = configuracao_relogio_ponto.cod_configuracao)
                    AND configuracao_relogio_ponto.cod_configuracao = '|| inCodConfiguracao;
    FOR reFaixas IN EXECUTE stSql LOOP  
        stSql := '     SELECT dias_turno.nom_dia
                         FROM ponto'|| stEntidade ||'.faixas_dias
                   INNER JOIN pessoal'|| stEntidade ||'.dias_turno
                           ON faixas_dias.cod_dia = dias_turno.cod_dia
                        WHERE cod_configuracao = '|| reFaixas.cod_configuracao ||'
                          AND cod_faixa = '|| reFaixas.cod_faixa ||'   
                          AND timestamp = '|| quote_literal(reFaixas.timestamp) ||' ';      
        stDias := '';
        FOR reDias IN EXECUTE stSql LOOP
            stDias := stDias || trim(reDias.nom_dia) ||'/';
        END LOOP;

        arTemp[1] := substr(stDias,1,char_length(stDias)-1);
        IF reFaixas.calculo_horas_extra = 'D' THEN
            arTemp[2] := 'Diário';
        END IF;
        IF reFaixas.calculo_horas_extra = 'S' THEN
            arTemp[2] := 'Semanal';
        END IF;
        IF reFaixas.calculo_horas_extra = 'M' THEN
            arTemp[2] := 'Mensal';
        END IF;
        arTemp[3] := reFaixas.horas;
        arTemp[4] := reFaixas.percentual ||' %';
        arTemp[5] := '00:00';
        arExtras[reFaixas.cod_faixa] := arTemp;
    END LOOP;


    arExtrasSemana[1] := '00:00';
    arExtrasSemana[2] := '00:00';
    arExtrasSemana[3] := '00:00';
    arExtrasSemana[4] := '00:00';
    arExtrasSemana[5] := '00:00';
    arExtrasSemana[6] := '00:00';
    arExtrasSemana[7] := '00:00';
    arExtrasSemana[8] := '00:00';
    
    stSql := '    SELECT relatorio_espelho_ponto.* 
                       , (relatorio_espelho_ponto.extras::interval + relatorio_espelho_ponto.ext_not::interval) as extras
                       , dias_turno.cod_dia
                    FROM ponto'|| stEntidade ||'.relatorio_espelho_ponto 
              INNER JOIN pessoal'|| stEntidade ||'.dias_turno
                      ON trim(lower(replace(dias_turno.nom_dia,''-feira'',''''))) = trim(lower(dia))
                ORDER BY relatorio_espelho_ponto.data';
    FOR reRelatorio IN EXECUTE stSql LOOP   
--         IF reRelatorio.extras is not null THEN  
--         END IF;
        IF reRelatorio.extras IS NOT NULL THEN    
            --stHora := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHora) ||' + INTERVAL '|| quote_literal(COALESCE(reRelatorio.extras,'00:00')) ||' ');          
            arExtrasSemana[reRelatorio.cod_dia] := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(arExtrasSemana[reRelatorio.cod_dia]) ||' + INTERVAL '|| quote_literal(COALESCE(reRelatorio.extras,'00:00')) ||' ');

            --Dias as semana que tiveram hora extra
            stCodDias := stCodDias || reRelatorio.cod_dia ||',';
        ELSE
            stHora := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHora) ||' + INTERVAL ''00:00'' ');          
        END IF;
        
        --PROCESSAMENTO DAS HORAS EXTRAS DIÁRIAS
        stSql := '    SELECT faixas_horas_extra.*                           
                        FROM ponto'|| stEntidade ||'.faixas_horas_extra
                  INNER JOIN ponto'|| stEntidade ||'.configuracao_relogio_ponto
                          ON configuracao_relogio_ponto.cod_configuracao = faixas_horas_extra.cod_configuracao
                         AND configuracao_relogio_ponto.ultimo_timestamp = faixas_horas_extra.timestamp
                  INNER JOIN ponto'|| stEntidade ||'.faixas_dias
                          ON faixas_dias.cod_configuracao = faixas_horas_extra.cod_configuracao
                         AND faixas_dias.cod_faixa = faixas_horas_extra.cod_faixa
                         AND faixas_dias.timestamp = faixas_horas_extra.timestamp
                   WHERE NOT EXISTS (SELECT 1
                                       FROM ponto'|| stEntidade ||'.configuracao_relogio_ponto_exclusao
                                       WHERE configuracao_relogio_ponto_exclusao.cod_configuracao = configuracao_relogio_ponto.cod_configuracao)
                         AND configuracao_relogio_ponto.cod_configuracao = '|| inCodConfiguracao ||'
                         AND faixas_dias.cod_dia = '|| reRelatorio.cod_dia ||'
                         AND faixas_horas_extra.calculo_horas_extra = ''D''
                    ORDER BY faixas_horas_extra.percentual
                           , faixas_horas_extra.horas';
--         stUltimaFaixa := '';
        IF reRelatorio.extras IS NOT NULL THEN
            stHorasDia := reRelatorio.extras;
            FOR reFaixas IN EXECUTE stSql LOOP
                IF reFaixas.horas is not null THEN
                END IF;
                inContador := 0;
                --stHorasDia := '00:00';
                stSql := 'SELECT faixas_dias.cod_dia
                            FROM ponto'|| stEntidade ||'.faixas_dias
                        WHERE faixas_dias.cod_configuracao = '|| reFaixas.cod_configuracao ||'
                            AND faixas_dias.cod_faixa = '|| reFaixas.cod_faixa ||'
                            AND faixas_dias.cod_dia = '|| reRelatorio.cod_dia ||'
                            AND faixas_dias.timestamp = '|| quote_literal(reFaixas.timestamp) ||' ';
--                 FOR reDias IN EXECUTE stSql LOOP
--                     --stHorasDia := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHorasDia) ||' + INTERVAL '|| quote_literal(reRelatorio.extras) ||' ');
--                     inContador := inContador  + 1;
--                 END LOOP;
            
                IF (reFaixas.horas <= stHorasDia AND stHorasDia != '00:00:00' AND substr(trim(stHorasDia),0,2) != '-') 
                OR (stHorasDia <= reFaixas.horas AND stHorasDia != '00:00:00' AND substr(trim(stHorasDia),0,2) != '-')  THEN
                    arTemp := arExtras[reFaixas.cod_faixa];
                    IF reFaixas.horas > stHorasDia THEN
                        arTemp[5] := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(arTemp[5]) ||' + INTERVAL '|| quote_literal(stHorasDia) ||' ');
    
                        arExtras[reFaixas.cod_faixa] := arTemp;
                        stHorasDia := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHorasDia) ||' - INTERVAL '|| quote_literal(stHorasDia) ||' ');
                    ELSE
                        arTemp[5] := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(arTemp[5]) ||' + INTERVAL '|| quote_literal(reFaixas.horas) ||' ');
    
                        arExtras[reFaixas.cod_faixa] := arTemp;
                        stHorasDia := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHorasDia) ||' - INTERVAL '|| quote_literal(reFaixas.horas) ||' ');
                    END IF;
                END IF;
    
--                 FOR reDias IN EXECUTE stSql LOOP
--                     stHorasDia := selectIntoVarchar('SELECT INTERVAL \''|| stHorasDia ||'\' / '|| inContador ||' ');
--                 END LOOP;
    --             IF stHorasDia = '00:00:00' OR substr(trim(stHorasDia),0,2) = '-' THEN
                    --EXIT;
    --             END IF;
                stUltimaFaixa := reFaixas.horas;
            END LOOP;
            stHorasSemana := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHorasDia) ||' + INTERVAL '|| quote_literal(stHorasSemana) ||' ');
            stHorasMensal := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHorasDia) ||' + INTERVAL '|| quote_literal(stHorasMensal) ||' ');
        END IF;

        --PROCESSAMENTO DAS HORAS EXTRAS SEMANAIS
        IF char_length(stCodDias) > 0 AND (reRelatorio.cod_dia = 7 OR reRelatorio.data = to_char(last_day(to_date(reRelatorio.data,'dd/mm/yyyy')),'dd/mm/yyyy')) THEN  
            stSql := '    SELECT faixas_horas_extra.*
                            FROM ponto'|| stEntidade ||'.faixas_horas_extra
                      INNER JOIN ponto'|| stEntidade ||'.configuracao_relogio_ponto
                              ON configuracao_relogio_ponto.cod_configuracao = faixas_horas_extra.cod_configuracao
                             AND configuracao_relogio_ponto.ultimo_timestamp = faixas_horas_extra.timestamp
                      INNER JOIN ponto'|| stEntidade ||'.faixas_dias
                              ON faixas_dias.cod_configuracao = faixas_horas_extra.cod_configuracao
                             AND faixas_dias.cod_faixa = faixas_horas_extra.cod_faixa
                             AND faixas_dias.timestamp = faixas_horas_extra.timestamp
                       WHERE NOT EXISTS (SELECT 1
                                           FROM ponto'|| stEntidade ||'.configuracao_relogio_ponto_exclusao
                                           WHERE configuracao_relogio_ponto_exclusao.cod_configuracao = configuracao_relogio_ponto.cod_configuracao)
                             AND configuracao_relogio_ponto.cod_configuracao = '|| inCodConfiguracao ||'
                             AND faixas_dias.cod_dia IN ('|| substr(stCodDias,1,char_length(stCodDias)-1) ||')
                             AND faixas_horas_extra.calculo_horas_extra = ''S''
                        ORDER BY faixas_horas_extra.percentual';
            FOR reFaixas IN EXECUTE stSql LOOP
                --Verificação dos dias que devem compor as horas extras desta faixa
                inContador := 0;
                stSql := 'SELECT faixas_dias.cod_dia
                            FROM ponto'|| stEntidade ||'.faixas_dias
                           WHERE faixas_dias.cod_configuracao = '|| reFaixas.cod_configuracao ||'
                             AND faixas_dias.cod_faixa = '|| reFaixas.cod_faixa ||'
                             AND faixas_dias.timestamp = '|| quote_literal(reFaixas.timestamp) ||' ';
--                 FOR reDias IN EXECUTE stSql LOOP
-- --                     stHorasSemana := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHorasSemana) ||' + INTERVAL '|| quote_literal(arExtrasSemana[reDias.cod_dia]) ||' ');
--                     inContador := inContador  + 1;
--                 END LOOP;
                IF (reFaixas.horas <= stHorasSemana AND stHorasSemana != '00:00:00' AND substr(trim(stHorasSemana),0,2) != '-') 
                OR (stHorasSemana <= reFaixas.horas AND stHorasSemana != '00:00:00' AND substr(trim(stHorasSemana),0,2) != '-')  THEN
                    arTemp := arExtras[reFaixas.cod_faixa];
                    IF reFaixas.horas > stHorasSemana THEN
                        arTemp[5] := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(arTemp[5]) ||' + INTERVAL '|| quote_literal(stHorasSemana) ||' ');
                        arExtras[reFaixas.cod_faixa] := arTemp;
                        stHorasSemana := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHorasSemana) ||' - INTERVAL '|| quote_literal(stHorasSemana) ||' ');
                    ELSE
                        arTemp[5] := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(arTemp[5]) ||' + INTERVAL '|| quote_literal(reFaixas.horas) ||' ');
                        arExtras[reFaixas.cod_faixa] := arTemp;
                        stHorasSemana := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHorasSemana) ||' - INTERVAL '|| quote_literal(reFaixas.horas) ||' ');
                    END IF;
                END IF;
--                 FOR reDias IN EXECUTE stSql LOOP
--                     arExtrasSemana[reDias.cod_dia] := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHorasSemana) ||' / '|| inContador ||' ');
--                 END LOOP;
            END LOOP;
            stCodDias := '';
            stHorasSemana := '00:00';
        END IF;
-- 
        --PROCESSAMENTO DAS HORAS EXTRAS MENSAL
        IF reRelatorio.data = to_char(last_day(to_date(reRelatorio.data,'dd/mm/yyyy')),'dd/mm/yyyy') THEN  
            stSql := '    SELECT faixas_horas_extra.*
                            FROM ponto'|| stEntidade ||'.faixas_horas_extra
                      INNER JOIN ponto'|| stEntidade ||'.configuracao_relogio_ponto
                              ON configuracao_relogio_ponto.cod_configuracao = faixas_horas_extra.cod_configuracao
                             AND configuracao_relogio_ponto.ultimo_timestamp = faixas_horas_extra.timestamp
                       WHERE NOT EXISTS (SELECT 1
                                           FROM ponto'|| stEntidade ||'.configuracao_relogio_ponto_exclusao
                                           WHERE configuracao_relogio_ponto_exclusao.cod_configuracao = configuracao_relogio_ponto.cod_configuracao)
                             AND configuracao_relogio_ponto.cod_configuracao = '|| inCodConfiguracao ||'
                             AND faixas_horas_extra.calculo_horas_extra = ''M''
                        ORDER BY faixas_horas_extra.percentual';
            FOR reFaixas IN EXECUTE stSql LOOP
                --Verificação dos dias que devem compor as horas extras desta faixa
--                 inContador := 0;
--                 stHorasMensal := '00:00';
                stSql := 'SELECT faixas_dias.cod_dia
                            FROM ponto'|| stEntidade ||'.faixas_dias
                           WHERE faixas_dias.cod_configuracao = '|| reFaixas.cod_configuracao ||'
                             AND faixas_dias.cod_faixa = '|| reFaixas.cod_faixa ||'
                             AND faixas_dias.timestamp = '|| quote_literal(reFaixas.timestamp) ||' ';
--                 FOR reDias IN EXECUTE stSql LOOP
--                     stHorasMensal := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHorasMensal) ||' + INTERVAL '|| quote_literal(arExtrasSemana[reDias.cod_dia]) ||' ');
--                     inContador := inContador  + 1;
--                 END LOOP;
                IF reFaixas.horas <= stHorasMensal THEN
                    arTemp := arExtras[reFaixas.cod_faixa];
                    arTemp[5] := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(arTemp[5]) ||' + INTERVAL '|| quote_literal(reFaixas.horas) ||' ');
                    arExtras[reFaixas.cod_faixa] := arTemp;
                    stHorasMensal := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHorasMensal) ||' - INTERVAL '|| quote_literal(reFaixas.horas) ||' ');
                END IF;
--                 FOR reDias IN EXECUTE stSql LOOP
--                     arExtrasSemana[reDias.cod_dia] := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHorasMensal) ||' / '|| inContador ||' ');
--                 END LOOP;
            END LOOP;
        END IF;
    END LOOP;

    WHILE arExtras[inIndex] IS NOT NULL LOOP
        arTemp := arExtras[inIndex];
        IF arTemp['5'] != '00:00' THEN            
            rwExtras.dias       := arTemp[1];
            rwExtras.calculo    := arTemp[2];
            rwExtras.faixa      := arTemp[3];
            rwExtras.percentual := arTemp[4];
            rwExtras.horas      := arTemp[5];
            RETURN NEXT rwExtras;
        END IF;
        inIndex := inIndex + 1;
    END LOOP;
END
$$ LANGUAGE 'plpgsql';

-- SELECT * FROM recuperaRelatorioEspelhoPonto(156,1,1,'01/10/2008','31/10/2008','');
-- SELECT * FROM recuperaRelatorioEspelhoPontoResumo(294,4,'2008-10-01','2008-10-31','');
-- SELECT * FROM recuperaRelatorioEspelhoPontoResumoExtras(1,'');
