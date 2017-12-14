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

* $Revision: 24045 $
* $Name$
* $Autor: Marcia $
* Date: 2006/04/27 10:50:00 $
*
* Caso de uso: uc-04.05.14
*
* Objetivo: 

*/



CREATE OR REPLACE FUNCTION pega2EventoDeDescontoDePrevidenciaParaSalario() RETURNS varchar as $$

DECLARE
    inCodPrevidencia         INTEGER;
    stTimestampTabela        VARCHAR;
    
    inCodTipo                INTEGER := 1;
    inCodEvento              INTEGER := 0;
    stNumeroEvento           VARCHAR := '';
    stEntidade            VARCHAR;
BEGIN
    stEntidade    := recuperarBufferTexto('stEntidade');
    inCodPrevidencia := recuperarBufferInteiro( 'inCodPrevidenciaOficial' );
    stTimestampTabela := pega1TimestampTabelaPrevidencia();

    IF stTimestampTabela != '' THEN
        inCodEvento := selectIntoInteger('
               SELECT cod_evento
                 FROM folhapagamento'||stEntidade||'.previdencia_evento
                 WHERE cod_tipo = '||inCodTipo||'
                   AND cod_previdencia = '||inCodPrevidencia||'
                   AND timestamp = '''||stTimestampTabela||'''
                                  ');
        iF inCodEvento is not null THEN
            stNumeroEvento := pega0NumeroDoEvento( inCodEvento );
        END IF;
    END IF;
    RETURN stNumeroEvento;
END;
$$ LANGUAGE 'plpgsql';

