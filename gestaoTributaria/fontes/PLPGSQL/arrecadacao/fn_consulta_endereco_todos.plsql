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
* $Id: fn_consulta_endereco_todos.plsql 59612 2014-09-02 12:00:51Z gelson $
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

CREATE OR REPLACE FUNCTION arrecadacao.fn_consulta_endereco_todos( INTEGER, INTEGER, INTEGER )  RETURNS varchar AS $$
DECLARE
    inInscricao     ALIAS FOR $1; --cgm, inscricao_municipal,inscricao_economica
    inTipo          ALIAS FOR $2; --qual tipo (3 = cgm, 1 = ins munc, 2 = insc eco)
    inDados         ALIAS FOR $3; --(1=endereco, 2=bairro, 3=cep,4=municipio,5=localizacao,6=numero)
    stRetorno       VARCHAR;
    stTipo          VARCHAR;
    inImovel        integer;
    
BEGIN

    IF ( inTipo = 1 ) THEN --inscricao municipal
        IF ( inDados = 1 ) THEN
            SELECT
                coalesce(tl.nom_tipo,' ')         ||' '||                                                    
                coalesce(nl.nom_logradouro,' ')   ||' '||
                coalesce(ltrim(i.numero)::varchar,' ')   ||' '||
                coalesce(i.complemento,' ')  
            INTO
                stRetorno
            FROM                                                                   
                (   SELECT * FROM
                    imobiliario.imovel
                    WHERE inscricao_municipal = inInscricao
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
                i.inscricao_municipal   = inInscricao ;
        ELSIF ( inDados = 2 ) THEN
            SELECT
                coalesce(sb.nom_bairro, ' ')
            INTO
                stRetorno
            FROM                                                                   
                (   SELECT * FROM
                    imobiliario.imovel
                    WHERE inscricao_municipal = inInscricao
                ) i,                                              
                imobiliario.imovel_lote il,                                
                imobiliario.lote ilot,
                imobiliario.lote_bairro ilb,
                sw_bairro sb

            WHERE
                il.inscricao_municipal  = i.inscricao_municipal     AND
                ilot.cod_lote = il.cod_lote                         AND
                ilot.cod_lote = ilb.cod_lote                        AND
                sb.cod_bairro = ilb.cod_bairro                      AND
                sb.cod_uf = ilb.cod_uf                              AND
                sb.cod_municipio = ilb.cod_municipio                AND
                i.inscricao_municipal = inInscricao 

            ORDER BY ilot.timestamp, ilb.timestamp DESC LIMIT 1;
        ELSIF ( inDados = 3 ) THEN
            SELECT 
                coalesce(imovel.cep, ' ')
            INTO
                stRetorno 
            FROM
                imobiliario.imovel
            WHERE 
                inscricao_municipal = inInscricao;
        ELSIF ( inDados = 4 ) THEN
            SELECT
                coalesce(sm.nom_municipio, ' ')||' - '||
                coalesce(su.nom_uf, ' ')
            INTO
                stRetorno
            FROM                                                                   
                (   SELECT * FROM
                    imobiliario.imovel
                    WHERE inscricao_municipal = inInscricao
                ) i,                                              
                imobiliario.imovel_lote il,                                
                imobiliario.lote ilot,
                imobiliario.lote_bairro ilb,
                sw_municipio sm,
                sw_uf su

            WHERE
                il.inscricao_municipal  = i.inscricao_municipal     AND
                ilot.cod_lote = il.cod_lote                         AND
                ilot.cod_lote = ilb.cod_lote                        AND
                sm.cod_uf = ilb.cod_uf                              AND
                sm.cod_municipio = ilb.cod_municipio                AND
                su.cod_uf = ilb.cod_uf                              AND
                i.inscricao_municipal = inInscricao 

            ORDER BY ilot.timestamp, ilb.timestamp DESC LIMIT 1;
        ELSIF ( inDados = 5 ) THEN
            SELECT
                (
                    SELECT 
                        tmp_il.nom_localizacao 
                    FROM 
                        imobiliario.localizacao AS tmp_il 
                    INNER JOIN 
                        imobiliario.localizacao_nivel AS tmp_iln 
                    ON  
                        tmp_il.codigo_composto = tmp_iln.valor || '.00' 
                        AND tmp_iln.cod_localizacao = lote_localizacao.cod_localizacao 
                        AND tmp_iln.cod_nivel = 1
                )
            INTO
                stRetorno

            FROM
                imobiliario.imovel_lote

            INNER JOIN
                (
                    SELECT
                        max(tmp.timestamp) AS timestamp,
                        tmp.inscricao_municipal
                    FROM
                        imobiliario.imovel_lote AS tmp
                    GROUP BY
                        tmp.inscricao_municipal
                )AS iml
            ON
                iml.inscricao_municipal = imovel_lote.inscricao_municipal
                AND iml.timestamp = imovel_lote.timestamp

            INNER JOIN
                imobiliario.lote_localizacao
            ON
                lote_localizacao.cod_lote = imovel_lote.cod_lote

            WHERE
                imovel_lote.inscricao_municipal = inInscricao;
        
        ELSIF ( inDados = 6 ) THEN
            SELECT
                coalesce(imovel.numero, ' ')
            INTO
                stRetorno 
            FROM
                imobiliario.imovel
            WHERE
                inscricao_municipal = inInscricao;
                
        END IF;
    ELSIF ( inTipo = 2 ) THEN --inscricao economica
            SELECT tipo 
                    INTO stTipo
                    FROM (   select inscricao_economica
                                    , timestamp
                                    , 'informado' as tipo 
                                from economico.domicilio_informado 
                                where inscricao_economica = inInscricao
                        union select inscricao_economica
                                    , timestamp
                                    , 'fiscal' as tipo 
                                from economico.domicilio_fiscal 
                                where inscricao_economica = inInscricao
                            order by timestamp desc limit 1
                        ) as res;
            
                if stTipo = 'fiscal' then
                    SELECT 
                        inscricao_municipal 
                    INTO 
                        inImovel 
                    FROM 
                        economico.domicilio_fiscal 
                    where 
                        inscricao_economica= inInscricao ORDER BY timestamp DESC LIMIT 1;

                    IF ( inDados = 5 ) THEN
                        SELECT
                            (
                                SELECT 
                                    tmp_il.nom_localizacao 
                                FROM 
                                    imobiliario.localizacao AS tmp_il 
                                INNER JOIN 
                                    imobiliario.localizacao_nivel AS tmp_iln 
                                ON  
                                    tmp_il.codigo_composto = tmp_iln.valor || '.00' 
                                    AND tmp_iln.cod_localizacao = lote_localizacao.cod_localizacao 
                                    AND tmp_iln.cod_nivel = 1
                            )
                        INTO
                            stRetorno

                        FROM
                            imobiliario.imovel_lote
            
                        INNER JOIN
                            (
                                SELECT
                                    max(tmp.timestamp) AS timestamp,
                                    tmp.inscricao_municipal
                                FROM
                                    imobiliario.imovel_lote AS tmp
                                GROUP BY
                                    tmp.inscricao_municipal
                            )AS iml
                        ON
                            iml.inscricao_municipal = imovel_lote.inscricao_municipal
                            AND iml.timestamp = imovel_lote.timestamp
            
                        INNER JOIN
                            imobiliario.lote_localizacao
                        ON
                            lote_localizacao.cod_lote = imovel_lote.cod_lote
            
                        WHERE
                            imovel_lote.inscricao_municipal = inImovel;
                    ELSE
                        stRetorno := arrecadacao.fn_consulta_endereco_todos(inImovel, 1, inDados);
                        if stRetorno is null then
                            stRetorno := 'Endereço Inválido!';
                        end if;
                    END IF;
                elsif stTipo = 'informado' then    
                    IF ( inDados = 1 ) THEN
                        SELECT                     
                            coalesce(tl.nom_tipo,' ')         ||' '||
                            coalesce(nl.nom_logradouro,' ')   ||' '||
                            coalesce(ltrim(di.numero,'0'),' ')  ||' '||
                            coalesce(di.complemento,' ')  
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
                            coalesce(sb.nom_bairro, ' ')
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
                            coalesce(di.cep, ' ')
                        INTO
                            stRetorno
                        FROM                                                                   
                            economico.domicilio_informado di
                        WHERE                                                                  
                            di.inscricao_economica  = inInscricao
                        ORDER BY di.timestamp DESC limit 1;
                    ELSIF ( inDados = 4 ) THEN
                        SELECT
                            coalesce(sm.nom_municipio, ' ')||' - '||coalesce(su.nom_uf, ' ')
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
                            coalesce(di.numero, ' ')
                        INTO
                            stRetorno
                        FROM
                            economico.domicilio_informado di
                        WHERE                                   
                            di.inscricao_economica  = inInscricao
                        ORDER BY di.timestamp DESC limit 1;                        
                        
                    END IF;
                else
                    stRetorno := 'Não Encontrado';
                end if;
        ELSIF ( inTipo = 3 ) THEN --cgm
            IF ( inDados = 1 ) THEN
                SELECT
                    coalesce(cgm.tipo_logradouro, ' ')    ||' '||
                    coalesce(cgm.logradouro, ' ')         ||' '||
                    coalesce(ltrim(cgm.numero, '0' ), ' ')  ||' '||
                    coalesce(cgm.complemento, ' ')
                INTO
                    stRetorno
                FROM                                                                   
                    sw_cgm cgm
                WHERE                                                                  
                    cgm.numcgm = inInscricao ;
            ELSIF ( inDados = 2 ) THEN
                SELECT
                    coalesce(cgm.bairro, ' ')
                INTO
                    stRetorno
                FROM                                                                   
                    sw_cgm cgm
                WHERE                                                                  
                    cgm.numcgm = inInscricao ;
            ELSIF ( inDados = 3 ) THEN
                SELECT
                    coalesce(cgm.cep, ' ')
                INTO
                    stRetorno
                FROM                                                                   
                    sw_cgm cgm
                WHERE                                                                  
                    cgm.numcgm = inInscricao ;
            ELSIF ( inDados = 4 ) THEN
                SELECT
                    coalesce(sm.nom_municipio, ' ')||' - '||coalesce(su.nom_uf, ' ')
                INTO
                    stRetorno
                FROM                                                                   
                    sw_cgm cgm,
                    sw_municipio sm,
                    sw_uf su
                WHERE
                    sm.cod_uf = cgm.cod_uf                     AND
                    cgm.cod_uf = su.cod_uf                     AND
                    cgm.cod_municipio = sm.cod_municipio       AND
                    cgm.numcgm = inInscricao ;
                    
            ELSIF ( inDados = 6 ) THEN
                SELECT
                    coalesce(cgm.numero, ' ')
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
$$ LANGUAGE 'plpgsql';
