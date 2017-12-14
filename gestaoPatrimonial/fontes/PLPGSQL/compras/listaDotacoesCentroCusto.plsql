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
* $Revision: 19674 $
* $Name$
* $Author: hboaventura $
* $Date: 2007-01-29 09:44:44 -0200 (Seg, 29 Jan 2007) $
*
* Casos de uso: uc-03.04.01
*/
/*                         */            

/*
$Log$
Revision 1.6  2007/01/29 11:44:44  hboaventura
Bug #7954#

Revision 1.5  2007/01/09 16:19:49  hboaventura
Bug #7954#

Revision 1.4  2007/01/08 18:36:20  hboaventura
Correção de duplicação de registros

Revision 1.3  2006/12/28 15:17:08  hboaventura
Bug #7771#, #7892#

Revision 1.1  2006/12/28 14:56:37  hboaventura
Bug #7771#, #7892#

*/

CREATE OR REPLACE FUNCTION compras.fn_lista_dotacoes(INTEGER,INTEGER,INTEGER,VARCHAR,INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    inCodEntidade       ALIAS FOR $1;
    exercicio           ALIAS FOR $2;
    inCodCentro         ALIAS FOR $3;
    inCodDespesa        ALIAS FOR $4;
    inNumCgm            ALIAS FOR $5;
    reRecord            RECORD;
    inTmp               integer;
    stSQL               VARCHAR := '';

BEGIN
    
    SELECT 
            ccd.cod_despesa
    into
            inTmp
    FROM    almoxarifado.centro_custo_dotacao as ccd
    WHERE   ccd.cod_centro      = inCodCentro 
    AND     ccd.cod_entidade    = inCodEntidade;

IF ( FOUND ) THEN

    stSQL := '
                SELECT 
                        despesa.cod_despesa,
                        conta_despesa.descricao
                FROM
                        orcamento.conta_despesa,
                        orcamento.despesa
                INNER JOIN
                        almoxarifado.centro_custo_dotacao
                ON
                        centro_custo_dotacao.exercicio = '|| quote_literal(exercicio) || '
                AND     centro_custo_dotacao.cod_centro = '|| inCodCentro || '
                AND     centro_custo_dotacao.cod_entidade = '|| inCodEntidade || '
    ';  
    
    IF ( inCodDespesa != '' ) THEN
        stSQL := stSQL || '
                AND     centro_custo_dotacao.cod_despesa = '|| quote_literal(inCodDespesa) || ' 
        ';
    END IF;
    stSQL := stSQL || '
                AND     centro_custo_dotacao.exercicio = despesa.exercicio     
                AND     centro_custo_dotacao.cod_despesa = despesa.cod_despesa  
                AND     centro_custo_dotacao.cod_entidade = despesa.cod_entidade   
                WHERE                                                              
                        conta_despesa.exercicio IS NOT NULL                        
                AND     despesa.cod_conta     = conta_despesa.cod_conta            
                AND     despesa.exercicio     = '|| quote_literal(exercicio) ||'            
                AND     despesa.exercicio     = conta_despesa.exercicio            
                AND     despesa.num_orgao IN (                                  
                                                SELECT                             
                                                        num_orgao                    
                                                FROM                               
                                                        empenho.permissao_autorizacao
                                                WHERE   numcgm = ' || inNumCgm || '
                                                AND     exercicio = '|| quote_literal(exercicio) ||'                
                                             )
    ';

ELSE

    stSQL := '
                SELECT
                        despesa.cod_despesa,
                        conta_despesa.descricao
                FROM
                        orcamento.despesa,
                        orcamento.conta_despesa
                WHERE                
                        conta_despesa.exercicio IS NOT NULL                                        
                AND     despesa.cod_conta     = conta_despesa.cod_conta  
                AND		despesa.exercicio     = '|| quote_literal(exercicio) ||'   
                AND     despesa.exercicio     = conta_despesa.exercicio                            
                AND     despesa.num_orgao IN (                                  
                                                SELECT                                             
                                                      num_orgao                           
                                                FROM                                               
                                                      empenho.permissao_autorizacao                
                                                WHERE                                              
                                                      numcgm    =  '|| inNumCgm ||' AND
                                                      exercicio =  '|| quote_literal(exercicio) ||' 
                                             )                                                     
                AND     despesa.cod_entidade = ' || inCodEntidade || ' 
    ';  
    
	IF ( inCodDespesa != '' ) THEN
    			stSQL := stSQL || ' AND despesa.cod_despesa = ' || quote_literal(inCodDespesa) || ' ';
    END IF;
    
        stSQL := stSQL || '
                GROUP BY 
                		despesa.cod_despesa,
                		conta_despesa.descricao
                	
    ';

END IF;
FOR reRecord IN EXECUTE stSQL LOOP 
    return next reRecord;
END LOOP;

END;
$$ language 'plpgsql'; 
