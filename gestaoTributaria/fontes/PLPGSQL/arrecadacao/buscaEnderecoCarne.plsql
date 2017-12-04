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
* $Id: buscaEnderecoCarne.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-5.3.19
* Caso de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.3  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_busca_endereco_carne(INTEGER, INTEGER, INTEGER)  RETURNS varchar[] AS $$
DECLARE
    inImovel        ALIAS FOR $1;
    inExercicio     ALIAS FOR $2;
    inLancamento    ALIAS FOR $3;
    inCodAdquirente integer;
    stOrdemEntrega  varchar;
    stEntrega1      varchar;
    stEntrega2      varchar;
    stEntrega3      varchar;
    stEntrega4      varchar;
    imob_corresp    integer := 0;
    stSql           varchar;
    tmp             varchar;
    stRetorno       varchar[] := array[0];
    reRecord        record;
    inTipoEnd       integer := 1;
    
BEGIN
        select valor 
          into stOrdemEntrega
          from administracao.configuracao
         where exercicio = inExercicio::varchar
           and cod_modulo= 12
           and parametro = 'ordem_entrega'; 

    stEntrega1 := split_part(stOrdemEntrega,',',2);
    stEntrega1 := substring(stEntrega1,3,(length(stEntrega1)-4 ));
    stEntrega2 := split_part(stOrdemEntrega,',',4);
    stEntrega2 := substring(stEntrega2,3,(length(stEntrega2)-4 ));
    stEntrega3 := split_part(stOrdemEntrega,',',6);
    stEntrega3 := substring(stEntrega3,3,(length(stEntrega3)-4 ));
    stEntrega4 := split_part(stOrdemEntrega,',',8);
    stEntrega4 := substring(stEntrega4,3,(length(stEntrega4)-4 ));

if ( position ('entrega' in stEntrega1) > 0 ) then -- se for endereço de entrega verifica se existe
    select inscricao_municipal::varchar into tmp from imobiliario.imovel_correspondencia where inscricao_municipal= inImovel;
    
    if NOT FOUND then
        inTipoEnd := 2;
    else  -- se for end de entrega ... busca ele
        stSql :=  '    select (tl.nom_tipo||'' ''|| nl.nom_logradouro) as logradouro, ic.numero, ic.cep,ic.complemento,ba.nom_bairro as bairro ,
                        mu.nom_municipio as municipio,uf.sigla_uf as uf from imobiliario.imovel_correspondencia ic inner join sw_uf uf on uf.cod_uf =  
                        ic.cod_uf inner join sw_municipio mu on mu.cod_municipio = ic.cod_municipio and mu.cod_uf = uf.cod_uf inner
                        join sw_bairro ba on ba.cod_bairro=ic.cod_bairro inner join sw_nome_logradouro nl on nl.cod_logradouro =
                        ic.cod_logradouro inner join sw_tipo_logradouro tl on tl.cod_tipo = nl.cod_tipo where inscricao_municipal='||inImovel; 
    end if;
elsif (position ('promitente' in stEntrega1) > 0) or ( inTipoEnd = 2 and position ('promitente' in stEntrega2 ) > 0)then
    select inscricao_municipal::varchar from imobiliario.proprietario where inscricao_municipal=inImovel and promitente = TRUE;
    if NOT FOUND then
        if inTipoEnd = 2 then
            inTipoEnd := 3;
        else 
            inTipoEnd := 2;
        end if;
    end if;
    else
        stSql := ' select cgm.logradouro::varchar, cgm.numero::varchar, cgm.bairro::varchar,cgm.complemento::varchar, cgm.cep::varchar, mu.nom_municipio::varchar as municipio
                    , uf.sigla_uf::varchar as uf from sw_cgm cgm inner join sw_uf uf on uf.cod_uf = cgm.cod_uf  inner join sw_municipio mu 
                    on mu.cod_municipio = cgm.cod_municipio and mu.cod_uf = uf.cod_uf  where numcgm in (select
                    numcgm from imobiliario.proprietario where inscricao_municipal='||inImovel||' and promitente = TRUE limit 1)';
end if;


if ( inTipoEnd = 1) then
    if (position ('propriet' in stEntrega1) > 0) then
        tmp := 'proprietario'; 
    elsif (position ('ndereço do imóvel' in stEntrega1) > 0) then
        tmp := 'imovel';
    end if;
    
elsif ( inTipoEnd = 2 ) then
    if (position ('propriet' in stEntrega2) > 0) then
        tmp := 'proprietario'; 
    elsif (position ('ndereço do imóvel' in stEntrega2) > 0) then
        tmp := 'imovel';
    end if;
    
elsif ( inTipoEnd = 3 ) then
    if (position ('propriet' in stEntrega3) > 0 )then
        tmp := 'proprietario'; 
    elsif (position ('imóvel' in stEntrega3) > 0 ) then
        tmp := 'ndereço do imóvel';
    end if;
else
    --Verifica se o imóvel tem endereco cadastrado para entrega
    SELECT COUNT(*) INTO imob_corresp FROM imobiliario.imovel_correspondencia WHERE inscricao_municipal = inImovel;

    if(imob_corresp > 0) THEN
        tmp := 'imovel_correspondencia';
    END if;
end if;

if ( tmp = 'proprietario') then
        stSql := ' select cgm.logradouro::varchar, cgm.numero::varchar,cgm.bairro::varchar,cgm.complemento::varchar, cgm.cep::varchar, mu.nom_municipio::varchar as municipio
                    , uf.sigla_uf::varchar as uf from sw_cgm cgm inner join sw_uf uf on uf.cod_uf = cgm.cod_uf  inner join sw_municipio mu 
                    on mu.cod_municipio = cgm.cod_municipio and mu.cod_uf = uf.cod_uf  where numcgm in (select
                    numcgm from imobiliario.proprietario where inscricao_municipal='||inImovel||' limit 1)';

elsif ( tmp = 'imovel') then
    stSql := '
        SELECT                     
            (tl.nom_tipo||''''''''||nl.nom_logradouro)::varchar as logradouro,
            COALESCE(i.numero, '''''''')::varchar  as numero ,
            COALESCE(i.complemento, '''''''')::varchar as complemento,
            COALESCE(ba.nom_bairro, '''''''')::varchar as bairro ,
            COALESCE(i.cep, '''''''')::varchar as cep      ,
            COALESCE(uf.sigla_uf, '''''''')::varchar as uf,
            COALESCE(mu.nom_municipio, '''''''')::varchar as municipio  
        FROM                                                                   
            (   SELECT * FROM
                imobiliario.imovel
                WHERE inscricao_municipal = '''||inImovel||'''
            ) i
             inner join imobiliario.imovel_lote as iil 
                     on i.inscricao_municipal = iil.inscricao_municipal
             inner join imobiliario.lote_bairro ilb
                     on iil.cod_lote = ilb.cod_lote
             inner join sw_uf uf
                     on ilb.cod_uf              = uf.cod_uf
             inner join sw_municipio mu
                     on mu.cod_uf               = uf.cod_uf
             inner join sw_bairro ba
                     on ilb.cod_municipio       = mu.cod_municipio
                    and ilb.cod_bairro          = ba.cod_bairro,                                              
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
            i.inscricao_municipal   = '||inImovel;
            
elsif ( tmp = 'imovel_correspondencia') then
    stSql := '
               SELECT   COALESCE(imobiliario.imovel_correspondencia.cep, '''''''') as cep
                      , COALESCE(imobiliario.imovel_correspondencia.numero, '''''''') as numero
                      , COALESCE(imobiliario.imovel_correspondencia.complemento, '''''''') as complemento
                      , COALESCE(bairro.nom_bairro, '''''''') 	  AS bairro
                      , COALESCE(uf.nom_uf, '''''''') 		  AS uf
                      , COALESCE(municipio.nom_municipio, '''''''')   AS municipio
                      , COALESCE(logradouro.nom_logradouro, '''''''') AS logradouro
	      
                FROM imobiliario.imovel_correspondencia 
               
          INNER JOIN sw_uf AS uf
                  ON uf.cod_uf = imovel_correspondencia.cod_uf
       
          INNER JOIN sw_municipio AS municipio
                  ON municipio.cod_uf = uf.cod_uf
       
          INNER JOIN sw_bairro AS bairro	   	   
                 ON bairro.cod_bairro    = imovel_correspondencia.cod_bairro
                AND bairro.cod_municipio = municipio.cod_municipio
       
          INNER JOIN sw_bairro_logradouro AS bairro_lograoduro   	   
                 ON bairro_lograoduro.cod_uf         = imovel_correspondencia.cod_uf
                AND bairro_lograoduro.cod_municipio  = imovel_correspondencia.cod_municipio         
                AND bairro_lograoduro.cod_bairro     = imovel_correspondencia.cod_bairro
                AND bairro_lograoduro.cod_logradouro = imovel_correspondencia.cod_logradouro
       
          INNER JOIN sw_nome_logradouro AS logradouro
                  ON logradouro.cod_logradouro = imovel_correspondencia.cod_logradouro
                  
               WHERE imovel_correspondencia.inscricao_municipal = '||inImovel ;
    
end if;

    SELECT
           ta.numcgm
      INTO
           inCodAdquirente
      FROM arrecadacao.lancamento_calculo AS lc
      JOIN arrecadacao.calculo AS ac
        ON ac.cod_calculo = lc.cod_calculo
      JOIN imobiliario.transferencia_imovel AS ti
        ON ti.dt_cadastro = ac.timestamp
      JOIN imobiliario.transferencia_adquirente AS ta
        ON ta.cod_transferencia = ti.cod_transferencia
     WHERE ti.inscricao_municipal = inImovel  AND lc.cod_lancamento = inLancamento;
     
if (inCodAdquirente::varchar <> '') then
    stSql := '
                SELECT  cgm.logradouro::varchar,
                        cgm.numero::varchar,
                        cgm.bairro::varchar,
                        cgm.complemento::varchar,
                        cgm.cep::varchar,
                        municipio.nom_municipio::varchar as municipio,
                        uf.sigla_uf::varchar as uf
                        
                  FROM sw_cgm AS cgm
                  
                  JOIN sw_municipio AS municipio
                    ON municipio.cod_uf = cgm.cod_uf
                   AND municipio.cod_municipio = cgm.cod_municipio
                  
                  JOIN sw_uf AS uf
                    ON uf.cod_uf = municipio.cod_uf
                    
                  JOIN sw_bairro AS bairro	   	   
                    ON bairro.cod_municipio = municipio.cod_municipio
                   AND bairro.cod_uf = municipio.cod_uf
                   
                  JOIN sw_logradouro AS logradouro
                    ON logradouro.cod_uf = municipio.cod_uf
                   AND logradouro.cod_municipio = municipio.cod_municipio
                    
                  JOIN sw_nome_logradouro AS nome_logradouro
                    ON nome_logradouro.cod_logradouro = logradouro.cod_logradouro
                   
                  JOIN sw_bairro_logradouro AS bairro_logradouro   	   
                    ON bairro_logradouro.cod_uf         = bairro.cod_uf
                   AND bairro_logradouro.cod_municipio  = bairro.cod_municipio         
                   AND bairro_logradouro.cod_bairro     = bairro.cod_bairro
                    
                 WHERE cgm.numcgm = '||inCodAdquirente||' LIMIT 1';
end if;

--RAISE NOTICE 'Debug: %', stSql;

    FOR reRecord IN EXECUTE stSql LOOP
        stRetorno[1] := reRecord.logradouro;
        stRetorno[2] := reRecord.numero;    
        stRetorno[3] := reRecord.complemento;
        stRetorno[4] := reRecord.bairro;    
        stRetorno[5] := reRecord.cep;       
        stRetorno[6] := reRecord.uf;        
        stRetorno[7] := reRecord.municipio;    
    END LOOP;

    RETURN stRetorno; 
END;
$$ LANGUAGE 'plpgsql';
