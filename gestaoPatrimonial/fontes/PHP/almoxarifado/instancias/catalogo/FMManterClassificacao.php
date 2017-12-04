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
    * Data de Criação   : 25/11/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott

    * @ignore

    $Id: FMManterClassificacao.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-03.03.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoCatalogoClassificacao.class.php");

$stPrograma = "ManterClassificacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

$obRegra = new RAlmoxarifadoCatalogoClassificacao();

Sessao::write('filtro', array());

// RecordSets dos Atributos
$rsAtributosDisponiveis = $rsAtributosSelecionados = new RecordSet;

$stAcao = $request->get('stAcao');

if ( empty( $stAcao )) {
    $stAcao = "alterar";
}

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
$stFiltro= isset($stFiltro) ? $stFiltro : "";

$stLocation = $pgList . "?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'].$stFiltro;;

Sessao::write('Valores', array());
$inCount = 0;

if ($stAcao == 'alterar') {
    $obRegra->setCodigo( $_REQUEST['inCodigoClassificacao'] );
    $obRegra->obRAlmoxarifadoCatalogo->setCodigo($_REQUEST['inCodigo']);

    $obRegra->obRAlmoxarifadoCatalogo->consultar();

    $obErro = $obRegra->consultar();
    if($obErro->ocorreu())
        echo $obErro->getDescricao();

    $ultimoEstrutural = SistemaLegado::pegaDado("cod_estrutural", "almoxarifado.catalogo_classificacao", " where cod_catalogo=".$_REQUEST['inCodigo']." ORDER BY cod_estrutural DESC LIMIT 1");
    $tamanhoCampoCodEstrutural = strlen($ultimoEstrutural);

    $inCodCatalogo = $obRegra->obRAlmoxarifadoCatalogo->getCodigo();
    $stDescricaoCatalogo = $obRegra->obRAlmoxarifadoCatalogo->getDescricao();

    $stDescricaoNivel = trim($obRegra->getDescricao());

    $obErro = $obRegra->listarClassificacao($rsClassificacao);
    if ( !$rsClassificacao->eof() ) {
        $rsClassificacao->ordena('nivel','DESC');
        $inCodNivel               = $rsClassificacao->getCampo('cod_nivel');
        $stDescricaoClassificacao = $rsClassificacao->getCampo('descricao_nivel');
    }

    $obHdnCodigoEstrutural = new Hidden;
    $obHdnCodigoEstrutural->setName( "stCodigoEstrutural" );
    $obHdnCodigoEstrutural->setValue( $_REQUEST['stCodigoEstrutural'] );

    $obHdnNivel = new Hidden;
    $obHdnNivel->setName( "inNivel" );
    $obHdnNivel->setValue( $rsClassificacao->getCampo('nivel') );
}

$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodClassificacao = new Hidden;
$obHdnCodClassificacao->setName("inCodigoClassificacao");
$obHdnCodClassificacao->setValue($request->get('inCodigoClassificacao'));

$obHdnCod = new Hidden;
$obHdnCod->setName("inCodCatalogo");
$obHdnCod->setValue(isset($inCodCatalogo) ? $inCodCatalogo : null);

$obHdnCodN = new Hidden;
$obHdnCodN->setName("inCodigoNivel");
$obHdnCodN->setValue(isset($inCodNivel) ? ($inCodNivel + 1) : null);

$obHdnCodCombo = new Hidden;
$obHdnCodCombo->setName("inCurrCombo");
$obHdnCodCombo->setValue("");

$obHdnCodNextCombo = new Hidden;
$obHdnCodNextCombo->setName("inNextCombo");
$obHdnCodNextCombo->setValue("");

$obHdnListaInclusao = new Hidden;
$obHdnListaInclusao->setName("stListaInclusao");

if ($stAcao == 'alterar') {
    $obHdnListaInclusao->setValue("");
} else {
    $obHdnListaInclusao->setValue("true");
}

$obHdnValida = new HiddenEval;
$obHdnValida->setName("stValida");
$obHdnValida->setValue("");

$obLblDadoNivel= new Label;
$obLblDadoNivel->setRotulo ( isset($stDescricaoClassificacao) ? $stDescricaoClassificacao : null );
$obLblDadoNivel->setValue  ( isset($inCodNivel) ? $inCodNivel : null );

$obLblCodigo= new Label;
$obLblCodigo->setRotulo ( "Código"  );
$obLblCodigo->setValue  ( isset($inCodigo) ? $inCodigo : null);

$obLblCodigoNivel = new Label;
$obLblCodigoNivel->setRotulo ( "Código"  );
$obLblCodigoNivel->setValue  ( $request->get('inCodigoClassificacao') );

if ($request->get('inCodigo')) {
    $obLblCodigo= new Label;
    $obLblCodigo->setRotulo ( "Catálogo"  );
    $obLblCodigo->setValue  ( $inCodCatalogo . " - " . $stDescricaoCatalogo );

    $obTxtCodEstrutural = new Label;
    $obTxtCodEstrutural->setRotulo ( "Código Estrutural"  );
    $obTxtCodEstrutural->setValue  ( $_REQUEST['stCodigoEstrutural'] );
} else {
    $obTxtCodCatalogo = new TextBox;
    $obTxtCodCatalogo->setRotulo              ( "Catálogo"             );
    $obTxtCodCatalogo->setTitle               ( "Selecione o catálogo." );
    $obTxtCodCatalogo->setName                ( "inCodCatalogoTxt"     );
    $obTxtCodCatalogo->setId                  ( "inCodCatalogoTxt"     );
    $obTxtCodCatalogo->setValue               ( $request->get('inCodCatalogoTxt') );
    $obTxtCodCatalogo->setSize                ( 6                      );
    $obTxtCodCatalogo->setMaxLength           ( 3                      );
    $obTxtCodCatalogo->setInteiro             ( true                   );
    $obTxtCodCatalogo->setNull                ( false                  );
    $obTxtCodCatalogo->obEvento->setOnChange  ( "mudaCombo(this,document.getElementById('inCatalogo'));"  );

    $obRegra->obRAlmoxarifadoCatalogo->listar( $rsCatalogo     ) ;

    $obCmbCodCatalogo = new Select;
    $obCmbCodCatalogo->setRotulo              ( "Catálogo"                      );
    $obCmbCodCatalogo->setName                ( "inCodCatalogo"                 );
    $obCmbCodCatalogo->setId                  ( "inCatalogo"                    );
    $obCmbCodCatalogo->setValue               ( $request->get('inCodCatalogoTxt'));
    $obCmbCodCatalogo->setStyle               ( "width: 200px"                  );
    $obCmbCodCatalogo->setCampoID             ( "cod_catalogo"                  );
    $obCmbCodCatalogo->setCampoDesc           ( "descricao"                     );
    $obCmbCodCatalogo->addOption              ( "", "Selecione"                 );
    $obCmbCodCatalogo->preencheCombo          ( $rsCatalogo                     );
    $obCmbCodCatalogo->obEvento->setOnChange  ( "mudaCatalogo(this,false);"           );
}

$obCmbNivel = new Select;
$obCmbNivel->setRotulo              ( "Nível a Inserir"                      );
$obCmbNivel->setTitle               ( "Selecione o nível em que será inserida a classificação." );
$obCmbNivel->setName                ( "inCodNivel"                           );
$obCmbNivel->setValue               ( isset($inCodNivel) ? $inCodNivel : null);
$obCmbNivel->setStyle               ( "width: 200px"                         );
$obCmbNivel->setCampoID             ( "nivel"                                );
$obCmbNivel->setCampoDesc           ( "nivel - descricao"                    );
$obCmbNivel->addOption              ( "", "Selecione"                        );
$obCmbNivel->obEvento->setOnChange  ( "verificaNivel(this);"                 );
$obCmbNivel->setNull                ( false                                  );

if ($stAcao == 'incluir') {
    $obTxtDescNivel = new TextBox;
    $obTxtDescNivel->setRotulo              ( "Descrição"                     );
    $obTxtDescNivel->setTitle               ( "Informe a descrição do nível." );
    $obTxtDescNivel->setName                ( "stDescricaoNivel"              );
    $obTxtDescNivel->setValue               ( $request->get('stDescricaoNivel'));
    $obTxtDescNivel->setSize                ( 50                     );
    $obTxtDescNivel->setMaxLength           ( 160                      );
    $obTxtDescNivel->setNull                ( false                  );
} else {
    if ( !$obRegra->obRAlmoxarifadoCatalogo->getPermiteManutencao() ) {
        $obLblDescNivel = new Label();
        $obLblDescNivel->setRotulo( 'Descrição' );
        $obLblDescNivel->setValue( $stDescricaoNivel );

        $obHdnDescNivel = new Hidden();
        $obHdnDescNivel->setName( 'hdnDescNivel' );
        $obHdnDescNivel->setValue( $stDescricaoNivel );
    } else {
        $obTxtDescNivel = new TextBox;
        $obTxtDescNivel->setRotulo     ( "Descrição"                    );
        $obTxtDescNivel->setTitle      ( "Informe a descrição do nível.");
        $obTxtDescNivel->setName       ( "stDescricaoNivel"             );
        $obTxtDescNivel->setValue      ( $stDescricaoNivel              );
        $obTxtDescNivel->setSize       ( 50                             );
        $obTxtDescNivel->setMaxLength  ( 160                            );
        $obTxtDescNivel->setNull       ( false                          );
    }
}

$obSpnListaClassificacao = new Span;
$obSpnListaClassificacao->setID('spnListaClassificacao');
$obSpnListaClassificacao->setValue("");

$obSpnListaAtributos = new Span;
$obSpnListaAtributos->setID('spnListaAtributos');
$obSpnListaAtributos->setValue("");

$obSpnCodEstrutural = new Span;
$obSpnCodEstrutural->setID('spnCodEstrutural');
$obSpnCodEstrutural->setValue("");

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm              ($obForm);
$obFormulario->setAjuda             ("UC-03.03.05");
$obFormulario->addHidden            ($obHdnAcao);
$obFormulario->addHidden            ($obHdnCtrl);
$obFormulario->addHidden            ($obHdnCodClassificacao);
$obFormulario->addHidden            ($obHdnCod);
$obFormulario->addHidden            ($obHdnCodN);
$obFormulario->addHidden            ($obHdnCodCombo);
$obFormulario->addHidden            ($obHdnCodNextCombo);
$obFormulario->addHidden            ($obHdnListaInclusao);
$obFormulario->addHidden            ($obHdnValida, true);

$obFormulario->addTitulo            ( "Dados do Catálogo" );

if ($stAcao == 'alterar') {
    $obFormulario->addHidden( $obHdnNivel );
    $obFormulario->addComponente( $obLblCodigo );

    $obFormulario->addHidden( $obHdnCodigoEstrutural );
} else {
    $obFormulario->addComponenteComposto( $obTxtCodCatalogo, $obCmbCodCatalogo);
    $obFormulario->addComponente ($obCmbNivel);
}

$obFormulario->addSpan      ( $obSpnListaClassificacao);
$obFormulario->addSpan      ( $obSpnCodEstrutural);

$obFormulario->addTitulo     ( "Dados do Nível ");

if ($stAcao == 'alterar') {
    $obFormulario->addComponente( $obLblDadoNivel);
    $obFormulario->addComponente( $obLblCodigoNivel);
}
if ($stAcao == 'incluir') {
    $obFormulario->addComponente ( $obTxtDescNivel );
} else {
    if ( !$obRegra->obRAlmoxarifadoCatalogo->getPermiteManutencao() ) {
        $obFormulario->addComponente( $obLblDescNivel );
        $obFormulario->addHidden($obHdnDescNivel);
    } else {
        $obFormulario->addComponente ( $obTxtDescNivel );
    }
}

$obFormulario->addSpan      ( $obSpnListaAtributos);

if ($stAcao=="incluir") {

    $obOk  = new Ok;
    $obOk->setId ("Ok");
    $obOk->obEvento->setOnClick("Salvar(); limpaDescricao();");

    $obLimpar = new Button;
    $obLimpar->setValue( "Limpar" );
    $obLimpar->obEvento->setOnClick( "frm.reset(); frm.inCodCatalogoTxt.focus();" );

    $obFormulario->defineBarra( array( $obOk, $obLimpar ) );

} else {
    $stLocation = $pgList.'?'.Sessao::getId().'&pg='.$_REQUEST['pg'].'&pos='.$_REQUEST['pos']."&stLink=".$_REQUEST['stLink']."&stAcao=".$_REQUEST['stAcao'];
    $obFormulario->Cancelar( $stLocation );
}

$obFormulario->show();

if ($stAcao == 'alterar') {
    echo "<script>BloqueiaFrames(true, false); $('Ok').disabled = true;</script>";
    sistemaLegado::ExecutaFrameOculto("redirecionaPagina( '$pgOcul?".Sessao::getId()."', 'frm' , 'MontaListaClassificacaoAlteracao'  );");
} else {
    echo "<script type='text/javascript'>mudaCatalogo(document.getElementById('inCatalogo'),true);</script>";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
