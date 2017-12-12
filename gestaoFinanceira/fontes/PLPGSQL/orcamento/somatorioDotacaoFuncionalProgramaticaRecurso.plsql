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
* $Revision: 27374 $
* $Name$
* $Author: melo $
* $Date: 2008-01-04 14:54:52 -0200 (Sex, 04 Jan 2008) $
*
* Casos de uso: uc-02.01.15
*/

/*
$Log$
Revision 1.8  2006/10/24 17:33:09  domluc
Correção Bug #7110#

Revision 1.7  2006/10/04 17:07:01  cako
Bug #7110#

Revision 1.6  2006/07/05 20:38:05  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_somatorio_dotacao_funcional_programatica_recurso(varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stSql               VARCHAR   := '';
    reRegistro          RECORD;
    arDotacao           VARCHAR[];
    arMascDespesa       VARCHAR[];

    inOrgaoAnt          INTEGER := 0;
    inUnidadeAnt        INTEGER := 0;
    inFuncaoAnt         INTEGER := 0;
    inSubFuncaoAnt      INTEGER := 0;
    inProgramaAnt       INTEGER := 0;
    inPaoAnt            INTEGER := 0;
    inNivel             INTEGER := 0;
    inPosicao           INTEGER;
    stOrgao             VARCHAR;
    stUnidade           VARCHAR;
    stFuncao            VARCHAR;
    stSubFuncao         VARCHAR;
    stPrograma          VARCHAR;
    stPao               VARCHAR;
    stNumPao            VARCHAR;
    stClassDespesa      VARCHAR;
    stDigitoProjeto     VARCHAR;
    stDigitoAtividade   VARCHAR;
    stDigitoOperacao    VARCHAR;


BEGIN
        CREATE TEMPORARY TABLE tmp_relatorio(
             dotacao        VARCHAR(80)
            ,cod_despesa    INTEGER
            ,descricao      VARCHAR(200)
            ,vl_ordinario   NUMERIC(14,2)
            ,vl_vinculado   NUMERIC(14,2)
            ,vl_total       NUMERIC(14,2)
            ,nivel          INTEGER
        );

        stSql := 'CREATE TEMPORARY TABLE tmp_despesa AS
        
                SELECT
                      orcamento.despesa.*
                     ,orcamento.fn_consulta_funcional_programatica(orcamento.despesa.exercicio, orcamento.despesa.cod_despesa)  as dotacao
                     ,rec.tipo as tipo_recurso
                FROM    orcamento.despesa
                        join orcamento.recurso(' || quote_literal(stExercicio) || ') as REC
                        ON (    rec.exercicio   = orcamento.despesa.exercicio
                            AND rec.cod_recurso = orcamento.despesa.cod_recurso )
			left join orcamento.conta_despesa
			       ON conta_despesa.exercicio = despesa.exercicio
			      AND conta_despesa.cod_conta = despesa.cod_conta
                WHERE   orcamento.despesa.exercicio = ' || quote_literal(stExercicio) || ' ' || stFiltro ;

        EXECUTE stSql;
        
        FOR reRegistro IN
            SELECT   distinct on (dotacao) *
            FROM     tmp_despesa
            ORDER BY dotacao
        LOOP
            arDotacao := string_to_array(reRegistro.dotacao,'.');

            IF reRegistro.cod_funcao <> inFuncaoAnt THEN
                SELECT INTO
                    stFuncao
                    descricao
                FROM   orcamento.funcao
                WHERE  cod_funcao = reRegistro.cod_funcao
                  AND  exercicio  = reRegistro.exercicio;
                INSERT INTO tmp_relatorio ( dotacao, descricao, nivel ) values ( arDotacao[1], stFuncao, 1 );
                inSubFuncaoAnt := 0;
                inProgramaAnt  := 0;
            END IF;
            inFuncaoAnt := reRegistro.cod_funcao;

            IF reRegistro.cod_subfuncao <> inSubfuncaoAnt THEN
                SELECT INTO
                    stSubfuncao
                    descricao
                FROM   orcamento.subfuncao
                WHERE  cod_subfuncao = reRegistro.cod_subfuncao
                  AND  exercicio     = reRegistro.exercicio;
                INSERT INTO tmp_relatorio ( dotacao, descricao, nivel) values ( arDotacao[1]||'.'||
                                                                                arDotacao[2], stSubfuncao, 2 );
                inProgramaAnt  := 0;
            END IF;
            inSubfuncaoAnt := reRegistro.cod_subfuncao;

            IF reRegistro.cod_programa <> inProgramaAnt THEN
                SELECT INTO
                    stPrograma
                    descricao
                FROM   orcamento.programa
                WHERE  cod_programa  = reRegistro.cod_programa
                  AND  exercicio     = reRegistro.exercicio;
                INSERT INTO tmp_relatorio ( dotacao, descricao, nivel ) values ( arDotacao[1]||'.'||
                                                                                 arDotacao[2]||'.'||
                                                                                 arDotacao[3], stPrograma, 3 );
            END IF;
            inProgramaAnt := reRegistro.cod_programa;

        END LOOP;

    FOR reRegistro IN
        SELECT  *
        FROM    tmp_relatorio
        ORDER BY dotacao
    LOOP
        reRegistro.vl_ordinario := orcamento.fn_totaliza_dotacao_recurso(reRegistro.dotacao,'ordinario');
        reRegistro.vl_vinculado := orcamento.fn_totaliza_dotacao_recurso(reRegistro.dotacao,'outros');
        reRegistro.vl_total     := ( coalesce(reRegistro.vl_ordinario,0) + coalesce(reRegistro.vl_vinculado,0) );
        RETURN next reRegistro;
    END LOOP;


    DROP TABLE tmp_relatorio;
    DROP TABLE tmp_despesa;

    RETURN;
END;
$$language 'plpgsql';
