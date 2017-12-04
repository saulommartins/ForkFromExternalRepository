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
*
* Casos d uso: uc-05.03.05
*
* $Id: $
*
*/

CREATE OR REPLACE FUNCTION deleteCalculo( INTEGER ) RETURNS VARCHAR AS '
DECLARE
    inCodCalculo       ALIAS FOR $1;
    inRetorno          VARCHAR;
BEGIN

    -- DELETE DA CADASTRO_ECONOMICO_CALCULO
    DELETE
      FROM  arrecadacao.cadastro_economico_calculo
     WHERE  cod_calculo = inCodCalculo;
    
    -- DELETE DA IMOVEL_CALCULO
    DELETE
      FROM  arrecadacao.imovel_calculo
     WHERE  cod_calculo = inCodCalculo;
    
    -- DELETE DA CALCULO_CGM
    DELETE
      FROM  arrecadacao.calculo_cgm
     WHERE  cod_calculo = inCodCalculo;
    
    -- DELETE DA CALCULO_GRUPO_CREDITO
    DELETE
      FROM  arrecadacao.calculo_grupo_credito
     WHERE  cod_calculo = inCodCalculo;
    
    -- DELETE DA LOG_CALCULO
    DELETE
      FROM  arrecadacao.log_calculo
     WHERE  cod_calculo = inCodCalculo;
    
    -- DELETE DA CALCULO
    DELETE
      FROM  arrecadacao.calculo
     WHERE  cod_calculo = inCodCalculo;

    inRetorno:= '' Calculo deletado ''||inCodCalculo;

    RETURN inRetorno;
END;

' LANGUAGE 'plpgsql';
