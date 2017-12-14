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
    * Página de Formulário Configurar Lançamentos de Despesa
    * Data de Criação   : 21/10/2011

    * @author Analista: Tonismar Bernardo
    * @author Desenvolvedor: Davi Aroldi

    * @ignore

    * Casos de uso: uc-02.02.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php" );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoContaTCEMS.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php" );
include_once(CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

$stPrograma = "ConfigurarLancamentosDespesa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

//não possui permissão de acesso a exercícios anteriores a 2012
if ( !Sessao::getExercicio() > '2012' ) {
    SistemaLegado::AlertaAviso( CAM_FW_INSTANCIAS."index/inicial.php", "Esta ação não está disponível à exercícios anteriores à 2012!", "n_incluir", "erro" );
}

$obTContabilidadePlanoConta = new TContabilidadePlanoContaTCEMS;

$stOrdem = " ORDER BY pc.cod_estrutural ";

$stFiltroMaterialConsumoDebito = " AND pc.exercicio = '".Sessao::getExercicio()."'
                AND pc.cod_estrutural like '1.1.5.6%' ";
//recupera contas para despepas de material de consumo
$obTContabilidadePlanoConta->recuperaContaPlanoAnalitica($rsPlanoContasMaterialConsumoDebito, $stFiltroMaterialConsumoDebito, $stOrdem);

$stFiltroMaterialConsumoCredito = " AND pc.exercicio = '".Sessao::getExercicio()."'
                AND pc.cod_estrutural = '2.1.3.1.1.01.00' ";
//recupera contas para despepas de material de consumo
$obTContabilidadePlanoConta->recuperaContaPlanoAnalitica($rsPlanoContasMaterialConsumoCredito, $stFiltroMaterialConsumoCredito, $stOrdem);

$stFiltroAlmoxarifadoDebito = " AND pc.exercicio = '".Sessao::getExercicio()."'
              AND pc.cod_estrutural like '3.3.1.1%' ";
$obTContabilidadePlanoConta->recuperaContaPlanoAnalitica($rsPlanoAlmoxarifadoDebito, $stFiltroAlmoxarifadoDebito, $stOrdem);

$stFiltroAlmoxarifadoCredito = " AND pc.exercicio = '".Sessao::getExercicio()."'
              AND pc.cod_estrutural like '1.1.5.6%' ";
$obTContabilidadePlanoConta->recuperaContaPlanoAnalitica($rsPlanoAlmoxarifadoCredito, $stFiltroAlmoxarifadoCredito, $stOrdem);

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodDespesaLista = new Hidden;
$obHdnCodDespesaLista->setName( "codContaDespesaLista" );
$obHdnCodDespesaLista->setId( "codContaDespesaLista" );
$obHdnCodDespesaLista->setValue( "" );

$obSpnListaDespesasDisponiveis = new Span;
$obSpnListaDespesasDisponiveis->setStyle("height: 200px; overflow: scroll");
$obSpnListaDespesasDisponiveis->setId("listaDespesasDisponiveis");

############################################# ABA LIQUIDACAO
##Despesa com Material de Consumo
$obRdoDespesaMaterialConsumo = new Radio;
$obRdoDespesaMaterialConsumo->setName( "rdoLiquidacao" );
$obRdoDespesaMaterialConsumo->setId( "rdoLiquidacaoConsumo" );
$obRdoDespesaMaterialConsumo->setRotulo( "Lançamentos" );
$obRdoDespesaMaterialConsumo->setLabel( "Despesa com Material de Consumo" );
$obRdoDespesaMaterialConsumo->setValue( "materialConsumo" );
$obRdoDespesaMaterialConsumo->obEvento->setOnClick("javascript: carregaContasLancamento('carregaContasLancamentoLiquidacao', 'materialConsumo')");
$obRdoDespesaMaterialConsumo->setChecked( true );

##Despesa com Material Permanente
$obRdoDespesaMaterialPermanente = new Radio;
$obRdoDespesaMaterialPermanente->setName( "rdoLiquidacao" );
$obRdoDespesaMaterialPermanente->setId( "rdoLiquidacaoPermanente" );
$obRdoDespesaMaterialPermanente->setLabel( "Despesa com Material Permanente" );
$obRdoDespesaMaterialPermanente->setValue( "materialPermanente" );
$obRdoDespesaMaterialPermanente->obEvento->setOnClick("javascript: carregaContasLancamento('carregaContasLancamentoLiquidacao', 'materialPermanente')");

##Despesa Pessoal
$obRdoDespesaPessoal = new Radio;
$obRdoDespesaPessoal->setName( "rdoLiquidacao" );
$obRdoDespesaPessoal->setId( "rdoLiquidacaoPessoal" );
$obRdoDespesaPessoal->setValue( "despesaPessoal" );
$obRdoDespesaPessoal->setLabel( "Despesa Pessoal" );
$obRdoDespesaPessoal->obEvento->setOnClick("javascript: carregaContasLancamento('carregaContasLancamentoLiquidacao', 'despesaPessoal')");

##Demais Despesas
$obRdoDemaisDespesas = new Radio;
$obRdoDemaisDespesas->setName( "rdoLiquidacao" );
$obRdoDemaisDespesas->setId( "rdoLiquidacaoDemais" );
$obRdoDemaisDespesas->setLabel( "Demais Despesas" );
$obRdoDemaisDespesas->setValue( "demaisDespesas" );
$obRdoDemaisDespesas->obEvento->setOnClick("javascript: carregaContasLancamento('carregaContasLancamentoLiquidacao', 'demaisDespesas')");

$obCmbLiquidacaoDebito = new Select;
$obCmbLiquidacaoDebito->setName( "stLancamentoDebitoLiquidacao" );
$obCmbLiquidacaoDebito->setId( "stLancamentoDebitoLiquidacao" );
$obCmbLiquidacaoDebito->setRotulo( "Débito" );
$obCmbLiquidacaoDebito->addOption( "", "Selecione" );
$obCmbLiquidacaoDebito->setValue( " " );
$obCmbLiquidacaoDebito->setCampoId( "cod_conta" );
$obCmbLiquidacaoDebito->setCampoDesc( "[cod_estrutural] - [nom_conta]" );
$obCmbLiquidacaoDebito->preencheCombo( $rsPlanoContasMaterialConsumoDebito );
$obCmbLiquidacaoDebito->setStyle( "width: 520" );
// $obCmbLiquidacaoDebito->setNull( false );

$obCmbLiquidacaoCredito = new Select;
$obCmbLiquidacaoCredito->setName( "stLancamentoCreditoLiquidacao" );
$obCmbLiquidacaoCredito->setId( "stLancamentoCreditoLiquidacao" );
$obCmbLiquidacaoCredito->setRotulo( "Crédito" );
// possui somente um registro
// $obCmbLiquidacaoCredito->addOption( "", "Selecione" );
$obCmbLiquidacaoCredito->setCampoId( "cod_conta" );
$obCmbLiquidacaoCredito->setCampoDesc( "[cod_estrutural] - [nom_conta]" );
$obCmbLiquidacaoCredito->preencheCombo( $rsPlanoContasMaterialConsumoCredito );
$obCmbLiquidacaoCredito->setStyle( "width: 520" );
// $obCmbLiquidacaoCredito->setNull( false );

############################################# ABA ALMOXARIFADO
$obCmbAlmoxarifadoDebito = new Select;
$obCmbAlmoxarifadoDebito->setName( "stLancamentoDebitoAlmoxarifado" );
$obCmbAlmoxarifadoDebito->setId( "stLancamentoDebitoAlmoxarifado" );
$obCmbAlmoxarifadoDebito->setRotulo( "Débito" );
$obCmbAlmoxarifadoDebito->addOption( "", "Selecione" );
$obCmbAlmoxarifadoDebito->setValue( " " );
$obCmbAlmoxarifadoDebito->setCampoId( "cod_conta" );
$obCmbAlmoxarifadoDebito->setCampoDesc( "[cod_estrutural] - [nom_conta]" );
$obCmbAlmoxarifadoDebito->preencheCombo( $rsPlanoAlmoxarifadoDebito );
$obCmbAlmoxarifadoDebito->setStyle( "width: 520" );
// $obCmbAlmoxarifadoDebito->setNull( false );

$obCmbAlmoxarifadoCredito = new Select;
$obCmbAlmoxarifadoCredito->setName( "stLancamentoCreditoAlmoxarifado" );
$obCmbAlmoxarifadoCredito->setId( "stLancamentoCreditoAlmoxarifado" );
$obCmbAlmoxarifadoCredito->setRotulo( "Crédito" );
$obCmbAlmoxarifadoCredito->addOption( "", "Selecione" );
$obCmbAlmoxarifadoCredito->setValue( " " );
$obCmbAlmoxarifadoCredito->setCampoId( "cod_conta" );
$obCmbAlmoxarifadoCredito->setCampoDesc( "[cod_estrutural] - [nom_conta]" );
$obCmbAlmoxarifadoCredito->preencheCombo( $rsPlanoAlmoxarifadoCredito );
$obCmbAlmoxarifadoCredito->setStyle( "width: 520" );
// $obCmbAlmoxarifadoCredito->setNull( false );

$stValor = SistemaLegado::pegaConfiguracao('forma_execucao_orcamento', 8, Sessao::getExercicio());

//RECUPERA LISTA DESPESA
$obROrcamentoReceita = new ROrcamentoDespesa;

$rsLista = new RecordSet();
$obROrcamentoReceita->listarDespesaConfiguracaoLancamentoNovo($rsLista2);

//INSTÃNCIA DO OBJETO LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código Estrutural");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "mascara_classificacao" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( "VINCULAR" );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:selecionaDespesa(this);" );
$obLista->ultimaAcao->setTipoLink( "checkbox" );
$obLista->ultimaAcao->addCampo("1","cod_conta"  );
$obLista->ultimaAcao->addCampo("2","descricao"  );
$obLista->commitAcao();
$obLista->montaInnerHtml();

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obFormularioAbas = new FormularioAbas;
$obFormularioAbas->addAba( "Liquidação" );
$obFormularioAbas->addTitulo( "Lançamentos de Liquidação" );
$obFormularioAbas->addComponente( $obRdoDespesaMaterialConsumo );
$obFormularioAbas->addComponente( $obRdoDespesaMaterialPermanente );
$obFormularioAbas->addComponente( $obRdoDespesaPessoal );
$obFormularioAbas->addComponente( $obRdoDemaisDespesas );
$obFormularioAbas->addTitulo( "Contas para Lançamento" );
$obFormularioAbas->addComponente( $obCmbLiquidacaoDebito );
$obFormularioAbas->addComponente( $obCmbLiquidacaoCredito );
$obFormularioAbas->addAba( "Almoxarifado" );
$obFormularioAbas->addTitulo( "Lançamentos de Almoxarifado" );
$obFormularioAbas->addTitulo( "Contas para Lançamento" );
$obFormularioAbas->addComponente( $obCmbAlmoxarifadoDebito );
$obFormularioAbas->addComponente( $obCmbAlmoxarifadoCredito );

$obTableTree = new TableTree;
$obTableTree->setRecordset            ($rsLista2);
$obTableTree->setArquivo              ($pgOcul);
$obTableTree->setParametros           (array('cod_estrutural' => 'mascara_classificacao', 'cod_conta' => 'cod_conta'));
$obTableTree->setComplementoParametros('stCtrl=montaTableDespesas');
$obTableTree->setSummary              ('Lista de Despesas');
$obTableTree->Head->addCabecalho      ('Código Estrutural',10);
$obTableTree->Head->addCabecalho      ('Descrição',40);
$obTableTree->Body->addCampo          ('mascara_classificacao','C');
$obTableTree->Body->addCampo          ('descricao', 'E');
$obTableTree->montaHTML();

$obSpnProgramas = new Span();
$obSpnProgramas->setId   ('spnProgramas');
$obSpnProgramas->setValue($obTableTree->getHTML());

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnCodDespesaLista );
$obFormulario->addSpan( $obSpnProgramas );
$obFormulario->addTitulo( "Informações de Lançamento" );
$obFormulario->addFormularioAbas( $obFormularioAbas );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/URBEM/ajax.php';
SistemaLegado::executaFrameOculto("jQuery('#listaDespesasDisponiveis').html('".$obLista->getHtml()."') ;");

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
