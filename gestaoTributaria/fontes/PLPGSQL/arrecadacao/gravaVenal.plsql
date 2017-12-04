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
* $Id: gravaVenal.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.3  2006/10/18 11:55:33  cercato
correcoes para funcionar com a tabela imovel_v_venal que foi modificada.

Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_grava_venal(int,numeric,numeric,numeric,int) returns boolean AS '
DECLARE
    inInscricaoMunicipal   ALIAS FOR $1;
    nuVenalTerritorial     ALIAS FOR $2;
    nuVenalPredial         ALIAS FOR $3;
    nuVenalTotal           ALIAS FOR $4;
    inExercicio            ALIAS FOR $5;
BEGIN
begin
    INSERT INTO arrecadacao.imovel_v_venal( inscricao_municipal,
                                            venal_territorial_calculado,
                                            venal_predial_calculado,
                                            venal_total_calculado,
                                            exercicio) 
    VALUES (    inInscricaoMunicipal,
                nuVenalTerritorial,
                nuVenalPredial,
                nuVenalTotal,
                inExercicio);
   
    if ( FOUND ) then
        return true;
    else
        return false; 
    end if;
exception
	when others then 
	return true;
end;
END;
' LANGUAGE 'plpgsql';
