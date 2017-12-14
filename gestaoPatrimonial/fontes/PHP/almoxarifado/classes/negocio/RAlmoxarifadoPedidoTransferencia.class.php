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
    * Classe de Regra de Nota de Transferência
    * Data de Criação   : 24/04/2006

    * @author Analista      : Diego Barbosa Victoria
    * @author Desenvolvedor : Rodrigo

    * @package URBEM
    * @subpackage Regra

    * Casos de uso: uc-03.03.08
*/

/*
$Log$
Revision 1.5  2007/08/06 19:01:45  leandro.zis
Corrigido nota de transferencia

Revision 1.4  2006/07/06 14:04:47  diego
Retirada tag de log com erro.

Revision 1.3  2006/07/06 12:09:32  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoPedidoTransferencia.class.php"                  );
include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoPedidoTransferenciaItem.class.php"              );
include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoPedidoTransferenciaItem.class.php"      );
include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoPedidoTransferenciaItemValor.class.php" );
include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoPedidoTransferenciaAnulacao.class.php"          );
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoPedidoTransferenciaItem.class.php"                 );
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoPedidoTransferenciaItemDestino.class.php"          );
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarifado.class.php"                            );
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarife.class.php"                              );

class RAlmoxarifadoPedidoTransferencia
{
 /**
    * @access Private
    * @var Object
 */
 var $obTransacao;
 /**
    * @access Private
    * @var Object
 */
 var $inExercicio;
 /**
    * @access Private
    * @var Object
 */
 var $inCodigo;
 /**
    * @access Private
    * @var Object
 */
 var $stObservacao;
 /**
   * @access Private
   * @var Object
 */
 var $obRAlmoxarifadoOrigem;
 /**
   * @access Private
   * @var Object
 */
 var $obRAlmoxarifadoDestino;
 /**
   * @access Private
   * @var Object
 */
 var $obRAlmoxarife;
 /**
   * @access Private
   * @var Object
 */
 var $obRAlmoxarifadoPedidoTransferenciaItem;

/**
   * @access Private
   * @var Object
 */
 var $obRAlmoxarifadoPedidoTransferenciaItemDestino;

 /**
     * @access Public
     * @param Object $valor
 */
 function setTransacao($valor) { $this->obTransacao = $valor;}
 /**
     * @access Public
     * @return Object
 */
 function getTransacao() { return $this->obTransacao;   }
 /**
     * @access Public
     * @param Object $valor
 */
 function setExercicio($valor) { $this->inExercicio = $valor; }
  /**
      * @access Public
      * @return Object
  */
 function getExercicio() { return $this->inExercicio;   }
 /**
     * @access Public
     * @param Object $valor
 */
 function setCodigo($valor) { $this->inCodigo = $valor;    }
  /**
      * @access Public
      * @return Object
  */
 function getCodigo() { return $this->inCodigo;      }
 /**
     * @access Public
     * @param Object $valor
 */
 function setObservacao($valor) { $this->stObservacao = $valor;}
  /**
      * @access Public
      * @return Object
  */
 function getObservacao() { return $this->stObservacao;  }

 function RAlmoxarifadoPedidoTransferencia()
 {
    $this->setTransacao(new Transacao);
    $this->obRAlmoxarifadoOrigem = new RAlmoxarifadoAlmoxarifado;
    $this->obRAlmoxarifadoDestino = new RAlmoxarifadoAlmoxarifado;
    $this->obRAlmoxarife   = new RAlmoxarifadoAlmoxarife;
 }

 function addPedidoTransferenciaItem()
 {
    $this->arRAlmoxarifadoPedidoTransferenciaItem[] = new RAlmoxarifadoPedidoTransferenciaItem($this);
    $this->arRAlmoxarifadoPedidoTransferenciaItemDestino[] = new RAlmoxarifadoPedidoTransferenciaItemDestino($this);
    $this->roUltimoPedidoTransferenciaItemDestino          = &$this->arRAlmoxarifadoPedidoTransferenciaItemDestino[count($this->arRAlmoxarifadoPedidoTransferenciaItemDestino)-1];
    $this->roUltimoPedidoTransferenciaItem                 = &$this->arRAlmoxarifadoPedidoTransferenciaItem[count($this->arRAlmoxarifadoPedidoTransferenciaItem)-1];
 }

 function anular($obTransacao = "")
 {
   $boFlagTransacao = false;
   $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $obTransacao);
   if (!$obErro->ocorreu()) {
      $obTAlmoxarifadoPedidoTransferenciaAnulacao = new TAlmoxarifadoPedidoTransferenciaAnulacao();
      $obTAlmoxarifadoPedidoTransferenciaAnulacao->setDado('cod_transferencia', $this->getCodigo());
      $obTAlmoxarifadoPedidoTransferenciaAnulacao->setDado('exercicio',$this->getExercicio());
      $obTAlmoxarifadoPedidoTransferenciaAnulacao->inclusao($obTransacao);
   }
   $this->obTransacao->fechaTransacao($boFlagTransacao, $obTransacao, $obErro, $obTAlmoxarifadoPedidoTransferencia);

   return $obErro;
 }

 function incluir($obTransacao = "")
 {
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $obTransacao);

        if (!($obErro->ocorreu())) {
            $obTAlmoxarifadoPedidoTransferencia = new TAlmoxarifadoPedidoTransferencia();
            $obErro = $obTAlmoxarifadoPedidoTransferencia->proximoCod($inCodigo , $boTransacao);
            $this->setCodigo($inCodigo);

            if (!($obErro->ocorreu())) {
                    $obTAlmoxarifadoPedidoTransferencia->setDado('cod_almoxarifado_origem' ,$this->obRAlmoxarifadoOrigem->getCodigo()                        );
                    $obTAlmoxarifadoPedidoTransferencia->setDado('cod_almoxarifado_destino' ,$this->obRAlmoxarifadoDestino->getCodigo()                        );
                    $obTAlmoxarifadoPedidoTransferencia->setDado('exercicio'        ,$this->getExercicio()                                      );
                    $obTAlmoxarifadoPedidoTransferencia->setDado('cod_transferencia',$this->getCodigo()                                         );
                    $obTAlmoxarifadoPedidoTransferencia->setDado('cgm_almoxarife'   ,$this->obRAlmoxarife->obRCGMAlmoxarife->obRCGM->getNumCGM());
                    $obTAlmoxarifadoPedidoTransferencia->setDado('observacao'       ,$this->getObservacao()                                     );
                    $obErro = $obTAlmoxarifadoPedidoTransferencia->inclusao( $obTransacao );

                    for ($i=0;$i<count($this->arRAlmoxarifadoPedidoTransferenciaItem);$i++) {
                       $obRAlmoxarifadoPedidoTransferenciaItem = $this->arRAlmoxarifadoPedidoTransferenciaItem[$i];
                       $obRAlmoxarifadoPedidoTransferenciaItemDestino = $this->arRAlmoxarifadoPedidoTransferenciaItemDestino[$i];
                       $obRAlmoxarifadoPedidoTransferenciaItem->incluir($boTransacao);
                       $obRAlmoxarifadoPedidoTransferenciaItemDestino->incluir($boTransacao);
                    }
            }

            $this->obTransacao->fechaTransacao($boFlagTransacao, $obTransacao, $obErro, $obTAlmoxarifadoPedidoTransferencia);

            return $obErro;
       }
 }

 function excluirItens($obTransacao="")
 {
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $obTransacao);

        if (!$obErro->ocorreu()) {
            $obTAlmoxarifadoPedidoTransferenciaItens = new TAlmoxarifadoPedidoTransferenciaItem();
            $obTAlmoxarifadoAtributoPedidoTransferenciaItem = new TAlmoxarifadoAtributoPedidoTransferenciaItem;
            $obTAlmoxarifadoPedidoTransferenciaItemDestino = new TAlmoxarifadoPedidoTransferenciaItemDestino;
            $obTAlmoxarifadoAtributoPedidoTransferenciaItemValor = new TAlmoxarifadoAtributoPedidoTransferenciaItemValor;
            $obTAlmoxarifadoAtributoPedidoTransferenciaItem->obTAlmoxarifadoPedidoTransferenciaItem = &$obTAlmoxarifadoPedidoTransferenciaItens;
            $obTAlmoxarifadoAtributoPedidoTransferenciaItemValor->obTAlmoxarifadoAtributoPedidoTransferenciaItem = &$obTAlmoxarifadoAtributoPedidoTransferenciaItem;

            $obTAlmoxarifadoPedidoTransferenciaItens->setDado('cod_transferencia' ,$this->getCodigo ());
            $obTAlmoxarifadoPedidoTransferenciaItens->setDado('exercicio'         ,$this->getExercicio());

            $obTAlmoxarifadoPedidoTransferenciaItemDestino->setDado('cod_transferencia' ,$this->getCodigo());
            $obTAlmoxarifadoPedidoTransferenciaItemDestino->setDado('exercicio'         ,$this->getExercicio());

            $obErro = $obTAlmoxarifadoAtributoPedidoTransferenciaItemValor->exclusao( $obTransacao );
            if(!$obErro->ocorreu())
               $obErro = $obTAlmoxarifadoAtributoPedidoTransferenciaItem->exclusao( $obTransacao );
            if(!$obErro->ocorreu())
               $obErro = $obTAlmoxarifadoPedidoTransferenciaItemDestino->exclusao($obTransacao);
            if(!$obErro->ocorreu())
               $obErro = $obTAlmoxarifadoPedidoTransferenciaItens->exclusao( $obTransacao );

            $this->obTransacao->fechaTransacao($boFlagTransacao, $obTransacao, $obErro, $obTAlmoxarifadoPedidoTransferenciaItens);
       }

   return $obErro;

 }

 function alterar($obTransacao = "")
 {
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $obTransacao);

        if (!($obErro->ocorreu())) {
            $obTAlmoxarifadoPedidoTransferencia = new TAlmoxarifadoPedidoTransferencia();

            $obTAlmoxarifadoPedidoTransferencia->setDado('cod_almoxarifado_origem' ,$this->obRAlmoxarifadoOrigem->getCodigo()                        );
            $obTAlmoxarifadoPedidoTransferencia->setDado('cod_almoxarifado_destino' ,$this->obRAlmoxarifadoDestino->getCodigo()                        );
            $obTAlmoxarifadoPedidoTransferencia->setDado('exercicio'        ,$this->getExercicio()                                      );
            $obTAlmoxarifadoPedidoTransferencia->setDado('cod_transferencia',$this->getCodigo()                                         );
            $obTAlmoxarifadoPedidoTransferencia->setDado('cgm_almoxarife'   ,$this->obRAlmoxarife->obRCGMAlmoxarife->obRCGM->getNumCGM());
            $obTAlmoxarifadoPedidoTransferencia->setDado('observacao'       ,$this->getObservacao()                                     );
            $obErro = $obTAlmoxarifadoPedidoTransferencia->alteracao( $obTransacao );

            if(!$obErro->ocorreu())
                $obErro = $this->excluirItens($obTransacao);
            if (!$obErro->ocorreu()) {

               for ($i=0;$i<count($this->arRAlmoxarifadoPedidoTransferenciaItem);$i++) {

                  $obRAlmoxarifadoPedidoTransferenciaItem = $this->arRAlmoxarifadoPedidoTransferenciaItem[$i];
                  $obRAlmoxarifadoPedidoTransferenciaItemDestino = $this->arRAlmoxarifadoPedidoTransferenciaItemDestino[$i];
                  $obRAlmoxarifadoPedidoTransferenciaItem->incluir($boTransacao);
                  $obRAlmoxarifadoPedidoTransferenciaItemDestino->incluir($boTransacao);
               }
            }

            $this->obTransacao->fechaTransacao($boFlagTransacao, $obTransacao, $obErro, $obTAlmoxarifadoPedidoTransferencia);
       }

   return $obErro;
 }

}
