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
* Date: 2006/01/25 10:50:00 $
*
* Caso de uso: uc-04.05.48
*
* Objetivo: Busca o codigo da cid vinculado ao servidor na data determinada.
* A data normalmente sera o timestamp stDataFinalCompetencia
* A tabela servidor_cid consta de todos os servidores, nem que seja 
* com o codigo cid = 0 de nao informado
*/



CREATE OR REPLACE FUNCTION pega0CidServidorNaData(integer,varchar) RETURNS integer as '

DECLARE
    inCodServidor           ALIAS FOR $1;
    stTimestamp             ALIAS FOR $2;

    inCodCid                INTEGER;

stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN


    inCodCid := selectIntoInteger  (''
        SELECT cod_cid
          FROM pessoal''||stEntidade||''.servidor_cid
          WHERE cod_servidor = ''||inCodServidor||''
            AND timestamp <= ''''''||stTimestamp||''''''
          ORDER BY timestamp desc 
          LIMIT 1''
                          );

   IF inCodCid is null THEN
      inCodCid = 0 ;
   END IF;

   RETURN inCodCid;

END;
' LANGUAGE 'plpgsql';

