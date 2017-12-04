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
* $Revision: 24681 $
* $Name$
* $Author: luciano $
* $Date: 2007-08-10 18:38:52 -0300 (Sex, 10 Ago 2007) $
*
* Casos de uso: uc-02.03.31
*/

/*
$Log$
Revision 1.1  2007/08/10 21:38:52  luciano
uc adiantamentos


*/

CREATE OR REPLACE FUNCTION empenho.fn_consultar_valor_prestado_nao_anulado(VARCHAR,INTEGER,INTEGER) RETURNS NUMERIC AS '

DECLARE
    stExercicio                ALIAS FOR $1;
    inCodEmpenho               ALIAS FOR $2;
    inCodEntidade              ALIAS FOR $3;
    nuValorPrestado            NUMERIC := 0.00;
    inCount                    INTEGER;
BEGIN

    SELECT COUNT(*) 
      INTO inCount 
      FROM empenho.item_prestacao_contas 
     WHERE cod_empenho = ( SELECT cod_empenho 
                             FROM empenho.prestacao_contas 
                            WHERE cod_empenho = inCodEmpenho 
                              AND cod_entidade = inCodEntidade 
                              AND exercicio = stExercicio
                         );

    IF (inCount > 0) THEN
    SELECT                                                              
        coalesce(SUM(valor_item),0.00) as vl_prestado
        INTO    nuValorPrestado                    
    FROM                                                                   
        empenho.item_prestacao_contas as eipc                              
    WHERE                                                                  
        NOT EXISTS ( SELECT num_item                                          
                     FROM empenho.item_prestacao_contas_anulado             
                     WHERE                                                    
                            cod_empenho     = eipc.cod_empenho                
                        AND exercicio       = eipc.exercicio                   
                        AND cod_entidade    = eipc.cod_entidade                
                        AND num_item        = eipc.num_item                    
                    )
    
        AND exercicio       = stExercicio
        AND cod_empenho     = inCodEmpenho
        AND cod_entidade    = inCodEntidade;
    ELSE
      SELECT COALESCE(tabela.vl_prestar, 0.00) INTO nuValorPrestado FROM (
          SELECT SUM(COALESCE(nota_liquidacao_paga.vl_pago, 0.00)) -
                 SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado, 0.00)) AS vl_prestar
               , prestacao_contas.exercicio
               , prestacao_contas.cod_empenho
               , prestacao_contas.cod_entidade
            FROM empenho.prestacao_contas
            JOIN empenho.empenho
              ON empenho.cod_empenho = prestacao_contas.cod_empenho
             AND empenho.exercicio = prestacao_contas.exercicio
             AND empenho.cod_entidade = prestacao_contas.cod_entidade
            JOIN empenho.nota_liquidacao
              ON nota_liquidacao.cod_empenho = empenho.cod_empenho
             AND nota_liquidacao.exercicio = empenho.exercicio
             AND nota_liquidacao.cod_entidade = empenho.cod_entidade
            JOIN empenho.nota_liquidacao_paga
              ON nota_liquidacao_paga.cod_nota = nota_liquidacao.cod_nota
             AND nota_liquidacao_paga.exercicio = nota_liquidacao.exercicio
             AND nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
       LEFT JOIN empenho.nota_liquidacao_paga_anulada
              ON nota_liquidacao_paga_anulada.cod_nota = nota_liquidacao_paga.cod_nota
             AND nota_liquidacao_paga_anulada.exercicio = nota_liquidacao_paga.exercicio
             AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
             AND nota_liquidacao_paga_anulada.timestamp = nota_liquidacao_paga.timestamp
        GROUP BY prestacao_contas.cod_empenho
               , prestacao_contas.cod_entidade
               , prestacao_contas.exercicio
               , prestacao_contas.data
        ORDER BY prestacao_contas.cod_empenho
               , prestacao_contas.data
               , prestacao_contas.cod_entidade
               , prestacao_contas.exercicio
      ) AS tabela
WHERE tabela.exercicio = stExercicio
  AND tabela.cod_empenho = inCodEmpenho
  AND tabela.cod_entidade = inCodEntidade;
    END IF;
    
    RETURN nuValorPrestado;

END;
'LANGUAGE 'plpgsql';
