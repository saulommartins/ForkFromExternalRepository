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
/* pega0DiasPorCargaHoraria
 * 
 * Data de Criação   : 29/10/2008


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Rafael Luis de Souza Garbin
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */

CREATE OR REPLACE FUNCTION recuperaHorarioPadrao(VARCHAR, INTEGER, VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    parData             VARCHAR:=$1;
    inCodContrato       INTEGER:=$2;
    stEntidade          VARCHAR:=$3;
    stHorarioPadrao     VARCHAR:='';
    stSQL               VARCHAR:='';
    inCodGrade          INTEGER;
    reRegistro          RECORD;
    reRegistroInterno   RECORD;
BEGIN
    -- Busca a grade do contrato
    stSQL := 'SELECT cod_grade
                FROM pessoal'||stEntidade||'.contrato_servidor
               WHERE cod_contrato = '||inCodContrato;
    
    inCodGrade := selectintointeger(stSQL);
    -- Verifica se existe compensação de horário para a data
    stSQL := 'SELECT dt_falta
                   , ( CASE extract(dow FROM dt_falta) -- Busca o cod_dia de acordo com a tabela pessoal.dias_turno
                            WHEN 0 THEN 1
                            WHEN 1 THEN 2
                            WHEN 2 THEN 3
                            WHEN 3 THEN 4
                            WHEN 4 THEN 5
                            WHEN 5 THEN 6
                            WHEN 6 THEN 7
                      END ) as cod_dia                   
                FROM ponto'||stEntidade||'.compensacao_horas
               WHERE dt_compensacao = to_date('||quote_literal(parData)||',''dd/mm/yyyy'')
                 AND cod_contrato = '||inCodContrato;

    FOR reRegistro IN  EXECUTE stSQL
    LOOP
        stSQL := ' SELECT to_char(escala_turno.hora_entrada_1, ''hh24:mi'') as hora_entrada_1
                        , to_char(escala_turno.hora_saida_1, ''hh24:mi'') as hora_saida_1
                        , to_char(escala_turno.hora_entrada_2, ''hh24:mi'') as hora_entrada_2
                        , to_char(escala_turno.hora_saida_2, ''hh24:mi'') as hora_saida_2
                     FROM ponto'||stEntidade||'.escala_contrato
               INNER JOIN ( SELECT cod_contrato
                                 , max(timestamp) as timestamp
                              FROM ponto'||stEntidade||'.escala_contrato
                          GROUP BY cod_contrato) as max_escala_contrato
                       ON escala_contrato.cod_contrato = max_escala_contrato.cod_contrato
                      AND escala_contrato.timestamp = max_escala_contrato.timestamp
               INNER JOIN ponto'||stEntidade||'.escala_turno
                       ON escala_turno.cod_escala = escala_contrato.cod_escala
               INNER JOIN ( SELECT cod_escala
                                 , cod_turno 
                                 , MAX(timestamp) as timestamp
                              FROM ponto'||stEntidade||'.escala_turno
                          GROUP BY cod_escala
                                 , cod_turno ) as max_escala_turno
                       ON escala_turno.cod_escala = max_escala_turno.cod_escala
                      AND escala_turno.cod_turno = max_escala_turno.cod_turno
                    WHERE NOT EXISTS (SELECT 1 
                                        FROM ponto'||stEntidade||'.escala_contrato_exclusao
                                       WHERE escala_contrato_exclusao.cod_contrato = escala_contrato.cod_contrato 
                                         AND escala_contrato_exclusao.cod_escala = escala_contrato.cod_escala
                                         AND escala_contrato_exclusao.timestamp = escala_contrato.timestamp)
                      AND NOT EXISTS (SELECT 1
                                        FROM ponto'||stEntidade||'.escala_exclusao
                                       WHERE escala_exclusao.cod_escala = escala_contrato.cod_escala)
                      AND escala_contrato.cod_contrato = '||inCodContrato||'
                      AND escala_turno.dt_turno = to_date('||quote_literal(reRegistro.dt_falta)||', ''dd/mm/yyyy'')';

        FOR reRegistroInterno IN  EXECUTE stSQL
        LOOP
            stHorarioPadrao := reRegistroInterno.hora_entrada_1||' - '||reRegistroInterno.hora_saida_1;
            IF reRegistroInterno.hora_entrada_2 IS NOT NULL AND reRegistroInterno.hora_saida_2 IS NOT NULL THEN
                stHorarioPadrao := stHorarioPadrao||' - '||reRegistroInterno.hora_entrada_2||' - '||reRegistroInterno.hora_saida_2;    
            END IF;
        END LOOP;

        IF trim(stHorarioPadrao) = '' THEN
            --Busca horário da grade para a compensacao
            stSQL := ' SELECT to_char(faixa_turno.hora_entrada,''hh24:mi'') as hora_entrada    
                            , to_char(faixa_turno.hora_saida,''hh24:mi'') as hora_saida                                        
                            , to_char(faixa_turno.hora_entrada_2,''hh24:mi'') as hora_entrada_2    
                            , to_char(faixa_turno.hora_saida_2,''hh24:mi'') as hora_saida_2
                        FROM pessoal'||stEntidade||'.faixa_turno 
                INNER JOIN (SELECT cod_grade                                             
                                , MAX(timestamp) as timestamp                        
                                FROM pessoal'||stEntidade||'.faixa_turno        
                            GROUP BY cod_grade) as max_faixa_turno
                        ON faixa_turno.cod_grade = max_faixa_turno.cod_grade                        
                        AND faixa_turno.timestamp = max_faixa_turno.timestamp                        
                INNER JOIN pessoal'||stEntidade||'.dias_turno
                        ON faixa_turno.cod_dia = dias_turno.cod_dia                                
                    WHERE faixa_turno.cod_grade = '||inCodGrade||'
                        AND faixa_turno.cod_dia = '||reRegistro.cod_dia;
    
            FOR reRegistroInterno IN  EXECUTE stSQL
            LOOP    
                stHorarioPadrao := reRegistroInterno.hora_entrada||' - '||reRegistroInterno.hora_saida;
                IF reRegistroInterno.hora_entrada_2 IS NOT NULL AND reRegistroInterno.hora_saida_2 IS NOT NULL THEN
                    stHorarioPadrao := stHorarioPadrao||' - '||reRegistroInterno.hora_entrada_2||' - '||reRegistroInterno.hora_saida_2;    
                END IF;
            END LOOP;
        END IF;
    END LOOP;

    -- Verifica se o contrato possui escala
    IF trim(stHorarioPadrao) = '' THEN 
        stSQL := ' SELECT to_char(escala_turno.hora_entrada_1, ''hh24:mi'') as hora_entrada_1
                        , to_char(escala_turno.hora_saida_1, ''hh24:mi'') as hora_saida_1
                        , to_char(escala_turno.hora_entrada_2, ''hh24:mi'') as hora_entrada_2
                        , to_char(escala_turno.hora_saida_2, ''hh24:mi'') as hora_saida_2
                     FROM ponto'||stEntidade||'.escala_contrato
               INNER JOIN ( SELECT cod_contrato
                                 , max(timestamp) as timestamp
                              FROM ponto'||stEntidade||'.escala_contrato
                          GROUP BY cod_contrato) as max_escala_contrato
                       ON escala_contrato.cod_contrato = max_escala_contrato.cod_contrato
                      AND escala_contrato.timestamp = max_escala_contrato.timestamp
               INNER JOIN ponto'||stEntidade||'.escala_turno
                       ON escala_turno.cod_escala = escala_contrato.cod_escala
               INNER JOIN ( SELECT cod_escala
                                 , cod_turno 
                                 , MAX(timestamp) as timestamp
                              FROM ponto'||stEntidade||'.escala_turno
                          GROUP BY cod_escala
                                 , cod_turno ) as max_escala_turno
                       ON escala_turno.cod_escala = max_escala_turno.cod_escala
                      AND escala_turno.cod_turno = max_escala_turno.cod_turno
                    WHERE NOT EXISTS (SELECT 1 
                                        FROM ponto'||stEntidade||'.escala_contrato_exclusao
                                       WHERE escala_contrato_exclusao.cod_contrato = escala_contrato.cod_contrato 
                                         AND escala_contrato_exclusao.cod_escala = escala_contrato.cod_escala
                                         AND escala_contrato_exclusao.timestamp = escala_contrato.timestamp)
                      AND NOT EXISTS (SELECT 1
                                        FROM ponto'||stEntidade||'.escala_exclusao
                                       WHERE escala_exclusao.cod_escala = escala_contrato.cod_escala)
                      AND escala_contrato.cod_contrato = '||inCodContrato||'
                      AND escala_turno.dt_turno = to_date('||quote_literal(parData)||',''dd/mm/yyyy'')';

        FOR reRegistro IN  EXECUTE stSQL
        LOOP
            stHorarioPadrao := reRegistro.hora_entrada_1||' - '||reRegistro.hora_saida_1;
            IF reRegistro.hora_entrada_2 IS NOT NULL AND reRegistro.hora_saida_2 IS NOT NULL THEN
                stHorarioPadrao := stHorarioPadrao||' - '||reRegistro.hora_entrada_2||' - '||reRegistro.hora_saida_2;    
            END IF;
        END LOOP;

    END IF;

     -- Caso não acha compensação nem escala, pega o horário da grade
    IF trim(stHorarioPadrao) = '' THEN 
        stSQL := 'SELECT ( CASE extract(dow FROM to_date('||quote_literal(parData)||', ''dd/mm/yyyy''))  -- Busca o cod_dia de acordo com a tabela pessoal.dias_turno
                                    WHEN 0 THEN 1
                                    WHEN 1 THEN 2
                                    WHEN 2 THEN 3
                                    WHEN 3 THEN 4
                                    WHEN 4 THEN 5
                                    WHEN 5 THEN 6
                                    WHEN 6 THEN 7
                            END ) as cod_dia'; 
        
        FOR reRegistro IN  EXECUTE stSQL
        LOOP
            --Busca horário da grade
            stSQL := ' SELECT to_char(faixa_turno.hora_entrada,''hh24:mi'') as hora_entrada    
                            , to_char(faixa_turno.hora_saida,''hh24:mi'') as hora_saida                                        
                            , to_char(faixa_turno.hora_entrada_2,''hh24:mi'') as hora_entrada_2    
                            , to_char(faixa_turno.hora_saida_2,''hh24:mi'') as hora_saida_2
                        FROM pessoal'||stEntidade||'.faixa_turno 
                INNER JOIN (SELECT cod_grade                                             
                                , MAX(timestamp) as timestamp                        
                                FROM pessoal'||stEntidade||'.faixa_turno        
                            GROUP BY cod_grade) as max_faixa_turno
                        ON faixa_turno.cod_grade = max_faixa_turno.cod_grade                        
                        AND faixa_turno.timestamp = max_faixa_turno.timestamp                        
                INNER JOIN pessoal'||stEntidade||'.dias_turno
                        ON faixa_turno.cod_dia = dias_turno.cod_dia                                
                    WHERE faixa_turno.cod_grade = '||inCodGrade||'
                        AND faixa_turno.cod_dia = '||reRegistro.cod_dia;

            FOR reRegistroInterno IN  EXECUTE stSQL
            LOOP 
                stHorarioPadrao := reRegistroInterno.hora_entrada||' - '||reRegistroInterno.hora_saida;
                IF reRegistroInterno.hora_entrada_2 IS NOT NULL AND reRegistroInterno.hora_saida_2 IS NOT NULL THEN
                   stHorarioPadrao := stHorarioPadrao||' - '||reRegistroInterno.hora_entrada_2||' - '||reRegistroInterno.hora_saida_2;    
                END IF;
            END LOOP;
        END LOOP;
    END IF;

    return stHorarioPadrao;
END
$$ LANGUAGE 'plpgsql';
