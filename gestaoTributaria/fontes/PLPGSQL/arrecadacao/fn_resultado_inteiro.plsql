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
* Pega retorno padrao, e retorna somente o valor calculado pela função!
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: fn_resultado_inteiro.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: UC-5.3.5
* Caso de uso: uc-05.03.05
*/

/*
$Log$
Revision 1.8  2007/08/16 19:29:14  dibueno
retirada de RAISEs

Revision 1.7  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_resultado_int(stValorRetorno VARCHAR) RETURNS integer AS $$
DECLARE
    inRetorno                   INTEGER;
    stTemp                      VARCHAR;
    stTipo                      VARCHAR;
BEGIN
/**
    Tipos de Retorno
    e = erro na execução da função
    n = numeric(20,4)
    i = inteiro
    s = string
    d = data sempre em formato YYYY-MM-DD hh:nn:ss
    t = timestamp unix
*/
stTipo := substring(stValorRetorno from 1 for 1);

IF stTipo = 'i' THEN
    inRetorno := ltrim(substring(stValorRetorno from 3),0)::integer;
ELSE
    inRetorno := 0;
END IF;

    RETURN inRetorno;
END;
$$ LANGUAGE plpgsql;
