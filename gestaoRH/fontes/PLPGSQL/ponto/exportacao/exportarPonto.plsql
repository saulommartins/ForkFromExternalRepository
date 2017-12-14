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
    /* exportarPonto
 * 
 * Data de Criação   : 23/10/2008


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */
CREATE OR REPLACE FUNCTION exportarPonto(INTEGER,VARCHAR,VARCHAR,VARCHAR,VARCHAR,VARCHAR,INTEGER) RETURNS SETOF colunasExportarPonto AS $$
DECLARE
    inCodFormato            ALIAS FOR $1;
    stPeriodoInicial        ALIAS FOR $2;
    stPeriodoFinal          ALIAS FOR $3;
    stEntidade              ALIAS FOR $4;
    stTipoFiltro            ALIAS FOR $5;
    stCodigosFiltro         ALIAS FOR $6;
    inExercicio             ALIAS FOR $7;

    rwExportarPonto         colunasExportarPonto%ROWTYPE;
    inCodEvento             INTEGER;
    stSql                   VARCHAR;
    stExtras                VARCHAR;
    stHora                  VARCHAR;
    stHoraExtraSemana       VARCHAR:='00:00';
    stHoraExtraMensal       VARCHAR:='00:00';
    stQuantidade            VARCHAR;
    stQuantidadeFormatado   VARCHAR;
    stQuantidadeDias        VARCHAR;
    stHoraTemp              VARCHAR;
    stCodDiasSemana         VARCHAR:='';
    stCodDiasMensal         VARCHAR:='';
    arCodigosFiltro         VARCHAR[];
    reContrato              RECORD;
    reFormato               RECORD;
    reEspelhoPonto          RECORD;
    reExtras                RECORD;
    reFaixas                RECORD;
    reEspelhoPontoResumo    RECORD;
    arQuantidade            INTEGER[];
    
BEGIN
    --Limpa a tabela que armazena os dados para a geração do relatório
    stSql := 'DELETE FROM ponto'||stEntidade||'.exportacao_ponto';
    execute stSql;

    stSql := 'SELECT cadastro.cod_contrato
                   , cadastro.registro
                   , cadastro.cod_grade
                   , configuracao_relogio_ponto'||stEntidade||'.cod_configuracao
                FROM ( SELECT *
                         FROM recuperarContratoServidor(''s,o,l,rf,sf,f,ef'','''||stEntidade||''',''0'','''||stTipoFiltro||''','''||stCodigosFiltro||''','''||inExercicio||''')
                     ) as cadastro';
                  
    stSql := stSql || '
                JOIN ponto'||stEntidade||'.configuracao_lotacao
                  ON cadastro.cod_orgao = configuracao_lotacao.cod_orgao
                JOIN ponto'||stEntidade||'.configuracao_relogio_ponto
                  ON configuracao_lotacao.cod_configuracao = configuracao_relogio_ponto'||stEntidade||'.cod_configuracao
                 AND configuracao_lotacao.timestamp = configuracao_relogio_ponto'||stEntidade||'.ultimo_timestamp
               WHERE NOT EXISTS (SELECT 1
                                   FROM ponto'||stEntidade||'.configuracao_relogio_ponto_exclusao
                                  WHERE configuracao_relogio_ponto_exclusao.cod_configuracao = configuracao_relogio_ponto'||stEntidade||'.cod_configuracao)
                 AND recuperarSituacaoDoContrato(cadastro.cod_contrato,0,'''||stEntidade||''') NOT IN (''R'', ''P'')';
                 
    stSql := stSql || ' ORDER BY cadastro.registro';

    FOR reContrato IN EXECUTE stSql LOOP
       SELECT sum(hs_trab::TIME) as hs_trab
            , SUM(ad_not::TIME) as ad_not
            , SUM(extras::TIME) as extras
            , SUM(atrasos::TIME) as atrasos
            , SUM(faltas::TIME) AS faltas
            , SUM(pega0DiasPorCargaHoraria(faltas::time,carga_horaria::time)) as faltas_dias
            , SUM(pega0DiasPorCargaHoraria(hs_trab::time,carga_horaria::time)) as hs_trab_dias
            , SUM(pega0DiasPorCargaHoraria(ad_not::time,carga_horaria::time)) as ad_not_dias
         INTO reEspelhoPonto
         FROM recuperaRelatorioEspelhoPonto(reContrato.cod_contrato,reContrato.cod_configuracao,reContrato.cod_grade,stPeriodoInicial,stPeriodoFinal,stEntidade);


       SELECT *
         INTO reEspelhoPontoResumo
         FROM recuperaRelatorioEspelhoPontoResumo(reContrato.cod_contrato,reContrato.cod_configuracao,to_date(stPeriodoInicial,'dd/mm/yyyy'),to_date(stPeriodoFinal,'dd/mm/yyyy'),stEntidade);
        --Informação do Relógio Ponto
        --1 | Horas Trabalhadas
        --2 | Adicional Noturno
        --3 | Atrasos
        --4 | Faltas
        --5 | Abonos DSR
        --6 | Descontos DSR
        --7 | Horas Extras

        stSql := 'SELECT dados_exportacao.*
                       , evento.codigo
                       , formato_exportacao.formato_minutos
                       , COALESCE(formato_informacao.formato,''H'') as formato
                    FROM ponto'||stEntidade||'.dados_exportacao
               LEFT JOIN ponto'||stEntidade||'.formato_informacao
                      ON formato_informacao.cod_formato = dados_exportacao.cod_formato
                     AND formato_informacao.cod_dado = dados_exportacao.cod_dado
                    JOIN ponto'||stEntidade||'.formato_exportacao
                      ON formato_exportacao.cod_formato = dados_exportacao.cod_formato
                    JOIN folhapagamento'||stEntidade||'.evento
                      ON evento.cod_evento = dados_exportacao.cod_evento
                   WHERE dados_exportacao.cod_formato = '||inCodFormato;
        FOR reFormato IN EXECUTE stSql LOOP
            stHora := null;
            --Horas Trabalhadas
            IF reFormato.cod_tipo = 1 AND reEspelhoponto.hs_trab IS NOT NULL THEN
                stHora := reEspelhoponto.hs_trab;
                stQuantidadeDias := reEspelhoponto.hs_trab_dias;
            END IF;
            --Adicional Noturno
            IF reFormato.cod_tipo = 2 AND reEspelhoponto.ad_not IS NOT NULL THEN
                stHora := reEspelhoponto.ad_not;
                stQuantidadeDias := reEspelhoponto.ad_not_dias;
            END IF;
            --Atrasos
            IF reFormato.cod_tipo = 3 AND reEspelhoponto.atrasos IS NOT NULL THEN
                stHora := reEspelhoponto.atrasos;
            END IF;
            --Faltas
            IF reFormato.cod_tipo = 4 AND reEspelhoponto.faltas IS NOT NULL THEN
                stHora := reEspelhoponto.faltas;
                stQuantidadeDias := reEspelhoponto.faltas_dias;
            END IF;
            --Abonos DSR
            IF reFormato.cod_tipo = 5 AND reEspelhoPontoResumo.abono_dsr IS NOT NULL THEN
                stHora := reEspelhoPontoResumo.abono_dsr;
            END IF;
            --Descontos DSR
            IF reFormato.cod_tipo = 6 AND reEspelhoPontoResumo.desc_dsr IS NOT NULL THEN
                stHora := reEspelhoPontoResumo.desc_dsr;
            END IF;
            --Horas Extras
            IF reFormato.cod_tipo = 7 AND reEspelhoponto.extras IS NOT NULL THEN
                stHora := '00:00';

--                 stSql := 'SELECT *
--                                , dias_turno.cod_dia
--                             FROM recuperaRelatorioEspelhoPonto('||reContrato.cod_contrato||','||reContrato.cod_configuracao||','||reContrato.cod_grade||','''||stPeriodoInicial||''','''||stPeriodoFinal||''','''||stEntidade||''')
--                             JOIN pessoal'||stEntidade||'.dias_turno
--                               ON replace(dias_turno.nom_dia,''-feira'','''') = dia';

                stSql := 'SELECT relatorio_espelho_ponto.*
                               , dias_turno.cod_dia
                            FROM ponto'||stEntidade||'.relatorio_espelho_ponto
                            JOIN pessoal'||stEntidade||'.dias_turno
                              ON replace(dias_turno.nom_dia,''-feira'','''') = relatorio_espelho_ponto.dia';
                FOR reExtras IN EXECUTE stSql LOOP  
                    --PROCESSAMENTO DIÁRIO
                    stHoraTemp := pega0ExtrasPorCalculo(reFormato.cod_evento,reExtras.cod_dia::varchar,'D',reExtras.extras,stEntidade);
                    stHora := selectIntoVarchar('SELECT INTERVAL '''||stHora||''' + INTERVAL '''||stHoraTemp||''' ');

                    --PROCESSAMENTO SEMANAL
                    IF reExtras.extras IS NOT NULL THEN
                        --Dias onde foram encontradas horas extras para a semana
                        stCodDiasSemana := stCodDiasSemana||reExtras.cod_dia||',';
                        --Horas extras da semana
                        stHoraExtraSemana := selectIntoVarchar('SELECT INTERVAL '''||stHoraExtraSemana||''' + INTERVAL '''||reExtras.extras||''' ');
                    END IF;
                    IF reExtras.cod_dia = 7 OR reExtras.data = to_char(last_day(to_date(reExtras.data,'dd/mm/yyyy')),'dd/mm/yyyy') THEN                    
                        stCodDiasSemana := substr(stCodDiasSemana,1,char_length(stCodDiasSemana)-1);
                        stHoraTemp := pega0ExtrasPorCalculo(reFormato.cod_evento,stCodDiasSemana::varchar,'S',stHoraExtraSemana,stEntidade);
                        stHora := selectIntoVarchar('SELECT INTERVAL '''||stHora||''' + INTERVAL '''||stHoraTemp||''' ');
                        stCodDiasSemana := '';
                        stHoraExtraSemana := '00:00';
                    END IF;

                    --PROCESSAMENTO MENSAL
                    IF reExtras.extras IS NOT NULL THEN
                        --Dias onde foram encontradas horas extras para o mês
                        stCodDiasMensal := stCodDiasMensal||reExtras.cod_dia||',';
                        --Horas extras do mês
                        stHoraExtraMensal := selectIntoVarchar('SELECT INTERVAL '''||stHoraExtraMensal||''' + INTERVAL '''||reExtras.extras||''' ');
                    END IF;
                    IF reExtras.data = to_char(last_day(to_date(reExtras.data,'dd/mm/yyyy')),'dd/mm/yyyy') THEN
                        stCodDiasMensal := substr(stCodDiasMensal,1,char_length(stCodDiasMensal)-1);
                        stHoraTemp := pega0ExtrasPorCalculo(reFormato.cod_evento,stCodDiasMensal,'M',stHoraExtraMensal,stEntidade);
                        stHora := selectIntoVarchar('SELECT INTERVAL '''||stHora||''' + INTERVAL '''||stHoraTemp||''' ');
                        stCodDiasMensal := '';
                        stHoraExtraMensal := '00:00';
                    END IF;
                END LOOP;                
            END IF;
            IF stHora is not null THEN
                --VERIFICAÇÃO DO FORMATO QUE DEVERÁ SER DEMONSTRADO OS VALORES DAS HORAS
                --DEPENDE DA CARGA HORÁRIO QUE O SERVIDOR DEVE FAZER EM CADA DIA
                --D: 10hs e 50 min = 10:50/Carga horaria
                --H: Segue para a próxima verificação
                IF reFormato.formato = 'D' THEN
                    stQuantidade := stQuantidadeDias;
                    stQuantidadeFormatado := stQuantidadeDias;
                ELSE
                    --VERIFICAÇÃO DO FORMATO QUE DEVERÁ SER DEMONSTRADO OS VALORES DAS HORAS
                    --D: 10hs e 50 min = 10,83 horas => 1083
                    --H: 10hs e 50 min = 10:50 horas => 1050                    
                    arQuantidade := string_to_array(stHora,':');
                    IF reFormato.formato_minutos = 'D' THEN
                        stQuantidade := (arQuantidade[1]+round((arQuantidade[2]/60.0),2))::VARCHAR;
                        stQuantidadeFormatado := replace(stQuantidade,'.',',');
                        stQuantidade := replace(stQuantidade,'.','');
                    ELSE
                        stQuantidade := arQuantidade[1]||arQuantidade[2];
                        stQuantidadeFormatado := arQuantidade[1]||','||arQuantidade[2];
                    END IF;
                END IF;

                IF stQuantidade::INTEGER > 0 THEN
                    --Salva os dados gerados para a geração do relatório
                    --com isso não é preciso executar a pl novamente para obter os dados
                    stSql := 'INSERT INTO ponto'||stEntidade||'.exportacao_ponto
                              (cod_contrato,cod_evento,cod_tipo,lancamento,formato) 
                              VALUES
                              ('||reContrato.cod_contrato||'
                              ,'||reFormato.cod_evento||'
                              ,'||reFormato.cod_tipo||'
                              ,'''||stQuantidadeFormatado||'''
                              ,'''||reFormato.formato||''')';
                    EXECUTE stSql;

                    rwExportarponto.registro            := reContrato.registro;
                    rwExportarponto.codigo_evento       := reFormato.codigo;
                    rwExportarponto.valor               := 0;
                    rwExportarponto.quantidade          := stQuantidade;
                    rwExportarponto.quantidade_parcelas := 0;
                    RETURN NEXT rwExportarPonto;    
                END IF;
            END IF;
        END LOOP;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';
