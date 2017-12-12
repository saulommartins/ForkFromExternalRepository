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
* $Revision: 14449 $
* $Name$
* $Author: cleisson $
* $Date: 2006-08-28 12:03:43 -0300 (Seg, 28 Ago 2006) $
*
* Casos de uso: uc-02.03.11, uc-02.03.03
*/

/*
$Log$
Revision 1.9  2006/08/28 15:02:30  cleisson
Bug #6762#

Revision 1.8  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos
*/

CREATE OR REPLACE FUNCTION pegaempenhoempenhorestos(varchar,integer) RETURNS VARCHAR  AS $$
DECLARE
    stExercicioRP       ALIAS FOR $1;
    inCodPreEmpenho     ALIAS FOR $2;
    crCursor            REFCURSOR;
    stSql               VARCHAR :='';
    stRetorno           VARCHAR :='';
    inCodEntidade       INTEGER;

BEGIN

  SELECT
        e.cod_entidade INTO inCodEntidade
    FROM
        empenho.empenho as e,
        empenho.pre_empenho as p
    WHERE
        e.cod_pre_empenho   = p.cod_pre_empenho
    AND e.exercicio         = p.exercicio    
    AND p.exercicio         = stExercicioRP
    AND p.cod_pre_empenho   = inCodPreEmpenho;


    stSql := '
        SELECT                                                                
         CASE WHEN parametro = ''cod_entidade_camara'' THEN ''Legislativo''   
              WHEN parametro = ''cod_entidade_rpps''   THEN ''RPPS''          
              ELSE ''Executivo''                                            
         END AS tipo_restos                                               
         FROM                                                             
           orcamento.entidade AS oe                                       
           LEFT JOIN administracao.configuracao AS ac ON(                 
                cod_modulo    = 8                AND                      
                parametro  LIKE ''cod_entidade_%'' AND                      
                ac.exercicio  = oe.exercicio     AND                      
                ac.valor      = oe.cod_entidade                           
           )                                                              
         WHERE                                                            
            oe.exercicio    = '|| stExercicioRP ||' AND 
            oe.cod_entidade = '|| inCodEntidade ||'         
    ';
    
    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO stRetorno;
    CLOSE crCursor;
    
    IF stRetorno IS NULL THEN
        stRetorno := '''';
    END IF;

    RETURN stRetorno;
END;

$$language 'plpgsql';
