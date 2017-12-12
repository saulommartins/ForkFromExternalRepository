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
CREATE OR REPLACE FUNCTION empenho.fn_empenho_liquidado_total(VARCHAR, INTEGER, VARCHAR, VARCHAR) RETURNS NUMERIC AS $$
DECLARE
    stExercicio                    ALIAS FOR $1;
    inCodEntidade                  ALIAS FOR $2;
    stDataInicial                  ALIAS FOR $3;
    stDataFinal                    ALIAS FOR $4;
    
    stSql                          VARCHAR := '';
    nuValor                        NUMERIC := 0;
    reRegistro                     RECORD;
BEGIN

        stSql := ' 
                SELECT SUM(empenho.nota_liquidacao_item.vl_total) AS vl_total          
                  FROM empenho.empenho
                       
            INNER JOIN empenho.pre_empenho
                    ON empenho.pre_empenho.cod_pre_empenho  = empenho.empenho.cod_pre_empenho
                   AND empenho.pre_empenho.exercicio        = empenho.empenho.exercicio
                       
            INNER JOIN empenho.pre_empenho_despesa
                    ON empenho.pre_empenho.exercicio       = empenho.pre_empenho_despesa.exercicio
                   AND empenho.pre_empenho.cod_pre_empenho = empenho.pre_empenho_despesa.cod_pre_empenho    
                       
                   
            INNER JOIN empenho.nota_liquidacao
                    ON empenho.empenho.exercicio    = empenho.nota_liquidacao.exercicio_empenho
                   AND empenho.empenho.cod_entidade = empenho.nota_liquidacao.cod_entidade
                   AND empenho.empenho.cod_empenho  = empenho.nota_liquidacao.cod_empenho    
                       
            INNER JOIN empenho.nota_liquidacao_item
                    ON empenho.nota_liquidacao_item.exercicio    = empenho.nota_liquidacao.exercicio
                   AND empenho.nota_liquidacao_item.cod_nota     = empenho.nota_liquidacao.cod_nota 
                   AND empenho.nota_liquidacao_item.cod_entidade = empenho.nota_liquidacao.cod_entidade
                    
                 WHERE empenho.empenho.exercicio            = ' || quote_literal( stExercicio ) || '
                   And empenho.nota_liquidacao.exercicio    = ' || quote_literal( stExercicio ) || '
                   AND empenho.nota_liquidacao.cod_entidade IN ( ' || inCodEntidade || ' ) 
                   AND empenho.nota_liquidacao.dt_liquidacao BETWEEN TO_DATE('|| quote_literal(stDataInicial) ||',''dd/mm/yyyy'')
                                                                 AND TO_DATE('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')';
    EXECUTE stSql;  

    FOR reRegistro IN EXECUTE stSql
    LOOP
       nuValor := reRegistro.vl_total;
    END LOOP;

    RETURN nuValor;
END;
$$ language 'plpgsql';