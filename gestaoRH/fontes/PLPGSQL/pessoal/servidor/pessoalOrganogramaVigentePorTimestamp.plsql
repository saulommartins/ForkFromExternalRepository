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
/* pessoalOrganogramaVigentePorTimestamp
 * 
 * Data de Criação : 28/07/2009


 * @author Analista : Dagiane Vieira
 * @author Desenvolvedor : Alex Cardoso
 
 * @package URBEM
 * @subpackage 

 */

CREATE OR REPLACE FUNCTION pessoalOrganogramaVigentePorTimestamp(VARCHAR,VARCHAR) RETURNS INTEGER AS $$
DECLARE
    stEntidade                      ALIAS FOR $1;
    stTimestampFechamentoPeriodo    ALIAS FOR $2;
    stSql                           VARCHAR;
    inCodOrganograma                INTEGER;
BEGIN
      stSql := '  SELECT cod_organograma
                    FROM organograma.orgao_nivel
                  WHERE cod_orgao = (
                                        SELECT cod_orgao
                                          FROM pessoal'||stEntidade||'.contrato_servidor_orgao
                                        WHERE timestamp <=  '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                      ORDER BY timestamp DESC
                                        LIMIT 1
                                    )
                  LIMIT 1';
    inCodOrganograma := selectIntoInteger(stSql);
    
    IF inCodOrganograma IS NULL THEN
    
        stSql := 'SELECT cod_organograma
                    FROM organograma.organograma
                   WHERE ativo IS TRUE';

        inCodOrganograma := selectIntoInteger(stSql);
    END IF;
    
    IF inCodOrganograma IS NULL THEN
        inCodOrganograma := 0;
    END IF;
    
    RETURN inCodOrganograma;
END;
$$ LANGUAGE 'plpgsql';
