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
 * PL para retorno de número de vagas cadastradas para o cargo
 * Data de Criação   : 05/11/2008


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza
 
 * @package URBEM
 * @subpackage 

 * @ignore # só use se FOR paginas que o cliente visualiza, se FOR mapeamento ou classe de negocio não se usa

 $Id:$
 */
CREATE OR REPLACE FUNCTION getVagasCadastradasCargo(INTEGER, INTEGER, INTEGER, INTEGER, VARCHAR) RETURNS INTEGER AS $$
DECLARE
    inCodRegime                     ALIAS FOR $1;
    inCodSubDivisao                 ALIAS FOR $2;
    inCodCargo                      ALIAS FOR $3;
    inCodPeriodoMovimentacao        ALIAS FOR $4;
    stEntidade                      ALIAS FOR $5;
    stSql                           VARCHAR;
    stTimestampFechamentoPeriodo    VARCHAR;
    inContador                      INTEGER;
BEGIN

    stTimestampFechamentoPeriodo := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao,stEntidade);
    
    stSql := '    SELECT nro_vaga_criada
                    FROM pessoal'||stEntidade||'.cargo_sub_divisao
              INNER JOIN (  SELECT cod_cargo
                                 , cod_sub_divisao
                                 , max(timestamp) as timestamp
                              FROM pessoal'||stEntidade||'.cargo_sub_divisao
                             WHERE timestamp <= '''||stTimestampFechamentoPeriodo||'''
                          GROUP BY cod_cargo
                                 , cod_sub_divisao) as max_cargo_sub_divisao
                      ON cargo_sub_divisao.cod_cargo       = max_cargo_sub_divisao.cod_cargo
                     AND cargo_sub_divisao.cod_sub_divisao = max_cargo_sub_divisao.cod_sub_divisao
                     AND cargo_sub_divisao.timestamp       = max_cargo_sub_divisao.timestamp
              INNER JOIN pessoal'||stEntidade||'.sub_divisao
                      ON cargo_sub_divisao.cod_sub_divisao = sub_divisao.cod_sub_divisao
              INNER JOIN pessoal'||stEntidade||'.regime
                      ON sub_divisao.cod_regime            = regime.cod_regime
                   WHERE cargo_sub_divisao.cod_cargo       = '||inCodCargo||' 
                     AND cargo_sub_divisao.cod_sub_divisao = '||inCodSubDivisao||'
                     AND regime.cod_regime = '||inCodRegime;
    inContador := selectIntoInteger(stSql);
    RETURN inContador;
END;
$$ LANGUAGE 'plpgsql';
