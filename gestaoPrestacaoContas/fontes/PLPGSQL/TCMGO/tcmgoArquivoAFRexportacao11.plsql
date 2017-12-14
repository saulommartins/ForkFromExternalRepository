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
* $Id:$
*
* Casos de uso: uc-02.02.11
*/

CREATE OR REPLACE FUNCTION tcmgo.arquivo_afr_exportacao11 (varchar, varchar, varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stEntidades         ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;
    stSql               VARCHAR := '';
    stSqlComplemento    VARCHAR := '';
    inTipoLancamento    VARCHAR;
    arEntidades         VARCHAR[];
    i                   INTEGER :=1;
    reRegistro          RECORD;
    arRetorno           NUMERIC[];
    stEntidadeTCMGO     VARCHAR;
    inOrgao             INTEGER;
    inUnidade           INTEGER;

BEGIN
    
    CREATE TEMPORARY TABLE tmp_balanco_verificacao_afr_recurso( 
        tipo_lancamento        varchar,
        orgao                  varchar,
        unidade                varchar,
        cod_fonte              integer,
        cod_estrutural         varchar,
        nivel                  integer,
        nom_conta              varchar,
        cod_sistema            integer,
        indicador_superavit    char(12),
        vl_saldo_anterior      numeric,
        vl_saldo_debitos       numeric,
        vl_saldo_creditos      numeric,
        vl_saldo_atual         numeric
    );

    arEntidades := string_to_array(stEntidades,',');

    FOR i IN 1..array_length(arEntidades,1)
    LOOP
        SELECT sw_cgm.nom_cgm
        INTO stEntidadeTCMGO
        FROM orcamento.entidade                   
            , sw_cgm                               
        WHERE entidade.numcgm = sw_cgm.numcgm
        AND entidade.exercicio = stExercicio
        AND entidade.cod_entidade = arEntidades[i]::integer;

        --Quando forem lançamentos da entidade Prefeitura utilizar o código 04
        IF (length(regexp_matches(lower(stEntidadeTCMGO),'(prefeitura)')::Varchar)) IS NOT NULL THEN
            inTipoLancamento := '04';
        --Quando forem lançamentos da entidade Camara será o código 01
        ELSEIF (length(regexp_matches(lower(stEntidadeTCMGO),'(câmara)|(camara)')::Varchar)) IS NOT NULL THEN
            inTipoLancamento := '01';
        --Quando forem lançamentos da entidade Instituto utilizar o código 05        
        ELSEIF (length(regexp_matches(lower(stEntidadeTCMGO),'(instituto)')::Varchar)) IS NOT NULL THEN
            inTipoLancamento := '05';
        --Quando forem lançamentos da entidade Consórcio utilizar o código 06
        ELSEIF (length(regexp_matches(lower(stEntidadeTCMGO),'(consórcio)|(consorcio)')::Varchar)) IS NOT NULL THEN
            inTipoLancamento := '06';
        END IF;

        --Buscar da configuração o orgao unidade configuracado em Gestão Prestação de Contas :: TCM - GO :: Configuração :: Configurar Órgão/Unidade das Contas Contábeis
        --Campo orgao e unidade serão com o mesmo valor
        SELECT  num_orgao
                , num_unidade
        INTO inOrgao
            ,inUnidade
        FROM tcmgo.configuracao_orgao_unidade 
        WHERE exercicio = stExercicio
        AND cod_entidade = arEntidades[i]::integer;

        stSql := '  INSERT INTO tmp_balanco_verificacao_afr_recurso
                    SELECT 
                              '''||inTipoLancamento||''' as tipo_lancamento
                            , '||COALESCE(inOrgao, 0)||' as orgao
                            , '||COALESCE(inUnidade, 0)||' as unidade
                            , plano_recurso.cod_recurso as cod_fonte
                            , registro.* 
                    FROM contabilidade.fn_rl_balancete_verificacao( '''||stExercicio||'''
                                                                    , '' cod_entidade IN  ( '||arEntidades[i]||' ) ''
                                                                    , '''||stDtInicial||'''
                                                                    , '''||stDtFinal||'''
                                                                    , ''A''::CHAR
                                                                ) 
                    AS registro
                            (cod_estrutural         varchar,
                             nivel                  integer,
                             nom_conta              varchar,
                             cod_sistema            integer,
                             indicador_superavit    char(12),
                             vl_saldo_anterior      numeric,
                             vl_saldo_debitos       numeric,
                             vl_saldo_creditos      numeric,
                             vl_saldo_atual         numeric
                            )
                    INNER JOIN contabilidade.plano_conta
                        ON plano_conta.exercicio = '''||stExercicio||'''
                        AND plano_conta.cod_estrutural = registro.cod_estrutural

                    LEFT JOIN contabilidade.plano_analitica
                        ON plano_analitica.exercicio  = plano_conta.exercicio
                        AND plano_analitica.cod_conta = plano_conta.cod_conta

                    LEFT JOIN contabilidade.plano_recurso
                         ON plano_recurso.exercicio   = plano_analitica.exercicio
                        AND plano_recurso.cod_plano   = plano_analitica.cod_plano
            
                    WHERE registro.cod_estrutural SIMILAR TO ''1.1.2%|1.1.3%|1.1.4%''
                    AND registro.cod_sistema <> 4 ;
        ';
        EXECUTE stSql;

    END LOOP;

    stSql := ' SELECT * FROM tmp_balanco_verificacao_afr_recurso ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;
    
    DROP TABLE tmp_balanco_verificacao_afr_recurso;

    RETURN;
END;
$$ LANGUAGE 'plpgsql'