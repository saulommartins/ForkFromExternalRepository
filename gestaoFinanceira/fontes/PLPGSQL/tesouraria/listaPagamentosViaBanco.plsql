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
* Lucas Stephanou 10/03/2007
*
* $Id: listaPagamentosViaBanco.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-02.04.33
*/

CREATE OR REPLACE FUNCTION tesouraria.fn_listar_pagamentos_via_banco( integer , integer , integer , integer ) RETURNS SETOF RECORD AS $$
DECLARE
    inCodLote           ALIAS FOR $1; --Lote da Arrecadacao - Gestao Tributaria
    inExercicio         ALIAS FOR $2; --Exercicio do Lote da Arrecadacao  - Gestao Tributaria e Exercicio do Boletim - Gestao Financeira
    inCodEntidade       ALIAS FOR $3; --Entidade da Boletim da Tesouraria - Gestao Financeira
    inCodBoletim        ALIAS FOR $4; --Boletim da Tesouraria - Gestao Financeira

    stSql               VARCHAR   := '';
    reRegistro          RECORD;

BEGIN

    -- buscar valores normais do creditos agrupado por receita    
		-- extra
    stSql := ' 
            select lote.cod_lote
                 , lote.data_lote
                 , lote.exercicio
                 , plano_analitica.cod_plano as codigo
                 , plano_banco.cod_plano
   			     , '|| '''extra''' ||'::varchar as tipo
                 , sum(pagamento_calculo.valor) as soma
              from arrecadacao.lote
                   inner join arrecadacao.pagamento_lote
                           on pagamento_lote.cod_lote  = lote.cod_lote
                          and pagamento_lote.exercicio = lote.exercicio
                   inner join arrecadacao.carne
                           on carne.numeracao    = pagamento_lote.numeracao
                          and carne.cod_convenio = pagamento_lote.cod_convenio
                   inner join arrecadacao.parcela
                           on parcela.cod_parcela = carne.cod_parcela
                   inner join arrecadacao.lancamento
                           on lancamento.cod_lancamento = parcela.cod_lancamento
                   inner join arrecadacao.pagamento
                           on pagamento.numeracao               = pagamento_lote.numeracao
                          and pagamento.ocorrencia_pagamento    = pagamento_lote.ocorrencia_pagamento
                          and pagamento.cod_convenio            = pagamento_lote.cod_convenio
                   inner join arrecadacao.pagamento_calculo
                           on pagamento.numeracao               = pagamento_calculo.numeracao
                          and pagamento.ocorrencia_pagamento    = pagamento_calculo.ocorrencia_pagamento
                          and pagamento.cod_convenio            = pagamento_calculo.cod_convenio
                   inner join arrecadacao.calculo
                           on calculo.cod_calculo = pagamento_calculo.cod_calculo
                   inner join monetario.credito
                           on credito.cod_credito = calculo.cod_credito
                          and credito.cod_especie = calculo.cod_especie
                          and credito.cod_genero  = calculo.cod_genero
                          and credito.cod_natureza= calculo.cod_natureza                                              
                          
                   inner join contabilidade.plano_analitica_credito
                           on plano_analitica_credito.cod_credito = credito.cod_credito
                          and plano_analitica_credito.cod_especie = credito.cod_especie
                          and plano_analitica_credito.cod_genero  = credito.cod_genero
                          and plano_analitica_credito.cod_natureza= credito.cod_natureza
                   inner join contabilidade.plano_analitica
                           on plano_analitica.cod_plano = plano_analitica_credito.cod_plano
                          and plano_analitica.exercicio = plano_analitica_credito.exercicio
                  inner join contabilidade.plano_conta
                           on plano_conta.cod_conta = plano_analitica.cod_conta
                          and plano_conta.exercicio = plano_analitica.exercicio                          
                          
                   inner join monetario.credito_conta_corrente
                           on credito_conta_corrente.cod_credito = credito.cod_credito
                          and credito_conta_corrente.cod_especie = credito.cod_especie
                          and credito_conta_corrente.cod_genero  = credito.cod_genero
                          and credito_conta_corrente.cod_natureza= credito.cod_natureza                   
                   inner join monetario.conta_corrente_convenio                          
                           on conta_corrente_convenio.cod_conta_corrente = credito_conta_corrente.cod_conta_corrente
                          and conta_corrente_convenio.cod_agencia = credito_conta_corrente.cod_agencia
                          and conta_corrente_convenio.cod_banco = credito_conta_corrente.cod_banco                          
                          and conta_corrente_convenio.cod_convenio = credito_conta_corrente.cod_convenio                          
                   inner join contabilidade.plano_banco
                           on plano_banco.cod_agencia = conta_corrente_convenio.cod_agencia
                          and plano_banco.cod_banco = conta_corrente_convenio.cod_banco
                          and plano_banco.cod_conta_corrente = conta_corrente_convenio.cod_conta_corrente
                          and plano_banco.exercicio = lote.exercicio

                 , tesouraria.boletim

                        where lote.data_lote <= boletim.dt_boletim
                          and lote.exercicio = boletim.exercicio
                          and lote.cod_lote =  ' || inCodLote || '
                          and lote.exercicio = ' || inExercicio || '
                          and boletim.cod_entidade = ' || inCodEntidade || ' 
                          and boletim.cod_boletim  = ' || inCodBoletim || ' 
                          and boletim.exercicio    = ' || inExercicio || ' 

                     group by lote.cod_lote
                            , lote.data_lote
                            , lote.exercicio
                            , plano_analitica.cod_plano
                            , plano_banco.cod_plano
    ';

    stSql := stSql || ' UNION ';

    stSql := stSql || ' 
            select lote.cod_lote
                 , lote.data_lote
                 , lote.exercicio
                 , receita.cod_receita as codigo
                 , plano_banco.cod_plano
				 , '|| '''orc''' ||'::varchar as tipo
                 , sum(pagamento_calculo.valor) as soma
              from arrecadacao.lote
                  inner join arrecadacao.pagamento_lote
                           on pagamento_lote.cod_lote  = lote.cod_lote
                          and pagamento_lote.exercicio = lote.exercicio
                  inner join arrecadacao.carne
                          on carne.numeracao    = pagamento_lote.numeracao
                         and carne.cod_convenio = pagamento_lote.cod_convenio
                  inner join arrecadacao.parcela
                          on parcela.cod_parcela = carne.cod_parcela
                  inner join arrecadacao.lancamento
                          on lancamento.cod_lancamento = parcela.cod_lancamento
                  inner join arrecadacao.pagamento
                           on pagamento.numeracao               = pagamento_lote.numeracao
                          and pagamento.ocorrencia_pagamento    = pagamento_lote.ocorrencia_pagamento
                          and pagamento.cod_convenio            = pagamento_lote.cod_convenio
                  inner join arrecadacao.pagamento_calculo
                           on pagamento.numeracao               = pagamento_calculo.numeracao
                          and pagamento.ocorrencia_pagamento    = pagamento_calculo.ocorrencia_pagamento
                          and pagamento.cod_convenio            = pagamento_calculo.cod_convenio
                  inner join arrecadacao.calculo
                           on calculo.cod_calculo = pagamento_calculo.cod_calculo
                  inner join monetario.credito
                           on credito.cod_credito = calculo.cod_credito
                          and credito.cod_especie = calculo.cod_especie
                          and credito.cod_genero  = calculo.cod_genero
                          and credito.cod_natureza= calculo.cod_natureza
                  inner join orcamento.receita_credito
                           on receita_credito.cod_credito = credito.cod_credito
                          and receita_credito.cod_especie = credito.cod_especie
                          and receita_credito.cod_genero  = credito.cod_genero
                          and receita_credito.cod_natureza= credito.cod_natureza
                          and receita_credito.divida_ativa  = lancamento.divida
                  inner join orcamento.receita
                           on receita.cod_receita = receita_credito.cod_receita
		                  and receita.exercicio = receita_credito.exercicio
                  inner join orcamento.conta_receita
                           on receita.cod_conta = conta_receita.cod_conta
                          and receita.exercicio = conta_receita.exercicio
                          
                   inner join monetario.credito_conta_corrente
                           on credito_conta_corrente.cod_credito = credito.cod_credito
                          and credito_conta_corrente.cod_especie = credito.cod_especie
                          and credito_conta_corrente.cod_genero  = credito.cod_genero
                          and credito_conta_corrente.cod_natureza= credito.cod_natureza                   
                   inner join monetario.conta_corrente_convenio                          
                           on conta_corrente_convenio.cod_conta_corrente = credito_conta_corrente.cod_conta_corrente
                          and conta_corrente_convenio.cod_agencia = credito_conta_corrente.cod_agencia
                          and conta_corrente_convenio.cod_banco = credito_conta_corrente.cod_banco                          
                          and conta_corrente_convenio.cod_convenio = credito_conta_corrente.cod_convenio                          
                   inner join contabilidade.plano_banco
                           on plano_banco.cod_agencia = conta_corrente_convenio.cod_agencia
                          and plano_banco.cod_banco = conta_corrente_convenio.cod_banco
                          and plano_banco.cod_conta_corrente = conta_corrente_convenio.cod_conta_corrente
                          and plano_banco.exercicio = lote.exercicio                          
                          
                 , tesouraria.boletim

                        where lote.data_lote <= boletim.dt_boletim
                          and lote.exercicio = boletim.exercicio
                          and lote.cod_lote =  ' || inCodLote || '
                          and lote.exercicio = ' || inExercicio || '
                          and boletim.cod_entidade = ' || inCodEntidade || ' 
                          and boletim.cod_boletim  = ' || inCodBoletim || ' 
                          and boletim.exercicio    = ' || inExercicio || ' 

                     group by lote.cod_lote
                            , lote.data_lote
                            , lote.exercicio
                            , receita.cod_receita
                            , plano_banco.cod_plano
    ';

    stSql := stSql || ' UNION ';

    -- buscar valores dos acrescimos do creditos agrupado por receita
    stSql := stSql || ' 
            select lote.cod_lote
                 , lote.data_lote
                 , lote.exercicio
                 , plano_analitica.cod_plano as codigo
                 , plano_banco.cod_plano
			     , '|| '''extra''' ||'::varchar as tipo	
                 , sum(pagamento_acrescimo.valor) as soma
              from arrecadacao.lote
                  inner join arrecadacao.pagamento_lote
                           on pagamento_lote.cod_lote  = lote.cod_lote
                          and pagamento_lote.exercicio = lote.exercicio
                  inner join arrecadacao.carne
                          on carne.numeracao    = pagamento_lote.numeracao
                         and carne.cod_convenio = pagamento_lote.cod_convenio
                  inner join arrecadacao.parcela
                          on parcela.cod_parcela = carne.cod_parcela
                  inner join arrecadacao.lancamento
                          on lancamento.cod_lancamento = parcela.cod_lancamento
                  inner join arrecadacao.pagamento
                           on pagamento.numeracao               = pagamento_lote.numeracao
                          and pagamento.ocorrencia_pagamento    = pagamento_lote.ocorrencia_pagamento
                          and pagamento.cod_convenio            = pagamento_lote.cod_convenio
                  inner join arrecadacao.pagamento_acrescimo
                           on pagamento.numeracao               = pagamento_acrescimo.numeracao
                          and pagamento.ocorrencia_pagamento    = pagamento_acrescimo.ocorrencia_pagamento
                          and pagamento.cod_convenio            = pagamento_acrescimo.cod_convenio
                  inner join arrecadacao.calculo
                           on calculo.cod_calculo = pagamento_acrescimo.cod_calculo
                  inner join monetario.credito_acrescimo
                           on credito_acrescimo.cod_credito = calculo.cod_credito
                          and credito_acrescimo.cod_especie = calculo.cod_especie
                          and credito_acrescimo.cod_genero  = calculo.cod_genero
                          and credito_acrescimo.cod_natureza= calculo.cod_natureza
                          and credito_acrescimo.cod_acrescimo= pagamento_acrescimo.cod_acrescimo 
                          and credito_acrescimo.cod_tipo = pagamento_acrescimo.cod_tipo 
                  inner join contabilidade.plano_analitica_credito_acrescimo
                           on plano_analitica_credito_acrescimo.cod_credito = credito_acrescimo.cod_credito
                          and plano_analitica_credito_acrescimo.cod_especie = credito_acrescimo.cod_especie
                          and plano_analitica_credito_acrescimo.cod_genero  = credito_acrescimo.cod_genero
                          and plano_analitica_credito_acrescimo.cod_natureza= credito_acrescimo.cod_natureza
                          and plano_analitica_credito_acrescimo.cod_acrescimo = credito_acrescimo.cod_acrescimo 
                          and plano_analitica_credito_acrescimo.cod_tipo = credito_acrescimo.cod_tipo 
                  inner join contabilidade.plano_analitica
                           on plano_analitica.cod_plano = plano_analitica_credito_acrescimo.cod_plano
                          and plano_analitica.exercicio = plano_analitica_credito_acrescimo.exercicio
                          
                   inner join monetario.credito_conta_corrente
                           on credito_conta_corrente.cod_credito = credito_acrescimo.cod_credito
                          and credito_conta_corrente.cod_especie = credito_acrescimo.cod_especie
                          and credito_conta_corrente.cod_genero  = credito_acrescimo.cod_genero
                          and credito_conta_corrente.cod_natureza= credito_acrescimo.cod_natureza                   
                   inner join monetario.conta_corrente_convenio                          
                           on conta_corrente_convenio.cod_conta_corrente = credito_conta_corrente.cod_conta_corrente
                          and conta_corrente_convenio.cod_agencia = credito_conta_corrente.cod_agencia
                          and conta_corrente_convenio.cod_banco = credito_conta_corrente.cod_banco                          
                          and conta_corrente_convenio.cod_convenio = credito_conta_corrente.cod_convenio                          
                   inner join contabilidade.plano_banco
                           on plano_banco.cod_agencia = conta_corrente_convenio.cod_agencia
                          and plano_banco.cod_banco = conta_corrente_convenio.cod_banco
                          and plano_banco.cod_conta_corrente = conta_corrente_convenio.cod_conta_corrente
                          and plano_banco.exercicio = lote.exercicio                          
                       
                 , tesouraria.boletim

                        where lote.data_lote <= boletim.dt_boletim
                          and lote.exercicio = boletim.exercicio
                          and lote.cod_lote =  ' || inCodLote || '
                          and lote.exercicio = ' || inExercicio || '
                          and boletim.cod_entidade = ' || inCodEntidade || ' 
                          and boletim.cod_boletim  = ' || inCodBoletim || ' 
                          and boletim.exercicio    = ' || inExercicio || ' 

                     group by lote.cod_lote
                            , lote.data_lote
                            , lote.exercicio
                            , plano_analitica.cod_plano
                            , plano_banco.cod_plano
    ';

    stSql := stSql || ' UNION ';

    stSql := stSql || ' 
            select lote.cod_lote
                 , lote.data_lote
                 , lote.exercicio
                 , receita.cod_receita as codigo
                 , plano_banco.cod_plano
                 , '|| '''orc''' ||'::varchar as tipo
                 , sum(pagamento_acrescimo.valor) as soma
              from arrecadacao.lote
                  inner join arrecadacao.pagamento_lote
                           on pagamento_lote.cod_lote  = lote.cod_lote
                          and pagamento_lote.exercicio = lote.exercicio
                  inner join arrecadacao.pagamento
                           on pagamento.numeracao               = pagamento_lote.numeracao
                          and pagamento.ocorrencia_pagamento    = pagamento_lote.ocorrencia_pagamento
                          and pagamento.cod_convenio            = pagamento_lote.cod_convenio
                  inner join arrecadacao.pagamento_acrescimo
                           on pagamento.numeracao               = pagamento_acrescimo.numeracao
                          and pagamento.ocorrencia_pagamento    = pagamento_acrescimo.ocorrencia_pagamento
                          and pagamento.cod_convenio            = pagamento_acrescimo.cod_convenio
                  inner join arrecadacao.calculo
                           on calculo.cod_calculo = pagamento_acrescimo.cod_calculo
                  inner join monetario.credito_acrescimo
                           on credito_acrescimo.cod_credito = calculo.cod_credito
                          and credito_acrescimo.cod_especie = calculo.cod_especie
                          and credito_acrescimo.cod_genero  = calculo.cod_genero
                          and credito_acrescimo.cod_natureza= calculo.cod_natureza
                          and credito_acrescimo.cod_acrescimo= pagamento_acrescimo.cod_acrescimo 
                          and credito_acrescimo.cod_tipo = pagamento_acrescimo.cod_tipo 
                  inner join orcamento.receita_credito_acrescimo
                           on receita_credito_acrescimo.cod_credito = credito_acrescimo.cod_credito
                          and receita_credito_acrescimo.cod_especie = credito_acrescimo.cod_especie
                          and receita_credito_acrescimo.cod_genero  = credito_acrescimo.cod_genero
                          and receita_credito_acrescimo.cod_natureza= credito_acrescimo.cod_natureza
                          and receita_credito_acrescimo.cod_acrescimo= credito_acrescimo.cod_acrescimo
                          and receita_credito_acrescimo.cod_tipo= credito_acrescimo.cod_tipo
                   inner join orcamento.receita
                           on receita.cod_receita = receita_credito_acrescimo.cod_receita
                          and receita.exercicio = receita_credito_acrescimo.exercicio
                  inner join orcamento.conta_receita
                           on receita.cod_conta = conta_receita.cod_conta
                          and receita.exercicio = conta_receita.exercicio

                   inner join monetario.credito_conta_corrente
                           on credito_conta_corrente.cod_credito = credito_acrescimo.cod_credito
                          and credito_conta_corrente.cod_especie = credito_acrescimo.cod_especie
                          and credito_conta_corrente.cod_genero  = credito_acrescimo.cod_genero
                          and credito_conta_corrente.cod_natureza= credito_acrescimo.cod_natureza                   
                   inner join monetario.conta_corrente_convenio                          
                           on conta_corrente_convenio.cod_conta_corrente = credito_conta_corrente.cod_conta_corrente
                          and conta_corrente_convenio.cod_agencia = credito_conta_corrente.cod_agencia
                          and conta_corrente_convenio.cod_banco = credito_conta_corrente.cod_banco                          
                          and conta_corrente_convenio.cod_convenio = credito_conta_corrente.cod_convenio                          
                   inner join contabilidade.plano_banco
                           on plano_banco.cod_agencia = conta_corrente_convenio.cod_agencia
                          and plano_banco.cod_banco = conta_corrente_convenio.cod_banco
                          and plano_banco.cod_conta_corrente = conta_corrente_convenio.cod_conta_corrente
                          and plano_banco.exercicio = lote.exercicio                          

                 , tesouraria.boletim

                        where lote.data_lote <= boletim.dt_boletim
                          and lote.exercicio = boletim.exercicio
                          and lote.cod_lote =  ' || inCodLote || '
                          and lote.exercicio = ' || inExercicio || '
                          and boletim.cod_entidade = ' || inCodEntidade || ' 
                          and boletim.cod_boletim  = ' || inCodBoletim || ' 
                          and boletim.exercicio    = ' || inExercicio || ' 

                     group by lote.cod_lote
                            , lote.data_lote
                            , lote.exercicio
                            , receita.cod_receita
                            , plano_banco.cod_plano
    ';

    stSql := stSql || ' UNION ';


    -- buscar valores de diff de pagamento
    stSql := stSql || '
            select lote.cod_lote
                 , lote.data_lote
                 , lote.exercicio
                 , plano_analitica.cod_plano as codigo
                 , plano_banco.cod_plano
 		         , '|| '''extra''' ||'::varchar as tipo
                 , sum(pagamento_diferenca.valor) as soma
              from arrecadacao.lote
                  inner join arrecadacao.pagamento_lote
                           on pagamento_lote.cod_lote  = lote.cod_lote
                          and pagamento_lote.exercicio = lote.exercicio
                  inner join arrecadacao.carne
                          on carne.numeracao    = pagamento_lote.numeracao
                         and carne.cod_convenio = pagamento_lote.cod_convenio
                  inner join arrecadacao.parcela
                          on parcela.cod_parcela = carne.cod_parcela
                  inner join arrecadacao.lancamento
                          on lancamento.cod_lancamento = parcela.cod_lancamento
                  inner join arrecadacao.pagamento
                           on pagamento.numeracao               = pagamento_lote.numeracao
                          and pagamento.ocorrencia_pagamento    = pagamento_lote.ocorrencia_pagamento
                          and pagamento.cod_convenio            = pagamento_lote.cod_convenio
                  inner join arrecadacao.pagamento_diferenca
                           on pagamento.numeracao               = pagamento_diferenca.numeracao
                          and pagamento.ocorrencia_pagamento    = pagamento_diferenca.ocorrencia_pagamento
                          and pagamento.cod_convenio            = pagamento_diferenca.cod_convenio
                  inner join arrecadacao.calculo
                           on calculo.cod_calculo = pagamento_diferenca.cod_calculo
                  inner join monetario.credito
                           on credito.cod_credito = calculo.cod_credito
                          and credito.cod_especie = calculo.cod_especie
                          and credito.cod_genero  = calculo.cod_genero
                          and credito.cod_natureza= calculo.cod_natureza
                  inner join contabilidade.plano_analitica_credito
                           on plano_analitica_credito.cod_credito = credito.cod_credito
                          and plano_analitica_credito.cod_especie = credito.cod_especie
                          and plano_analitica_credito.cod_genero  = credito.cod_genero
                          and plano_analitica_credito.cod_natureza= credito.cod_natureza
                  inner join contabilidade.plano_analitica
                           on plano_analitica.cod_plano = plano_analitica_credito.cod_plano
                          and plano_analitica.exercicio = plano_analitica_credito.exercicio
                  inner join contabilidade.plano_conta
                           on plano_conta.cod_conta = plano_analitica.cod_conta
                          and plano_conta.exercicio = plano_analitica.exercicio
                          
                   inner join monetario.credito_conta_corrente
                           on credito_conta_corrente.cod_credito = credito.cod_credito
                          and credito_conta_corrente.cod_especie = credito.cod_especie
                          and credito_conta_corrente.cod_genero  = credito.cod_genero
                          and credito_conta_corrente.cod_natureza= credito.cod_natureza                   
                   inner join monetario.conta_corrente_convenio                          
                           on conta_corrente_convenio.cod_conta_corrente = credito_conta_corrente.cod_conta_corrente
                          and conta_corrente_convenio.cod_agencia = credito_conta_corrente.cod_agencia
                          and conta_corrente_convenio.cod_banco = credito_conta_corrente.cod_banco                          
                          and conta_corrente_convenio.cod_convenio = credito_conta_corrente.cod_convenio                          
                   inner join contabilidade.plano_banco
                           on plano_banco.cod_agencia = conta_corrente_convenio.cod_agencia
                          and plano_banco.cod_banco = conta_corrente_convenio.cod_banco
                          and plano_banco.cod_conta_corrente = conta_corrente_convenio.cod_conta_corrente
                          and plano_banco.exercicio = lote.exercicio                          
                                                    
                 , tesouraria.boletim

                        where lote.data_lote <= boletim.dt_boletim
                          and lote.exercicio = boletim.exercicio
                          and lote.cod_lote =  ' || inCodLote || '
                          and lote.exercicio = ' || inExercicio || '
                          and boletim.cod_entidade = ' || inCodEntidade || ' 
                          and boletim.cod_boletim  = ' || inCodBoletim || ' 
                          and boletim.exercicio    = ' || inExercicio || ' 

                     group by lote.cod_lote
                            , lote.data_lote
                            , lote.exercicio
                            , plano_analitica.cod_plano
                            , plano_banco.cod_plano
    ';

    stSql := stSql || ' UNION ';

    stSql := stSql || '
            select lote.cod_lote
                 , lote.data_lote
                 , lote.exercicio
                 , receita.cod_receita as codigo
                 , plano_banco.cod_plano
				  , '|| '''orc''' ||'::varchar as tipo
                 , sum(pagamento_diferenca.valor) as soma
              from arrecadacao.lote
                  inner join arrecadacao.pagamento_lote
                           on pagamento_lote.cod_lote  = lote.cod_lote
                          and pagamento_lote.exercicio = lote.exercicio
                  inner join arrecadacao.carne
                          on carne.numeracao    = pagamento_lote.numeracao
                         and carne.cod_convenio = pagamento_lote.cod_convenio
                  inner join arrecadacao.parcela
                          on parcela.cod_parcela = carne.cod_parcela
                  inner join arrecadacao.lancamento
                          on lancamento.cod_lancamento = parcela.cod_lancamento
                  inner join arrecadacao.pagamento
                           on pagamento.numeracao               = pagamento_lote.numeracao
                          and pagamento.ocorrencia_pagamento    = pagamento_lote.ocorrencia_pagamento
                          and pagamento.cod_convenio            = pagamento_lote.cod_convenio
                  inner join arrecadacao.pagamento_diferenca
                           on pagamento.numeracao               = pagamento_diferenca.numeracao
                          and pagamento.ocorrencia_pagamento    = pagamento_diferenca.ocorrencia_pagamento
                          and pagamento.cod_convenio            = pagamento_diferenca.cod_convenio
                  inner join arrecadacao.calculo
                           on calculo.cod_calculo = pagamento_diferenca.cod_calculo
                  inner join monetario.credito
                           on credito.cod_credito = calculo.cod_credito
                          and credito.cod_especie = calculo.cod_especie
                          and credito.cod_genero  = calculo.cod_genero
                          and credito.cod_natureza= calculo.cod_natureza
             	     inner join orcamento.receita_credito
                           on receita_credito.cod_credito = credito.cod_credito
                          and receita_credito.cod_especie = credito.cod_especie
                          and receita_credito.cod_genero  = credito.cod_genero
                          and receita_credito.cod_natureza= credito.cod_natureza
                          and receita_credito.divida_ativa  = lancamento.divida
                   inner join orcamento.receita
                           on receita.cod_receita = receita_credito.cod_receita
  		                  and receita.exercicio = receita_credito.exercicio
                  inner join orcamento.conta_receita
                           on receita.cod_conta = conta_receita.cod_conta
                          and receita.exercicio = conta_receita.exercicio

                   inner join monetario.credito_conta_corrente
                           on credito_conta_corrente.cod_credito = credito.cod_credito
                          and credito_conta_corrente.cod_especie = credito.cod_especie
                          and credito_conta_corrente.cod_genero  = credito.cod_genero
                          and credito_conta_corrente.cod_natureza= credito.cod_natureza                   
                   inner join monetario.conta_corrente_convenio                          
                           on conta_corrente_convenio.cod_conta_corrente = credito_conta_corrente.cod_conta_corrente
                          and conta_corrente_convenio.cod_agencia = credito_conta_corrente.cod_agencia
                          and conta_corrente_convenio.cod_banco = credito_conta_corrente.cod_banco                          
                          and conta_corrente_convenio.cod_convenio = credito_conta_corrente.cod_convenio                          
                   inner join contabilidade.plano_banco
                           on plano_banco.cod_agencia = conta_corrente_convenio.cod_agencia
                          and plano_banco.cod_banco = conta_corrente_convenio.cod_banco
                          and plano_banco.cod_conta_corrente = conta_corrente_convenio.cod_conta_corrente
                          and plano_banco.exercicio = lote.exercicio                          

                 , tesouraria.boletim

                        where lote.data_lote <= boletim.dt_boletim
                          and lote.exercicio = boletim.exercicio
                          and lote.cod_lote =  ' || inCodLote || '
                          and lote.exercicio = ' || inExercicio || '
                          and boletim.cod_entidade = ' || inCodEntidade || ' 
                          and boletim.cod_boletim  = ' || inCodBoletim || ' 
                          and boletim.exercicio    = ' || inExercicio || ' 

                     group by lote.cod_lote
                            , lote.data_lote
                            , lote.exercicio
                            , receita.cod_receita
                            , plano_banco.cod_plano
    ';



    -- agrupar consultas
    stSql := ' select cod_lote, data_lote, exercicio , codigo, cod_plano , tipo, soma
                from ( ' || stSql || ' ) as tabela';
                --group by cod_lote , data_lote, exercicio , codigo , cod_plano, tipo 

    FOR reRegistro IN EXECUTE stSql LOOP
        return next reRegistro;
    END LOOP;

END;

$$ language 'plpgsql';
