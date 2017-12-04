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
* $Revision:$
* $Name:  $
* $Author: Lisiane Morais $
* $Date: $
*
* $Id:$
*/
CREATE OR REPLACE FUNCTION tcemg.empenho_empenhado_liquidado(varchar,varchar,varchar,varchar,varchar, varchar, varchar,varchar,varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio                    ALIAS FOR $1;
    stDtInicial                    ALIAS FOR $2;
    stDtFinal                      ALIAS FOR $3;
    stCodEntidades                 ALIAS FOR $4;
    stCodOrgao                     ALIAS FOR $5;
    stCodUnidade                   ALIAS FOR $6;
    stCodPao                       ALIAS FOR $7;
    stCodRecurso                   ALIAS FOR $8;
    stSituacao                     ALIAS FOR $9;
    stTipoRelatorio                ALIAS FOR $10; 
    
    stSql                          VARCHAR   := '';
    reRegistro                     RECORD;
BEGIN
    stSql := 'SELECT entidade
                   , descricao_categoria
                   , nom_tipo
                   , empenho
                   , exercicio
                   , cgm
                   , cgm||'' - ''||razao_social::varchar AS credor
                   , cod_nota
                   , stData 
                   , ordem
                   , conta
                   , coalesce(nome_conta,''NÃO INFORMADO'') AS nome_conta
                   , valor
                   , valor_anulado
                   , descricao
                   , recurso::varchar
                   , despesa || '' - '' || descricao_despesa::varchar AS despesa
                   , ''''::VARCHAR AS num_documento
                   , ''''::VARCHAR AS banco
                   , to_char(dt_empenho ,''dd/mm/yyyy'') as dt_empenho
                   , dotacao
                   , cod_recurso
                   , num_orgao
                   , num_unidade
              FROM ( SELECT e.cod_entidade as entidade
                          , categoria_empenho.descricao as descricao_categoria
                          , tipo_empenho.nom_tipo
                          , e.cod_empenho as empenho
                          , e.exercicio as exercicio
                          , pe.cgm_beneficiario as cgm
                          , cgm.nom_cgm as razao_social
                          , cast( pe.descricao as varchar ) as descricao
                          , e.dt_empenho
                          , ped_d_cd.dotacao
                          , ped_d_cd.cod_recurso
                          , ped_d_cd.num_unidade
                          , ped_d_cd.num_orgao';

            if (stSituacao = '1') then            
                   stSql := stSql ||
                         ', 0 as cod_nota
                          , 0 as ordem
                          , 0 as conta
                          , cgm.nom_cgm as nome_conta
                          , to_char(e.dt_empenho,''dd/mm/yyyy'') as stData
                          , coalesce(sum(e.vl_anulado), 0.00) as valor_anulado
                          , sum(e.vl_total) as valor ';
            end if;            

            if (stSituacao = '3') then
                    stSql := stSql || '
                           , to_char(nl.dt_liquidacao,''dd/mm/yyyy'') as stData
                           , nli.cod_nota as cod_nota
                           , sum(nli.vl_total) as valor
                           , coalesce(sum(nlia.vl_anulado), 0.00) as valor_anulado
                           , 0 as ordem
                           , 0 as conta
                           , cgm.nom_cgm  as nome_conta ';
            end if;

            stSql := stSql ||
                           ', ped_d_cd.nom_recurso as recurso
                            , ped_d_cd.cod_estrutural as despesa
                            , ped_d_cd.descricao AS descricao_despesa';
            
            IF (stSituacao = '1') THEN
            stSql := stSql || '
                  FROM 
                      (
                         SELECT    
                                e.cod_entidade
                            ,   e.cod_empenho
                            ,   e.exercicio
                            ,   e.dt_empenho
                            ,   e.cod_categoria
                            ,   ipe.vl_total
                            ,   ipe.cod_pre_empenho
                            ,   ipe.num_item
                            ,   sum(eai.vl_anulado) as vl_anulado
                        FROM    empenho.empenho as e
                  INNER JOIN    empenho.item_pre_empenho as ipe
                          ON    e.exercicio       = ipe.exercicio
                         AND    e.cod_pre_empenho = ipe.cod_pre_empenho
                   LEFT JOIN  empenho.empenho_anulado ea
                          ON    ea.exercicio = e.exercicio
                         AND    ea.cod_entidade = e.cod_entidade
                         AND    ea.cod_empenho = e.cod_empenho
                         AND to_date( to_char( ea."timestamp", ''dd/mm/yyyy''), ''dd/mm/yyyy'') BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'')

                   LEFT JOIN empenho.empenho_anulado_item AS eai
                        ON (    eai.exercicio = ea.exercicio
                            AND eai.cod_entidade = ea.cod_entidade
                            AND eai.cod_empenho = ea.cod_empenho
                            AND eai."timestamp" = ea."timestamp"
                            AND eai.exercicio = e.exercicio
                            AND eai.cod_pre_empenho = ipe.cod_pre_empenho
                            AND eai.num_item = ipe.num_item
                           )
                    GROUP BY    e.cod_entidade
                           ,    e.cod_empenho
                           ,    e.exercicio
                           ,    e.dt_empenho
                           ,    e.cod_categoria
                           ,    ipe.vl_total
                           ,    ipe.cod_pre_empenho
                           ,    ipe.num_item
                      ) as e
                    ';
            ELSE
                stSql := stSql || '
                    FROM
                        empenho.empenho     as e';
            END IF;
            
            stSql := stSql || '
                JOIN empenho.categoria_empenho 
                  ON categoria_empenho.cod_categoria = e.cod_categoria
                   , empenho.historico   as h ';

                if (stSituacao = '3') then
                    stSql := stSql || '
                    , empenho.nota_liquidacao nl
                    , empenho.nota_liquidacao_item nli
           
            LEFT JOIN empenho.nota_liquidacao_item_anulado AS nlia
                   ON nli.exercicio       = nlia.exercicio
                  AND nli.cod_nota        = nlia.cod_nota
                  AND nli.cod_entidade    = nlia.cod_entidade
                  AND nli.num_item        = nlia.num_item
                  AND nli.cod_pre_empenho = nlia.cod_pre_empenho
                  AND nli.exercicio_item  = nlia.exercicio_item
                  AND to_date(to_char(nlia.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'')';
                    
                end if;

             stSql := stSql || '
                   , sw_cgm              as cgm
                   , empenho.pre_empenho as pe
                   
                JOIN empenho.tipo_empenho 
                ON  tipo_empenho.cod_tipo = pe.cod_tipo
                
     LEFT OUTER JOIN (
                    SELECT
                        ped.exercicio, 
                        ped.cod_pre_empenho, 
                        d.num_pao, 
                        d.num_orgao,
                        d.num_unidade, 
                        d.cod_recurso,
                        d.cod_despesa,
                        rec.nom_recurso, 
                        d.cod_conta,
                        cd.cod_estrutural,
                        cd.descricao, 
                        rec.masc_recurso_red,
                        rec.cod_detalhamento,
                        ppa.acao.num_acao,
                        programa.num_programa,
                        d.cod_subfuncao,
                        LPAD(d.num_orgao::VARCHAR, 2, ''0'')||''.''||LPAD(d.num_unidade::VARCHAR, 2, ''0'')||''.''||d.cod_funcao||''.''||d.cod_subfuncao||''.''||ppa.programa.num_programa||''.''||LPAD(d.num_pao::VARCHAR, 4, ''0'')||''.''||REPLACE(cd.cod_estrutural, ''.'', '''') AS dotacao
                        
                    FROM
                        empenho.pre_empenho_despesa as ped, 
                        orcamento.despesa           as d
                        
                        JOIN orcamento.recurso(''' || stExercicio || ''') as rec
                          ON rec.cod_recurso = d.cod_recurso
                         AND rec.exercicio = d.exercicio
                        
                        JOIN orcamento.programa_ppa_programa
                          ON programa_ppa_programa.cod_programa = d.cod_programa
                         AND programa_ppa_programa.exercicio    = d.exercicio
                        
                        JOIN ppa.programa
                          ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                          
                        JOIN orcamento.pao_ppa_acao
                          ON pao_ppa_acao.num_pao   = d.num_pao
                         AND pao_ppa_acao.exercicio = d.exercicio
                         
                        JOIN ppa.acao 
                          ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
                           
                           , orcamento.conta_despesa as cd
                           
                    WHERE ped.exercicio      = ''' || stExercicio || '''
                      AND ped.cod_despesa    = d.cod_despesa
                      AND ped.exercicio      = d.exercicio
                      AND ped.cod_conta      = cd.cod_conta
                      AND ped.exercicio      = cd.exercicio
                ) AS ped_d_cd
               ON pe.exercicio       = ped_d_cd.exercicio
              AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho 

            WHERE e.exercicio         = ''' || stExercicio || '''
              AND e.exercicio         = pe.exercicio
              AND e.cod_pre_empenho   = pe.cod_pre_empenho
              AND e.cod_entidade      IN (' || stCodEntidades || ')
              AND pe.cgm_beneficiario = cgm.numcgm 
              AND h.cod_historico     = pe.cod_historico    
              AND h.exercicio         = pe.exercicio   ';

                if (stSituacao = '1') then
                    stSql := stSql || ' AND e.dt_empenho BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'') ';
                end if;

                if (stSituacao = '3') then
                    stSql := stSql || '
                    --Ligação EMPENHO : NOTA LIQUIDAÇÃO
                        AND e.exercicio    = nl.exercicio_empenho
                        AND e.cod_entidade = nl.cod_entidade
                        AND e.cod_empenho  = nl.cod_empenho

                    --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO ITEM
                        AND nl.exercicio    = nli.exercicio
                        AND nl.cod_nota     = nli.cod_nota
                        AND nl.cod_entidade = nli.cod_entidade
                        AND nl.dt_liquidacao BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'') ';
                end if;

                if (stCodOrgao is not null and stCodOrgao<>'') then
                    stSql := stSql || ' AND ped_d_cd.num_orgao = '|| stCodOrgao ||' ';
                end if;

                if (stCodUnidade is not null and stCodUnidade<>'') then
                    stSql := stSql || ' AND ped_d_cd.num_unidade = '|| stCodUnidade ||'  ';
                end if;

                if (stCodPao is not null and stCodPao<>'') then
                    stSql := stSql || ' AND ped_d_cd.num_pao = '|| stCodPao ||' ';
                --    stSql := stSql || ' AND ped_d_cd.num_acao ='|| stCodPao ||' ';
                end if;
                
                if (stCodRecurso is not null and stCodRecurso<>'' ) then
                    stSql := stSql || ' AND ped_d_cd.cod_recurso IN ('|| stCodRecurso ||') ';
                end if;
                
                if (stTipoRelatorio = 'ensino_fundamental') then
                    stSql := stSql || ' AND ped_d_cd.cod_subfuncao IN ( 361 ) ';
                end if;
                
                if (stTipoRelatorio = 'gasto_25') then
                    stSql := stSql || ' AND ped_d_cd.cod_subfuncao NOT IN ( 362,363,364 ) ';
                end if;
            stSql := stSql || '
            GROUP BY ';
            
            if (stSituacao = '1') then
                stSql := stSql || 'e.dt_empenho, e.cod_pre_empenho,';
            end if;

            if (stSituacao = '3') then
                    stSql := stSql || 'nl.dt_liquidacao, nli.cod_nota,';
            end if;

            stSql := stSql || ' e.cod_entidade, e.cod_empenho , e.exercicio , pe.cgm_beneficiario, cgm.nom_cgm, pe.descricao
            , ped_d_cd.cod_estrutural , ped_d_cd.nom_recurso, categoria_empenho.descricao, tipo_empenho.nom_tipo, ped_d_cd.descricao, e.dt_empenho
            , ped_d_cd.dotacao, ped_d_cd.cod_recurso,  ped_d_cd.num_orgao, ped_d_cd.num_unidade
            
              ORDER BY ';

            if (stSituacao = '1') then
                stSql := stSql || 'e.dt_empenho,';
            end if;

            if (stSituacao = '3') then
                    stSql := stSql || 'nl.dt_liquidacao,';
            end if;

            stSql := stSql || 'e.cod_entidade , e.cod_empenho , e.exercicio, ';

            if (stSituacao = '1') then
                    stSql := stSql || 'e.cod_pre_empenho,';
            end if;

            if (stSituacao = '3') then
                    stSql := stSql || ' nli.cod_nota,';
            end if;

            stSql := stSql || 'pe.cgm_beneficiario, cgm.nom_cgm
                            ) as tbl
            
                        WHERE valor <> ''0.00''
                     ORDER BY num_orgao
                            , num_unidade
                            , cod_recurso
                            , to_date(stData,''dd/mm/yyyy'')
                            , entidade
                            , empenho
                            , exercicio
                            , cgm
                            , razao_social
                            , cod_nota
                            , ordem
                            , conta
                            , nome_conta';
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;
          
    RETURN;
END;
$$ language 'plpgsql';