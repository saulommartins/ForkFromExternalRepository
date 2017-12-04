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
    * Classe de regra de negócio Modelo
    * Data de Criação: 12/05/2005

    * @author Analista: Diego Barbosa
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2006-10-27 16:37:56 -0300 (Sex, 27 Out 2006) $

    * Casos de uso uc-02.05.01, uc-02.01.35

*/

/*
$Log$
Revision 1.7  2006/10/27 19:37:56  cako
Bug #6773#

Revision 1.6  2006/08/25 17:49:01  fernando
Bug #6773#

Revision 1.5  2006/07/21 14:13:58  cleisson
Bug #6624#

Revision 1.4  2006/07/05 20:44:40  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_LRF_MAPEAMENTO."TLRFTCERSModeloLRF.class.php"  );
include_once ( CAM_GF_LRF_NEGOCIO."RLRFTCERSQuadro.class.php"          );

/**
    * Classe de Regra de Modelo
    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class RLRFTCERSModelo
{
/**
    * @var Object
    * @access Private
*/
var $obTLRFTCERSModelo;
/**
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var Integer
    * @access Private
*/
var $inCodModelo;
/**
    * @var String
    * @access Private
*/
var $stNomModelo;
/**
    * @var String
    * @access Private
*/
var $stNomModeloOrcamento;
/**
    * @var Object
    * @access Private
*/
var $arRLRFTCERSQuadro;
/**
    * @var Object
    * @access Private
*/
var $roUltimoQuadro;

/**
    * @access Public
    * @param Object $valor
*/
function setTLRFTCERSModelo($valor) { $this->obTLRFTCERSModelo                   = $valor;  }
/**
    * @access Public
    * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio                      = $valor;  }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodModelo($valor) { $this->inCodModelo                      = $valor;  }
/**
    * @access Public
    * @param String $valor
*/
function setNomModeloOrcamento($valor) { $this->stNomModeloOrcamento            = $valor;  }
/**
    * @access Public
    * @param String $valor
*/
function setNomModelo($valor) { $this->stNomModelo            = $valor;  }
/**
    * @access Public
    * @param Array $valor
*/
function setRLRFTCERSQuadro($valor) { $this->arRLRFTCERSQuadro                   = $valor;  }
/**
    * @access Public
    * @param Object $valor
*/
function setUltimoQuadro($valor) { $this->roUltimoQuadro                   = $valor;  }

/**
    * @access Public
    * @return Object
*/
function getTLRFTCERSModeloo() { return $this->obTLRFTCERSModelo;                   }
/**
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;                      }
/**
    * @access Public
    * @return Integer
*/
function getCodModelo() { return $this->inCodModelo;                      }
/**
    * @access Public
    * @return String
*/
function getNomModelo() { return $this->stNomModelo;                      }
/**
    * @access Public
    * @return String
*/
function getNomModeloOrcamento() { return $this->stNomModeloOrcamento;                      }
/**
    * @access Public
    * @return Array
*/
function getRLRFTCERSQuadro() { return $this->arRLRFTCERSQuadro;                   }
/**
    * @access Public
    * @return Object
*/
function getUltimoQuadro() { return $this->roUltimoQuadro;                   }

/**
     * Método construtor
     * @access Private
*/
function RLRFTCERSModelo()
{
    $this->obTLRFTCERSModelo = new TLRFTCERSModeloLRF();
    $this->obTransacao    = new Transacao();
}

/**
    * Método para adicionar quadros
    * @access Public
*/
function addQuadro()
{
    $this->arRLRFTCERSQuadro[] = new RLRFTCERSQuadro( $this );
    $this->roUltimoQuadro = &$this->arRLRFTCERSQuadro[ count( $this->arRLRFTCERSQuadro ) -1 ];
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
    if( $this->stExercicio )
        $stFiltro = " exercicio = '".$this->stExercicio."' AND ";
    if( $this->inCodModelo )
        $stFiltro .= " cod_modelo = ".$this->inCodModelo." AND ";
    if( $this->stNomModelo )
        $stFiltro .= " TO_LOWER( cod_modelo ) like TO_LOWER( '".$this->stCodModelo."' ) || '%' AND ";

    $stFiltro = ( $stFiltro ) ? ' WHERE '.substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';
    $stOrder = ( $stOrder ) ? $stOrder : " exercicio, cod_modelo ";
    $obErro = $this->obTLRFTCERSModelo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarModelosAjuste(&$rsRecordSet, $stOrder = '', $boTransacao = "")
{
    $stFiltro = " cod_modelo <>9 AND cod_modelo <> 14 AND ";
    if( $this->stExercicio )
        $stFiltro .= " exercicio = '".$this->stExercicio."' AND ";
    if( $this->inCodModelo )
        $stFiltro .= " cod_modelo = ".$this->inCodModelo." AND ";
    if( $this->stNomModelo )
        $stFiltro .= " TO_LOWER( cod_modelo ) like TO_LOWER( '".$this->stCodModelo."' ) || '%' AND ";

    $stFiltro = ( $stFiltro ) ? ' WHERE '.substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';
    $stOrder = ( $stOrder ) ? $stOrder : " exercicio, cod_modelo ";
    $obErro = $this->obTLRFTCERSModelo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Array  $arRLRFTCERSQuadro Retorna array de objetos Quadro
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarQuadros(&$arRLRFTCERSQuadros, $boTransacao = "")
{
    $obRLRFTCERSQuadro = new RLRFTCERSQuadro( $this );
    $obErro = $obRLRFTCERSQuadro->listar( $rsQuadros, 'cod_quadro', $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $inCount = 0;
        while ( !$rsQuadros->eof() ) {
            $arRLRFTCERSQuadros[$inCount] = new RLRFTCERSQuadro( $this );
            $arRLRFTCERSQuadros[$inCount]->setCodQuadro( $rsQuadros->getCampo( 'cod_quadro' ) );
            $arRLRFTCERSQuadros[$inCount]->setNomQuadro( $rsQuadros->getCampo( 'nom_quadro' ) );
            $obErro = $arRLRFTCERSQuadros[$inCount]->consultar( $boTransacao );

            if ( $obErro->ocorreu() ) {
                break;
            }

            $inCount++;
            $rsQuadros->proximo();
        }
    }

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    $this->obTLRFTCERSModelo->setDado( 'exercicio' , $this->stExercicio );
    $this->obTLRFTCERSModelo->setDado( 'cod_modelo', $this->inCodModelo  );
    $obErro = $this->obTLRFTCERSModelo->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->stNomModelo = $rsRecordSet->getCampo( 'nom_modelo' );
        $this->stNomModeloOrcamento = $rsRecordSet->getCampo( 'nom_modelo_orcamento' );
        $obErro = $this->listarQuadros( $arRLRFTCERSQuadros, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->arRLRFTCERSQuadro = $arRLRFTCERSQuadros;
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
        if ( count( $this->arRLRFTCERSQuadro ) ) {
            foreach ($this->arRLRFTCERSQuadro as $obRLRFTCERSQuadro) {
                $obErro = $obRLRFTCERSQuadro->salvarValorContas( $boTransacao );
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
        $this->obTLRFTCERSModelo->setDado( "exercicio", $this->stExercicio );
        $this->obTLRFTCERSModelo->setDado( "nom_modelo", $this->stNomModelo );
        $this->obTLRFTCERSModelo->setDado( "nom_modelo_orcamento", $this->stNomModeloOrcamento );
        if ($this->inCodModelo) {
            $this->obTLRFTCERSModelo->setDado( "cod_modelo", $this->inCodModelo );
            $obErro = $this->obTLRFTCERSModelo->alteracao( $boTransacao );
        } else {
            $this->obTLRFTCERSModelo->proximoCod( $this->inCodModelo, $boTransacao );
            $this->obTLRFTCERSModelo->setDado( "cod_modelo", $this->inCodModelo );
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->obTLRFTCERSModelo->inclusao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTLRFTCERSModelo );

    return $obErro;
}

}
?>
