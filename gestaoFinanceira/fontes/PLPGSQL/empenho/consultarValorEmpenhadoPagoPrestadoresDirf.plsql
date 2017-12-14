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
* Casos de uso: uc-02.03.03
*/

/*
$Log$
Revision 1.5  2006/07/05 20:37:37  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_consultar_valor_empenhado_pago_prestadores_dirf(VARCHAR,INTEGER,INTEGER,INTEGER) RETURNS NUMERIC AS $$

DECLARE
    stExercicio                ALIAS FOR $1;
    inCodEmpenho               ALIAS FOR $2;
    inCodEntidade              ALIAS FOR $3;
    inMes                      ALIAS FOR $4;    
    nuValor                    NUMERIC := 0.00;
BEGIN
    
    SELECT DISTINCT
        coalesce(sum(nota_liquidacao_paga.vl_pago),0.00)
        FROM empenho.empenho        
        INTO nuValor
        
        INNER JOIN empenho.nota_liquidacao      
             ON nota_liquidacao.exercicio_empenho    = empenho.exercicio
            AND nota_liquidacao.cod_entidade         = empenho.cod_entidade
            AND nota_liquidacao.cod_empenho          = empenho.cod_empenho

        INNER JOIN empenho.nota_liquidacao_paga 
            ON nota_liquidacao_paga.cod_nota        = nota_liquidacao.cod_nota
            AND nota_liquidacao_paga.exercicio       = nota_liquidacao.exercicio
            AND nota_liquidacao_paga.cod_entidade    = nota_liquidacao.cod_entidade

        INNER JOIN empenho.pre_empenho          
            ON  pre_empenho.exercicio                = empenho.exercicio
            AND pre_empenho.cod_pre_empenho          = empenho.cod_pre_empenho

        LEFT JOIN empenho.pre_empenho_despesa  
            ON pre_empenho_despesa.exercicio         = pre_empenho.exercicio
            AND pre_empenho_despesa.cod_pre_empenho  = pre_empenho.cod_pre_empenho
        
        LEFT JOIN empenho.restos_pre_empenho
                ON restos_pre_empenho.cod_pre_empenho   = pre_empenho.cod_pre_empenho
                AND restos_pre_empenho.exercicio        = pre_empenho.exercicio
         
        LEFT JOIN empenho.nota_liquidacao_paga_anulada
            ON nota_liquidacao_paga_anulada.exercicio       = nota_liquidacao_paga.exercicio
            AND nota_liquidacao_paga_anulada.cod_nota       = nota_liquidacao_paga.cod_nota
            AND nota_liquidacao_paga_anulada.cod_entidade   = nota_liquidacao_paga.cod_entidade
            AND nota_liquidacao_paga_anulada.timestamp      = nota_liquidacao_paga.timestamp

        WHERE empenho.cod_entidade = inCodEntidade
        AND (empenho.cod_empenho = inCodEmpenho OR restos_pre_empenho.cod_pre_empenho = inCodEmpenho)
        AND nota_liquidacao_paga_anulada.cod_nota IS NULL
        AND to_char(nota_liquidacao_paga.timestamp, 'mm')::INT = inMes
        AND to_char(nota_liquidacao_paga.timestamp, 'yyyy') = stExercicio    
        ;

    IF nuValor IS NULL THEN
        nuValor := 0.00;
    END IF;

    RETURN nuValor;

END;
$$ LANGUAGE 'plpgsql';
