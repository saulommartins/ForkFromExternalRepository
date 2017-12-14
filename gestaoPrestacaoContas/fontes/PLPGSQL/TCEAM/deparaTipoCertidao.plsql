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
CREATE OR REPLACE FUNCTION tceam.fn_depara_tipo_certidao(INTEGER) RETURNS INTEGER AS $$
DECLARE
    inCodDocumento          ALIAS FOR $1;
    inResult                INTEGER := 0;
BEGIN
    IF (inCodDocumento = 12) THEN 
        inResult := 1;
    ELSEIF (inCodDocumento in (7, 25)) THEN
        inResult := 2;
    ELSEIF (inCodDocumento = 13) THEN
        inResult := 3;
    ELSEIF (inCodDocumento = 14) THEN
        inResult := 4;
    ELSEIF (inCodDocumento = 4) THEN
        inResult := 5;
    ELSE
        inResult := 99;
    END IF;

    return inResult;
END;


$$ LANGUAGE 'plpgsql';
