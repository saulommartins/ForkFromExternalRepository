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
 * Deleta as contas sintéticas e analíticas que não possuem movimentações
 * Data de Criação   : 12/12/2013

 * @author Desenvolvedor Eduardo Paculski Schitz
 
 * @package URBEM
 * @subpackage 

 $Id:$
*/

CREATE OR REPLACE FUNCTION contabilidade.deletar_escolha_plano_contas(VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio            ALIAS FOR $1;
    stSql                  VARCHAR   := '';
    reRegistro             RECORD;
BEGIN

    -- Deleta os vínculos das contas Analíticas
    stSql := '
        DELETE 
          FROM contabilidade.plano_recurso 
         WHERE plano_recurso.exercicio = ''' || stExercicio || ''' 
           AND plano_recurso.cod_plano IN (    SELECT plano_analitica.cod_plano
                                                 FROM contabilidade.plano_conta
                                                 JOIN contabilidade.plano_analitica
                                                   ON plano_analitica.cod_conta = plano_conta.cod_conta                                             
                                                  AND plano_analitica.exercicio = plano_conta.exercicio                                             
                                                WHERE plano_conta.exercicio = ''' || stExercicio || '''
                                                  AND NOT EXISTS ( SELECT 1 FROM contabilidade.conta_debito                                         
                                                                           WHERE conta_debito.exercicio = plano_analitica.exercicio                 
                                                                             AND conta_debito.cod_plano = plano_analitica.cod_plano )               
                                                  AND NOT EXISTS ( SELECT 1 FROM contabilidade.conta_credito                                        
                                                                           WHERE conta_credito.exercicio = plano_analitica.exercicio                
                                                                             AND conta_credito.cod_plano = plano_analitica.cod_plano )              
                                                  AND NOT EXISTS ( SELECT 1 FROM contabilidade.plano_banco                                          
                                                                           WHERE plano_banco.exercicio = plano_analitica.exercicio                  
                                                                             AND plano_banco.cod_plano = plano_analitica.cod_plano )                
                                                  AND NOT EXISTS ( SELECT 1 FROM contabilidade.configuracao_lancamento_receita                      
                                                                           WHERE configuracao_lancamento_receita.exercicio = plano_conta.exercicio  
                                                                             AND configuracao_lancamento_receita.cod_conta = plano_conta.cod_conta )
                                                  AND NOT EXISTS ( SELECT 1 FROM contabilidade.configuracao_lancamento_credito                      
                                                                           WHERE configuracao_lancamento_credito.exercicio = plano_conta.exercicio  
                                                                             AND configuracao_lancamento_credito.cod_conta = plano_conta.cod_conta )
                                                  AND NOT EXISTS ( SELECT 1 FROM contabilidade.configuracao_lancamento_debito                       
                                                                           WHERE configuracao_lancamento_debito.exercicio = plano_conta.exercicio   
                                                                             AND configuracao_lancamento_debito.cod_conta = plano_conta.cod_conta ) 
                                                  AND plano_conta.cod_estrutural NOT LIKE ''1.1.1%''
                                                  AND plano_conta.cod_estrutural NOT LIKE ''7.2.1.1.1%''
                                                  AND plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.1%''
                                                  AND plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.2%''
                                                  AND plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.3%''
                                                  AND plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.4%''
                                        )
    ';

    EXECUTE stSql;

    -- Deleta as contas Analíticas
    stSql := '
        DELETE 
          FROM contabilidade.plano_analitica 
         WHERE plano_analitica.exercicio = ''' || stExercicio || ''' 
           AND plano_analitica.cod_plano IN (    SELECT plano_analitica.cod_plano
                                                   FROM contabilidade.plano_conta
                                                   JOIN contabilidade.plano_analitica
                                                     ON plano_analitica.cod_conta = plano_conta.cod_conta                                             
                                                    AND plano_analitica.exercicio = plano_conta.exercicio                                             
                                                  WHERE plano_conta.exercicio = ''' || stExercicio || '''
                                                    AND NOT EXISTS ( SELECT 1 FROM contabilidade.conta_debito                                         
                                                                             WHERE conta_debito.exercicio = plano_analitica.exercicio                 
                                                                               AND conta_debito.cod_plano = plano_analitica.cod_plano )               
                                                    AND NOT EXISTS ( SELECT 1 FROM contabilidade.conta_credito                                        
                                                                             WHERE conta_credito.exercicio = plano_analitica.exercicio                
                                                                               AND conta_credito.cod_plano = plano_analitica.cod_plano )              
                                                    AND NOT EXISTS ( SELECT 1 FROM contabilidade.plano_banco                                          
                                                                             WHERE plano_banco.exercicio = plano_analitica.exercicio                  
                                                                               AND plano_banco.cod_plano = plano_analitica.cod_plano )                
                                                    AND NOT EXISTS ( SELECT 1 FROM contabilidade.configuracao_lancamento_receita                      
                                                                             WHERE configuracao_lancamento_receita.exercicio = plano_conta.exercicio  
                                                                               AND configuracao_lancamento_receita.cod_conta = plano_conta.cod_conta )
                                                    AND NOT EXISTS ( SELECT 1 FROM contabilidade.configuracao_lancamento_credito                      
                                                                             WHERE configuracao_lancamento_credito.exercicio = plano_conta.exercicio  
                                                                               AND configuracao_lancamento_credito.cod_conta = plano_conta.cod_conta )
                                                    AND NOT EXISTS ( SELECT 1 FROM contabilidade.configuracao_lancamento_debito                       
                                                                             WHERE configuracao_lancamento_debito.exercicio = plano_conta.exercicio   
                                                                               AND configuracao_lancamento_debito.cod_conta = plano_conta.cod_conta ) 
                                                    AND NOT EXISTS ( SELECT 1 FROM empenho.ordem_pagamento_retencao
                                                                              WHERE ordem_pagamento_retencao.exercicio = plano_analitica.exercicio   
                                                                               AND ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano )
                                                    AND NOT EXISTS ( SELECT 1 FROM patrimonio.grupo_plano_depreciacao
                                                                             WHERE grupo_plano_depreciacao.exercicio = plano_analitica.exercicio   
                                                                               AND grupo_plano_depreciacao.cod_plano = plano_analitica.cod_plano )
                                                    AND NOT EXISTS ( SELECT 1 FROM empenho.responsavel_adiantamento
                                                                             WHERE responsavel_adiantamento.exercicio        = plano_analitica.exercicio   
                                                                               AND responsavel_adiantamento.conta_lancamento = plano_analitica.cod_plano )
                                                    AND NOT EXISTS ( SELECT 1 FROM empenho.contrapartida_responsavel
                                                                             WHERE contrapartida_responsavel.exercicio           = plano_analitica.exercicio   
                                                                               AND contrapartida_responsavel.conta_contrapartida = plano_analitica.cod_plano )
                                                    AND NOT EXISTS ( SELECT 1 FROM tesouraria.recibo_extra
                                                                             WHERE recibo_extra.exercicio = plano_analitica.exercicio   
                                                                               AND recibo_extra.cod_plano = plano_analitica.cod_plano ) 
                                                    AND plano_conta.cod_estrutural NOT LIKE ''1.1.1%''
                                                    AND plano_conta.cod_estrutural NOT LIKE ''7.2.1.1.1%''
                                                    AND plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.1%''
                                                    AND plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.2%''
                                                    AND plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.3%''
                                                    AND plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.4%''
                                        )
    ';

    EXECUTE stSql;

    -- Deleta os vínculos das contas Sintéticas
    stSql := '
        DELETE 
          FROM contabilidade.classificacao_plano
         WHERE classificacao_plano.exercicio = ''' || stExercicio || ''' 
           AND classificacao_plano.cod_conta IN ( SELECT plano_conta.cod_conta
                                                    FROM contabilidade.plano_conta
                                               LEFT JOIN contabilidade.plano_analitica
                                                      ON plano_analitica.cod_conta = plano_conta.cod_conta                                             
                                                     AND plano_analitica.exercicio = plano_conta.exercicio                                             
                                                   WHERE plano_conta.exercicio = ''' || stExercicio || '''
                                                     AND NOT EXISTS ( SELECT 1 FROM contabilidade.conta_debito                                         
                                                                              WHERE conta_debito.exercicio = plano_analitica.exercicio                 
                                                                                AND conta_debito.cod_plano = plano_analitica.cod_plano )               
                                                     AND NOT EXISTS ( SELECT 1 FROM contabilidade.conta_credito                                        
                                                                              WHERE conta_credito.exercicio = plano_analitica.exercicio                
                                                                                AND conta_credito.cod_plano = plano_analitica.cod_plano )              
                                                     AND NOT EXISTS ( SELECT 1 FROM contabilidade.plano_banco                                          
                                                                              WHERE plano_banco.exercicio = plano_analitica.exercicio                  
                                                                                AND plano_banco.cod_plano = plano_analitica.cod_plano )                
                                                     AND NOT EXISTS ( SELECT 1 FROM contabilidade.configuracao_lancamento_receita                      
                                                                              WHERE configuracao_lancamento_receita.exercicio = plano_conta.exercicio  
                                                                                AND configuracao_lancamento_receita.cod_conta = plano_conta.cod_conta )
                                                     AND NOT EXISTS ( SELECT 1 FROM contabilidade.configuracao_lancamento_credito                      
                                                                              WHERE configuracao_lancamento_credito.exercicio = plano_conta.exercicio  
                                                                                AND configuracao_lancamento_credito.cod_conta = plano_conta.cod_conta )
                                                     AND NOT EXISTS ( SELECT 1 FROM contabilidade.configuracao_lancamento_debito                       
                                                                              WHERE configuracao_lancamento_debito.exercicio = plano_conta.exercicio   
                                                                                AND configuracao_lancamento_debito.cod_conta = plano_conta.cod_conta ) 
                                                     AND plano_conta.cod_estrutural NOT LIKE ''1.1.1%''
                                                     AND plano_conta.cod_estrutural NOT LIKE ''7.2.1.1.1%''
                                                     AND plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.1%''
                                                     AND plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.2%''
                                                     AND plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.3%''
                                                     AND plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.4%''
                                        )
    ';

    EXECUTE stSql;

    -- Deleta as contas Sintéticas
    stSql := '
        DELETE 
          FROM contabilidade.plano_conta
         WHERE plano_conta.exercicio = ''' || stExercicio || ''' 
           AND plano_conta.cod_conta IN ( SELECT plano_conta.cod_conta
                                            FROM contabilidade.plano_conta
                                       LEFT JOIN contabilidade.plano_analitica
                                              ON plano_analitica.cod_conta = plano_conta.cod_conta                                             
                                             AND plano_analitica.exercicio = plano_conta.exercicio                                             
                                           WHERE plano_conta.exercicio = ''' || stExercicio || '''
                                             AND NOT EXISTS ( SELECT 1 FROM contabilidade.conta_debito                                         
                                                                      WHERE conta_debito.exercicio = plano_analitica.exercicio                 
                                                                        AND conta_debito.cod_plano = plano_analitica.cod_plano )               
                                             AND NOT EXISTS ( SELECT 1 FROM contabilidade.conta_credito                                        
                                                                      WHERE conta_credito.exercicio = plano_analitica.exercicio                
                                                                        AND conta_credito.cod_plano = plano_analitica.cod_plano )              
                                             AND NOT EXISTS ( SELECT 1 FROM contabilidade.plano_banco                                          
                                                                      WHERE plano_banco.exercicio = plano_analitica.exercicio                  
                                                                        AND plano_banco.cod_plano = plano_analitica.cod_plano )                
                                             AND NOT EXISTS ( SELECT 1 FROM contabilidade.configuracao_lancamento_receita                      
                                                                      WHERE configuracao_lancamento_receita.exercicio = plano_conta.exercicio  
                                                                        AND configuracao_lancamento_receita.cod_conta = plano_conta.cod_conta )
                                             AND NOT EXISTS ( SELECT 1 FROM contabilidade.configuracao_lancamento_credito                      
                                                                      WHERE configuracao_lancamento_credito.exercicio = plano_conta.exercicio  
                                                                        AND configuracao_lancamento_credito.cod_conta = plano_conta.cod_conta )
                                             AND NOT EXISTS ( SELECT 1 FROM contabilidade.configuracao_lancamento_debito                       
                                                                      WHERE configuracao_lancamento_debito.exercicio = plano_conta.exercicio   
                                                                        AND configuracao_lancamento_debito.cod_conta = plano_conta.cod_conta )
                                             AND NOT EXISTS ( SELECT 1 FROM contabilidade.plano_analitica
                                                                      WHERE plano_analitica.cod_conta = plano_conta.cod_conta                                             
                                                                        AND plano_analitica.exercicio = plano_conta.exercicio ) 
                                             AND NOT EXISTS ( SELECT 1 FROM orcamento.receita_credito_tributario
                                                                      WHERE receita_credito_tributario.cod_conta = plano_conta.cod_conta                                             
                                                                        AND receita_credito_tributario.exercicio = plano_conta.exercicio ) 
                                             AND plano_conta.cod_estrutural NOT LIKE ''1.1.1%''
                                             AND plano_conta.cod_estrutural NOT LIKE ''7.2.1.1.1%''
                                             AND plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.1%''
                                             AND plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.2%''
                                             AND plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.3%''
                                             AND plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.4%''
                                        )
    ';

    EXECUTE stSql;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';
