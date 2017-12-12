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
* Classe de mapeamento para administracao.logradouro
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.03.98
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
class TLogradouro extends Persistente
{
function TLogradouro()
{
    parent::Persistente();
    $this->setTabela('sw_logradouro');
    $this->setCampoCod('cod_logradouro');

    $this->AddCampo('cod_logradouro',      'integer', true, '', true,  false);
    $this->AddCampo('cod_uf',              'integer', true, '', false, true);
    $this->AddCampo('cod_municipio',       'integer', true, '', false, true);
}

function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT 
                       TL.cod_tipo,                                      
                       TL.nom_tipo||' '||NL.nom_logradouro as tipo_nome, 
                       TL.nom_tipo,                                      
                       NL.nom_logradouro,                                
                       L.*,                                              
                       B.nom_bairro,                                     
                       M.nom_municipio,                                  
                       U.nom_uf,                                         
                       U.sigla_uf,                                       
                       imobiliario.fn_consulta_cep(L.cod_logradouro) AS cep 
                  FROM sw_tipo_logradouro AS TL,                       
                       ( SELECT DISTINCT ON (cod_logradouro)
                                  cod_logradouro
                                , timestamp
                                , cod_tipo
                                , nom_logradouro
                                , dt_inicio
                                , MAX(dt_fim) as dt_fim
                                , cod_norma
                           FROM sw_nome_logradouro as snl
                           WHERE dt_fim IS NULL
                       GROUP BY cod_logradouro
                                , timestamp
                                , cod_tipo
                                , nom_logradouro
                                , dt_inicio
                       ORDER BY cod_logradouro DESC
                       )AS NL,                       
                       sw_municipio  AS M,                        
                       sw_uf         AS U,                        
                       sw_logradouro AS L                         
       LEFT OUTER JOIN sw_bairro_logradouro AS BL 
                    ON BL.cod_logradouro = L.cod_logradouro   
                   AND BL.cod_uf         = L.cod_uf           
                   AND BL.cod_municipio  = L.cod_municipio               
       LEFT OUTER JOIN sw_bairro               AS B 
                    ON B.cod_bairro      = BL.cod_bairro      
                   AND B.cod_uf          = BL.cod_uf          
                   AND B.cod_municipio   = BL.cod_municipio              
                 WHERE L.cod_logradouro  = NL.cod_logradouro 
                   AND L.cod_municipio   = M.cod_municipio    
                   AND L.cod_uf          = M.cod_uf           
                   AND M.cod_uf          = U.cod_uf           
                   AND NL.cod_tipo       = TL.cod_tipo  
    ";                 

    return $stSql;
}

function recuperaRelacionamentoRelatorio(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoRelatorio().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaRelacionamentoRelatorio()
{
    $stSql  = " SELECT                                                     
                    '1' as grupo
                    ,sw_tipo_logradouro.cod_tipo                                           
                    ,sw_tipo_logradouro.nom_tipo||' '||sw_nome_logradouro.nom_logradouro as tipo_nome
                    ,sw_tipo_logradouro.nom_tipo
                    ,sw_nome_logradouro.nom_logradouro
                    ,sw_logradouro.*                                  
                    ,sw_bairro.cod_bairro
                    ,initcap(sw_bairro.nom_bairro) as nom_bairro
                    ,sw_municipio.nom_municipio
                    ,sw_uf.nom_uf
                    ,sw_uf.sigla_uf
                    ,imobiliario.fn_consulta_cep(sw_logradouro.cod_logradouro) AS cep
                    ,TO_CHAR(sw_nome_logradouro.timestamp,'dd/mm/yyyy hh24:mm') as data_logradouro   
                    ,norma.num_norma||'/'||norma.exercicio||' - '||tipo_norma.nom_tipo_norma||' - '||norma.nom_norma as descricao_norma_relatorio
                    ,TO_CHAR(sw_nome_logradouro.dt_inicio,'dd/mm/yyyy') as dt_inicio
                    ,TO_CHAR(sw_nome_logradouro.dt_fim,'dd/mm/yyyy') as dt_fim
                FROM sw_logradouro
                
                INNER JOIN sw_nome_logradouro
                    ON sw_logradouro.cod_logradouro = sw_nome_logradouro.cod_logradouro
                
                INNER JOIN (SELECT                                               
                                MAX(timestamp) AS timestamp,                     
                                cod_logradouro                                   
                            FROM sw_nome_logradouro                              
                            GROUP BY cod_logradouro                              
                            ORDER BY cod_logradouro                              
                ) AS max_nome_logradouro
                    ON sw_nome_logradouro.cod_logradouro = max_nome_logradouro.cod_logradouro 
                    AND sw_nome_logradouro.timestamp      = max_nome_logradouro.timestamp      

                INNER JOIN sw_tipo_logradouro
                    ON sw_nome_logradouro.cod_tipo = sw_tipo_logradouro.cod_tipo
                       
                INNER JOIN sw_municipio
                     ON sw_logradouro.cod_municipio   = sw_municipio.cod_municipio    
                    AND sw_logradouro.cod_uf          = sw_municipio.cod_uf           
                
                INNER JOIN sw_uf
                    ON sw_municipio.cod_uf          = sw_uf.cod_uf
                
                INNER JOIN normas.norma
                    ON norma.cod_norma = sw_nome_logradouro.cod_norma    
            
                INNER JOIN normas.tipo_norma
                    ON tipo_norma.cod_tipo_norma = norma.cod_tipo_norma

                LEFT JOIN sw_bairro_logradouro
                     ON sw_bairro_logradouro.cod_logradouro = sw_logradouro.cod_logradouro   
                    AND sw_bairro_logradouro.cod_uf         = sw_logradouro.cod_uf           
                    AND sw_bairro_logradouro.cod_municipio  = sw_logradouro.cod_municipio               
                
                LEFT JOIN sw_bairro
                     ON sw_bairro.cod_bairro      = sw_bairro_logradouro.cod_bairro      
                    AND sw_bairro.cod_uf          = sw_bairro_logradouro.cod_uf          
                    AND sw_bairro.cod_municipio   = sw_bairro_logradouro.cod_municipio
        ";

    return $stSql;
}


function recuperaHistoricoLogradouro(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaHistoricoLogradouro().$stCondicao.$stOrdem;    
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}


private function montaRecuperaHistoricoLogradouro()
{
    $stSql  = " SELECT
                        CASE WHEN sw_nome_logradouro.timestamp < (SELECT max(timestamp) FROM sw_nome_logradouro as max 
                                                                  WHERE max.cod_logradouro = sw_nome_logradouro.cod_logradouro) 
                               THEN
                                        '3'
                               else
                                        '1'
                        END as grupo
                       ,sw_tipo_logradouro.cod_tipo
                       ,sw_tipo_logradouro.nom_tipo||' '||sw_nome_logradouro.nom_logradouro as tipo_nome
                       ,sw_tipo_logradouro.nom_tipo
                       ,sw_nome_logradouro.nom_logradouro as nome_anterior
                       ,sw_nome_logradouro.nom_logradouro
                       ,TO_CHAR(sw_nome_logradouro.dt_inicio,'dd/mm/yyyy') as dt_inicio
                       ,TO_CHAR(sw_nome_logradouro.dt_fim,'dd/mm/yyyy') as dt_fim   
                       ,sw_nome_logradouro.cod_norma
                       ,norma.cod_tipo_norma||' - '||norma.nom_norma as descricao_norma
                       ,norma.num_norma||'/'||norma.exercicio||' - '||tipo_norma.nom_tipo_norma||' - '||norma.nom_norma as descricao_norma_relatorio
                       ,sw_logradouro.*
                       ,initcap(sw_bairro.nom_bairro) as nom_bairro
                       ,sw_municipio.nom_municipio
                       ,sw_uf.nom_uf
                       ,sw_uf.sigla_uf
                       ,imobiliario.fn_consulta_cep(sw_logradouro.cod_logradouro) AS cep
                       ,TO_CHAR(sw_nome_logradouro.timestamp,'dd/mm/yyyy hh24:mm') as data_logradouro
                       ,TO_CHAR(sw_nome_logradouro.timestamp,'yyyy') as exercicio
                       ,row_number() OVER (ORDER BY sw_nome_logradouro.dt_inicio) as sequencial
                  FROM sw_logradouro
            INNER JOIN sw_nome_logradouro
                    ON sw_logradouro.cod_logradouro = sw_nome_logradouro.cod_logradouro

            INNER JOIN sw_tipo_logradouro
                    ON sw_nome_logradouro.cod_tipo = sw_tipo_logradouro.cod_tipo
                       
            INNER JOIN sw_municipio
                    ON sw_logradouro.cod_municipio   = sw_municipio.cod_municipio    
                   AND sw_logradouro.cod_uf          = sw_municipio.cod_uf           
                
            INNER JOIN sw_uf
                    ON sw_municipio.cod_uf          = sw_uf.cod_uf
                
            INNER JOIN normas.norma
                    ON norma.cod_norma = sw_nome_logradouro.cod_norma    
            
            INNER JOIN normas.tipo_norma
                    ON tipo_norma.cod_tipo_norma = norma.cod_tipo_norma    
            
            LEFT JOIN sw_bairro_logradouro
                   ON sw_bairro_logradouro.cod_logradouro = sw_logradouro.cod_logradouro   
                  AND sw_bairro_logradouro.cod_uf         = sw_logradouro.cod_uf           
                  AND sw_bairro_logradouro.cod_municipio  = sw_logradouro.cod_municipio               
                
            LEFT JOIN sw_bairro
                   ON sw_bairro.cod_bairro      = sw_bairro_logradouro.cod_bairro      
                  AND sw_bairro.cod_uf          = sw_bairro_logradouro.cod_uf          
                  AND sw_bairro.cod_municipio   = sw_bairro_logradouro.cod_municipio
        ";

   return $stSql;
}



}
