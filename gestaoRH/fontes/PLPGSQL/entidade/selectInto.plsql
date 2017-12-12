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
--/**
--    * Função PLSQL
--    * Data de Criação: 13/06/2007
--
--
--    * @author Analista: Diego Lemos de Souza
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23209 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-13 09:55:56 -0300 (Qua, 13 Jun 2007) $
--
--    * Casos de uso: uc-04.00.00
--*/
CREATE OR REPLACE FUNCTION selectIntoInteger(VARCHAR) RETURNS INTEGER as $$
DECLARE
    stSql                       ALIAS FOR $1;
    inRetorno                   INTEGER;
    crCursor                    REFCURSOR;
BEGIN
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO inRetorno;
    CLOSE crCursor;    
    RETURN inRetorno;
END;
$$ LANGUAGE 'plpgsql';


CREATE OR REPLACE FUNCTION selectIntoVarchar(VARCHAR) RETURNS VARCHAR as $$
DECLARE
    stSql                       ALIAS FOR $1;
    stRetorno                   VARCHAR;
    crCursor                    REFCURSOR;
BEGIN
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO stRetorno;
    CLOSE crCursor;
    RETURN stRetorno;
END;
$$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION selectIntoNumeric(VARCHAR) RETURNS NUMERIC as $$
DECLARE
    stSql                       ALIAS FOR $1;
    nuRetorno                   NUMERIC;
    crCursor                    REFCURSOR;
BEGIN
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO nuRetorno;
    CLOSE crCursor;
    RETURN nuRetorno;
END;
$$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION selectIntoBoolean(VARCHAR) RETURNS BOOLEAN as $$
DECLARE
    stSql                       ALIAS FOR $1;
    boRetorno                   BOOLEAN;
    crCursor                    REFCURSOR;
BEGIN
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO boRetorno;
    CLOSE crCursor;
    RETURN boRetorno;
END;
$$ LANGUAGE 'plpgsql';
