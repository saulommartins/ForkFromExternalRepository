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
    * Página de Formulário
    * Data de Criação   : 15/04/2008

    * @author Diego Barbosa Victoria

    * @ignore

    * Casos de uso : uc-06.03.00

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TPB_MAPEAMENTO."TTPBObras.class.php");
include_once(CAM_GPC_TPB_MAPEAMENTO."TTPBTipoCategoriaObra.class.php");
include_once(CAM_GPC_TPB_MAPEAMENTO."TTPBTipoFonteObras.class.php");
include_once(CAM_GPC_TPB_MAPEAMENTO."TTPBTipoObra.class.php");
include_once(CAM_GPC_TPB_MAPEAMENTO."TTPBTipoSituacao.class.php");

$stPrograma = "ManterObras";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if (empty( $stAcao )) {
    $stAcao = "alterar";
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

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName( "exercicio" );
$obHdnExercicio->setValue( $_REQUEST['exercicio'] );

if ($stAcao == 'alterar') {
    $obTObras = new TTPBObras();
    $obTObras->setDado('num_obra',$_REQUEST['num_obra']);
    $obTObras->setDado('exercicio',$_REQUEST['exercicio']);
    
    $obTObras->recuperaPorChave($rsObras);

    $inNumero            = $rsObras->getCampo('num_obra');
    $stDataCadastro      = $rsObras->getCampo('dt_cadastro');
    $boPatrimonio        = $rsObras->getCampo('patrimonio');
    $stLocalidade        = $rsObras->getCampo('localidade');
    $stDescricao         = $rsObras->getCampo('descricao');
    $inCodTipoObra       = $rsObras->getCampo('cod_tipo_obra');
    $inCodCategoriaObra  = $rsObras->getCampo('cod_tipo_categoria');
    $inCodFonteObra      = $rsObras->getCampo('cod_tipo_fonte');
    $stMesAno            = $rsObras->getCampo('mes_ano_estimado_fim');
    $inMesEstimado       = substr($stMesAno,0,2);
    $inAnoEstimado       = substr($stMesAno,2,4);
    $stDataInicio        = $rsObras->getCampo('dt_inicio');
    $stDataConclusao     = $rsObras->getCampo('dt_conclusao');
    $stDataRecebimento   = $rsObras->getCampo('dt_recebimento');
    $inCodSituacao       = $rsObras->getCampo('cod_tipo_situacao');
    $vlOrcado            = number_format($rsObras->getCampo('vl_obra'), 2, ",", ".");
} elseif ($stAcao == 'incluir') {
    $obMapeamento  = new TTPBObras();
    $obMapeamento->proximoCod($inNumero);
}

$obTTipoCategoriaObra   = new TTPBTipoCategoriaObra();
$obTTipoCategoriaObra->recuperaTodos($rsTipoCategoriaObra," WHERE exercicio='".Sessao::getExercicio()."' ORDER BY cod_tipo");

$obTTipoFonteObras      = new TTPBTipoFonteObras();
$obTTipoFonteObras->recuperaTodos($rsTipoFonteObras, " WHERE exercicio='".Sessao::getExercicio()."' ORDER BY cod_tipo");

$obTTipoObra            = new TTPBTipoObra();
$obTTipoObra->recuperaTodos($rsTipoObra, " WHERE exercicio='".Sessao::getExercicio()."'  ORDER BY cod_tipo");

$obTTipoSituacao        = new TTPBTipoSituacao();
$obTTipoSituacao->recuperaTodos($rsTipoSituacao, " WHERE exercicio='".Sessao::getExercicio()."'  ORDER BY cod_tipo");

//Arquivo ObraCadastro

$obNumero = new Inteiro();
$obNumero->setRotulo('Número da Obra');
$obNumero->setName  ('inNumero');
$obNumero->setId    ('inNumero');
$obNumero->setValue ($inNumero);
$obNumero->setNull  ( false );
$obNumero->setMaxLength( 4 );
$obNumero->setSize  ( 5 );
$obNumero->setReadOnly ( ($stAcao=='alterar') );

$obDataCadastro = new Data();
$obDataCadastro->setRotulo( 'Data de Cadastro da Obra');
$obDataCadastro->setName    ('stDataCadastro');
$obDataCadastro->setId      ('stDataCadastro');
$obDataCadastro->setValue   ($stDataCadastro);
$obDataCadastro->setNull    ( false );

$obPatrimonio = new SimNao();
$obPatrimonio->setRotulo( 'Obra incorporável ao Patrimônio');
$obPatrimonio->setName  ('boPatrimonio');
$obPatrimonio->setChecked  ( $boPatrimonio );

$obLocalidade = new TextBox();
$obLocalidade->setRotulo('Localidade');
$obLocalidade->setName  ('stLocalidade');
$obLocalidade->setId    ('stLocalidade');
$obLocalidade->setValue ($stLocalidade);
$obLocalidade->setNull  ( false );
$obLocalidade->setMaxLength( 150 );
$obLocalidade->setSize  ( 50 );

$obDescricao = new TextArea;
$obDescricao->setRotulo ( "Descrição Sucinta" );
$obDescricao->setName   ( "stDescricao" );
$obDescricao->setId     ( "stDescricao" );
$obDescricao->setValue  ( $stDescricao  );
$obDescricao->setNull   ( false );
$obDescricao->setRows   ( 3 );

$obTipoObra = new Select;
$obTipoObra->setRotulo     ( "Tipo de Obra"                 );
$obTipoObra->setTitle      ( "Selecione o tipo de obra."    );
$obTipoObra->setName       ( "inCodTipoObra"                    );
$obTipoObra->setId         ( "inCodTipoObra"                    );
$obTipoObra->setCampoID    ( "cod_tipo"                     );
$obTipoObra->setCampoDesc  ( "descricao"                    );
$obTipoObra->addOption     ( "", "Selecione"                );
$obTipoObra->preencheCombo ( $rsTipoObra                    );
$obTipoObra->setStyle      ( "width: 200px"                 );
$obTipoObra->setNull       ( false );
$obTipoObra->setValue      ( $inCodTipoObra                     );

$obCategoriaObra = new Select;
$obCategoriaObra->setRotulo     ( "Categoria"                 );
$obCategoriaObra->setTitle      ( "Selecione a categoria da obra."    );
$obCategoriaObra->setName       ( "inCodCategoriaObra"                    );
$obCategoriaObra->setId         ( "inCodCategoriaObra"                    );
$obCategoriaObra->setCampoID    ( "cod_tipo"                     );
$obCategoriaObra->setCampoDesc  ( "descricao"                    );
$obCategoriaObra->addOption     ( "", "Selecione"                );
$obCategoriaObra->preencheCombo ( $rsTipoCategoriaObra                    );
$obCategoriaObra->setStyle      ( "width: 200px"                 );
$obCategoriaObra->setNull       ( false );
$obCategoriaObra->setValue      ( $inCodCategoriaObra                     );

$obFonteObra = new Select;
$obFonteObra->setRotulo     ( "Fonte de Recursos"                 );
$obFonteObra->setTitle      ( "Selecione a fonte de recursos da obra."    );
$obFonteObra->setName       ( "inCodFonteObra"                    );
$obFonteObra->setId         ( "inCodFonteObra"                    );
$obFonteObra->setCampoID    ( "cod_tipo"                     );
$obFonteObra->setCampoDesc  ( "descricao"                    );
$obFonteObra->addOption     ( "", "Selecione"                );
$obFonteObra->preencheCombo ( $rsTipoFonteObras                    );
$obFonteObra->setStyle      ( "width: 200px"                 );
$obFonteObra->setNull       ( false );
$obFonteObra->setValue      ( $inCodFonteObra                     );

$obVlOrcado = new Numerico;
$obVlOrcado->setName       ( 'vlOrcado'                         );
$obVlOrcado->setId         ( 'vlOrcado'                         );
$obVlOrcado->setMaxLength  ( 10                                 );
$obVlOrcado->setSize       ( 20                                 );
$obVlOrcado->setRotulo     ( 'Valor Orçado'                     );
$obVlOrcado->setTitle      ( 'Informe o Valor Orçado da Obra'   );
$obVlOrcado->setNull       ( true                               );
$obVlOrcado->setValue      ( $vlOrcado                          );

//Arquivo ObraInicio

$obMesEstimado = new SelectMeses();
$obMesEstimado->setRotulo     ( "Mes e ano estimados para conclusão"       );
$obMesEstimado->setTitle      ( "Mes e ano estimados para conclusão da obra."    );
$obMesEstimado->setName       ( "inMesEstimado"                    );
$obMesEstimado->setId         ( "inMesEstimado"                    );
$obMesEstimado->setStyle      ( "width: 200px"                 );
$obMesEstimado->setValue      ( $inMesEstimado                     );

$obAnoEstimado = new Exercicio();
$obAnoEstimado->setName       ( "inAnoEstimado"                    );
$obAnoEstimado->setId         ( "inAnoEstimado"                    );
$obAnoEstimado->setValue      ( $inAnoEstimado                     );

$obDataInicio = new Data();
$obDataInicio->setRotulo( 'Data de Início da Obra');
$obDataInicio->setName    ('stDataInicio');
$obDataInicio->setId      ('stDataInicio');
$obDataInicio->setValue   ($stDataInicio);

//Arquivo ObraConclusao

$obDataConclusao = new Data();
$obDataConclusao->setRotulo( 'Data de Conclusão da Obra');
$obDataConclusao->setName    ('stDataConclusao');
$obDataConclusao->setId      ('stDataConclusao');
$obDataConclusao->setValue   ($stDataConclusao);

$obDataRecebimento = new Data();
$obDataRecebimento->setRotulo( 'Data de Recebimento da Obra');
$obDataRecebimento->setName    ('stDataRecebimento');
$obDataRecebimento->setId      ('stDataRecebimento');
$obDataRecebimento->setValue   ($stDataRecebimento);

//Arquivo ObraConclusao

$obSituacao = new Select;
$obSituacao->setRotulo     ( "Situação da Obra"                 );
$obSituacao->setTitle      ( "Selecione a situação da obra."    );
$obSituacao->setName       ( "inCodSituacao"                    );
$obSituacao->setId         ( "inCodSituacao"                    );
$obSituacao->setCampoID    ( "cod_tipo"                     );
$obSituacao->setCampoDesc  ( "descricao"                    );
$obSituacao->addOption     ( "", "Selecione"                );
$obSituacao->preencheCombo ( $rsTipoSituacao                    );
$obSituacao->setStyle      ( "width: 200px"                 );
$obSituacao->setValue      ( $inCodSituacao                     );

$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden ($obHdnCtrl );
$obFormulario->addHidden ($obHdnAcao );
$obFormulario->addHidden ( $obHdnExercicio );
$obFormulario->addTitulo  ( "Arquivo ObraCadastro" );
$obFormulario->addComponente( $obNumero );
$obFormulario->addComponente( $obDataCadastro );
$obFormulario->addComponente( $obPatrimonio );
$obFormulario->addComponente( $obLocalidade );
$obFormulario->addComponente( $obDescricao );
$obFormulario->addComponente( $obTipoObra );
$obFormulario->addComponente( $obCategoriaObra );
$obFormulario->addComponente( $obFonteObra );
$obFormulario->addComponente( $obVlOrcado );
$obFormulario->addTitulo  ( "Arquivo ObraInicio" );
$obFormulario->agrupaComponentes( array( $obMesEstimado , $obAnoEstimado ) );
$obFormulario->addComponente( $obDataInicio );
$obFormulario->addTitulo  ( "Arquivo ObraConclusao" );
$obFormulario->addComponente( $obDataConclusao );
$obFormulario->addComponente( $obDataRecebimento );
$obFormulario->addTitulo  ( "Arquivo ObraSituacao" );
$obFormulario->addComponente( $obSituacao );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
