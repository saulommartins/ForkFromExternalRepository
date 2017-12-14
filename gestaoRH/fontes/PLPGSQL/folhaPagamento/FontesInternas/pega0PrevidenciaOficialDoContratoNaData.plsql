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
* script de funcao PLSQL
* 
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br

* $Revision: 23095 $
* $Name$
* $Autor: MArcia $
* Date: 2005/11/29 10:50:00 $
*
* Caso de uso: uc-04.05.48
*
* Objetivo: Recebe o codigo do contrato e retorna a previdencia oficial 
* para o contrato na data.
*/



CREATE OR REPLACE FUNCTION pega0PrevidenciaOficialDoContratoNaData(integer,varchar) RETURNS integer as '

DECLARE
    inCodContrato           ALIAS FOR $1;
    stTimestamp             ALIAS FOR $2;

    inCodPrevidencia        INTEGER := 0;
stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN


    inCodPrevidencia := selectIntoInteger(''
        SELECT csp.cod_previdencia
          FROM pessoal''||stEntidade||''.contrato_servidor_previdencia  as csp

          LEFT OUTER JOIN
            (SELECT folhapagamento''||stEntidade||''.previdencia_previdencia.cod_previdencia FROM folhapagamento''||stEntidade||''.previdencia_previdencia 
              WHERE folhapagamento''||stEntidade||''.previdencia_previdencia.vigencia <= to_date(''''''||stTimestamp||'''''',''''yyyy-mm-dd'''')
                AND folhapagamento''||stEntidade||''.previdencia_previdencia.tipo_previdencia = ''''o'''' )as prev
            ON prev.cod_previdencia = csp.cod_previdencia
         WHERE cod_contrato = ''||inCodContrato||''
            AND prev.cod_previdencia is not null
            AND csp.bo_excluido = false 
          ORDER BY csp.timestamp desc 
           LIMIT 1''
               ); 

    IF inCodPrevidencia is null THEN
       inCodPrevidencia := 0;
    END IF;

    RETURN inCodPrevidencia;
END;
' LANGUAGE 'plpgsql';

