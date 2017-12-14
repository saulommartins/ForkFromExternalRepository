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
* $Id: fn_consulta_endereco_corresp_todos.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.2  2007/09/04 15:58:29  cercato
correcao na consulta.

Revision 1.1  2007/08/30 12:56:38  cercato
Bug#10043#

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_consulta_endereco_corresp_todos( INTEGER, INTEGER, INTEGER )  RETURNS varchar AS '
DECLARE
    inInscricao     ALIAS FOR $1; --cgm, inscricao_municipal,inscricao_economica
    inTipo          ALIAS FOR $2; --qual tipo (3 = cgm, 1 = ins munc, 2 = insc eco)
    inDados         ALIAS FOR $3; --(1=endereco, 2=bairro, 3=cep,4=municipio,6=numero)
    stRetorno       VARCHAR;
    stTipo          VARCHAR;
    inImovel        integer;
    
BEGIN

    IF ( inTipo = 1 ) THEN --inscricao municipal
        IF ( inDados = 1 ) THEN
            SELECT
                coalesce(tl.nom_tipo,'' '')         ||'' ''||
                coalesce(nl.nom_logradouro,'' '')   ||'' ''||
                coalesce(ltrim(ic.numero::varchar,0::varchar),'' '')  ||'' ''||
                coalesce(ic.complemento,'' '')  
            INTO
                stRetorno
            FROM
                imobiliario.imovel_correspondencia ic,
                sw_logradouro l,
                sw_nome_logradouro nl,
                sw_tipo_logradouro tl
            WHERE
                l.cod_logradouro        = ic.cod_logradouro         AND
                nl.cod_logradouro       = l.cod_logradouro          AND
                tl.cod_tipo             = nl.cod_tipo               AND
                ic.inscricao_municipal  = inInscricao 
                ORDER BY ic.timestamp DESC LIMIT 1;
        ELSIF ( inDados = 2 ) THEN
            SELECT
                coalesce(sb.nom_bairro, '' '')
            INTO
                stRetorno
            FROM
                imobiliario.imovel_correspondencia ic,
                sw_bairro sb

            WHERE
                sb.cod_bairro = ic.cod_bairro                      AND
                sb.cod_uf = ic.cod_uf                              AND
                sb.cod_municipio = ic.cod_municipio                AND
                ic.inscricao_municipal = inInscricao 

            ORDER BY ic.timestamp DESC LIMIT 1;
        ELSIF ( inDados = 3 ) THEN
            SELECT 
                coalesce(ic.cep, '' '')
            INTO
                stRetorno 
            FROM
                imobiliario.imovel_correspondencia ic
            WHERE 
                ic.inscricao_municipal = inInscricao
            ORDER BY ic.timestamp DESC LIMIT 1;
            
        ELSIF ( inDados = 4 ) THEN
            SELECT
                coalesce(sm.nom_municipio, '' '')||'' - ''||coalesce(su.nom_uf, '' '')
            INTO
                stRetorno
            FROM                                                                   
                imobiliario.imovel_correspondencia ic,                                              
                sw_municipio sm,
                sw_uf su

            WHERE
                sm.cod_uf = ic.cod_uf                              AND
                sm.cod_municipio = ic.cod_municipio                AND
                su.cod_uf = ic.cod_uf                              AND
                ic.inscricao_municipal = inInscricao 

            ORDER BY ic.timestamp DESC LIMIT 1;
            
        ELSIF ( inDados = 6 ) THEN
            SELECT 
                coalesce(ic.numero, '' '')
            INTO
                stRetorno 
            FROM
                imobiliario.imovel_correspondencia ic
            WHERE 
                ic.inscricao_municipal = inInscricao
            ORDER BY ic.timestamp DESC LIMIT 1;
            
        END IF;
        
    ELSIF ( inTipo = 2 ) THEN --inscricao economica
            SELECT tipo 
                    INTO stTipo
                    FROM (   select inscricao_economica
                                    , timestamp
                                    , ''informado'' as tipo 
                                from economico.domicilio_informado 
                                where inscricao_economica = inInscricao
                        union select inscricao_economica
                                    , timestamp
                                    , ''fiscal'' as tipo 
                                from economico.domicilio_fiscal 
                                where inscricao_economica = inInscricao
                            order by timestamp desc limit 1
                        ) as res;
            
                if stTipo = ''fiscal'' then
                    SELECT inscricao_municipal INTO inImovel FROM economico.domicilio_fiscal where inscricao_economica= inInscricao ORDER BY timestamp DESC LIMIT 1;
                    stRetorno := arrecadacao.fn_consulta_endereco_corresp_todos(inImovel, 1, inDados);
                    if stRetorno is null then
                        stRetorno := ''Endereço Inválido!'';
                    end if;
                elsif stTipo = ''informado'' then
                
                    IF ( inDados = 1 ) THEN
                        SELECT                     
                            coalesce(tl.nom_tipo,'' '')         ||'' ''||
                            coalesce(nl.nom_logradouro,'' '')   ||'' ''||
                            coalesce(ltrim(di.numero,0::varchar),'' '')  ||'' ''||
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
                            di.inscricao_economica  = inInscricao
                        ORDER BY di.timestamp DESC limit 1;
                    ELSIF ( inDados = 2 ) THEN
                        SELECT
                            coalesce(sb.nom_bairro, '' '')
                        INTO
                            stRetorno
                        FROM                                                                   
                            economico.domicilio_informado di,
                            sw_bairro sb
                        WHERE                                                                  
                            di.cod_bairro = sb.cod_bairro             AND
                            di.cod_uf = sb.cod_uf                     AND
                            di.cod_municipio = sb.cod_municipio       AND
                            di.inscricao_economica  = inInscricao
                        ORDER BY di.timestamp DESC limit 1;
                        
                    ELSIF ( inDados = 3 ) THEN
                        SELECT
                            coalesce(di.cep, '' '')
                        INTO
                            stRetorno
                        FROM                                                                   
                            economico.domicilio_informado di
                        WHERE                                                                  
                            di.inscricao_economica  = inInscricao
                        ORDER BY di.timestamp DESC limit 1;
                    ELSIF ( inDados = 4 ) THEN
                        SELECT
                            coalesce(sm.nom_municipio, '' '')||'' - ''||coalesce(su.nom_uf, '' '')
                        INTO
                            stRetorno
                        FROM                                                                   
                            economico.domicilio_informado di,
                            sw_municipio sm,
                            sw_uf su
                        WHERE
                            sm.cod_uf = di.cod_uf                     AND
                            di.cod_uf = su.cod_uf                     AND
                            di.cod_municipio = sm.cod_municipio       AND
                            di.inscricao_economica  = inInscricao
                        ORDER BY di.timestamp DESC limit 1;
                    
                    ELSIF ( inDados = 6 ) THEN
                        SELECT
                            coalesce(di.numero, '' '')
                        INTO
                            stRetorno
                        FROM                                                                   
                            economico.domicilio_informado di
                        WHERE                                                                  
                            di.inscricao_economica  = inInscricao
                        ORDER BY di.timestamp DESC limit 1;                        
                        
                    END IF;
                else
                    stRetorno := ''Não Encontrado'';
                end if;                
                
        ELSIF ( inTipo = 3 ) THEN --cgm
            IF ( inDados = 1 ) THEN
                SELECT
                    coalesce(cgm.tipo_logradouro_corresp, '' '')    ||'' ''||
                    coalesce(cgm.logradouro_corresp, '' '')         ||'' ''||
                    coalesce(trim(cgm.numero_corresp), '' '')  ||'' ''||
                    coalesce(cgm.complemento_corresp, '' '')
                INTO
                    stRetorno
                FROM                                                                   
                    sw_cgm cgm
                WHERE                                                                  
                    cgm.numcgm = inInscricao ;
            ELSIF ( inDados = 2 ) THEN
                SELECT
                    coalesce(cgm.bairro_corresp, '' '')
                INTO
                    stRetorno
                FROM                                                                   
                    sw_cgm cgm
                WHERE                                                                  
                    cgm.numcgm = inInscricao ;
            ELSIF ( inDados = 3 ) THEN
                SELECT
                    coalesce(cgm.cep_corresp, '' '')
                INTO
                    stRetorno
                FROM                                                                   
                    sw_cgm cgm
                WHERE                                                                  
                    cgm.numcgm = inInscricao ;
            ELSIF ( inDados = 4 ) THEN
                SELECT
                    coalesce(sm.nom_municipio, '' '')||'' - ''||coalesce(su.nom_uf, '' '')
                INTO
                    stRetorno
                FROM                                                                   
                    sw_cgm cgm,
                    sw_municipio sm,
                    sw_uf su
                WHERE
                    sm.cod_uf = cgm.cod_uf_corresp                     AND
                    cgm.cod_uf_corresp = su.cod_uf                     AND
                    cgm.cod_municipio_corresp = sm.cod_municipio       AND
                    cgm.numcgm = inInscricao ;
                    
            ELSIF ( inDados = 6 ) THEN
                SELECT
                    coalesce(cgm.numero_corresp, '' '')
                INTO
                    stRetorno
                FROM                                                                   
                    sw_cgm cgm
                WHERE                                                                  
                    cgm.numcgm = inInscricao ;
                    
            END IF;
        END IF;

    RETURN stRetorno;
END;
' LANGUAGE 'plpgsql';
