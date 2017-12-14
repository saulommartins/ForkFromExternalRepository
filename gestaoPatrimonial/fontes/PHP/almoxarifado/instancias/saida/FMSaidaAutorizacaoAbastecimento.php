<?php /*
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
  * Página de Formulario
  * Data de Criação: 04/01/2006

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

  * @ignore

  $Id: FMMovimentacaoRequisicao.php 37339 2009-01-16 11:41:18Z melo $

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_ALM_COMPONENTES."ISelectAlmoxarifadoAlmoxarife.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoAutorizacao.class.php";
include_once CAM_GP_FRO_MAPEAMENTO."TFrotaAutorizacao.class.php";
include_once TALM."TAlmoxarifadoCatalogoItemMarca.class.php";
include_once TALM."TAlmoxarifadoEstoqueMaterial.class.php";

$stAcao = $request->get('stAcao');

# Define o nome dos arquivos PHP
$stPrograma = "SaidaAutorizacaoAbastecimento";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once $pgJs;

$obTLancamentoAutorizacao = new TAlmoxarifadoLancamentoAutorizacao;
$obTLancamentoAutorizacao->setDado('exercicio'       , $_REQUEST['stExercicio'] );
$obTLancamentoAutorizacao->setDado('cod_autorizacao' , $_REQUEST['inCodAutorizacao'] );
$obTLancamentoAutorizacao->recuperaRelacionamento($rsLancamento);

Sessao::write('saida', $rsLancamento );

$stExercicio      = $rsLancamento->getCampo('exercicio');
$inCodAutorizacao = $rsLancamento->getCampo('cod_autorizacao');
$inCodVeiculo     = $rsLancamento->getCampo('cod_veiculo');
$inCodModelo      = $rsLancamento->getCampo('cod_modelo');
$stNomModelo      = $rsLancamento->getCampo('nom_modelo');
$inCodMarca       = $rsLancamento->getCampo('cod_marca');
$stNomMarca       = $rsLancamento->getCampo('nom_marca');

$flKlmSaida           = $rsLancamento->getCampo('kil_saida');
$flKlmInicial         = $rsLancamento->getCampo('km_inicial');
$inCGMRespAutorizacao = $rsLancamento->getCampo('cgm_resp_autorizacao');

$nuQuilometragem = $flKlmSaida ? $flKlmSaida : $flKlmInicial;

$inCodItem    = $rsLancamento->getCampo('cod_item');
$stDescItem   = $rsLancamento->getCampo('descricao_resumida');
$inCodUnidade = $rsLancamento->getCampo('cod_unidade');
$stNomUnidade = $rsLancamento->getCampo('nom_unidade');

Sessao::consultarDadosSessao();
$inCGMAlmoxarife        = Sessao::read('numCgm');
$stNomAlmoxarife        = Sessao::read('nomCgm');

$obTFrotaAutorizacao = new TFrotaAutorizacao();
$stFiltro =' where cod_veiculo='.$rsLancamento->getCampo('cod_veiculo');
$stFiltro.=' and cod_autorizacao='.$rsLancamento->getCampo('cod_autorizacao');
$obTFrotaAutorizacao->recuperaTodos($rsAutorizacaoAbastecimento, $stFiltro);

$obTCGM = new TCGM;
$obTCGM->setDado('numcgm', $rsAutorizacaoAbastecimento->getCampo('cgm_motorista'));
$obTCGM->consultar();

$numCgmMotorista   = $obTCGM->getDado('numcgm');
$stNomCgmMotorista = $obTCGM->getDado('nom_cgm');

$inNumAbastecimento = number_format($rsAutorizacaoAbastecimento->getCampo('quantidade'), 4, ",", ".");

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obLblAutorizacao = new Label;
$obLblAutorizacao->setRotulo ( "Autorização"   );
$obLblAutorizacao->setName   ( "stAutorizacao" );
$obLblAutorizacao->setValue  ( $inCodAutorizacao."/".$stExercicio );

$obLblAlmoxarife = new Label;
$obLblAlmoxarife->setRotulo ( "Almoxarife" );
$obLblAlmoxarife->setName   ( "stAlmoxarife" );
$obLblAlmoxarife->setValue  ( "$inCGMAlmoxarife - $stNomAlmoxarife" );

$obLblVeiculo = new Label;
$obLblVeiculo->setRotulo ( "Veículo" );
$obLblVeiculo->setName   ( "stVeiculo" );
$obLblVeiculo->setValue  ( "$inCodVeiculo - $stNomMarca - $stNomModelo" );

$obQuilometragem = new Numerico;
$obQuilometragem->setName      ( "nuQuilometragem" );
$obQuilometragem->setId        ( "nuQuilometragem" );
$obQuilometragem->setRotulo    ( "Quilometragem"   );
$obQuilometragem->setTitle     ( "Informe a Quilometragem do veículo." );
$obQuilometragem->setNegativo  ( false );
$obQuilometragem->setNull      ( true );
$obQuilometragem->setSize      ( 20 );
$obQuilometragem->setMaxLength ( 17 );
$obQuilometragem->setDecimais  ( 0 );
$obQuilometragem->setMinValue  ( $nuQuilometragem );
$obQuilometragem->setValue     ( number_format($nuQuilometragem, 0, "", "."));

$obSelectAlmoxarifado = new ISelectAlmoxarifadoAlmoxarife;
$obSelectAlmoxarifado->setId("inCodAlmoxarifado");
$obSelectAlmoxarifado->obEvento->setOnChange("validaAlmoxarifado(); montaParametrosGET('recuperaSaldoItem');");

$obLblSolicitante = new Label;
$obLblSolicitante->setRotulo ( "Motorista" );
$obLblSolicitante->setName   ( "stSolicitante" );
$obLblSolicitante->setValue  ( "$numCgmMotorista - $stNomCgmMotorista" );

$obTxtObservacao = new TextArea;
$obTxtObservacao->setName  ( "stObservacao" );
$obTxtObservacao->setId    ( "stObservacao");
$obTxtObservacao->setRotulo( "Observação" );
$obTxtObservacao->setTitle ( "Informe a observação." );
$obTxtObservacao->setCols  ( 30 );
$obTxtObservacao->setRows  ( 3  );
$obTxtObservacao->setMaxCaracteres( '160' );
$obTxtObservacao->setCols(4);
$obTxtObservacao->setRows(4);
$obTxtObservacao->setNull  ( true );
$obTxtObservacao->obEvento->setOnKeyUp("return Contador(this, 160);");

$obHdnItem = new Hidden;
$obHdnItem->setId    ('inCodItem');
$obHdnItem->setName  ('inCodItem');
$obHdnItem->setValue ($inCodItem);

$obHdnCGMUsuario = new Hidden;
$obHdnCGMUsuario->setId    ('stCGMUsuario');
$obHdnCGMUsuario->setName  ('stCGMUsuario');

$obLblItem = new Label;
$obLblItem->setRotulo ( "Item" );
$obLblItem->setName   ( "stItem" );
$obLblItem->setValue  ( $inCodItem." - ".$stDescItem );

$obLblUnidadeMedida = new Label;
$obLblUnidadeMedida->setRotulo ( "Unidade de Medida" );
$obLblUnidadeMedida->setName   ( "stUnidadeMedida" );
$obLblUnidadeMedida->setValue  ( $inCodUnidade." - ".$stNomUnidade );

$obHdnTotalAbastecimento = new Hidden;
$obHdnTotalAbastecimento->setName  ('nuQtdeAutorizada');

$obTotalAbastecimento = new Label;
$obTotalAbastecimento->setName   ( "stQuantidade" );

if ($inNumAbastecimento == 0) {
    $obTotalAbastecimento->setRotulo( "Completar Tanque" );
    $obTotalAbastecimento->setValue ( "Sim" );

    # Armazena o valor da quantidade autorizada no Hidden.
    $obHdnTotalAbastecimento->setValue (0);
} else {
    $obTotalAbastecimento->setRotulo( "Quantidade Autorizada" );
    $obTotalAbastecimento->setValue ( $inNumAbastecimento);

    # Armazena o valor da quantidade autorizada no Hidden.
    $obHdnTotalAbastecimento->setValue ($inNumAbastecimento);
}

# Recupera todas as marcas cadatradas.
$obTAlmoxarifadoCatalogoItemMarca = new TAlmoxarifadoCatalogoItemMarca;
$stFiltro = " AND acim.cod_item = ".$inCodItem;

$obTAlmoxarifadoCatalogoItemMarca->recuperaItemMarcaComSaldo($rsMarca, $stFiltro);

$obSelectMarca = new Select;
$obSelectMarca->setRotulo     ("Marca"              );
$obSelectMarca->setName       ("inCodMarca"         );
$obSelectMarca->setId         ("inCodMarca"         );
$obSelectMarca->setTitle      ("Selecione a Marca." );
$obSelectMarca->setNull       (false                );
$obSelectMarca->setCampoID    ("cod_marca"          );
$obSelectMarca->addOption     ("","Selecione"       );
$obSelectMarca->setCampoDesc  ("[descricao]"        );
$obSelectMarca->preencheCombo ($rsMarca             );
$obSelectMarca->obEvento->setOnChange("montaParametrosGET('recuperaSaldoItem');");

# Recupera todos os centros de custos.
$obTAlmoxarifadoEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial;

$stFiltro  = "";
$stFiltro .= " AND aem.cod_item = ".$inCodItem;
$stFiltro .= " AND accp.numcgm  = ".Sessao::read('numCgm');

$obTAlmoxarifadoEstoqueMaterial->recuperaEstoqueCentroDeCustoComSaldo($rsCentroCusto, $stFiltro);

if ($rsCentroCusto->getNumLinhas() < 1) {  
    $jsOnload .= "alertPopUp('Atenção!','Este Almoxarife não possui permissão para nenhum centro de custo!','');";
}

$obSelectCentroCusto = new Select;
$obSelectCentroCusto->setRotulo     ("Centro de Custo"    );
$obSelectCentroCusto->setId         ("inCodCentroCusto"   );
$obSelectCentroCusto->setName       ("inCodCentroCusto"   );
$obSelectCentroCusto->setTitle      ("Selecione o Centro de Custo." );
$obSelectCentroCusto->setNull       (false                );
$obSelectCentroCusto->setCampoID    ("cod_centro"         );
$obSelectCentroCusto->addOption     ("","Selecione"       );
$obSelectCentroCusto->setCampoDesc  ("[descricao]"        );
$obSelectCentroCusto->preencheCombo ($rsCentroCusto       );
$obSelectCentroCusto->obEvento->setOnChange("montaParametrosGET('recuperaSaldoItem');");

# Hidden que grava o saldo do estoque do item.
$obHdnSaldo = new Hidden;
$obHdnSaldo->setId    ('nuHdnSaldoEstoque');
$obHdnSaldo->setName  ('nuHdnSaldoEstoque');

# Label com o saldo do item, conforme almoxarifado, marca e centro de custo.
$obLblSaldo = new Label;
$obLblSaldo->setRotulo ("Saldo"         );
$obLblSaldo->setName   ("nuSaldoEstoque");
$obLblSaldo->setId     ("nuSaldoEstoque");
$obLblSaldo->setValue  ('0,0000');

# Quando for uma quantidade específica autorizada, cria um Label informando.
if ($inNumAbastecimento > 0) {
    $obLblQtde = new Label;
    $obLblQtde->setRotulo ( "Quantidade" );
    $obLblQtde->setName   ( "stVeiculo" );
    $obLblQtde->setValue  ( $inNumAbastecimento );

    $obHdnQtde = new Hidden;
    $obHdnQtde->setId    ('nuQuantidade');
    $obHdnQtde->setname  ('nuQuantidade');
    $obHdnQtde->setValue ($inNumAbastecimento);

} else {
    # Quantidade que o usuário irá informar de saída.
    $obTxtQtde = new Quantidade;
    $obTxtQtde->setId       ('nuQuantidade');
    $obTxtQtde->setname     ('nuQuantidade');
    $obTxtQtde->setDecimais (4);
    $obTxtQtde->setMinValue (0.0001);
}

if ($stAcao == "saida") {
    include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeConfiguracaoLancamentoContaDespesaItem.class.php";
    include_once CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php";
    $obROrcamentoDespesa = new ROrcamentoDespesa;
    $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao('3.3.9.0.30');
    $obROrcamentoDespesa->listarCodEstruturalDespesa($rsContaDespesa, " AND conta_despesa.cod_estrutural <> '3.3.9.0.30.00.00.00.00' ORDER BY conta_despesa.cod_estrutural");

    $obTContabilidadeConfiguracaoLancamentoContaDespesaItem = new TContabilidadeConfiguracaoLancamentoContaDespesaItem;
    $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->setDado('cod_item', $inCodItem);
    $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->setDado('exercicio', Sessao::getExercicio());
    $boOk = $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->consultarItem();

    $obCmbDesdobramento = new Select;
    $obCmbDesdobramento->setRotulo( "Desdobramento para Lançamento Contábil" );
    $obCmbDesdobramento->setName( "inCodContaDespesa" );
    $obCmbDesdobramento->setCampoID( "cod_conta" );
    $obCmbDesdobramento->setCampoDesc( "cod_estrutural" );
    $obCmbDesdobramento->addOption( "", "Selecione" );
    $obCmbDesdobramento->preencheCombo($rsContaDespesa);

    if ($obTContabilidadeConfiguracaoLancamentoContaDespesaItem->getDado('cod_conta_despesa')) {
        $obCmbDesdobramento->setDisabled(true);
        $obCmbDesdobramento->setValue( $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->getDado('cod_conta_despesa'));

        $obHdnDesdobramento = new Hidden;
        $obHdnDesdobramento->setId('hdnDesdobramento');
        $obHdnDesdobramento->setName('hdnDesdobramento');
        $obHdnDesdobramento->setValue($obTContabilidadeConfiguracaoLancamentoContaDespesaItem->getDado('cod_conta_despesa'));
    }
}

# Formulário
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->setAjuda  ("UC-03.03.33");

$obFormulario->addHidden ( $obHdnCtrl               );
$obFormulario->addHidden ( $obHdnAcao               );
$obFormulario->addHidden ( $obHdnTotalAbastecimento );
$obFormulario->addHidden ( $obHdnItem               );
$obFormulario->addHidden ( $obHdnSaldo              );
$obFormulario->addHidden ( $obHdnCGMUsuario         );

$obFormulario->addTitulo     ( 'Dados da Autorização' );
$obFormulario->addComponente ( $obLblAutorizacao      );
$obFormulario->addComponente ( $obLblAlmoxarife       );
$obFormulario->addComponente ( $obLblVeiculo          );
$obFormulario->addComponente ( $obQuilometragem       );
$obFormulario->addComponente ( $obSelectAlmoxarifado  );
$obFormulario->addComponente ( $obLblSolicitante      );
$obFormulario->addComponente ( $obTxtObservacao       );

$obFormulario->addTitulo     ( 'Dados do Item'       );
$obFormulario->addComponente ( $obLblItem            );
$obFormulario->addComponente ( $obLblUnidadeMedida   );
$obFormulario->addComponente ( $obTotalAbastecimento );
$obFormulario->addComponente ( $obSelectMarca        );
$obFormulario->addComponente ( $obSelectCentroCusto  );
$obFormulario->addComponente ( $obLblSaldo           );

if ($inNumAbastecimento > 0) {
    $obFormulario->addHidden     ( $obHdnQtde );
    $obFormulario->addComponente ( $obLblQtde );
} else
    $obFormulario->addComponente ( $obTxtQtde );

if ($stAcao == "saida") {
    $obFormulario->addComponente ( $obCmbDesdobramento   );
    
    if(isset($obHdnDesdobramento)){
        $obFormulario->addHidden ( $obHdnDesdobramento );
    }
}

if ($stAcao == "incluir") {
    $obFormulario->OK();
} else {
    if ($stAcao == "saida") {

        $obOk  = new Ok(false);
        $obOk->obEvento->setOnClick("validaUsuarioSecundario('".$obOk->obEvento->getOnClick()."');");

        $obCancelar  = new Cancelar;
        $obCancelar->obEvento->setOnClick("Cancelar('".$pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro.'&stFiltroSigla='.$request->get('stFiltroSigla').'&stFiltroDescricao='.$request->get('stFiltroDescricao')."','telaPrincipal');");

        $obFormulario->defineBarra( array( $obOk, $obCancelar ) );
    } else {
        $obFormulario->Cancelar( $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro.'&stFiltroSigla='.$request->get('stFiltroSigla').'&stFiltroDescricao='.$request->get('stFiltroDescricao'), true);
    }
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
