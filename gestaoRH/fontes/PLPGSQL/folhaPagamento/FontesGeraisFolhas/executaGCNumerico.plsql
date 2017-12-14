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
--    * Data de Criação: 00/00/0000
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 27905 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2008-02-07 13:07:29 -0200 (Qui, 07 Fev 2008) $
--
--    * Casos de uso: uc-04.05.09
--    * Casos de uso: uc-04.05.10
--*/

CREATE OR REPLACE FUNCTION executaGCNumerico(VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    stFormula       ALIAS FOR $1;
    inCodModulo     INTEGER;
    inCodBiblioteca INTEGER;
    inCodFuncao     INTEGER;
    arFormula       VARCHAR[];
    stFuncao        VARCHAR := '';    
    nuRetorno       VARCHAR := '';    
    stSql           VARCHAR := '';    
    crCursor        REFCURSOR;
BEGIN
    arFormula       := string_to_array(stFormula,'.');
    inCodModulo     := arFormula[1];
    inCodBiblioteca := arFormula[2];
    inCodFuncao     := arFormula[3];

    SELECT nom_funcao
      INTO stFuncao
      FROM administracao.funcao
     WHERE cod_modulo       = inCodModulo
       AND cod_biblioteca   = inCodBiblioteca
       AND cod_funcao       = inCodFuncao;
    
    stSql := 'SELECT ' || stFuncao ||'()';

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuRetorno;
    CLOSE crCursor;
    
    RETURN nuRetorno;
END;
$$ LANGUAGE 'plpgsql';


