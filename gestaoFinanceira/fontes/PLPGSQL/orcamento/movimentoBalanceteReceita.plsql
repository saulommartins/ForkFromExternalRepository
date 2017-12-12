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
* Casos de uso: uc-02.01.21
*/

/*
$Log$
Revision 1.6  2006/07/05 20:38:04  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_movimento_balancete_receita(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR)  RETURNS boolean AS $$
DECLARE
    stExercicio                 ALIAS FOR $1;
    stCodEstrutural             ALIAS FOR $2;
    stCodEntidades              ALIAS FOR $3;
    dtInicial                   ALIAS FOR $4;
    dtFinal                     ALIAS FOR $5;
    stSql                       VARCHAR   := '';
    nuPrevisto                  NUMERIC   := 0;
    inNumRegistro               INTEGER   := 0;
    boRetorno                   BOOLEAN   := FALSE;
    crCursor                    REFCURSOR;

BEGIN
     stSql := 'SELECT   count(*)
        FROM
                 tmp_valor
        WHERE
                cod_estrutural like ''' || stCodEstrutural || '%''
                AND  data BETWEEN to_date('|| quote_literal(dtInicial) ||',''dd/mm/yyyy'') AND to_date('|| quote_literal(dtFinal) ||', ''dd/mm/yyyy'')
                ';


    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO inNumRegistro;
    CLOSE crCursor;


    stSql := 'SELECT
                count( r.vl_original ) as soma
                FROM    orcamento.conta_receita cr,
                        orcamento.receita r
                WHERE   cr.exercicio = ''' || stExercicio || ''' AND cr.exercicio = r.exercicio AND
                        cr.cod_estrutural like ''' || stCodEstrutural || '%'' AND
                        r.cod_entidade IN ( ' || stCodEntidades || ' ) AND
                        r.cod_conta = cr.cod_conta
                 AND r.vl_original <> 0
            ';

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuPrevisto;
    CLOSE crCursor;


    IF ( nuPrevisto > 0 OR inNumRegistro > 0 ) THEN
        boRetorno = TRUE;
    END IF;


    RETURN boRetorno;
END;
$$ LANGUAGE 'plpgsql';
