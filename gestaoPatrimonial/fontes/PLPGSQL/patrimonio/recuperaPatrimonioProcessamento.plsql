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
 * PL responsável por encerrar o processo do Inventário, atualizando
 * o histórico dos bens conforme as alterações do Inventário.
 *
 * Data de Criação: 14/10/2009


 * @author Analista:      Gelson Wolowski
 * @author Desenvolvedor: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */

CREATE OR REPLACE FUNCTION patrimonio.fn_inventario_processamento(VARCHAR, INTEGER) RETURNS INTEGER as $$
DECLARE

    stExercicio   ALIAS FOR $1;
    idInventario  ALIAS FOR $2;

BEGIN

  INSERT INTO  patrimonio.historico_bem
  
       SELECT  
               inventario_historico_bem.cod_bem
            ,  inventario_historico_bem.cod_situacao
            ,  inventario_historico_bem.cod_local
            ,  inventario_historico_bem.cod_orgao
            ,  NOW()::timestamp(3)
            ,  inventario_historico_bem.descricao
            
         FROM  patrimonio.inventario_historico_bem
         
   INNER JOIN  patrimonio.bem
           ON  bem.cod_bem = inventario_historico_bem.cod_bem
   
        WHERE  1=1
  
          AND  inventario_historico_bem.id_inventario = idInventario
          AND  inventario_historico_bem.exercicio     = stExercicio;

  RETURN 1;
 
END
$$ LANGUAGE 'plpgsql';
