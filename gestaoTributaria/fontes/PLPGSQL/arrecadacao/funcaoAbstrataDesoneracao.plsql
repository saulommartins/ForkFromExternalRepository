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
* $Id: funcaoAbstrataDesoneracao.plsql 59612 2014-09-02 12:00:51Z gelson $
*
*
* Casos d uso: uc-05.03.02 
*/

/*
$Log$
Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION funcaoAbstrataDesoneracao( integer , integer , numeric) RETURNS numeric AS $$
DECLARE
    -- parametros
    inCodDesoneracao  alias for $1;
    inNumCgm          alias for $2;
    nuValor           alias for $3;

    stFuncao          varchar;
    nuRetorno         varchar := '';
    stSql             varchar := '';
    crCursor          refcursor;
BEGIN  
        select funcao.nom_funcao 
          into stFuncao
          from arrecadacao.desoneracao
    inner join administracao.funcao
            on funcao.cod_funcao = desoneracao.cod_funcao
           and funcao.cod_biblioteca = desoneracao.cod_biblioteca
           and funcao.cod_modulo = desoneracao.cod_modulo
         where desoneracao.cod_desoneracao = inCodDesoneracao;

    stSql := 'SELECT ' || stFuncao ||'('|| inCodDesoneracao  ||' , '|| inNumCgm ||' , '|| nuValor ||')';
    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuRetorno;
    CLOSE crCursor;

    return nuRetorno;
END;
$$ LANGUAGE 'plpgsql';
