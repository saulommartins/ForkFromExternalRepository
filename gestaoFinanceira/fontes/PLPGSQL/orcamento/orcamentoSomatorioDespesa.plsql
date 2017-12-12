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

CREATE OR REPLACE FUNCTION orcamento.fn_orcamento_somatorio_despesa(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stCodEntidade       ALIAS FOR $3;
    stNumOrgao          ALIAS FOR $4;
    stNumUnidade        ALIAS FOR $5;
    stSql               VARCHAR   := '''';
    stMascara           VARCHAR   := '''';
    reRegistro          RECORD;

BEGIN

--SELECT QUE BUSCA A MASCARA DE DESPESA DO EXERCICIO ATUAL
    stMascara = selectIntoVarchar('SELECT valor 
                                     FROM administracao.configuracao
                                    WHERE cod_modulo = 8 
                                      AND parametro = ''masc_class_despesa'' 
                                      AND exercicio = '||quote_literal(stExercicio)||' ');

--TABELA COM OS REGISTOS A SEREM LISTADOS
stSql := '
CREATE TEMPORARY TABLE tmp_relatorio AS
    SELECT
        orcamento.fn_consulta_class_despesa(cd.cod_conta, cd.exercicio, '||quote_literal(stMascara)||') as classificacao,
        publico.fn_mascarareduzida(orcamento.fn_consulta_class_despesa(cd.cod_conta, cd.exercicio, '||quote_literal(stMascara)||')) as classificacao_reduzida,
        publico.fn_nivel(orcamento.fn_consulta_class_despesa(cd.cod_conta, cd.exercicio, '||quote_literal(stMascara)||')) as nivel,
        cd.cod_conta,
        cd.exercicio,
        cd.descricao,
        d.num_orgao,
        d.num_unidade
    FROM
        orcamento.conta_despesa as cd
            LEFT JOIN orcamento.despesa as d ON
                cd.exercicio = d.exercicio AND
                cd.cod_conta = d.cod_conta
    WHERE
        cd.exercicio = '||quote_literal(stExercicio)||'
    ORDER BY
        classificacao ';

EXECUTE stSql;

--TABELA POR ONDE SÃO GERADO OS VALORES
stSql := '
    CREATE TEMPORARY TABLE tmp_despesa AS
        SELECT
            cod_conta,
            vl_original,
            orcamento.fn_consulta_class_despesa(cod_conta, exercicio, ''' || stMascara || ''') as classificacao
        FROM
            orcamento.despesa
        WHERE
                exercicio       = ''' || stExercicio     || '''
            AND cod_entidade    IN (' || stCodEntidade || ')';

            if (stNumOrgao is not null and stNumOrgao <> '') then
                stSql:= stSql || ' AND num_orgao       = ' || stNumOrgao;
            end if;

            if (stNumUnidade is not null and stNumUnidade <> '') then
                stSql:= stSql || ' AND num_unidade     = ' || stNumUnidade;
            end if;

            stSql:= stSql || stFiltro ;


EXECUTE stSql;

--RETORNO DA PL
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
        reRegistro.valor := coalesce(orcamento.fn_totaliza_despesa(reRegistro.classificacao_reduzida),0);
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_relatorio;
    DROP TABLE tmp_despesa;

    RETURN;
--    RETURN reRegistro;
END;
$$ LANGUAGE 'plpgsql';
