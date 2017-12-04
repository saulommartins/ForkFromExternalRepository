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
* Casos de uso: uc-02.01.11
*/

/*
$Log$
Revision 1.7  2006/07/05 20:38:05  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_somatorio_despesa_orgao(varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stSql               VARCHAR   := '';
    reRegistro          RECORD;
    reRegistro2         RECORD;
    nuSoma              NUMERIC(14,2);
    arMascDespesa       VARCHAR[];

BEGIN
        SELECT INTO
                   arMascDespesa
                   string_to_array(administracao.configuracao.valor,'.')
              FROM administracao.configuracao
             WHERE administracao.configuracao.cod_modulo = 8
               AND administracao.configuracao.parametro = 'masc_despesa'
               AND administracao.configuracao.exercicio = quote_literal(stExercicio);

    stSql := '
        CREATE TEMPORARY TABLE tmp_class_despesa as
            SELECT
                orcamento.fn_consulta_class_despesa(cod_conta, exercicio, (( SELECT administracao.configuracao.valor
                                                                             FROM administracao.configuracao
                                                                             WHERE administracao.configuracao.cod_modulo = 8
                                                                             AND administracao.configuracao.parametro = ''masc_class_despesa''
                                                                             AND administracao.configuracao.exercicio ='|| quote_literal(stExercicio) ||'))
                                                    ) as classificacao
                ,publico.fn_mascarareduzida( orcamento.fn_consulta_class_despesa(cod_conta, exercicio, (( SELECT administracao.configuracao.valor
                                                                                                          FROM administracao.configuracao
                                                                                                          WHERE administracao.configuracao.cod_modulo = 8
                                                                                                          AND administracao.configuracao.parametro = ''masc_class_despesa''
                                                                                                          AND administracao.configuracao.exercicio ='|| quote_literal(stExercicio) ||')))
                                            ) as classificacao_reduzida
                ,publico.fn_nivel( orcamento.fn_consulta_class_despesa(cod_conta, exercicio, (( SELECT administracao.configuracao.valor
                                                                                                FROM administracao.configuracao
                                                                                                WHERE administracao.configuracao.cod_modulo = 8
                                                                                                AND administracao.configuracao.parametro = ''masc_class_despesa''
                                                                                                AND administracao.configuracao.exercicio ='|| quote_literal(stExercicio) ||')))
                                ) as nivel
                ,cod_conta
                ,exercicio
                ,descricao
            FROM    orcamento.conta_despesa
            WHERE   exercicio ='|| quote_literal(stExercicio) ||'
            ORDER BY classificacao
        ';
    EXECUTE stSql;
    
        CREATE TEMPORARY TABLE tmp_relatorio(
             classificacao          VARCHAR(100)
            ,classificacao_reduzida VARCHAR(100)
            ,nivel                  INTEGER
            ,cod_conta              INTEGER
            ,num_orgao              INTEGER
            ,exercicio              VARCHAR(4)
            ,descricao              VARCHAR(100)
            ,vl_original            NUMERIC(14,2)
        );


        stSql := 'CREATE TEMPORARY TABLE tmp_despesa AS
                SELECT
                     cod_conta
                     ,num_orgao
                     ,vl_original
                    ,orcamento.fn_consulta_class_despesa(cod_conta, exercicio, (( SELECT administracao.configuracao.valor
                                                                                  FROM administracao.configuracao
                                                                                  WHERE administracao.configuracao.cod_modulo = 8
                                                                                  AND administracao.configuracao.parametro = ''masc_class_despesa''
                                                                                  AND administracao.configuracao.exercicio = ' || quote_literal(stExercicio) || '))
                                                        ) as classificacao
                FROM    orcamento.despesa
                WHERE   exercicio = ' || quote_literal(stExercicio) || '
                ' || stFiltro ;

        EXECUTE stSql;


        FOR reRegistro IN
            SELECT   DISTINCT on (num_orgao) *
            FROM     tmp_despesa
            ORDER BY num_orgao
        LOOP
            FOR reRegistro2 IN
                SELECT   *
                FROM     tmp_class_despesa
            LOOP
                nuSoma := orcamento.fn_totaliza_despesa_orgao(reRegistro2.classificacao_reduzida,reRegistro.num_orgao);
                IF nuSoma <> 0.00 THEN
                    nuSoma := coalesce(nuSoma,0);
                    INSERT INTO tmp_relatorio (num_orgao, cod_conta, classificacao, classificacao_reduzida, nivel, exercicio, descricao, vl_original) VALUES (reRegistro.num_orgao, reRegistro2.cod_conta, reRegistro2.classificacao, reRegistro2.classificacao_reduzida, reRegistro2.nivel, reRegistro2.exercicio, reRegistro2.descricao, nuSoma);
                END IF;
            END LOOP;
        END LOOP;


    FOR reRegistro IN
        SELECT   cod_conta
                ,num_orgao
                ,nivel
                ,descricao
                ,classificacao
                ,classificacao_reduzida
                ,vl_original as valor
        FROM
                 tmp_relatorio
        ORDER BY num_orgao, classificacao
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_relatorio;
    DROP TABLE tmp_despesa;
    DROP TABLE tmp_class_despesa;

    RETURN;
END;
$$ language 'plpgsql';
