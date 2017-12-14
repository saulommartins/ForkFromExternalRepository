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
* ultimo_contrato_pensionista_previdencia
* Data de Criação   : 25/02/2010


* @author Analista      Dagiane
* @author Desenvolvedor Eduardo Schitz

* @package URBEM
* @subpackage 

* @ignore # 

$Id:$
*/

CREATE OR REPLACE FUNCTION ultimo_contrato_pensionista_previdencia(VARCHAR, INTEGER) RETURNS SETOF colunasUltimoContratoPensionistaPrevidencia AS $$
DECLARE
    stEntidade                      ALIAS FOR $1;
    inCodPeriodoMovimentacao        ALIAS FOR $2;
    rwPensionista                   colunasUltimoContratoPensionistaPrevidencia%ROWTYPE;
    stTimestampFechamentoPeriodo    VARCHAR;
    stSql                           VARCHAR;
    reRegistro                      RECORD;
BEGIN
    stTimestampFechamentoPeriodo := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao,stEntidade);

    stSql := '    SELECT contrato_pensionista_previdencia.cod_contrato
                       , contrato_pensionista_previdencia.cod_previdencia
                    FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
              INNER JOIN (  SELECT contrato_pensionista_previdencia.cod_contrato
                                 , contrato_pensionista_previdencia.cod_previdencia
                                 , max(contrato_pensionista_previdencia.timestamp) as timestamp
                              FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
                             WHERE contrato_pensionista_previdencia.timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                          GROUP BY contrato_pensionista_previdencia.cod_contrato
                                 , contrato_pensionista_previdencia.cod_previdencia) as max_contrato_pensionista_previdencia
                      ON max_contrato_pensionista_previdencia.cod_contrato    = contrato_pensionista_previdencia.cod_contrato
                     AND max_contrato_pensionista_previdencia.cod_previdencia = contrato_pensionista_previdencia.cod_previdencia
                     AND max_contrato_pensionista_previdencia.timestamp       = contrato_pensionista_previdencia.timestamp';

    FOR reRegistro IN EXECUTE stSql LOOP
        rwPensionista.cod_contrato    := reRegistro.cod_contrato;
        rwPensionista.cod_previdencia := reRegistro.cod_previdencia;
        
        RETURN NEXT rwPensionista;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';
