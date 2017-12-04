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
* ultimo_atributo_contrato_servidor_valor
* Data de Criação   : 29/06/2009


* @author Analista      Dagiane
* @author Desenvolvedor Rafael Garbin

* @package URBEM
* @subpackage 

* @ignore # 

$Id:$
*/

CREATE OR REPLACE FUNCTION ultimo_atributo_contrato_servidor_valor(VARCHAR, INTEGER) RETURNS SETOF colunasUltimoAtributoContratoServidorValor AS $$
DECLARE
    stEntidade                      ALIAS FOR $1;
    inCodPeriodoMovimentacao        ALIAS FOR $2;
    stSql                           VARCHAR;
    rwAtributo                      colunasUltimoAtributoContratoServidorValor%ROWTYPE;
    stTimestampFechamentoPeriodo    VARCHAR;
    reRegistro                      RECORD;
BEGIN
    stTimestampFechamentoPeriodo := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao,stEntidade);

    stSql := '      SELECT atributo_contrato_servidor_valor.cod_contrato
                         , atributo_contrato_servidor_valor.cod_atributo
                         , atributo_contrato_servidor_valor.cod_modulo
                         , atributo_contrato_servidor_valor.cod_cadastro
                         , atributo_contrato_servidor_valor.valor
                      FROM pessoal'|| stEntidade ||'.atributo_contrato_servidor_valor
                INNER JOIN (    SELECT cod_contrato
                                     , max(timestamp) as timestamp
                                  FROM pessoal'|| stEntidade ||'.atributo_contrato_servidor_valor
                                 WHERE atributo_contrato_servidor_valor.timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                              GROUP BY cod_contrato) as max_atributo_contrato_servidor_valor
                        ON atributo_contrato_servidor_valor.cod_contrato = max_atributo_contrato_servidor_valor.cod_contrato
                       AND atributo_contrato_servidor_valor.timestamp = max_atributo_contrato_servidor_valor.timestamp';

    FOR reRegistro IN EXECUTE stSql LOOP
        rwAtributo.cod_contrato  := reRegistro.cod_contrato;
        rwAtributo.cod_atributo  := reRegistro.cod_atributo;
        rwAtributo.cod_modulo    := reRegistro.cod_modulo;
        rwAtributo.cod_cadastro  := reRegistro.cod_cadastro;
        rwAtributo.valor         := reRegistro.valor;
        
        RETURN NEXT rwAtributo;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';
