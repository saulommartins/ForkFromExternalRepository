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
   * Página de processamento para Nota de Transferência
   * Data de criação : 24/04/2006

   * @author Analista    : Diego Victoria
   * @author Programador : Rodrigo

   * @ignore

   Caso de uso: uc-03.03.08

   $Id:$

   **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoPedidoTransferencia.class.php"                        );

//Define o nome dos arquivos PHP
$stPrograma = "ManterNotaTransferencia";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
$inCodAlmoxarifadoOrigem = $_REQUEST['inCodAlmoxarifadoOrigem'];
$inCodAlmoxarifadoDestino = $_REQUEST['inCodAlmoxarifadoDestino'];
$stObservacao = $_REQUEST['stObservacao'];
$stExercicio = $_REQUEST['stExercicio'];

$obRPedidoTransferencia = new RAlmoxarifadoPedidoTransferencia();

$obErro = new Erro;

$stCtrl = $_REQUEST['stCtrl'];

 switch ($stAcao) {

   case "incluir":

      if (count(Sessao::read('arItens'))>0) {

         $obRPedidoTransferencia->obRAlmoxarifadoOrigem->setCodigo($inCodAlmoxarifadoOrigem);
         $obRPedidoTransferencia->obRAlmoxarifadoDestino->setCodigo($inCodAlmoxarifadoDestino);
         $obRPedidoTransferencia->obRAlmoxarife->obRCGMAlmoxarife->obRCGM->setNumCGM(Sessao::read(numCgm));
         $obRPedidoTransferencia->setObservacao($stObservacao);
         $obRPedidoTransferencia->setExercicio($stExercicio);

         $arItens = Sessao::read('arItens');

         foreach ($arItens as $key => $value) {

            if ($_REQUEST['inCodAlmoxarifadoOrigem'] == $_REQUEST['inCodAlmoxarifadoDestino']) {
               if ($value['cod_centro'] == $value['cod_centro_destino']) {
                  SistemaLegado::exibeAviso("O item (".$value['descricao_item'].") esta com dados incorretos - CENTRO DE CUSTO IGUAL (ORIGEM / DESTINO).","n_incluir","erro");
                  exit;
               }
            }

            $obRPedidoTransferencia->addPedidoTransferenciaItem();
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->setCodigo($value['cod_item']    );
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRMarca->setCodigo($value['cod_marca']          );
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->setCodigo($value['cod_centro']);
            //$obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->setCodCentroDestino($value['cod_centro_destino']                           );
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->setQuantidade($value['quantidade']                                           );
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->setValoresAtributos( $value['valores_atributos'] );

            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItemDestino->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->setCodigo($value['cod_item']    );
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItemDestino->obRAlmoxarifadoEstoqueItem->obRMarca->setCodigo($value['cod_marca']          );
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItemDestino->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->setCodigo($value['cod_centro']);
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItemDestino->setCodCentroDestino($value['cod_centro_destino']                             );
         }

         $obErro = $obRPedidoTransferencia->incluir();

         if (!$obErro->ocorreu()) {
            Sessao::remove('arItens');
            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao, $obRPedidoTransferencia->getCodigo(),"incluir","aviso", Sessao::getId(), "../");
         } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
         }
      } else {
         SistemaLegado::exibeAviso("Deve existir pelo menos um ítem.","n_incluir","erro");
      }
   break;

  case "alterar":

      if (count(Sessao::read('arItens'))>0) {
         $obRPedidoTransferencia->setCodigo($_REQUEST['inCodTransferencia']);
         $obRPedidoTransferencia->obRAlmoxarifadoOrigem->setCodigo($inCodAlmoxarifadoOrigem);
         $obRPedidoTransferencia->obRAlmoxarifadoDestino->setCodigo($inCodAlmoxarifadoDestino);
         $obRPedidoTransferencia->obRAlmoxarife->obRCGMAlmoxarife->obRCGM->setNumCGM(Sessao::read('numCgm'));
         $obRPedidoTransferencia->setObservacao($stObservacao);
         $obRPedidoTransferencia->setExercicio($_REQUEST['stExercicio']);

         foreach (Sessao::read('arItens') as $key => $value) {

            if ($_REQUEST['inCodAlmoxarifadoOrigem'] == $_REQUEST['inCodAlmoxarifadoDestino']) {
               if ($value['cod_centro'] == $value['cod_centro_destino']) {
                  SistemaLegado::exibeAviso("O item (".$value['descricao_item'].") esta com dados incorretos - CENTRO DE CUSTO IGUAL (ORIGEM / DESTINO).","n_incluir","erro");
                  exit;
               }
            }

            $obRPedidoTransferencia->addPedidoTransferenciaItem();
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->setCodigo($value['cod_item']    );
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRMarca->setCodigo($value['cod_marca']          );
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->setCodigo($value['cod_centro']);
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->setQuantidade($value['quantidade']                                           );
            //$obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->setCodCentroDestino($value['cod_centro_destino']                           );

            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItemDestino->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->setCodigo($value['cod_item']    );
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItemDestino->obRAlmoxarifadoEstoqueItem->obRMarca->setCodigo($value['cod_marca']          );
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItemDestino->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->setCodigo($value['cod_centro']);
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItemDestino->setCodCentroDestino($value['cod_centro_destino']                             );

         }
         $obErro = $obRPedidoTransferencia->alterar();
         if (!$obErro->ocorreu()) {
             SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao, $obRPedidoTransferencia->getCodigo(),"alterar","aviso", Sessao::getId(), "../");
         } else {
             SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
         }

      } else {
         SistemaLegado::exibeAviso("Deve existir pelo menos um ítem.","n_alterar","erro");
      }
   break;

   case "anular":

               $obRPedidoTransferencia->setCodigo($_REQUEST['inCodTransferencia']);
               $obRPedidoTransferencia->setExercicio($_REQUEST['stExercicio']);
               $obErro = $obRPedidoTransferencia->anular();
               if (!$obErro->ocorreu()) {
                   SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao, $obRPedidoTransferencia->getCodigo(),"anular","aviso", Sessao::getId(), "../");
               } else {
                   SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_anular","erro");
               }
   break;
}
