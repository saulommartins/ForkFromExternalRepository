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
*/

CREATE OR REPLACE FUNCTION tesouraria.fn_saldo_conta_tesouraria_relatorio_boletim( VARCHAR, VARCHAR, VARCHAR, BOOLEAN )
RETURNS SETOF RECORD
AS $$
DECLARE
    
    varExercicio            ALIAS FOR $1;
    varDtInicial            ALIAS FOR $2;
    varDtFinal              ALIAS FOR $3;
    bolMovimentacao         ALIAS FOR $4;
    
   datInicial                DATE      := TO_DATE( varDtInicial::text, 'dd/mm/yyyy' );
   datDtFinal                DATE      := TO_DATE( varDtFinal::text  , 'dd/mm/yyyy' );
   timInicial                TIMESTAMP := TO_TIMESTAMP( datInicial::text  , 'YYYY-MM-DD');
   tmpDt                     VARCHAR   := datDtFinal+1;
   timDtFinal                TIMESTAMP := TO_TIMESTAMP( tmpDt, 'YYYY-MM-DD');
   timInicial2               DATE := TO_DATE( datInicial::text, 'YYYY-MM-DD');
   timDtFinal2               DATE := TO_DATE( datDtFinal::text, 'YYYY-MM-DD');

   numValorImplantado        NUMERIC   := 0.00;
   numVlTransferencia        NUMERIC   := 0.00;
   numVlTransferenciaDeb     NUMERIC   := 0.00;
   numVlTransferenciaCred    NUMERIC   := 0.00;
   numVlPago                 NUMERIC   := 0.00;
   numVlPagoEstornado        NUMERIC   := 0.00;
   numValorTesouraria        NUMERIC   := 0.00;
   numVlArrecadacao          NUMERIC   := 0.00;
   numVlArrecadacaoEstornada NUMERIC   := 0.00;

   -- Variaveis calculo Arrecadacao.
   recArrecadacao            RECORD;
   numArrecadVlr             NUMERIC   := 0.00;
   numArrecadVlrDesconto     NUMERIC   := 0.00;
   numArrecadVlrJuros        NUMERIC   := 0.00;
   numArrecadVlrMulta        NUMERIC   := 0.00;

   -- Variaveis Arrecadacao estornada.
   numArrecadVlrEstornada         NUMERIC   := 0.00;
   numArrecadVlrDescontoEstornada NUMERIC   := 0.00;
   numArrecadVlrJurosEstornada    NUMERIC   := 0.00;
   numArrecadVlrMultaEstornada    NUMERIC   := 0.00;

   -- Variaveis Calculo de Valores de multa e juros
   recMultaJuRos             RECORD;
   numVlJuros                NUMERIC   := 0.00;
   numVlMulta                NUMERIC   := 0.00;
   recAux                    RECORD;
   numAux                    NUMERIC   := 0.00;
   varAux                    VARCHAR   := '';
   stSql                     VARCHAR   := '';
   stNomePrefeitura          VARCHAR   := '';

BEGIN
    stSql := 'CREATE TEMPORARY TABLE tmp_saldo_implantado AS (
                    SELECT  cod_plano
                            ,COALESCE(vl_saldo,0.00) as vl_saldo
                    FROM tesouraria.saldo_tesouraria
                    WHERE exercicio = '|| quote_literal(varExercicio) ||'
            )';
    EXECUTE stSql;

   SELECT valor
     INTO stNomePrefeitura
     FROM administracao.configuracao
    WHERE parametro = 'nom_prefeitura'
	  AND exercicio = varExercicio
   ;

   --
   -- Contabiliza valores da movimentaçao se a data final 01/01 e o bolMovimentacao está como true
   --
   IF (datDtFinal != TO_DATE( '01/01/'||varExercicio, 'dd/mm/yyyy') OR bolMovimentacao) THEN
   
      --
      -- Valores da tabela tesouraria.arrecadacao_receita.
      --
	  stSql := ' CREATE TEMPORARY TABLE tmp_arrecadacao_receita AS (
                        SELECT arrecadacao.exercicio
                                 , arrecadacao.cod_arrecadacao
                                 , arrecadacao.timestamp_arrecadacao
                                 , arrecadacao_receita.vl_arrecadacao AS valor
                                 , arrecadacao.cod_plano
                              FROM tesouraria.boletim
                                 , tesouraria.arrecadacao
                                 , tesouraria.arrecadacao_receita
                                 , orcamento.receita
                                 , orcamento.conta_receita
                                 , contabilidade.plano_conta
                                 , contabilidade.plano_analitica ';
                IF varExercicio::integer > 2012 THEN
					stSql := stSql || ', contabilidade.configuracao_lancamento_receita ';
				END IF;
				
                    stSql := stSql || ' WHERE boletim.exercicio   = arrecadacao.exercicio
                               AND boletim.cod_boletim = arrecadacao.cod_boletim
                               AND boletim.cod_entidade= arrecadacao.cod_entidade
                               AND arrecadacao.exercicio             = arrecadacao_receita.exercicio
                               AND arrecadacao.cod_arrecadacao       = arrecadacao_receita.cod_arrecadacao
                               AND arrecadacao.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao

                               AND arrecadacao_receita.exercicio            = receita.exercicio
                               AND arrecadacao_receita.cod_receita          = receita.cod_receita
                               -- Join com orcamento.conta_receita
                               AND receita.exercicio            = conta_receita.exercicio
                               AND receita.cod_conta            = conta_receita.cod_conta ';

                IF varExercicio::integer > 2012 THEN
					stSql := stSql || '
							   AND conta_receita.exercicio = configuracao_lancamento_receita.exercicio
							   AND conta_receita.cod_conta = configuracao_lancamento_receita.cod_conta_receita

							   AND configuracao_lancamento_receita.exercicio = plano_conta.exercicio
 							   AND configuracao_lancamento_receita.cod_conta = plano_conta.cod_conta
							 ';
				ELSE
				
					stSql := stSql || '
                               -- Join com contabilidade.conta_plano
                               AND conta_receita.exercicio            = plano_conta.exercicio
                               AND  (''4.''||conta_receita.cod_estrutural = plano_conta.cod_estrutural
                                     OR conta_receita.cod_estrutural = plano_conta.cod_estrutural)
							 ';
				END IF;
					
					stSql := stSql || '
                               -- Join com contabilidade.conta_analitica
                               AND plano_conta.exercicio           = plano_analitica.exercicio
                               AND plano_conta.cod_conta           = plano_analitica.cod_conta

                               -- Filtros
                               AND boletim.exercicio     = '|| quote_literal(varExercicio) ||'
                               AND boletim.dt_boletim    BETWEEN '''|| datInicial ||''' AND '''|| datDtFinal ||'''
                               AND arrecadacao.devolucao is false ';
                    IF varExercicio::integer > 2012 THEN
						       stSql := stSql || ' AND configuracao_lancamento_receita.estorno is false ';
					END IF;
                                        
                        stSql := stSql || ' UNION ALL

                            SELECT   arrecadacao.exercicio
                                    , arrecadacao.cod_arrecadacao
                                    , arrecadacao.timestamp_arrecadacao
                                    , arrecadacao_receita.vl_arrecadacao AS valor
                                    , arrecadacao.cod_plano                                 
                              FROM tesouraria.boletim
                                 , tesouraria.arrecadacao
                                   INNER JOIN tesouraria.arrecadacao_estornada ON (
                                            tesouraria.arrecadacao_estornada.exercicio              = tesouraria.arrecadacao.exercicio
                                        AND tesouraria.arrecadacao_estornada.cod_arrecadacao        = tesouraria.arrecadacao.cod_arrecadacao
                                        AND tesouraria.arrecadacao_estornada.timestamp_arrecadacao  = tesouraria.arrecadacao.timestamp_arrecadacao
                                        AND tesouraria.arrecadacao.devolucao                        = false
                                   )
                                 , tesouraria.arrecadacao_receita
                                 , tesouraria.arrecadacao_receita_dedutora
                                 , tesouraria.arrecadacao_receita_dedutora_estornada
                                 , orcamento.receita
                                 , orcamento.conta_receita
                                 , contabilidade.plano_conta
                                 , contabilidade.plano_analitica';
                    IF varExercicio::integer > 2012 THEN
						stSql := stSql || ', contabilidade.configuracao_lancamento_receita ';
					END IF;

                        stSql := stSql || ' WHERE boletim.exercicio   = arrecadacao.exercicio
                                 AND boletim.cod_boletim = arrecadacao.cod_boletim
                                 AND boletim.cod_entidade= arrecadacao.cod_entidade
                                 AND arrecadacao.exercicio             = arrecadacao_receita.exercicio
                                 AND arrecadacao.cod_arrecadacao       = arrecadacao_receita.cod_arrecadacao
                                 AND arrecadacao.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao
                            
                                 AND arrecadacao_receita.cod_arrecadacao       = arrecadacao_receita_dedutora.cod_arrecadacao
                                 AND arrecadacao_receita.cod_receita           = arrecadacao_receita_dedutora.cod_receita
                                 AND arrecadacao_receita.exercicio             = arrecadacao_receita_dedutora.exercicio
                                 AND arrecadacao_receita.timestamp_arrecadacao = arrecadacao_receita_dedutora.timestamp_arrecadacao
                            
                                 AND arrecadacao_receita_dedutora.cod_arrecadacao        = arrecadacao_receita_dedutora_estornada.cod_arrecadacao
                                 AND arrecadacao_receita_dedutora.cod_receita            = arrecadacao_receita_dedutora_estornada.cod_receita
                                 AND arrecadacao_receita_dedutora.exercicio              = arrecadacao_receita_dedutora_estornada.exercicio
                                 AND arrecadacao_receita_dedutora.timestamp_arrecadacao  = arrecadacao_receita_dedutora_estornada.timestamp_arrecadacao
                                 AND arrecadacao_receita_dedutora.cod_receita_dedutora   = arrecadacao_receita_dedutora_estornada.cod_receita_dedutora
                            
                                 AND arrecadacao_receita_dedutora_estornada.cod_arrecadacao       = arrecadacao_estornada.cod_arrecadacao
                                 AND arrecadacao_receita_dedutora_estornada.exercicio             = arrecadacao_estornada.exercicio
                                 AND arrecadacao_receita_dedutora_estornada.timestamp_arrecadacao = arrecadacao_estornada.timestamp_arrecadacao
                                 AND arrecadacao_receita_dedutora_estornada.timestamp_estornada   = arrecadacao_estornada.timestamp_estornada
                            
                                 AND arrecadacao_receita_dedutora.exercicio            = receita.exercicio
                                 AND arrecadacao_receita_dedutora.cod_receita          = receita.cod_receita
                                 -- Join com orcamento.conta_receita
                                 AND receita.exercicio            = conta_receita.exercicio
                                 AND receita.cod_conta            = conta_receita.cod_conta ';

                    IF varExercicio::integer > 2012 THEN
							stSql := stSql || '

								 AND conta_receita.exercicio = configuracao_lancamento_receita.exercicio
							     AND conta_receita.cod_conta = configuracao_lancamento_receita.cod_conta_receita

							     AND configuracao_lancamento_receita.exercicio = plano_conta.exercicio
							     AND configuracao_lancamento_receita.cod_conta = plano_conta.cod_conta
							 ';

					ELSE

							stSql := stSql || '

                                 -- Join com contabilidade.conta_plano
                                 AND conta_receita.exercicio            = plano_conta.exercicio
                                 AND  (''4.''||conta_receita.cod_estrutural = plano_conta.cod_estrutural
                                     OR conta_receita.cod_estrutural = plano_conta.cod_estrutural)
							 ';

					END IF;

							stSql := stSql || '
                                 -- Join com contabilidade.conta_analitica
                                 AND plano_conta.exercicio           = plano_analitica.exercicio
                                 AND plano_conta.cod_conta           = plano_analitica.cod_conta
                            
                                 -- Filtros
                                 AND boletim.exercicio     =  '|| quote_literal(varExercicio)||'
                                 AND to_date(arrecadacao_receita_dedutora_estornada.timestamp_dedutora_estornada::text, ''yyyy-mm-dd'') BETWEEN '''|| timInicial2 ||''' AND '''|| timDtFinal2 ||'''
                                 )';

EXECUTE stSql;                            
     
      --
      -- Arrecadacao estornada
      --
      
      --FOR recArrecadacao IN SELECT arrecadacao_estornada.exercicio
      stSql := ' CREATE TEMPORARY TABLE tmp_arrecadacao_estornada AS (
                        SELECT arrecadacao_estornada.exercicio
                                 , arrecadacao_estornada.cod_arrecadacao
                                 , arrecadacao_estornada.timestamp_estornada  
                                 , arrecadacao_estornada_receita.vl_estornado AS valor
                                 , arrecadacao.cod_plano         
                              FROM tesouraria.boletim
                                 , tesouraria.arrecadacao
                                   INNER JOIN tesouraria.arrecadacao_estornada ON (
                                            arrecadacao_estornada.exercicio             = arrecadacao.exercicio
                                        AND arrecadacao_estornada.cod_arrecadacao       = arrecadacao.cod_arrecadacao
                                        AND arrecadacao_estornada.timestamp_arrecadacao = arrecadacao.timestamp_arrecadacao
                                        AND arrecadacao.devolucao                       = false
                                   )
                                   INNER JOIN tesouraria.arrecadacao_estornada_receita ON (
                                          arrecadacao_estornada_receita.exercicio             = arrecadacao_estornada.exercicio
                                      AND arrecadacao_estornada_receita.cod_arrecadacao       = arrecadacao_estornada.cod_arrecadacao
                                      AND arrecadacao_estornada_receita.timestamp_arrecadacao = arrecadacao_estornada.timestamp_arrecadacao
                                      AND arrecadacao_estornada_receita.timestamp_estornada   = arrecadacao_estornada.timestamp_estornada
                                   )
                                 , tesouraria.arrecadacao_receita
                                 , orcamento.receita
                                 , orcamento.conta_receita
                                 , contabilidade.plano_conta
                                 , contabilidade.plano_analitica ';
                IF varExercicio::integer > 2012 THEN
					stSql := stSql || ', contabilidade.configuracao_lancamento_receita ';
				END IF;

                    stSql := stSql ||' WHERE arrecadacao_estornada.cod_arrecadacao IS NOT NULL
                               AND boletim.exercicio   = arrecadacao_estornada.exercicio
                               AND boletim.cod_boletim = arrecadacao_estornada.cod_boletim
                               AND boletim.cod_entidade= arrecadacao_estornada.cod_entidade

                               AND arrecadacao_estornada.exercicio             = arrecadacao_receita.exercicio
                               AND arrecadacao_estornada.cod_arrecadacao       = arrecadacao_receita.cod_arrecadacao
                               AND arrecadacao_estornada.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao

                               AND arrecadacao_receita.exercicio            = receita.exercicio
                               AND arrecadacao_receita.cod_receita          = receita.cod_receita
                               -- Join com orcamento.conta_receita
                               AND receita.exercicio            = conta_receita.exercicio
                               AND receita.cod_conta            = conta_receita.cod_conta ';

                IF varExercicio::integer > 2012 THEN
					stSql := stSql || '
							   AND conta_receita.exercicio = configuracao_lancamento_receita.exercicio
							   AND conta_receita.cod_conta = configuracao_lancamento_receita.cod_conta_receita

							   AND configuracao_lancamento_receita.exercicio = plano_conta.exercicio
						       AND configuracao_lancamento_receita.cod_conta = plano_conta.cod_conta
							 ';

				ELSE

					stSql := stSql || '
                               -- Join com contabilidade.conta_plano
                               AND conta_receita.exercicio            = plano_conta.exercicio
                               AND  (''4.''||conta_receita.cod_estrutural = plano_conta.cod_estrutural
                                     OR conta_receita.cod_estrutural = plano_conta.cod_estrutural)
						 ';
				END IF;

					stSql := stSql || '
                               -- Join com contabilidade.conta_analitica
                               AND plano_conta.exercicio           = plano_analitica.exercicio
                               AND plano_conta.cod_conta           = plano_analitica.cod_conta
                               
                               -- Filtros
                               AND boletim.exercicio     = '|| quote_literal(varExercicio) ||'
                               AND boletim.dt_boletim    BETWEEN '''|| datInicial ||''' AND '''|| datDtFinal ||'''
                               ';
                    IF varExercicio::integer > 2012 THEN
							   stSql := stSql || ' AND configuracao_lancamento_receita.estorno is true ';
					END IF;
                    stSql := stSql || ')';
                                        
EXECUTE stSql;                                        
      
      --
      -- Valores tesouraria.arrecadacao_receita_dedutora.
      --
      
               stSql := ' CREATE TEMPORARY TABLE tmp_arrecadacao_receita_dedutora AS (
                            SELECT DISTINCT
                                   arrecadacao.exercicio
                                 , arrecadacao.cod_arrecadacao
                                 , arrecadacao.timestamp_arrecadacao
                                 , arrecadacao_receita_dedutora.vl_deducao   AS valor
                                 , arrecadacao.cod_plano
                              FROM tesouraria.boletim
                                 , tesouraria.arrecadacao
                                 , tesouraria.arrecadacao_receita
                                 , tesouraria.arrecadacao_receita_dedutora
                                 , orcamento.receita
                                 , orcamento.conta_receita
                                 , contabilidade.plano_conta
                                 , contabilidade.plano_analitica ';
                IF varExercicio::integer > 2012 THEN
					stSql := stSql || ', contabilidade.configuracao_lancamento_receita ';
				END IF;

                    stSql := stSql || ' WHERE boletim.exercicio   = arrecadacao.exercicio
                               AND boletim.cod_boletim = arrecadacao.cod_boletim
                               AND boletim.cod_entidade= arrecadacao.cod_entidade
                               -- Join com arrecadacao_receita
                               AND arrecadacao.exercicio              = arrecadacao_receita.exercicio
                               AND arrecadacao.cod_arrecadacao        = arrecadacao_receita.cod_arrecadacao
                               AND arrecadacao.timestamp_arrecadacao  = arrecadacao_receita.timestamp_arrecadacao
                               -- Join com arrecadacao_receita_dedutora
                               AND arrecadacao_receita.cod_arrecadacao       = arrecadacao_receita_dedutora.cod_arrecadacao
                               AND arrecadacao_receita.cod_receita           = arrecadacao_receita_dedutora.cod_receita
                               AND arrecadacao_receita.exercicio             = arrecadacao_receita_dedutora.exercicio
                               AND arrecadacao_receita.timestamp_arrecadacao = arrecadacao_receita_dedutora.timestamp_arrecadacao
                               -- Join com orcamento.receita
                               AND arrecadacao_receita_dedutora.exercicio             = receita.exercicio
                               AND arrecadacao_receita_dedutora.cod_receita_dedutora  = receita.cod_receita
                               -- Join com orcamento.conta_receita
                               AND receita.exercicio             = conta_receita.exercicio
                               AND receita.cod_conta             = conta_receita.cod_conta
                               -- Join com contabilidade.conta_plano
                               ';
                               
                    IF NOT ( stNomePrefeitura = 'Tribunal de Contas Estado de Mato Grosso do Sul' ) THEN
                    stSql := stSql || ' AND conta_receita.exercicio            = plano_conta.exercicio ';
                    END IF;
                    
                    IF (varExercicio < '2008') THEN
                         stSql := stSql || ' AND ''''4.''''||conta_receita.cod_estrutural = plano_conta.cod_estrutural ';
                    ELSE 
                        IF varExercicio::integer > 2012 THEN
	    				 	stSql := stSql || '
							   AND conta_receita.exercicio = configuracao_lancamento_receita.exercicio
							   AND conta_receita.cod_conta = configuracao_lancamento_receita.cod_conta_receita

							   AND configuracao_lancamento_receita.exercicio = plano_conta.exercicio
							   AND configuracao_lancamento_receita.cod_conta = plano_conta.cod_conta
							 ';

						ELSE
                         	stSql := stSql || ' AND conta_receita.cod_estrutural = plano_conta.cod_estrutural ';
						END IF;
                    END IF;    
               stSql := stSql || '           -- Join com contabilidade.conta_analitica
                               AND plano_conta.exercicio             = plano_analitica.exercicio
                               AND plano_conta.cod_conta             = plano_analitica.cod_conta

                               AND boletim.exercicio     = '''||varExercicio||'''
                               AND boletim.dt_boletim BETWEEN '''||timInicial2||''' AND '''||timDtFinal2||'''
                               ';
                               
            IF varExercicio::integer > 2012 THEN
	    stSql := stSql || ' AND configuracao_lancamento_receita.estorno is true ';
            END IF;
            
            stSql := stSql || ')';
            
EXECUTE stSql;            
      
      -- Devolução de Receitas
      --FOR recArrecadacao IN SELECT arrecadacao.exercicio
      stSql := ' CREATE TEMPORARY TABLE tmp_arrecadacao_devolucao AS (
                        SELECT arrecadacao.exercicio
                                 , arrecadacao.cod_arrecadacao
                                 , arrecadacao.timestamp_arrecadacao
                                 , arrecadacao_receita.vl_arrecadacao   AS valor
                                 , arrecadacao.cod_plano
                              FROM tesouraria.boletim
                                 , tesouraria.arrecadacao
                                 , tesouraria.arrecadacao_receita
                                 , orcamento.receita
                                 , orcamento.conta_receita
                                 , contabilidade.plano_conta
                                 , contabilidade.plano_analitica ';
                IF varExercicio::integer > 2012 THEN
					stSql := stSql || ', contabilidade.configuracao_lancamento_receita ';
				END IF;

                    stSql := stSql || ' WHERE boletim.exercicio   = arrecadacao.exercicio
                               AND boletim.cod_boletim = arrecadacao.cod_boletim
                               AND boletim.cod_entidade= arrecadacao.cod_entidade
                               -- Join com arrecadacao_receita
                               AND arrecadacao.exercicio              = arrecadacao_receita.exercicio
                               AND arrecadacao.cod_arrecadacao        = arrecadacao_receita.cod_arrecadacao
                               AND arrecadacao.timestamp_arrecadacao  = arrecadacao_receita.timestamp_arrecadacao
                               -- Join com orcamento.receita
                               AND arrecadacao_receita.exercicio    = receita.exercicio
                               AND arrecadacao_receita.cod_receita  = receita.cod_receita
                               -- Join com orcamento.conta_receita
                               AND receita.exercicio             = conta_receita.exercicio
                               AND receita.cod_conta             = conta_receita.cod_conta ';

                    IF varExercicio::integer > 2012 THEN
						stSql := stSql || '

							   AND conta_receita.exercicio = configuracao_lancamento_receita.exercicio
							   AND conta_receita.cod_conta = configuracao_lancamento_receita.cod_conta_receita

							   AND configuracao_lancamento_receita.exercicio = plano_conta.exercicio
							   AND configuracao_lancamento_receita.cod_conta = plano_conta.cod_conta
							 ';

					ELSE

						stSql := stSql || '

                               -- Join com contabilidade.conta_plano
                               AND conta_receita.exercicio            = plano_conta.exercicio
                               AND ((''4.''||conta_receita.cod_estrutural = plano_conta.cod_estrutural) OR (conta_receita.cod_estrutural = plano_conta.cod_estrutural))
						 ';

					END IF;

				     stSql := stSql || '
                               -- Join com contabilidade.conta_analitica
                               AND plano_conta.exercicio             = plano_analitica.exercicio
                               AND plano_conta.cod_conta             = plano_analitica.cod_conta
                               
                               AND boletim.exercicio     = '|| quote_literal(varExercicio) ||'
                               AND boletim.dt_boletim    BETWEEN '''|| datInicial ||''' AND '''|| datDtFinal ||'''
                               AND arrecadacao.devolucao = true ';
                        IF varExercicio::integer > 2012 THEN
							   stSql := stSql || ' AND configuracao_lancamento_receita.estorno is true ';
			END IF;
                        
                        stSql := stSql || ' )';
EXECUTE stSql;
      
      --
      -- Calcula Valor Transferencia Debito 
      --
      stSql := ' CREATE TEMPORARY TABLE tmp_transferencia_debito AS (
                    SELECT ( coalesce( sum( transferencia.valor ) , 0.00 ) - coalesce( sum(transferencia_estornada.valor)   ,0.00) ) as valor_trans_debito
                            , transferencia.exercicio
                            , transferencia.cod_plano_debito as cod_plano
                        FROM tesouraria.transferencia
                            LEFT JOIN tesouraria.transferencia_estornada
                                    ON transferencia_estornada.exercicio = transferencia.exercicio
                                AND transferencia_estornada.cod_entidade = transferencia.cod_entidade
                                AND transferencia_estornada.cod_lote = transferencia.cod_lote
                                AND transferencia_estornada.tipo = transferencia.tipo
                                AND transferencia_estornada.timestamp_estornada BETWEEN '|| quote_literal(timInicial) ||' AND '|| quote_literal(timDtFinal) ||'
                    WHERE transferencia.exercicio               = '|| quote_literal(varExercicio) || '
                        AND transferencia.timestamp_transferencia BETWEEN '|| quote_literal(timInicial) ||' AND '|| quote_literal(timDtFinal) ||'
                    GROUP BY transferencia.exercicio
                            , cod_plano
            )';
        EXECUTE stSql;
      
      --
      -- Calcula Valor Transferencia Credito.
      --
      stSql := ' CREATE TEMPORARY TABLE tmp_transferencia_credito AS (
                    SELECT ( coalesce( sum( transferencia.valor ) , 0.00 ) - coalesce( sum(transferencia_estornada.valor)   ,0.00) ) as valor_trans_credito
                            , transferencia.exercicio
                            , transferencia.cod_plano_credito as cod_plano
                        FROM tesouraria.transferencia
                            LEFT JOIN tesouraria.transferencia_estornada
                                    ON transferencia_estornada.exercicio = transferencia.exercicio
                                AND transferencia_estornada.cod_entidade = transferencia.cod_entidade
                                AND transferencia_estornada.cod_lote = transferencia.cod_lote
                                AND transferencia_estornada.tipo = transferencia.tipo
                                AND transferencia_estornada.timestamp_estornada BETWEEN '|| quote_literal(timInicial) ||' AND '|| quote_literal(timDtFinal) ||'
                    WHERE transferencia.exercicio               = '|| quote_literal(varExercicio) || '
                        AND transferencia.timestamp_transferencia BETWEEN '|| quote_literal(timInicial) ||' AND '|| quote_literal(timDtFinal) ||'
                    GROUP BY transferencia.exercicio
                            , cod_plano
            )';
        EXECUTE stSql;
      
      --
      -- pagamento Credito 
      --
      stSql := ' CREATE TEMPORARY TABLE tmp_pagamento_credito AS (
                    SELECT SUM(coalesce(empenho.nota_liquidacao_paga.vl_pago, 0.00)) as valor_pagamento_credito
                            , tesouraria.pagamento.exercicio
                            , tesouraria.pagamento.cod_plano
                            
                        FROM tesouraria.pagamento   
                        , empenho.nota_liquidacao_paga
                    WHERE                                                 
                            tesouraria.pagamento.exercicio    = empenho.nota_liquidacao_paga.exercicio   
                        AND tesouraria.pagamento.cod_entidade = empenho.nota_liquidacao_paga.cod_entidade
                        AND tesouraria.pagamento.timestamp    = empenho.nota_liquidacao_paga.timestamp   
                        AND tesouraria.pagamento.cod_nota     = empenho.nota_liquidacao_paga.cod_nota
                        
                        AND tesouraria.pagamento.timestamp BETWEEN '|| quote_literal(timInicial) ||' AND '|| quote_literal(timDtFinal) ||'
                        AND tesouraria.pagamento.exercicio_boletim = '|| quote_literal(varExercicio) || '
                    GROUP BY pagamento.exercicio
                            , pagamento.cod_plano
            )';
        EXECUTE stSql;
      
      --
      -- pagamento Debito 
      --
    stSql := ' CREATE TEMPORARY TABLE tmp_pagamento_debito AS (  
                    SELECT SUM(coalesce(ENLPA.vl_anulado, 0.00)) as valor_pagamento_debito
                            , P.exercicio
                            , P.cod_plano
                    FROM
                        tesouraria.boletim             as BOLETIM,
                        tesouraria.pagamento_estornado as PE,
                        tesouraria.pagamento           as P,
                        empenho.pagamento_liquidacao as EPL,
                        empenho.pagamento_liquidacao_nota_liquidacao_paga as EPLNLP,
                        empenho.nota_liquidacao_paga                      as ENLP,
                        empenho.nota_liquidacao_paga_anulada              as ENLPA,
                        empenho.nota_liquidacao                           as ENL
                    WHERE
                            BOLETIM.cod_boletim         = PE.cod_boletim
                        AND BOLETIM.exercicio           = PE.exercicio_boletim
                        AND BOLETIM.cod_entidade        = PE.cod_entidade
                        
                        AND PE.cod_nota                 = P.cod_nota
                        AND PE.exercicio                = P.exercicio
                        AND PE.cod_entidade             = P.cod_entidade
                        AND PE.timestamp                = P.timestamp
                        
                        AND PE.cod_nota                 = ENLPA.cod_nota
                        AND PE.exercicio                = ENLPA.exercicio
                        AND PE.cod_entidade             = ENLPA.cod_entidade
                        AND PE.timestamp_anulado        = ENLPA.timestamp_anulada
                        AND PE.timestamp                = ENLPA.timestamp
                        
                        AND ENLPA.cod_nota               = ENLP.cod_nota
                        AND ENLPA.exercicio              = ENLP.exercicio
                        AND ENLPA.cod_entidade           = ENLP.cod_entidade
                        AND ENLPA.timestamp              = ENLP.timestamp
                        
                        AND ENLP.cod_nota               = ENL.cod_nota
                        AND ENLP.exercicio              = ENL.exercicio
                        AND ENLP.cod_entidade           = ENL.cod_entidade
                        
                        AND EPL.cod_ordem               = EPLNLP.cod_ordem
                        AND EPL.exercicio               = EPLNLP.exercicio
                        AND EPL.cod_entidade            = EPLNLP.cod_entidade
                        AND EPL.exercicio_liquidacao    = EPLNLP.exercicio_liquidacao
                        AND EPL.cod_nota                = EPLNLP.cod_nota
                        
                        AND EPLNLP.exercicio_liquidacao = ENLP.exercicio
                        AND EPLNLP.cod_nota             = ENLP.cod_nota
                        AND EPLNLP.cod_entidade         = ENLP.cod_entidade
                        AND EPLNLP.timestamp            = ENLP.timestamp
                        
                        AND to_char(PE.timestamp_anulado,''yyyy'')   = ' ||quote_literal(varExercicio) ||'
                        AND BOLETIM.dt_boletim BETWEEN '|| quote_literal(timInicial2) ||'  AND '|| quote_literal(timDtFinal2) ||'
                    GROUP BY P.exercicio
                            , P.cod_plano
            )';
        EXECUTE stSql;
      
   END IF;
   
    stSql :='            
                SELECT cod_plano
                        ,exercicio
                        , ((valor_saldo_implantado) + (valor_arrecadacao - valor_transferencia - valor_pagamentos) )as valor_saldo_anterior
                FROM (
                -- BUSCAR VALORES DE ARRECADACAO, ARRECADACAO IMPLANTADO, TRANSFERENCIAS E PAGAMENTOS
                        SELECT  cod_plano
                                ,exercicio
                                ,(valor_arrecadado)
                                -
                                (COALESCE(SUM(valor_estornado + valor_receita_dedutora +valor_devolucao),0.00)) as valor_arrecadacao
                                ,ABS(COALESCE(transferencia.valor_transferencia,0.00)) as valor_transferencia
                                ,COALESCE(pagamentos.valor_pagamento,0.00) as valor_pagamentos
                                ,COALESCE(saldo_implantado.vl_saldo,0.00) as valor_saldo_implantado
                        FROM(
                                SELECT 
                                        cod_plano
                                        ,exercicio
                                        ,COALESCE(SUM(tmp_arrecadacao_receita.valor),0.00) as valor_arrecadado
                                        ,COALESCE(SUM(tmp_arrecadacao_estornada.valor),0.00) as valor_estornado
                                        ,COALESCE(SUM(tmp_arrecadacao_receita_dedutora.valor),0.00) as valor_receita_dedutora
                                        ,COALESCE(SUM(tmp_arrecadacao_devolucao.valor),0.00) as valor_devolucao
                                FROM tmp_arrecadacao_receita
                                LEFT JOIN tmp_arrecadacao_estornada
                                        using(cod_plano, exercicio)
                                LEFT JOIN tmp_arrecadacao_receita_dedutora
                                        using(cod_plano, exercicio)
                                LEFT JOIN tmp_arrecadacao_devolucao
                                using(cod_plano, exercicio)
                                GROUP BY cod_plano ,exercicio
                            ) as arrecadacao
                
                        --LEFT JOIN COM TRANSFERENCIAS
                        LEFT JOIN (
                                        SELECT cod_plano
                                                ,exercicio
                                                ,COALESCE( SUM(tmp_transferencia_credito.valor_trans_credito),0.00)
                                                -
                                                COALESCE( SUM(tmp_transferencia_debito.valor_trans_debito),0.00) as valor_transferencia
                                        FROM tmp_transferencia_credito
                                        LEFT JOIN tmp_transferencia_debito
                                        USING(exercicio, cod_plano)
                                        GROUP BY cod_plano ,exercicio
                        ) as transferencia USING(cod_plano,exercicio)
                        
                        --LEFT JOIN COM PAGAMENTOS
                        LEFT JOIN(
                                        SELECT cod_plano
                                                ,exercicio
                                                ,COALESCE( SUM(tmp_pagamento_credito.valor_pagamento_credito),0.00)
                                                -
                                                COALESCE( SUM(tmp_pagamento_debito.valor_pagamento_debito),0.00) as valor_pagamento
                                        FROM tmp_pagamento_credito
                                        LEFT JOIN tmp_pagamento_debito
                                        USING(exercicio, cod_plano)
                                        GROUP BY cod_plano ,exercicio
                        ) as pagamentos USING(cod_plano,exercicio)		
                        --JOIN COM SALDO_IMPLANTADO
                        JOIN ( 		
                                        SELECT  cod_plano
                                                ,SUM(vl_saldo) as vl_saldo
                                        FROM tmp_saldo_implantado
                                        GROUP BY cod_plano
                        ) as saldo_implantado USING(cod_plano)
                           
                        GROUP BY cod_plano
                                 , exercicio
                                 , valor_arrecadado
                                 , transferencia.valor_transferencia
                                 , pagamentos.valor_pagamento
                                 , saldo_implantado.vl_saldo
                ) as relatorio
                GROUP BY cod_plano
                        , exercicio 
                        , valor_arrecadacao
                        , valor_transferencia
                        , valor_pagamentos
                        , valor_saldo_implantado
            ';   
   
FOR recArrecadacao IN EXECUTE stSql
LOOP
    RETURN next recArrecadacao;
END LOOP;   


DROP TABLE tmp_arrecadacao_receita;
DROP TABLE tmp_arrecadacao_estornada;
DROP TABLE tmp_arrecadacao_receita_dedutora;
DROP TABLE tmp_arrecadacao_devolucao;
DROP TABLE tmp_transferencia_debito;
DROP TABLE tmp_transferencia_credito;
DROP TABLE tmp_pagamento_credito;
DROP TABLE tmp_pagamento_debito;
DROP TABLE tmp_saldo_implantado;
  

END;

$$ language 'plpgsql';
