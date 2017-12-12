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
--
-- script de funcao PLSQL
--
-- URBEM Solugues de Gestco Pzblica Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 23095 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2006/05/11 10:50:00 $
--
-- Caso de uso: uc-04.05.48
--
-- Objetivo: recebe string de eventos delimitados por ';' e retorna 
-- o somatorio ( conforme provento/desconto ) dos buffers dos eventos
--
--
--
CREATE OR REPLACE FUNCTION criarBufferNumericoFolhas(varchar,numeric) RETURNS numeric as $$

DECLARE
    stNomeBufferPar ALIAS FOR $1;
    nuValor         ALIAS FOR $2;
    stTipoFolha     VARCHAR;
    stNomeBuffer    VARCHAR:='';
BEGIN
    stTipoFolha := recuperarBufferTexto('stTipoFolha');
    stNomeBuffer := stNomeBufferPar;
    IF stTipoFolha = 'C' THEN
        stNomeBuffer := stNomeBuffer || recuperarBufferInteiro('inCodConfiguracao');
    END IF;

    IF stTipoFolha = 'F' OR stTipoFolha = 'D' OR stTipoFolha = 'R' THEN
        stNomeBuffer := stNomeBuffer || LOWER(recuperarBufferTexto('stDesdobramento')); 
    END IF;
    RETURN criarBufferNumerico(stNomeBuffer,nuValor);
END;
$$ LANGUAGE 'plpgsql';


