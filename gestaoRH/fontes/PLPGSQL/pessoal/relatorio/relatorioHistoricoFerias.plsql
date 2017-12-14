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
* relatorioHistoricoFerias
* Data de Criação   : 04/06/2009


* @author Analista      Dagiane Vieira
* @author Desenvolvedor Rafael Garbin

* @package URBEM
* @subpackage 

$Id:$
*/

CREATE OR REPLACE FUNCTION relatorioHistoricoFerias(VARCHAR,VARCHAR,VARCHAR,VARCHAR,VARCHAR) RETURNS SETOF colunasRelatorioHistoricoFerias AS $$
DECLARE
    stEntidade                  ALIAS FOR $1;
    stExercicio                 ALIAS FOR $2;
    stDataLimite                ALIAS FOR $3;
    stTipoFiltro                ALIAS FOR $4;
    stValoresFiltro             ALIAS FOR $5;
    stSql                       VARCHAR := '';
    stSqlAux                    VARCHAR := '';
    stDataRescisao              VARCHAR := '';
    dtLimite                    DATE;
    dtDataRescisao              DATE;
    inCodPeriodoMovimentacao    INTEGER;    
    rwRegistro                  colunasRelatorioHistoricoFerias%ROWTYPE;
    reRegistro                  RECORD;
    reFerias                    RECORD;
BEGIN
    stSql := 'SELECT cod_periodo_movimentacao
                FROM folhapagamento'||stEntidade||'.periodo_movimentacao
            ORDER BY cod_periodo_movimentacao DESC 
               LIMIT 1';
               
    inCodPeriodoMovimentacao := selectIntoInteger(stSql);
    -- Monta consulta principal
    stSql := 'SELECT registro
                   , cod_contrato
                   , nom_cgm
                   , numcgm
                   , desc_orgao  as lotacao
                   , desc_local  as local
                   , desc_regime as regime
                   , desc_funcao as funcao
                   , cod_orgao as cod_lotacao
                   , cod_local
                   , cod_regime
                   , to_char(dt_posse, ''dd/mm/yyyy'') as dt_posse
                   , to_char(dt_nomeacao, ''dd/mm/yyyy'') as dt_nomeacao
                FROM recuperarContratoServidor(  ''cgm,oo,l,car,f,anp''
                                               , '||quote_literal(stEntidade)||'
                                               , '||inCodPeriodoMovimentacao||'
                                               , '||quote_literal(stTipoFiltro)||'
                                               , '||quote_literal(stValoresFiltro)||'
                                               , '||quote_literal(stExercicio)||') 
            ORDER BY nom_cgm';
    FOR reRegistro IN EXECUTE stSql LOOP
        dtLimite := to_date(stDataLimite, 'dd/mm/yyyy');

        -- Caso contrato esteja rescindido, altera a data limite para a data de rescisão do contrato
        stSqlAux := ' SELECT to_char(dt_rescisao, ''dd/mm/yyyy'') as dt_rescisao
                        FROM pessoal'||stEntidade||'.contrato_servidor_caso_causa
                       WHERE cod_contrato = '||reRegistro.cod_contrato;        
        
        stDataRescisao := selectIntoVarchar(stSqlAux);
        
        IF stDataRescisao IS NOT NULL THEN
            dtDataRescisao := to_date(stDataRescisao, 'dd/mm/yyyy');
            IF dtLimite > dtDataRescisao THEN 
                dtLimite := dtDataRescisao;
            END IF;
        END IF;

        rwRegistro.registro               := reRegistro.registro;
        rwRegistro.cod_contrato           := reRegistro.cod_contrato;
        rwRegistro.numcgm                 := reRegistro.numcgm;
        rwRegistro.nom_cgm                := reRegistro.nom_cgm;
        rwRegistro.dt_inicio_contagem     := recuperarDataInicioContagemTempoContrato(stEntidade, rwRegistro.cod_contrato, stExercicio);
        rwRegistro.lotacao                := reRegistro.lotacao;
        rwRegistro.local                  := reRegistro.local;
        rwRegistro.regime                 := reRegistro.regime;
        rwRegistro.funcao                 := reRegistro.funcao;
        rwRegistro.dt_posse               := reRegistro.dt_posse;
        rwRegistro.dt_nomeacao            := reRegistro.dt_nomeacao;
        rwRegistro.cod_regime             := reRegistro.cod_regime;
        rwRegistro.cod_lotacao            := reRegistro.cod_lotacao;
        rwRegistro.cod_local              := reRegistro.cod_local;
        
        -- Verifica se o contrato possui lancamento de férias
        stSqlAux := 'SELECT true
                       FROM pessoal'||stEntidade||'.ferias
                      WHERE cod_contrato = '||reRegistro.cod_contrato;
                      
        IF selectIntoBoolean(stSqlAux) = TRUE THEN 
            stSqlAux := 'SELECT *
                              , tipo_folha.descricao as folha
                              , ( CASE WHEN pagar_13 = ''t''  THEN ''Sim''
                                                              ELSE ''Não'' END) as pagar_decimo
                              , mes_competencia||''/''||ano_competencia as mes_pagamento
                              , forma_pagamento_ferias.dias as dias
                              , forma_pagamento_ferias.abono as abono
                           FROM pessoal'||stEntidade||'.ferias
                           JOIN pessoal'||stEntidade||'.lancamento_ferias
                             ON ferias.cod_ferias = lancamento_ferias.cod_ferias
                           JOIN folhapagamento'||stEntidade||'.tipo_folha
                             ON lancamento_ferias.cod_tipo = tipo_folha.cod_tipo
                           JOIN pessoal'||stEntidade||'.forma_pagamento_ferias
                             ON ferias.cod_forma = forma_pagamento_ferias.cod_forma
                          WHERE cod_contrato = '||reRegistro.cod_contrato||'
                       ORDER BY ferias.dt_inicial_aquisitivo';
                       
            FOR reFerias IN EXECUTE stSqlAux LOOP
                rwRegistro.dt_inicial_aquisitivo := reFerias.dt_inicial_aquisitivo;
                rwRegistro.dt_final_aquisitivo   := reFerias.dt_final_aquisitivo;
                rwRegistro.dt_inicial_gozo       := reFerias.dt_inicio;
                rwRegistro.dt_final_gozo         := reFerias.dt_fim;
                rwRegistro.faltas                := reFerias.faltas;
                rwRegistro.dias_ferias           := reFerias.dias_ferias;
                rwRegistro.dias_abono            := reFerias.dias_abono;
                rwRegistro.dias                  := reFerias.dias;
                rwRegistro.abono                 := reFerias.abono;
                rwRegistro.mes_pagamento         := reFerias.mes_pagamento;
                rwRegistro.folha                 := reFerias.folha;
                rwRegistro.pagar_13              := reFerias.pagar_decimo;

                IF reFerias.dt_final_aquisitivo >= dtLimite THEN 
                    rwRegistro.dt_final_aquisitivo := dtLimite;
                    EXIT;
                END IF; 

                RETURN NEXT rwRegistro;
            END LOOP;
        ELSE
            rwRegistro.dt_inicial_aquisitivo := rwRegistro.dt_inicio_contagem -1;
            rwRegistro.dt_final_aquisitivo   := rwRegistro.dt_inicio_contagem -1;
        END IF;

        -- Percorrendo todos os periodos aquisitivos do contrato até a data limite
        WHILE (rwRegistro.dt_final_aquisitivo + interval '1 year')::date <= dtLimite LOOP
            rwRegistro.dt_inicial_aquisitivo := rwRegistro.dt_final_aquisitivo+1;
            rwRegistro.dt_final_aquisitivo   := (rwRegistro.dt_inicial_aquisitivo + interval '1 year')::date -1;
            rwRegistro.dt_inicial_gozo       := NULL;
            rwRegistro.dt_final_gozo         := NULL;
            rwRegistro.faltas                := NULL;
            rwRegistro.dias_ferias           := NULL;
            rwRegistro.dias_abono            := NULL;
            rwRegistro.dias                  := NULL;
            rwRegistro.abono                 := NULL;
            rwRegistro.mes_pagamento         := NULL;
            rwRegistro.folha                 := NULL;
            rwRegistro.pagar_13              := NULL;  

            rwRegistro.cod_contrato          := reRegistro.cod_contrato;  
            rwRegistro.dt_inicio_contagem    := rwRegistro.dt_inicio_contagem;
            rwRegistro.dt_final_aquisitivo   := (rwRegistro.dt_inicial_aquisitivo + interval '1 year')::date-1;            

            IF rwRegistro.dt_final_aquisitivo >= dtLimite THEN
                rwRegistro.dt_final_aquisitivo := dtLimite;
            END IF;

            RETURN NEXT rwRegistro;
        END LOOP;

        IF (rwRegistro.dt_final_aquisitivo + interval '1 year')::date > dtLimite 
            AND rwRegistro.dt_final_aquisitivo < dtLimite THEN

            rwRegistro.dt_inicial_aquisitivo := rwRegistro.dt_final_aquisitivo+1;
            rwRegistro.dt_inicial_gozo       := NULL;
            rwRegistro.dt_final_gozo         := NULL;
            rwRegistro.faltas                := NULL;
            rwRegistro.dias_ferias           := NULL;
            rwRegistro.dias_abono            := NULL;
            rwRegistro.dias                  := NULL;
            rwRegistro.abono                 := NULL;
            rwRegistro.mes_pagamento         := NULL;
            rwRegistro.folha                 := NULL;
            rwRegistro.pagar_13              := NULL;  
            rwRegistro.dt_final_aquisitivo := dtLimite;

            RETURN NEXT rwRegistro;
        END IF;

    END LOOP;
END;
$$ LANGUAGE 'plpgsql';
