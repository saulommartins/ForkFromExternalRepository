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
/**
    * Script de função PLPGSQL - Relatório STN - RREO - Anexo 14.
    * Data de Criação: 09/06/2008


    * @author Rodrigo Soares Rodrigues

    * Casos de uso: uc-06.01.14

    $Id: $

*/

CREATE OR REPLACE FUNCTION stn.fn_anexo14_receita_valor_previsto(varchar,varchar,varchar,varchar) RETURNS numeric(14,2) AS '
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEstrutural         ALIAS FOR $2;
    stCodEntidades          ALIAS FOR $3;
    stCodRecursos           ALIAS FOR $4;
    stSql                   VARCHAR   := '''';
    nuSoma                  NUMERIC   := 0;
    crCursor                REFCURSOR;

BEGIN
    stSql := ''SELECT
                sum( r.vl_original ) as soma
                FROM    orcamento.conta_receita cr,
                        orcamento.receita r
                WHERE   cr.exercicio = '''''' || stExercicio || '''''' AND cr.exercicio = r.exercicio AND
                        cr.cod_estrutural like '''''' || stCodEstrutural || ''%'''' AND
                        r.cod_entidade IN ( '' || stCodEntidades || '' ) AND
                        r.cod_recurso IN ( '' || stCodRecursos || '' ) AND
                        r.cod_conta = cr.cod_conta'';

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuSoma;
    CLOSE crCursor;

    RETURN nuSoma;
END;
'language 'plpgsql';