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
* ultimo_contrato_servidor_previdencia
* Data de Criação   : 25/02/2010


* @author Analista      Dagiane
* @author Desenvolvedor Eduardo Schitz

* @package URBEM
* @subpackage 

* @ignore # 

$Id:$
*/

CREATE OR REPLACE FUNCTION ultimo_contrato_servidor_previdencia(VARCHAR, INTEGER) RETURNS SETOF colunasUltimoContratoServidorPrevidencia AS $$
DECLARE
    stEntidade                      ALIAS FOR $1;
    inCodPeriodoMovimentacao        ALIAS FOR $2;
    rwServidor                      colunasUltimoContratoServidorPrevidencia%ROWTYPE;
    stTimestampFechamentoPeriodo    VARCHAR;
    stSql                           VARCHAR;
    reRegistro                      RECORD;
BEGIN
    stTimestampFechamentoPeriodo := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao,stEntidade);

    stSql := '    SELECT contrato_servidor_previdencia.cod_contrato
                       , contrato_servidor_previdencia.cod_previdencia
                       , contrato_servidor_previdencia.bo_excluido
                    FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
              INNER JOIN (  SELECT contrato_servidor_previdencia.cod_contrato
                                 , contrato_servidor_previdencia.cod_previdencia
                                 , max(contrato_servidor_previdencia.timestamp) as timestamp
                              FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                             WHERE contrato_servidor_previdencia.timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                          GROUP BY contrato_servidor_previdencia.cod_contrato
                                 , contrato_servidor_previdencia.cod_previdencia) as max_contrato_servidor_previdencia
                      ON max_contrato_servidor_previdencia.cod_contrato    = contrato_servidor_previdencia.cod_contrato
                     AND max_contrato_servidor_previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia
                     AND max_contrato_servidor_previdencia.timestamp       = contrato_servidor_previdencia.timestamp';

    FOR reRegistro IN EXECUTE stSql LOOP
        rwServidor.cod_contrato    := reRegistro.cod_contrato;
        rwServidor.cod_previdencia := reRegistro.cod_previdencia;
        rwServidor.bo_excluido     := reRegistro.bo_excluido;
        
        RETURN NEXT rwServidor;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';
