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
    * Classe de Regra de Negócio Carteira Vacinacao
    * Data de Criação   : 20/12/2004

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra

      $Revision: 30566 $
      $Name$
      $Author: souzadl $
      $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

      Caso de uso: uc-04.04.07

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCarteiraVacinacao.class.php"                             );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalDependenteCarteiraVacinacao.class.php"                             );

/**
    * Classe de Regra de Negócio Pesssoal Servidor Carteira Vacinacao
    * Data de Criação   : 20/12/2004

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra
*/

class RPessoalCarteiraVacinacao
{
    /**
    * @var Integer
    * @access Private
    */
    public $arCarteira;

    /**
    * @var Integer
    * @access Private
    */
    public $inCodCarteiraVacinacao;
    public $inCodDependente;

    /**
    * @var Date
    * @access Private
    */
    public $dtDataApresentacao;

    /**
    * @var Integer
    * @access Private
    */
    public $boApresentada;

    /**
    * @var Integer
    * @access Private
    */
    public $obTPessoalCarteiraVacinacao;

    /**
    * @var Integer
    * @access Private
    */
    public $obTransacao;

    /**
    * @var Reference Object
    * @access Private
    */
    public $roPessoalDependente;

    /**
    * @access Public
    * @param Object $valor
    */
    public function setCarteira($valor) { $this->arCarteira                      = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setCodCarteiraVacinacao($valor) { $this->inCodCarteiraVacinacao          = $valor; }
    public function setCodDependente($valor) { $this->inCodDependente                 = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setDataApresentacao($valor) { $this->dtDataApresentacao              = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setApresentada($valor) { $this->boApresentada                   = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setTPessoalCarteiraVacinacao($valor) { $this->obTPessoalCarteiraVacinacao     = $valor; }
    public function setTPessoalDependenteCarteiraVacinacao($valor) { $this->obTPessoalDependenteCarteiraVacinacao     = $valor; }
    public function setTransacao($valor) { $this->obTransacao                     = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function getCodCarteiraVacinacao() { return $this->inCodCarteiraVacinacao;                            }
    public function getCodDependente() { return $this->inCodDependente;                                   }

    /**
    * @access Public
    * @param Object $valor
    */
    public function getDataApresentacao() { return $this->dtDataApresentacao;                       }

    /**
    * @access Public
    * @param Object $valor
    */
    public function getApresentada() { return $this->boApresentada;                            }

    /**
    * @access Public
    * @param Object $valor
    */
    public function getCarteira() { return $this->arCarteira;                       }

    public function getTransacao() { return $this->obTransacao;                      }
    public function getUltimoCarteiraVacinacao() { return $this->obUltimoCarteiraVacinacao;        }

    public function RPessoalCarteiraVacinacao(&$roPessoalDependente)
    {
        $this->setTransacao                           ( new Transacao                            );
        $this->setTPessoalDependenteCarteiraVacinacao ( new TPessoalDependenteCarteiraVacinacao            );
        $this->setTPessoalCarteiraVacinacao           ( new TPessoalCarteiraVacinacao            );
        $this->roPessoalDependente                    = &$roPessoalDependente;
        $this->setCarteira                            = array();

    }

function incluirCarteira($boTransacao = "")
{
    $boFlagTransacao = false;
    $rsCarteira = new RecordSet;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalCarteiraVacinacao->setDado("dt_apresentacao"      , $this->getDataApresentacao() );
        $this->obTPessoalCarteiraVacinacao->setDado("apresentada"          , $this->getApresentada()      );
        $obErro = $this->obTPessoalCarteiraVacinacao->proximoCod( $inCodCarteiraVacinacao , $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setCodCarteiraVacinacao( $inCodCarteiraVacinacao );
            $this->obTPessoalCarteiraVacinacao->setDado("cod_carteira" , $this->getCodCarteiraVacinacao() );
            $obErro = $this->obTPessoalCarteiraVacinacao->inclusao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalDependenteCarteiraVacinacao->setDado("cod_dependente", $this->roPessoalDependente->getCodDependente() );
            $this->obTPessoalDependenteCarteiraVacinacao->setDado("cod_carteira"  , $this->getCodCarteiraVacinacao() );
            $obEro = $this->obTPessoalDependenteCarteiraVacinacao->inclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/** Exclui registro do banco
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirCarteira($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $stFiltro = " WHERE cod_dependente = " . $this->roPessoalDependente->getCodDependente();
    $this->obTPessoalDependenteCarteiraVacinacao->recuperaTodos( $rsCarteiraVacinacao, $stFiltro, "", $boTransacao );
    if ( $rsCarteiraVacinacao->getNumLinhas() >= '1' ) {
        if ( !$obErro->ocorreu() ) {
           $this->obTPessoalDependenteCarteiraVacinacao->setDado("cod_dependente", $this->roPessoalDependente->getCodDependente() );
           $obErro = $this->obTPessoalDependenteCarteiraVacinacao->exclusao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            while ( !$rsCarteiraVacinacao->eof() ) {
                $this->obTPessoalCarteiraVacinacao->setDado("cod_carteira", $rsCarteiraVacinacao->getCampo("cod_carteira") );
                $obErro = $this->obTPessoalCarteiraVacinacao->exclusao( $boTransacao );
                if ( $obErro->ocorreu() ) {
                    break;
                }
                $rsCarteiraVacinacao->proximo();
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

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
function listarCarteira(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
        $stFiltro = "";
        if ( $this->roPessoalDependente->getCodDependente() ) {
            $stFiltro .= " AND PD.cod_dependente = ".$this->roPessoalDependente->getCodDependente();
        }
        $obErro = $this->obTPessoalCarteiraVacinacao->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder ,$boTransacao );

       return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarCarteira(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = " WHERE cod_dependente = " . $this->roPessoalDependente->getCodDependente();
    $this->obTPessoalDependenteCarteiraVacinacao->recuperaTodos( $rsCarteiraVacinacao, $stFiltro, "", $boTransacao );
    if( $this->inCodPrevidencia )
        $stFiltro = " WHERE cod_carteira = '" . $this->inCod . "'";
    $obErro = $this->obTPessoalCarteiraVacinacao->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
