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
* Casos de uso: uc-02.02.04
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:31  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION contabilidade.fn_recupera_valor_implantacao_saldo(VARCHAR,INTEGER) RETURNS NUMERIC AS '

DECLARE
    reRecord            RECORD;
    stSql               VARCHAR := '''';
    stExercicio         ALIAS FOR $1;
    inCodPlano          ALIAS FOR $2;
    nuOut               NUMERIC;
    chTipo              CHAR := ''I'';

BEGIN

    stSql := ''
        SELECT * FROM (
                SELECT
                    vl.*
                FROM
                     contabilidade.conta_debito     as cd
                    ,contabilidade.valor_lancamento as vl
                WHERE   cd.cod_lote  = vl.cod_lote
                AND     cd.tipo      = vl.tipo
                AND     cd.sequencia = vl.sequencia
                AND     cd.exercicio = vl.exercicio
                AND     cd.tipo_valor= vl.tipo_valor
                AND     cd.cod_entidade= vl.cod_entidade
                AND     vl.tipo      = ''''''||chTipo||''''''
                AND     cd.exercicio   = ''||stExercicio||''
                AND     cd.cod_plano   = ''||inCodPlano||''
            UNION
                SELECT
                    vl.*
                FROM
                     contabilidade.conta_credito    as cc
                    ,contabilidade.valor_lancamento as vl
                WHERE   cc.cod_lote  = vl.cod_lote
                AND     cc.tipo      = vl.tipo
                AND     cc.sequencia = vl.sequencia
                AND     cc.exercicio = vl.exercicio
                AND     cc.tipo_valor= vl.tipo_valor
                AND     cc.cod_entidade= vl.cod_entidade
                AND     vl.tipo      = ''''''||chTipo||''''''
                AND     cc.exercicio   = ''||stExercicio||''
                AND     cc.cod_plano   = ''||inCodPlano||''
        ) as tabela
        '';

    FOR reRecord IN EXECUTE stSql LOOP
        nuOut := reRecord.vl_lancamento;
    END LOOP;

    RETURN nuOut;

END;
'LANGUAGE 'plpgsql';
