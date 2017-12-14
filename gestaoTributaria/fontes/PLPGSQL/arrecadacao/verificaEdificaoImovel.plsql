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
* Valor de um lancamento!
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: verificaEdificaoImovel.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.3  2007/03/14 13:25:09  fabio
corrigida para verificar se a edificação está baixada ou não (se estiver baixada, equivale ao imóvel não possuir edificação)

Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.verificaEdificacaoImovel(integer) returns boolean as '
declare
    inImovel        ALIAS FOR $1;
    inCons          integer;
begin
       SELECT cod_construcao    
         INTO inCons
         FROM imobiliario.unidade_autonoma
        WHERE inscricao_municipal = inImovel
          AND cod_construcao NOT IN (
                                    SELECT cod_construcao
                                      FROM (
                                           SELECT MAX (TIMESTAMP) AS TIMESTAMP
                                                , cod_construcao
                                                , dt_termino
                                             FROM imobiliario.baixa_unidade_autonoma
                                         GROUP BY cod_construcao
                                                , dt_termino
                                           ) AS BT
                                     WHERE BT.dt_termino IS NULL
                                    );

if ( FOUND ) then
    return true;
else
    return false;
end if;

end;
'language 'plpgsql';
