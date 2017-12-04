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
 * Página de Formulário para configuração
 * Data de Criação   : 22/01/2007

 * @author Diego Barbosa Victoria

 * @ignore

 * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php";
include_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php";
include_once TTGO."TTGOConfiguracaoEntidade.class.php";
include_once TTGO."TTGOOrgao.class.php";

$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao   = $request->get('stAcao');
$stModulo = $request->get('modulo');

if (empty($stAcao)) {
    $stAcao = "alterar";
}

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;
$pgProc .="?"."&stModulo=".$stModulo;

if (isset($inCodigo)) {
    $stLocation .= "&inCodigo=$inCodigo";
}

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTxtCodigo = new TextBox;
$obTxtCodigo->setRotulo          ( "Código da Entidade");
$obTxtCodigo->setName            ( "inCodigo"  );
$obTxtCodigo->setId              ( "inCodigo"  );
$obTxtCodigo->setObrigatorio     ( true        );
$obTxtCodigo->setSize            ( 6           );
$obTxtCodigo->setMaxLength       ( 6           );
$obTxtCodigo->setMinLength       ( 6           );
$obTxtCodigo->setAlfaNumerico    ( false       );

$obLabel = new Label();
$obLabel->setValue("Os arquivos serão agrupados pelo código informado para as entidades");

//Lista de códigos cadastrados para cada entidade
$obPersistente = new TTGOConfiguracaoEntidade();

if ($stAcao == 'undgestora') {
    $obPersistente->setDado('parametro','tc_codigo_unidade_gestora');
    $obPersistente->recuperaCodigos($rsEntidades,''," ORDER BY ent.cod_entidade");
    foreach ($rsEntidades->arElementos as $index => $value) {
        if (substr($value['valor'],3,1) == "_") {
            $valor = substr($value['valor'],0,3);
        } else {
            $valor = substr($value['valor'],0,4);
        }
        $rsEntidades->arElementos[$index]['valor'] = $valor;
    }
} elseif ($stAcao == 'balancete') {
    $obPersistente->setDado('cod_modulo',$stModulo);
    $obPersistente->setDado('parametro','tc_codigo_tipo_balancete');
    $obPersistente->recuperaCodigos($rsEntidades,''," ORDER BY ent.cod_entidade");
} elseif ($stAcao == 'orgao') {
    $obPersistente->setDado('parametro','tc_codigo_tipo_orgao');
    $obPersistente->recuperaCodigos($rsEntidades,''," ORDER BY ent.cod_entidade");
}

$obLista = new Lista();
$obLista->setMostraPaginacao(false);
$obLista->setTitulo('Lista de Códigos de cada Entidade');
$obLista->setRecordSet($rsEntidades);
//Cabeçalhos
$obLista->addCabecalho('', 5);
$obLista->addCabecalho('Entidade', 55);

//Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[cod_entidade] - [nom_cgm]');
$obLista->commitDado();

if ($stAcao == 'undgestora') {
    $obTxtCodigo = new TextBox();
    $obTxtCodigo->setName           ('inCodigo_[cod_entidade]');
    $obTxtCodigo->setValue          ('[valor]');
    $obTxtCodigo->setSize           ( 8 );
    $obTxtCodigo->setMaxLength      ( 4 );
    $obTxtCodigo->setMinLength      ( 4 );
    $obTxtCodigo->setInteiro        ( true  );

    $obLista->addCabecalho('Unidade Gestora', 10);
    $obLista->addDadoComponente( $obTxtCodigo , false);
    $obLista->ultimoDado->setAlinhamento('CENTRO');
    $obLista->ultimoDado->setCampo( "valor" );
    $obLista->commitDadoComponente();

    $obTTGOOrgao = new TTGOOrgao();
    $obTTGOOrgao->setDado( 'exercicio', Sessao::getExercicio() );
    $obTTGOOrgao->recuperaUnidadeOrcamentaria( $rsUnidade );

    $obCmbUnidade = new Select();
    $obCmbUnidade->setName( 'inNumUnidade_[cod_entidade]' );
    $obCmbUnidade->setId( 'inNumUnidade_[cod_entidade]' );
    $obCmbUnidade->setValue( 'orgao_unidade' );
    $obCmbUnidade->addOption( '', 'Selecione' );
    $obCmbUnidade->setCampoId( '[num_unidade]_[num_orgao]' );
    $obCmbUnidade->setCampoDesc( 'nom_unidade' );
    $obCmbUnidade->preencheCombo( $rsUnidade );

    $obLista->addCabecalho('Órgão - Unidade', 10);
    $obLista->addDadoComponente( $obCmbUnidade , false);
    $obLista->ultimoDado->setAlinhamento('CENTRO');
    $obLista->ultimoDado->setCampo( "orgao_unidade" );
    $obLista->commitDadoComponente();
} elseif ($stAcao == 'balancete') {
    $obCmbTipoBalancete = new Select();
    $obCmbTipoBalancete->setName( 'inTipoBalancete_[cod_entidade]' );
    $obCmbTipoBalancete->addOption( '', 'Selecione' );
    $obCmbTipoBalancete->addOption( '01', 'Administração Direta (Prefeitura)' );
    $obCmbTipoBalancete->addOption( '02', 'Legislativo (Câmara)' );
    $obCmbTipoBalancete->addOption( '03', 'FUNDEF' );
    $obCmbTipoBalancete->addOption( '04', 'Administração Direta - Fundo Especial' );
    $obCmbTipoBalancete->addOption( '05', 'Administração Indireta  Autarquia' );
    $obCmbTipoBalancete->addOption( '06', 'Administração Indireta Fundação' );
    $obCmbTipoBalancete->addOption( '07', 'Empresas Públicas' );
    $obCmbTipoBalancete->addOption( '08', 'Sociedade de Economia Mista' );
    $obCmbTipoBalancete->addOption( '09', 'Previdência Municipal (Regime Próprios)' );
    $obCmbTipoBalancete->addOption( '10', 'Fundo Municipal de Saúde - FMS' );
    $obCmbTipoBalancete->addOption( '99', 'Outros'   );
    $obCmbTipoBalancete->setValue('[valor]');
    $obLista->addCabecalho('Tipo do Balancete',25);
    $obLista->addDadoComponente( $obCmbTipoBalancete, false );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->ultimoDado->setCampo( 'tipo_balancete' );
    $obLista->commitDadoComponente();
}

$obSpnCodigos = new Span();
$obSpnCodigos->setId('spnCodigos');
$obLista->montaHTML();
$obSpnCodigos->setValue($obLista->getHTML());

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm              ($obForm);

$obFormulario->addHidden            ($obHdnAcao);
$obFormulario->addHidden            ($obHdnCtrl);
$obFormulario->addTitulo            ( "Parâmetros por Entidade" );
$obFormulario->addSpan              ($obSpnCodigos);

$obFormulario->OK      ();
$obFormulario->show();

//SistemaLegado::executaFrameOculto( "buscaValor('recuperaFormularioAlteracao','$pgOcul','$pgProc','','Sessao::getId()');" );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
