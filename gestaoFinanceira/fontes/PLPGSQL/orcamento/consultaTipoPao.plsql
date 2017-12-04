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
* Casos de uso: uc-02.08.08
*/

CREATE OR REPLACE FUNCTION orcamento.fn_consulta_tipo_pao(VARCHAR, INTEGER)  RETURNS INTEGER AS $$
DECLARE
    stExercicio                 ALIAS FOR $1;
    inNumPao                    ALIAS FOR $2;
    stSql                       VARCHAR   := '';

    inPosPao                    INTEGER;
    inMascPao                   INTEGER;
    inTipoPao                   INTEGER;

    arRetorno                   NUMERIC[] := array[0];
    crCursor                    REFCURSOR;
BEGIN

    SELECT
        string_to_array(valor,'.') INTO arRetorno
    FROM
        administracao.configuracao
    WHERE
        parametro   = 'masc_despesa' AND
        exercicio   = stExercicio  AND
        cod_modulo  = 8 ;

    IF(arRetorno IS NULL) THEN
        RETURN NULL;
    END IF;

    SELECT
        valor INTO inPosPao
    FROM
        administracao.configuracao
    WHERE
        parametro   = 'pao_posicao_digito_id' AND
        exercicio   = stExercicio  AND
        cod_modulo  = 8 ;

    SELECT substr(publico.fn_mascara_dinamica(cast(arRetorno[5]as varchar), cast(inNumPao as varchar) ),inPosPao,1) INTO inMascPao ;

    SELECT
        strpos(valor,cast(inMascPao as varchar)) INTO inPosPao
    FROM
        administracao.configuracao
    WHERE
        parametro   = 'pao_digitos_id_projeto' AND
        exercicio   = stExercicio  AND
        cod_modulo  = 8 ;

    IF (inPosPao<=0) THEN
        SELECT
            strpos(valor,cast(inMascPao as varchar)) INTO inPosPao
        FROM
            administracao.configuracao
        WHERE
            parametro   = 'pao_digitos_id_atividade' AND
            exercicio   = stExercicio  AND
            cod_modulo  = 8 ;
        IF (inPosPao<=0) THEN
            SELECT
                strpos(valor,cast(inMascPao as varchar)) INTO inPosPao
            FROM
                administracao.configuracao
            WHERE
                parametro   = 'pao_digitos_id_oper_especiais' AND
                exercicio   = stExercicio  AND
                cod_modulo  = 8 ;
            IF (inPosPao <= 0) THEN
                SELECT
                    strpos(valor,cast(inMascPao as varchar)) INTO inPosPao
                FROM
                    administracao.configuracao
                WHERE
                    parametro   = 'pao_digitos_id_nao_orcamentarios' AND
                    exercicio   = stExercicio  AND
                    cod_modulo  = 8 ;
                IF (inPosPao > 0) THEN
                    inTipoPao := 4;
                END IF;  
            ELSE 
                inTipoPao := 3;
            END IF;
        ELSE
            inTipoPao := 2;
        END IF;
    ELSE
        inTipoPao := 1;
    END IF;


    RETURN inTipoPao;
END;
$$ LANGUAGE 'plpgsql';

