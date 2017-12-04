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
* ultimo_previdencia_previdencia
* Data de Criação   : 22/01/2010


* @author Analista      Dagiane
* @author Desenvolvedor Eduardo Schitz

* @package URBEM
* @subpackage 

* @ignore # 

$Id:$
*/

CREATE OR REPLACE FUNCTION ultimo_previdencia_previdencia(VARCHAR, INTEGER) RETURNS SETOF colunasUltimoPrevidenciaPrevidencia AS $$
DECLARE
    stEntidade                      ALIAS FOR $1;
    inCodPeriodoMovimentacao        ALIAS FOR $2;
    rwPrevidencia                   colunasUltimoPrevidenciaPrevidencia%ROWTYPE;
    stTimestampFechamentoPeriodo    VARCHAR;
    stSql                           VARCHAR;
    reRegistro                      RECORD;
BEGIN
    stTimestampFechamentoPeriodo := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao,stEntidade);

    stSql := '    SELECT previdencia_previdencia.cod_previdencia
                       , previdencia_previdencia.tipo_previdencia
                       , previdencia_previdencia.descricao
                    FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
              INNER JOIN (  SELECT previdencia_previdencia.cod_previdencia
                                 , max(previdencia_previdencia.timestamp) as timestamp
                              FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                             WHERE previdencia_previdencia.timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                          GROUP BY previdencia_previdencia.cod_previdencia) as max_previdencia_previdencia
                      ON max_previdencia_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                     AND max_previdencia_previdencia.timestamp       = previdencia_previdencia.timestamp';

    FOR reRegistro IN EXECUTE stSql LOOP
        rwPrevidencia.cod_previdencia  := reRegistro.cod_previdencia;
        rwPrevidencia.tipo_previdencia := reRegistro.tipo_previdencia;
        rwPrevidencia.descricao        := reRegistro.descricao;
        
        RETURN NEXT rwPrevidencia;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';
