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
    * Classe de regra de negócio para Natureza Jurídica
    * Data de Criação: 22/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMNaturezaJuridica.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.08
*/

/*
$Log$
Revision 1.7  2007/02/27 13:48:07  cassiano
Bug #8434#

Revision 1.6  2007/02/26 20:23:35  cassiano
Bug #8431#

Revision 1.5  2007/02/26 19:58:46  cassiano
Bug #8430#

Revision 1.4  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMNaturezaJuridica.class.php"  );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMBaixaNaturezaJuridica.class.php"  );

class RCEMNaturezaJuridica
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoNatureza;
/**
    * @access Private
    * @var String
*/
var $stNomeNatureza;
/**
    * @access Private
    * @var Date
*/
var $dtDataBaixa;
/**
    * @access Private
    * @var String
*/
var $stMotivoBaixa;

//SETTERS
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoNatureza($valor) { $this->inCodigoNatureza = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNomeNatureza($valor) { $this->stNomeNatureza = $valor;   }
/**
    * @access Public
    * @param Date $valor
*/
function setDataBaixa($valor) { $this->dtDataBaixa = $valor;      }
/**
    * @access Public
    * @param String $valor
*/
function setMotivoBaixa($valor) { $this->stMotivoBaixa = $valor;    }

//GETTERS
/**
    * @access Public
    * @return Integer
*/
function getCodigoNatureza() { return $this->inCodigoNatureza; }
/**
    * @access Public
    * @return String
*/
function getNomeNatureza() { return $this->stNomeNatureza;   }
/**
    * @access Public
    * @return Date
*/
function getDataBaixa() { return $this->dtDataBaixa;      }
/**
    * @access Public
    * @return String
*/
function getMotivoBaixa() { return $this->stMotivoBaixa;    }

//METODO CONSTRUTOR
/**
    * Método construtor
    * @access Private
*/
function RCEMNaturezaJuridica()
{
    $this->obTCEMNaturezaJuridica = new TCEMNaturezaJuridica;
    $this->obTCEMBaixaNaturezaJuridica = new TCEMBaixaNaturezaJuridica;
    $this->obTransacao            = new Transacao;
}
/**
    * Inclui os registros de natureza jurídica
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirNaturezaJuridica($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $arCodigoNatureza = explode("-",$this->inCodigoNatureza);
            $this->obTCEMNaturezaJuridica->setDado( "cod_natureza", $arCodigoNatureza[0].$arCodigoNatureza[1] );
            $this->obTCEMNaturezaJuridica->setDado( "nom_natureza", $this->stNomeNatureza );
            $obErro = $this->obTCEMNaturezaJuridica->recuperaPorChave($rsRecordSet, $boTransacao);
            if ( !$obErro->ocorreu() ) {
                if ( !$rsRecordSet->eof() ) {
                    $obErro->setDescricao("A natureza ".$rsRecordSet->getCampo("nom_natureza")." já foi cadastrada com o código ".$this->inCodigoNatureza."!");
                } else {
                    $obErro = $this->validaNomeNaturezaJuridica ( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $obErro = $this->obTCEMNaturezaJuridica->inclusao( $boTransacao );
                    }
                }
            }
        }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMNaturezaJuridica );

    return $obErro;
}

/**
    * Excluir uma natureza
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirNaturezaJuridica($boTransacao = "")
{
$boFlaTransacao = false;
$obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $arCodigoNatureza = explode("-",$this->inCodigoNatureza);
        $this->obTCEMNaturezaJuridica->setDado( "cod_natureza", $arCodigoNatureza[0].$arCodigoNatureza[1] );
        $this->obTCEMNaturezaJuridica->setDado( "nom_natureza" , $this->stNomeNatureza );
        $obErro = $this->obTCEMNaturezaJuridica->exclusao( $boTransacao );
        if ( $obErro->ocorreu() ) {
            if ( strpos( $obErro->getDescricao(), "fk_" ) !== false ) {
               $obErro->setDescricao("Natureza jurídica $this->inCodigoNatureza ainda está sendo referenciada pelo sistema!");
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMNaturezaJuridica );

    return $obErro;
}

/**
    * Altera uma  categoria
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function alterarNaturezaJuridica($boTransacao = "")
{
    $boFlaTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMNaturezaJuridica->setDado( "cod_natureza", $this->inCodigoNatureza );
        $this->obTCEMNaturezaJuridica->setDado( "nom_natureza" , $this->stNomeNatureza );
        $obErro = $this->validaNomeNaturezaJuridica ( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obTCEMNaturezaJuridica->alteracao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMNaturezaJuridica );

    return $obErro;

}

/**
    * Recupera os registros de natureza jurídica de acordo com o filtro
    * @access Public
    * @param  Object $rsRecordSet Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarNaturezaJuridica(&$rsRecordSet, $boTransacao = "", $boBaixa = false)
{
    $stFiltro = "";
    if ( $this->getCodigoNatureza() ) {
      $stFiltro .= "AND N.cod_natureza = ".$this->getCodigoNatureza();
    }
    if ( $this->getNomeNatureza() ) {
            $stFiltro .= "AND UPPER(N.nom_natureza ) like UPPER( '%".$this->getNomeNatureza()."%' ) ";
    }
    $stOrdem = " ORDER BY N.cod_natureza";
    if ($boBaixa) {
        $obErro = $this->obTCEMNaturezaJuridica->recuperaNaturezaParaBaixa( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    } else {
        $obErro = $this->obTCEMNaturezaJuridica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    }

    return $obErro;
}

/**
    * baixa a natureza jurídica setada
    * @access Public
    * @param  Object $rsRecordSet Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function baixarNaturezaJuridica($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMBaixaNaturezaJuridica->setDado( "cod_natureza" , $this->getCodigoNatureza() );
        $this->obTCEMBaixaNaturezaJuridica->setDado( "motivo" , $this->stMotivoBaixa );
        $obErro = $this->obTCEMBaixaNaturezaJuridica->inclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMBaixaNaturezaJuridica );

    return $obErro;
}

/**
    * Recupera do banco de dados a Natureza Jurídica da Atividade Setada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function consultarNaturezaJuridica($boTransacao = "")
{
    $obErro = new Erro;
        if ($this->inCodigoNatureza) {
            $obErro = $this->listarNaturezaJuridica( $rsNatureza, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->stNomeNatureza   = $rsNomeAtividade->getCampo( "nom_Natureza" );
            }
        }

        return $obErro;
}
function validaNomeNaturezaJuridica($boTransacao = "")
{
    $stFiltro = " WHERE  nom_natureza = '".$this->stNomeNatureza."' ";
    if ($this->inCodigoNatureza AND $this->stNomeNatureza) {
        $stFiltro .= " AND cod_natureza <> ".$this->inCodigoNatureza;
    }
    $stOrdem = "";
    $obErro = $this->obTCEMNaturezaJuridica->recuperaTodos( $rsNatureza, $stFiltro, $stOrdem, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsNatureza->eof() ) {
        $obErro->setDescricao( "Já existe outro nome de natureza jurídica  cadastrado com o nome ".$this->stNomeNatureza."!" );
    }

    return $obErro;
}

}
