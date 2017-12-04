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
     * Classe de regra de negócio para tipo de edificação
     * Data de Criação: 15/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMTipoEdificacao.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTipoEdificacao.class.php" );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoTipoEdificacao.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoTipoEdificacaoValor.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTipoEdificacaoAliquota.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTipoEdificacaoValorM2.class.php" );

class RCIMTipoEdificacao
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoTipo;
/**
    * @access Private
    * @var String
*/
var $stNomeTipo;
/**
    * @access Private
    * @var Object
*/
var $obTCIMTipoEdificacao;
/**
    * @access Private
    * @var Object
*/
var $obRCadastroDinamico;
var $dtAliquotaVigencia;
var $inAliquotaCodNorma;
var $inAliquotaTerritorial;
var $inAliquotaPredial;
var $dtMDVigencia;
var $inMDCodNorma;
var $inMDTerritorial;
var $inMDPredial;

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
function setCodigoTipo($valor) { $this->inCodigoTipo = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNomeTipo($valor) { $this->stNomeTipo   = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoTipo() { return $this->inCodigoTipo; }
/**
    * @access Public
    * @return Integer
*/
function getNomeTipo() { return $this->stNomeTipo;   }

/**
     * Método construtor
     * @access Private
*/
function RCIMTipoEdificacao()
{
    $this->obTCIMTipoEdificacao = new TCIMTipoEdificacao;
    $this->obTransacao    = new Transacao;
    $this->obRCadastroDinamico  = new RCadastroDinamico;
    $this->obRCadastroDinamico->setPersistenteValores   ( new TCIMAtributoTipoEdificacaoValor );
    $this->obRCadastroDinamico->setCodCadastro( 5 );
//    $this->obRCadastroDinamico->obRModulo->setCodModulo( 12 );
}

/**
    * Inclui os dados setados na tabela de TipoEdificacao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirTipoEdificacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->validaNomeTipoEdificacao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obRCadastroDinamico->setPersistenteAtributos( new TCIMAtributoTipoEdificacao() );
            $obErro = $this->obTCIMTipoEdificacao->proximoCod( $this->inCodigoTipo, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCIMTipoEdificacao->setDado( "cod_tipo"   , $this->inCodigoTipo    );
                $this->obTCIMTipoEdificacao->setDado( "nom_tipo"   , $this->stNomeTipo      );
                $obErro = $this->obTCIMTipoEdificacao->inclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    if ($this->dtAliquotaVigencia) {
                        $obTCIMTipoEdificacaoAliquota = new TCIMTipoEdificacaoAliquota;
                        $obTCIMTipoEdificacaoAliquota->setDado( "cod_tipo", $this->inCodigoTipo );
                        $obTCIMTipoEdificacaoAliquota->setDado( "cod_norma", $this->inAliquotaCodNorma );
                        $obTCIMTipoEdificacaoAliquota->setDado( "dt_vigencia", $this->dtAliquotaVigencia );
                        $obTCIMTipoEdificacaoAliquota->setDado( "aliquota_territorial", $this->inAliquotaTerritorial );
                        $obTCIMTipoEdificacaoAliquota->setDado( "aliquota_predial", $this->inAliquotaPredial );
                        $obTCIMTipoEdificacaoAliquota->inclusao( $boTransacao );
                    }

                    if ($this->dtMDVigencia) {
                        $obTCIMTipoEdificacaoValorM2 = new TCIMTipoEdificacaoValorM2;
                        $obTCIMTipoEdificacaoValorM2->setDado( "cod_tipo", $this->inCodigoTipo );
                        $obTCIMTipoEdificacaoValorM2->setDado( "cod_norma", $this->inMDCodNorma );
                        $obTCIMTipoEdificacaoValorM2->setDado( "dt_vigencia", $this->dtMDVigencia );
                        $obTCIMTipoEdificacaoValorM2->setDado( "valor_m2_territorial", $this->inMDTerritorial );
                        $obTCIMTipoEdificacaoValorM2->setDado( "valor_m2_predial", $this->inMDPredial );
                        $obTCIMTipoEdificacaoValorM2->inclusao( $boTransacao );
                    }

                    //O Restante dos valores vem setado da página de processamento
                    $arChaveAtributoTipoEdificacao =  array( "cod_tipo" => $this->inCodigoTipo );
                    $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoTipoEdificacao );
                    $obErro = $this->obRCadastroDinamico->salvar( $boTransacao );
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMTipoEdificacao );

    return $obErro;
}

/**
    * Altera os dados do TipoEdificacao selecionado no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarTipoEdificacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->validaNomeTipoEdificacao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ($this->dtAliquotaVigencia) {
                $obTCIMTipoEdificacaoAliquota = new TCIMTipoEdificacaoAliquota;
                $obTCIMTipoEdificacaoAliquota->setDado( "cod_tipo", $this->inCodigoTipo );
                $obTCIMTipoEdificacaoAliquota->setDado( "cod_norma", $this->inAliquotaCodNorma );
                $obTCIMTipoEdificacaoAliquota->setDado( "dt_vigencia", $this->dtAliquotaVigencia );
                $obTCIMTipoEdificacaoAliquota->setDado( "aliquota_territorial", $this->inAliquotaTerritorial );
                $obTCIMTipoEdificacaoAliquota->setDado( "aliquota_predial", $this->inAliquotaPredial );
                $obTCIMTipoEdificacaoAliquota->inclusao( $boTransacao );
            }

            if ($this->dtMDVigencia) {
                $obTCIMTipoEdificacaoValorM2 = new TCIMTipoEdificacaoValorM2;
                $obTCIMTipoEdificacaoValorM2->setDado( "cod_tipo", $this->inCodigoTipo );
                $obTCIMTipoEdificacaoValorM2->setDado( "cod_norma", $this->inMDCodNorma );
                $obTCIMTipoEdificacaoValorM2->setDado( "dt_vigencia", $this->dtMDVigencia );
                $obTCIMTipoEdificacaoValorM2->setDado( "valor_m2_territorial", $this->inMDTerritorial );
                $obTCIMTipoEdificacaoValorM2->setDado( "valor_m2_predial", $this->inMDPredial );
                $obTCIMTipoEdificacaoValorM2->inclusao( $boTransacao );
            }

            $this->obRCadastroDinamico->setPersistenteAtributos( new TCIMAtributoTipoEdificacao() );
            //O Restante dos valores vem setado da página de processamento
            $arChaveAtributoTipoEdificacao =  array( "cod_tipo" => $this->inCodigoTipo );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoTipoEdificacao );
            $obErro = $this->obRCadastroDinamico->salvar( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCIMTipoEdificacao->setDado( "cod_tipo"   , $this->inCodigoTipo    );
                $this->obTCIMTipoEdificacao->setDado( "nom_tipo"   , $this->stNomeTipo      );
                $obErro = $this->obTCIMTipoEdificacao->alteracao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMTipoEdificacao );

    return $obErro;
}

/**
    * Exclui o TipoEdificacao selecionado do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirTipoEdificacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTCIMTipoEdificacaoAliquota = new TCIMTipoEdificacaoAliquota;
        $obTCIMTipoEdificacaoAliquota->setDado( "cod_tipo", $this->inCodigoTipo );
        $obTCIMTipoEdificacaoAliquota->exclusao( $boTransacao );

        $obTCIMTipoEdificacaoValorM2 = new TCIMTipoEdificacaoValorM2;
        $obTCIMTipoEdificacaoValorM2->setDado( "cod_tipo", $this->inCodigoTipo );
        $obTCIMTipoEdificacaoValorM2->exclusao( $boTransacao );

        $this->obRCadastroDinamico->setPersistenteAtributos( new TCIMAtributoTipoEdificacao() );
        $arChaveAtributoTipoEdificacao =  array( "cod_tipo" => $this->inCodigoTipo );
        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoTipoEdificacao );
        $obErro = $this->obRCadastroDinamico->excluir( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMTipoEdificacao->setDado( "cod_tipo"   , $this->inCodigoTipo    );
            $obErro = $this->obTCIMTipoEdificacao->exclusao( $boTransacao );
        }
        if ($obErro->ocorreu() ) {
            if ( strpos($obErro->getDescricao(),"fk_" ) !== false ) {
                $obErro->setDescricao( "Tipo de Edificação ".$this->inCodigoTipo." - ".$this->stNomeTipo." ainda está sendo referenciado pelo sistema!" );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMTipoEdificacao );

    return $obErro;
}

/**
    * Recupera do abnco de dados os dados do TipoEdificacao selecionado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarTipoEdificacao($boTransacao = "")
{
    $this->obTCIMTipoEdificacao->setDado( "cod_tipo"   , $this->inCodigoTipo    );
    $obErro = $this->obTCIMTipoEdificacao->recuperaPorChave( $rsTipo, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->stNomeTipo = $rsTipo->getCampo( "nom_tipo" );
    }

    return $obErro;
}

/**
    * Lista os Tipos de Edificacao disponíveis
    * @access Public
    * @param  Object $rsTipoEdificacao Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarTiposEdificacao(&$rsTipoEdificacao, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoTipo) {
        $stFiltro .= " COD_TIPO = ".$this->inCodigoTipo." AND";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem = " ORDER BY NOM_TIPO ";
    $obErro = $this->obTCIMTipoEdificacao->recuperaTodos( $rsTipoEdificacao, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Valida se o nome do tipo de logradouro não existe
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function validaNomeTipoEdificacao($boTransacao = "")
{
    $stFiltro = " WHERE ";
    if ($this->inCodigoTipo) {
        //ENTRA NO FILTRO SOMENTE NO CASO DE ALTERACAO
        $stFiltro .= " COD_TIPO <> ".$this->inCodigoTipo." AND";
    }
    $stFiltro .= " LOWER( nom_tipo ) ";
    $stFiltro .= "LIKE LOWER( '".$this->stNomeTipo."' ) ";
    $stOrder = "";
    $obErro = $this->obTCIMTipoEdificacao->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $obErro->setDescricao( "Já existe um tipo de edificação cadastrado com o nome ".$this->stNomeTipo."!" );
    }

    return $obErro;
}

}
?>
