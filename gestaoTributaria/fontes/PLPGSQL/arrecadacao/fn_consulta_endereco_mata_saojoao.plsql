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
* $Id: fn_consulta_endereco_mata_saojoao.plsql 63867 2015-10-27 17:25:14Z evandro $
*
* Caso de uso: uc-05.03.11
*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_consulta_endereco_mata_saojoao( INTEGER , VARCHAR )  RETURNS varchar AS $$
DECLARE
    inImovel                        ALIAS FOR $1;
    stExercicio                     ALIAS FOR $2;
    stRetorno                       VARCHAR:='';
    arOrdemEntrega                  VARCHAR[];     
    stCodOrdem                      VARCHAR:='';
    stEnderecoCorrespondenciaImovel VARCHAR:='';
    stEnderecoImovel                VARCHAR:='';
    stEnderecoCgm                   VARCHAR:='';
    
BEGIN
    --BUSCANDO A ORDEM E SEU CODIGO PARA DEFINIR QUAL CONSULTA SERA EXECUTADA
    SELECT   ordem as cod_ordem_entrega
            
    INTO arOrdemEntrega
    
    FROM (  SELECT string_to_array(REPLACE(regexp_replace(valor,'({|}|")','','gi'),',','~'),'~') as ordem
              FROM administracao.configuracao 
             WHERE parametro = 'ordem_entrega' 
               AND exercicio = stExercicio
    )as foo;

    --imcc => endereco correspondencia do imovel
    --imv => endereco do imovel
    --ip => endereco do cgm
    SELECT  
            imcc.endereco AS endereco_correspondencia_imovel
            ,imv.endereco AS endereco_imovel
            ,ip.endereco  AS endereco_cgm
    
    INTO    stEnderecoCorrespondenciaImovel
            ,stEnderecoImovel
            ,stEnderecoCgm
    FROM
        imobiliario.imovel AS ii
     
    LEFT JOIN (
        SELECT
            IMC.inscricao_municipal,
            TL.nom_tipo||'§'||
            IMC.cod_logradouro||'§'||
            LOGRN.nom_logradouro||'§'||
            IMC.numero||'§'||
            IMC.complemento||'§'||
            BAIRRO.nom_bairro||'§'||
            IMC.cep||'§'||
            MUN.cod_municipio||'§'||
            MUN.nom_municipio||'§'||
            UF.cod_uf||'§'||
            UF.sigla_uf
            AS endereco,
            MUN.cod_municipio,
            UF.cod_uf,
            IMC.timestamp
    
        FROM
            imobiliario.imovel_correspondencia  as IMC
    
        INNER JOIN 
            sw_uf as UF
        ON 
            UF.cod_uf = IMC.cod_UF
    
        INNER JOIN 
            sw_municipio as MUN
        ON 
            MUN.cod_uf = UF.cod_UF
            AND MUN.cod_municipio = IMC.cod_municipio
    
        INNER JOIN 
            sw_bairro as BAIRRO
        ON 
            BAIRRO.cod_uf = UF.cod_UF
            AND BAIRRO.cod_municipio = MUN.cod_municipio
            AND BAIRRO.cod_bairro = IMC.cod_bairro
    
        INNER JOIN 
            sw_logradouro as LOGR
        ON 
            LOGR.cod_logradouro = IMC.cod_logradouro
            AND LOGR.cod_municipio = IMC.cod_municipio
            AND LOGR.cod_uf = IMC.cod_uf
    
        INNER JOIN 
            sw_nome_logradouro as LOGRN
        ON 
            LOGRN.cod_logradouro = LOGR.cod_logradouro
    
        INNER JOIN 
            sw_tipo_logradouro as TL
        ON 
            TL.cod_tipo = LOGRN.cod_tipo
    
    ) as imcc
    ON
        imcc.inscricao_municipal = ii.inscricao_municipal
    
    LEFT JOIN (
        SELECT
            i.inscricao_municipal,
            TL.nom_tipo||'§'||
            l.cod_logradouro||'§'||
            nl.nom_logradouro||'§'||
            i.numero||'§'||
            i.complemento||'§'||
            bairro.nom_bairro||'§'||
            i.cep||'§'||
            mun.cod_municipio||'§'||
            mun.nom_municipio||'§'||
            uf.cod_uf||'§'||
            uf.sigla_uf
            AS endereco,
            uf.cod_uf,
            mun.cod_municipio
    
        FROM
            imobiliario.imovel AS i
    
        INNER JOIN
            imobiliario.imovel_confrontacao ic
        ON
            ic.inscricao_municipal = i.inscricao_municipal
    
        INNER JOIN 
            imobiliario.confrontacao_trecho ct
        ON 
            ct.cod_confrontacao  = ic.cod_confrontacao AND
            ct.cod_lote             = ic.cod_lote
    
        INNER JOIN 
            imobiliario.trecho t
        ON 
            t.cod_trecho     = ct.cod_trecho     AND
            t.cod_logradouro    = ct.cod_logradouro
    
        INNER JOIN 
            sw_logradouro l
        ON 
            l.cod_logradouro = t.cod_logradouro
    
        INNER JOIN 
            sw_nome_logradouro nl
        ON 
            nl.cod_logradouro = l.cod_logradouro
                                            
        INNER JOIN 
            sw_tipo_logradouro tl
        ON 
            tl.cod_tipo       = nl.cod_tipo
    
        INNER JOIN 
            imobiliario.lote_bairro as ilb
        ON 
            ilb.cod_lote = ic.cod_lote
            AND ilb.cod_municipio = l.cod_municipio
            AND ilb.cod_uf = l.cod_uf
    
        INNER JOIN 
            sw_bairro as bairro
        ON 
            bairro.cod_bairro = ilb.cod_bairro
            AND bairro.cod_municipio = l.cod_municipio
            AND bairro.cod_uf = l.cod_uf
    
        INNER JOIN 
            sw_municipio as mun
        ON 
            mun.cod_municipio = l.cod_municipio
            AND mun.cod_uf = l.cod_uf
    
        INNER JOIN 
            sw_uf as uf
        ON 
            uf.cod_uf = mun.cod_uf
    )AS imv
    ON
        imv.inscricao_municipal = ii.inscricao_municipal
    
    LEFT JOIN (
        SELECT
            ip.inscricao_municipal,
            cgm.tipo_logradouro_corresp||'§'||
            sw_cgm_logradouro_correspondencia.cod_logradouro||'§'||
            cgm.logradouro_corresp||'§'||
            cgm.numero_corresp||'§'||
            cgm.complemento_corresp||'§'||
            cgm.bairro_corresp||'§'||
            cgm.cep_corresp||'§'||
            MUN.cod_municipio||'§'||
            MUN.nom_municipio||'§'||
            UF.cod_uf||'§'||
            UF.sigla_uf
            AS endereco,
            UF.cod_uf,
            MUN.cod_municipio,
            ip.timestamp
    
        FROM
            imobiliario.proprietario  as ip
    
        INNER JOIN
            sw_cgm AS cgm
        ON
            cgm.numcgm = ip.numcgm
    
        INNER JOIN 
            sw_uf as UF
        ON 
            UF.cod_uf = cgm.cod_uf_corresp
    
        INNER JOIN 
            sw_municipio as MUN
        ON 
            MUN.cod_uf = UF.cod_uf
            AND MUN.cod_municipio = cgm.cod_municipio_corresp
        
        LEFT JOIN sw_cgm_logradouro_correspondencia
            ON sw_cgm_logradouro_correspondencia.numcgm = cgm.numcgm

    ) as ip
    ON
        ip.inscricao_municipal = ii.inscricao_municipal
    
    WHERE                                                                  
        ii.inscricao_municipal = inImovel
    
    ORDER BY
        imcc.timestamp desc
    LIMIT 1;

    --Loop para atribuir o valor do endereco de acordo com a ordem setada na configuracao
    FOREACH stCodOrdem IN ARRAY arOrdemEntrega
    LOOP
        CASE stCodOrdem
            WHEN '4' THEN stRetorno:= stEnderecoCorrespondenciaImovel;
            WHEN '3' THEN stRetorno:= stEnderecoImovel;
            WHEN '2','1' THEN stRetorno:= stEnderecoCgm;
            ELSE
        END CASE;
        EXIT WHEN stRetorno <> '';
    END LOOP;

    RETURN stRetorno;
END;
$$ LANGUAGE 'plpgsql';
