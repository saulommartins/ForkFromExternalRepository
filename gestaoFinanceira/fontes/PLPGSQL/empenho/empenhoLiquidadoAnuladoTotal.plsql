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
*/
CREATE OR REPLACE FUNCTION empenho.fn_empenho_liquidado_anulado_total(VARCHAR, INTEGER, VARCHAR, VARCHAR) RETURNS NUMERIC AS $$
DECLARE
    stExercicio                    ALIAS FOR $1;
    inCodEntidade                  ALIAS FOR $2;
    stDataInicial                  ALIAS FOR $3;
    stDataFinal                    ALIAS FOR $4;
    
    stSql                          VARCHAR := '';
    nuValor                        NUMERIC := 0;
    reRegistro                     RECORD;
BEGIN

  stSql := '     SELECT SUM(nota_liquidacao_item_anulado.vl_anulado) AS vl_anulado
                   FROM empenho.empenho
                   
             INNER JOIN empenho.pre_empenho
                     ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                    AND pre_empenho.exercicio       = empenho.exercicio
             
             INNER JOIN empenho.nota_liquidacao
                     ON nota_liquidacao.exercicio_empenho = empenho.exercicio    
                    AND nota_liquidacao.cod_entidade      = empenho.cod_entidade 
                    AND nota_liquidacao.cod_empenho       = empenho.cod_empenho
             
             INNER JOIN empenho.nota_liquidacao_item
                     ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio            
                    AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota             
                    AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
            
             INNER JOIN empenho.nota_liquidacao_item_anulado
                     ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
                    AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                    AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item      
                    AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade
                    AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
                    AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota
                    AND to_date(to_char(nota_liquidacao_item_anulado.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN TO_DATE('|| quote_literal(stDataInicial) ||',''dd/mm/yyyy'') AND TO_DATE('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')

                  WHERE empenho.exercicio                            = ' || quote_literal( stExercicio ) || '
                    AND nota_liquidacao_item_anulado.cod_entidade IN ( ' || inCodEntidade || ' )';

    EXECUTE stSql;  

    FOR reRegistro IN EXECUTE stSql
    LOOP
       nuValor := reRegistro.vl_anulado;
    END LOOP;

    RETURN nuValor;
    
END;
$$ language 'plpgsql';