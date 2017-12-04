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
*

	$Id: somatorioReceita.plsql 59612 2014-09-02 12:00:51Z gelson $

*
* Casos de uso: uc-02.01.10
*/

/*
$Log$
Revision 1.7  2006/07/05 20:38:05  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_somatorio_receita(varchar,varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stSql               VARCHAR   := '''';
    reRegistro          RECORD;

BEGIN
        CREATE TEMPORARY TABLE tmp_relatorio AS
            SELECT
                CASE WHEN SUBSTR(cod_estrutural,1,1)::integer = 9
                    THEN orcamento.fn_consulta_class_receita(cod_conta, exercicio::character varying, (( SELECT administracao.configuracao.valor FROM administracao.configuracao WHERE administracao.configuracao.cod_modulo = 8 AND administracao.configuracao.parametro::text = ''masc_class_receita_dedutora''::text AND administracao.configuracao.exercicio = stExercicio))::character varying) 
                    ELSE orcamento.fn_consulta_class_receita(cod_conta, exercicio::character varying, (( SELECT administracao.configuracao.valor FROM administracao.configuracao WHERE administracao.configuracao.cod_modulo = 8 AND administracao.configuracao.parametro::text = ''masc_class_receita''::text AND administracao.configuracao.exercicio = stExercicio))::character varying) 
                END as classificacao
                ,CASE WHEN SUBSTR(cod_estrutural,1,1)::integer = 9
                    THEN publico.fn_mascarareduzida( orcamento.fn_consulta_class_receita(cod_conta, exercicio::character varying, (( SELECT administracao.configuracao.valor FROM administracao.configuracao WHERE administracao.configuracao.cod_modulo = 8 AND administracao.configuracao.parametro::text = ''masc_class_receita_dedutora''::text AND administracao.configuracao.exercicio = stExercicio))::character varying) )
                    ELSE publico.fn_mascarareduzida( orcamento.fn_consulta_class_receita(cod_conta, exercicio::character varying, (( SELECT administracao.configuracao.valor FROM administracao.configuracao WHERE administracao.configuracao.cod_modulo = 8 AND administracao.configuracao.parametro::text = ''masc_class_receita''::text AND administracao.configuracao.exercicio = stExercicio))::character varying) )
                 END as classificacao_reduzida
                ,CASE WHEN SUBSTR(cod_estrutural,1,1)::integer = 9
                    THEN publico.fn_nivel( orcamento.fn_consulta_class_receita(cod_conta, exercicio::character varying, (( SELECT administracao.configuracao.valor FROM administracao.configuracao WHERE administracao.configuracao.cod_modulo = 8 AND administracao.configuracao.parametro::text = ''masc_class_receita_dedutora''::text AND administracao.configuracao.exercicio = stExercicio))::character varying) ) 
                    ELSE publico.fn_nivel( orcamento.fn_consulta_class_receita(cod_conta, exercicio::character varying, (( SELECT administracao.configuracao.valor FROM administracao.configuracao WHERE administracao.configuracao.cod_modulo = 8 AND administracao.configuracao.parametro::text = ''masc_class_receita''::text AND administracao.configuracao.exercicio = stExercicio))::character varying) )
                 END as nivel
                ,cod_conta
                ,exercicio
                ,descricao
            FROM    orcamento.conta_receita
            WHERE   exercicio = stExercicio
            ORDER BY classificacao;

    stSql := ''CREATE TEMPORARY TABLE tmp_receita AS
                SELECT
                     rec.cod_conta
                     ,rec.vl_original
                    ,CASE WHEN substr(crec.cod_estrutural,1,1)::integer = 9
                        THEN orcamento.fn_consulta_class_receita(rec.cod_conta, rec.exercicio::character varying, (( SELECT administracao.configuracao.valor FROM administracao.configuracao WHERE administracao.configuracao.cod_modulo = 8 AND administracao.configuracao.parametro::text = ''''masc_class_receita_dedutora''''::text AND administracao.configuracao.exercicio = '''''' || stExercicio || ''''''))::character varying) 
                        ELSE orcamento.fn_consulta_class_receita(rec.cod_conta, rec.exercicio::character varying, (( SELECT administracao.configuracao.valor FROM administracao.configuracao WHERE administracao.configuracao.cod_modulo = 8 AND administracao.configuracao.parametro::text = ''''masc_class_receita''''::text AND administracao.configuracao.exercicio = '''''' || stExercicio || ''''''))::character varying)
                     END as classificacao
                FROM    orcamento.receita as rec
                        JOIN orcamento.conta_receita as crec
                            USING (cod_conta, exercicio)
                WHERE   rec.exercicio = '''''' || stExercicio || ''''''
                '' || stFiltro ;
    EXECUTE stSql;

    FOR reRegistro IN
        SELECT   cod_conta
                ,nivel
                ,descricao
                ,classificacao
                ,classificacao_reduzida
                ,0.00 as valor
        FROM
                 tmp_relatorio
    LOOP
        reRegistro.valor := coalesce(orcamento.fn_totaliza_receita(reRegistro.classificacao_reduzida),0);
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_relatorio;
    DROP TABLE tmp_receita;

    --RETURN reRegistro;
    RETURN;
END;
'language 'plpgsql';
