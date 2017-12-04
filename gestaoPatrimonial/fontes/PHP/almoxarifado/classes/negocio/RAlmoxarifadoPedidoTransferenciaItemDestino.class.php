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
    * Classe de Regra de Transferencia de Itens do Pedido para o destino
    * Data de Criação   : 18/12/2008

    * @author Analista      : Gelson W
    * @author Desenvolvedor : Luiz Felipe Prestes Teixeira

    * @package URBEM
    * @subpackage Regra

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoPedidoTransferenciaItemDestino.class.php"       );
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarifado.class.php"                            );
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoEstoqueItem.class.php"                             );

class RAlmoxarifadoPedidoTransferenciaItemDestino
{
    /**
        * @access Private
        * @var Object
    */
    public $obTransacao;

    /**
        * @access Private
        * @var Object
    */
    public $obRAlmoxarifadoEstoqueItem;

    /**
        * @access Private
        * @var Numeric
    */

    public $nmQuantidade;

    /**
      * @access Private
      * @var Array
    */

    public $arValoresAtributos;

    /**
      * @access Private
      * @var integer
    */

    public $inCodCentroCustoDestino;

    /**
        * @access Public
        * @param Array $valor
    */

    public function setValoresAtributos($valor) { $this->arValoresAtributos = $valor; }

    /**
        * @access Public
        * @return Arrray
    */

    public function getValoresAtributos() { return $this->arValoresAtributos;   }

/**
        * @access Public
        * @param integer $valor
    */

    public function setCodCentroDestino($valor) { $this->inCodCentroCustoDestino = $valor; }

    /**
        * @access Public
        * @return integer
    */

    public function getCodCentroDestino() { return $this->inCodCentroCustoDestino;   }

    /**
         * @access Public
         * @return Numeric
     */

   public function setQuantidade($valor) { $this->nmQuantidade = $valor; }

    /**
         * @access Public
         * @return Numeric
     */

    public function getQuantidade() { return $this->nmQuantidade; }

    /**
         * Método construtor
         * @access Public
    */

    public function RAlmoxarifadoPedidoTransferenciaItemDestino(&$obRAlmoxarifadoPedidoTransferencia)
    {
        $this->obRAlmoxarifadoEstoqueItem        = new RAlmoxarifadoEstoqueItem();
        $this->roAlmoxarifadoPedidoTransferencia = &$obRAlmoxarifadoPedidoTransferencia;
        $this->obTransacao                       = new Transacao ;
    }

    /**
        * @access Public
        * @param Boolean $boTransacao
        * @return Erro
    */
    public function incluir($boTransacao="")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if (!$obErro->ocorreu()) {

            $obTAlmoxarifadoTransferenciaItemDestino = new TAlmoxarifadoPedidoTransferenciaItemDestino();
            $obTAlmoxarifadoTransferenciaItemDestino->setDado("cod_transferencia"   , $this->roAlmoxarifadoPedidoTransferencia->getCodigo()                 );
            $obTAlmoxarifadoTransferenciaItemDestino->setDado("exercicio"           , $this->roAlmoxarifadoPedidoTransferencia->getExercicio()              );
            $obTAlmoxarifadoTransferenciaItemDestino->setDado("cod_item"            , $this->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo()       );
            $obTAlmoxarifadoTransferenciaItemDestino->setDado("cod_marca"           , $this->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo()              );
            $obTAlmoxarifadoTransferenciaItemDestino->setDado("cod_centro"          , $this->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo()     );
            $obTAlmoxarifadoTransferenciaItemDestino->setDado("cod_centro_destino"  , $this->getCodCentroDestino()                                        );
            $obErro = $obTAlmoxarifadoTransferenciaItemDestino->inclusao($boTransacao);

        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoPedidoTransferencia);

        return $obErro;
    }

}
