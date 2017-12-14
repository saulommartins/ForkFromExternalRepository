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
* ultimo_contrato_servidor_conta_salario
* Data de Criação   : 29/06/2009


* @author Analista      Dagiane
* @author Desenvolvedor Rafael Garbin

* @package URBEM
* @subpackage 

* @ignore # 

$Id:$
*/

CREATE OR REPLACE FUNCTION ultimo_contrato_servidor_conta_salario(VARCHAR, INTEGER) RETURNS SETOF colunasUltimoContratoServidorContaSalario AS $$
DECLARE
    stEntidade                      ALIAS FOR $1;
    inCodPeriodoMovimentacao        ALIAS FOR $2;
    stSql                           VARCHAR;
    rwContaSalario                  colunasUltimoContratoServidorContaSalario%ROWTYPE;
    stTimestampFechamentoPeriodo    VARCHAR;
    reRegistro                      RECORD;
BEGIN
    stTimestampFechamentoPeriodo := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao,stEntidade);

    stSql := '    SELECT contrato_servidor_conta_salario_historico.cod_agencia
                       , contrato_servidor_conta_salario_historico.cod_banco
                       , contrato_servidor_conta_salario_historico.cod_contrato
                       , contrato_servidor_conta_salario_historico.nr_conta
                    FROM pessoal'||stEntidade||'.contrato_servidor_conta_salario_historico
              INNER JOIN (  SELECT contrato_servidor_conta_salario_historico.cod_contrato
                                 , max(contrato_servidor_conta_salario_historico.timestamp) as timestamp
                              FROM pessoal'||stEntidade||'.contrato_servidor_conta_salario_historico
                             WHERE contrato_servidor_conta_salario_historico.timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                          GROUP BY contrato_servidor_conta_salario_historico.cod_contrato) as max_contrato_servidor_conta_salario_historico
                      ON max_contrato_servidor_conta_salario_historico.cod_contrato = contrato_servidor_conta_salario_historico.cod_contrato
                     AND max_contrato_servidor_conta_salario_historico.timestamp = contrato_servidor_conta_salario_historico.timestamp
                   WHERE EXISTS (   SELECT 1
                                      FROM pessoal'||stEntidade||'.contrato_servidor_forma_pagamento
                                INNER JOIN (    SELECT contrato_servidor_forma_pagamento.cod_contrato
                                                     , max(timestamp) as timestamp
                                                  FROM pessoal'||stEntidade||'.contrato_servidor_forma_pagamento
                                                 WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                              GROUP BY contrato_servidor_forma_pagamento.cod_contrato
                                           ) as max_contrato_servidor_forma_pagamento
                                        ON contrato_servidor_forma_pagamento.cod_contrato = max_contrato_servidor_forma_pagamento.cod_contrato
                                       AND contrato_servidor_forma_pagamento.timestamp = max_contrato_servidor_forma_pagamento.timestamp
                                     WHERE contrato_servidor_forma_pagamento.cod_forma_pagamento = 3 --Credito em conta
                                       AND contrato_servidor_forma_pagamento.cod_contrato = contrato_servidor_conta_salario_historico.cod_contrato
                                )';

    FOR reRegistro IN EXECUTE stSql LOOP
        rwContaSalario.cod_contrato := reRegistro.cod_contrato;
        rwContaSalario.cod_agencia  := reRegistro.cod_agencia;
        rwContaSalario.cod_banco    := reRegistro.cod_banco;
        rwContaSalario.nr_conta     := reRegistro.nr_conta;
        
        RETURN NEXT rwContaSalario;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';
