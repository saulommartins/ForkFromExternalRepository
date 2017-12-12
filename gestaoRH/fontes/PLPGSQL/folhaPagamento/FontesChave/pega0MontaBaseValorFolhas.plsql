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
--nuValor := pega0MontaBaseValorFolhas('1;12;13;14;16;18;22;24;31;139;140;141;342;347;348;375;401;2381;2382','baseinss');
CREATE OR REPLACE FUNCTION pega0MontaBaseValorFolhas(varchar,varchar) RETURNS numeric as $$

DECLARE
    stLista         ALIAS FOR $1;
    stNomeBasePar   ALIAS FOR $2;
    stTipoFolha     VARCHAR;
    nuValorBase     NUMERIC;
    stNomeBase      VARCHAR:='';
    stDesdobramento VARCHAR:='';
BEGIN
    stTipoFolha := recuperarBufferTexto('stTipoFolha');
    stNomeBase := stNomeBasePar;
    IF stTipoFolha = 'S' OR stTipoFolha = 'C' THEN
        nuValorBase := pega0MontaBaseValor(stLista);
        IF stTipoFolha = 'C' THEN
             stNomeBase := stNomeBase || recuperarBufferInteiro('inCodConfiguracao');
        END IF;
    ELSE
        IF stTipoFolha = 'F' THEN
            nuValorBase := pega0MontaBaseValorFerias(stLista);
        END IF;

        IF stTipoFolha = 'D' THEN
            nuValorBase := pega0MontaBaseValorDecimo(stLista);
        END IF;

        IF stTipoFolha = 'R' THEN
            nuValorBase := pega0MontaBaseValorRescisao(stLista);
        END IF;

        stNomeBase := stNomeBase || LOWER(recuperarBufferTexto('stDesdobramento'));
    END IF;
    IF stNomeBasePar != '' THEN
        nuValorBase := criarBufferNumerico(stNomeBase,nuValorBase);
    END IF;
    RETURN nuValorBase;
END;
$$ LANGUAGE 'plpgsql';


