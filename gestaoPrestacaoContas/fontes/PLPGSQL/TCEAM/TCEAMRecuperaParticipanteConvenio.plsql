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

CREATE OR REPLACE  FUNCTION tceam.recupera_participante_convenio(character varying, character varying) RETURNS SETOF record AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stMes               ALIAS FOR $2;
        
    stSql               VARCHAR   := '';
    reRegistro          RECORD;
    reAuxiliar          RECORD;
    reFinal             RECORD;
BEGIN

    stSql := '
            CREATE TABLE tmp_participante_convenio AS(
                SELECT CASE WHEN (sw_cgm_pessoa_fisica.numcgm = participante_convenio.cgm_fornecedor)   THEN sw_cgm_pessoa_fisica.cpf 
                            WHEN (sw_cgm_pessoa_juridica.numcgm = participante_convenio.cgm_fornecedor) THEN sw_cgm_pessoa_juridica.cnpj
                        END AS cod_cic_psticipante
                      , CASE WHEN (sw_cgm_pessoa_fisica.numcgm = participante_convenio.cgm_fornecedor)   THEN 1
                             WHEN (sw_cgm_pessoa_juridica.numcgm = participante_convenio.cgm_fornecedor) THEN 2
                             ELSE 3
                        END AS tp_pessoa_participante
                      , sw_cgm.nom_cgm AS nm_participante
                      , REPLACE((participante_convenio.valor_participacao::numeric(14,2))::text, ''.'', '','') AS vl_participacao
                      , participante_convenio.percentual_participacao AS vl_percentual_participacao
                      , ''''::VARCHAR AS nu_certidao_casan 
                      , ''''::VARCHAR AS data_certidao_casan
                      , ''''::VARCHAR AS dt_validadecertidao_casan
                      , ''''::VARCHAR AS nu_certidao_celesc
                      , ''''::VARCHAR AS dt_certidao_celesc
                      , ''''::VARCHAR AS dt_validade_certidao_celesc
                      , ''''::VARCHAR AS nu_certidao_ipesc
                      , ''''::VARCHAR AS dt_certidao_ipesc
                      , ''''::VARCHAR AS dt_validade_certidao_ipesc
                      , ''''::VARCHAR AS nu_certidao_fazenda_municipal
                      , ''''::VARCHAR AS dt_certidao_fazenda_municipal
                      , ''''::VARCHAR AS dt_validade_certidao_fazenda_municipal
                      , ''''::VARCHAR AS nu_certidao_fazenda_federal
                      , ''''::VARCHAR AS dt_certidao_fazenda_federal
                      , ''''::VARCHAR AS dt_validade_certidao_fazenda_federal
                      , ''''::VARCHAR AS nu_certidao_cndt
                      , ''''::VARCHAR AS dt_certidao_cndt
                      , ''''::VARCHAR AS dt_validade_certidao_cndt
                      , ''''::VARCHAR AS nu_certidao_outras
                      , ''''::VARCHAR AS dt_certidao_outras
                      , ''''::VARCHAR AS dt_validade_certidao_outras
                      , convenio.num_convenio
                      , CASE WHEN (esfera_convenio.esfera = ''E'') THEN 1
                             WHEN (esfera_convenio.esfera = ''F'') THEN 2
                             WHEN (esfera_convenio.esfera = ''M'') THEN 3
                             WHEN (esfera_convenio.esfera = ''G'') THEN 4
                             WHEN (esfera_convenio.esfera = ''O'') THEN 5
                      END AS tp_esferaconvenio
                     , participante_certificacao.num_certificacao
                     , participante_certificacao.exercicio
                     , participante_certificacao.cgm_fornecedor
                     
                  FROM licitacao.participante_convenio
            
            INNER JOIN licitacao.convenio
                    ON convenio.num_convenio = participante_convenio.num_convenio
                   AND convenio.exercicio = participante_convenio.exercicio
                   
             LEFT JOIN tceam.esfera_convenio
                    ON esfera_convenio.num_convenio = convenio.num_convenio
                   AND esfera_convenio.exercicio    = convenio.exercicio
            
            INNER JOIN sw_cgm
                    ON sw_cgm.numcgm = participante_convenio.cgm_fornecedor
             
             LEFT JOIN sw_cgm_pessoa_fisica
                    ON sw_cgm_pessoa_fisica.numcgm = participante_convenio.cgm_fornecedor
                    
             LEFT JOIN sw_cgm_pessoa_juridica
                    ON sw_cgm_pessoa_juridica.numcgm = participante_convenio.cgm_fornecedor
    
            INNER JOIN licitacao.participante_certificacao
                    ON participante_certificacao.num_certificacao = participante_convenio.num_certificacao
                   AND participante_certificacao.exercicio        = participante_convenio.exercicio_certificacao
                   AND participante_certificacao.cgm_fornecedor   = participante_convenio.cgm_fornecedor
             
                 WHERE convenio.exercicio                     = ' || quote_literal(stExercicio)  || '
                   AND to_char(convenio.dt_assinatura,''mm'') = ' || quote_literal(stMes) || '
            )';
            
    EXECUTE stSql;
   
    stSql := 'SELECT * FROM tmp_participante_convenio;';
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
        
         stSql := 'SELECT certificacao_documentos.cod_documento
                        , certificacao_documentos.num_documento
                        , TO_CHAR(certificacao_documentos.dt_emissao, ''YYYYMMDD'') AS dt_emissao
                        , TO_CHAR(certificacao_documentos.dt_validade, ''YYYYMMDD'') AS dt_validade
                     FROM licitacao.certificacao_documentos

               INNER JOIN licitacao.documento
                       ON documento.cod_documento = certificacao_documentos.cod_documento

               INNER JOIN tceam.tipo_certidao_documento 
                       ON tipo_certidao_documento.cod_documento = documento.cod_documento

                    WHERE num_certificacao = ' || reRegistro.num_certificacao || '
                      AND exercicio        = ' || quote_literal(reRegistro.exercicio) || '
                      AND cgm_fornecedor   = ' || reRegistro.cgm_fornecedor ;
        
         FOR reAuxiliar IN EXECUTE stSql
         LOOP
           
            CASE reAuxiliar.cod_documento
                
                --CERTIDÃO FEDERAL
                WHEN 2 THEN
                
                     UPDATE tmp_participante_convenio
                        SET nu_certidao_fazenda_federal          = reAuxiliar.num_documento 
                          , dt_certidao_fazenda_federal          = reAuxiliar.dt_emissao 
                          , dt_validade_certidao_fazenda_federal = reAuxiliar.dt_validade
                      WHERE num_certificacao = reRegistro.num_certificacao
                        AND exercicio        = reRegistro.exercicio 
                        AND cgm_fornecedor   = reRegistro.cgm_fornecedor;
                        
                        
                --CERTIDÃO ESTADUAL (IPESC)
                WHEN 3 THEN

                     UPDATE tmp_participante_convenio
                        SET nu_certidao_ipesc          = reAuxiliar.num_documento 
                          , dt_certidao_ipesc          = reAuxiliar.dt_emissao 
                          , dt_validade_certidao_ipesc = reAuxiliar.dt_validade
                      WHERE num_certificacao = reRegistro.num_certificacao
                        AND exercicio        = reRegistro.exercicio 
                        AND cgm_fornecedor   = reRegistro.cgm_fornecedor;   

                --CERTIDÃO MUNICIPAL
                WHEN 4 THEN
                
                     UPDATE tmp_participante_convenio
                        SET nu_certidao_fazenda_municipal          = reAuxiliar.num_documento 
                          , dt_certidao_fazenda_municipal          = reAuxiliar.dt_emissao 
                          , dt_validade_certidao_fazenda_municipal = reAuxiliar.dt_validade
                      WHERE num_certificacao = reRegistro.num_certificacao
                        AND exercicio        = reRegistro.exercicio 
                        AND cgm_fornecedor   = reRegistro.cgm_fornecedor;   
                
                --INSS (CASAN)
                WHEN 5 THEN
                
                     UPDATE tmp_participante_convenio
                        SET nu_certidao_casan         = reAuxiliar.num_documento 
                          , data_certidao_casan       = reAuxiliar.dt_emissao 
                          , dt_validadecertidao_casan = reAuxiliar.dt_validade
                      WHERE num_certificacao = reRegistro.num_certificacao
                        AND exercicio        = reRegistro.exercicio 
                        AND cgm_fornecedor   = reRegistro.cgm_fornecedor;
                
                --FGTS (CELESC)
                WHEN 6 THEN
                
                     UPDATE tmp_participante_convenio
                        SET nu_certidao_celesc          = reAuxiliar.num_documento 
                          , dt_certidao_celesc          = reAuxiliar.dt_emissao 
                          , dt_validade_certidao_celesc = reAuxiliar.dt_validade
                      WHERE num_certificacao = reRegistro.num_certificacao
                        AND exercicio        = reRegistro.exercicio 
                        AND cgm_fornecedor   = reRegistro.cgm_fornecedor;
                        
                --CERTIDÃO TRABALHISTA (CNDT)
                WHEN 7 THEN
                
                     UPDATE tmp_participante_convenio
                        SET nu_certidao_cndt          = reAuxiliar.num_documento 
                          , dt_certidao_cndt          = reAuxiliar.dt_emissao 
                          , dt_validade_certidao_cndt = reAuxiliar.dt_validade
                      WHERE num_certificacao = reRegistro.num_certificacao
                        AND exercicio        = reRegistro.exercicio 
                        AND cgm_fornecedor   = reRegistro.cgm_fornecedor;
                
                ELSE
                
                     UPDATE tmp_participante_convenio
                        SET nu_certidao_outras          = reAuxiliar.num_documento 
                          , dt_certidao_outras          = reAuxiliar.dt_emissao 
                          , dt_validade_certidao_outras = reAuxiliar.dt_validade
                      WHERE num_certificacao = reRegistro.num_certificacao
                        AND exercicio        = reRegistro.exercicio 
                        AND cgm_fornecedor   = reRegistro.cgm_fornecedor;
                
                END CASE;
   
         END LOOP;
    END LOOP;
        
    stSql := 'SELECT * FROM tmp_participante_convenio;';
    
    FOR reFinal IN EXECUTE stSql
    LOOP
     RETURN next reFinal;
    END LOOP;

    DROP TABLE tmp_participante_convenio;

    RETURN;
   
END;
$$ LANGUAGE 'plpgsql';