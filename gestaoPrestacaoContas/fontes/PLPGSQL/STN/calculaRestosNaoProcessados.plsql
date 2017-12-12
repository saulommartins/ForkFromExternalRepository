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
* Titulo do arquivo Calcula Restos a Pagar Não Processados do Exercício
* Data de Criação : 13/06/2008 


* @author Analista Gelson
* @author Desenvolvedor eduardoschitz 

* @package URBEM
* @subpackage 

$Id:$

*/

CREATE OR REPLACE FUNCTION stn.calcula_restos_nao_processados (  varchar, varchar , varchar, varchar, varchar) RETURNS numeric AS '
DECLARE
    exercicio alias for $1;
    data_ini  alias for $2;
    data_fim  alias for $3;
    stEntidades alias for $4;
    stRPPS alias for $5;
    reRegistro RECORD;
    nuValor Numeric;
    stSql   varchar := '''';
    stSqlRPPS   varchar := '''';
    stCondEntidades varchar;
    inCodEntidadeRPPS integer;
    crCursor    REFCURSOR;    
BEGIN

stCondEntidades := '' '' ;

stSqlRPPS = '' SELECT valor FROM administracao.configuracao where parametro = ''''cod_entidade_rpps'''' AND cod_modulo = 8 AND exercicio = '''''' || exercicio || '''''' '';

OPEN crCursor FOR EXECUTE stSqlRPPS;
    FETCH crCursor INTO inCodEntidadeRPPS;
CLOSE crCursor;

if ( stRPPS = ''false'' ) then

    stCondEntidades := '' and e.cod_entidade in ( '' || stEntidades || '' )  and e.cod_entidade not in ( '' || inCodEntidadeRPPS || '' ) '';

else

    stCondEntidades := '' and e.cod_entidade in ( '' || stEntidades || '' )  and e.cod_entidade in ( '' || inCodEntidadeRPPS || '' ) '';

end if; 

stSql = ''
SELECT 
    (coalesce (
    (SELECT 
         sum(ipe.vl_total) as valor
    FROM
         empenho.empenho     as e 
       , empenho.historico   as h 
       , empenho.item_pre_empenho ipe                    
       , sw_cgm              as cgm
       , empenho.pre_empenho as pe
    LEFT OUTER JOIN (
            SELECT
                ped.exercicio, 
                ped.cod_pre_empenho, 
                d.num_pao, 
                d.num_orgao,
                d.num_unidade, 
                d.cod_recurso,
                rec.nom_recurso, 
                d.cod_conta,
                cd.cod_estrutural, 
                rec.masc_recurso_red,
                rec.cod_detalhamento 
            FROM
                empenho.pre_empenho_despesa as ped, 
                orcamento.despesa           as d
                JOIN orcamento.recurso( '''''' || exercicio || '''''' ) as rec
                ON ( rec.cod_recurso = d.cod_recurso
                    AND rec.exercicio = d.exercicio )
                ,orcamento.conta_despesa     as cd
            WHERE
                ped.exercicio      = '''''' || exercicio || ''''''  AND
                ped.cod_despesa    = d.cod_despesa and 
                ped.exercicio      = d.exercicio   and 
                ped.cod_conta      = cd.cod_conta  and 
                ped.exercicio      = cd.exercicio
    ) as ped_d_cd ON pe.exercicio = ped_d_cd.exercicio AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho 
    WHERE
            e.exercicio         = '''''' || exercicio || ''''''
        AND e.exercicio         = pe.exercicio
        AND e.cod_pre_empenho   = pe.cod_pre_empenho
        '' || stCondEntidades || ''
        AND pe.cgm_beneficiario = cgm.numcgm 
        AND h.cod_historico     = pe.cod_historico    
        AND h.exercicio         = pe.exercicio   
        AND e.dt_empenho between to_date( ''''''|| data_ini ||'''''' , ''''dd/mm/yyyy'''' )
                           and   to_date( ''''''|| data_fim ||'''''' , ''''dd/mm/yyyy'''' )
        AND e.exercicio = pe.exercicio
        AND e.cod_pre_empenho = pe.cod_pre_empenho
        AND pe.exercicio = ipe.exercicio
        AND pe.cod_pre_empenho = ipe.cod_pre_empenho ) , 0.00) 
    - 
    coalesce (
    (SELECT 
        sum(eai.vl_anulado) as valor
    FROM
        empenho.empenho     as e 
       ,empenho.historico   as h 
            , empenho.empenho_anulado ea
            , empenho.empenho_anulado_item eai
            
      , sw_cgm              as cgm
      , empenho.pre_empenho as pe
        LEFT OUTER JOIN (
            SELECT
                ped.exercicio, 
                ped.cod_pre_empenho, 
                d.num_pao, 
                d.num_orgao,
                d.num_unidade, 
                d.cod_recurso,
                rec.nom_recurso, 
                d.cod_conta,
                cd.cod_estrutural, 
                rec.masc_recurso_red,
                rec.cod_detalhamento 
            FROM
                empenho.pre_empenho_despesa as ped, 
                orcamento.despesa           as d
                JOIN orcamento.recurso( '''''' || exercicio || '''''' ) as rec
                ON ( rec.cod_recurso = d.cod_recurso
                    AND rec.exercicio = d.exercicio )
                ,orcamento.conta_despesa     as cd
            WHERE
                ped.exercicio      = '''''' || exercicio || ''''''   AND
                ped.cod_despesa    = d.cod_despesa and 
                ped.exercicio      = d.exercicio   and 
                ped.cod_conta      = cd.cod_conta  and 
                ped.exercicio      = cd.exercicio
        ) as ped_d_cd ON pe.exercicio = ped_d_cd.exercicio AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho 
    
    WHERE
            e.exercicio         = '''''' || exercicio || ''''''
        AND e.exercicio         = pe.exercicio
        AND e.cod_pre_empenho   = pe.cod_pre_empenho
        '' || stCondEntidades|| ''
        AND pe.cgm_beneficiario = cgm.numcgm 
        AND h.cod_historico     = pe.cod_historico    
        AND h.exercicio         = pe.exercicio   
        AND to_date( to_char( ea.timestamp, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' ) between to_date( ''''''|| data_ini ||'''''' , ''''dd/mm/yyyy'''' )
                                                                              and   to_date( ''''''|| data_fim ||'''''' , ''''dd/mm/yyyy'''' )
        AND ea.exercicio = e.exercicio AND ea.exercicio = eai.exercicio
        AND ea.timestamp = eai.timestamp
        AND ea.cod_entidade = e.cod_entidade AND ea.cod_entidade = eai.cod_entidade
        AND ea.cod_empenho = e.cod_empenho AND  ea.cod_empenho = eai.cod_empenho) , 0.00)
    -
    coalesce ( 
    (SELECT 
        sum(nli.vl_total) as valor
    FROM
        empenho.empenho     as e 
       ,empenho.historico   as h 
       , empenho.nota_liquidacao nl
       , empenho.nota_liquidacao_item nli
      , sw_cgm              as cgm
      , empenho.pre_empenho as pe
        LEFT OUTER JOIN (
            SELECT
                ped.exercicio, 
                ped.cod_pre_empenho, 
                d.num_pao, 
                d.num_orgao,
                d.num_unidade, 
                d.cod_recurso,
                rec.nom_recurso, 
                d.cod_conta,
                cd.cod_estrutural, 
                rec.masc_recurso_red,
                rec.cod_detalhamento 
            FROM
                empenho.pre_empenho_despesa as ped, 
                orcamento.despesa           as d
                JOIN orcamento.recurso( '''''' || exercicio || '''''' ) as rec
                ON ( rec.cod_recurso = d.cod_recurso
                    AND rec.exercicio = d.exercicio )
                ,orcamento.conta_despesa     as cd
            WHERE
                ped.exercicio      = '''''' || exercicio || ''''''   AND
                ped.cod_despesa    = d.cod_despesa and 
                ped.exercicio      = d.exercicio   and 
                ped.cod_conta      = cd.cod_conta  and 
                ped.exercicio      = cd.exercicio
        ) as ped_d_cd ON pe.exercicio = ped_d_cd.exercicio AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho 
    WHERE
            e.exercicio         = '''''' || exercicio || ''''''
        AND e.exercicio         = pe.exercicio
        AND e.cod_pre_empenho   = pe.cod_pre_empenho
        '' || stCondEntidades|| ''
        AND pe.cgm_beneficiario = cgm.numcgm 
        AND h.cod_historico     = pe.cod_historico    
        AND h.exercicio         = pe.exercicio   
        --Ligação EMPENHO : NOTA LIQUIDAÇÃO
        AND e.exercicio = nl.exercicio_empenho
        AND e.cod_entidade = nl.cod_entidade
        AND e.cod_empenho = nl.cod_empenho
        --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO ITEM
        AND nl.dt_liquidacao between to_date( ''''''|| data_ini ||'''''' , ''''dd/mm/yyyy'''' )
                               and   to_date( ''''''|| data_fim ||'''''' , ''''dd/mm/yyyy'''' )
        AND nl.exercicio = nli.exercicio
        AND nl.cod_nota = nli.cod_nota
        AND nl.cod_entidade = nli.cod_entidade)
    ,0.00)
    +
    coalesce ( 
    (SELECT 
        sum(nlia.vl_anulado) as valor
    FROM
         empenho.empenho     as e 
       , empenho.historico   as h 
       , empenho.nota_liquidacao nl
       , empenho.nota_liquidacao_item nli
       , empenho.nota_liquidacao_item_anulado nlia
       , sw_cgm              as cgm
       , empenho.pre_empenho as pe
        LEFT OUTER JOIN (
            SELECT
                ped.exercicio, 
                ped.cod_pre_empenho, 
                d.num_pao, 
                d.num_orgao,
                d.num_unidade, 
                d.cod_recurso,
                rec.nom_recurso, 
                d.cod_conta,
                cd.cod_estrutural, 
                rec.masc_recurso_red,
                rec.cod_detalhamento 
            FROM
                empenho.pre_empenho_despesa as ped, 
                orcamento.despesa           as d
                JOIN orcamento.recurso( '''''' || exercicio || '''''' ) as rec
                ON ( rec.cod_recurso = d.cod_recurso
                    AND rec.exercicio = d.exercicio )
                ,orcamento.conta_despesa     as cd
            WHERE
                ped.exercicio      = '''''' || exercicio || ''''''   AND
                ped.cod_despesa    = d.cod_despesa and 
                ped.exercicio      = d.exercicio   and 
                ped.cod_conta      = cd.cod_conta  and 
                ped.exercicio      = cd.exercicio
        ) as ped_d_cd ON pe.exercicio = ped_d_cd.exercicio AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho 
    WHERE
            e.exercicio         = '''''' || exercicio || ''''''
        AND e.exercicio         = pe.exercicio
        AND e.cod_pre_empenho   = pe.cod_pre_empenho
        '' || stCondEntidades|| ''
        AND pe.cgm_beneficiario = cgm.numcgm 
        AND h.cod_historico     = pe.cod_historico    
        AND h.exercicio         = pe.exercicio   
        --Ligação EMPENHO : NOTA LIQUIDAÇÃO
        AND e.exercicio = nl.exercicio_empenho
        AND e.cod_entidade = nl.cod_entidade
        AND e.cod_empenho = nl.cod_empenho
        --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO ITEM
        AND to_date(to_char(nlia.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') between to_date( ''''''|| data_ini ||'''''' , ''''dd/mm/yyyy'''' )
                                                                         and   to_date( ''''''|| data_fim ||'''''' , ''''dd/mm/yyyy'''' )
        AND nl.exercicio = nli.exercicio
        AND nl.cod_nota = nli.cod_nota
        AND nl.cod_entidade = nli.cod_entidade
        --Ligação NOTA LIQUIDAÇÃO ITEM : NOTA LIQUIDAÇÃO ITEM ANULADO
        AND nli.exercicio = nlia.exercicio
        AND nli.cod_nota = nlia.cod_nota
        AND nli.cod_entidade = nlia.cod_entidade
        AND nli.num_item = nlia.num_item
        AND nli.cod_pre_empenho = nlia.cod_pre_empenho
        AND nli.exercicio_item = nlia.exercicio_item) , 0.00) ) as valor  ''; 

FOR reRegistro IN EXECUTE stSql
LOOP
    nuValor := reRegistro.valor;
END LOOP;

return nuValor;
end;
'LANGUAGE 'plpgsql';
