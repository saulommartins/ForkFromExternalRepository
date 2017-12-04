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
* $Id: atualizaVenal.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.1  2006/11/13 11:51:39  fabio
criada PL para atualizar o valor venal durante calculo de IPTU (Mata)


*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_atualiza_venal(int,numeric,numeric,int) returns boolean AS '
DECLARE
    inInscricaoMunicipal   ALIAS FOR $1;
    nuVenalPredial         ALIAS FOR $2;
    nuVenalTotal           ALIAS FOR $3;
    inExercicio            ALIAS FOR $4;
    tsTimestamp            varchar;
BEGIN

    SELECT
        timestamp::varchar
    INTO
        tsTimestamp
    FROM
        arrecadacao.imovel_v_venal as venal
    WHERE 
        inscricao_municipal = inInscricaoMunicipal
    ORDER BY
        timestamp DESC
    LIMIT 1 ;
   
 
    UPDATE 
        arrecadacao.imovel_v_venal
    SET 
        venal_predial_calculado = nuVenalPredial,
        venal_total_calculado   = nuVenalTotal
    WHERE 
        inscricao_municipal     = inInscricaoMunicipal
    AND exercicio               = inExercicio::varchar
    AND timestamp               = tsTimestamp::timestamp;

 
    if ( FOUND ) then
        return true;
    else
        return false; 
    end if;

END;
' LANGUAGE 'plpgsql';
