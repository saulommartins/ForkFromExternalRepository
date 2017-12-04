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

CREATE OR REPLACE FUNCTION orcamento.fn_receita_valor_previsto_uniao(varchar,varchar,varchar) RETURNS numeric(14,2) AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEstrutural         ALIAS FOR $2;
    stCodEntidades          ALIAS FOR $3;
    stSql                   VARCHAR   := '';
    nuSoma                  NUMERIC   := 0;
    crCursor                REFCURSOR;

BEGIN

    
    stSql := '
        SELECT COALESCE(SUM(COALESCE(vl_original,0.00)),0.00)::NUMERIC AS soma
        FROM (
            SELECT
               ocr.cod_estrutural  as cod_estrutural ,
               ore.vl_original     as vl_original,
               ore.cod_entidade    as cod_entidade,
               ore.exercicio       as exercicio,
               ocr.descricao      
           FROM
                orcamento.conta_receita         as ocr  ,
                orcamento.receita               as ore  ,
                orcamento.recurso               as re   ,
                stn.vinculo_recurso             as sre  
           WHERE
               ore.exercicio           = '||quote_literal(stExercicio)||'
               AND ore.cod_entidade    IN ( '||stCodEntidades||' )
               AND ocr.cod_estrutural like '||quote_literal(stCodEstrutural||'%')||'
               AND ocr.cod_conta       = ore.cod_conta
               AND ocr.exercicio       = ore.exercicio
       
               -- join recurso
               AND re.cod_recurso      = ore.cod_recurso
               AND re.exercicio        = ore.exercicio 
       
               -- join vinculo_recurso
               AND sre.cod_recurso      = ore.cod_recurso
               AND sre.exercicio        = ore.exercicio
               AND sre.cod_vinculo      IN (7,8,9)
       
           GROUP BY
               ocr.cod_estrutural ,
               ore.vl_original ,
               ore.cod_entidade ,
               ore.exercicio ,
               ocr.descricao
        ) AS soma ';

    EXECUTE stSql;

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuSoma;
    CLOSE crCursor;

    RETURN nuSoma;
END;
$$ language 'plpgsql';
