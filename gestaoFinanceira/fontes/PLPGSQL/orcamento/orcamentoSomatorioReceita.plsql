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
* Casos de uso: uc-02.01.09
*/

/*
$Log$
Revision 1.6  2006/07/05 20:38:05  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_orcamento_somatorio_receita(varchar,varchar,varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stCodEntidade       ALIAS FOR $3;
    stSql               VARCHAR   := '''';
    stMascara           VARCHAR   := '''';
    reRegistro          RECORD;
    flValorDeducao      NUMERIC   := 0.00;

BEGIN
SELECT
    valor
INTO
    stMascara
FROM
    administracao.configuracao
WHERE
    cod_modulo = 8 AND
    parametro = ''masc_class_receita'' AND
    exercicio = stExercicio;

CREATE TEMPORARY TABLE tmp_relatorio AS
    SELECT
        orcamento.fn_consulta_class_receita(cod_conta, exercicio, stMascara) as classificacao,
        publico.fn_mascarareduzida(orcamento.fn_consulta_class_receita(cod_conta, exercicio, stMascara)) as classificacao_reduzida,
        publico.fn_nivel(orcamento.fn_consulta_class_receita(cod_conta, exercicio, stMascara)) as nivel,
        cod_conta,
        exercicio,
        descricao
    FROM
        orcamento.conta_receita
    WHERE
        exercicio = stExercicio
    ORDER BY
        classificacao;

stSql := ''
    CREATE TEMPORARY TABLE tmp_receita AS
        SELECT
            cod_conta,
            vl_original,
            orcamento.fn_consulta_class_receita(cod_conta, exercicio, '''''' || stMascara || '''''') as classificacao
        FROM
            orcamento.receita
        WHERE
            exercicio = '''''' || stExercicio || '''''' AND
            cod_entidade IN ('' || stCodEntidade || '')
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
    IF (reRegistro.classificacao = ''9.9.0.0.00.00.00.00.00'') THEN
        flValorDeducao := reRegistro.valor;
    END IF;
    
END LOOP;

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
    IF (reRegistro.classificacao = ''1.0.0.0.00.00.00.00.00'') THEN
        reRegistro.valor := reRegistro.valor + flValorDeducao;
    END IF;

    RETURN next reRegistro;
END LOOP;


    DROP TABLE tmp_relatorio;
    DROP TABLE tmp_receita;

    RETURN;
END;
'language 'plpgsql';
