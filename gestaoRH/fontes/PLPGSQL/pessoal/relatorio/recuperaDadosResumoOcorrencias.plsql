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
 * PL recuperaDadosResumoOcorrencias
 * Data de Criação   : 09/09/2008


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */


CREATE OR REPLACE FUNCTION recuperaDadosResumoOcorrencias(integer,varchar,date,date,varchar) returns SETOF colunasDadosResumoOcorrencias AS $$
DECLARE
    inCodContrato       ALIAS FOR $1;
    stExercicio         ALIAS FOR $2;    
    dtPeriodoInicial    ALIAS FOR $3;
    dtPeriodoFinalPar   ALIAS FOR $4;
    stEntidade          ALIAS FOR $5;   
    stSql               VARCHAR;
    stContagemTempo     VARCHAR;
    dtRescisao          DATE;
    dtPeriodoFinal      DATE;
    reRegistro          RECORD;
    rwRetorno           colunasDadosResumoOcorrencias%ROWTYPE;
BEGIN
    dtPeriodoFinal := dtPeriodoFinalPar;
    stSql := 'SELECT dt_rescisao
                FROM pessoal'|| stEntidade ||'.contrato_servidor_caso_causa
               WHERE cod_contrato = '|| inCodContrato;
    stContagemTempo := selectIntoVarchar(stSql);                   
    dtRescisao := stContagemTempo::DATE;
    IF dtRescisao is not null THEN
        dtPeriodoFinal := dtRescisao;
    END IF;
    
    
    stSql := 'SELECT descricao
                   , abreviacao
                   , operador
                   , sum((periodo_final-periodo_inicial))+1 as qtd_dias
                FROM (SELECT assentamento_assentamento.descricao
                           , assentamento_assentamento.abreviacao
                           , assentamento_operador.descricao as operador
                           , CASE WHEN assentamento_gerado.periodo_inicial < '''||dtPeriodoInicial||'''
                                  THEN '''||dtPeriodoInicial||'''
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
                              GROUP BY cod_assentamento) as max_assentamento
                           , pessoal'|| stEntidade ||'.assentamento_operador
                       WHERE assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                         AND assentamento_gerado.cod_assentamento = assentamento_assentamento.cod_assentamento
                         AND assentamento_assentamento.cod_assentamento = assentamento.cod_assentamento
                         AND assentamento.cod_assentamento = max_assentamento.cod_assentamento
                         AND assentamento.timestamp = max_assentamento.timestamp
                         AND assentamento_assentamento.cod_operador = assentamento_operador.cod_operador
                         AND (to_char(assentamento_gerado.periodo_inicial,''yyyy-mm'') between '|| quote_literal(to_char(dtPeriodoInicial,'yyyy-mm')) ||'
                                                                                           AND '|| quote_literal(to_char(dtPeriodoFinal,'yyyy-mm')) ||'
                          or  to_char(assentamento_gerado.periodo_final,''yyyy-mm'')   between '|| quote_literal(to_char(dtPeriodoInicial,'yyyy-mm')) ||'
                                                                                           AND '|| quote_literal(to_char(dtPeriodoFinal,'yyyy-mm')) ||')
                         AND assentamento.grade_efetividade is true 
                         AND assentamento_gerado_contrato_servidor.cod_contrato = '|| quote_literal(inCodContrato) ||'
                         AND assentamento_gerado.cod_assentamento_gerado NOT IN (SELECT cod_assentamento_gerado 
                                                                                      FROM pessoal.assentamento_gerado_excluido
                                                                                      WHERE assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                                                                                        AND assentamento_gerado_excluido.timestamp = assentamento_gerado.timestamp
                                                                                )) AS tabela
                    GROUP BY descricao
                           , abreviacao
                           , operador                 
                    ORDER BY abreviacao';
    FOR reRegistro IN EXECUTE stSql LOOP        
        rwRetorno.assentamento  := COALESCE(reRegistro.abreviacao,'') ||'-'|| reRegistro.descricao;
        rwRetorno.operador      := reRegistro.operador;
        rwRetorno.qtd_dias      := reRegistro.qtd_dias;
        RETURN NEXT rwRetorno;
    END LOOP;
    rwRetorno.assentamento  := 'FA-Falta Abreviação';
    rwRetorno.operador      := '';
    rwRetorno.qtd_dias      := null;
    RETURN NEXT rwRetorno;    
END;    
$$ language 'plpgsql';
