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

CREATE OR REPLACE FUNCTION recuperaRelogioPontoPeriodo (VARCHAR, VARCHAR, INTEGER, VARCHAR) RETURNS SETOF colunasRelogioPontoPeriodo AS $$
DECLARE
    parDataInicial      VARCHAR:=$1;
    parDataFinal        VARCHAR:=$2;
    inCodContrato       INTEGER:=$3;
    stEntidade          VARCHAR:=$4;
    stSQL               VARCHAR;
    rwRelogioPonto      colunasRelogioPontoPeriodo%ROWTYPE;
    reRegistro          RECORD;
    reRegistroInterno   RECORD;
    boSair              BOOLEAN:=false;
    dtAux               DATE;
    inCodPonto          INTEGER;
    stHorario           VARCHAR;
    stDataInicial       DATE;
    stDataFinal         DATE;
BEGIN
    stDataInicial := to_date(parDataInicial, 'dd/mm/yyyy');
    stDataFinal   := to_date(parDataFinal, 'dd/mm/yyyy');

    dtAux := stDataInicial;
    dtAux := dtAux-1;    
    
    LOOP
        
        IF boSair = TRUE THEN
            EXIT;
        END IF;
        -- Busca Proximo dia do Periodo Informado
        stSQL := 'SELECT to_char('''||dtAux||'''::date + 1,''dd/mm/yyyy'')';
        rwRelogioPonto.data := selectintovarchar(stSQL);
        dtAux := dtAux+1;    

        --Busca horario padrão de trabalho
        rwRelogioPonto.cod_contrato         := inCodContrato;
        rwRelogioPonto.horario_padrao       := recuperaHorarioPadrao(rwRelogioPonto.data, inCodContrato, stEntidade);
        rwRelogioPonto.carga_horaria_padrao := recuperaHorasTrabalhadas(rwRelogioPonto.horario_padrao);
        
        -- Busca Origem e Tipo
        stSQL := 'SELECT descricao_tipo as tipo
                       , descricao_origem as origem
                    FROM recuperaTipoOrigemTurnoServidor('||inCodContrato||', '||quote_literal(rwRelogioPonto.data)||', '||quote_literal(stEntidade)||')';
        
        FOR reRegistro IN  EXECUTE stSQL
        LOOP
            rwRelogioPonto.tipo            := reRegistro.tipo;    
            rwRelogioPonto.origem          := reRegistro.origem;
        END LOOP;  

        --Busca Horarios
        stHorario := recuperaHorarioTrabalhado(rwRelogioPonto.data, inCodContrato, stEntidade);
        
        IF trim(lower(rwRelogioPonto.tipo)) = 'folga' THEN
            rwRelogioPonto.horas_trabalho  := '00:00';
        ELSE
            rwRelogioPonto.horas_trabalho := recuperaHorasTrabalhadasEmHorarioPadrao(rwRelogioPonto.data, inCodContrato, stEntidade, '', '');
        END IF;

        stSQL := 'SELECT to_char(interval '||quote_literal(trim(rwRelogioPonto.carga_horaria_padrao))||' - interval '||quote_literal(trim(rwRelogioPonto.horas_trabalho))||', ''hh24:mi'')';
        rwRelogioPonto.horas_faltas := selectintovarchar(stSQL);

        -- Busca Justificativa
        stSQL := '    SELECT justificativa.descricao
                           , justificativa.anular_faltas
                           , justificativa.lancar_dias_trabalho
                           , to_char(relogio_ponto_justificativa.horas_falta,''hh24:mi'') as horas_falta
                           , to_char(relogio_ponto_justificativa.horas_abonar,''hh24:mi'') as horas_abonar
                        FROM ponto'||stEntidade||'.relogio_ponto_justificativa
                  INNER JOIN ( SELECT cod_contrato
                                    , cod_justificativa
                                    , sequencia
                                    , max(timestamp) as timestamp
                                 FROM ponto'||stEntidade||'.relogio_ponto_justificativa
                             GROUP BY cod_contrato
                                    , cod_justificativa
                                    , sequencia) as max_relogio_ponto_justificativa
                          ON relogio_ponto_justificativa.cod_contrato = max_relogio_ponto_justificativa.cod_contrato
                         AND relogio_ponto_justificativa.cod_justificativa = max_relogio_ponto_justificativa.cod_justificativa
                         AND relogio_ponto_justificativa.sequencia = max_relogio_ponto_justificativa.sequencia
                         AND relogio_ponto_justificativa.timestamp = max_relogio_ponto_justificativa.timestamp
                  INNER JOIN ponto'||stEntidade||'.justificativa
                          ON relogio_ponto_justificativa.cod_justificativa = justificativa.cod_justificativa
                   LEFT JOIN ponto'||stEntidade||'.justificativa_horas
                          ON justificativa.cod_justificativa = justificativa_horas.cod_justificativa
                       WHERE relogio_ponto_justificativa.cod_contrato = '||inCodContrato||'
                         AND '||quote_literal(dtAux)||' between relogio_ponto_justificativa.periodo_inicio AND relogio_ponto_justificativa.periodo_termino
                         AND NOT EXISTS (SELECT 1
                                           FROM ponto'||stEntidade||'.relogio_ponto_justificativa_exclusao
                                          WHERE relogio_ponto_justificativa_exclusao.cod_contrato =relogio_ponto_justificativa.cod_contrato
                                            AND relogio_ponto_justificativa_exclusao.cod_justificativa =relogio_ponto_justificativa.cod_justificativa
                                            AND relogio_ponto_justificativa_exclusao.sequencia =relogio_ponto_justificativa.sequencia
                                            AND relogio_ponto_justificativa_exclusao.timestamp =relogio_ponto_justificativa.timestamp)';
        
        rwRelogioPonto.justificativa_afastamento := '';
        rwRelogioPonto.horas_faltas_anuladas := '00:00';
        rwRelogioPonto.horas_abonadas        := '00:00';

        FOR reRegistro IN EXECUTE stSQL LOOP
            rwRelogioPonto.justificativa_afastamento := reRegistro.descricao;

            --Verifica se é para lancar justificativa apenas para dias de trabalho
            IF reRegistro.lancar_dias_trabalho = 't' THEN
                IF trim(lower(rwRelogioPonto.tipo)) = 'folga' THEN
                    rwRelogioPonto.justificativa_afastamento := '';
                END IF;
            END IF;

            --Verifica horas abonada e faltas anuladas
            IF reRegistro.anular_faltas = 't' THEN
                rwRelogioPonto.horas_faltas_anuladas := rwRelogioPonto.horas_faltas;
                rwRelogioPonto.horas_faltas          := '00:00';
                rwRelogioPonto.horas_abonadas        := '00:00';
            ELSE
                rwRelogioPonto.horas_faltas_anuladas := reRegistro.horas_falta;
                rwRelogioPonto.horas_abonadas        := reRegistro.horas_abonar;    

                --Busca Horas Falta corrigida
                IF reRegistro.horas_falta::TIME >= rwRelogioPonto.horas_faltas::TIME THEN
                    rwRelogioPonto.horas_faltas_anuladas := rwRelogioPonto.horas_faltas;
                    rwRelogioPonto.horas_faltas := '00:00';
                ELSE
                    stSQL := 'SELECT to_char(interval '||quote_literal(trim(rwRelogioPonto.horas_faltas))||' - interval '||quote_literal(trim(reRegistro.horas_falta))||', ''hh24:mi'') as horas_faltas';
                    
                    FOR reRegistroInterno IN  EXECUTE stSQL
                    LOOP
                        --Verifica se o resultado deu negativo
                        stSQL := 'SELECT interval '||quote_literal(trim(rwRelogioPonto.horas_faltas))||' < interval '||quote_literal(trim(reRegistro.horas_falta))||'';
                        IF selectintovarchar(stSQL) = 't' THEN
                            rwRelogioPonto.horas_faltas := '00:00';
                        ELSE
                            rwRelogioPonto.horas_faltas := reRegistroInterno.horas_faltas;
                        END IF;
                    END LOOP;
                    IF trim(rwRelogioPonto.horas_faltas) = '00:00' THEN
                        rwRelogioPonto.horas_faltas_anuladas := '00:00';
                    END IF;
                END IF;
            END IF;
        END LOOP;
        
        IF trim(rwRelogioPonto.justificativa_afastamento) = '' THEN
            --Busca Assentamento
            stSQL := '  SELECT trim(assentamento_assentamento.descricao) as descricao_assentamento
                          FROM pessoal'||stEntidade||'.assentamento_gerado_contrato_servidor
                    INNER JOIN pessoal'||stEntidade||'.assentamento_gerado
                            ON assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                    INNER JOIN ( SELECT cod_assentamento_gerado
                                      , MAX(timestamp) as timestamp
                                   FROM pessoal'||stEntidade||'.assentamento_gerado
                               GROUP BY cod_assentamento_gerado ) as max_assentamento_gerado
                            ON assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado
                           AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp
                    INNER JOIN pessoal'||stEntidade||'.assentamento_assentamento
                            ON assentamento_gerado.cod_assentamento = assentamento_assentamento.cod_assentamento
                    INNER JOIN pessoal'||stEntidade||'.classificacao_assentamento
                            ON assentamento_assentamento.cod_classificacao = classificacao_assentamento.cod_classificacao                    
                    INNER JOIN pessoal'||stEntidade||'.tipo_classificacao                                                           
                            ON classificacao_assentamento.cod_tipo = tipo_classificacao.cod_tipo                                             
                         WHERE assentamento_gerado_contrato_servidor.cod_contrato = '||inCodContrato||'
                           AND classificacao_assentamento.cod_tipo = 2  -- tipo de classificação para assentamento de afstamento temporario
                           AND '||quote_literal(dtAux)||' between assentamento_gerado.periodo_inicial AND assentamento_gerado.periodo_final
                           AND NOT EXISTS ( SELECT 1
                                              FROM pessoal'||stEntidade||'.assentamento_gerado_excluido
                                             WHERE assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                                               AND assentamento_gerado_excluido.timestamp = assentamento_gerado.timestamp)';
            
            rwRelogioPonto.justificativa_afastamento  := selectintovarchar(stSQL);

            IF trim(rwRelogioPonto.justificativa_afastamento)!='' THEN
                rwRelogioPonto.horas_trabalho    := '00:00'; 
                rwRelogioPonto.horas_faltas      := '00:00';
            END IF;  
        END IF;

        IF trim(lower(rwRelogioPonto.tipo)) = 'folga' THEN
            rwRelogioPonto.horas_trabalho    := '00:00'; 
            rwRelogioPonto.horas_faltas      := '00:00';
        END IF;

        rwRelogioPonto.dia     := dia_semana(dtAux);
        rwRelogioPonto.horario := stHorario;
        
        RETURN NEXT rwRelogioPonto;
        
        IF dtAux >= stDataFinal THEN
            boSair = TRUE;            
        END IF;
    END LOOP;
END
$$ LANGUAGE 'plpgsql';
