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
* $Revision: 27052 $
* $Name$
* $Author: cako $
* $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $
*
* Casos de uso: uc-02.03.10
*/

/*
$Log$
Revision 1.7  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_empenho_restos_pagar_credor(varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar, varchar, varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stFiltro                ALIAS FOR $2;
    stDtInicial             ALIAS FOR $3;
    stDtFinal               ALIAS FOR $4;
    stCodEntidades          ALIAS FOR $5;
    stCodOrgao              ALIAS FOR $6;
    stCodUnidade            ALIAS FOR $7;
    stCodRecurso            ALIAS FOR $8;
    stDestinacaoRecurso     ALIAS FOR $9;
    inCodDetalhamento       ALIAS FOR $10;
    stCodElementoDispensa   ALIAS FOR $11;
    stSituacao              ALIAS FOR $12;
    stCodFuncao             ALIAS FOR $13;
    stCodSubFuncao          ALIAS FOR $14;
    stOrdenacao             ALIAS FOR $15;
    stMascara               ALIAS FOR $16;
    inCGM                   ALIAS FOR $17;
    inCodEmpenhoInicial     ALIAS FOR $18;
    inCodEmpenhoFinal       ALIAS FOR $19;
    stSql               VARCHAR   := '';
    ponto               VARCHAR   := '.';
    stDtInicialEmissao  VARCHAR   := '';
    stDtFinalEmissao    VARCHAR   := '';
    stDtInicialAux      VARCHAR   := '';
    reRegistro          RECORD;
    reReg               RECORD;

BEGIN

    IF (LENGTH(stDtInicial) <> 10) THEN
        stSql := 'SELECT DISTINCT exercicio FROM empenho.empenho WHERE empenho.exercicio < ' || quote_literal(stExercicio) || ' ';
    ELSE
        stSql := 'SELECT DISTINCT exercicio FROM empenho.empenho WHERE empenho.exercicio = ' || quote_literal(stExercicio) || ' ';
    END IF;

    CREATE TEMPORARY TABLE tmp_empenhos (
        cod_estrutural  VARCHAR,
        entidade        INTEGER,
        empenho         TEXT,
        exercicio       CHAR(4),
        cgm             INTEGER,
        razao_social    VARCHAR,
        valor_empenhado NUMERIC,
        valor_pago      NUMERIC,
        valor_liquidado NUMERIC,
        valor_anulado   NUMERIC,
        valor_apagar    NUMERIC,
        data            TEXT,
        data_vencimento TEXT        
    );

    FOR reReg IN EXECUTE stSql
    LOOP

        stDtInicialEmissao := '01/01/' || reReg.exercicio;
        stDtFinalEmissao := '31/12/' || reReg.exercicio;
        IF (LENGTH(stDtInicial) <> 10) THEN
            stDtInicialAux := stDtInicial || reReg.exercicio;
        ELSE
            stDtInicialAux := stDtInicial;
        END IF;

        stSql := ' INSERT INTO tmp_empenhos
            SELECT
                cod_estrutural,
                entidade,
                empenho,
                exercicio,
                cgm,
                razao_social,
                valor_empenhado,
                valor_pago,
                valor_liquidado,
                valor_anulado,
                (valor_empenhado - valor_anulado - valor_pago) as valor_apagar,
                data,
                data_vencimento
             FROM(
                SELECT
                    publico.fn_mascara_dinamica( ''' || stMascara  || ''', (ped_d_cd.num_orgao    ||'''|| ponto ||'''|| ped_d_cd.num_unidade  ||'''|| ponto ||'''|| ped_d_cd.cod_funcao   ||'''|| ponto ||'''|| ped_d_cd.cod_subfuncao||'''|| ponto ||'''|| ped_d_cd.num_programa ||'''|| ponto ||'''|| ped_d_cd.num_acao      ||'''|| ponto ||'''|| replace(ped_d_cd.cod_estrutural,''.'', '''') ) ) as cod_estrutural,
                    e.cod_entidade      as entidade,
                    (e.cod_entidade ||''-''||e.cod_empenho||''/''|| e.exercicio) as empenho,
                    e.exercicio         as exercicio,
                    pe.cgm_beneficiario as cgm,
                    cgm.nom_cgm         as razao_social,
        
                    -- Valor empenhado
                    empenho.fn_empenho_empenhado( e.exercicio ,e.cod_empenho, e.cod_entidade,''' || stDtInicialEmissao || ''' ,''' || stDtFinalEmissao || ''') as valor_empenhado,
        
                    -- Valor pago
                    (empenho.fn_empenho_pago( e.exercicio ,e.cod_empenho, e.cod_entidade,''' || stDtInicialAux    || ''',''' || stDtFinal      || ''') - empenho.fn_empenho_estornado( e.exercicio,e.cod_empenho , e.cod_entidade ,''' || stDtInicialAux || ''' ,''' || stDtFinal || '''  )) as valor_pago,
        
                    -- Valor Liquidado
                    (empenho.fn_empenho_liquidado( e.exercicio ,e.cod_empenho , e.cod_entidade ,''' || stDtInicialAux || ''' ,''' || stDtFinal || ''') - empenho.fn_empenho_estorno_liquidacao( e.exercicio ,e.cod_empenho ,e.cod_entidade ,'''  || stDtInicialAux || ''' ,'''  || stDtFinal || '''  )) as valor_liquidado,
        
                    -- Valor Empenhado
                    empenho.fn_empenho_anulado( e.exercicio ,e.cod_empenho , e.cod_entidade,''' || stDtInicialAux || ''',''' || stDtFinal || ''') as valor_anulado,
        
                    to_char(e.dt_empenho   ,''dd/mm/yyyy'') as data,
                    to_char(e.dt_vencimento,''dd/mm/yyyy'') as data_vencimento
                FROM
                    empenho.empenho     as e,
                    sw_cgm              as cgm,
                    empenho.pre_empenho as pe
                        LEFT JOIN empenho.restos_pre_empenho as rpe ON
                            pe.exercicio        = rpe.exercicio AND
                            pe.cod_pre_empenho  = rpe.cod_pre_empenho
                        LEFT JOIN (
                            SELECT
                                ped.exercicio,
                                ped.cod_pre_empenho,
                                d.num_orgao,
                                d.num_unidade,
                                d.cod_recurso,
                                d.cod_programa,
                                d.num_pao,
                                cd.cod_estrutural,
                                d.cod_funcao,
                                d.cod_subfuncao,
                                rec.masc_recurso_red,   
                                rec.cod_detalhamento,
                                ppa.programa.num_programa,
                                ppa.acao.num_acao
                            FROM
                                empenho.pre_empenho_despesa as ped,
                                orcamento.despesa           as d
                                JOIN orcamento.recurso(' || quote_literal(reReg.exercicio) ||') as rec
                                ON ( rec.exercicio = d.exercicio
                                    AND rec.cod_recurso = d.cod_recurso )
                                JOIN orcamento.programa_ppa_programa
                                  ON programa_ppa_programa.cod_programa = d.cod_programa
                                 AND programa_ppa_programa.exercicio   = d.exercicio
                                JOIN ppa.programa
                                  ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                                JOIN orcamento.pao_ppa_acao
                                  ON pao_ppa_acao.num_pao = d.num_pao
                                 AND pao_ppa_acao.exercicio = d.exercicio
                                JOIN ppa.acao 
                                  ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
                                ,orcamento.conta_despesa     as cd
                            WHERE
                                ped.cod_despesa = d.cod_despesa AND
                                ped.exercicio   = d.exercicio AND
                                ped.cod_conta     = cd.cod_conta AND
                                ped.exercicio     = cd.exercicio
                        ) as ped_d_cd ON
                            pe.exercicio       = ped_d_cd.exercicio AND
                            pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho
                WHERE
                    e.exercicio         = ' || quote_literal(reReg.exercicio) || ' AND
                    e.exercicio         = pe.exercicio AND
                    e.exercicio         = pe.exercicio AND
                    e.cod_pre_empenho   = pe.cod_pre_empenho AND
                    e.cod_entidade      IN (' || stCodEntidades || ') AND
                    pe.cgm_beneficiario = cgm.numcgm
            ';


        if (inCGM is not null and inCGM<>'') then
            stSql := stSql || '  AND pe.cgm_beneficiario     = ' || inCGM || ' ';
        end if;
    
        if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial<>'') then
            stSql := stSql || '  AND e.cod_empenho >= '|| inCodEmpenhoInicial ||'  ';
        end if;
    
        if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal<>'') then
            stSql := stSql || '  AND e.cod_empenho <= '|| inCodEmpenhoFinal ||'  ';
        end if;
    
        if (stCodOrgao is not null and stCodOrgao<>'') then
            stSql := stSql || '
                     AND CASE WHEN pe.implantado = true
                       THEN rpe.num_orgao      = '|| stCodOrgao ||'
                       ELSE ped_d_cd.num_orgao = '|| stCodOrgao ||'
                     END
            ';
        end if;
    
        if (stCodUnidade is not null and stCodUnidade<>'') then
            stSql := stSql || '
                     AND CASE WHEN pe.implantado = true
                       THEN rpe.num_unidade      = '|| stCodUnidade ||'
                       ELSE ped_d_cd.num_unidade = '|| stCodUnidade ||'
                     END
            ';
        end if;
    
        if (stCodRecurso is not null and stCodRecurso<>'') then
            stSql := stSql || '
                     AND CASE WHEN pe.implantado = true
                       THEN rpe.recurso          = '|| stCodRecurso ||'
                       ELSE ped_d_cd.cod_recurso = '|| stCodRecurso ||'
                     END
            ';
        end if;
    
        if (stDestinacaoRecurso is not null and stDestinacaoRecurso <> '') then
            stSql := stSql || ' AND CASE WHEN pe.implantado = false    
                                    THEN ped_d_cd.masc_recurso_red like '''|| stDestinacaoRecurso||'%'||'''  
                                 END
            ';
        end if;
    
        if (inCodDetalhamento is not null and inCodDetalhamento <> '') then
            stSql := stSql || ' AND CASE WHEN pe.implantado = false 
                                    THEN ped_d_cd.cod_detalhamento = '|| inCodDetalhamento ||' 
                                 END 
            ';
        end if;
    
        if (stCodElementoDispensa is not null and stCodElementoDispensa<>'') then
            stSql := stSql || '
                     AND CASE WHEN pe.implantado = true
                       THEN rpe.cod_estrutural      like rtrim(replace(substr(''' || stCodElementoDispensa || ''',0,length(''' || stCodElementoDispensa || ''')-2),''.'',''''),''0'') || ''%''
                       ELSE ped_d_cd.cod_estrutural like publico.fn_mascarareduzida (''' || stCodElementoDispensa || ''')|| ''%''
                     END
            ';
        end if;
    
        if (stCodFuncao is not null and stCodFuncao<>'') then
            stSql := stSql || '
                     AND CASE WHEN pe.implantado = true
                       THEN rpe.cod_funcao      = '|| stCodFuncao ||'
                       ELSE ped_d_cd.cod_funcao = '|| stCodFuncao ||'
                     END
            ';
        end if;
    
        if (stCodSubFuncao is not null and stCodSubFuncao<>'') then
            stSql := stSql || '
                     AND CASE WHEN pe.implantado = true
                       THEN rpe.cod_subfuncao      IN ('|| stCodSubFuncao ||')
                       ELSE ped_d_cd.cod_subfuncao IN ('|| stCodSubFuncao ||')
                     END
            ';
        end if;
    
        stSql := stSql || ' ORDER BY ';
    
        if (stOrdenacao is null and stOrdenacao = '') then
            stSql := stSql || ' e.dt_empenho,e.cod_empenho ';
        end if;
    
        if (stOrdenacao::INTEGER = 1) then
            stSql := stSql || ' e.cod_empenho, cgm.nom_cgm ';
        end if;
    
        if (stOrdenacao::INTEGER = 2) then
            stSql := stSql || ' e.dt_vencimento, cgm.nom_cgm ';
        end if;
    
        if (stOrdenacao::INTEGER = 3) then
            stSql := stSql || ' ped_d_cd.cod_recurso, cgm.nom_cgm ';
        end if;
    
        if (stOrdenacao::INTEGER = 4) then
            stSql := stSql || ' cgm.nom_cgm, e.cod_empenho ';
        end if;
    
        stSql := stSql || ' ) as tbl where valor_empenhado <> ''0.00'' AND (valor_empenhado - valor_anulado - valor_pago) <> ''0.00'' ';
        
        EXECUTE stSql;
    
    END LOOP;

    stSql := 'SELECT
                   cod_estrutural ,                                 
                   entidade, 
                   empenho ,
                   exercicio ,
                   cgm ,
                   razao_social, 
                   valor_empenhado ,
                   valor_pago ,
                   valor_liquidado,                            
                   valor_anulado ,                              
                   valor_apagar ,                               
                   data ,
                   data_vencimento
               FROM tmp_empenhos; ';

    FOR reRegistro IN EXECUTE stSql
    LOOP

        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_empenhos;

    RETURN;
END;
$$ language 'plpgsql';
