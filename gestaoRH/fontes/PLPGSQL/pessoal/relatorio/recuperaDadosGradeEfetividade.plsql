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
CREATE OR REPLACE FUNCTION recuperaDiasEfetivosMesServidor(integer,integer,integer,integer,integer,integer) returns integer as $$
DECLARE
    inDias          ALIAS FOR $1;
    inMes           ALIAS FOR $2;
    inAno           ALIAS FOR $3;
    inDiasFim       ALIAS FOR $4;
    inMesFim        ALIAS FOR $5;
    inAnoFim        ALIAS FOR $6;
    inDiasEfetivo   INTEGER;
    inRetorno       INTEGER;
BEGIN 
    inDiasEfetivo := to_char(last_day(to_date(inAno::varchar ||'-'|| inMes::varchar ||'-'|| inDias::varchar,'yyyy-mm-dd')),'dd')::INTEGER;
    IF inDias > 0 THEN        
        inDiasEfetivo := inDiasEfetivo - inDias + 1;
    END IF;        
    IF inMesFim = inMes AND inAno = inAnoFim THEN
        inDiasEfetivo := inDiasFim;
    END IF;
    inRetorno := inDiasEfetivo;
    IF to_date(inAno::varchar ||'-'|| inMes::varchar,'yyyy-mm') > to_date(inAnoFim::varchar ||'-'|| inMesFim::varchar,'yyyy-mm') THEN
        inRetorno := -1;
    END IF;
    return inRetorno;
END;
$$ language 'plpgsql';

CREATE OR REPLACE FUNCTION recuperaDadosGradeEfetividade(integer,varchar,date,date,varchar) returns SETOF colunasGradeEfetividade AS $$
DECLARE
    inCodContrato       ALIAS FOR $1;
    stExercicio         ALIAS FOR $2;    
    dtPeriodoInicialPar ALIAS FOR $3;
    dtPeriodoFinalPar   ALIAS FOR $4;
    stEntidade          ALIAS FOR $5;   
    stSql               VARCHAR;
    stContagemTempo     VARCHAR;
    stEfetividade       VARCHAR;
    stAssentamentos     VARCHAR := '';
    dtContagemTempo     DATE;
    dtRescisao          DATE;
    dtPeriodoInicial    DATE;
    dtPeriodoFinal      DATE;
    reRegistro          RECORD;
    regAux               RECORD;
    inAno               INTEGER;
    inAnoFim            INTEGER;
    inMes               INTEGER;
    inMesFim            INTEGER;
    inMesAux            INTEGER;
    inDias              INTEGER;
    inDiasFim           INTEGER;
    inDiasEfetivo       INTEGER;
    inDiasAfastado      INTEGER;
    inDiasExtra         INTEGER;
    inIndex             INTEGER;
    inTotalAno          INTEGER:=0;
    rwRetorno           colunasGradeEfetividade%ROWTYPE;
BEGIN
    dtPeriodoFinal   := dtPeriodoFinalPar;
    dtPeriodoInicial := dtPeriodoInicialPar;
    stSql := 'SELECT dt_rescisao
                FROM pessoal'|| stEntidade ||'.contrato_servidor_caso_causa
               WHERE cod_contrato = '|| inCodContrato;
    stContagemTempo := selectIntoVarchar(stSql);                   
    dtRescisao := stContagemTempo::DATE;
    IF dtRescisao is not null THEN
        dtPeriodoFinal := dtRescisao;
    END IF;
    
    stSql := 'SELECT valor 
                FROM administracao.configuracao 
               WHERE parametro = ''dtContagemInicial'' 
                 AND cod_modulo = 22 
                 AND exercicio = '|| quote_literal(stExercicio) ||' ';
    stContagemTempo := selectIntoVarchar(stSql);

    stSql := 'SELECT case '|| quote_literal(stContagemTempo) ||'
                     when ''dtAdmissao'' THEN dt_admissao
                     when ''dtPosse''    THEN dt_posse
                     when ''dtNomeacao'' THEN dt_nomeacao
                     end as dt_contagem_tempo
                FROM pessoal'|| stEntidade ||'.contrato_servidor_nomeacao_posse
                   , (SELECT cod_contrato
                           , max(timestamp) as timestamp
                        FROM pessoal'|| stEntidade ||'.contrato_servidor_nomeacao_posse
                      GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse
               WHERE contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato
                 AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp         
                 AND contrato_servidor_nomeacao_posse.cod_contrato = '|| inCodContrato;
    stContagemTempo := selectIntoVarchar(stSql);
    dtContagemTempo := stContagemTempo::DATE;

    stSql := '  SELECT 
                        '|| quote_literal(dtContagemTempo) || ' as periodo_inicial
                        ,'|| quote_literal(dtPeriodoFinal) || ' as periodo_final

                UNION
                --Assentamento anterior da data de admissao    
                SELECT CASE WHEN assentamento_gerado.periodo_inicial < '''||dtPeriodoInicialPar||'''
                            THEN '''||dtPeriodoInicialPar||'''
                            ELSE assentamento_gerado.periodo_inicial
                        END AS periodo_inicial
                     , assentamento_gerado.periodo_final
                FROM pessoal'|| stEntidade ||'.assentamento_gerado_contrato_servidor
                    , pessoal'|| stEntidade ||'.assentamento_gerado
                    , pessoal'|| stEntidade ||'.assentamento_assentamento
                    , pessoal'|| stEntidade ||'.assentamento
                    , (SELECT cod_assentamento
                            , max(timestamp) as timestamp
                        FROM pessoal'|| stEntidade ||'.assentamento
                        GROUP BY cod_assentamento
                    ) as max_assentamento
                    , ( SELECT cod_assentamento_gerado, cod_assentamento
                                , max(timestamp) as timestamp
                        FROM pessoal.assentamento_gerado
                        GROUP BY cod_assentamento_gerado ,cod_assentamento
                    ) as max_assentamento_gerado
                WHERE assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                AND assentamento_gerado.cod_assentamento = assentamento_assentamento.cod_assentamento
                AND assentamento_assentamento.cod_assentamento = assentamento.cod_assentamento
                AND assentamento.cod_assentamento = max_assentamento.cod_assentamento
                AND assentamento.timestamp = max_assentamento.timestamp
                AND (to_char(assentamento_gerado.periodo_inicial,''yyyy-mm'')  < to_char(to_date('''||dtContagemTempo     ||''',''yyyy-mm-dd''),''yyyy-mm'')
                AND  to_char(assentamento_gerado.periodo_final,''yyyy-mm'')   >= to_char(to_date('''||dtPeriodoInicialPar ||''',''yyyy-mm-dd''),''yyyy-mm''))
                AND assentamento.grade_efetividade is true
                AND assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado
                AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp
                AND assentamento_gerado.cod_assentamento = max_assentamento_gerado.cod_assentamento
                AND assentamento_gerado_contrato_servidor.cod_contrato = '|| inCodContrato ||'
                AND assentamento_gerado.cod_assentamento_gerado NOT IN (SELECT cod_assentamento_gerado                                                                                      
                                                                          FROM pessoal.assentamento_gerado_excluido
                                                                         WHERE assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                                                                           AND assentamento_gerado_excluido.timestamp = assentamento_gerado.timestamp
                                                                        )
                ';

FOR regAux IN EXECUTE stSql
LOOP

    inAno := to_char(regAux.periodo_inicial,'yyyy');
    inMes := to_char(regAux.periodo_inicial,'mm');
    inDias := to_char(regAux.periodo_inicial,'dd');
    inAnoFim := to_char(regAux.periodo_final,'yyyy');
    inMesFim := to_char(regAux.periodo_final,'mm');
    inDiasFim := to_char(regAux.periodo_final,'dd');

    WHILE inAno <= inAnoFim LOOP
        rwRetorno.ano    := inAno;

        FOR inMesAux IN 1 .. 12 LOOP
            inDiasExtra := 0;
            IF to_char(to_date(inAno ||'-'|| inMes,'yyyy-mm'),'yyyy-mm') >= to_char(dtContagemTempo,'yyyy-mm') THEN                
                inDiasEfetivo := recuperaDiasEfetivosMesServidor(inDias,inMes,inAno,inDiasFim,inMesFim,inAnoFim);
                inDias := 0;            
            END IF;
            stSql := 'SELECT COALESCE(assentamento_assentamento.abreviacao,''FA'') as abreviacao
                           , assentamento_gerado.periodo_inicial
                           , assentamento_gerado.periodo_final
                           , assentamento_assentamento.cod_operador
                        FROM pessoal'|| stEntidade ||'.assentamento_gerado_contrato_servidor
                           , pessoal'|| stEntidade ||'.assentamento_gerado
                           , pessoal'|| stEntidade ||'.assentamento_assentamento
                           , pessoal'|| stEntidade ||'.assentamento
                           , (SELECT cod_assentamento
                                   , max(timestamp) as timestamp
                                FROM pessoal'|| stEntidade ||'.assentamento
                              GROUP BY cod_assentamento
                              ) as max_assentamento
                           , ( SELECT cod_assentamento_gerado, cod_assentamento
                                      , max(timestamp) as timestamp
                               FROM pessoal.assentamento_gerado
                               GROUP BY cod_assentamento_gerado ,cod_assentamento
                             ) as max_assentamento_gerado
                       WHERE assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                         AND assentamento_gerado.cod_assentamento = assentamento_assentamento.cod_assentamento
                         AND assentamento_assentamento.cod_assentamento = assentamento.cod_assentamento
                         AND assentamento.cod_assentamento = max_assentamento.cod_assentamento
                         AND assentamento.timestamp = max_assentamento.timestamp
                         AND (to_char(assentamento_gerado.periodo_inicial,''yyyy-mm'') <= '|| quote_literal(to_char(to_date(inAno ||'-'|| inMes,'yyyy-mm'),'yyyy-mm')) ||'
                         AND  to_char(assentamento_gerado.periodo_final,''yyyy-mm'')   >= '|| quote_literal(to_char(to_date(inAno ||'-'|| inMes,'yyyy-mm'),'yyyy-mm')) ||')
                         AND assentamento.grade_efetividade is true
                         AND assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado
                         AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp
                         AND assentamento_gerado.cod_assentamento = max_assentamento_gerado.cod_assentamento
                         AND assentamento_gerado_contrato_servidor.cod_contrato = '|| inCodContrato ||'
                         AND assentamento_gerado.cod_assentamento_gerado NOT IN (SELECT cod_assentamento_gerado                                                                                      
                                                                                   FROM pessoal.assentamento_gerado_excluido
                                                                                  WHERE assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                                                                                    AND assentamento_gerado_excluido.timestamp = assentamento_gerado.timestamp
                                                                                )';
                        
            stAssentamentos := '';
            FOR reRegistro IN EXECUTE stSql LOOP                
                inDiasAfastado := 0;
                IF to_char(reRegistro.periodo_inicial,'yyyy-mm') < to_char(to_date(inAno::varchar ||'-'|| inMes::varchar,'yyyy-mm'),'yyyy-mm') THEN
                    reRegistro.periodo_inicial := to_date(inAno::varchar ||'-'|| inMes::varchar,'yyyy-mm-dd');
                END IF; 
                IF to_char(reRegistro.periodo_final,'yyyy-mm') > to_char(to_date(inAno ||'-'|| inMes,'yyyy-mm'),'yyyy-mm') THEN
                    reRegistro.periodo_final := last_day(to_date(inAno::varchar ||'-'|| inMes::varchar,'yyyy-mm-dd'));
                END IF;                 

                inDiasAfastado := reRegistro.periodo_final-reRegistro.periodo_inicial+1;
                --Soma
                IF reRegistro.cod_operador = 1 THEN
                    inDiasExtra := inDiasExtra + inDiasAfastado;
                END IF;
                --Diminui
                IF reRegistro.cod_operador = 2 THEN
                    inDiasEfetivo := inDiasEfetivo - inDiasAfastado;
                END IF;
                --Dobra
                IF reRegistro.cod_operador = 4 THEN
                    inDiasExtra := inDiasExtra + inDiasAfastado;
                END IF;
                stAssentamentos := stAssentamentos || ' '|| inDiasAfastado||reRegistro.abreviacao;
            END LOOP;
            
            IF inDiasEfetivo >= 0 THEN
                
                IF inMes = inMesAux THEN
                    inTotalAno := inTotalAno + inDiasEfetivo;            
                END IF;
                stEfetividade := inDiasEfetivo ||'E';
            ELSE
                stEfetividade := '-';
            END IF;
            
            IF inDiasAfastado is not null THEN
                --inDiasExtra, são os dias que deverão ser somados no total do ano
                --esses dias correspondem a assentamentos que possuem o operador igual a soma
                --exemplo disso Tempo de Empresa Privada
                --inDiasExtra, também é usado para dobrar os dias em casdo de operador igual
                --a dobrar.
                IF inMes = inMesAux THEN
                    inTotalAno := inTotalAno + inDiasExtra;
                END IF;
                IF inDiasEfetivo >= 0 THEN
                    stEfetividade := stEfetividade||stAssentamentos;
                ELSE
                    stEfetividade := stAssentamentos;
                END IF;
            END IF;            
            
            IF inMes = 1 THEN    
                rwRetorno.jan    := stEfetividade;
            ELSIF inMes > inMesAux THEN
                rwRetorno.jan    := '-';
            END IF;
            IF inMes = 2 THEN    
                rwRetorno.fev    := stEfetividade;                
            ELSIF inMes > inMesAux THEN
                rwRetorno.fev    := '-';
            END IF;
            IF inMes = 3 THEN    
                rwRetorno.mar    := stEfetividade;                
            ELSIF inMes > inMesAux THEN
                rwRetorno.mar    := '-';
            END IF;
            IF inMes = 4 THEN    
                rwRetorno.abr    := stEfetividade;                
            ELSIF inMes > inMesAux THEN
                rwRetorno.abr    := '-';
            END IF;
            IF inMes = 5 THEN    
                rwRetorno.mai    := stEfetividade;                
            ELSIF inMes > inMesAux THEN
                rwRetorno.mai    := '-';
            END IF;
            IF inMes = 6 THEN    
                rwRetorno.jun    := stEfetividade;                
            ELSIF inMes > inMesAux THEN
                rwRetorno.jun    := '-';
            END IF;
            IF inMes = 7 THEN    
                rwRetorno.jul    := stEfetividade;                
            ELSIF inMes > inMesAux THEN
                rwRetorno.jul    := '-';
            END IF;
            IF inMes = 8 THEN    
                rwRetorno.ago    := stEfetividade;                
            ELSIF inMes > inMesAux THEN
                rwRetorno.ago    := '-';
            END IF;
            IF inMes = 9 THEN    
                rwRetorno.set    := stEfetividade;                
            ELSIF inMes > inMesAux THEN
                rwRetorno.set    := '-';
            END IF;
            IF inMes = 10 THEN    
                rwRetorno.out    := stEfetividade;                
            ELSIF inMes > inMesAux THEN
                rwRetorno.out    := '-';
            END IF;
            IF inMes = 11 THEN    
                rwRetorno.nov    := stEfetividade;                
            ELSIF inMes > inMesAux THEN
                rwRetorno.nov    := '-';
            END IF;
            IF inMes = 12 THEN    
                rwRetorno.dez    := stEfetividade;                
            ELSIF inMes > inMesAux THEN
                rwRetorno.dez    := '-';
            END IF;
            
            IF inMes = inMesAux THEN
                inMes := inMes + 1;
            END IF;
            inMesAux := inMesAux + 1;
            
            inDiasAfastado := null;                                                                                                                                        

        END LOOP;        
        rwRetorno.total  := inTotalAno;
        
        inTotalAno := 0;
        inAno := inAno + 1;
        inMes := 1;
            
        RETURN NEXT rwRetorno;
    END LOOP;

END LOOP;
END;    
$$ language 'plpgsql';