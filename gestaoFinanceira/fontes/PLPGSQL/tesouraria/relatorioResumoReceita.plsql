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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 28985 $
* $Name$
* $Author: tonismar $
* $Date: 2008-04-03 16:15:10 -0300 (Qui, 03 Abr 2008) $
*
* Casos de uso: uc-02.04.13
*
*/


CREATE OR REPLACE FUNCTION tesouraria.fn_relatorio_resumo_receita(VARCHAR,VARCHAR,VARCHAR,VARCHAR,VARCHAR,BIGINT,BIGINT,BIGINT,BIGINT,BIGINT,VARCHAR,VARCHAR,VARCHAR,VARCHAR) 
RETURNS SETOF RECORD AS '

DECLARE
    stEntidade              ALIAS FOR $1;
    stExercicio             ALIAS FOR $2;
    stDtInicial             ALIAS FOR $3;
    stDtFinal               ALIAS FOR $4;
    stTipoRelatorio         ALIAS FOR $5;
    inReceitaInicial        ALIAS FOR $6;
    inReceitaFinal          ALIAS FOR $7;
    inContaBancoInicial     ALIAS FOR $8;
    inContaBancoFinal       ALIAS FOR $9;
    inRecurso               ALIAS FOR $10;
    stTipoReceita           ALIAS FOR $11;
    stDestinacaoRecurso     ALIAS FOR $12;
    inCodDetalhamento       ALIAS FOR $13;
    boUtilizaEstruturalTCE  ALIAS FOR $14;

    reRegistro          RECORD;
    stDataAno           VARCHAR := '''';
    stSql               VARCHAR := '''';
    stCampos            VARCHAR := '''';
    stCampos2           VARCHAR := '''';
    stFiltroArrec       VARCHAR := '''';
    stFiltroArrecR      VARCHAR := '''';
    stFiltroContas      VARCHAR := '''';
    stFiltroArrecEst    VARCHAR := '''';
    stFiltroArrecDed    VARCHAR := '''';
    stFiltroArrecDedEst VARCHAR := '''';
    stFiltroTransf      VARCHAR := '''';
    stFiltroTransfEst   VARCHAR := '''';
BEGIN

IF (stTipoRelatorio = ''B'') THEN
    stCampos  := '' ,CAST(conta_banco as varchar) as conta_banco'';
    stCampos2 := '',conta_banco'';
ELSIF (stTipoRelatorio = ''R'') THEN
    stCampos  := '' ,CAST(recurso as varchar) as recurso'';
    stCampos2 := '' ,recurso'';
ELSIF (stTipoRelatorio = ''E'') THEN
    stCampos  := '' ,CAST(entidade as varchar) as entidade'';
    stCampos2 := '' ,entidade'';
ELSE
    stCampos  := '' ,CAST('''''''' as varchar) as complemento'';
    stCampos2 := '' ,complemento'';
END IF;


IF (stDtInicial = stDtFinal ) THEN
    stFiltroArrec       := '' where  TO_DATE(TO_CHAR(arrecadacao_receita.timestamp_arrecadacao,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') = to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''') '';
    stFiltroArrecR      := '' AND TO_DATE(TO_CHAR(tesouraria.arrecadacao_receita.timestamp_arrecadacao,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') = to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''')'';
    stFiltroArrecDed    := '' AND TO_DATE(TO_CHAR(ard.timestamp_arrecadacao,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') = to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''') '';
    stFiltroArrecDedEst := '' AND TO_DATE(TO_CHAR(ARDE.timestamp_dedutora_estornada,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') = to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''') '';
    stFiltroArrecEst    := '' where TO_DATE(TO_CHAR(arrecadacao_estornada_receita.timestamp_estornada,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') = to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''') '';
    stFiltroTransf      := '' AND TO_DATE(TO_CHAR(TT.timestamp_transferencia,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') = to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''') '';
    stFiltroTransfEst   := '' AND TO_DATE(TO_CHAR(TTE.timestamp_estornada,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') = to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''') '';
ELSE
    stFiltroArrec       := '' where TO_DATE(TO_CHAR(arrecadacao_receita.timestamp_arrecadacao,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') 
                BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''')  AND to_date('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''') '';
    
    stFiltroArrecR      := '' AND TO_DATE(TO_CHAR(tesouraria.arrecadacao_receita.timestamp_arrecadacao,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''')  
                BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''')  AND to_date('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''') '';
           
    stFiltroArrecDed    := '' AND TO_DATE(TO_CHAR(ard.timestamp_arrecadacao,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') 
                BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''')  AND to_date('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''') '';
    stFiltroArrecDedEst := '' AND TO_DATE(TO_CHAR(ARDE.timestamp_dedutora_estornada,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') 
                BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''')  AND to_date('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''') '';
    stFiltroArrecEst    := '' where TO_DATE(TO_CHAR(arrecadacao_estornada_receita.timestamp_estornada,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') 
                BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''')  AND to_date('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''') '';
    stFiltroTransf      := '' AND TO_DATE(TO_CHAR(TT.timestamp_transferencia,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') 
                BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''')  AND to_date('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''') '';
    stFiltroTransfEst   := '' AND TO_DATE(TO_CHAR(TTE.timestamp_estornada,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') 
                BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''')  AND to_date('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''') '';
END IF;

IF ((inContaBancoInicial <> 0) OR (inContaBancoFinal <> 0)) THEN
    IF (inContaBancoInicial = inContaBancoFinal) THEN
        stFiltroContas = '' AND consulta.cod_plano = '' || inContaBancoInicial || '' '';
        stFiltroTransf      := stFiltroTransf        || '' AND  CPC.cod_plano = '' || inContaBancoInicial || '' '';
        stFiltroTransfEst   := stFiltroTransfEst     || '' AND  CPC.cod_plano = '' || inContaBancoInicial || '' '';

    ELSE
        stFiltroContas      := stFiltroContas        || '' AND  consulta.cod_plano BETWEEN '' || inContaBancoInicial || '' AND '' || inContaBancoFinal || '' '';
        stFiltroTransf      := stFiltroTransf        || '' AND  CPC.cod_plano BETWEEN '' || inContaBancoInicial || '' AND '' || inContaBancoFinal || '' '';
        stFiltroTransfEst   := stFiltroTransfEst     || '' AND  CPC.cod_plano BETWEEN '' || inContaBancoInicial || '' AND '' || inContaBancoFinal || '' '';
    END IF;
END IF;

IF ((inReceitaInicial <> 0) OR (inReceitaFinal <> 0)) THEN
    IF (inReceitaInicial = inReceitaFinal) THEN
        stFiltroContas       := stFiltroContas         || '' AND consulta.cod_receita = '' || inReceitaInicial || '' '';
        stFiltroArrecDedEst  := stFiltroArrecDedEst  || '' AND arde.cod_receita_dedutora = '' || inReceitaInicial || '' ''; 
        stFiltroArrecDed     := stFiltroArrecDed || '' AND ard.cod_receita_dedutora = '' || inReceitaInicial || '' ''; 
        stFiltroTransf       := stFiltroTransf || '' AND cpcr.cod_plano = '' || inReceitaInicial || '' ''; 
        stFiltroTransfEst    := stFiltroTransfEst || '' AND cpcr.cod_plano = '' || inReceitaInicial || '' ''; 
    ELSE
        stFiltroContas       := stFiltroContas      || '' AND consulta.cod_receita BETWEEN '' || inReceitaInicial || '' AND '' || inReceitaFinal || '' '';
        stFiltroArrecDedEst   := stFiltroArrecDedEst  || '' AND  arde.cod_receita_dedutora BETWEEN '' || inReceitaInicial || '' AND '' || inReceitaFinal || '' '';
        stFiltroArrecDed      := stFiltroArrecDed  || '' AND  ard.cod_receita_dedutora BETWEEN '' || inReceitaInicial || '' AND '' || inReceitaFinal || '' '';
        stFiltroTransf        := stFiltroTransf || '' AND cpcr.cod_plano BETWEEN '' || inReceitaInicial || '' AND '' || inReceitaFinal || '' '';
        stFiltroTransfEst     := stFiltroTransfEst || '' AND cpcr.cod_plano BETWEEN '' || inReceitaInicial || '' AND '' || inReceitaFinal || '' '';
    END IF;
END IF;

IF (inRecurso != 999999) THEN
        stFiltroContas       := stFiltroContas            || '' AND  recurso.cod_recurso = '' || inRecurso || '' '';
END IF;
    stFiltroContas := stFiltroContas || '' AND consulta.cod_entidade in ( '' || stEntidade || '' ) '';  

stFiltroArrecDed    := stFiltroArrecDed || '' AND ard.exercicio  = '''''' || stExercicio || '''''' '';      
stFiltroArrecDedEst := stFiltroArrecDedEst  || '' AND arde.exercicio  = '''''' || stExercicio || '''''' '';      
stFiltroTransf      := stFiltroTransf       || '' AND tt.exercicio  = '''''' || stExercicio || '''''' AND tt.cod_entidade  in ( '' || stEntidade || '' ) '';      
stFiltroTransfEst   := stFiltroTransfEst    || '' AND tt.exercicio  = '''''' || stExercicio || '''''' AND tt.cod_entidade  in ( '' || stEntidade || '' ) '';      


stSql := ''
    SELECT 
        receita
        ,nom_conta
        ,tipo
        ,sum(arrecadacao) as arrecadacao
        ,sum(estorno) as estorno
        ''|| stCampos ||''
    FROM ( '';

--------------------------------------------------------
--                                          ARRECADACOES 
--------------------------------------------------------
IF stTipoReceita = ''orcamentaria'' OR stTipoReceita = ''geral'' THEN
    stSql := stSql ||''
        SELECT
             cast(arrecadacoes.cod_receita as numeric) as receita
            ,cast(arrecadacoes.nom_conta as varchar) as nom_conta
            ,cast(''''O'''' as varchar) as tipo
            ,cast(coalesce(arrecadacoes.vl_arrecadado,0.00) as numeric) as arrecadacao
            ,cast(coalesce(arrecadacoes.vl_estornado,0.00) as numeric) as estorno
            ,cast(CPA.cod_plano || '''' - '''' || CPC.nom_conta as varchar) as conta_banco
            ,cast(arrecadacoes.recurso as varchar) as recurso
            ,cast(arrecadacoes.entidade as varchar) as entidade
        FROM
             contabilidade.plano_conta     AS CPC
            ,contabilidade.plano_analitica AS CPA 

            LEFT JOIN(
                select consulta.exercicio
                     , consulta.cod_receita 
                     , conta_receita.descricao as nom_conta
                     , consulta.cod_plano
                     , sum ( coalesce( vl_arrecadado, 0 ) ) as vl_arrecadado 
                     , sum ( coalesce( vl_estornado , 0 ) ) as vl_estornado
                     , recurso.masc_recurso_red || '''' - '''' || recurso.nom_recurso as recurso
                     , OE.entidade
                    from (
                          select arrecadacao.cod_plano
                               , arrecadacao.exercicio
                               , arrecadacao.cod_entidade
                               , case when (not arrecadacoes.cod_receita is null ) 
                                        then arrecadacoes.cod_receita
                                      else  estornos.cod_receita
                                 end as cod_receita
                               , CASE WHEN arrecadacao.devolucao = false 
                                    THEN arrecadacoes.vl_arrecadado    
                                    ELSE 0.00
                                 END as vl_arrecadado
                               , CASE WHEN arrecadacao.devolucao = false
                                    THEN estornos.vl_estornado 
                                    ELSE arrecadacoes.vl_arrecadado -- Se for uma devolução, o valor arrecadado deve ser demonstrado como estorno
                                END as vl_estornado
                            from tesouraria.arrecadacao
                               ---- totalizando arrecadacoes
                          left  join ( select arrecadacao_receita.cod_receita
                                            , arrecadacao_receita.exercicio
                                            , arrecadacao_receita.cod_arrecadacao
                                            , arrecadacao_receita.timestamp_arrecadacao
                                            , sum ( arrecadacao_receita.vl_arrecadacao ) as vl_arrecadado
                                         from tesouraria.arrecadacao_receita
                                           '' || stFiltroArrec || ''
                
                                     group by  arrecadacao_receita.cod_receita
                                            , arrecadacao_receita.exercicio 
                                            , arrecadacao_receita.cod_arrecadacao
                                            , arrecadacao_receita.timestamp_arrecadacao
                              
                                  ) as arrecadacoes
                                  on ( arrecadacao.cod_arrecadacao        = arrecadacoes.cod_arrecadacao
                                 and   arrecadacao.exercicio              = arrecadacoes.exercicio 
                                 and   arrecadacao.timestamp_arrecadacao  = arrecadacoes.timestamp_arrecadacao
                                 --and   arrecadacao.devolucao = ''''f'''' ) --deixar comentado caso precise ser analizado
                                 )

                          left join ( select arrecadacao_estornada_receita.cod_receita
                                           , arrecadacao_estornada_receita.exercicio
                                           , arrecadacao_estornada_receita.cod_arrecadacao
                                           , arrecadacao_estornada_receita.timestamp_arrecadacao
                                           , sum ( arrecadacao_estornada_receita.vl_estornado ) as vl_estornado
                                        from tesouraria.arrecadacao_estornada_receita 
                                       '' || stFiltroArrecEst || '' 
                                     group by cod_receita
                                            , exercicio 
                                            , cod_arrecadacao
                                            , timestamp_arrecadacao
                                  ) as estornos
                                on ( arrecadacao.cod_arrecadacao        = estornos.cod_arrecadacao
                               and   arrecadacao.exercicio              = estornos.exercicio 
                               and   arrecadacao.timestamp_arrecadacao  = estornos.timestamp_arrecadacao )
                          where (   ( arrecadacoes.cod_receita = estornos.cod_receita and arrecadacoes.exercicio   = estornos.exercicio )
                                 or ( not arrecadacoes.cod_receita is null and estornos.cod_receita is null )
                                 or ( not estornos.cod_receita is null and arrecadacoes.cod_receita is null ) 
                                )               
                            ) as consulta
                   join orcamento.receita
                     on ( receita.cod_receita = consulta.cod_receita
                    and   receita.exercicio = consulta.exercicio )
                   join orcamento.conta_receita
                     on ( receita.cod_conta = conta_receita.cod_conta
                    and   receita.exercicio = conta_receita.exercicio )
                     ''; 
                IF boUtilizaEstruturalTCE = ''true'' THEN
                    stSql := stSql || '' 
                   join contabilidade.plano_analitica
                     on ( consulta.cod_plano = plano_analitica.cod_plano
                    and   consulta.exercicio = plano_analitica.exercicio )
                   join contabilidade.plano_conta
                     on ( plano_analitica.exercicio = plano_conta.exercicio 
                    and   plano_analitica.cod_conta = plano_conta.cod_conta ) '';
                END IF;
                stSql := stSql || ''
                   join orcamento.recurso(''''''|| stExercicio ||'''''') 
                     on ( recurso.cod_recurso = receita.cod_recurso
                    AND   recurso.exercicio   = receita.exercicio '';

if (stDestinacaoRecurso is not null and stDestinacaoRecurso <> '''') then
    stSql := stSql || '' AND recurso.masc_recurso_red like ''''''|| stDestinacaoRecurso||''%''||'''''' '';
end if;

if (inCodDetalhamento is not null and inCodDetalhamento <> '''') then
    stSql := stSql || '' AND recurso.cod_detalhamento = ''|| inCodDetalhamento ||'' '';
end if;

    stSql := stSql || ''  )
                 INNER JOIN( SELECT OE.cod_entidade || '''' - '''' || CGM.nom_cgm as entidade
                                  , OE.cod_entidade
                                  , OE.exercicio
                               FROM orcamento.entidade as OE
                                  , sw_cgm as CGM
                              WHERE OE.numcgm = CGM.numcgm
                             ) as OE on(
                                 OE.cod_entidade = consulta.cod_entidade   AND
                                 OE.exercicio    = consulta.exercicio
                             )
                where not consulta.exercicio is null '' || stFiltroContas || ''
                group by consulta.exercicio
                     , consulta.cod_receita 
                     , consulta.cod_plano
                     , conta_receita.descricao
                     , tipo
                     , recurso
                     , OE.entidade
                
            ) as arrecadacoes ON (    arrecadacoes.cod_plano = cpa.cod_plano
                                  AND arrecadacoes.exercicio = cpa.exercicio
                              )

            ,contabilidade.plano_banco     AS CPB
            ,contabilidade.plano_recurso   AS CPR
        WHERE 
                CPC.exercicio = '''''' || stExercicio || ''''''             
            AND CPC.exercicio    = CPA.exercicio                       
            AND CPC.cod_conta    = CPA.cod_conta                       
            AND CPA.exercicio    = CPB.exercicio                       
            AND CPA.cod_plano    = CPB.cod_plano                       
            AND CPA.exercicio    = CPR.exercicio                       
            AND CPA.cod_plano    = CPR.cod_plano  

        GROUP BY arrecadacoes.cod_receita
               , arrecadacoes.nom_conta
               , arrecadacoes.vl_arrecadado
               , arrecadacoes.vl_estornado
               , CPA.cod_plano
               , CPC.nom_conta
               , arrecadacoes.recurso
               , arrecadacoes.entidade
        '';

        stSql := stSql || ''
                                    
        UNION ALL                                       

--------------------------------------------------------
--                          DEDUÇÕES
--------------------------------------------------------
-- Devem ser demonstradas em separado às arrecadações em 
-- que elas foram utilizadas


        SELECT
             cast(rec.cod_receita as numeric) as receita
            ,cast(crec.descricao as varchar) as nom_conta '';

        stSql := stSql || ''
            ,cast(''''O'''' as varchar) as tipo
            -- INVERTE A DEMONSTRAÇÂO DOS VALORES 
            ,cast(coalesce(dedutoras.vl_estornado,0.00) as numeric) as arrecadacao
            ,cast(coalesce(dedutoras.vl_deducao,0.00) as numeric) as estorno
            --
            ,cast(pab.cod_plano||'''' - ''''||pcb.nom_conta as varchar) as conta_banco
            ,cast(orec.masc_recurso_red||'''' - '''' || orec.nom_recurso as varchar) as recurso
            ,cast(OE.entidade as varchar) as entidade

        FROM ( SELECT  ard.cod_receita_dedutora
                      ,sum(ard.vl_deducao) as vl_deducao
                      ,0.00 as vl_estornado
                      ,ard.cod_arrecadacao
                      ,ard.timestamp_arrecadacao
                      ,ard.exercicio
                      ,false as bo_devolucao
               FROM tesouraria.arrecadacao_receita_dedutora as ard
               WHERE ard.cod_receita is not null
                     '' || stFiltroArrecDed || ''
               GROUP BY ard.cod_receita_dedutora, ard.cod_arrecadacao, ard.timestamp_arrecadacao, ard.exercicio

               UNION ALL 

               SELECT   arrecadacao_receita.cod_receita as cod_receita_dedutora
                       ,sum(vl_arrecadacao) as vl_deducao
                       ,0.00 as vl_estornado
                       ,arrecadacao_receita.cod_arrecadacao
                       ,arrecadacao_receita.timestamp_arrecadacao
                       ,arrecadacao_receita.exercicio
                       ,true as bo_devolucao
               FROM tesouraria.arrecadacao_receita
                                    
                JOIN tesouraria.arrecadacao
                  ON arrecadacao.cod_arrecadacao = arrecadacao_receita.cod_arrecadacao
                 AND arrecadacao.exercicio = arrecadacao_receita.exercicio
                 AND arrecadacao.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao
                 
                JOIN tesouraria.arrecadacao_receita_dedutora as ard
                  ON ard.cod_arrecadacao = arrecadacao_receita.cod_arrecadacao
                 AND ard.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao
                 AND ard.cod_receita = arrecadacao_receita.cod_receita
                 AND ard.exercicio = arrecadacao_receita.exercicio
                 
                WHERE ard.cod_receita is not null
                  '' || stFiltroArrecR || ''
                  AND arrecadacao.devolucao = ''''t''''

                
               GROUP BY arrecadacao_receita.cod_receita, arrecadacao_receita.cod_arrecadacao, arrecadacao_receita.timestamp_arrecadacao, arrecadacao_receita.exercicio


               UNION ALL


               SELECT arde.cod_receita_dedutora
                      ,0.00 as vl_deducao
                      ,sum(arde.vl_estornado) as vl_estornado
                      ,arde.cod_arrecadacao
                      ,arde.timestamp_arrecadacao
                      ,arde.exercicio
                      ,false as bo_devolucao
               FROM tesouraria.arrecadacao_receita_dedutora_estornada arde
               JOIN tesouraria.arrecadacao_receita_dedutora as ard
               ON (    ard.cod_arrecadacao       = arde.cod_arrecadacao
                   AND ard.cod_receita           = arde.cod_receita
                   AND ard.exercicio             = arde.exercicio
                   AND ard.timestamp_arrecadacao = arde.timestamp_arrecadacao
                   AND ard.cod_receita_dedutora  = arde.cod_receita_dedutora
               )
               WHERE arde.cod_receita is not null
                     '' || stFiltroArrecDedEst || ''

               GROUP BY arde.cod_receita_dedutora, arde.cod_arrecadacao, arde.timestamp_arrecadacao, arde.exercicio

        ) as dedutoras
        JOIN tesouraria.arrecadacao_receita as AR
        ON (   ar.cod_arrecadacao       = dedutoras.cod_arrecadacao
           AND ar.timestamp_arrecadacao = dedutoras.timestamp_arrecadacao
        )
        JOIN tesouraria.arrecadacao as ta
        ON (   ta.cod_arrecadacao       = ar.cod_arrecadacao
           AND ta.timestamp_arrecadacao = ar.timestamp_arrecadacao
           AND ta.devolucao             = dedutoras.bo_devolucao
        )
        --- Conta de Caixa/Banco que arrecadou com dedução
        JOIN contabilidade.plano_analitica as pab
        ON (   ta.cod_plano = pab.cod_plano
           AND ta.exercicio = pab.exercicio 
        )
        JOIN contabilidade.plano_conta as pcb
        ON (   pcb.cod_conta = pab.cod_conta
           AND pcb.exercicio = pab.exercicio
        )
        JOIN orcamento.receita as rec
          ON ( rec.cod_receita = dedutoras.cod_receita_dedutora
         AND   rec.exercicio   = dedutoras.exercicio )
        JOIN orcamento.conta_receita crec
          ON ( rec.cod_conta = crec.cod_conta
         AND   rec.exercicio = crec.exercicio ) '';

    IF stExercicio::integer > 2012 THEN
        stSql := stSql || '' JOIN contabilidade.configuracao_lancamento_receita
                               ON configuracao_lancamento_receita.cod_conta_receita = crec.cod_conta
                              AND configuracao_lancamento_receita.exercicio = crec.exercicio '';
    END IF;
         
        stSql := stSql || '' JOIN orcamento.recurso(''''''|| stExercicio ||'''''') as orec
          ON ( orec.cod_recurso = rec.cod_recurso
         AND   orec.exercicio   = rec.exercicio '';

if (stDestinacaoRecurso is not null and stDestinacaoRecurso <> '''') then
    stSql := stSql || '' AND orec.masc_recurso_red like ''''''|| stDestinacaoRecurso||''%''||'''''' '';
end if;

if (inCodDetalhamento is not null and inCodDetalhamento <> '''') then
    stSql := stSql || '' AND orec.cod_detalhamento = ''|| inCodDetalhamento ||'' '';
end if;

    stSql := stSql || ''  )

            JOIN( SELECT OE.cod_entidade || '''' - '''' || CGM.nom_cgm as entidade
                       , OE.cod_entidade
                       , OE.exercicio
                    FROM orcamento.entidade as OE
                        , sw_cgm as CGM
                   WHERE OE.numcgm = CGM.numcgm
                ) as OE on(
                      OE.cod_entidade = ta.cod_entidade
                  AND OE.exercicio    = ta.exercicio
                )

          WHERE crec.exercicio = '''''' || stExercicio || ''''''             
            AND ta.cod_entidade in('' || stEntidade || '') '';


    IF ((inContaBancoInicial <> 0) OR (inContaBancoFinal <> 0)) THEN
        IF (inContaBancoInicial = inContaBancoFinal) THEN
            stSql := stSql || '' AND pab.cod_plano = '' || inContaBancoInicial || '' '';
        ELSE
            stSql := stSql || '' AND pab.cod_plano BETWEEN '' || inContaBancoInicial || '' AND '' || inContaBancoFinal || '' '';
        END IF;
    END IF;
    
    IF (inRecurso != 999999) THEN
        stSql := stSql || '' AND orec.cod_recurso = '' || inRecurso || '' '';
    END IF;

    stSql := stSql || ''
        GROUP BY rec.cod_receita
               , crec.descricao
               , dedutoras.vl_estornado
               , dedutoras.vl_deducao
               , dedutoras.cod_arrecadacao
               , pab.cod_plano
               , pcb.nom_conta
               , orec.masc_recurso_red
               , orec.nom_recurso
               , OE.entidade
    '';

END IF; -- IF ORCAMENTARIA OU GERAL

IF stTipoReceita = ''geral''
    THEN stSql := stSql || '' UNION ALL'';
END IF;

--------------------------------------------------------
--                                    ARRECADACOES EXTRA
--------------------------------------------------------
IF stTipoReceita = ''extra'' OR stTipoReceita = ''geral'' THEN
    stSql := stSql || ''
        SELECT
             cast(arrecadacoes_extra.cod_receita as numeric) as receita
            ,cast(arrecadacoes_extra.nom_conta as varchar) as nom_conta
          --  ,cast(arrecadacoes_extra.cod_estrutural as varchar) as estrutural
            ,cast(''''E'''' as varchar) as tipo
            ,cast(arrecadacoes_extra.vl_arrecadado as numeric) as arrecadacao
            ,cast(arrecadacoes_extra.vl_estornado as numeric) as estorno
            ,cast(arrecadacoes_extra.conta_banco as varchar) as conta_banco
            ,cast(arrecadacoes_extra.recurso as varchar) as recurso
            ,cast(arrecadacoes_extra.entidade as varchar) as entidade
        FROM (
            SELECT       
                 TT.exercicio
                ,CPCR.cod_plano as cod_receita
                ,CPCR.conta as nom_conta
                ,CPC.conta_banco
                ,SUM(coalesce(TT.valor,0.00))           as vl_arrecadado
                ,CAST(''''0.00'''' as NUMERIC(14,2))    as vl_estornado
                ,OE.entidade
                ,'''''''' as recurso
            FROM
                tesouraria.transferencia as TT
                -- BUSCA CONTA BANCO        
                INNER JOIN (
                    SELECT
                        CPA.cod_plano || '''' - '''' || CPC.nom_conta as conta_banco                
                        ,CPA.cod_plano
                        ,CPA.exercicio 
                    FROM
                        contabilidade.plano_conta as CPC,
                        contabilidade.plano_analitica as CPA
                    WHERE 
                        CPC.cod_conta = CPA.cod_conta AND
                        CPC.exercicio = CPA.exercicio 
                ) as CPC on(
                    TT.cod_plano_debito = CPC.cod_plano AND
                    TT.exercicio        = CPC.exercicio 
                )
                -- BUSCA CONTA RECEITA        
                INNER JOIN (
                    SELECT
                         CPC.nom_conta as conta
                        ,CPC.cod_estrutural
                        ,CPA.cod_plano
                        ,CPA.exercicio 
                    FROM
                        contabilidade.plano_conta as CPC,
                        contabilidade.plano_analitica as CPA
                    WHERE 
                        CPC.cod_conta = CPA.cod_conta AND
                        CPC.exercicio = CPA.exercicio 
                ) as CPCR on(
                    TT.cod_plano_credito = CPCR.cod_plano AND
                    TT.exercicio         = CPCR.exercicio 
                )
                --BUSCA ENTIDADE
                INNER JOIN(
                    SELECT 
                        OE.cod_entidade || '''' - '''' || CGM.nom_cgm as entidade
                        ,OE.cod_entidade
                        ,OE.exercicio     
                    FROM 
                        orcamento.entidade as OE
                        ,sw_cgm as CGM 
                    WHERE
                        OE.numcgm = CGM.numcgm
                ) as OE on(
                    OE.cod_entidade = TT.cod_entidade   AND
                    OE.exercicio    = TT.exercicio 
                )
               
            WHERE 
                TT.cod_tipo = 2
            ''|| stFiltroTransf; 

        IF boUtilizaEstruturalTCE = ''true'' THEN
            stSql := stSql || '' AND ( CPCR.cod_estrutural like ''''1.1.2.%'''' OR
                                       CPCR.cod_estrutural like ''''1.1.3.%'''' OR 
                                       CPCR.cod_estrutural like ''''1.2.1.%'''' OR 
                                       CPCR.cod_estrutural like ''''2.1.1.%'''' OR
                                       CPCR.cod_estrutural like ''''2.1.2.%'''' OR
                                       CPCR.cod_estrutural like ''''2.1.9.%'''' OR
                                       CPCR.cod_estrutural like ''''2.2.1.%'''' OR
                                       CPCR.cod_estrutural like ''''2.2.2.%'''' )
            '';
        END IF;

        stSql := stSql || ''
            GROUP BY        
                TT.exercicio
                ,CPCR.conta
                ,CPCR.cod_plano
                ,CPC.conta_banco   
                ,OE.entidade

        UNION ALL                                        
--------------------------------------------------------
--                         ESTORNO DE ARRECADACOES EXTRA
--------------------------------------------------------
            SELECT       
                 TT.exercicio
                ,CPCR.cod_plano as cod_receita
                ,CPCR.conta as nom_conta
                ,CPC.conta_banco
                ,CAST(''''0.00'''' as NUMERIC(14,2))    as vl_arrecadado
                ,SUM(coalesce(TTE.valor,0.00))           as vl_estornado
                ,OE.entidade
                ,'''''''' as recurso
            FROM
                tesouraria.transferencia as TT
                INNER JOIN tesouraria.transferencia_estornada as TTE on(
                    TTE.cod_entidade    = TT.cod_entidade   AND
                    TTE.tipo            = TT.tipo           AND
                    TTE.exercicio       = TT.exercicio      AND
                    TTE.cod_lote        = TT.cod_lote
                )               
                -- BUSCA CONTA BANCO        
                INNER JOIN (
                    SELECT
                        CPA.cod_plano || '''' - '''' || CPC.nom_conta as conta_banco                
                        ,CPA.cod_plano
                        ,CPA.exercicio 
                    FROM
                        contabilidade.plano_conta as CPC,
                        contabilidade.plano_analitica as CPA
                    WHERE 
                        CPC.cod_conta = CPA.cod_conta AND
                        CPC.exercicio = CPA.exercicio 
                ) as CPC on(
                    TT.cod_plano_debito = CPC.cod_plano AND
                    TT.exercicio        = CPC.exercicio 
                )
                -- BUSCA CONTA RECEITA        
                INNER JOIN (
                    SELECT
                         CPC.nom_conta as conta
                        ,CPC.cod_estrutural
                        ,CPA.cod_plano
                        ,CPA.exercicio 
                    FROM
                        contabilidade.plano_conta as CPC,
                        contabilidade.plano_analitica as CPA
                    WHERE 
                        CPC.cod_conta = CPA.cod_conta AND
                        CPC.exercicio = CPA.exercicio 
                ) as CPCR on(
                    TT.cod_plano_credito = CPCR.cod_plano AND
                    TT.exercicio         = CPCR.exercicio 
                )
                --BUSCA ENTIDADE
                INNER JOIN(
                    SELECT 
                        OE.cod_entidade || '''' - '''' || CGM.nom_cgm as entidade
                        ,OE.cod_entidade
                        ,OE.exercicio     
                    FROM 
                        orcamento.entidade as OE
                        ,sw_cgm as CGM 
                    WHERE
                        OE.numcgm = CGM.numcgm
                ) as OE on(
                    OE.cod_entidade = TT.cod_entidade   AND
                    OE.exercicio    = TT.exercicio 
                )
               
            WHERE 
                TT.cod_tipo = 2
            ''|| stFiltroTransfEst;

        IF boUtilizaEstruturalTCE = ''true'' THEN
            stSql := stSql || '' AND ( CPCR.cod_estrutural like ''''1.1.2.%'''' OR
                                       CPCR.cod_estrutural like ''''1.1.3.%'''' OR 
                                       CPCR.cod_estrutural like ''''1.2.1.%'''' OR 
                                       CPCR.cod_estrutural like ''''2.1.1.%'''' OR
                                       CPCR.cod_estrutural like ''''2.1.2.%'''' OR
                                       CPCR.cod_estrutural like ''''2.1.9.%'''' OR
                                       CPCR.cod_estrutural like ''''2.2.1.%'''' OR
                                       CPCR.cod_estrutural like ''''2.2.2.%'''' )
            '';
        END IF;

        stSql := stSql || ''
            GROUP BY        
                TT.exercicio
                ,CPCR.conta
                ,CPCR.cod_plano
                ,CPC.conta_banco   
                ,OE.entidade

        ) as arrecadacoes_extra    
    '';
END IF; -- END IF extra ou geral

    stSql := stSql ||''

) AS tbl 
WHERE (arrecadacao <> 0.00 OR estorno <> 0.00)
GROUP BY
    receita
    ,nom_conta
    ,tipo
    ''|| stCampos2 ||''

ORDER BY 
     receita ASC                                                                                        
'';

raise notice ''%'', stSql;

FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN next reRegistro;
END LOOP;

RETURN;

END;

'language 'plpgsql';
