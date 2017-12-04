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
/* recuperaRelatorioEspelhoPontoResumo
 * 
 * Data de Criação   : 23/10/2008


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */
CREATE OR REPLACE FUNCTION recuperaRelatorioEspelhoPontoResumo(INTEGER,INTEGER,DATE,DATE,VARCHAR) RETURNS SETOF colunasRelatorioCartaoPontoResumo AS $$
DECLARE
    inCodContrato                   ALIAS FOR $1;
    inCodConfiguracaoPonto          ALIAS FOR $2;
    dtInicioPeriodoPar              ALIAS FOR $3;
    dtFimPeriodoPar                 ALIAS FOR $4;
    stEntidade                      ALIAS FOR $5;

    dtInicioPeriodo         DATE;
    dtFimPeriodo            DATE;
    rwCartaoPontoResumo     colunasRelatorioCartaoPontoResumo%ROWTYPE;
    stSql                   VARCHAR;
    reConfiguracao          RECORD;
    reRegistro              RECORD;
    reExtras                RECORD;
    crCursor                REFCURSOR;
    inQtdDSR                INTEGER:=0;
    inFaltas                INTEGER:=0;
    inDescontoDSR           INTEGER:=0;
    
    stHorasAbono            VARCHAR:='00:00';
    stHorasFalta            VARCHAR:='00:00';
    stHoraAtrasos           VARCHAR:='00:00';
    stHoraFaltas            VARCHAR:='00:00';
    stHoraExtras            VARCHAR:='00:00';
    stHoraExtNot            VARCHAR:='00:00';
    stHoraADNot             VARCHAR:='00:00';
    stBancoHoras            VARCHAR:='00:00';
    stGradePadrao           VARCHAR;
    stHorasTrabalho         VARCHAR;   
    stExtras                VARCHAR:='';   
    stHoraFaltaSemana       VARCHAR:='00:00';
    stAbonoDSR              VARCHAR:='00:00';
    stDescDSR               VARCHAR:='00:00';
    stCalculo               VARCHAR:='00:00';
    arFaltas                INTEGER[];
    arHrTrabalho            INTEGER[];
DECLARE
BEGIN

    stSql := 'SELECT configuracao_parametros_gerais.*
                   , replace(dias_semana.nom_dia,''-feira'','''') as nom_dia 
                   , falta_dsr.horas as faltas
                   , horas_desconto_dsr.horas as desconto
                   , COALESCE(configuracao_banco_horas.ativar_banco, false) as ativar_banco
                FROM ponto'|| stEntidade ||'.configuracao_parametros_gerais
                JOIN ponto'|| stEntidade ||'.configuracao_relogio_ponto
                  ON configuracao_relogio_ponto.cod_configuracao = configuracao_parametros_gerais.cod_configuracao
                 AND configuracao_relogio_ponto.ultimo_timestamp = configuracao_parametros_gerais.timestamp
           LEFT JOIN ponto'|| stEntidade ||'.falta_dsr
                  ON configuracao_parametros_gerais.cod_configuracao = falta_dsr.cod_configuracao
                 AND configuracao_parametros_gerais.timestamp = falta_dsr.timestamp
           LEFT JOIN ponto'|| stEntidade ||'.horas_desconto_dsr
                  ON configuracao_parametros_gerais.cod_configuracao = horas_desconto_dsr.cod_configuracao
                 AND configuracao_parametros_gerais.timestamp = horas_desconto_dsr.timestamp
           LEFT JOIN ponto'|| stEntidade ||'.configuracao_banco_horas
                  ON configuracao_relogio_ponto.cod_configuracao = configuracao_banco_horas.cod_configuracao
                 AND configuracao_relogio_ponto.ultimo_timestamp = configuracao_banco_horas.timestamp
                JOIN administracao.dias_semana
                  ON configuracao_parametros_gerais.cod_dia_dsr = dias_semana.cod_dia
               WHERE NOT EXISTS (SELECT 1
                                   FROM ponto'|| stEntidade ||'.configuracao_relogio_ponto_exclusao
                                  WHERE configuracao_relogio_ponto_exclusao.cod_configuracao = configuracao_relogio_ponto.cod_configuracao)
                 AND configuracao_parametros_gerais.cod_configuracao = '|| inCodConfiguracaoPonto;
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO reConfiguracao;
    CLOSE crCursor; 

    dtInicioPeriodo := dtInicioPeriodoPar;
    dtFimPeriodo    := dtFimPeriodoPar;
    WHILE dtInicioPeriodo <= dtFimPeriodo LOOP
        IF dia_semana(dtInicioPeriodo) = reConfiguracao.nom_dia THEN
            inQtdDSR := inQtdDSR + 1;
        END IF;
        dtInicioPeriodo := dtInicioPeriodo+1;
    END LOOP;

    stSql := 'SELECT relogio_ponto_justificativa.*
                FROM ponto'|| stEntidade ||'.relogio_ponto_justificativa
                JOIN (  SELECT cod_contrato
                             , cod_justificativa
                             , sequencia
                             , max(timestamp) as timestamp
                         FROM ponto'|| stEntidade ||'.relogio_ponto_justificativa
                      GROUP BY cod_contrato
                             , cod_justificativa
                             , sequencia) as max_dados_relogio_ponto
                  ON relogio_ponto_justificativa.cod_contrato = max_dados_relogio_ponto.cod_contrato
                 AND relogio_ponto_justificativa.timestamp = max_dados_relogio_ponto.timestamp
                 AND relogio_ponto_justificativa.cod_justificativa = max_dados_relogio_ponto.cod_justificativa
                 AND relogio_ponto_justificativa.sequencia = max_dados_relogio_ponto.sequencia
                 
               WHERE relogio_ponto_justificativa.cod_contrato = '|| inCodContrato ||'
                 AND (relogio_ponto_justificativa.periodo_inicio  BETWEEN '|| quote_literal(dtInicioPeriodoPar) ||'
                                                                      AND '|| quote_literal(dtFimPeriodoPar)    ||'
                       OR
                      relogio_ponto_justificativa.periodo_termino BETWEEN '|| quote_literal(dtInicioPeriodoPar) ||'
                                                                      AND '|| quote_literal(dtFimPeriodoPar)    ||')
                 AND NOT EXISTS (SELECT 1
                                   FROM ponto'|| stEntidade ||'.relogio_ponto_justificativa_exclusao
                                  WHERE relogio_ponto_justificativa.cod_contrato = relogio_ponto_justificativa_exclusao.cod_contrato
                                    AND relogio_ponto_justificativa.cod_justificativa = relogio_ponto_justificativa_exclusao.cod_justificativa
                                    AND relogio_ponto_justificativa.sequencia = relogio_ponto_justificativa_exclusao.sequencia
                                    AND relogio_ponto_justificativa.timestamp = relogio_ponto_justificativa_exclusao.timestamp)';
                                    
    FOR reRegistro IN EXECUTE stSql LOOP        
        dtInicioPeriodo := reRegistro.periodo_inicio;
        dtFimPeriodo    := reRegistro.periodo_termino;
        WHILE dtInicioPeriodo <= dtFimPeriodo LOOP
            stHorasAbono := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHorasAbono) ||' + INTERVAL '|| quote_literal(reRegistro.horas_abonar) ||' ');   
            dtInicioPeriodo := dtInicioPeriodo+1;
        END LOOP;
    END LOOP;

--     stSql := 'SELECT *
--                    , dias_turno.cod_dia
--                 FROM recuperaRelatorioEspelhoPonto('|| inCodContrato ||','|| inCodConfiguracaoPonto ||','|| inCodGrade ||','|| quote_literal(to_char(dtInicioPeriodoPar,'dd/mm/yyyy')) ||','|| quote_literal(to_char(dtFimPeriodoPar,'dd/mm/yyyy')) ||','|| quote_literal(stEntidade) ||' )
--                 JOIN pessoal.dias_turno
--                   ON replace(dias_turno.nom_dia,''-feira'','''') = dia';
    stSql := 'SELECT relatorio_espelho_ponto.*
                   , dias_turno.cod_dia
                FROM ponto'|| stEntidade ||'.relatorio_espelho_ponto
                JOIN pessoal'|| stEntidade ||'.dias_turno
                  ON replace(dias_turno.nom_dia,''-feira'','''') = relatorio_espelho_ponto.dia
            ORDER BY relatorio_espelho_ponto.data ASC';
    FOR reExtras IN EXECUTE stSql LOOP 
        --FALTAS
        arFaltas := string_to_array(reExtras.faltas,':');
        arHrTrabalho := string_to_array(reExtras.hs_trab,':'); 
        IF arHrTrabalho[1] > 0 THEN
            inFaltas := inFaltas + (arFaltas[1]/arHrTrabalho[1]);
        END IF;

        --ABONO DSR
        --DESC  DSR
        IF reExtras.faltas IS NOT NULL AND reExtras.faltas != '' THEN
            stHoraFaltaSemana := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHoraFaltaSemana) ||' + INTERVAL '|| quote_literal(reExtras.faltas) ||' ');
        END IF;
        
        IF reExtras.cod_dia = 7 OR reExtras.data = to_char(last_day(to_date(reExtras.data,'dd/mm/yyyy')),'dd/mm/yyyy') THEN
            IF stHoraFaltaSemana::interval > reConfiguracao.faltas::interval AND reExtras.cod_dia = 7 THEN
                inDescontoDSR := inDescontoDSR + 1;
            END IF;
            stHoraFaltaSemana := '00:00';
        END IF;
        

        --EXTRAS
        IF reExtras.extras IS NOT NULL AND reExtras.extras != '' THEN
            stHoraExtras := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHoraExtras) ||' + INTERVAL '|| quote_literal(reExtras.extras) ||' ');
        END IF;

        --ATRASOS
        IF reExtras.atrasos IS NOT NULL AND reExtras.atrasos != '' THEN
            stHoraAtrasos := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHoraAtrasos) ||' + INTERVAL '|| quote_literal(reExtras.atrasos) ||' ');
        END IF;

        --FALTAS
        IF reExtras.faltas IS NOT NULL AND reExtras.faltas != '' THEN
            stHoraFaltas := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHoraFaltas) ||' + INTERVAL '|| quote_literal(reExtras.faltas) ||' ');   
        END IF;

        --EXT NOT     
        IF reExtras.ext_not IS NOT NULL AND reExtras.ext_not != '' THEN
            stHoraExtNot := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHoraExtNot) ||' + INTERVAL '|| quote_literal(reExtras.ext_not) ||' ');   
        END IF;

        --AD NOT
        IF reExtras.ad_not IS NOT NULL AND reExtras.ad_not != '' THEN
            stHoraADNot := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHoraADNot) ||' + INTERVAL '|| quote_literal(reExtras.ad_not) ||' ');   
        END IF;
    END LOOP;
    --ABONO DSR
    IF reConfiguracao.desconto IS NOT NULL THEN
        stAbonoDSR := inQtdDSR*reConfiguracao.desconto;
        --DESC  DSR
        stDescDSR  := inDescontoDSR*reConfiguracao.desconto;
        IF stDescDSR > stAbonoDSR THEN
            stDescDSR  := stAbonoDSR;
            stAbonoDSR := '00:00';
        ELSE
            stAbonoDSR := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stAbonoDSR) ||' - INTERVAL '|| quote_literal(stDescDSR) ||' ');
        END IF;
    ELSE
        stAbonoDSR := '00:00';
        stDescDSR  := '00:00';
    END IF;
   
    --BANCO DE HORAS
    IF reConfiguracao.ativar_banco IS TRUE THEN
        stBancoHoras := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHoraExtras) ||' + INTERVAL '|| quote_literal(stHoraExtNot) ||' ');
        stBancoHoras := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stBancoHoras) ||' - INTERVAL '|| quote_literal(stHoraFaltas) ||' ');
    END IF;

    --Extras - (Atrasos + Faltas)
    stCalculo := selectIntoVarchar('SELECT INTERVAL '|| quote_literal(stHoraExtras) ||' + INTERVAL '|| quote_literal(stHoraExtNot) ||' - (INTERVAL '|| quote_literal(stHoraAtrasos) ||' + INTERVAL '|| quote_literal(stHoraFaltas) ||') '); 

    rwCartaoPontoResumo.qtd_dsr             := inQtdDSR;
    rwCartaoPontoResumo.abono_dsr           := stAbonoDSR;
    rwCartaoPontoResumo.desc_dsr            := stDescDSR;
    rwCartaoPontoResumo.abono_justificado   := stHorasAbono;
    rwCartaoPontoResumo.faltas              := inFaltas;
    rwCartaoPontoResumo.extras              := stCalculo;
    rwCartaoPontoResumo.banco_horas         := stBancoHoras;
    RETURN NEXT rwCartaoPontoResumo;
END
$$ LANGUAGE 'plpgsql';

