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
* $Id: fn_ultimo_venal_por_im_lanc.plsql 29203 2008-04-15 14:45:04Z fabio $
*
* Caso de uso: uc-05.03.00
*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_tipo_ultimo_venal_por_im_lanc( INTEGER,INTEGER) returns varchar AS '
DECLARE
    inInscricaoMunicipal   ALIAS FOR $1;
    inCodLancamento        ALIAS FOR $2;
    inMaxCodCalculo        integer;
    tsTimestampCalculo     timestamp;
    tsMaiorTimestamp       timestamp;
    stResultado            varchar;
BEGIN
-- pega calculo do lancamento
     select max(cod_calculo)
       into inMaxCodCalculo
       from arrecadacao.lancamento_calculo
      where cod_lancamento = inCodLancamento;

-- timestamp do calculo
     select timestamp
       into tsTimestampCalculo
       from arrecadacao.calculo
      where cod_calculo = inMaxCodCalculo;

-- maior timestamp menor que timestamp do calculo
     select max(timestamp) 
       into tsMaiorTimestamp
       from arrecadacao.imovel_v_venal
      where timestamp <= tsTimestampCalculo
        and inscricao_municipal = inInscricaoMunicipal
        and (venal_total_informado IS not null OR venal_total_calculado IS not null);
    
-- venal do timestamp encontrado    
     select 
            case when ( iv.venal_total_informado is not null ) then
                ''Informado''
            else
                case when ( iv.venal_total_calculado is not null ) then
                    ''Calculado''
                end
            end

       into stResultado
       from arrecadacao.imovel_v_venal as iv
      where inscricao_municipal = inInscricaoMunicipal
        and timestamp = tsMaiorTimestamp
   order by iv.venal_total_informado,venal_total_calculado desc
      limit 1;

    return stResultado;
END;
' LANGUAGE 'plpgsql';
