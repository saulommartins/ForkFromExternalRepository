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
    * Classe de mapeamento da tabela ORCAMENTO.UNIDADE
    * Data de Criação: 13/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TOrcamentoUnidade.class.php 65975 2016-07-05 14:47:21Z lisiane $

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-05-18 12:21:39 -0300 (Sex, 18 Mai 2007) $

    * Casos de uso: uc-02.01.02
*/

/**
  * Efetua conexão com a tabela  ORCAMENTO.UNIDADE
  * Data de Criação: 13/07/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Marcelo B. Paulino

*/
class TOrcamentoUnidade extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
    $this->setTabela('orcamento.unidade');

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,num_unidade,num_orgao');

    $this->AddCampo('exercicio'          ,'char'    ,true  ,'04',true ,true);
    $this->AddCampo('num_unidade'        ,'integer' ,true  ,''  ,true ,false);
    $this->AddCampo('num_orgao'          ,'integer' ,true  ,''  ,true ,true);
    $this->AddCampo('nom_unidade'        ,'varchar' ,true  ,'60',false,false);
    $this->AddCampo('usuario_responsavel','integer' ,false ,''  ,false,true);
}

function montaRecuperaRelacionamento()
{
    $stSql = " SELECT unidade.*
                     , unidade.nom_unidade
                     , orgao.nom_orgao
                     , sw_cgm.nom_cgm AS nome_usuario
                  FROM orcamento.unidade
            INNER JOIN orcamento.orgao
                    ON unidade.exercicio = orgao.exercicio
                   AND unidade.num_orgao = orgao.num_orgao
            INNER JOIN sw_cgm
                    ON sw_cgm.numcgm = unidade.usuario_responsavel ";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaMascarado.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaMascarado(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaMascarado().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMascarado()
{
    $stSql  = " SELECT unidade.nom_unidade
                     , orgao.nom_orgao
                     , unidade.*
                  FROM ( SELECT *
                              , sw_fn_mascara_dinamica( '".$this->getDado('stMascaraUnidade')."', num_unidade::VARCHAR ) as num_unidade_mask
                              , sw_fn_mascara_dinamica('".$this->getDado('stMascaraOrgao')."', num_orgao::VARCHAR )   as num_orgao_mask
                           FROM orcamento.unidade
                       ) AS unidade
            INNER JOIN orcamento.orgao
                    ON unidade.exercicio = orgao.exercicio
                   AND unidade.num_orgao = orgao.num_orgao";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosExportacao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosExportacao()
{
  $stQuebra = "\n";
  $stSql  = "SELECT                                              ".$stQuebra;
  $stSql .= "    OO.exercicio,                                   ".$stQuebra;
    $stSql .= "    OO.num_orgao,                                   ".$stQuebra;
    $stSql .= "    UO.num_unidade,                                 ".$stQuebra;
    $stSql .= "    TC.identificador,                               ".$stQuebra;
  $stSql .= "    UO.nom_unidade,                                 ".$stQuebra;
    $stSql .= "    PJ.cnpj                                         ".$stQuebra;
  $stSql .= "FROM                                                ".$stQuebra;
    $stSql .= "    tcers.uniorcam AS TC,                           ".$stQuebra;
    $stSql .= "    sw_cgm_pessoa_juridica AS PJ,                   ".$stQuebra;
    $stSql .= "    orcamento.unidade AS UO                         ".$stQuebra;
  $stSql .= "INNER JOIN                                          ".$stQuebra;
    $stSql .= "    orcamento.orgao AS OO                           ".$stQuebra;
  $stSql .= "ON                                                  ".$stQuebra;
    $stSql .= "        (UO.exercicio   = OO.exercicio              ".$stQuebra;
    $stSql .= "    AND UO.num_orgao    = OO.num_orgao)             ".$stQuebra;
    $stSql .= "WHERE                                               ".$stQuebra;
    $stSql .= "        (UO.exercicio   = TC.exercicio              ".$stQuebra;
    $stSql .= "    AND UO.num_unidade  = TC.num_unidade            ".$stQuebra;
    $stSql .= "    AND UO.num_orgao    = TC.num_orgao              ".$stQuebra;
    $stSql .= "    AND PJ.numcgm       = TC.numcgm)                ".$stQuebra;
  $stSql .= "    AND TC.exercicio <= '".$this->getDado('exercicio')."'       ".$stQuebra;
  $stSql .= "AND TC.identificador IN(".$this->getDado('identificador').")    ".$stQuebra;

  $stSql .= "UNION                                       ".$stQuebra;

  $stSql .= "SELECT                                      ".$stQuebra;
  $stSql .= "    '2004' as exercicio,                    ".$stQuebra;
  $stSql .= "    OO.num_orgao,                           ".$stQuebra;
  $stSql .= "    UO.num_unidade,                         ".$stQuebra;
  $stSql .= "    TC.identificador,                       ".$stQuebra;
  $stSql .= "    'UNIORCAM' as nom_unidade,              ".$stQuebra;
  $stSql .= "    PJ.cnpj                                 ".$stQuebra;
  $stSql .= "FROM                                        ".$stQuebra;
  $stSql .= "    tcers.uniorcam AS TC,                   ".$stQuebra;
  $stSql .= "    sw_cgm_pessoa_juridica AS PJ,           ".$stQuebra;
  $stSql .= "    orcamento.unidade AS UO                 ".$stQuebra;
  $stSql .= "INNER JOIN                                  ".$stQuebra;
  $stSql .= "    orcamento.orgao AS OO                   ".$stQuebra;
  $stSql .= "ON                                          ".$stQuebra;
  $stSql .= "        (UO.exercicio   = OO.exercicio      ".$stQuebra;
  $stSql .= "    AND UO.num_orgao    = OO.num_orgao)     ".$stQuebra;
  $stSql .= "WHERE                                       ".$stQuebra;
  $stSql .= "        (UO.exercicio   = TC.exercicio      ".$stQuebra;
  $stSql .= "    AND UO.num_unidade  = TC.num_unidade    ".$stQuebra;
  $stSql .= "    AND UO.num_orgao    = TC.num_orgao      ".$stQuebra;
  $stSql .= "    AND PJ.numcgm            = TC.numcgm    ".$stQuebra;
  $stSql .= "    AND TC.exercicio    = '2005')           ".$stQuebra;
  $stSql .= "AND TC.identificador IN(".$this->getDado('identificador').")    ".$stQuebra;

    return $stSql;
}

/* Utilizado no e-Sfinge (TCE-SC) */
function recuperaUnidadeOrcamentaria(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaUnidadeOrcamentaria();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaUnidadeOrcamentaria()
{
$stSql = "
               SELECT orcamento.unidade.exercicio
                    , orcamento.unidade.num_unidade
                    , orcamento.unidade.num_orgao
                    , orgao.nom_orgao || ' - ' || unidade.nom_unidade as nom_unidade
                 FROM orcamento.unidade
                 JOIN orcamento.orgao
                   ON orcamento.orgao.exercicio = orcamento.unidade.exercicio
                  AND orcamento.orgao.num_orgao = orcamento.unidade.num_orgao
                WHERE orcamento.unidade.exercicio = '".$this->getDado('exercicio')."'";

    return $stSql;
}

function recuperaOrgaoUnidadeOrcamentaria(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql       = $this->montaRecuperaOrgaoUnidadeOrcamentaria();

    $obErro      = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaOrgaoUnidadeOrcamentaria()
{
    $stSql = "SELECT orcamento.unidade.exercicio
                 , orcamento.unidade.num_unidade
                 , orcamento.unidade.num_orgao
                 , orgao.nom_orgao
                 , unidade.nom_unidade
              FROM orcamento.unidade
              JOIN orcamento.orgao
                ON orcamento.orgao.exercicio = orcamento.unidade.exercicio
               AND orcamento.orgao.num_orgao = orcamento.unidade.num_orgao
             WHERE orcamento.unidade.exercicio   = '".$this->getDado('exercicio')."'
               AND orcamento.unidade.num_orgao   = ".$this->getDado('num_orgao')."
               AND orcamento.unidade.num_unidade = ".$this->getDado('num_unidade');

    return $stSql;
}

function recuperaPorOrgao(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql       = $this->montaRecuperaPorOrgao();

    $obErro      = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaPorOrgao()
{
    $stSql = "SELECT unidade.num_unidade, unidade.nom_unidade
                FROM orcamento.unidade
               WHERE unidade.exercicio   = '".$this->getDado('exercicio')."'
                 AND unidade.num_orgao   = ".$this->getDado('num_orgao');

    return $stSql;
}

}
