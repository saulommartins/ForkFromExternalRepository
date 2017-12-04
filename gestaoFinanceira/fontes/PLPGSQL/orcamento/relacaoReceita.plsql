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
	$Id: relacaoReceita.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-02.01.19
*/

/*
$Log$
Revision 1.6  2006/07/05 20:38:05  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_relacao_receita(varchar,varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stSql               VARCHAR   := '''';
    stMascClassReceita  VARCHAR   := '''';
    stMascClassReceitaDedutora VARCHAR   := '''';
    stMascRecurso       VARCHAR   := '''';
    reRegistro          RECORD;

BEGIN
        SELECT INTO
                   stMascClassReceita
                   administracao.configuracao.valor
         FROM   administracao.configuracao
        WHERE   administracao.configuracao.cod_modulo = 8
          AND   administracao.configuracao.parametro = ''masc_class_receita''
          AND   administracao.configuracao.exercicio = stExercicio;

        SELECT INTO
                   stMascClassReceitaDedutora
                   administracao.configuracao.valor
         FROM   administracao.configuracao
        WHERE   administracao.configuracao.cod_modulo = 8
          AND   administracao.configuracao.parametro = ''masc_class_receita_dedutora''
          AND   administracao.configuracao.exercicio = stExercicio;

        SELECT INTO
                   stMascRecurso
                   administracao.configuracao.valor
         FROM   administracao.configuracao
        WHERE   administracao.configuracao.cod_modulo = 8
          AND   administracao.configuracao.parametro = ''masc_recurso''
          AND   administracao.configuracao.exercicio = stExercicio;

        stMascClassReceita  := coalesce(stMascClassReceita,'''');
        stMascRecurso       := coalesce(stMascRecurso,'''');

        stSql := ''CREATE TEMPORARY TABLE tmp_relacao AS
                    SELECT
                        ocr.exercicio,
                        ocr.cod_conta,
                        CASE WHEN substr(ocr.cod_estrutural,1,1) = ''''9''''
                            THEN orcamento.fn_consulta_class_receita(ocr.cod_conta, ocr.exercicio, ''''''||stMascClassReceitaDedutora||'''''') 
                            ELSE orcamento.fn_consulta_class_receita(ocr.cod_conta, ocr.exercicio, ''''''||stMascClassReceita||'''''') 
                        END AS classificacao,
                        ocr.descricao AS descricao_receita,
                        --sw_fn_mascara_dinamica(''''''||stMascRecurso||'''''', ore.cod_recurso::character varying) AS cod_recurso,
                        oru.masc_recurso_red as cod_recurso,
                        oru.nom_recurso,
                        ore.cod_receita,
                        ore.vl_original AS valor_previsto,
                        ore.cod_entidade,
                        orcamento.fn_receita_realizada_periodo( ocr.exercicio
                                                               ,cast(ore.cod_entidade as varchar)
                                                               ,ore.cod_receita
                                                               ,''''01/01/''''||ocr.exercicio
                                                               ,TO_CHAR( now(), ''''dd/mm/'''' )||ocr.exercicio
                        ) as vl_arrecadado
                    FROM
                        orcamento.conta_receita ocr,
                        orcamento.receita ore,
                        orcamento.recurso('''''' || stExercicio || '''''') as oru
                    WHERE   ocr.cod_conta   = ore.cod_conta
                    AND     ocr.exercicio   = ore.exercicio
                    AND     ore.cod_recurso = oru.cod_recurso
                    AND     ore.exercicio   = oru.exercicio
                    AND     ore.exercicio    = '' || quote_literal(stExercicio) || '' '' || stFiltro ;

        EXECUTE stSql;

    FOR reRegistro IN
        SELECT  *
        FROM    tmp_relacao
        ORDER BY classificacao
    LOOP
--        IF substr(reRegistro.classificacao,1,1) != 9 THEN
            reRegistro.vl_arrecadado = reRegistro.vl_arrecadado * -1;
--        END IF;

        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_relacao;

    RETURN;
END;
'language 'plpgsql';
