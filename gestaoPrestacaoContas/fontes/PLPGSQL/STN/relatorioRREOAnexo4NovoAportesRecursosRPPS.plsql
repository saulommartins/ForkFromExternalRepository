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
CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo4novo_aportes_recursos_rpps(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stDataInicial           ALIAS FOR $2;
    stDataFinal             ALIAS FOR $3;
    stCodEntidades          ALIAS FOR $4;
    dtInicioAno             VARCHAR := '';
    stSql                   VARCHAR := '';
    reRegistro              RECORD;
    dtInicialAnterior       VARCHAR := '';
    dtFinalAnterior         VARCHAR := '';
    stExercicioAnterior     VARCHAR := '';
    dtInicioAnoAnterior     VARCHAR := '';
    arDatas                 VARCHAR[] ;

BEGIN

    stExercicioAnterior :=  trim(to_char((to_number(stExercicio, '99999')-1), '99999'));
    
    dtInicioAno := '01/01/' || stExercicio;
    
    dtInicioAnoAnterior := '01/01/' || stExercicioAnterior;
    dtInicialAnterior := SUBSTRING(stDataInicial,0,6) || stExercicioAnterior;
    dtFinalAnterior := SUBSTRING(stDataFinal,0,6) || stExercicioAnterior;

    stSql := '
    SELECT cod_aporte
         , nom_aporte
         , nom_grupo
         , cod_grupo
         , exercicio
         , SUM(previsao_inicial) AS previsao_inicial
         , SUM(no_bimestre) AS no_bimestre
         , SUM(ate_bimestre) AS ate_bimestre
         , SUM(ate_bimestre_anterior) AS ate_bimestre_anterior
    FROM (
            SELECT aporte_recurso_rpps.cod_aporte
                 , receita.cod_receita
                 , conta_receita.cod_estrutural AS mascara_classificacao
                 , conta_receita.descricao AS nom_conta
                 , aporte_recurso_rpps.descricao AS nom_aporte
                 , aporte_recurso_rpps_grupo.descricao AS nom_grupo
                 , aporte_recurso_rpps_grupo.cod_grupo
                 , aporte_recurso_rpps.exercicio
                 , CASE WHEN conta_receita.cod_estrutural IS NOT NULL THEN
                             COALESCE(orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                                     ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     , ''' || stCodEntidades || '''
                             ), 0.00)
                   ELSE
                            0.00
                   END AS previsao_inicial
                 , CASE WHEN conta_receita.cod_estrutural IS NOT NULL THEN
                            COALESCE(orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                                     ,''' || stDataInicial || '''
                                                                     ,''' || stDataFinal || '''
                            ), 0.00)
                   ELSE
                            0.00
                   END AS no_bimestre
                 , CASE WHEN conta_receita.cod_estrutural IS NOT NULL THEN
                            COALESCE(orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                                     ,''' || dtInicioAno || '''
                                                                     ,''' || stDataFinal || '''
                            ), 0.00)
                   ELSE
                            0.00
                   END AS ate_bimestre
                 , CASE WHEN conta_receita.cod_estrutural IS NOT NULL THEN
                            COALESCE(orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                                     ,''' || dtInicioAnoAnterior || ''' 
                                                                     ,''' || dtFinalAnterior || '''
                            ), 0.00)
                   ELSE
                            0.00
                   END AS ate_bimestre_anterior
              FROM stn.aporte_recurso_rpps
        INNER JOIN stn.aporte_recurso_rpps_grupo
                ON stn.aporte_recurso_rpps_grupo.cod_grupo = stn.aporte_recurso_rpps.cod_grupo
               AND stn.aporte_recurso_rpps_grupo.exercicio = stn.aporte_recurso_rpps.exercicio
         LEFT JOIN stn.aporte_recurso_rpps_receita
                ON aporte_recurso_rpps_receita.cod_aporte = aporte_recurso_rpps.cod_aporte
               AND aporte_recurso_rpps_receita.exercicio = aporte_recurso_rpps.exercicio
               AND aporte_recurso_rpps_receita.timestamp = (SELECT MAX(timestamp)
                                                              FROM stn.aporte_recurso_rpps_receita t1
                                                             WHERE aporte_recurso_rpps_receita.exercicio = t1.exercicio)
         LEFT JOIN orcamento.receita
                ON aporte_recurso_rpps_receita.cod_receita = receita.cod_receita
               AND aporte_recurso_rpps_receita.exercicio = receita.exercicio
         LEFT JOIN orcamento.conta_receita
                ON conta_receita.cod_conta = receita.cod_conta
               AND conta_receita.exercicio = receita.exercicio
        
             WHERE aporte_recurso_rpps.exercicio = ''' || stExercicio || '''
        ) as tabela
  GROUP BY cod_aporte
         , nom_aporte
         , nom_grupo
         , cod_grupo
         , exercicio
  ORDER BY cod_grupo
         , cod_aporte
    ';

    FOR reRegistro IN EXECUTE stSql
        LOOP
    
            RETURN next reRegistro;
        END LOOP;
    
        --DROP TABLE tmp_valor;
    
    
        RETURN;
    END;
$$language 'plpgsql';
