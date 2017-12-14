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
    * Classe de Regra de Transferencia de Itens do Pedido
    * Data de Criação   : 25/04/2006

    * @author Analista      : Diego Victoria Barbosa
    * @author Desenvolvedor : Rodrigo

    * @package URBEM
    * @subpackage Regra

    * Casos de uso: uc-03.03.08
*/

/*
$Log$
Revision 1.6  2007/08/06 19:01:45  leandro.zis
Corrigido nota de transferencia

Revision 1.5  2006/07/06 14:04:47  diego
Retirada tag de log com erro.

Revision 1.4  2006/07/06 12:09:32  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoPedidoTransferenciaItem.class.php"             );
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarifado.class.php"                            );
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoEstoqueItem.class.php"                             );

class RAlmoxarifadoPedidoTransferenciaItem
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

    //function setCodCentroDestino($valor) { $this->inCodCentroCustoDestino = $valor; }

    /**
        * @access Public
        * @return integer
    */

    //function getCodCentroDestino() { return $this->inCodCentroCustoDestino;   }

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

    public function RAlmoxarifadoPedidoTransferenciaItem(&$obRAlmoxarifadoPedidoTransferencia)
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

            $obTAlmoxarifadoTransferenciaItem = new TAlmoxarifadoPedidoTransferenciaItem();
            $obTAlmoxarifadoTransferenciaItem->setDado("cod_transferencia"   , $this->roAlmoxarifadoPedidoTransferencia->getCodigo()                 );
            $obTAlmoxarifadoTransferenciaItem->setDado("exercicio"           , $this->roAlmoxarifadoPedidoTransferencia->getExercicio()              );
            $obTAlmoxarifadoTransferenciaItem->setDado("cod_item"            , $this->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getCodigo()       );
            $obTAlmoxarifadoTransferenciaItem->setDado("cod_marca"           , $this->obRAlmoxarifadoEstoqueItem->obRMarca->getCodigo()              );
            $obTAlmoxarifadoTransferenciaItem->setDado("cod_centro"          , $this->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getCodigo()     );
            //$obTAlmoxarifadoTransferenciaItem->setDado("cod_centro_destino"  , $this->getCodCentroDestino()                                        );
            $obTAlmoxarifadoTransferenciaItem->setDado("quantidade"          , $this->getQuantidade()                                                );
            $obErro = $obTAlmoxarifadoTransferenciaItem->inclusao($boTransacao);

            if ($this->arValoresAtributos) {
                 foreach ($this->arValoresAtributos as $arAtributos) {
                     $obTAlmoxarifadoAtributoPedidoTransferenciaItem = new TAlmoxarifadoAtributoPedidoTransferenciaItem;
                     $obTAlmoxarifadoAtributoPedidoTransferenciaItem->obTAlmoxarifadoPedidoTransferenciaItem = &$obTAlmoxarifadoTransferenciaItem;
                     $obTAlmoxarifadoAtributoPedidoTransferenciaItem->setDado( "quantidade"      , $arAtributos['quantidade'] );
                     $obTAlmoxarifadoAtributoPedidoTransferenciaItem->setCampoCod('cod_sequencial');
                     $obErro = $obTAlmoxarifadoAtributoPedidoTransferenciaItem->inclusao( $boTransacao );

                     if (!$obErro->ocorreu()) {
                        foreach ($arAtributos['atributo'] as $arValorAtributo) {
                            $obTAlmoxarifadoAtributoPedidoTransferenciaItemValor = new TAlmoxarifadoAtributoPedidoTransferenciaItemValor;
                            $obTAlmoxarifadoAtributoPedidoTransferenciaItemValor->obTAlmoxarifadoAtributoPedidoTransferenciaItem = &$obTAlmoxarifadoAtributoPedidoTransferenciaItem;
                            $obTAlmoxarifadoAtributoPedidoTransferenciaItemValor->setDado( "cod_modulo"  , "29" );
                            $obTAlmoxarifadoAtributoPedidoTransferenciaItemValor->setDado( "cod_cadastro", "2" );
                            $obTAlmoxarifadoAtributoPedidoTransferenciaItemValor->setDado( "cod_atributo", $arValorAtributo["cod_atributo"]);
                            $obTAlmoxarifadoAtributoPedidoTransferenciaItemValor->setDado( "valor"       , $arValorAtributo["valor"] );
                            $obErro = $obTAlmoxarifadoAtributoPedidoTransferenciaItemValor->inclusao( $boTransacao );
                        }
                    }

                 }
             }

        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoPedidoTransferencia);

        return $obErro;
    }

}
