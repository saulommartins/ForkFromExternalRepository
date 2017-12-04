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
* ultimo_atributo_contrato_cargo_cbo
* Data de Criação   : 25/10/2013


* @author Analista      Dagiane
* @author Desenvolvedor Carolina Schwaab Marçal

* @package URBEM
*/

CREATE OR REPLACE FUNCTION ultimo_contrato_servidor_cargo_cbo(VARCHAR, INTEGER) RETURNS SETOF colunasUltimoContratoServidorCargoCbo AS $$
DECLARE
    stEntidade                      ALIAS FOR $1;
    inCodPeriodoMovimentacao        ALIAS FOR $2;
    stSql                           VARCHAR;
    rwCbo                        colunasUltimoContratoServidorCargoCbo%ROWTYPE;
    stTimestampFechamentoPeriodo    VARCHAR;
    reRegistro                      RECORD;
BEGIN
    stTimestampFechamentoPeriodo := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao,stEntidade);

   stSql := '    SELECT cbo_cargo.cod_cbo
                                , cargo.cod_cargo
                    FROM pessoal'|| stEntidade ||'.cargo
              INNER JOIN (  SELECT max(cbo_cargo.timestamp) as timestamp
                                             , cbo_cargo.cod_cargo
                                     FROM pessoal'|| stEntidade ||'.cbo_cargo
                                   WHERE cbo_cargo.timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                               GROUP BY cbo_cargo.cod_cargo
                                ) as max_contrato_servidor_cbo
                          ON max_contrato_servidor_cbo.cod_cargo = cargo.cod_cargo
              INNER JOIN pessoal.cbo_cargo
                          ON cbo_cargo.cod_cargo = max_contrato_servidor_cbo.cod_cargo
                        AND cbo_cargo.timestamp = max_contrato_servidor_cbo.timestamp';

    FOR reRegistro IN EXECUTE stSql LOOP
        rwCbo.cod_cbo    := reRegistro.cod_cbo;
        rwCbo.cod_cargo := reRegistro.cod_cargo;
      
        
        RETURN NEXT rwCbo;
    END LOOP;           
END;
$$ LANGUAGE 'plpgsql';