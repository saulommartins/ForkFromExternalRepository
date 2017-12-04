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
* ultimo_contrato_servidor_padrao
* Data de Criação   : 29/06/2009


* @author Analista      Dagiane
* @author Desenvolvedor Rafael Garbin

* @package URBEM
* @subpackage 

* @ignore # 

$Id:$
*/

CREATE OR REPLACE FUNCTION ultimo_contrato_servidor_padrao(VARCHAR, INTEGER) RETURNS SETOF colunasUltimoContratoServidorPadrao AS $$
DECLARE
    stEntidade                      ALIAS FOR $1;
    inCodPeriodoMovimentacao        ALIAS FOR $2;
    stSql                           VARCHAR;
    rwPadrao                        colunasUltimoContratoServidorPadrao%ROWTYPE;
    stTimestampFechamentoPeriodo    VARCHAR;
    reRegistro                      RECORD;
BEGIN
    stTimestampFechamentoPeriodo := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao,stEntidade);

    stSql := '    SELECT contrato_servidor_padrao.cod_contrato
                       , contrato_servidor_padrao.cod_padrao
                       , padrao_padrao.valor
                       , padrao.descricao
                    FROM pessoal'|| stEntidade ||'.contrato_servidor_padrao
              INNER JOIN (  SELECT contrato_servidor_padrao.cod_contrato
                                 , max(contrato_servidor_padrao.timestamp) as timestamp
                              FROM pessoal'|| stEntidade ||'.contrato_servidor_padrao
                             WHERE contrato_servidor_padrao.timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                          GROUP BY contrato_servidor_padrao.cod_contrato) as max_contrato_servidor_padrao
                      ON max_contrato_servidor_padrao.cod_contrato = contrato_servidor_padrao.cod_contrato
                     AND max_contrato_servidor_padrao.timestamp = contrato_servidor_padrao.timestamp
              INNER JOIN folhapagamento'|| stEntidade ||'.padrao
                      ON padrao.cod_padrao = contrato_servidor_padrao.cod_padrao
              INNER JOIN folhapagamento'|| stEntidade ||'.padrao_padrao
                      ON padrao_padrao.cod_padrao = padrao.cod_padrao
              INNER JOIN (  SELECT padrao_padrao.cod_padrao
                                 , max(padrao_padrao.timestamp) as timestamp
                              FROM folhapagamento'|| stEntidade ||'.padrao_padrao
                             WHERE padrao_padrao.timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                          GROUP BY padrao_padrao.cod_padrao) as max_padrao_padrao
                      ON max_padrao_padrao.cod_padrao = padrao_padrao.cod_padrao
                     AND max_padrao_padrao.timestamp = padrao_padrao.timestamp';

    FOR reRegistro IN EXECUTE stSql LOOP
        rwPadrao.cod_contrato := reRegistro.cod_contrato;
        rwPadrao.cod_padrao   := reRegistro.cod_padrao;
        rwPadrao.valor        := reRegistro.valor;
        rwPadrao.descricao    := reRegistro.descricao;
        
        RETURN NEXT rwPadrao;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';
