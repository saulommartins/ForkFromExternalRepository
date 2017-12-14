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
* ultimo_contrato_servidor_nomeacao_posse
* Data de Criação   : 29/06/2009


* @author Analista      Dagiane
* @author Desenvolvedor Rafael Garbin

* @package URBEM
* @subpackage 

* @ignore # 

$Id:$
*/

CREATE OR REPLACE FUNCTION ultimo_contrato_servidor_nomeacao_posse(VARCHAR, INTEGER) RETURNS SETOF colunasUltimoContratoServidorNomeacaoPosse AS $$
DECLARE
    stEntidade                      ALIAS FOR $1;
    inCodPeriodoMovimentacao        ALIAS FOR $2;
    stSql                           VARCHAR;
    rwNomeacaoPosse                 colunasUltimoContratoServidorNomeacaoPosse%ROWTYPE;
    stTimestampFechamentoPeriodo    VARCHAR;
    reRegistro                      RECORD;
BEGIN
    stTimestampFechamentoPeriodo := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao,stEntidade);

    stSql := '  SELECT contrato_servidor_nomeacao_posse.cod_contrato
                     , contrato_servidor_nomeacao_posse.dt_nomeacao
                     , contrato_servidor_nomeacao_posse.dt_posse
                     , contrato_servidor_nomeacao_posse.dt_admissao
                  FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
            INNER JOIN (  SELECT contrato_servidor_nomeacao_posse.cod_contrato
                               , max(contrato_servidor_nomeacao_posse.timestamp) as timestamp
                            FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                           WHERE contrato_servidor_nomeacao_posse.timestamp <= '||quote_literal(stTimestampFechamentoPeriodo)||'
                        GROUP BY contrato_servidor_nomeacao_posse.cod_contrato) as max_contrato_servidor_nomeacao_posse
                    ON max_contrato_servidor_nomeacao_posse.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato
                   AND max_contrato_servidor_nomeacao_posse.timestamp = contrato_servidor_nomeacao_posse.timestamp';

    FOR reRegistro IN EXECUTE stSql LOOP
        rwNomeacaoPosse.cod_contrato       := reRegistro.cod_contrato;
        rwNomeacaoPosse.dt_nomeacao        := reRegistro.dt_nomeacao;
        rwNomeacaoPosse.dt_posse           := reRegistro.dt_posse;
        rwNomeacaoPosse.dt_admissao        := reRegistro.dt_admissao;

        RETURN NEXT rwNomeacaoPosse;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';
