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
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: gravaFaturamento.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.1  2006/09/18 14:25:13  domluc
Grava Faturamento

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_grava_faturamento(int,numeric,numeric,varchar) returns boolean AS '
DECLARE
    inInscricaoEconomica   ALIAS FOR $1;
    nuValorBruto           ALIAS FOR $2;
    nuComplemento          ALIAS FOR $3;
    stCompetencia          ALIAS FOR $4;
    stComp                 varchar;
    inAux                  integer; 
BEGIN
    inAux := date_part( ''month'' , now()::date )::integer - 1;
    stComp := inAux::varchar||''/'';
    inAux := date_part( ''year'' , now()::date )::integer;
    stComp := stComp||inAux::varchar;

     
    INSERT INTO arrecadacao.cadastro_economico_faturamento( inscricao_economica,
                                                            valor_bruto_informado,
                                                            complemento,        
                                                            competencia) 
    VALUES (    inInscricaoEconomica,
                nuValorBruto,
                nuComplemento,
                stComp);
   
    if ( FOUND ) then
        return true;
    else
        return false; 
    end if;

END;
' LANGUAGE 'plpgsql';
