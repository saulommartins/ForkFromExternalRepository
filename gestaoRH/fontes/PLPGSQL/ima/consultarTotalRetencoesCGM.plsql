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
/* consultar_total_retencoes_cgm
 * 
 * Data de Criação : 23/01/2009


 * @author Analista : Dagiane   
 * @author Desenvolvedor : Rafael Garbin
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */

CREATE OR REPLACE FUNCTION consultar_total_retencoes_cgm(INTEGER,INTEGER,INTEGER,INTEGER,VARCHAR,VARCHAR) RETURNS NUMERIC AS $$
DECLARE
    inExercicio                ALIAS FOR $1;
    inCodCGM                   ALIAS FOR $2;
    inCodEntidade              ALIAS FOR $3;
    inCodConta                 ALIAS FOR $4;
    stEntidade                 ALIAS FOR $5;
    stTipoConta                ALIAS FOR $6;
    nuValor                    NUMERIC := 0.00;
    reRecord                   RECORD;
    stSql                      VARCHAR;
BEGIN

   stSql :='
    SELECT DISTINCT
        SUM(coalesce(empenho.ordem_pagamento_retencao.vl_retencao,0)) as valor          

          FROM empenho.ordem_pagamento_retencao
                    
    INNER JOIN empenho.ordem_pagamento 
            ON ordem_pagamento.cod_ordem = ordem_pagamento_retencao.cod_ordem
           AND ordem_pagamento.exercicio = ordem_pagamento_retencao.exercicio
           AND ordem_pagamento.cod_entidade = ordem_pagamento_retencao.cod_entidade
           
    INNER JOIN empenho.pagamento_liquidacao
            ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio
           AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
           AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem
           
    INNER JOIN empenho.nota_liquidacao
            ON nota_liquidacao.exercicio = pagamento_liquidacao.exercicio_liquidacao 
           AND nota_liquidacao.cod_entidade = pagamento_liquidacao.cod_entidade 
           AND nota_liquidacao.cod_nota = pagamento_liquidacao.cod_nota
    
    INNER JOIN empenho.nota_liquidacao_paga
            ON nota_liquidacao_paga.exercicio       = nota_liquidacao.exercicio
            AND nota_liquidacao_paga.cod_entidade   = nota_liquidacao.cod_entidade
            AND nota_liquidacao_paga.cod_nota       = nota_liquidacao.cod_nota

    INNER JOIN empenho.empenho
            ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
           AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
           AND empenho.cod_entidade = nota_liquidacao.cod_entidade
    
    INNER JOIN empenho.pre_empenho
            ON pre_empenho.exercicio       = empenho.exercicio
           AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho        
    
    LEFT JOIN empenho.restos_pre_empenho
            ON restos_pre_empenho.cod_pre_empenho   = pre_empenho.cod_pre_empenho
            AND restos_pre_empenho.exercicio        = pre_empenho.exercicio

    LEFT JOIN empenho.nota_liquidacao_paga_anulada
            ON nota_liquidacao_paga_anulada.exercicio       = nota_liquidacao_paga.exercicio
            AND nota_liquidacao_paga_anulada.cod_nota       = nota_liquidacao_paga.cod_nota
            AND nota_liquidacao_paga_anulada.cod_entidade   = nota_liquidacao_paga.cod_entidade
            AND nota_liquidacao_paga_anulada.timestamp      = nota_liquidacao_paga.timestamp

    LEFT JOIN empenho.ordem_pagamento_anulada           
            ON ordem_pagamento_anulada.exercicio        = ordem_pagamento.exercicio
            AND ordem_pagamento_anulada.cod_entidade    = ordem_pagamento.cod_entidade
            AND ordem_pagamento_anulada.cod_ordem       = ordem_pagamento.cod_ordem
    
         WHERE ordem_pagamento_retencao.exercicio = '''||inExercicio||'''
           AND ordem_pagamento_retencao.cod_entidade = '||inCodEntidade||'
           AND nota_liquidacao_paga_anulada.cod_nota IS NULL
           AND ordem_pagamento_anulada.cod_ordem IS NULL
           AND pre_empenho.cgm_beneficiario = '||inCodCGM;

    FOR reRecord IN EXECUTE stSql
    LOOP        
        IF reRecord.valor IS NULL THEN            
            reRecord.valor := 0.00;
        END IF;
        
        RETURN reRecord.valor;
    END LOOP;

END;
$$ LANGUAGE 'plpgsql';
