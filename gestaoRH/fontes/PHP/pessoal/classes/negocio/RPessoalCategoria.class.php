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
    * Classe de Regra de Negócio Categoria
    * Data de Criação   : 25/12/2004

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra

    Caso de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCategoria.class.php"                           );

/**
    * Classe de Regra de Negócio Pesssoal Tipo Admissao
    * Data de Criação   : 25/12/2004

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra
*/

class RPessoalCategoria
{
    /**
    * @var Integer
    * @access Private
    */
    public $inCodCategoria;

    /**
    * @var String
    * @access Private
    */
    public $stDescricao;

    /**
    * @var Object
    * @access Private
    */
    public $obTPessoalCategoria;
    /**
    * @var Object
    * @access Private
    */
    public $roRFolhaPagamentoFGTS;
    /**
    * @var Object
    * @access Private
    */
    public $roRFolhaPagamentoFGTSCategoria;

    /**
    * @var Object
    * @access Private
    */
    public $roRPessoalMovimentoSefipSaida;

    /**
    * @var Object
    * @access Private
    */
    public $roRPessoalCategoriaMovimento;

    /**
    * @access Public
    * @param Object $valor
    */
    public function setroRPessoalMovimentoSefipSaida(&$Obejto) { $this->roRPessoalMovimentoSefipSaida = $Obejto;}

    /**
    * @access Public
    * @param Object $valor
    */

    public function getroRPessoalMovimentoSefipSaida() { return $this->roRPessoalMovimentoSefipSaida; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setroRPessoalCategoriaMovimento(&$Obejto) {$this->roRPessoalCategoriaMovimento = $Obejto;}

    /**
    * @access Public
    * @param Object $valor
    */
    public function getroRPessoalCategoriaMovimento(&$Obejto) {return $this->roRPessoalCategoriaMovimento;}

    /**
    * @access Public
    * @param Object $valor
    */
    public function setCodCategoria($valor) { $this->inCodCategoria       = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setDescricao($valor) { $this->stDescricao          = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setTPessoalCategoria($valor) { $this->obTPessoalCategoria       = $valor; }
    /**
    * @access Public
    * @param Object $valor
    */
    public function setRORFolhaPagamentoFGTS(&$valor) { $this->roRFolhaPagamentoFGTS    = &$valor; }
    /**
    * @access Public
    * @param Object $valor
    */
    public function setRORFolhaPagamentoFGTSCategoria(&$valor) { $this->roRFolhaPagamentoFGTSCategoria  = &$valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function getCodCategoria() { return $this->inCodCategoria;            }
    public function getDescricao() { return $this->stDescricao;               }
    public function getTPessoalCategoria() { return $this->obTPessoalCategoria;       }
    /**
        * @access Public
        * @return Object
    */
    public function getRORFolhaPagamentoFGTS() { return $this->roRFolhaPagamentoFGTS;  }
    /**
        * @access Public
        * @return Object
    */
    public function getRORFolhaPagamentoFGTSCategoria() { return $this->roRFolhaPagamentoFGTSCategoria;  }

    public function RPessoalCategoria($roRPessoalCategoriaMovimento = '' , $roRPessoalMovimentoSefipSaida = '')
    {
        $this->setTPessoalCategoria             ( new TPessoalCategoria          );
        $this->setroRPessoalCategoriaMovimento  ( $roPessoalCategoriaMovimento   );
        $this->setroRPessoalMovimentoSefipSaida ( $roRPessoalMovimentoSefipSaida );
    }

    /**
    * Executa um recuperaTodos na classe Persistente PessoalCategoria
    * @access Public
    * @param  Object $rsResultado Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function listarCategoria(&$rsResultado , $stFiltro = "", $boTransacao = "")
    {
        if ( $this->getCodCategoria() != "" ) {
            $stFiltro .= " AND cod_categoria = ".$this->getCodCategoria();
        }
        if ($stFiltro != "") {
            $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
        }
        $obErro = $this->obTPessoalCategoria->recuperaTodos( $rsResultado, $stFiltro, "cod_categoria", $boTransacao );

        return $obErro;
    }

}
