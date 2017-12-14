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
* Date: 2006/05/11 10:50:00 $
*
* Caso de uso: uc-04.05.48
*
* Objetivo: recebe o codigo externo (codigo visto usuario) do evento e retorna 
* o codigo interno do evento; O codigo externo ja deve vir formatado com 
* o tamanho correto da mascara.
*/




CREATE OR REPLACE FUNCTION pega0CodigoEventoPeloNumero(varchar) RETURNS integer as '

DECLARE
    stCodigoExternoEvento    ALIAS FOR $1;

    inCodEvento             INTEGER := 0;
    stSql                   VARCHAR := '''';
    reRegistro              RECORD;
stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN


    stSql := ''
        SELECT cod_evento
          FROM folhapagamento''||stEntidade||''.evento
         WHERE codigo = ''''''||stCodigoExternoEvento||''''''
            '';

    FOR reRegistro IN EXECUTE stSql
    LOOP
       inCodEvento :=  reRegistro.cod_evento;
    END LOOP;

    RETURN inCodEvento;
END;
' LANGUAGE 'plpgsql';

