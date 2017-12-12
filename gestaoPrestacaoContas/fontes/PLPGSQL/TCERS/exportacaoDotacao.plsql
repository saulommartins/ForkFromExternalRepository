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
* $Revision: 59612 $
* $Name$
* $Author: gelson $
* $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
*
* Casos de uso: uc-02.08.08
* Casos de uso: uc-02.08.08
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.6  2006/07/05 20:37:45  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcerj.fn_exportacao_dotacao(varchar,varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEntidades          ALIAS FOR $2;
    stSql                   VARCHAR   := '''';
    reRegistro              RECORD;
BEGIN
        stSql := ''CREATE TEMPORARY TABLE tmp_relacao AS
                SELECT
                    cast(substr(replace(ocd.cod_estrutural,''''.'''',''''''''),1,8) as integer) as cod_estrutural,
                    od.num_unidade,
                    od.exercicio,
                    orcamento.fn_consulta_tipo_pao(cast('' || stExercicio || '' as varchar),od.num_pao),
                    od.num_pao,
                    od.cod_recurso,
                    od.cod_funcao,
                    od.cod_subfuncao,
                    od.cod_programa,
                    --empenho.fn_saldo_dotacao(od.exercicio,od.cod_despesa)  as valor,
                    od.vl_original,
                    od.num_orgao
                FROM
                    orcamento.conta_despesa ocd,
                    orcamento.despesa od,
                    orcamento.recurso oru,
                    orcamento.funcao ofu,
                    orcamento.subfuncao osf,
                    orcamento.programa opg,
                    orcamento.pao opao
                WHERE   ocd.cod_conta   = od.cod_conta
                AND     ocd.exercicio   = od.exercicio
                AND     od.cod_recurso  = oru.cod_recurso
                AND     od.exercicio    = oru.exercicio
                AND     od.cod_funcao   = ofu.cod_funcao
                AND     od.exercicio    = ofu.exercicio
                AND     od.cod_subfuncao= osf.cod_subfuncao
                AND     od.exercicio    = osf.exercicio
                AND     od.cod_programa = opg.cod_programa
                AND     od.exercicio    = opg.exercicio
                AND     od.num_pao      = opao.num_pao
                AND     od.exercicio    = opao.exercicio
                AND     od.exercicio    = '' || stExercicio || ''
                AND     od.cod_entidade IN ('' || stCodEntidades || '')
                '';
        EXECUTE stSql;



    FOR reRegistro IN
        SELECT  *
        FROM    tmp_relacao
        ORDER BY cod_estrutural
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_relacao;
    RETURN;


END;

'language plpgsql;

