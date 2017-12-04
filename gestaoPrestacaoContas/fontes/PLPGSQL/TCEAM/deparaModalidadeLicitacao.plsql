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
CREATE OR REPLACE FUNCTION tceam.fn_depara_modalidade_licitacao(INTEGER, INTEGER) RETURNS INTEGER AS $$
DECLARE
    inCodModalidade         ALIAS FOR $1;
    inCodTipoObjeto         ALIAS FOR $2;
    inResult                INTEGER := 0;
BEGIN
    IF (inCodModalidade = 1) THEN 
        IF (inCodTipoObjeto = 1) THEN
            inResult := 1;
        ELSEIF (inCodTipoObjeto = 2) THEN
            inResult := 2;
        END IF;
    ELSEIF (inCodModalidade = 2) THEN
        IF (inCodTipoObjeto = 1) THEN
            inResult := 3;
        ELSEIF (inCodTipoObjeto = 2) THEN
            inResult := 4;
        END IF;
    ELSEIF (inCodModalidade = 3) THEN
        IF (inCodTipoObjeto = 1) THEN
            inResult := 5;
        ELSEIF (inCodTipoObjeto = 2) THEN
            inResult := 6;
        END IF;
    ELSEIF (inCodModalidade = 4) THEN
        inResult := 7;
    ELSEIF (inCodModalidade = 5) THEN
        inResult := 10;
    ELSEIF (inCodModalidade = 6) THEN
        inResult := 13;
    ELSEIF (inCodModalidade = 7) THEN
        inResult := 11;
    ELSEIF (inCodModalidade = 8) THEN
        inResult := 8;
    ELSEIF (inCodModalidade = 9) THEN
        inResult := 9;
    ELSE
        inResult := null;
    END IF;

    return inResult;
END;


$$ LANGUAGE 'plpgsql';
