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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
* $Id: TCETORecuperaCodigoOrgao.plsql 60694 2014-11-10 17:09:02Z evandro $
* $Revision: $
* $Name$
* $Author: $
* $Date: $
*
*/
CREATE OR REPLACE FUNCTION tceto.recupera_codigo_orgao(VARCHAR, INTEGER, VARCHAR) RETURNS INTEGER AS $$ 
DECLARE
    stExercicio         ALIAS FOR $1;
    inEntidade          ALIAS FOR $2;
    stTipoDados         ALIAS FOR $3;
    stSql               VARCHAR := '';
    stParametro         VARCHAR := '';
    stFiltroParametro   VARCHAR := '';
    inCodOrgao          INTEGER := 0;
BEGIN

    SELECT substr(parametro, 14) 
        INTO stParametro
    FROM administracao.configuracao
    WHERE exercicio = stExercicio
    AND valor = inEntidade::VARCHAR
    AND cod_modulo = 8
    AND parametro ILIKE 'cod_entidade_%';
    
    IF stParametro = 'prefeitura' THEN
        stFiltroParametro := 'tceto_'||stTipoDados||'_prefeitura';
    ELSIF stParametro = 'camara' THEN
        stFiltroParametro := 'tceto_'||stTipoDados||'_camara';
    ELSIF stParametro = 'rpps' THEN
        stFiltroParametro := 'tceto_'||stTipoDados||'_rpps';
    ELSE
        stFiltroParametro := 'tceto_'||stTipoDados||'_outros';
    END IF;
    
    SELECT  CASE 
                WHEN valor = ''
                    THEN 0::INTEGER 
                ELSE valor::INTEGER
            END AS valor
      INTO inCodOrgao
    FROM administracao.configuracao
    WHERE exercicio = stExercicio
    AND cod_modulo = 64
    AND parametro = stFiltroParametro;
    
    IF inCodOrgao IS NULL THEN
        inCodOrgao := 0;
    END IF;
    RETURN inCodOrgao;
END;
$$ LANGUAGE 'plpgsql';