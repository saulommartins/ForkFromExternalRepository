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
* Casos de uso: uc-02.00.00
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:50  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcers.fn_modelos_lrf(VARCHAR, VARCHAR, INTEGER, INTEGER, VARCHAR[][]) RETURNS BOOLEAN AS $$
DECLARE
    stTabela            ALIAS FOR $1;
    stExercicio         ALIAS FOR $2;
    inCodModelo         ALIAS FOR $3;
    inCodQuadro         ALIAS FOR $4;
    arParametros        ALIAS FOR $5;
    stSql               VARCHAR := '';
    stEstrutural        VARCHAR := '';
    inNumLinhas         INTEGER := 1;
    inLoop              INTEGER := 1;
    inCodConta          INTEGER;
    stTabelaIn          VARCHAR;
    stTabelaOut         VARCHAR;
    stEstrutVerifica    VARCHAR;

BEGIN

    stTabelaIn  := 'tcers.plano_conta_modelo_lrf';
    stTabelaOut := 'contabilidade.plano_conta';

    inNumLinhas := Coalesce(array_upper( arParametros, 1 ),0);
    FOR inLoop IN 1..inNumLinhas LOOP

        SELECT  COALESCE(MAX(cod_conta),0)+1 as cod_conta
          INTO  inCodConta
        FROM    tcers.plano_conta_modelo_lrf
        WHERE   exercicio   = stExercicio
          AND   cod_modelo  = inCodModelo
          AND   cod_quadro  = inCodQuadro
        ;

        --INSERT INTO tcers.conta_quadro(exercicio, cod_modelo, cod_quadro, cod_conta, ordem)
        --    VALUES                    (stExercicio, inCodModelo, inCodQuadro, inCodConta, to_number(arParametros[inLoop][2], \'99999999\') )
        --;

        stEstrutural := arParametros[inLoop][1];
        stEstrutural := replace(stEstrutural,'''','');
        IF stTabela = 'receita' THEN
            --stEstrutural := '4.' || substr(stEstrutural,2,length(stEstrutural)-2);
            stEstrutural := '4.' || stEstrutural;
        ELSIF stTabela = 'despesa' THEN
            --stEstrutural := '3.' || substr(stEstrutural,2,length(stEstrutural)-2);
            stEstrutural := '3.' || stEstrutural;
        END IF;



        SELECT      cod_estrutural
            INTO    stEstrutVerifica
        FROM
            contabilidade.plano_conta
        WHERE exercicio      = stExercicio
        AND   cod_estrutural = stEstrutural;

        IF stEstrutVerifica IS NULL THEN
        END IF;

        stSql := '
        INSERT INTO '|| stTabelaIn ||' (cod_modelo, cod_quadro, exercicio, cod_conta, redutora, ordem)
            SELECT
                 '|| inCodModelo||' as cod_modelo
                ,'|| inCodQuadro||' as cod_quadro
                ,exercicio
                ,cod_conta
                ,'|| arParametros[inLoop][3] ||' as redutora
                ,'|| to_number(arParametros[inLoop][2], '99999999') ||' as ordem
            FROM
                '|| stTabelaOut ||'
            WHERE exercicio      = '|| quote_literal(stExercicio) ||'
            AND   cod_estrutural = '|| quote_literal(stEstrutural);

        EXECUTE stSql;

    END LOOP;

    RETURN true;
END;
$$ LANGUAGE 'plpgsql';
