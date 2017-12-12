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
* $Revision: 13341 $
* $Name$
* $Author: bruce $
* $Date: 2006-07-31 09:41:30 -0300 (Seg, 31 Jul 2006) $
*
* Casos de uso: uc-02.01.11
*/

/*
$Log$
Revision 1.8  2006/07/31 12:41:30  bruce
foi trocado a forma de execução dos selects para evitar um erro do banco. Agora todos os selects são executados pelo comando EXECUTE

Revision 1.7  2006/07/05 20:38:05  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_somatorio_despesa(VARCHAR, VARCHAR) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stSql               VARCHAR   := '''';
    reRegistro          RECORD;

BEGIN
        CREATE TEMPORARY TABLE tmp_relatorio AS
            SELECT
                orcamento.fn_consulta_class_despesa(cod_conta, exercicio, (( SELECT administracao.configuracao.valor FROM administracao.configuracao WHERE administracao.configuracao.cod_modulo = 8 AND administracao.configuracao.parametro = ''masc_class_despesa'' AND administracao.configuracao.exercicio = stExercicio))) as classificacao
                ,publico.fn_mascarareduzida( orcamento.fn_consulta_class_despesa(cod_conta, exercicio, (( SELECT administracao.configuracao.valor FROM administracao.configuracao WHERE administracao.configuracao.cod_modulo = 8 AND administracao.configuracao.parametro = ''masc_class_despesa'' AND administracao.configuracao.exercicio = stExercicio))) ) as classificacao_reduzida
                ,publico.fn_nivel( orcamento.fn_consulta_class_despesa(cod_conta, exercicio, (( SELECT administracao.configuracao.valor FROM administracao.configuracao WHERE administracao.configuracao.cod_modulo = 8 AND administracao.configuracao.parametro = ''masc_class_despesa'' AND administracao.configuracao.exercicio = stExercicio))) ) as nivel
                ,cod_conta
                ,exercicio
                ,descricao
            FROM    orcamento.conta_despesa
            WHERE   exercicio = stExercicio
            ORDER BY classificacao;

       -- CREATE TEMPORARY TABLE tmp_despesa AS
       --     SELECT
       --          de.cod_conta
       --          ,de.vl_original
       --         ,orcamento.fn_consulta_class_despesa(cod_conta, exercicio::character varying, (( SELECT administracao.configuracao.valor FROM administracao.configuracao WHERE administracao.configuracao.cod_modulo = 8 AND administracao.configuracao.parametro::text = ''masc_class_despesa''::text AND administracao.configuracao.exercicio = exercicio))::character varying) as classificacao
       --     FROM    orcamento.despesa as de
       --     WHERE   exercicio = stExercicio;
        stSql := ''CREATE TEMPORARY TABLE tmp_despesa AS
                SELECT
                     cod_conta
                     ,vl_original
                    ,orcamento.fn_consulta_class_despesa(cod_conta, exercicio, (( SELECT administracao.configuracao.valor FROM administracao.configuracao WHERE administracao.configuracao.cod_modulo = 8 AND administracao.configuracao.parametro = ''''masc_class_despesa'''' AND administracao.configuracao.exercicio = '''''' || stExercicio || '''''' ))) as classificacao
                FROM    orcamento.despesa
                WHERE   exercicio = '''''' || stExercicio || ''''''
                '' || stFiltro ;

        EXECUTE stSql;

                --,coalesce( orcamento.fn_totaliza_despesa( classificacao_reduzida ) , 0) as soma
    stSql := ''SELECT cod_conta
                     ,nivel
                     ,descricao
                     ,classificacao
                     ,classificacao_reduzida
                     ,0.00 as valor
               FROM
                    tmp_relatorio '';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        reRegistro.valor := coalesce(orcamento.fn_totaliza_despesa(reRegistro.classificacao_reduzida),0);
        --SELECT INTO reRegistro.valor orcamento.fn_totaliza_despesa(reRegistro.classificacao_reduzida);
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_relatorio;
    DROP TABLE tmp_despesa;

    RETURN;
END;
' LANGUAGE 'plpgsql';
