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
 * Titulo do arquivo Formata o código do PISPASEP
 * Data de Criação   : 16/09/2008


 * @author Analista      Dagiane
 * @author Desenvolvedor Diego 
 
 * @package URBEM
 * @subpackage 

 * @ignore # só use se for paginas que o cliente visualiza, se for mapeamento ou classe de negocio não se usa

 $Id:$
 */

create or replace function remove_meses(date,integer) returns date as $$
declare
    dtEntrada       alias for $1;
    inMeses         alias for $2;
    stSQL           varchar;
begin
    stSQL := 'select to_date(('|| quote_literal(dtEntrada) ||'::date - interval '|| quote_literal(inMeses||' month') ||')::VARCHAR, '|| quote_literal('yyyy-mm-dd') ||')';
    return selectIntoVarchar(stSQL);
end
$$ language 'plpgsql';
