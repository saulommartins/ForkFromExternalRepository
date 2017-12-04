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
    * Data de Criação: 21/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * Casos de uso: uc-03.02.12

    $Id: PRManterItem.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_FRO_MAPEAMENTO.'TFrotaItem.class.php';
include_once CAM_GP_FRO_MAPEAMENTO.'TFrotaCombustivelItem.class.php';

include_once CAM_GP_ALM_MAPEAMENTO.'TAlmoxarifadoCatalogoItem.class.php';

$stPrograma = "ManterItem";
$pgFilt		= "FL".$stPrograma.".php";
$pgList		= "LS".$stPrograma.".php";
$pgForm		= "FM".$stPrograma.".php";
$pgProc		= "PR".$stPrograma.".php";
$pgOcul		= "OC".$stPrograma.".php";
$pgJs		= "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obTFrotaItem 				 = new TFrotaItem;
$obTFrotaCombustivelItem 	 = new TFrotaCombustivelItem;
$obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem;

Sessao::setTrataExcecao(true);
Sessao::getTransacao()->setMapeamento( $obTFrotaItem );

switch ($stAcao) {
    case 'incluir' :

        $stChaveClassificacao = $_REQUEST['stChaveClassificacao'];
        $inCodCatalogo		  = $_REQUEST['inCodCatalogo'];
        $inCodItem            = $_REQUEST['inCodItem'];
        $slTipoItem			  = $_REQUEST['slTipoItem'];
        $slCombustivel        = $_REQUEST['slCombustivel'];
        $stNomItem            = $_REQUEST['stNomItem'];
        $inCodTipoCadastro    = $_REQUEST['inCodTipoCadastro'];

        $stMensagem 	  	  = "";
        $boSucesso			  = false;

        $rsItemAlmoxarifado   = new RecordSet;

        # Validações necessárias.
        if (empty($slTipoItem)) {
            $stMensagem = "Campo Tipo inválido";
        } elseif ($slTipoItem == 1 && empty($slCombustivel)) {
            $stMensagem = "Campo Combustível inválido";
        }

        if ($inCodTipoCadastro == 1) {
            if (empty($inCodItem)) {
                $stMensagem = "Informe o Item";
            }
        } elseif ($inCodTipoCadastro == 2) {
            if (empty($stChaveClassificacao)) {
                $stMensagem = "Informe a classificação";
            }
        }

        if (empty($stMensagem)) {
            # Recupera todos os itens da classificação selecionada, se o cadastro
            # for por classificação.
            if (!empty($stChaveClassificacao)) {
                $obTAlmoxarifadoCatalogoItem->setDado('cod_estrutural' , $stChaveClassificacao);
                $obTAlmoxarifadoCatalogoItem->setDado('cod_catalogo'   , $inCodCatalogo);
                $obTAlmoxarifadoCatalogoItem->recuperaItemPorClassificacao($rsItemAlmoxarifado, $stSqlFiltro);

                $inItemExistente = 0;

                $obTFrotaItem->setDado('cod_tipo', $slTipoItem);

                # Valida se o item a ser incluído já não faz parte do frota.
                while (!$rsItemAlmoxarifado->eof()) {

                    $obTFrotaItem->setDado('cod_item', $rsItemAlmoxarifado->getCampo('cod_item'));
                    $obTFrotaItem->recuperaPorChave($rsItemFrota);

                    if ($rsItemFrota->getNumLinhas() <= 0) {
                        # Insere na tabela frota.item
                        $obTFrotaItem->inclusao();

                        # Se o tipo for combustivel, insere na table frota.combustivel_item
                        if ($slTipoItem == 1) {
                            $obTFrotaCombustivelItem->setDado('cod_item',        $rsItemAlmoxarifado->getCampo('cod_item'));
                            $obTFrotaCombustivelItem->setDado('cod_combustivel', $slCombustivel 						  );
                            $obTFrotaCombustivelItem->inclusao();
                        }
                    }

                    $rsItemAlmoxarifado->proximo();
                }

                $stMsgAux = "Itens da Classificação Inseridos com Sucesso";

                $boSucesso = true;
            } elseif (!empty($inCodItem)) {

                # Verifica se o item já não tem um registro na frota.item
                $obTFrotaItem->setDado('cod_item', $inCodItem);
                $obTFrotaItem->recuperaPorChave($rsItem);

                if ($rsItem->getNumLinhas() > 0) {
                    $stMensagem = "Este item já está cadastrado no Frota";
                } else {
                    # Insere na tabela frota.item
                    $obTFrotaItem->setDado('cod_tipo', $slTipoItem);
                    $obTFrotaItem->inclusao();

                    # Se o tipo for combustivel, insere na table frota.combustivel_item
                    if ($slTipoItem == 1) {
                        $obTFrotaCombustivelItem->setDado('cod_item',        $inCodItem     );
                        $obTFrotaCombustivelItem->setDado('cod_combustivel', $slCombustivel );
                        $obTFrotaCombustivelItem->inclusao();
                    }

                    $stMsgAux = "Item: ".$inCodItem." - ".$stNomItem;
                    $boSucesso = true;
                }
            }

            if ($boSucesso) {
                SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,$stMsgAux,"incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
        }
    break;

    case 'alterar':
        if ($_REQUEST['slTipoItem'] == 1 && empty($_REQUEST['slCombustivel'])) {
            $stMensagem = "Campo combustível inválido.";
        }

        if (!$stMensagem) {
            //altera a table frota.item
            $obTFrotaItem->setDado( 'cod_item', $_REQUEST['inCodItem'] );
            $obTFrotaItem->setDado( 'cod_tipo', $_REQUEST['slTipoItem'] );
            $obTFrotaItem->alteracao();

            //exclui os combustiveis do item
            $obTFrotaCombustivelItem->setDado('cod_item', $_REQUEST['inCodItem'] );
            $obTFrotaCombustivelItem->exclusao();

            //se for combustivel, incluir na table frota.combustivel_item
            if ($_REQUEST['slTipoItem'] == 1) {
                $obTFrotaCombustivelItem->setDado( 'cod_item', $_REQUEST['inCodItem'] );
                $obTFrotaCombustivelItem->setDado( 'cod_combustivel', $_REQUEST['slCombustivel'] );
                $obTFrotaCombustivelItem->inclusao();
            }
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Item: '.$_REQUEST['inCodItem'].' - '.$_REQUEST['stNomItem'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
        }
    break;

    case 'excluir':
        // Só permite excluir caso esteja setado o ID do Item.
        if (!empty($_REQUEST['inCodItem'])) {

            //exclui da table frota.item
            $obTFrotaItem->setDado('cod_item', $_REQUEST['inCodItem'] );

            $obTFrotaItem->recuperaPermissaoAlterarItem($rsPodeExcluir,$stFiltroExclusao);

            // Retorna true ou false como STRING
            $boExcluirItem = $rsPodeExcluir->getCampo('permissao');

            if ($boExcluirItem == 'true') {

                //exclui da table frota.combustivel_item
                $obTFrotaCombustivelItem->setDado('cod_item', $_REQUEST['inCodItem'] );
                $obTFrotaCombustivelItem->exclusao();

                $obTFrotaItem->exclusao();
                SistemaLegado::alertaAviso($pgList."?".$sessao->id."&stAcao=".$stAcao,'Item: '.$_REQUEST['inCodItem'],"excluir","aviso", $sessao->id, "../");
             } else {
                $stErro = " Esse item não pode ser excluído pois já esta em uso no sistema! ";
                SistemaLegado::alertaAviso($pgList."?".$sessao->id."&stAcao=".$stAcao,urlencode($stErro),"n_excluir","erro", $sessao->id,"");
            }
        }
    break;
}

Sessao::encerraExcecao();

?>
