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
 * PL para retorno de número de vagas cadastradas para a especialidade
 * Data de Criação   : 05/11/2008 

 
 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza

 * @package URBEM
 * @subpackage 

 * @ignore # só use se FOR paginas que o cliente visualiza, se FOR mapeamento ou classe de negocio não se usa

 $Id:$
 */

CREATE OR REPLACE FUNCTION getVagasOcupadasCargo(INTEGER, INTEGER, INTEGER, INTEGER, BOOLEAN, VARCHAR) RETURNS INTEGER AS $$
DECLARE
    inCodRegime                     ALIAS FOR $1;
    inCodSubDivisao                 ALIAS FOR $2;
    inCodCargo                      ALIAS FOR $3;
    inCodPeriodoMovimentacao        ALIAS FOR $4;
    boLiberaVagaMesRescisao         ALIAS FOR $5;
    stEntidade                      ALIAS FOR $6;
    stSql                           VARCHAR;
    stTimestampFechamentoPeriodo    VARCHAR;
    stSqlLiberacaoVagaMesRescisao   VARCHAR;
    inContador                      INTEGER;
BEGIN

    stTimestampFechamentoPeriodo := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao,stEntidade);
    
    stSqlLiberacaoVagaMesRescisao := ' AND ( recuperarSituacaoDoContrato(contrato_servidor.cod_contrato,'||inCodPeriodoMovimentacao||','''||stEntidade||''') = ''A'' ';
    IF boLiberaVagaMesRescisao IS FALSE THEN
        stSqlLiberacaoVagaMesRescisao := stSqlLiberacaoVagaMesRescisao||' OR recuperarSituacaoDoContratoRescisaoPostergada(contrato_servidor.cod_contrato,'||inCodPeriodoMovimentacao||','''||stEntidade||''') = ''A'' ';
    END IF;
    stSqlLiberacaoVagaMesRescisao := stSqlLiberacaoVagaMesRescisao||' ) ';

    stSql := 'SELECT count(1) as contador
                FROM pessoal'||stEntidade||'.contrato_servidor
               WHERE cod_cargo       = '||inCodCargo||' 
                 AND cod_sub_divisao = '||inCodSubDivisao||'
                 AND cod_regime      = '||inCodRegime||'
                    '||stSqlLiberacaoVagaMesRescisao;
    inContador := selectIntoInteger(stSql);

    stSql := '    SELECT count(1) as contador 
                    FROM pessoal'||stEntidade||'.contrato_servidor 
              INNER JOIN pessoal'||stEntidade||'.contrato_servidor_funcao
                      ON contrato_servidor.cod_contrato = contrato_servidor_funcao.cod_contrato
              INNER JOIN (  SELECT cod_contrato
                                 , max(timestamp) as timestamp
                              FROM pessoal'||stEntidade||'.contrato_servidor_funcao
                             WHERE timestamp <= '''||stTimestampFechamentoPeriodo||'''
                          GROUP BY cod_contrato) as max_contrato_servidor_funcao
                      ON contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato
                     AND contrato_servidor_funcao.timestamp    = max_contrato_servidor_funcao.timestamp
              INNER JOIN pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
                      ON contrato_servidor.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato
              INNER JOIN (  SELECT cod_contrato
                                 , max(timestamp) as timestamp
                              FROM pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
                             WHERE timestamp <= '''||stTimestampFechamentoPeriodo||'''
                          GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao
                      ON contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato
                     AND contrato_servidor_sub_divisao_funcao.timestamp    = max_contrato_servidor_sub_divisao_funcao.timestamp
              INNER JOIN pessoal'||stEntidade||'.contrato_servidor_regime_funcao
                      ON contrato_servidor.cod_contrato = contrato_servidor_regime_funcao.cod_contrato
              INNER JOIN (  SELECT cod_contrato
                                 , max(timestamp) as timestamp
                              FROM pessoal'||stEntidade||'.contrato_servidor_regime_funcao
                             WHERE timestamp <= '''||stTimestampFechamentoPeriodo||'''
                          GROUP BY cod_contrato) as max_contrato_servidor_regime_funcao
                      ON contrato_servidor_regime_funcao.cod_contrato         = max_contrato_servidor_regime_funcao.cod_contrato
                     AND contrato_servidor_regime_funcao.timestamp            = max_contrato_servidor_regime_funcao.timestamp
                     
                   WHERE contrato_servidor_funcao.cod_cargo                   = '||inCodCargo||'
                     AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao = '||inCodSubDivisao||'
                     AND contrato_servidor_regime_funcao.cod_regime           = '||inCodRegime||'
                     AND (    
                              contrato_servidor.cod_cargo       != contrato_servidor_funcao.cod_cargo
                           OR contrato_servidor.cod_sub_divisao != contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                           OR contrato_servidor.cod_regime      != contrato_servidor_regime_funcao.cod_regime
                         )
                        '||stSqlLiberacaoVagaMesRescisao;
    inContador := inContador + selectIntoInteger(stSql);
    RETURN inContador;
END;
$$ LANGUAGE 'plpgsql';
