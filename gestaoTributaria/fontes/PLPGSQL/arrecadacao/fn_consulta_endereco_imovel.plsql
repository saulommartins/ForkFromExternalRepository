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
* $Id: fn_consulta_endereco_imovel.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-5.3.19
* Caso de uso: uc-05.03.19
*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_consulta_endereco_imovel( INTEGER )  RETURNS varchar AS $$
DECLARE
    inImovel    ALIAS FOR $1;
    stRetorno   VARCHAR;
    
BEGIN
    SELECT                     
        coalesce(tl.nom_tipo,' ')         ||' '||                                                    
        coalesce(nl.nom_logradouro,' ')   ||' '||
        coalesce(ltrim(i.numero::varchar,'0'),' ') ||' '||
        coalesce(i.complemento,' ')  
    INTO
        stRetorno
    FROM                                                                   
        (   SELECT * FROM
            imobiliario.imovel
            WHERE inscricao_municipal = inImovel
        ) i,                                              
        imobiliario.imovel_confrontacao ic,                                
        imobiliario.confrontacao_trecho ct,                                
        imobiliario.trecho t,                                              
        sw_logradouro l,
        sw_nome_logradouro nl,                                               
        sw_tipo_logradouro tl
    WHERE                                                                  
        ic.inscricao_municipal  = i.inscricao_municipal     AND            
        ct.cod_confrontacao     = ic.cod_confrontacao       AND            
        ct.cod_lote             = ic.cod_lote               AND            
        t.cod_trecho            = ct.cod_trecho             AND            
        t.cod_logradouro        = ct.cod_logradouro         AND            
        l.cod_logradouro        = t.cod_logradouro          AND            
        nl.cod_logradouro       = l.cod_logradouro          AND               
        tl.cod_tipo             = nl.cod_tipo               AND               
        i.inscricao_municipal   = inImovel
    ;

    RETURN stRetorno;
END;
$$ LANGUAGE 'plpgsql';