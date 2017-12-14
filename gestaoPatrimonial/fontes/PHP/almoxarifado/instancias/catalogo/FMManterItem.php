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
    * Página de Formulário Classificão
    * Data de Criação   : 05/12/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @ignore

    * Casos de uso: uc-03.03.06

    $Id: FMManterItem.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoItem.class.php";
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoClassificacao.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoMaterial.class.php";
include_once CAM_GP_ALM_COMPONENTES."IMontaCatalogoClassificacao.class.php";
include_once CAM_GA_ADM_COMPONENTES."ISelectUnidadeMedida.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php";

$stPrograma = "ManterItem";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgOcul2 = CAM_GP_ALM_PROCESSAMENTO.'OCIMontaCatalogoClassificacao.php';
$pgJs   = "JS".$stPrograma.".js";

$pgProx = $pgProc;

include_once($pgJs);

$arrayTransf = Sessao::read('transf4');

if ($arrayTransf) {
    $stFiltro = '';

    foreach ($arrayTransf as $stCampo => $stValor) {
        if (is_array($stValor)) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                $stFiltro .= "&".$stCampo2."=".urlencode( $stValor2 );
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

$stLocation = $pgList . "?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'].$stFiltro;

Sessao::write('Valores', array());
Sessao::write('Servicos', array());

$inCount = 0;

$obRAlmoxarifadoCatalogoClassificacao = new RAlmoxarifadoCatalogoClassificacao;
$obRAlmoxarifadoCatalogoItem = new RAlmoxarifadoCatalogoItem;
$obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem;

$rsNiveisClassificacao = new RecordSet;

$stAcao        = $_REQUEST["stAcao"];
$inCodigo      = $_REQUEST["inCodigo"];
$inCodCatalogo = $_REQUEST["inCodCatalogo"];
$stEstrutural  = $_REQUEST["stChaveClassificacao"];

if (empty($stAcao))
    $stAcao = "alterar";

$rsAtributosDisponiveis = $rsAtributosSelecionados = new RecordSet;
$boTemMovimentacao = false;
$boPermiteManutencao = true;

if ($inCodigo) {
    Sessao::write('carregarCombo', true);

    $obRAlmoxarifadoCatalogoItem->setCodigo($inCodigo);
    $obRAlmoxarifadoCatalogoItem->consultar();

    if ($stAcao == "alterar") {
        $obTAlmoxarifadoCatalogoItem->setDado('cod_item'  , $inCodigo);
        $obTAlmoxarifadoCatalogoItem->setDado('exercicio' , Sessao::getExercicio());
        $obTAlmoxarifadoCatalogoItem->recuperaValorItemUltimaCompra($rsItemUltimaCompra);
        if ($rsItemUltimaCompra->getNumLinhas() > 0) {
            $nuVlUltimaCompra = number_format(str_replace('.',',', $rsItemUltimaCompra->getCampo('vl_unitario_ultima_compra')),2,',','.');
        }
    }

    $stDescricao = htmlspecialchars($obRAlmoxarifadoCatalogoItem->getDescricao());
    $stDescricaoResumida = htmlspecialchars($obRAlmoxarifadoCatalogoItem->getDescricaoResumida());
    $boAtivo = $obRAlmoxarifadoCatalogoItem->getAtivo();
    $inCodClassificacao = $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoClassificacao->getCodigo();
    $inCodCatalogo = $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->getCodigo();
    $boPermiteManutencao = $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->getPermiteManutencao();
    include_once(CAM_GP_ALM_MAPEAMENTO. "TAlmoxarifadoEstoqueMaterial.class.php");
    $obTMapeamento = new TAlmoxarifadoEstoqueMaterial;
    $stFiltro = ' where cod_item = '.$obRAlmoxarifadoCatalogoItem->getCodigo();
    $obTMapeamento->recuperaTodos($rsEstoque, $stFiltro );
    $boTemMovimentacao = $rsEstoque->getNumLinhas() > 0;

    Sessao::write('transf3',$obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoClassificacao->getEstrutural());

    $stEstrutural    = $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoClassificacao->getEstrutural();
    $inCodTipo       = $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoTipoItem->getCodigo();
    $stDescTipo      = $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoTipoItem->getDescricao();
    $inCodUnidade    = $obRAlmoxarifadoCatalogoItem->obRUnidadeMedida->getCodUnidade();
    $stDescUnidade   = $obRAlmoxarifadoCatalogoItem->obRUnidadeMedida->getNome();
    $inCodGrandeza   = $obRAlmoxarifadoCatalogoItem->obRUnidadeMedida->obRGrandeza->getCodGrandeza();
    $nuEstoqueMaximo = $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoControleEstoque->getEstoqueMaximo();
    $nuEstoqueMinimo = $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoControleEstoque->getEstoqueMinimo();
    $nuPontoPedido   = $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoControleEstoque->getPontoDePedido();

    $stLocation .= "&inCodigo=$inCodigo";
    $obRAlmoxarifadoCatalogoItem->obRCadastroDinamico->setChavePersistenteValores( array("cod_item"=>$obRAlmoxarifadoCatalogoItem->getCodigo() ) );
    $obRAlmoxarifadoCatalogoItem->obRCadastroDinamico->setCodCadastro(2);
    $obRAlmoxarifadoCatalogoItem->obRCadastroDinamico->recuperaAtributosDisponiveis ( $rsAtributosDisponiveis  );
    $obRAlmoxarifadoCatalogoItem->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosSelecionados );

} else {
    $obRAlmoxarifadoCatalogoItem->obRCadastroDinamico->setPersistenteAtributos ( new TAdministracaoAtributoDinamico );
    $obRAlmoxarifadoCatalogoItem->obRCadastroDinamico->setCodCadastro(2);
    $obRAlmoxarifadoCatalogoItem->obRCadastroDinamico->recuperaAtributos( $rsAtributosDisponiveis );
    $inCodTipo = 1;
}

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCod = new Hidden;
$obHdnCod->setName("inCodigo");
$obHdnCod->setValue($inCodigo);

$obIMontaCatalogoClassificacao = new IMontaCatalogoClassificacao;
$obIMontaCatalogoClassificacao->obIMontaClassificacao->setOnChangeCombo("goOculto('preencheAtributos');");
$obIMontaCatalogoClassificacao->obIMontaClassificacao->setCodEstruturalReduzido($stEstrutural);
$obIMontaCatalogoClassificacao->obIMontaClassificacao->setComboClassificacaoCompleta(true);
$obIMontaCatalogoClassificacao->obIMontaClassificacao->setCodigoClassificacao($inCodClassificacao);
$obIMontaCatalogoClassificacao->obIMontaClassificacao->setUltimoNivelRequerido(true);
$obIMontaCatalogoClassificacao->obIMontaClassificacao->setClassificacaoRequerida(true);
$obIMontaCatalogoClassificacao->setCodCatalogo($inCodCatalogo);
$obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->setNull(false);
$obIMontaCatalogoClassificacao->setReadOnly($boTemMovimentacao || !$boPermiteManutencao);

if ($stAcao == "alterar") {
   $obLblCodItem = new Label;
   $obLblCodItem->setRotulo  ( "Código"   );
   $obLblCodItem->setId      ( "CodItem"  );
   $obLblCodItem->setName    ( "inCodigo" );
   $obLblCodItem->setValue   ( $inCodigo  );
}

if ($stAcao <> "consultar") {
   $obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoTipoItem->listar( $rsTipo ) ;

   $arRdTipo = array();
   for ($i = 0; $i < $rsTipo->getNumLinhas(); $i++) {
        if ($rsTipo->getCampo('cod_tipo') != 0) {
            $obRdTipo = new Radio;
            $obRdTipo->setRotulo                      ( "Tipo"                                      );
            $obRdTipo->setTitle                       ( "Selecione o tipo de item desejado."         );
            $obRdTipo->setName                        ( "inCodTipo"                                 );
            $obRdTipo->setLabel                       ( $rsTipo->getCampo('descricao')              );
            $obRdTipo->setValue                       ( $rsTipo->getCampo('cod_tipo')               );
            $obRdTipo->setChecked                     ( $inCodTipo == $rsTipo->getCampo('cod_tipo') );
            $obRdTipo->setNull                        ( false                                       );
            $arRdTipo[] = $obRdTipo;
            $rsTipo->proximo();
        }
    }
} else {
   $obLblTipo = new Label;
   $obLblTipo->setRotulo( "Tipo"      );
   $obLblTipo->setValue ( $stDescTipo );

   $obHdnTipo = new Hidden;
   $obHdnTipo->setName ( "inCodTipo" );
   $obHdnTipo->setValue( $inCodTipo  );
}

if ($boPermiteManutencao && !$boTemMovimentacao) {
   $obTxtDescricao = new TextBox;
   $obTxtDescricao->setRotulo              ( "Descrição"             );
   $obTxtDescricao->setTitle               ( "Informe a descrição do item." );
   $obTxtDescricao->setName                ( "stDescricao"     );
   $obTxtDescricao->setValue               ( $stDescricao      );
   $obTxtDescricao->setSize                ( 90                    );
   $obTxtDescricao->setMaxLength           ( 1600                  );
   $obTxtDescricao->setNull                ( false                  );

   $obTxtDescricaoResumida = new TextBox;
   $obTxtDescricaoResumida->setRotulo              ( "Descrição Resumida" );
   $obTxtDescricaoResumida->setTitle               ( "Informe a descrição resumida." );
   $obTxtDescricaoResumida->setName                ( "stDescricaoResumida"     );
   $obTxtDescricaoResumida->setValue               ( $stDescricaoResumida      );
   $obTxtDescricaoResumida->setSize                ( 80                   );
   $obTxtDescricaoResumida->setMaxLength           ( 100                   );
   $obTxtDescricaoResumida->setNull                ( false                  );
   $obTxtDescricaoResumida->obEvento->setOnBlur    ( "if(stDescricao.value=='')stDescricao.value = this.value;" );

} else {
   $obLblDescricao = new Label;
   $obLblDescricao->setRotulo  ( "Descrição"       );
   $obLblDescricao->setName    ( "stDescricao"     );
   $obLblDescricao->setValue   ( $stDescricao      );

   $obHdnDescricao = new Hidden;
   $obHdnDescricao->setName   ( "stDescricao" );
   $obHdnDescricao->setValue  ( $stDescricao  );

   $obLblDescricaoResumida = new Label();
   $obLblDescricaoResumida->setRotulo              ( "Descrição Resumida" );
   $obLblDescricaoResumida->setValue               ( $stDescricaoResumida      );

   $obHdnDescricaoResumida = new Hidden;
   $obHdnDescricaoResumida->setName                ( "stDescricaoResumida"     );
   $obHdnDescricaoResumida->setValue               ( $stDescricaoResumida      );

}

$obLblValorUltimaCompra = new Label;
$obLblValorUltimaCompra->setRotulo ( "Valor da Última Compra" );
$obLblValorUltimaCompra->setValue  ( $nuVlUltimaCompra        );

$obHdnValorUltimaCompra = new Hidden;
$obHdnValorUltimaCompra->setName   ( "nuVlUltimaCompra" );
$obHdnValorUltimaCompra->setValue  ( $nuVlUltimaCompra  );

$obRdInativo = new Radio;
$obRdInativo->setRotulo ( "Status" );
$obRdInativo->setTitle  ( "Selecione o status." );
$obRdInativo->setName   ( "boAtivo" );
$obRdInativo->setLabel  ( "Inativo" );
$obRdInativo->setChecked( !$boAtivo );
$obRdInativo->setValue  ( 'false' );
$obRdInativo->setNull   ( false );

$obRdAtivo = new Radio;
$obRdAtivo->setRotulo ( "Status" );
$obRdAtivo->setTitle  ( "Selecione o status." );
$obRdAtivo->setName   ( "boAtivo" );
$obRdAtivo->setLabel  ( "Ativo" );
$obRdAtivo->setChecked( $boAtivo );
$obRdAtivo->setValue  ( 'true' );
$obRdAtivo->setNull   ( false );

$obSpnListaAtributos = new Span;
$obSpnListaAtributos->setID('spnListaAtributos');

if (!$boTemMovimentacao) {
   $obISelectUnidadeMedida = new ISelectUnidadeMedida;
   $obISelectUnidadeMedida->setName                ( "inCodUnidade"                         );
   $obISelectUnidadeMedida->setValue               ( $inCodUnidade.'-'.$inCodGrandeza       );
   $obISelectUnidadeMedida->setStyle               ( "width: 200px"                         );
   $obISelectUnidadeMedida->setNull                ( false                                  );
} else {
   $obLblUnidadeMedida = new Label;
   $obLblUnidadeMedida->setRotulo              ( "Unidade Medida"                       );
   $obLblUnidadeMedida->setValue               ( $stDescUnidade                         );
   $obHdnUnidadeMedida = new Hidden;
   $obHdnUnidadeMedida->setValue               ( $inCodUnidade.'-'.$inCodGrandeza       );
   $obHdnUnidadeMedida->setName                ( 'inCodUnidade'                         );
}

$obTxtEstoqueMinimo = new Numerico;
$obTxtEstoqueMinimo->setRotulo              ( "Estoque Mínimo"               );
$obTxtEstoqueMinimo->setTitle               ( "Informe o estoque mínimo do item."   );
$obTxtEstoqueMinimo->setName                ( "nuEstoqueMinimo"              );
$obTxtEstoqueMinimo->setValue               ( $nuEstoqueMinimo != '0,0000' ?  $nuEstoqueMinimo : ''  );
$obTxtEstoqueMinimo->setNull                ( true                           );
$obTxtEstoqueMinimo->setNegativo            ( "false");

$obTxtPontoPedido = new Numerico;
$obTxtPontoPedido->setRotulo                ( "Ponto de Pedido"              );
$obTxtPontoPedido->setName                  ( "nuPontoPedido"                );
$obTxtPontoPedido->setTitle                 ( "Informe o ponto de pedido do item."  );
$obTxtPontoPedido->setValue                 (  $nuPontoPedido != '0,0000' ?  $nuPontoPedido : ''   );
$obTxtPontoPedido->setNull                  ( true                           );

$obTxtEstoqueMaximo = new Numerico;
$obTxtEstoqueMaximo->setRotulo              ( "Estoque Máximo"               );
$obTxtEstoqueMaximo->setTitle               ( "Informe o estoque máximo do item."   );
$obTxtEstoqueMaximo->setName                ( "nuEstoqueMaximo"              );
$obTxtEstoqueMaximo->setValue               ( $nuEstoqueMaximo  != '0,0000' ?  $nuEstoqueMaximo : ''  );
$obTxtEstoqueMaximo->setNull                ( true                           );

if ($stAcao == 'alterar') {

        $bloqueiaCombo = false;

        $stFiltroLancamento = "where cod_item =".$inCodigo;
        $TAlmoxarifadoLancamentoMaterial = new TAlmoxarifadoLancamentoMaterial;
        $TAlmoxarifadoLancamentoMaterial->recuperaLancamentosItem($rsLancamentos,$stFiltroLancamento);

        if ($rsLancamentos->arElementos[0]['lancamentos']  > 0) {
            $bloqueiaCombo = true;
        }

        Sessao::write('boBloqueiaCombo', $bloqueiaCombo);
}

$obCmbAtributos = new SelectMultiplo();
$obCmbAtributos->setDisabled($bloqueiaCombo);
$obCmbAtributos->setName   ( 'inCodAtributos');
$obCmbAtributos->setTitle  ( 'Selecione os atributos que serão selecionados na entrada de estoque.');
$obCmbAtributos->setRotulo ( "Atributos" );
$obCmbAtributos->setNull   ( true );
$obCmbAtributos->setTitle  ( "Selecione os atributos de entrada." );

// lista de atributos disponiveis
$obCmbAtributos->SetNomeLista1 ('inCodAtributosDisponiveis');
$obCmbAtributos->setCampoId1   ('cod_atributo');
$obCmbAtributos->setCampoDesc1 ('nom_atributo');
$obCmbAtributos->SetRecord1    ( $rsAtributosDisponiveis );

// lista de atributos selecionados
$obCmbAtributos->SetNomeLista2 ('inCodAtributosSelecionados');
$obCmbAtributos->setCampoId2   ('cod_atributo');
$obCmbAtributos->setCampoDesc2 ('nom_atributo');
$obCmbAtributos->SetRecord2    ( $rsAtributosSelecionados );

Sessao::write('rsAtributosSelecionados', $rsAtributosSelecionados);

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm              ($obForm);
$obFormulario->setAjuda             ("UC-03.03.06");
$obFormulario->addHidden            ($obHdnAcao);
$obFormulario->addHidden            ($obHdnCtrl);
$obFormulario->addHidden            ($obHdnCod);
$obHdnMovimentacao = new Hidden();
$obHdnMovimentacao->setName('boTemMovimentacao');
$obHdnMovimentacao->setValue($boTemMovimentacao);
$obFormulario->addHidden            ($obHdnMovimentacao);

$obFormulario->addTitulo            ( "Dados da Classificação" );

$obIMontaCatalogoClassificacao->geraFormulario($obFormulario);

$obFormulario->addTitulo     ( "Dados do Item" );

if ($stAcao == "alterar") {
   $obFormulario->addComponente ( $obLblCodItem );
}

if ($stAcao <> "consultar") {
   $obFormulario->agrupaComponentes ($arRdTipo );
} else {
   $obFormulario->addComponente ( $obLblTipo );
   $obFormulario->addHidden     ( $obHdnTipo );
}

if ($boPermiteManutencao&&!$boTemMovimentacao) {
   $obFormulario->addComponente ($obTxtDescricaoResumida);
   $obFormulario->addComponente ($obTxtDescricao);
} else {
   $obFormulario->addComponente ($obLblDescricaoResumida);
   $obFormulario->addHidden     ($obHdnDescricaoResumida);
   $obFormulario->addComponente ($obLblDescricao);
   $obFormulario->addHidden     ($obHdnDescricao);
 }

if ($stAcao == "alterar") {
    $obFormulario->addComponente ($obLblValorUltimaCompra);
    $obFormulario->addHidden     ($obHdnValorUltimaCompra);
}

if ($stAcao == "alterar") {
   $obFormulario->agrupaComponentes(array($obRdAtivo, $obRdInativo));
}

$obFormulario->addSpan      ( $obSpnListaAtributos );

if(!$boTemMovimentacao)
  $obFormulario->addComponente ($obISelectUnidadeMedida );
else {
  $obFormulario->addComponente ($obLblUnidadeMedida);
  $obFormulario->addHidden     ($obHdnUnidadeMedida);
}

$obFormulario->addTitulo    ( "Controle de Estoque" );
$obFormulario->addComponente ( $obTxtEstoqueMinimo );
$obFormulario->addComponente ( $obTxtPontoPedido   );
$obFormulario->addComponente ( $obTxtEstoqueMaximo );

$obFormulario->addTitulo    ( "Atributos de Entrada");
$obFormulario->addComponente ( $obCmbAtributos );

if ($stAcao=="incluir")
    $obFormulario->OK();
else
    $obFormulario->Cancelar( $stLocation );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
