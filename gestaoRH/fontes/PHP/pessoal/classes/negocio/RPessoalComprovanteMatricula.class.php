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
    * Classe de Regra de Negócio Comprovante Vacinacao
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
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalComprovanteMatricula.class.php"                             );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalDependenteComprovanteMatricula.class.php"                          );
//include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalGrauParentesco.class.php"                                );

/**
    * Classe de Regra de Negócio Pesssoal Servidor Comprovante Vacinacao
    * Data de Criação   : 20/12/2004

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra
*/

class RPessoalComprovanteMatricula
{
    /**
    * @var Integer
    * @access Private
    */
    public $arComprovante;

    /**
    * @var Reference Object
    * @access Private
    */
    public $roPessoalDependente;

    /**
    * @var Integer
    * @access Private
    */
    public $inCodComprovanteMatricula;

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
    public $obTPessoalComprovanteMatricula;

    /**
    * @access Public
    * @param Object $valor
    */
    public function setComprovante($valor) { $this->arComprovante                      = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setCodDependente($valor) { $this->inCodDependente                    = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setCodComprovanteMatricula($valor) { $this->inCodComprovanteMatricula          = $valor; }

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
    public function setTPessoalComprovanteMatricula($valor) { $this->obTPessoalComprovanteMatricula     = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function getCodDependente() { return $this->inCodDependente;                              }

    /**
    * @access Public
    * @param Object $valor
    */
    public function getCodComprovanteMatricula() { return $this->inCodComprovanteMatricula;                   }

    /**
    * @access Public
    * @param Object $valor
    */
    public function getDataApresentacao() { return $this->dtDataApresentacao;                             }

    /**
    * @access Public
    * @param Object $valor
    */
    public function getApresentada() { return $this->boApresentada;                                  }

    /**
    * @access Public
    * @param Object $valor
    */
    public function getComprovante() { return $this->arComprovante;                       }

    public function RPessoalComprovanteMatricula(&$roPessoalDependente)
    {
        $this->obTransacao                               = new Transacao;
        $this->obTPessoalDependenteComprovanteMatricula  = new TPessoalDependenteComprovanteMatricula;
        $this->setTPessoalComprovanteMatricula           ( new TPessoalComprovanteMatricula);
        $this->setComprovante                            = array();
        $this->roPessoalDependente                       = &$roPessoalDependente;

    }

function incluirComprovante($boTransacao = "")
{
    $boFlagTransacao = false;
    $rsComprovante = new RecordSet;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalComprovanteMatricula->setDado("dt_apresentacao"      , $this->getDataApresentacao());
        $this->obTPessoalComprovanteMatricula->setDado("apresentada"          , $this->getApresentada());
        $obErro = $this->obTPessoalComprovanteMatricula->proximoCod( $inCodComprovanteMatricula , $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setCodComprovanteMatricula( $inCodComprovanteMatricula );
            $this->obTPessoalComprovanteMatricula->setDado("cod_comprovante" , $this->getCodComprovanteMatricula() );
            $obErro = $this->obTPessoalComprovanteMatricula->inclusao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalDependenteComprovanteMatricula->setDado("cod_dependente", $this->roPessoalDependente->getCodDependente() );
            $this->obTPessoalDependenteComprovanteMatricula->setDado("cod_comprovante"  , $this->getCodComprovanteMatricula() );
            $obEro = $this->obTPessoalDependenteComprovanteMatricula->inclusao( $boTransacao );
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
function excluirComprovante($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $stFiltro = " WHERE cod_dependente = " . $this->roPessoalDependente->getCodDependente();
    $this->obTPessoalDependenteComprovanteMatricula->recuperaTodos( $rsComprovanteMatricula, $stFiltro, "", $boTransacao );
    if ( $rsComprovanteMatricula->getNumLinhas() >= '1' ) {
        if ( !$obErro->ocorreu() ) {
           $this->obTPessoalDependenteComprovanteMatricula->setDado("cod_dependente", $this->roPessoalDependente->getCodDependente() );
           $obErro = $this->obTPessoalDependenteComprovanteMatricula->exclusao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            while ( !$rsComprovanteMatricula->eof() ) {
                $this->obTPessoalComprovanteMatricula->setDado("cod_comprovante", $rsComprovanteMatricula->getCampo("cod_comprovante") );
                $obErro = $this->obTPessoalComprovanteMatricula->exclusao( $boTransacao );
                if ( $obErro->ocorreu() ) {
                    break;
                }
                $rsComprovanteMatricula->proximo();
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

    /**
    * Executa um recuperaRelacionnamento na classe Persistente Dependente/comprovantematricula
    * @access Public
    * @param  Object $rsComprovante Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function listarComprovante(&$rsComprovante, $stFiltro = "", $boTransacao = "")
    {
        $stFiltro = "";
        $stOrder = "";
        if ( $this->roPessoalDependente->getCodDependente() ) {
            $stFiltro .= " AND PD.cod_dependente = ".$this->roPessoalDependente->getCodDependente();
        }
        $obErro = $this->obTPessoalComprovanteMatricula->recuperaRelacionamento( $rsComprovante , $stFiltro, $stOrder ,$boTransacao );

        return $obErro;
    }
}
