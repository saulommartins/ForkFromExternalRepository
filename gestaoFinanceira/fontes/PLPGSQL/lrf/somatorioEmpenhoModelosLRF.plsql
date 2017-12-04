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
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.05.03,uc-02.05.04,uc-02.05.05,uc-02.05.06,uc-02.05.07,uc-02.05.08,uc-02.05.10,uc-02.05.11
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:50  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcers.fn_somatorio_empenho_modelos_lrf(varchar,varchar,varchar,varchar,varchar,varchar,varchar) RETURNS numeric(14,2) AS '
DECLARE
    stTipo                  ALIAS FOR $1;
    stTipoRelatorio         ALIAS FOR $2;
    stCodEstrutural         ALIAS FOR $3;
    stCodEntidades          ALIAS FOR $4;
    stExercicio             ALIAS FOR $5;
    stDataInicial           ALIAS FOR $6;
    stDataFinal             ALIAS FOR $7;

    stSql                   VARCHAR   := '''';
    nuSoma                  NUMERIC   := 0;
    crCursor                REFCURSOR;

    vl_empenhado            NUMERIC   := 0;
    vl_anulado              NUMERIC   := 0;
    vl_liquidado            NUMERIC   := 0;
    vl_liquidado_estornado  NUMERIC   := 0;

BEGIN

    stSql := ''
        SELECT
            coalesce(sum(ipe.vl_total),0.00)
        FROM
            empenho.pre_empenho         as pe '';
            IF stTipo = ''2'' THEN
                stSql := stSql || ''
                LEFT OUTER JOIN empenho.restos_pre_empenho as rpe ON
                    pe.exercicio        = rpe.exercicio AND
                    pe.cod_pre_empenho  = rpe.cod_pre_empenho AND
                    rpe.cod_estrutural  like replace('''''' || stCodEstrutural || ''%'''',''''.'''','''''''') AND '';
                    IF stTipoRelatorio = ''1'' THEN
                        stSql := stSql || '' rpe.num_orgao   <> 1 '';
                    ELSE
                        stSql := stSql || '' rpe.num_orgao   = 1 '';
                    END IF;
            END IF;
            stSql := stSql || ''
                LEFT OUTER JOIN (
                    SELECT
                        ped.exercicio, ped.cod_pre_empenho, d.num_orgao, cd.cod_estrutural
                    FROM
                        empenho.pre_empenho_despesa as ped, orcamento.despesa as d, orcamento.conta_despesa as cd
                    WHERE
                        cd.cod_estrutural   like '''''' || stCodEstrutural || ''%''''  AND '';

                        IF stTipoRelatorio = ''1'' THEN
                            stSql := stSql || '' d.num_orgao   <> 1  AND '';
                        ELSE
                            stSql := stSql || '' d.num_orgao   = 1 AND '';
                        END IF;

                        stSql := stSql || ''
                        ped.cod_despesa     = d.cod_despesa     AND
                        ped.exercicio       = d.exercicio       AND
                        ped.cod_conta       = cd.cod_conta      AND
                        d.exercicio         = cd.exercicio
                    ) as ped_d_cd ON
                        pe.exercicio        = ped_d_cd.exercicio AND
                        pe.cod_pre_empenho  = ped_d_cd.cod_pre_empenho
            ,empenho.empenho             as e
            ,empenho.item_pre_empenho    as ipe
        WHERE
                e.cod_entidade             IN ('' || stCodEntidades || '')'';
        IF stTipo = ''1'' THEN
            stSql := stSql || '' AND e.exercicio                = '' || stExercicio;
        ELSE
            stSql := stSql || '' AND e.exercicio                < '' || stExercicio;
        END IF;

            stSql := stSql || ''
            AND e.dt_empenho BETWEEN to_date(''''''||stDataInicial||'''''',''''dd/mm/yyyy'''') AND to_date(''''''||stDataFinal||'''''',''''dd/mm/yyyy'''')

            AND e.exercicio                = pe.exercicio
            AND e.cod_pre_empenho          = pe.cod_pre_empenho

            AND pe.exercicio               = ipe.exercicio
            AND pe.cod_pre_empenho         = ipe.cod_pre_empenho '';

        IF stTipo = ''1'' THEN
            stSql := stSql || '' AND ped_d_cd.cod_estrutural is not null '';
        ELSE
            stSql := stSql || '' AND (rpe.cod_estrutural is not null OR  ped_d_cd.cod_estrutural is not null)'';
        END IF;


    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO vl_empenhado;
    CLOSE crCursor;

    stSql := ''
        SELECT
            coalesce(sum(EEAI.vl_anulado),0.00)
        FROM
            empenho.pre_empenho         as pe'';
            IF stTipo = ''2'' THEN
                stSql := stSql || ''
                LEFT OUTER JOIN empenho.restos_pre_empenho as rpe ON
                    pe.exercicio        = rpe.exercicio AND
                    pe.cod_pre_empenho  = rpe.cod_pre_empenho AND
                    rpe.cod_estrutural  like replace('''''' || stCodEstrutural || ''%'''',''''.'''','''''''') AND '';
                    IF stTipoRelatorio = ''1'' THEN
                        stSql := stSql || '' rpe.num_orgao   <> 1 '';
                    ELSE
                        stSql := stSql || '' rpe.num_orgao   = 1 '';
                    END IF;
            END IF;
            stSql := stSql || ''
                LEFT OUTER JOIN (
                    SELECT
                        ped.exercicio, ped.cod_pre_empenho, d.num_orgao, cd.cod_estrutural
                    FROM
                        empenho.pre_empenho_despesa as ped, orcamento.despesa as d, orcamento.conta_despesa as cd
                    WHERE
                        cd.cod_estrutural   like '''''' || stCodEstrutural || ''%''''  AND '';

                        IF stTipoRelatorio = ''1'' THEN
                            stSql := stSql || '' d.num_orgao   <> 1  AND '';
                        ELSE
                            stSql := stSql || '' d.num_orgao   = 1 AND '';
                        END IF;

                        stSql := stSql || ''
                        ped.cod_despesa     = d.cod_despesa     AND
                        ped.exercicio       = d.exercicio       AND
                        ped.cod_conta       = cd.cod_conta      AND
                        d.exercicio         = cd.exercicio
                    ) as ped_d_cd ON
                        pe.exercicio        = ped_d_cd.exercicio AND
                        pe.cod_pre_empenho  = ped_d_cd.cod_pre_empenho
            ,empenho.item_pre_empenho    as EIPE
            ,empenho.empenho_anulado_item as EEAI
        WHERE
                pe.exercicio            = EIPE.exercicio
            AND pe.cod_pre_empenho      = EIPE.cod_pre_empenho

            AND EIPE.exercicio           = EEAI.exercicio
            AND EIPE.cod_pre_empenho     = EEAI.cod_pre_empenho
            AND EIPE.num_item            = EEAI.num_item'';

        IF stTipo = ''1'' THEN
            stSql := stSql || '' AND EIPE.exercicio                = '' || stExercicio;
        ELSE
            stSql := stSql || '' AND EIPE.exercicio                < '' || stExercicio;
        END IF;

            stSql := stSql || ''
            AND EEAI.exercicio           =''|| stExercicio ||''
            AND EEAI.cod_entidade        IN (''||stCodEntidades||'')

            AND to_date(to_char(EEAI.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') BETWEEN to_date(''''''||stDataInicial||'''''',''''dd/mm/yyyy'''') AND to_date(''''''||stDataFinal||'''''',''''dd/mm/yyyy'''')'';

        IF stTipo = ''1'' THEN
            stSql := stSql || '' AND ped_d_cd.cod_estrutural is not null '';
        ELSE
            stSql := stSql || '' AND (rpe.cod_estrutural is not null OR  ped_d_cd.cod_estrutural is not null)'';
        END IF;



    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO vl_anulado;
    CLOSE crCursor;

    stSql := ''
        SELECT
            coalesce(sum(nli.vl_total),0.00)
        FROM
            empenho.pre_empenho         as pe'';
            IF stTipo = ''2'' THEN
                stSql := stSql || ''
                LEFT OUTER JOIN empenho.restos_pre_empenho as rpe ON
                    pe.exercicio        = rpe.exercicio AND
                    pe.cod_pre_empenho  = rpe.cod_pre_empenho AND
                    rpe.cod_estrutural  like replace('''''' || stCodEstrutural || ''%'''',''''.'''','''''''') AND '';
                    IF stTipoRelatorio = ''1'' THEN
                        stSql := stSql || '' rpe.num_orgao   <> 1 '';
                    ELSE
                        stSql := stSql || '' rpe.num_orgao   = 1 '';
                    END IF;
            END IF;
            stSql := stSql || ''
                LEFT OUTER JOIN (
                    SELECT
                        ped.exercicio, ped.cod_pre_empenho, d.num_orgao, cd.cod_estrutural
                    FROM
                        empenho.pre_empenho_despesa as ped, orcamento.despesa as d, orcamento.conta_despesa as cd
                    WHERE
                        cd.cod_estrutural   like '''''' || stCodEstrutural || ''%''''  AND '';

                        IF stTipoRelatorio = ''1'' THEN
                            stSql := stSql || '' d.num_orgao   <> 1  AND '';
                        ELSE
                            stSql := stSql || '' d.num_orgao   = 1 AND '';
                        END IF;

                        stSql := stSql || ''
                        ped.cod_despesa     = d.cod_despesa     AND
                        ped.exercicio       = d.exercicio       AND
                        ped.cod_conta       = cd.cod_conta      AND
                        d.exercicio         = cd.exercicio
                    ) as ped_d_cd ON
                        pe.exercicio        = ped_d_cd.exercicio AND
                        pe.cod_pre_empenho  = ped_d_cd.cod_pre_empenho
            ,empenho.empenho               as e
            ,empenho.nota_liquidacao_item  as nli
            ,empenho.nota_liquidacao       as nl
        WHERE
                e.cod_entidade             IN ('' || stCodEntidades || '')'';

        IF stTipo = ''1'' THEN
            stSql := stSql || '' AND e.exercicio                = '' || stExercicio;
        ELSE
            stSql := stSql || '' AND e.exercicio                < '' || stExercicio;
        END IF;

            stSql := stSql || ''
            AND e.exercicio                = pe.exercicio
            AND e.cod_pre_empenho          = pe.cod_pre_empenho

            AND e.exercicio = nl.exercicio_empenho
            AND e.cod_entidade = nl.cod_entidade
            AND e.cod_empenho = nl.cod_empenho

            AND nl.exercicio = nli.exercicio
            AND nl.cod_nota = nli.cod_nota
            AND nl.cod_entidade = nli.cod_entidade

            AND nl.dt_liquidacao BETWEEN to_date(''''''||stDataInicial||'''''',''''dd/mm/yyyy'''') AND to_date(''''''||stDataFinal||'''''',''''dd/mm/yyyy'''')'';

        IF stTipo = ''1'' THEN
            stSql := stSql || '' AND ped_d_cd.cod_estrutural is not null '';
        ELSE
            stSql := stSql || '' AND (rpe.cod_estrutural is not null OR  ped_d_cd.cod_estrutural is not null)'';
        END IF;



    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO vl_liquidado;
    CLOSE crCursor;

    stSql := ''
        SELECT
            coalesce(sum(ENLIA.vl_anulado),0.00)
        FROM
            empenho.pre_empenho         as pe'';
            IF stTipo = ''2'' THEN
                stSql := stSql || ''
                LEFT OUTER JOIN empenho.restos_pre_empenho as rpe ON
                    pe.exercicio        = rpe.exercicio AND
                    pe.cod_pre_empenho  = rpe.cod_pre_empenho AND
                    rpe.cod_estrutural  like replace('''''' || stCodEstrutural || ''%'''',''''.'''','''''''') AND '';
                    IF stTipoRelatorio = ''1'' THEN
                        stSql := stSql || '' rpe.num_orgao   <> 1 '';
                    ELSE
                        stSql := stSql || '' rpe.num_orgao   = 1 '';
                    END IF;
            END IF;
            stSql := stSql || ''
                LEFT OUTER JOIN (
                    SELECT
                        ped.exercicio, ped.cod_pre_empenho, d.num_orgao, cd.cod_estrutural
                    FROM
                        empenho.pre_empenho_despesa as ped, orcamento.despesa as d, orcamento.conta_despesa as cd
                    WHERE
                        d.cod_entidade          IN (''||stCodEntidades||'') AND
                        cd.cod_estrutural   like '''''' || stCodEstrutural || ''%''''  AND '';

                        IF stTipoRelatorio = ''1'' THEN
                            stSql := stSql || '' d.num_orgao   <> 1  AND '';
                        ELSE
                            stSql := stSql || '' d.num_orgao   = 1 AND '';
                        END IF;

                        stSql := stSql || ''
                        ped.cod_despesa     = d.cod_despesa     AND
                        ped.exercicio       = d.exercicio       AND
                        ped.cod_conta       = cd.cod_conta      AND
                        d.exercicio         = cd.exercicio
                    ) as ped_d_cd ON
                        pe.exercicio        = ped_d_cd.exercicio AND
                        pe.cod_pre_empenho  = ped_d_cd.cod_pre_empenho
             ,empenho.empenho                      as EE
             ,empenho.nota_liquidacao              as ENL
             ,empenho.nota_liquidacao_item         as ENLI
             ,empenho.nota_liquidacao_item_anulado as ENLIA

        WHERE
              pe.cod_pre_empenho         = EE.cod_pre_empenho
          And pe.exercicio               = EE.exercicio

          And EE.exercicio                = ENL.exercicio_empenho
          And EE.cod_entidade             = ENL.cod_entidade
          And EE.cod_empenho              = ENL.cod_empenho
          And EE.cod_entidade             IN (''||stCodEntidades||'')'';

        IF stTipo = ''1'' THEN
            stSql := stSql || '' AND EE.exercicio                = '' || stExercicio;
        ELSE
            stSql := stSql || '' AND EE.exercicio                < '' || stExercicio;
        END IF;

            stSql := stSql || ''
          And ENL.exercicio               = ENLI.exercicio
          And ENL.cod_nota                = ENLI.cod_nota
          And ENL.cod_entidade            = ENLI.cod_entidade

          AND to_date(to_char(ENLIA.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') BETWEEN to_date(''''''||stDataInicial||'''''',''''dd/mm/yyyy'''') AND to_date (''''''||stDataFinal||'''''',''''dd/mm/yyyy'''')

          And ENLI.exercicio           = ENLIA.exercicio
          And ENLI.cod_pre_empenho     = ENLIA.cod_pre_empenho
          And ENLI.num_item            = ENLIA.num_item
          And ENLI.cod_entidade        = ENLIA.cod_entidade
          And ENLI.exercicio_item      = ENLIA.exercicio_item
          And ENLI.cod_nota            = ENLIA.cod_nota'';

        IF stTipo = ''1'' THEN
            stSql := stSql || '' AND ped_d_cd.cod_estrutural is not null '';
        ELSE
            stSql := stSql || '' AND (rpe.cod_estrutural is not null OR  ped_d_cd.cod_estrutural is not null)'';
        END IF;

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO vl_liquidado_estornado;
    CLOSE crCursor;

    nuSoma := (vl_empenhado - vl_anulado) - (vl_liquidado - vl_liquidado_estornado);

    RETURN nuSoma;
END;
' LANGUAGE 'plpgsql';
