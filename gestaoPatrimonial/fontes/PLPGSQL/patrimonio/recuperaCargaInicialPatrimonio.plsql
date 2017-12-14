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
/* PL responsável por popular as tabelas do Inventário com o último histórico
 * do bem do Patrimonio.
 *
 * Data de Criação: 09/10/2009


 * @author Analista:      Gelson Wolowski
 * @author Desenvolvedor: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */

CREATE OR REPLACE FUNCTION patrimonio.fn_carga_inventario_patrimonio(VARCHAR, INTEGER, INTEGER) RETURNS INTEGER as $$
DECLARE

    stExercicio   ALIAS FOR $1;
    idInventario  ALIAS FOR $2;
    idNumCgm      ALIAS FOR $3;

BEGIN
  
    INSERT INTO  patrimonio.inventario_historico_bem
            (
                  exercicio
               ,  id_inventario
               ,  cod_bem
               ,  timestamp_historico
               ,  timestamp
               ,  cod_situacao
               ,  cod_local
               ,  cod_orgao
               ,  descricao
            )
       
       SELECT  stExercicio
            ,  idInventario
            ,  historico_bem.cod_bem
            ,  historico_bem.timestamp
            ,  NOW()::timestamp(3)
            ,  historico_bem.cod_situacao
            ,  historico_bem.cod_local
            ,  historico_bem.cod_orgao
            ,  ''
            
         FROM  patrimonio.historico_bem
         
   INNER JOIN  patrimonio.bem
           ON  bem.cod_bem = historico_bem.cod_bem
   
   INNER JOIN  (
                   SELECT  cod_bem
                        ,  MAX(timestamp) AS timestamp
                     FROM  patrimonio.historico_bem
                 GROUP BY  cod_bem
               ) as resumo
       ON  resumo.cod_bem   = historico_bem.cod_bem
      AND  resumo.timestamp = historico_bem.timestamp
   
        WHERE  1=1
        
          AND  NOT EXISTS
               (
                    SELECT  1
                      FROM  patrimonio.bem_baixado
                     WHERE  bem_baixado.cod_bem = bem.cod_bem
               )  ORDER BY  historico_bem.cod_bem;
  
  RETURN 1;
 
END
$$ LANGUAGE 'plpgsql';
