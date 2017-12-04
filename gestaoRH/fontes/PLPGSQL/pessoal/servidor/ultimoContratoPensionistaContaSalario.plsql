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
* ultimo_contrato_pensionista_conta_salario
* Data de Criação   : 29/06/2009


* @author Analista      Dagiane
* @author Desenvolvedor Rafael Garbin

* @package URBEM
* @subpackage 

* @ignore # 

$Id:$
*/

CREATE OR REPLACE FUNCTION ultimo_contrato_pensionista_conta_salario(VARCHAR, INTEGER) RETURNS SETOF colunasUltimoContratoPensionistaContaSalario AS $$
DECLARE
    stEntidade                      ALIAS FOR $1;
    inCodPeriodoMovimentacao        ALIAS FOR $2;
    stSql                           VARCHAR;
    rwContaSalario                  colunasUltimoContratoPensionistaContaSalario%ROWTYPE;
    stTimestampFechamentoPeriodo    VARCHAR;
    reRegistro                      RECORD;
BEGIN
    stTimestampFechamentoPeriodo := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao,stEntidade);

    stSql := '    SELECT contrato_pensionista_conta_salario.cod_agencia
                       , contrato_pensionista_conta_salario.cod_banco
                       , contrato_pensionista_conta_salario.cod_contrato
                       , contrato_pensionista_conta_salario.nr_conta
                    FROM pessoal'||stEntidade||'.contrato_pensionista_conta_salario
              INNER JOIN (  SELECT contrato_pensionista_conta_salario.cod_contrato
                                 , max(contrato_pensionista_conta_salario.timestamp) as timestamp
                              FROM pessoal'||stEntidade||'.contrato_pensionista_conta_salario
                             WHERE contrato_pensionista_conta_salario.timestamp <= '||quote_literal(stTimestampFechamentoPeriodo)||'
                          GROUP BY contrato_pensionista_conta_salario.cod_contrato) as max_contrato_pensionista_conta_salario
                      ON max_contrato_pensionista_conta_salario.cod_contrato = contrato_pensionista_conta_salario.cod_contrato
                     AND max_contrato_pensionista_conta_salario.timestamp = contrato_pensionista_conta_salario.timestamp';

    FOR reRegistro IN EXECUTE stSql LOOP
        rwContaSalario.cod_contrato := reRegistro.cod_contrato;
        rwContaSalario.cod_agencia  := reRegistro.cod_agencia;
        rwContaSalario.cod_banco    := reRegistro.cod_banco;
        rwContaSalario.nr_conta     := reRegistro.nr_conta;
        
        RETURN NEXT rwContaSalario;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';
