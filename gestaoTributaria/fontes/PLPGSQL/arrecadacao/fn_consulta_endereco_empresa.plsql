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
* $Id: fn_consulta_endereco_empresa.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-5.3.19
* Caso de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.3  2007/03/23 20:53:08  dibueno
Bug #8883#

Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_consulta_endereco_empresa( INTEGER )  RETURNS varchar AS '
DECLARE
    inIE        ALIAS FOR $1;
    stTipo      VARCHAR;
    inImovel    integer;
    stRetorno   VARCHAR;
    
BEGIN
        -- pega tipo do ultimo domicilio
        SELECT tipo 
          INTO stTipo
          FROM (   select inscricao_economica
                        , timestamp
                        , ''informado'' as tipo 
                     from economico.domicilio_informado 
                    where inscricao_economica = inIE
             union select inscricao_economica
                        , timestamp
                        , ''fiscal'' as tipo 
                     from economico.domicilio_fiscal 
                    where inscricao_economica = inIE
                 order by timestamp desc limit 1
            ) as res;

    if stTipo = ''fiscal'' then
        SELECT inscricao_municipal INTO inImovel FROM economico.domicilio_fiscal where inscricao_economica=inIE ORDER BY timestamp DESC LIMIT 1;
        stRetorno := arrecadacao.fn_consulta_endereco_imovel(inImovel);
        if stRetorno is null then
            stRetorno := ''Endereço Inválido!'';
        end if;
    elsif stTipo = ''informado'' then    
        SELECT                     
            coalesce(tl.nom_tipo,'' '')         ||'' ''||                                                    
            coalesce(nl.nom_logradouro,'' '')   ||'' ''||
            coalesce(di.numero,'' '')   ||'' ''||
            coalesce(di.complemento,'' '')  
        INTO
            stRetorno
        FROM                                                                   
            economico.domicilio_informado di,
            sw_logradouro l,
            sw_nome_logradouro nl,                                               
            sw_tipo_logradouro tl
        WHERE                                                                  
            l.cod_logradouro        = di.cod_logradouro         AND            
            nl.cod_logradouro       = l.cod_logradouro          AND               
            tl.cod_tipo             = nl.cod_tipo               AND
            di.inscricao_economica = inIE
        ORDER BY di.timestamp DESC limit 1
        ;
    else
        stRetorno := ''Não Encontrado'';     
    end if;

    RETURN stRetorno;
END;
' LANGUAGE 'plpgsql';
