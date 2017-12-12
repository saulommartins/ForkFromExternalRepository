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
    * Classe de regra de negócio de Quadro
    * Data de Criação: 13/05/2005

    * @author Analista: Diego Barbosa
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso uc-02.05.01

*/

/*
$Log$
Revision 1.4  2006/07/05 20:44:40  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_LRF_MAPEAMENTO."TLRFTCERSQuadroModeloLRF.class.php" );
include_once ( CAM_GF_LRF_NEGOCIO."RLRFTCERSContaPlano.class.php"           );

/**
    * Classe de Regra de Quadro
    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class RLRFTCERSQuadro
{
/**
    * @var Object
    * @access Private
*/
var $obTLRFTCERSQuadroModelo;
/**
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @var Integer
    * @access Private
*/
var $inCodQuadro;
/**
    * @var String
    * @access Private
*/
var $stNomQuadro;
/**
    * @var Object
    * @access Private
*/
var $arRLRFTCERSContaPlano;
/**
    * @var Object
    * @access Private
*/
var $roUltimaContaPlano;
/**
    * @var Object
    * @access Private
*/
var $roRLRFTCERSModelo;

/**
    * @access Public
    * @param Object $valor
*/
function setTLRFTCERSQuadroModelo($valor) { $this->obTLRFTCERSQuadroModelo = $valor;  }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodQuadro($valor) { $this->inCodQuadro          = $valor;  }
/**
    * @access Public
    * @param String $valor
*/
function setNomQuadro($valor) { $this->stNomModelo          = $valor;  }
/**
    * @access Public
    * @param Array $valor
*/
function setRLRFTCERSContaPlano($valor) { $this->arRLRFTCERSContaPlano   = $valor;  }
/**
    * @access Public
    * @param Object $valor
*/
function setUltimaContaPlano($valor) { $this->roUltimaContaPlano   = $valor;  }

/**
    * @access Public
    * @return Object
*/
function getTLRFTCERSQuadroModelo() { return $this->obTLRFTCERSQuadroModelo;            }
/**
    * @access Public
    * @return Integer
*/
function getCodQuadro() { return $this->inCodQuadro;                     }
/**
    * @access Public
    * @return String
*/
function getNomQuadro() { return $this->stNomQuadro;                     }
/**
    * @access Public
    * @return Array
*/
function getRLRFTCERSContaPlano() { return $this->arRLRFTCERSContaPlano;              }
/**
    * @access Public
    * @return Object
*/
function getUltimaContaPlano() { return $this->roUltimaContaPlano;              }

/**
     * Método construtor
     * @access Private
*/
function RLRFTCERSQuadro(&$roRLRFTCERSModelo)
{
    $this->obTLRFTCERSQuadroModelo = new TLRFTCERSQuadroModeloLRF();
    $this->roRLRFTCERSModelo       = &$roRLRFTCERSModelo;
}

/**
    * Método para adicionar conta plano
    * @access Public
*/
function addContaPlano()
{
    $this->obTransacao          = new Transacao();
    $this->arRLRFTCERSContaPlano[] = new RLRFTCERSContaPlano( $this );
    $this->roUltimaContaPlano   = &$this->arRLRFTCERSContaPlano[ count( $this->arRLRFTCERSContaPlano ) -1 ];
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $stOrder = '', $boTransacao = "")
{
    if( $this->roRLRFTCERSModelo->getExercicio() )
        $stFiltro .= " exercicio = '".$this->roRLRFTCERSModelo->getExercicio()."' AND ";
    if( $this->roRLRFTCERSModelo->getCodModelo() )
        $stFiltro .= " cod_modelo = ".$this->roRLRFTCERSModelo->getCodModelo()." AND ";
    if( $this->inCodQuadro )
        $stFiltro .= " cod_quadro = ".$this->inCodQuadro." AND ";
    if( $this->stNomQuadro )
        $stFiltro .= " TO_LOWER( nom_modelo ) like TO_LOWER( '".$this->stNomModelo."' ) || '%' AND ";

    $stFiltro = ( $stFiltro ) ? ' WHERE '.substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';
    $stOrder = ( $stOrder ) ? $stOrder : " exercicio, cod_modelo, cod_quadro ";
    $obErro = $this->obTLRFTCERSQuadroModelo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Metodo para monstar o array de objetos ContaPlano
    * @access Public
    * @param  Array  $arRLRFTCERSContaPlano Retorna array de objetos ContaPlano
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarContaPlano(&$arRLRFTCERSContaPlano, $boTransacao = "")
{
    $obRLRFTCERSContaPlano = new RLRFTCERSContaPlano( $this );
    $obErro = $obRLRFTCERSContaPlano->listar( $rsContaPlano, 'cod_quadro', $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $inCount = 0;
        while ( !$rsContaPlano->eof() ) {
            $arRLRFTCERSContaPlano[$inCount] = new RLRFTCERSContaPlano( $this );
            $arRLRFTCERSContaPlano[$inCount]->roRLRFTCERSQuadro->setCodQuadro( $rsContaPlano->getCampo( 'cod_quadro' ) );
            $arRLRFTCERSContaPlano[$inCount]->roRLRFTCERSQuadro->setNomQuadro( $rsContaPlano->getCampo( 'nom_quadro' ) );
            $arRLRFTCERSContaPlano[$inCount]->setCodConta( $rsContaPlano->getCampo( 'cod_conta' ) );
            $obErro = $arRLRFTCERSContaPlano[$inCount]->consultar( $boTransacao );

            if( !$obErro->ocorreu() )
                break;

            $inCount++;
            $rsContaPlano->proximo();
        }
    }

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    $this->obTLRFTCERSQuadroModelo->setDado( 'exercicio' , $this->roRLRFTCERSModelo->getExercicio() );
    $this->obTLRFTCERSQuadroModelo->setDado( 'cod_modelo', $this->roRLRFTCERSModelo->getCodModelo()  );
    $this->obTLRFTCERSQuadroModelo->setDado( 'cod_quadro', $this->inCodQuadro                     );
    $obErro = $this->obTLRFTCERSQuadroModelo->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->stNomQuadro = $rsRecordSet->getCampo( 'nom_quadro' );
    }

    if ( !$obErro->ocorreu() ) {
        $this->stNomQuadro = $rsRecordSet->getCampo( 'nom_quadro' );
        $obErro = $this->listarContaPlano( $arRLRFTCERSContaPlano, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->arRLRFTCERSContaPlano = $arRLRFTCERSContaPlano;
        }
    }

    return $obErro;
}

/**
    * Método para incluir/alterar valor das contas no banco
    * @access Public
    * @param Object $boTransacao
    * @return Object $obErro
*/
function salvarValorContas($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( count( $this->arRLRFTCERSContaPlano ) ) {
            foreach ($this->arRLRFTCERSContaPlano as $obRLRFTCERSContaPlano) {
                $obErro = $obRLRFTCERSContaPlano->salvarValores( $boTransacao );
                if( $obErro->ocorreu() )
                    break;
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTLRFTCERSQuadroModelo );

    return $obErro;
}

/**
    * Método para incluir contas no banco
    * @access Public
    * @param Object $boTransacao
    * @return Object $obErro
*/
function salvarContas($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( count( $this->arRLRFTCERSContaPlano ) ) {
            foreach ($this->arRLRFTCERSContaPlano as $obRLRFTCERSContaPlano) {
                $obErro = $obRLRFTCERSContaPlano->salvar( $boTransacao );
                if( $obErro->ocorreu() )
                    break;
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTLRFTCERSQuadroModelo );

    return $obErro;
}

/**
    * Método para incluir ou alterar dados no banco
    * @access Public
    * @param Object $boTransacao
    * @return Object $obErro
*/
function salvar($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTLRFTCERSQuadroModelo->setDado( "exercicio" , $this->roRLRFTCERSModelo->getExercicio() );
        $this->obTLRFTCERSQuadroModelo->setDado( "cod_modulo", $this->roRLRFTCERSModelo->getCodModelo() );
        $this->obTLRFTCERSQuadroModelo->setDado( "nom_quadro", $this->stNomQuadro                    );
        if ($this->inCodQuadro) {
            $this->obTLRFTCERSQuadroModelo->setDado( "cod_quadro", $this->inCodQuadro );
            $obErro = $this->obTLRFTCERSQuadroModelo->alteracao( $boTransacao );
        } else {
            $obErro = $this->obTLRFTCERSQuadroModelo->proximoCod( $this->inCodQuadro, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTLRFTCERSQuadroModelo->setDado( "cod_quadro", $this->inCodQuadro );
                $obErro = $this->obTLRFTCERSQuadroModelo->inclusao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTLRFTCERSQuadroModelo );

    return $obErro;
}

}
?>
