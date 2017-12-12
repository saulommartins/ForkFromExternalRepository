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
/**
     * Classe de regra de negócio para bairro
     * Data de Criação: 27/09/2004
     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
     * @author Desenvolvedor: Fabio Bertoldi Rodrigues
     * @package URBEM
     * @subpackage Regra
     * $Id: RCIMBairro.class.php 63252 2015-08-07 19:04:21Z evandro $
     * Casos de uso: uc-05.01.05
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoBairro.class.php"      );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoBairroMunicipio.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoMunicipio.class.php"   );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php"          );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMBairroAliquota.class.php");
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMBairroValorM2.class.php");
include_once ( CAM_GA_CGM_MAPEAMENTO."TCGMLogradouro.class.php" );

class RCIMBairro
{
var $dtAliquotaVigencia;
var $inAliquotaCodNorma;
var $inAliquotaTerritorial;
var $inAliquotaPredial;
var $dtMDVigencia;
var $inMDCodNorma;
var $inMDTerritorial;
var $inMDPredial;

/**
    * @access Private
    * @var Object
*/
var $obTCGMLogradouro;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoBairro;
/**
    * @access Private
    * @var String
*/
var $stNomeBairro;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoUF;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoMunicipio;
/**
    * @access Private
    * @var Object
*/
var $obTBairro;
/**
    * @access Private
    * @var Object
*/
var $obTBairroMunicipio;
/**
    * @access Private
    * @var Object
*/
var $obTMunicipio;
/**
    * @access Private
    * @var Object
*/
var $obTUF;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;

//SETTERS
function setAliquotaVigencia($valor) { $this->dtAliquotaVigencia = $valor; }
function setAliquotaCodNorma($valor) { $this->inAliquotaCodNorma = $valor; }
function setAliquotaTerritorial($valor) { $this->inAliquotaTerritorial = $valor; }
function setAliquotaPredial($valor) { $this->inAliquotaPredial = $valor; }
function setMDVigencia($valor) { $this->dtMDVigencia = $valor; }
function setMDCodNorma($valor) { $this->inMDCodNorma = $valor; }
function setMDTerritorial($valor) { $this->inMDTerritorial = $valor; }
function setMDPredial($valor) { $this->inMDPredial = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setCodigoBairro($valor) { $this->inCodigoBairro     = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNomeBairro($valor) { $this->stNomeBairro       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodigoUF($valor) { $this->inCodigoUF         = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodigoMunicipio($valor) { $this->inCodigoMunicipio  = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTBairro($valor) { $this->obTBairro          = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTBairroMunicipio($valor) { $this->obTBairroMunicipio = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTMunicipio($valor) { $this->obTMunicipio       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTUF($valor) { $this->obTUF              = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTransacao($valor) { $this->obTransacao        = $valor; }

//GETTERS
/**
    * @access Public
    * @return Integer
*/
function getCodigoBairro() { return $this->inCodigoBairro;    }
/**
    * @access Public
    * @return String
*/
function getNomeBairro() { return $this->stNomeBairro;      }
/**
    * @access Public
    * @return Integer
*/
function getCodigoUF() { return $this->inCodigoUF;        }
/**
    * @access Public
    * @return Integer
*/
function getCodigoMunicipio() { return $this->inCodigoMunicipio; }
/**
    * @access Public
    * @return Object
*/
function getTBairro() { return $this->obTBairro;         }
/**
    * @access Public
    * @return Object
*/
function getTBairroMunicipio() { return $this->obTBairroMunicipio;}
/**
    * @access Public
    * @return Object
*/
function getTMunicipio() { return $this->obTMunicipio;      }
/**
    * @access Public
    * @return Object
*/
function getTUF() { return $this->obTUF;             }
/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao;       }
/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;       }

//METODO CONSTRUTOR
/**
     * Método construtor
     * @access Private
*/
function __construct()
{
    $this->obTBairro          = new TBairro;
    $this->obTBairroMunicipio = new TBairroMunicipio;
    $this->obTMunicipio       = new TMunicipio;
    $this->obTUF              = new TUF;
    $this->obTCGMLogradouro   = new TCGMLogradouro();
    $this->obTransacao        = new Transacao;
}

/**
    * Inclui os dados setados na tabela de Bairro
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirBairro($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->validaNomeBairro( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obTBairro->proximoCod( $this->inCodigoBairro, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTBairro->setDado( "cod_bairro",    $this->inCodigoBairro    );
                $this->obTBairro->setDado( "nom_bairro",    stripslashes($this->stNomeBairro));
                $this->obTBairro->setDado( "cod_uf",        $this->inCodigoUF        );
                $this->obTBairro->setDado( "cod_municipio", $this->inCodigoMunicipio );
                $obErro = $this->obTBairro->inclusao( $boTransacao );

                if ($this->dtAliquotaVigencia) {
                    $obTCIMBairroAliquota = new TCIMBairroAliquota;
                    $obTCIMBairroAliquota->setDado( "cod_bairro", $this->inCodigoBairro );
                    $obTCIMBairroAliquota->setDado( "cod_uf", $this->inCodigoUF );
                    $obTCIMBairroAliquota->setDado( "cod_municipio", $this->inCodigoMunicipio );
                    $obTCIMBairroAliquota->setDado( "cod_norma", $this->inAliquotaCodNorma );
                    $obTCIMBairroAliquota->setDado( "dt_vigencia", $this->dtAliquotaVigencia );
                    $obTCIMBairroAliquota->setDado( "aliquota_territorial", $this->inAliquotaTerritorial );
                    $obTCIMBairroAliquota->setDado( "aliquota_predial", $this->inAliquotaPredial );
                    $obTCIMBairroAliquota->inclusao( $boTransacao );
                }

                if ($this->dtMDVigencia) {
                    $obTCIMBairroValorM2 = new TCIMBairroValorM2;
                    $obTCIMBairroValorM2->setDado( "cod_bairro", $this->inCodigoBairro );
                    $obTCIMBairroValorM2->setDado( "cod_uf", $this->inCodigoUF );
                    $obTCIMBairroValorM2->setDado( "cod_municipio", $this->inCodigoMunicipio );
                    $obTCIMBairroValorM2->setDado( "cod_norma", $this->inMDCodNorma );
                    $obTCIMBairroValorM2->setDado( "dt_vigencia", $this->dtMDVigencia );
                    $obTCIMBairroValorM2->setDado( "valor_m2_territorial", $this->inMDTerritorial );
                    $obTCIMBairroValorM2->setDado( "valor_m2_predial", $this->inMDPredial );
                    $obTCIMBairroValorM2->inclusao( $boTransacao );
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBairro );

    return $obErro;
}

/**
    * Altera os dados do Bairro setado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarBairro($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->validaNomeBairro( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTBairro->setDado( "cod_bairro",    $this->inCodigoBairro    );
            $this->obTBairro->setDado( "nom_bairro",    stripslashes($this->stNomeBairro)      );
            $this->obTBairro->setDado( "cod_uf",        $this->inCodigoUF        );
            $this->obTBairro->setDado( "cod_municipio", $this->inCodigoMunicipio );
            $obErro = $this->obTBairro->alteracao( $boTransacao );
        }

        if ($this->dtAliquotaVigencia) {
            $obTCIMBairroAliquota = new TCIMBairroAliquota;
            $obTCIMBairroAliquota->setDado( "cod_bairro", $this->inCodigoBairro );
            $obTCIMBairroAliquota->setDado( "cod_uf", $this->inCodigoUF );
            $obTCIMBairroAliquota->setDado( "cod_municipio", $this->inCodigoMunicipio );
            $obTCIMBairroAliquota->setDado( "cod_norma", $this->inAliquotaCodNorma );
            $obTCIMBairroAliquota->setDado( "dt_vigencia", $this->dtAliquotaVigencia );
            $obTCIMBairroAliquota->setDado( "aliquota_territorial", $this->inAliquotaTerritorial );
            $obTCIMBairroAliquota->setDado( "aliquota_predial", $this->inAliquotaPredial );
            $obTCIMBairroAliquota->inclusao( $boTransacao );
        }

        if ($this->dtMDVigencia) {
            $obTCIMBairroValorM2 = new TCIMBairroValorM2;
            $obTCIMBairroValorM2->setDado( "cod_bairro", $this->inCodigoBairro );
            $obTCIMBairroValorM2->setDado( "cod_uf", $this->inCodigoUF );
            $obTCIMBairroValorM2->setDado( "cod_municipio", $this->inCodigoMunicipio );
            $obTCIMBairroValorM2->setDado( "cod_norma", $this->inMDCodNorma );
            $obTCIMBairroValorM2->setDado( "dt_vigencia", $this->dtMDVigencia );
            $obTCIMBairroValorM2->setDado( "valor_m2_territorial", $this->inMDTerritorial );
            $obTCIMBairroValorM2->setDado( "valor_m2_predial", $this->inMDPredial );
            $obTCIMBairroValorM2->inclusao( $boTransacao );
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBairro );

    return $obErro;
}

/**
    * Exclui os dados do Logradouro setado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirBairro($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $this->obTBairro->setDado( "cod_bairro",    $this->inCodigoBairro    );
        $this->obTBairro->setDado( "cod_uf",        $this->inCodigoUF        );
        $this->obTBairro->setDado( "cod_municipio", $this->inCodigoMunicipio );
        $this->obTBairro->recuperaPorChave($rsBairro,$boTransacao);

        if ( $rsBairro->getNumLinhas() > 0 ) {
            $obErro = $this->obTBairro->exclusao( $boTransacao );
        }
    }

    if (!$obErro->ocorreu()) {
        $this->obTCGMLogradouro->setDado( "cod_bairro"      , $this->getCodigoBairro()    );
        $this->obTCGMLogradouro->setDado( "cod_uf"          , $this->getCodigoUF()        );
        $this->obTCGMLogradouro->setDado( "cod_municipio"   , $this->getCodigoMunicipio() );
        $this->obTCGMLogradouro->recuperaBairroCgm($rsBairro,"","",$boTransacao);

        if ( $rsBairro->getNumLinhas() < 0 ) {
            $obErro = $this->obTCGMLogradouro->exclusao( $boTransacao );
        }else{
            $obErro->setDescricao("Bairro está vinculado a um CGM.");
        }
    }

    if ( !$obErro->ocorreu() ) {
        $obTCIMBairroAliquota = new TCIMBairroAliquota;
        $obTCIMBairroAliquota->setDado( "cod_bairro"    ,  $this->inCodigoBairro );
        $obTCIMBairroAliquota->setDado( "cod_uf"        , $this->inCodigoUF );
        $obTCIMBairroAliquota->setDado( "cod_municipio" , $this->inCodigoMunicipio );
        $obErro = $obTCIMBairroAliquota->exclusao( $boTransacao );
    }
    if (!$obErro->ocorreu()) {
        $obTCIMBairroValorM2 = new TCIMBairroValorM2;
        $obTCIMBairroValorM2->setDado( "cod_bairro", $this->inCodigoBairro );
        $obTCIMBairroValorM2->setDado( "cod_uf", $this->inCodigoUF );
        $obTCIMBairroValorM2->setDado( "cod_municipio", $this->inCodigoMunicipio );
        $obErro = $obTCIMBairroValorM2->exclusao( $boTransacao );    
    }
   
    if (!$obErro->ocorreu()) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBairro );
    }else{
        $obErro->setDescricao("Bairro está em uso pelo sistema!");
    }
    
    return $obErro;
}

/**
    * Recupera do banco de dados os dados do Bairro selecionado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarBairro($boTransacao = "")
{
    $this->obTBairro->setDado( "cod_bairro",    $this->inCodigoBairro    );
    $this->obTBairro->setDado( "cod_uf",        $this->inCodigoUF        );
    $this->obTBairro->setDado( "cod_municipio", $this->inCodigoMunicipio );
    $obErro = $this->obTBairro->recuperaPorChave( $rsRecordSet, $boTransacao );

    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->inCodigoBairro    = $rsRecordSet->getCampo("cod_bairro");
        $this->stNomeBairro      = $rsRecordSet->getCampo("nom_bairro");
        $this->inCodigoUF        = $rsRecordSet->getCampo("cod_uf");
        $this->inCodigoMunicipio = $rsRecordSet->getCampo("cod_municipio");
    }

    return $obErro;
}

/**
    * Lista os Bairros conforme o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarBairros(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->stNomeBairro) {
        $stFiltro .= " AND UPPER( B.NOM_BAIRRO ) ";
        $stFiltro .= "LIKE UPPER('".$this->stNomeBairro."%') ";
    }
    if ($this->inCodigoBairro) {
        $stFiltro .= " AND B.COD_BAIRRO = ".$this->inCodigoBairro;
    }
    if ($this->inCodigoUF) {
        $stFiltro .= " AND B.COD_UF = ".$this->inCodigoUF;
    }
    if ($this->inCodigoMunicipio) {
        $stFiltro .= " AND B.COD_MUNICIPIO = ".$this->inCodigoMunicipio;
    }
    $stOrdem = " ORDER BY B.NOM_BAIRRO ";
    $obErro = $this->obTBairro->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista os Bairros conforme o filtro setado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function validaNomeBairro($boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoBairro) {
        //ENTRA NO FILTRO SOMENTE NO CASO DE ALTERACAO
        $stFiltro .= " AND B.COD_BAIRRO <> ".$this->inCodigoBairro;
    }
    $stFiltro .= " AND UPPER( B.NOM_BAIRRO ) ";
    $stFiltro .= "LIKE UPPER( '".$this->stNomeBairro."' ) ";
    $stFiltro .= " AND B.COD_UF = ".$this->inCodigoUF;
    $stFiltro .= " AND B.COD_MUNICIPIO = ".$this->inCodigoMunicipio;
    $stOrdem = " ";
    $obErro = $this->obTBairro->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $obErro->setDescricao( "Já existe um bairro com este nome cadastrado nesta cidade!" );
    }

    return $obErro;
}

/**
    * Lista as Unidades Federais conforme o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarUF(&$rsResultado , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoUF) {
        $stFiltro .= " cod_uf = ".$this->inCodigoUF." AND";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $obErro = $this->obTUF->recuperaTodos( $rsResultado, $stFiltro, "nom_uf", $boTransacao );

    return $obErro;
}

/**
    * Lista os Municipios conforme o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarMunicipios(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->inCodigoUF || $this->getCodigoUF() ) {
        $stFiltro .= " cod_uf = ".$this->inCodigoUF." AND ";
    }
    if ($this->inCodigoMunicipio) {
        $stFiltro .= " COD_MUNICIPIO = ".$this->inCodigoMunicipio." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $obErro = $this->obTMunicipio->recuperaTodos( $rsRecordSet, $stFiltro, "nom_municipio", $boTransacao );

    return $obErro;
}
}
