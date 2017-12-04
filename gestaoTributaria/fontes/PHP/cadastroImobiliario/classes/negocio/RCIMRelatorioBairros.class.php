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
     * Classe de regra de Relatório de Bairros
     * Data de Criação: 23/03/2005

     * @author Analista: Fábio Bertoldi Rodrigues
     * @author Desenvolvedor: Marcelo B. Paulino

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMRelatorioBairros.class.php 63839 2015-10-22 18:08:07Z franver $

     * Casos de uso: uc-05.01.19
*/

/*
$Log$
Revision 1.7  2006/09/18 09:12:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."PersistenteRelatorio.class.php"    );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoBairro.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php"              );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMBairroAliquota.class.php");
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMBairroValorM2.class.php");

/**
    * Classe de Regra para relatório de Bairros
    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino
*/
class RCIMRelatorioBairros extends PersistenteRelatorio
{
/**
    * @access Private
    * @var Integer
*/
var $inCodInicio;
/**
    * @access Private
    * @var Integer
*/
var $inCodTermino;
/**
    * @access Private
    * @var Integer
*/
var $stOrder;
/**
    * @var Object
    * @access Private
*/
var $obTBairro;
/**
    * @var Object
    * @access Private
*/
var $obRBairro;
/**
    * @access Public
    * @param String $valor
*/
var $obTCIMBairroValorM2;
var $boRSMD;// = false;
var $boAliquota;// = false;
function setCodInicio($valor) { $this->inCodInicio    = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodTermino($valor) { $this->inCodTermino   = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setOrder($valor) { $this->stOrder        = $valor; }

/**
    * @access Public
    * @return Integer
*/

function setboRSMD($valor) { $this->boRSMD     = $valor; }
function setboAliquota($valor) { $this->boAliquota = $valor; }

function getCodInicio() { return $this->inCodInicio;  }
/**
    * @access Public
    * @return Integer
*/
function getCodTermino() { return $this->inCodTermino; }
/**
    * @access Public
    * @return Integer
*/
function getOrder() { return $this->stOrder;      }

/**
    * Método Construtor
    * @access Private
*/

function getboRSMD() { return $this->boRSMD; }
function getboAliquota() { return $this->boAliquota; }

function RCIMRelatorioBairros()
{
    $this->obTBairro = new TBairro;
    $this->obRBairro = new RCIMBairro;
//    $this->obTCIMBairroValorM2 = new TCIMBairroValorM2;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    $stFiltro = "";
    if ( $this->obRBairro->getNomeBairro() ) {
        $stFiltro .= " AND UPPER ( B.nom_bairro ) like UPPER ( '%".$this->obRBairro->getNomeBairro()."%' )";
    }
    if ( $this->obRBairro->getCodigoMunicipio() ) {
        $stFiltro .= " AND M.cod_municipio = '".$this->obRBairro->getCodigoMunicipio()."'";
    }
    if ( $this->obRBairro->getCodigoUF() ) {
        $stFiltro .= " AND M.cod_uf = '".$this->obRBairro->getCodigoUF()."'";
    }
    if ( $this->getCodInicio() AND !$this->getCodTermino() ) {
        $stFiltro .= " AND B.cod_bairro >= ".$this->inCodInicio;
    } elseif ( !$this->getCodInicio() AND $this->getCodTermino() ) {
        $stFiltro .= " AND B.cod_bairro <= ".$this->inCodTermino;
    } elseif ( $this->getCodInicio() AND $this->getCodTermino() ) {
        $stFiltro .= " AND B.cod_bairro between ".$this->inCodInicio." AND ".$this->inCodTermino ;
    }

    switch ($this->stOrder) {
        case 'codigo':
            $stOrder = " B.cod_bairro, U.cod_uf, M.cod_municipio";
        break;
        case 'uf':
            $stOrder = " U.cod_uf, M.nom_municipio, B.nom_bairro";
        break;
        case 'municipio':
            $stOrder = " M.nom_municipio, U.cod_uf, B.nom_bairro";
        break;
        case 'bairro':
            $stOrder = " B.nom_bairro, U.cod_uf, M.nom_municipio";
        break;
        default: $stOrder = " U.nom_uf, M.nom_municipio, B.nom_bairro, B.cod_bairro";
    }

    $obErro = $this->obTBairro->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder );
    return $obErro;
}

function getRecordSetValor(&$rsRecordSet)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecordSetValor( $this->stOrder );
//    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecordSetValor($stOrder="")
{
    $stSql = "";
    $stSql .= " SELECT                                     \n" ;
    $stSql .= "      B.*,                                  \n" ;
    $stSql .= "      U.nom_uf,                             \n" ;
    $stSql .= "      M.nom_municipio                      \n" ;

    if ($this->boRSMD == true) {
        $stSql .= "     , m2.valor_m2_territorial,   \n" ;
        $stSql .= "      m2.valor_m2_predial \n" ;
    }

    if ($this->boAliquota == true) {
        $stSql .= " ,    aliquota.aliquota_territorial,  \n";
        $stSql .= "      aliquota.aliquota_predial  \n";
    }

    $stSql .= "   FROM                                   \n" ;
    $stSql .= "      sw_bairro     AS B \n" ;

    if ($this->boRSMD == true) {
         $stSql .= " LEFT JOIN (SELECT *  \n" ;
         $stSql .= "          FROM imobiliario.bairro_valor_m2   \n" ;
         $stSql .= "             INNER JOIN( SELECT max(timestamp) as timestamp     \n" ;
         $stSql .= "                    , cod_bairro    \n" ;
         $stSql .= "                    , cod_uf        \n" ;
         $stSql .= "                    , cod_municipio     \n" ;
         $stSql .= "                FROM imobiliario.bairro_valor_m2    \n" ;
         $stSql .= "               GROUP BY cod_bairro, cod_uf, cod_municipio) as valor_m2_predial  \n" ;
         $stSql .= "          USING(cod_bairro, cod_uf, cod_municipio, timestamp) )AS m2    \n" ;
         $stSql .= "   USING( cod_bairro, cod_uf, cod_municipio )                          \n" ;
    }

    if ($this->boAliquota == true) {
        $stSql .= " LEFT JOIN (SELECT *     \n" ;
        $stSql .= "          FROM imobiliario.bairro_aliquota   \n" ;
        $stSql .= "             INNER JOIN( SELECT max(timestamp) as timestamp  \n" ;
        $stSql .= "                    , cod_bairro     \n" ;
        $stSql .= "                    , cod_uf     \n" ;
        $stSql .= "                    , cod_municipio  \n" ;
        $stSql .= "                FROM imobiliario.bairro_aliquota     \n" ;
        $stSql .= "               GROUP BY cod_bairro, cod_uf, cod_municipio) as aliquota_temp  \n" ;
        $stSql .= "          USING(cod_bairro, cod_uf, cod_municipio, timestamp) )AS aliquota   \n" ;
        $stSql .= "   USING( cod_bairro, cod_uf, cod_municipio )    \n" ;
    }

    $stSql .= " , ";
    $stSql .= "      sw_uf         AS U,                        \n" ;
    $stSql .= "      sw_municipio  AS M                     \n" ;
    $stSql .= "   WHERE                                         \n" ;
    $stSql .= "      B.cod_uf        = U.cod_uf        AND      \n" ;
    $stSql .= "      B.cod_municipio = M.cod_municipio AND      \n" ;
    $stSql .= "      M.cod_uf        = U.cod_uf                 \n" ;
//    $stSql .= "  ORDER BY  M.nom_municipio, U.cod_uf, B.nom_bairro  \n" ;

    $stFiltro = "";
    if ( $this->obRBairro->getNomeBairro() ) {
        $stFiltro .= " AND UPPER ( B.nom_bairro ) like UPPER ( '%".$this->obRBairro->getNomeBairro()."%' )";
    }
    if ( $this->obRBairro->getCodigoMunicipio() ) {
        $stFiltro .= " AND M.cod_municipio = '".$this->obRBairro->getCodigoMunicipio()."'";
    }
    if ( $this->obRBairro->getCodigoUF() ) {
        $stFiltro .= " AND M.cod_uf = '".$this->obRBairro->getCodigoUF()."'";
    }
    if ( $this->getCodInicio() AND !$this->getCodTermino() ) {
        $stFiltro .= " AND B.cod_bairro >= ".$this->inCodInicio;
    } elseif ( !$this->getCodInicio() AND $this->getCodTermino() ) {
        $stFiltro .= " AND B.cod_bairro <= ".$this->inCodTermino;
    } elseif ( $this->getCodInicio() AND $this->getCodTermino() ) {
        $stFiltro .= " AND B.cod_bairro between ".$this->inCodInicio." AND ".$this->inCodTermino ;
    }

    switch ($this->stOrder) {
        case 'codigo':
            $stOrder = " B.cod_bairro, U.cod_uf, M.cod_municipio";
        break;
        case 'uf':
            $stOrder = " U.cod_uf, M.nom_municipio, B.nom_bairro";
        break;
        case 'municipio':
            $stOrder = " M.nom_municipio, U.cod_uf, B.nom_bairro";
        break;
        case 'bairro':
            $stOrder = " B.nom_bairro, U.cod_uf, M.nom_municipio";
        break;
        default: $stOrder = " U.nom_uf, M.nom_municipio, B.nom_bairro, B.cod_bairro";
    }

    $stSql = $stSql." ".$stFiltro." ORDER BY ".$stOrder;

    return $stSql;
   }
}
