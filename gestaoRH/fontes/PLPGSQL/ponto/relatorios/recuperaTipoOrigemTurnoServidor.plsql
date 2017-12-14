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
 * PL para Retornar o Tipo e a Origem de um Turno para o Relogio Ponto de um Servidor
 * Data de Criação   : 17/10/2008


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Alex Cardoso
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */
 

CREATE OR REPLACE FUNCTION recuperaTipoOrigemTurnoServidor(INTEGER,VARCHAR,VARCHAR) RETURNS colunasTipoOrigemTurnoServidor AS $$
DECLARE
    inCodContrato         ALIAS FOR $1;
    stDtTurno             ALIAS FOR $2;
    stEntidade            ALIAS FOR $3;
    
    rwTipoOrigemTurnoServidor       colunasTipoOrigemTurnoServidor%ROWTYPE;

    crCursor              REFCURSOR;
    reRegistro            RECORD;
    stSql                 VARCHAR;
    stAux                 VARCHAR;
    inCodGrade            INTEGER;

    --variaveis de conteudo de retorno
    boCompensacao         BOOLEAN;
    boCalendario          BOOLEAN;
    boEscala              BOOLEAN;
    boGrade               BOOLEAN;    
    boDiaTrabalho         BOOLEAN;    
    stDescricaoTipo       VARCHAR;
    stDescricaoOrigem     VARCHAR;
    stCompensacaoDtFalta  VARCHAR;
BEGIN
        boCompensacao         := FALSE;
        boCalendario          := FALSE;
        boEscala              := FALSE;
        boGrade               := FALSE;   
        boDiaTrabalho         := FALSE;   
        stDescricaoTipo       := '';
        stDescricaoOrigem     := '';
        stCompensacaoDtFalta  := '';
        
        --verifica se existe compensacao com data da falta igual a data corrente
        stSql := ' SELECT to_char(dt_falta, ''dd/mm/yyyy'') as dt_falta
                        , to_char(dt_compensacao, ''dd/mm/yyyy'') as dt_compensacao
                     FROM ponto'||stEntidade||'.compensacao_horas                           
                    WHERE NOT EXISTS (SELECT 1                                                       
                                        FROM ponto'||stEntidade||'.compensacao_horas_exclusao                        
                                       WHERE compensacao_horas_exclusao.cod_compensacao = compensacao_horas.cod_compensacao
                                         AND compensacao_horas_exclusao.cod_contrato = compensacao_horas.cod_contrato)
                      AND cod_contrato = '||inCodContrato||'
                      AND (dt_falta = TO_DATE('||quote_literal(stDtTurno)||', ''dd/mm/yyyy'') OR dt_compensacao = TO_DATE('||quote_literal(stDtTurno)||', ''dd/mm/yyyy''))';
                                          
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reRegistro;
        CLOSE crCursor;
                                                  
        IF reRegistro.dt_falta = stDtTurno THEN
            boCompensacao := true;
            boDiaTrabalho := false;
        ELSEIF reRegistro.dt_compensacao = stDtTurno THEN
                stCompensacaoDtFalta := reRegistro.dt_falta;
                boCompensacao        := true;
                boDiaTrabalho        := true;
        END IF;
        
        --se a compensacao com data de falta existe, indica a falta. Senao, verifica se existe compensacao com data de compensacao para a data atual. 
        IF boCompensacao IS FALSE THEN
                
                stAux := selectIntoVarchar('    SELECT escala_turno.tipo
                                                  FROM ponto'||stEntidade||'.escala
                                                  JOIN ponto'||stEntidade||'.escala_turno
                                                    ON (escala_turno.cod_escala = escala.cod_escala AND
                                                        escala_turno.timestamp = escala.ultimo_timestamp)
                                                  JOIN ponto'||stEntidade||'.escala_contrato
                                                    ON (escala.cod_escala = escala_contrato.cod_escala)
                                                  JOIN ( SELECT cod_contrato
                                                              , cod_escala
                                                              , max(timestamp) as timestamp
                                                           FROM ponto'||stEntidade||'.escala_contrato
                                                       GROUP BY cod_contrato
                                                              , cod_escala
                                                       ) as max_escala_contrato
                                                    ON (max_escala_contrato.cod_contrato = escala_contrato.cod_contrato AND
                                                        max_escala_contrato.cod_escala = escala_contrato.cod_escala AND
                                                        max_escala_contrato.timestamp = escala_contrato.timestamp
                                                        )
                                                 WHERE NOT EXISTS (SELECT 1 
                                                                     FROM ponto'||stEntidade||'.escala_contrato_exclusao
                                                                    WHERE escala_contrato_exclusao.cod_contrato = max_escala_contrato.cod_contrato 
                                                                      AND escala_contrato_exclusao.cod_escala = max_escala_contrato.cod_escala
                                                                      AND escala_contrato_exclusao.timestamp = max_escala_contrato.timestamp)
                                                       AND NOT EXISTS (SELECT 1
                                                                         FROM ponto'||stEntidade||'.escala_exclusao
                                                                        WHERE escala_exclusao.cod_escala = escala.cod_escala)
                                                       AND escala_contrato.cod_contrato = '||inCodContrato||'
                                                       AND escala_turno.dt_turno = TO_DATE('||quote_literal(stDtTurno)||', ''dd/mm/yyyy'')');
                                                  
                -- caso exista turno com data corrente para o contrato, verifica o tipo
                IF stAux IS NOT NULL THEN
                    boEscala := true;
                    
                    IF stAux = 'T' THEN
                        boDiaTrabalho := true;
                    END IF;
                END IF; --tipo de dia escalal
        END IF; -- tipo de dia compensacao

        IF boCompensacao IS FALSE AND boEscala IS FALSE THEN
            -- verifica se existe algum feriado no calendario, que não esteja especificado
            stAux := selectIntoVarchar(' SELECT to_char(dt_feriado, ''dd/mm/yyyy'') as dt_feriado
                                           FROM calendario'||stEntidade||'.feriado
                                          WHERE dt_feriado = TO_DATE('||quote_literal(stDtTurno)||', ''dd/mm/yyyy'') 
                                            AND tipoferiado = ''F''');

            -- caso nao exista feriado, verifica o tipo de dia folga/trabalho, atravas dos dias cadastrados na grade do servidor
            IF stAux IS NULL THEN
                boGrade    := true;
                inCodGrade := selectIntoInteger('SELECT cod_grade FROM pessoal'||stEntidade||'.contrato_servidor WHERE cod_contrato = '||inCodContrato);
            
                --caso nao exista turno com data corrente para o contrato, verifica a existencia do dia na grade de horario como dia de trabalho
                --pega o numero do dia da semana para efetuar de -> para com cod_dia
                stAux := selectIntoVarchar('  SELECT faixa_turno.*
                                                FROM pessoal'||stEntidade||'.faixa_turno
                                                JOIN (  SELECT cod_grade
                                                                , max(timestamp) as timestamp
                                                            FROM pessoal'||stEntidade||'.faixa_turno
                                                        GROUP BY cod_grade) as max_faixa_turno
                                                    ON (     faixa_turno.cod_grade = max_faixa_turno.cod_grade 
                                                        AND faixa_turno.timestamp = max_faixa_turno.timestamp)
                                                WHERE faixa_turno.cod_grade = '||inCodGrade||' 
                                                    AND faixa_turno.cod_dia = (SELECT extract(dow FROM TO_DATE('||quote_literal(stDtTurno)||', ''dd/mm/yyyy''))+1)');
                IF stAux IS NOT NULL THEN
                    boDiaTrabalho := true;
                END IF; --tipo de dia grade
            ELSE
                boCalendario := true;
            END IF; --tipo de dia calendario
        END IF;
        
        IF boDiaTrabalho IS TRUE THEN
            stDescricaoTipo := 'Trabalho';
        ELSE
            stDescricaoTipo := 'Folga';
        END IF;
        
        /*-- ORIGEM --*/
        
        IF boCompensacao IS TRUE THEN
            stDescricaoOrigem := 'Compensação';
        ELSE
            IF boEscala IS TRUE THEN
                stDescricaoOrigem := 'Escala';
            ELSE
                IF boCalendario IS TRUE THEN
                    stDescricaoOrigem := 'Feriado';
                END IF;
            END IF;
        END IF;


        
        rwTipoOrigemTurnoServidor.compensacao           := boCompensacao;
        rwTipoOrigemTurnoServidor.compensacao_dt_falta  := stCompensacaoDtFalta;
        rwTipoOrigemTurnoServidor.calendario            := boCalendario;
        rwTipoOrigemTurnoServidor.escala                := boEscala;
        rwTipoOrigemTurnoServidor.grade                 := boGrade;
        rwTipoOrigemTurnoServidor.dia_trabalho          := boDiaTrabalho;
        rwTipoOrigemTurnoServidor.descricao_tipo        := stDescricaoTipo;
        rwTipoOrigemTurnoServidor.descricao_origem      := stDescricaoOrigem;
        RETURN rwTipoOrigemTurnoServidor;
        
END;
$$ language 'plpgsql';
