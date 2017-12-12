<?php
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
?>
<?php
/**
  * Classe de mapeamento da tabela ECONOMICO.CADASTRO_ECONOMICO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMCadastroEconomico.class.php 63376 2015-08-21 18:55:42Z arthur $

* Casos de uso: uc-05.02.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.CADASTRO_ECONOMICO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMCadastroEconomico extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMCadastroEconomico()
{
    parent::Persistente();
    $this->setTabela('economico.cadastro_economico');

    $this->setCampoCod('inscricao_economica');
    $this->setComplementoChave('');

    $this->AddCampo('inscricao_economica','integer',true,'',true,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);
    $this->AddCampo('dt_abertura','date',false,'',false,false);
}

function recuperaInscricaoBaixa(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaInscricaoBaixa().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    //$this->debug();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaInscricaoBaixa()
{
    $stSql ="SELECT  cgm.numcgm                                                                                       \n";
    $stSql.="       ,TO_CHAR(cadastro_economico.dt_abertura,'dd/mm/yyyy')                AS dt_abertura               \n";
    $stSql.="       ,TO_CHAR(baixa_cadastro_economico.dt_inicio,'dd/mm/yyyy')            AS dt_baixa                  \n";
    $stSql.="       ,CASE WHEN CAST( cadastro_economico_empresa_fato.numcgm AS VARCHAR ) IS NOT NULL                  \n";
    $stSql.="              THEN '1'                                                                                   \n";
    $stSql.="             WHEN CAST( cadastro_economico_autonomo.numcgm     AS VARCHAR ) IS NOT NULL                  \n";
    $stSql.="              THEN '3'                                                                                   \n";
    $stSql.="             WHEN CAST( cadastro_economico_empresa_direito.numcgm AS VARCHAR ) IS NOT NULL               \n";
    $stSql.="              THEN '2'                                                                                   \n";
    $stSql.="        END                                                                 AS enquadramento             \n";
    $stSql.="       ,cadastro_economico.inscricao_economica                                                           \n";
    $stSql.="       ,cadastro_economico.timestamp                                                                     \n";
    $stSql.="       ,cgm.nom_cgm                                                                                      \n";
    $stSql.="      ,economico.fn_busca_sociedade(cadastro_economico.inscricao_economica) AS sociedade                 \n";
    $stSql.=" FROM economico.baixa_cadastro_economico                                                                 \n";
    $stSql.="     ,sw_cgm AS cgm                                                                                      \n";
    $stSql.="     ,economico.cadastro_economico                                                                       \n";
    $stSql.="      LEFT JOIN economico.cadastro_economico_empresa_fato                                                \n";
    $stSql.="      ON cadastro_economico.inscricao_economica = cadastro_economico_empresa_fato.inscricao_economica    \n";
    $stSql.="      LEFT JOIN economico.cadastro_economico_autonomo                                                    \n";
    $stSql.="      ON cadastro_economico.inscricao_economica = cadastro_economico_autonomo.inscricao_economica        \n";
    $stSql.="      LEFT JOIN economico.cadastro_economico_empresa_direito                                             \n";
    $stSql.="      ON cadastro_economico.inscricao_economica = cadastro_economico_empresa_direito.inscricao_economica \n";
    $stSql.=" WHERE cadastro_economico.inscricao_economica = baixa_cadastro_economico.inscricao_economica             \n";
    $stSql.="   AND COALESCE( cadastro_economico_empresa_fato.numcgm                                                  \n";
    $stSql.="                ,cadastro_economico_empresa_direito.numcgm                                               \n";
    $stSql.="                ,cadastro_economico_autonomo.numcgm        ) = cgm.numcgm                                \n";
    $stSql.="   AND baixa_cadastro_economico.dt_termino IS NULL                                                       \n";

    return $stSql;
}

function recuperaInscricao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaInscricao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaInscricao()
{
    $stSql  = "   select distinct coalesce( ef.numcgm, ed.numcgm, au.numcgm )       as numcgm           \n";
    $stSql .= "        , ce.inscricao_economica                                                         \n";
    $stSql .= "        , ce.timestamp                                                                   \n";
    $stSql .= "        , TO_CHAR(ce.dt_abertura,'dd/mm/yyyy')                       as dt_abertura      \n";
    $stSql .= "        , cgm.nom_cgm                                                                    \n";
    $stSql .= "        , case                                                                           \n";
    $stSql .= "               when cast( ef.numcgm as varchar) is not null                              \n";
    $stSql .= "               then '1'                                                                  \n";
    $stSql .= "               when cast( au.numcgm as varchar) is not null                              \n";
    $stSql .= "               then '3'                                                                  \n";
    $stSql .= "               when cast( ed.numcgm as varchar) is not null                              \n";
    $stSql .= "               then '2'                                                                  \n";
    $stSql .= "          end                                                        as enquadramento    \n";
    $stSql .= "        , economico.fn_busca_sociedade(ce.inscricao_economica)       as sociedade        \n";
    $stSql .= "     FROM economico.cadastro_economico                               as ce               \n";
    $stSql .= "LEFT JOIN economico.cadastro_economico_empresa_fato                  as ef               \n";
    $stSql .= "       ON ce.inscricao_economica = ef.inscricao_economica                                \n";
    $stSql .= "LEFT JOIN economico.cadastro_economico_autonomo                      as au               \n";
    $stSql .= "       ON ce.inscricao_economica = au.inscricao_economica                                \n";
    $stSql .= "LEFT JOIN economico.cadastro_economico_empresa_direito               as ed               \n";
    $stSql .= "       ON ce.inscricao_economica = ed.inscricao_economica                                \n";
    $stSql .= "     LEFT JOIN (
                        SELECT
                            baixa_cadastro_economico.*
                        FROM
                            economico.baixa_cadastro_economico

                        INNER JOIN
                            (
                                SELECT
                                    max( timestamp ) as timestamp,
                                    inscricao_economica

                                FROM
                                    economico.baixa_cadastro_economico

                                GROUP BY
                                    inscricao_economica
                            )AS tmp
                        ON
                            tmp.inscricao_economica = baixa_cadastro_economico.inscricao_economica
                            AND tmp.timestamp = baixa_cadastro_economico.timestamp
                    ) as ba \n";
    $stSql .= "    ON ce.inscricao_economica = ba.inscricao_economica,                  \n";
    $stSql .= "    sw_cgm                                                     as cgm              \n";
    $stSql .= "    where coalesce( ef.numcgm, ed.numcgm, au.numcgm ) = cgm.numcgm                       \n";

    return $stSql;
}

function recuperaListaConsulta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaConsulta().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaConsulta()
{
    $stSQL  ="SELECT DISTINCT ON(CE.inscricao_economica )                            
                     CE.inscricao_economica                                         
                   , ACE.cod_atividade                                              
                   , A.nom_atividade        
                   , COALESCE ( A.cod_estrutural, '&nbsp;') AS cod_estrutural
                   , COALESCE ( CEED.numcgm, CEEF.numcgm, CEA.numcgm ) AS numcgm
                   , CGM.nom_cgm
                   , DF.inscricao_municipal
                   , EDNJ.cod_natureza
                   , CERC.numcgm as resp_contabil_cgm
                   , CERC.sequencia as resp_contabil_sequencia
                   , SOC.numcgm as cgm_socio
                   , to_char(BA.dt_inicio, 'dd/mm/yyyy') AS data_baixa
                   , to_char(BA.dt_termino, 'dd/mm/yyyy') AS data_reativacao
                   , el.cod_licenca         
                   , el.exercicio AS licenca_exercicio
                   , CASE WHEN (BA.dt_inicio IS NULL) OR (BA.dt_inicio IS NOT NULL AND BA.dt_termino IS NOT NULL)
                           THEN 'Ativo'
                           ELSE TBI.nom_tipo                                            
                     END AS situacao                                                  
                
                FROM economico.cadastro_economico CE                                  
           
           LEFT JOIN ( SELECT MAX(ocorrencia_atividade) AS ocorrencia_atividade        
                            , inscricao_economica                                     
                        FROM economico.atividade_cadastro_economico                   
                       WHERE principal = TRUE                                         
                    GROUP BY inscricao_economica                                      
                    ) ACE_MAX                                                    
                  ON CE.inscricao_economica = ACE_MAX.inscricao_economica
                  
           LEFT JOIN economico.atividade_cadastro_economico AS ACE                
                  ON ACE.ocorrencia_atividade = ACE_MAX.ocorrencia_atividade
                 AND ACE.inscricao_economica = ACE_MAX.inscricao_economica
                 AND ACE.inscricao_economica = CE.inscricao_economica             
                                                                                    
           LEFT JOIN economico.licenca_atividade ela
                  ON ACE.ocorrencia_atividade = ela.ocorrencia_atividade
                 AND ACE.inscricao_economica =  ela.inscricao_economica
                 AND ACE.cod_atividade = ela.cod_atividade                         
                                                                                   
           LEFT JOIN economico.licenca_especial ele
                  ON ACE.ocorrencia_atividade = ele.ocorrencia_atividade
                 AND ACE.inscricao_economica  = ele.inscricao_economica
                 AND ACE.cod_atividade        = ele.cod_atividade                  
           
           LEFT JOIN economico.licenca el
                  ON ela.cod_licenca = el.cod_licenca
                  OR ele.cod_licenca = el.cod_licenca                             
           
           LEFT JOIN economico.atividade A
                  ON A.cod_atividade = ACE.cod_atividade                          
           
           LEFT JOIN economico.cadastro_economico_empresa_direito CEED
                  ON CEED.inscricao_economica = CE.inscricao_economica            
           
           LEFT JOIN economico.cadastro_economico_empresa_fato CEEF
                  ON CEEF.inscricao_economica = CE.inscricao_economica            
           
           LEFT JOIN economico.cadastro_economico_autonomo CEA
                  ON CEA.inscricao_economica = CE.inscricao_economica             
           
           LEFT JOIN ( SELECT MAX(timestamp) AS timestamp                              
                            , inscricao_economica                                     
                        FROM economico.domicilio_fiscal                              
                    GROUP BY inscricao_economica                                      
                    ) AS DF_MAX                                                  
                  ON DF_MAX.inscricao_economica = CE.inscricao_economica          
           
           LEFT JOIN economico.domicilio_fiscal AS DF                             
                  ON DF.timestamp           = DF_MAX.timestamp
                 AND DF.inscricao_economica = DF_MAX.inscricao_economica
                 AND DF.inscricao_economica = CE.inscricao_economica              

           LEFT JOIN economico.empresa_direito_natureza_juridica EDNJ
                  ON EDNJ.inscricao_economica = CEED.inscricao_economica         
           
           LEFT JOIN economico.sociedade SOC
                  ON SOC.inscricao_economica = CEED.inscricao_economica          
           
           LEFT JOIN (  SELECT tmp.*                                                   
                          FROM economico.cadastro_econ_resp_contabil AS tmp            
                    INNER JOIN ( SELECT max( timestamp) AS timestamp
                                      , inscricao_economica                             
                                   FROM economico.cadastro_econ_resp_contabil           
                               GROUP BY inscricao_economica                             
                            )AS tmp2                                                
                           ON tmp2.timestamp           = tmp.timestamp                          
                          AND tmp2.inscricao_economica = tmp.inscricao_economica  
                    ) AS CERC
                   ON CERC.inscricao_economica = CE.inscricao_economica           
            
            LEFT JOIN economico.baixa_cadastro_economico BA
                   ON BA.inscricao_economica = CE.inscricao_economica             
            
            LEFT JOIN economico.tipo_baixa_inscricao TBI
                   ON TBI.cod_tipo = BA.cod_tipo                              
                    , sw_cgm AS CGM                                                  
            
            LEFT JOIN sw_cgm_pessoa_juridica AS CGMPJ
                   ON CGMPJ.numcgm = CGM.numcgm                                   
            
            LEFT JOIN sw_cgm_pessoa_fisica AS CGMPF
                   ON CGMPF.numcgm = CGM.numcgm                                   
                
                WHERE COALESCE ( CEED.numcgm, CEEF.numcgm, CEA.numcgm ) = cgm.numcgm  \n";

    return $stSQL;
}

function recuperaConsulta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaConsulta().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConsulta()
{
    $stSql = "
        SELECT DISTINCT ON (CE.inscricao_economica)
               CE.inscricao_economica
             , TO_CHAR(CE.dt_abertura,'dd/mm/yyyy') as dt_abertura
             , TO_CHAR(CE.timestamp, 'dd/mm/yyyy') as dt_inclusao
             , ACE.cod_atividade
             , A.nom_atividade
             , COALESCE ( A.cod_estrutural, '&nbsp;' ) as cod_estrutural
             , CGM.nom_cgm
             , CGMPF.cpf
             , CGMPJ.cnpj
             , COALESCE ( CEED.numcgm, CEEF.numcgm, CEA.numcgm ) as numcgm
             , CASE WHEN CAST( CEEF.numcgm AS VARCHAR) IS NOT NULL THEN
                  'F'
               WHEN CAST( CEA.numcgm AS VARCHAR ) IS NOT NULL THEN
                  'A'
               WHEN CAST( CEED.numcgm AS VARCHAR) IS NOT NULL THEN
                  'D'
               END AS enquadramento
             , CASE WHEN (DF.timestamp IS NOT NULL AND DI.timestamp IS NOT NULL  AND DF.timestamp >= DI.timestamp) THEN
                  'F'
               WHEN (DF.timestamp IS NOT NULL AND DI.timestamp IS NOT NULL  AND DF.timestamp < DI.timestamp) THEN
                  'I'
               WHEN (DF.timestamp IS NOT NULL) THEN
                  'F'
               WHEN (DI.timestamp IS NOT NULL) THEN
                  'I'
               END AS tipo_domicilio
             , CEED.num_registro_junta
             , NJ.cod_natureza
             , NJ.nom_natureza
             , TL.nom_tipo||' '||NL.nom_logradouro as logradouro_f
             , TL2.nom_tipo||' '||NL2.nom_logradouro as logradouro_i
             , I.numero AS numero_f
             , DI.numero AS numero_i
             , I.complemento AS complemento_f
             , DI.complemento AS complemento_i
             , CA.nom_categoria
             , CASE WHEN BA.dt_inicio IS NOT NULL AND BA.dt_termino IS NULL THEN
                   BA.timestamp
               ELSE
                   NULL
               END AS dt_baixa
             , CASE WHEN BA.dt_inicio IS NOT NULL AND BA.dt_termino IS NULL THEN
                   BA.motivo
               ELSE
                   NULL
               END AS motivo
             , UF.nom_uf
             , MU.nom_municipio
             , BAI.nom_bairro
             , BAI.cod_bairro
             , DI.cod_logradouro
             , DI.cep
             , DI.caixa_postal
             , DF.inscricao_municipal
          FROM economico.cadastro_economico CE
          
     LEFT JOIN economico.atividade_cadastro_economico ACE
            ON CE.inscricao_economica = ACE.inscricao_economica
           AND ACE.principal = TRUE
           
     LEFT JOIN economico.atividade A
            ON A.cod_atividade = ACE.cod_atividade
            
     LEFT JOIN economico.cadastro_economico_empresa_direito CEED
            ON CEED.inscricao_economica = CE.inscricao_economica
            
     LEFT JOIN economico.cadastro_economico_empresa_fato CEEF
            ON CEEF.inscricao_economica = CE.inscricao_economica
            
     LEFT JOIN economico.cadastro_economico_autonomo CEA
            ON CEA.inscricao_economica = CE.inscricao_economica
            
     LEFT JOIN economico.categoria CA
            ON CA.cod_categoria = CEED.cod_categoria
            
     LEFT JOIN economico.empresa_direito_natureza_juridica EDNJ
            ON EDNJ.inscricao_economica = CEED.inscricao_economica
            
     LEFT JOIN economico.natureza_juridica NJ
            ON NJ.cod_natureza = EDNJ.cod_natureza
     
     LEFT JOIN economico.domicilio_informado DI
            ON DI.inscricao_economica = CE.inscricao_economica
     
     LEFT JOIN ( SELECT MAX(timestamp) AS timestamp                              
                      , inscricao_economica                                     
                   FROM economico.domicilio_fiscal                              
               GROUP BY inscricao_economica                                      
              ) AS DF_MAX
           ON DF_MAX.inscricao_economica = CE.inscricao_economica          
    
    LEFT JOIN economico.domicilio_fiscal AS DF                             
           ON DF.timestamp           = DF_MAX.timestamp
          AND DF.inscricao_economica = DF_MAX.inscricao_economica
          AND DF.inscricao_economica = CE.inscricao_economica 
     
     LEFT JOIN economico.sociedade S
            ON S.inscricao_economica = CE.inscricao_economica
     
     LEFT JOIN imobiliario.imovel I
            ON I.inscricao_municipal = DF.inscricao_municipal
     
     LEFT JOIN imobiliario.imovel_confrontacao IC
            ON IC.inscricao_municipal = I.inscricao_municipal
     
     LEFT JOIN imobiliario.confrontacao_trecho CT
            ON CT.cod_confrontacao = IC.cod_confrontacao
           AND CT.cod_lote         = IC.cod_lote
           AND CT.principal        = true
     
     LEFT JOIN sw_uf UF
            ON UF.cod_uf = DI.cod_uf
     
     LEFT JOIN sw_municipio MU
            ON MU.cod_municipio = DI.cod_municipio
           AND MU.cod_uf        = DI.cod_uf
     
     LEFT JOIN sw_bairro BAI
            ON BAI.cod_bairro    = DI.cod_bairro
           AND BAI.cod_uf        = DI.cod_uf
           AND BAI.cod_municipio = DI.cod_municipio
     
     LEFT JOIN sw_nome_logradouro NL
            ON NL.cod_logradouro = CT.cod_logradouro
     
     LEFT JOIN sw_tipo_logradouro TL
            ON TL.cod_tipo = NL.cod_tipo
     
     LEFT JOIN sw_nome_logradouro NL2
            ON NL2.cod_logradouro = DI.cod_logradouro
     
     LEFT JOIN sw_tipo_logradouro TL2
            ON TL2.cod_tipo = NL2.cod_tipo
     
     LEFT JOIN ( SELECT tmp.*
                   FROM economico.baixa_cadastro_economico AS tmp
             INNER JOIN ( SELECT MAX(timestamp) as timestamp
                               , inscricao_economica
                            FROM economico.baixa_cadastro_economico
                        GROUP BY inscricao_economica
                        )AS tmp2
                     ON tmp.inscricao_economica = tmp2.inscricao_economica
                    AND tmp.timestamp = tmp2.timestamp
               ) AS BA
            ON BA.inscricao_economica = CE.inscricao_economica
             , sw_cgm AS CGM
     
     LEFT JOIN sw_cgm_pessoa_fisica AS CGMPF
            ON CGMPF.numcgm = CGM.numcgm
     
     LEFT JOIN sw_cgm_pessoa_juridica AS CGMPJ
            ON CGMPJ.numcgm = CGM.numcgm
            
         WHERE COALESCE ( CEED.numcgm, CEEF.numcgm, CEA.numcgm ) = cgm.numcgm ";

    return $stSql;
}

function recuperaNomeEmpresa(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaNomeEmpresa().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql,  $boTransacao );

    return $obErro;
}

function montaRecuperaNomeEmpresa()
{
    $stSql  = "SELECT                                                                                  \n";
    $stSql .= "    CE.inscricao_economica,                                                             \n";
    $stSql .= "    CGM.nom_cgm,                                                                        \n";
    $stSql .= "    COALESCE (                                                                          \n";
    $stSql .= "        CEED.numcgm,                                                                    \n";
    $stSql .= "        CEEF.numcgm,                                                                    \n";
    $stSql .= "        CEA.numcgm                                                                      \n";
    $stSql .= "    ) as numcgm                                                                         \n";
    $stSql .= "FROM                                                                                    \n";
    $stSql .= "    economico.cadastro_economico CE                                                     \n";
    $stSql .= "    LEFT JOIN economico.cadastro_economico_empresa_direito CEED ON                      \n";
    $stSql .= "        CEED.inscricao_economica = CE.inscricao_economica                               \n";
    $stSql .= "    LEFT JOIN economico.cadastro_economico_empresa_fato CEEF ON                         \n";
    $stSql .= "        CEEF.inscricao_economica = CE.inscricao_economica                               \n";
    $stSql .= "    LEFT JOIN economico.cadastro_economico_autonomo CEA ON                              \n";
    $stSql .= "        CEA.inscricao_economica = CE.inscricao_economica,                               \n";
    $stSql .= "    sw_cgm AS CGM                                                                       \n";
    $stSql .= "WHERE                                                                                   \n";
    $stSql .= "    COALESCE (                                                                          \n";
    $stSql .= "        CEED.numcgm,                                                                    \n";
    $stSql .= "        CEEF.numcgm,                                                                    \n";
    $stSql .= "        CEA.numcgm                                                                      \n";
    $stSql .= "    ) = cgm.numcgm                                                                      \n";

    return $stSql;
}

function recuperaModalidadeAtividadeInscricao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaModalidadeAtividadeInscricao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql,  $boTransacao );

    return $obErro;
}

function montaRecuperaModalidadeAtividadeInscricao()
{
    $stSql  = " SELECT \n";
    $stSql .= "     ece.inscricao_economica, \n";
    $stSql .= "     ea.nom_atividade, \n";
    $stSql .= "     ea.cod_atividade, \n";
    $stSql .= "     ( \n";
    $stSql .= "         SELECT \n";
    $stSql .= "             eml.nom_modalidade \n";
    $stSql .= "         FROM \n";
    $stSql .= "             economico.modalidade_lancamento AS eml \n";
    $stSql .= "         WHERE \n";
    $stSql .= "             eml.cod_modalidade = COALESCE( eceml.cod_modalidade, eam.cod_modalidade) \n";
    $stSql .= "     )AS nom_modalidade, \n";
    $stSql .= "     COALESCE( eceml.cod_modalidade, eam.cod_modalidade) AS cod_modalidade, \n";
    $stSql .= "     COALESCE( ecd.numcgm, ecf.numcgm, eca.numcgm ) AS numcgm, \n";

    $stSql .= "     ( \n";
    $stSql .= "         SELECT \n";
    $stSql .= "             cgm.nom_cgm \n";
    $stSql .= "         FROM \n";
    $stSql .= "             sw_cgm AS cgm \n";
    $stSql .= "         WHERE \n";
    $stSql .= "             cgm.numcgm = COALESCE( ecd.numcgm, ecf.numcgm, eca.numcgm ) \n";
    $stSql .= "     ) AS nom_cgm \n";

    $stSql .= " FROM \n";
    $stSql .= "     economico.cadastro_economico AS ece \n";

    $stSql .= " LEFT JOIN \n";
    $stSql .= "     economico.cadastro_economico_empresa_direito AS ecd \n";
    $stSql .= " ON \n";
    $stSql .= "     ecd.inscricao_economica = ece.inscricao_economica \n";

    $stSql .= " LEFT JOIN \n";
    $stSql .= "     economico.cadastro_economico_empresa_fato AS ecf \n";
    $stSql .= " ON \n";
    $stSql .= "     ecf.inscricao_economica = ece.inscricao_economica \n";

    $stSql .= " LEFT JOIN \n";
    $stSql .= "     economico.cadastro_economico_autonomo AS eca \n";
    $stSql .= " ON \n";
    $stSql .= "     eca.inscricao_economica = ece.inscricao_economica \n";

    $stSql .= " INNER JOIN (
                    SELECT ate.inscricao_economica
                        , max(ocorrencia_atividade) AS ocorrencia_atividade
                    FROM economico.atividade_cadastro_economico AS ate
                    GROUP BY inscricao_economica
                )AS ate
                    ON
                        ate.inscricao_economica = ece.inscricao_economica

                INNER JOIN
                    economico.atividade_cadastro_economico AS eac
                ON
                    eac.inscricao_economica = ate.inscricao_economica
                    AND eac.ocorrencia_atividade = ate.ocorrencia_atividade \n";

    $stSql .= " LEFT JOIN \n";
    $stSql .= "     economico.cadastro_economico_modalidade_lancamento AS eceml \n";
    $stSql .= " ON \n";
    $stSql .= "     eceml.inscricao_economica = ece.inscricao_economica \n";
    $stSql .= "     AND eceml.ocorrencia_atividade = eac.ocorrencia_atividade \n";
    $stSql .= "     AND eceml.cod_atividade = eac.cod_atividade \n";

    $stSql .= " INNER JOIN \n";
    $stSql .= "     economico.atividade AS ea \n";
    $stSql .= " ON \n";
    $stSql .= "     ea.cod_atividade = eac.cod_atividade \n";

    $stSql .= " LEFT JOIN \n";
    $stSql .= "     economico.atividade_modalidade_lancamento AS eam \n";
    $stSql .= " ON \n";
    $stSql .= "     eam.cod_atividade = ea.cod_atividade \n";

    return $stSql;
}

function recuperaInscricaoRetentor(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaInscricaoRetentor().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql,  $boTransacao );

    return $obErro;
}

function montaRecuperaInscricaoRetentor()
{
    $stSql  = " SELECT \n";
    $stSql .= "    ece.inscricao_economica, \n";
    $stSql .= "     COALESCE( ecd.numcgm, ecf.numcgm, eca.numcgm ) AS numcgm, \n";
    $stSql .= "     edf.inscricao_municipal, \n";
    $stSql .= "     ( \n";
    $stSql .= "        SELECT \n";
    $stSql .= "            split_part ( dados.valor,'§',1) ||' '|| split_part ( dados.valor,'§',2 ) \n";
    $stSql .= "        FROM \n";
    $stSql .= "            ( \n";
    $stSql .= "              SELECT \n";
    $stSql .= "                CASE WHEN (edf.inscricao_municipal IS NOT NULL) AND (edi.inscricao_economica IS NOT NULL) THEN \n";
    $stSql .= "                    CASE WHEN (edf.timestamp > edi.timestamp) THEN \n";
    $stSql .= "                        economico.fn_busca_domicilio_fiscal( edf.inscricao_municipal ) \n";
    $stSql .= "                    ELSE \n";
    $stSql .= "                        economico.fn_busca_domicilio_informado( ece.inscricao_economica ) \n";
    $stSql .= "                    END \n";
    $stSql .= "                ELSE \n";
    $stSql .= "                    CASE WHEN (edf.inscricao_municipal IS NOT NULL) THEN \n";
    $stSql .= "                        economico.fn_busca_domicilio_fiscal( edf.inscricao_municipal ) \n";
    $stSql .= "                    ELSE \n";
    $stSql .= "                        economico.fn_busca_domicilio_informado( ece.inscricao_economica ) \n";
    $stSql .= "                    END \n";
    $stSql .= "                END AS valor \n";
    $stSql .= "            )AS dados \n";
    $stSql .= "     ) AS logradouro, \n";
    $stSql .= "     ( \n";
    $stSql .= "         SELECT \n";
    $stSql .= "             COALESCE ( pf.cpf, pj.cnpj ) \n";
    $stSql .= "         FROM  \n";
    $stSql .= "             sw_cgm AS cgm \n";
    $stSql .= "         LEFT JOIN \n";
    $stSql .= "            sw_cgm_pessoa_fisica AS pf \n";
    $stSql .= "         ON  \n";
    $stSql .= "            pf.numcgm = cgm.numcgm \n";
    $stSql .= "         LEFT JOIN \n";
    $stSql .= "            sw_cgm_pessoa_juridica AS pj \n";
    $stSql .= "         ON \n";
    $stSql .= "            pj.numcgm = cgm.numcgm \n";
    $stSql .= "         WHERE \n";
    $stSql .= "            cgm.numcgm = COALESCE( ecd.numcgm, ecf.numcgm, eca.numcgm ) \n";
    $stSql .= "     ) AS CPF_CNPJ, \n";
    $stSql .= "     (  \n";
    $stSql .= "         SELECT \n";
    $stSql .= "             cgm.nom_cgm \n";
    $stSql .= "         FROM  \n";
    $stSql .= "             sw_cgm AS cgm \n";
    $stSql .= "         WHERE  \n";
    $stSql .= "             cgm.numcgm = COALESCE( ecd.numcgm, ecf.numcgm, eca.numcgm ) \n";
    $stSql .= "     ) AS nom_cgm  \n";
    $stSql .= " FROM  \n";
    $stSql .= "     economico.cadastro_economico AS ece \n";
    $stSql .= " LEFT JOIN  \n";
    $stSql .= "     economico.cadastro_economico_empresa_direito AS ecd \n";
    $stSql .= " ON  \n";
    $stSql .= "     ecd.inscricao_economica = ece.inscricao_economica \n";
    $stSql .= " LEFT JOIN  \n";
    $stSql .= "     economico.cadastro_economico_empresa_fato AS ecf \n";
    $stSql .= " ON  \n";
    $stSql .= "     ecf.inscricao_economica = ece.inscricao_economica \n";
    $stSql .= " LEFT JOIN  \n";
    $stSql .= "     economico.cadastro_economico_autonomo AS eca \n";
    $stSql .= " ON  \n";
    $stSql .= "     eca.inscricao_economica = ece.inscricao_economica \n";

    $stSql .= " LEFT JOIN ( \n";
    $stSql .= "     SELECT \n";
    $stSql .= "         edf_tmp.inscricao_economica, \n";
    $stSql .= "         edf_tmp.inscricao_municipal, \n";
    $stSql .= "         edf_tmp.timestamp \n";
    $stSql .= "     FROM \n";
    $stSql .= "         economico.domicilio_fiscal AS edf_tmp, \n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 MAX (timestamp) AS timestamp, \n";
    $stSql .= "                 inscricao_economica \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 economico.domicilio_fiscal \n";
    $stSql .= "             GROUP BY \n";
    $stSql .= "                 inscricao_economica \n";
    $stSql .= "         )AS tmp \n";
    $stSql .= "     WHERE \n";
    $stSql .= "         tmp.timestamp = edf_tmp.timestamp \n";
    $stSql .= "         AND tmp.inscricao_economica = edf_tmp.inscricao_economica \n";
    $stSql .= " )AS edf \n";
    $stSql .= " ON \n";
    $stSql .= "     ece.inscricao_economica = edf.inscricao_economica \n";

    $stSql .= " LEFT JOIN ( \n";
    $stSql .= "     SELECT \n";
    $stSql .= "         edi_tmp.timestamp, \n";
    $stSql .= "         edi_tmp.inscricao_economica \n";
    $stSql .= "     FROM \n";
    $stSql .= "         economico.domicilio_informado AS edi_tmp, \n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 MAX(timestamp) AS timestamp, \n";
    $stSql .= "                 inscricao_economica \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 economico.domicilio_informado \n";
    $stSql .= "             GROUP BY \n";
    $stSql .= "                 inscricao_economica \n";
    $stSql .= "         )AS tmp \n";
    $stSql .= "     WHERE \n";
    $stSql .= "         tmp.timestamp = edi_tmp.timestamp \n";
    $stSql .= "         AND tmp.inscricao_economica = edi_tmp.inscricao_economica \n";
    $stSql .= " )AS edi \n";
    $stSql .= " ON \n";
    $stSql .= "     ece.inscricao_economica = edi.inscricao_economica \n";

    return $stSql;
}

function recuperaInscricaoEndereco(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaInscricaoEndereco( $stFiltro );
    $this->stDebug = $stSql;
    #$this->debug();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql,  $boTransacao );

    return $obErro;
}

function montaRecuperaInscricaoEndereco($stFiltro)
{
    $stSql  = " SELECT
                    *
                FROM
                    arrecadacao.fn_consulta_endereco_empresa ( ". $stFiltro ." ) as endereco  \n";

    return $stSql;

}

function recuperaInscricaoEconomica(&$rsRecordSet, $stCNPJ = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaInscricaoEconomica( $stCNPJ );
    $this->stDebug = $stSql;
    #$this->debug();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql,  $boTransacao );

    return $obErro;
}

function montaRecuperaInscricaoEconomica($stCNPJ)
{
    $stSql  = " SELECT
                    COALESCE( ceed.inscricao_economica, ceef.inscricao_economica, cea.inscricao_economica ) AS inscricao_economica

                FROM
                    sw_cgm_pessoa_juridica AS scpj

                LEFT JOIN
                    economico.cadastro_economico_empresa_direito AS ceed
                ON
                    ceed.numcgm = scpj.numcgm

                LEFT JOIN
                    economico.cadastro_economico_empresa_fato AS ceef
                ON
                    ceef.numcgm = scpj.numcgm

                LEFT JOIN
                    economico.cadastro_economico_autonomo AS cea
                ON
                    cea.numcgm = scpj.numcgm

                WHERE
                    scpj.cnpj = ".$stCNPJ;

    return $stSql;
}

function recuperaDocumentosBaixaInscricaoEconomica(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDocumentosBaixaInscricaoEconomica().$stFiltro;
    $this->stDebug = $stSql;
    #$this->debug();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDocumentosBaixaInscricaoEconomica()
{
    $stSql  = "
        SELECT
            arquivos_documento.nome_arquivo_swx AS arquivo_ooffice,
            modelo_documento.nome_documento AS nome_pro_combo,
            modelo_documento.nome_arquivo_agt AS nome_interno

        FROM
            administracao.modelo_arquivos_documento

        INNER JOIN
            administracao.modelo_documento
        ON
            modelo_documento.cod_documento = modelo_arquivos_documento.cod_documento
            AND modelo_documento.cod_tipo_documento = modelo_arquivos_documento.cod_tipo_documento

        INNER JOIN
            administracao.arquivos_documento
        ON
            arquivos_documento.cod_arquivo = modelo_arquivos_documento.cod_arquivo
    ";

    return $stSql;
}

function recuperaDadosCertidaoBaixa(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosCertidaoBaixa().$stFiltro;
    $this->stDebug = $stSql;
    #$this->debug();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosCertidaoBaixa()
{
    $stSql = "
        SELECT
            TO_CHAR( baixa_cadastro_economico.dt_inicio, 'dd/mm/yyyy' ) as databaixa,
            COALESCE( cadastro_economico_empresa_fato.numcgm, cadastro_economico_empresa_direito.numcgm, cadastro_economico_autonomo.numcgm ) AS numcgm,
            (
                SELECT
                    nom_cgm
                FROM
                    sw_cgm
                WHERE
                    sw_cgm.numcgm = COALESCE( cadastro_economico_empresa_fato.numcgm, cadastro_economico_empresa_direito.numcgm, cadastro_economico_autonomo.numcgm )
            )AS nomcgm,
            atividade.nom_atividade AS ramo,
            publico.fn_data_extenso(now()::date) AS datacorrente,
            CASE WHEN processo_baixa_cad_economico.cod_processo IS NOT NULL THEN
                ', conforme requerimento Nº '||processo_baixa_cad_economico.cod_processo||'/'||processo_baixa_cad_economico.exercicio
            ELSE
                ' '
            END AS processo

        FROM
            economico.baixa_cadastro_economico

        LEFT JOIN
            economico.processo_baixa_cad_economico
        ON
            processo_baixa_cad_economico.inscricao_economica = baixa_cadastro_economico.inscricao_economica
            AND processo_baixa_cad_economico.timestamp = baixa_cadastro_economico.timestamp

        LEFT JOIN
            economico.cadastro_economico_autonomo
        ON
            cadastro_economico_autonomo.inscricao_economica = baixa_cadastro_economico.inscricao_economica

        LEFT JOIN
            economico.cadastro_economico_empresa_direito
        ON
            cadastro_economico_empresa_direito.inscricao_economica = baixa_cadastro_economico.inscricao_economica

        LEFT JOIN
            economico.cadastro_economico_empresa_fato
        ON
            cadastro_economico_empresa_fato.inscricao_economica = baixa_cadastro_economico.inscricao_economica

        INNER JOIN
            economico.atividade_cadastro_economico
        ON
            atividade_cadastro_economico.inscricao_economica = baixa_cadastro_economico.inscricao_economica
            AND atividade_cadastro_economico.principal = true

        INNER JOIN
            economico.atividade
        ON
            atividade.cod_atividade = atividade_cadastro_economico.cod_atividade

    ";

    return $stSql;
}

function montaRecuperaInscricaoEconomicaConversao()
{
    $stSQL  = "SELECT                                                                                             \n";
    $stSQL .= "    CADASTRO_ECONOMICO.INSCRICAO_ECONOMICA,                                                        \n";
    $stSQL .= "    CADASTRO_ECONOMICO.TIMESTAMP,                                                                  \n";
    $stSQL .= "    CADASTRO_ECONOMICO.DT_ABERTURA,                                                                \n";
    $stSQL .= "    COALESCE(CADASTRO_ECONOMICO_AUTONOMO.NUMCGM,                                                   \n";
    $stSQL .= "    CADASTRO_ECONOMICO_EMPRESA_FATO.NUMCGM,                                                        \n";
    $stSQL .= "             NULL) AS NUMCGM,                                                                      \n";
    $stSQL .= "    (SELECT                                                                                        \n";
    $stSQL .= "        NOM_CGM                                                                                    \n";
    $stSQL .= "    FROM                                                                                           \n";
    $stSQL .= "        SW_CGM,                                                                                    \n";
    $stSQL .= "        SW_CGM_PESSOA_FISICA                                                                       \n";
    $stSQL .= "     WHERE                                                                                         \n";
    $stSQL .= "        SW_CGM.NUMCGM = SW_CGM_PESSOA_FISICA.NUMCGM                                                \n";
    $stSQL .= "        AND SW_CGM.NUMCGM = COALESCE(CADASTRO_ECONOMICO_AUTONOMO.NUMCGM,                           \n";
    $stSQL .= "                                     CADASTRO_ECONOMICO_EMPRESA_FATO.NUMCGM,                       \n";
    $stSQL .= "                                     NULL) ) AS NOM_CGM                                            \n";
    $stSQL .= "FROM                                                                                               \n";
    $stSQL .= "    ECONOMICO.CADASTRO_ECONOMICO                                                                   \n";
    $stSQL .= "LEFT JOIN                                                                                          \n";
    $stSQL .= "    (                                                                                              \n";
    $stSQL .= "    SELECT                                                                                         \n";
    $stSQL .= "        BCE2.inscricao_economica                                                                   \n";
    $stSQL .= "        ,BCE2.dt_inicio                                                                            \n";
    $stSQL .= "    FROM                                                                                           \n";
    $stSQL .= "        (SELECT                                                                                    \n";
    $stSQL .= "        INSCRICAO_ECONOMICA,                                                                       \n";
    $stSQL .= "        MAX(DT_INICIO) AS DT_INICIO                                                                \n";
    $stSQL .= "         FROM                                                                                      \n";
    $stSQL .= "        ECONOMICO.BAIXA_CADASTRO_ECONOMICO                                                         \n";
    $stSQL .= "         GROUP BY                                                                                  \n";
    $stSQL .= "        INSCRICAO_ECONOMICA) BCE,                                                                  \n";
    $stSQL .= "        ECONOMICO.BAIXA_CADASTRO_ECONOMICO BCE2                                                    \n";
    $stSQL .= "    WHERE                                                                                          \n";
    $stSQL .= "        BCE.INSCRICAO_ECONOMICA =  BCE2.INSCRICAO_ECONOMICA                                        \n";
    $stSQL .= "        AND BCE.DT_INICIO       =  BCE2.DT_INICIO                                                  \n";
    $stSQL .= "        AND BCE2.DT_TERMINO IS NULL                                                                \n";
    $stSQL .= "    ) AS BAIXA_CADASTRO_ECONOMICO                                                                  \n";
    $stSQL .= "ON                                                                                                 \n";
    $stSQL .= "    BAIXA_CADASTRO_ECONOMICO.INSCRICAO_ECONOMICA = CADASTRO_ECONOMICO.INSCRICAO_ECONOMICA          \n";
    $stSQL .= "LEFT JOIN                                                                                          \n";
    $stSQL .= "    ECONOMICO.CADASTRO_ECONOMICO_AUTONOMO                                                          \n";
    $stSQL .= "ON                                                                                                 \n";
    $stSQL .= "    CADASTRO_ECONOMICO_AUTONOMO.INSCRICAO_ECONOMICA = CADASTRO_ECONOMICO.INSCRICAO_ECONOMICA       \n";
    $stSQL .= "LEFT JOIN                                                                                          \n";
    $stSQL .= "    ECONOMICO.CADASTRO_ECONOMICO_EMPRESA_FATO                                                      \n";
    $stSQL .= "ON                                                                                                 \n";
    $stSQL .= "    CADASTRO_ECONOMICO_EMPRESA_FATO.INSCRICAO_ECONOMICA = CADASTRO_ECONOMICO.INSCRICAO_ECONOMICA   \n";
    $stSQL .= "LEFT JOIN                                                                                          \n";
    $stSQL .= "    ECONOMICO.CADASTRO_ECONOMICO_EMPRESA_DIREITO                                                   \n";
    $stSQL .= "ON                                                                                                 \n";
    $stSQL .= "    CADASTRO_ECONOMICO_EMPRESA_DIREITO.INSCRICAO_ECONOMICA = CADASTRO_ECONOMICO.INSCRICAO_ECONOMICA\n";
    $stSQL .= "WHERE                                                                                              \n";
    $stSQL .= "    BAIXA_CADASTRO_ECONOMICO.INSCRICAO_ECONOMICA IS NULL                                           \n";
    $stSQL .= "    AND CADASTRO_ECONOMICO_EMPRESA_DIREITO.INSCRICAO_ECONOMICA IS NULL                             \n";

    return $stSQL;
}

function recuperaInscricaoEconomicaConversao(&$rsRecordSet, $stFiltro = "",$stOrdem="" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaInscricaoEconomicaConversao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
