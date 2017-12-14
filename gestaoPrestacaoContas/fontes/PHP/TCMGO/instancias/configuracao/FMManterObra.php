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
/*
    * Formulário de Cadastro de Obras
    * Data de Criação   : 16/04/2007

    * @author Analista      Tonismar Bernardo
    * @author Desenvolvedor

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: FMManterObra.php 61522 2015-01-29 18:33:35Z carlos.silva $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/empenho/classes/componentes/IPopUpEmpenho.class.php';
include_once CAM_GA_ADM_COMPONENTES.'ISelectUnidadeMedida.class.php';

$stPrograma = "ManterObra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once ( $pgOcul );

Sessao::write('arEmpenhos', array());

$stAcao = $request->get('stAcao');

if (!$stAcao) {
    $stAcao = 'incluir';
}

if ($stAcao == 'alterar') {

    include_once ( TTGO.'TTGOObras.class.php' );
    $obTTGOObras = new TTGOObras;
    $obTTGOObras->setDado( 'cod_obra', $_GET['cod_obra'] );
    $obTTGOObras->setDado( 'ano_obra', $_GET['ano_obra'] );
    $obTTGOObras->consultar();
    $stDescricao  = trim($obTTGOObras->getDado ( 'especificacao' ));
    $stEndereco   = trim($obTTGOObras->getDado('endereco'));
    $stBairro     = trim($obTTGOObras->getDado('bairro'));
    $inQuantidade = $obTTGOObras->getDado('quantidade');
    $inCodUnidade = $obTTGOObras->getDado('cod_unidade');
    $inCodGrandeza = $obTTGOObras->getDado('cod_grandeza');
    $stFiscal     = trim($obTTGOObras->getDado('fiscal'));

    $inGrauLatitude     = str_pad($obTTGOObras->getDado('grau_latitude'), 2, '0', STR_PAD_LEFT);
    $inMinutoLatitude   = str_pad($obTTGOObras->getDado('minuto_latitude'), 2, '0', STR_PAD_LEFT);
    $arSegundoLatitude  = explode('.', $obTTGOObras->getDado('segundo_latitude'));
    $inSegundoLatitude  = str_pad($arSegundoLatitude[0], 2, '0', STR_PAD_LEFT);
    $inSegundoLatitude .= '.'.str_pad($arSegundoLatitude[1], 2, '0');

    $inGrauLongitude    = str_pad($obTTGOObras->getDado('grau_longitude'), 2, '0', STR_PAD_LEFT);
    $inMinutoLongitude  = str_pad($obTTGOObras->getDado('minuto_longitude'), 2, '0', STR_PAD_LEFT);
    $arSegundoLongitude  = explode('.', $obTTGOObras->getDado('segundo_longitude'));
    $inSegundoLongitude  = str_pad($arSegundoLongitude[0], 2, '0', STR_PAD_LEFT);
    $inSegundoLongitude .= '.'.str_pad($arSegundoLongitude[1], 2, '0');

    // buscando os empenhos da obra
    carregaEmpenhos( $_GET['cod_obra'] , $_GET['ano_obra'] );

}

$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

$obHdnAcao = new Hidden;

$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

if ($stAcao == 'incluir') {
    $obTxtCodObra = new Inteiro;
    $obTxtCodObra->setName   ( 'inCodObra'      );
    $obTxtCodObra->setId     ( 'inCodObra'      );
    $obTxtCodObra->setRotulo ( 'Código da Obra' );
    $obTxtCodObra->setNull   ( false            );
    $obTxtCodObra->setValue  ( $_GET['cod_obra']);
    $obTxtCodObra->setMaxLength ( 4 );;

    $stExercicio = new Exercicio;
    $stExercicio->setValue ( $_GET['ano_obra'] );

} else {

    $obTxtCodObra = new Hidden ;
    $obTxtCodObra->setName  ( 'inCodObra'      );
    $obTxtCodObra->setValue ( $_GET['cod_obra']);

    $stExercicio = new  Hidden ;
    $stExercicio->setName  ( 'stExercicio'    );
    $stExercicio->setValue ( $_GET['ano_obra']);

    $obLblCodObra = new Label;
    $obLblCodObra->setRotulo ( 'Código da Obra' );
    $obLblCodObra->setValue  ( $_GET['ano_obra']);

    $obLblAnoObra = new Label;
    $obLblAnoObra->setRotulo ( 'Exercício' );
    $obLblAnoObra->setValue  ( $_GET['ano_obra']   );
}

$obTxtDescricao = new TextBox;
$obTxtDescricao->setName   ( 'stDescricao' );
$obTxtDescricao->setId     ( 'stDescricao' );
$obTxtDescricao->setRotulo ( 'Descrição'   );
$obTxtDescricao->setNull   ( false         );
$obTxtDescricao->setValue  ( $stDescricao  );
$obTxtDescricao->setSize   ( 100 );
$obTxtDescricao->setMaxLength ( 100 );

$obLblGrauLatitude = new Label;
$obLblGrauLatitude->setRotulo ('Latitude');
$obLblGrauLatitude->setValue  ('Grau');

$obLblMinuto = new Label;
$obLblMinuto->setRotulo ('Minutos');
$obLblMinuto->setValue  ('Minutos');

$obLblSegundo = new Label;
$obLblSegundo->setRotulo ('Segundos');
$obLblSegundo->setValue  ('Segundos');

$obLblGrauLongitude = new Label;
$obLblGrauLongitude->setRotulo ('Longitude');
$obLblGrauLongitude->setValue  ('Grau');

$obTxtGrauLatitude = new TextBox;
$obTxtGrauLatitude->setName   ("inGrauLatitude");
$obTxtGrauLatitude->setTitle  ("Informe a Latitude da Obra" );
$obTxtGrauLatitude->setValue  ($inGrauLatitude);
$obTxtGrauLatitude->setRotulo ("Latitude");
$obTxtGrauLatitude->setInteiro(true);
$obTxtGrauLatitude->setNull   (true);
$obTxtGrauLatitude->setSize(2);
$obTxtGrauLatitude->setMaxLength(2);

$obTxtMinutoLatitude = new TextBox;
$obTxtMinutoLatitude->setName   ("inMinutoLatitude");
$obTxtMinutoLatitude->setTitle  ("Informe a Latitude da Obra" );
$obTxtMinutoLatitude->setValue  ($inMinutoLatitude);
$obTxtMinutoLatitude->setRotulo ("Latitude");
$obTxtMinutoLatitude->setInteiro(true);
$obTxtMinutoLatitude->setNull   (true);
$obTxtMinutoLatitude->setSize(2);
$obTxtMinutoLatitude->setMaxLength(2);

$obTxtSegundoLatitude = new TextBox;
$obTxtSegundoLatitude->setName   ("inSegundoLatitude");
$obTxtSegundoLatitude->setTitle  ("Informe a Latitude da Obra" );
$obTxtSegundoLatitude->setValue  ($inSegundoLatitude);
$obTxtSegundoLatitude->setRotulo ("Latitude");
$obTxtSegundoLatitude->setMascara('99.99');
$obTxtSegundoLatitude->setNull   (true);
$obTxtSegundoLatitude->setSize(5);
$obTxtSegundoLatitude->setMaxLength(5);

$obTxtGrauLongitude = new TextBox;
$obTxtGrauLongitude->setName   ("inGrauLongitude");
$obTxtGrauLongitude->setTitle  ("Informe a Longitude da Obra" );
$obTxtGrauLongitude->setValue  ($inGrauLongitude);
$obTxtGrauLongitude->setRotulo ("Longitude");
$obTxtGrauLongitude->setInteiro(true);
$obTxtGrauLongitude->setNull   (true);
$obTxtGrauLongitude->setSize(2);
$obTxtGrauLongitude->setMaxLength(2);

$obTxtMinutoLongitude = new TextBox;
$obTxtMinutoLongitude->setName   ("inMinutoLongitude");
$obTxtMinutoLongitude->setTitle  ("Informe a Longitude da Obra" );
$obTxtMinutoLongitude->setValue  ($inMinutoLongitude);
$obTxtMinutoLongitude->setRotulo ("Longitude");
$obTxtMinutoLongitude->setInteiro(true);
$obTxtMinutoLongitude->setNull   (true);
$obTxtMinutoLongitude->setSize(2);
$obTxtMinutoLongitude->setMaxLength(2);

$obTxtSegundoLongitude = new TextBox;
$obTxtSegundoLongitude->setName   ("inSegundoLongitude");
$obTxtSegundoLongitude->setTitle  ("Informe a Longitude da Obra" );
$obTxtSegundoLongitude->setValue  ($inSegundoLongitude);
$obTxtSegundoLongitude->setRotulo ("Longitude");
$obTxtSegundoLongitude->setMascara('99.99');
$obTxtSegundoLongitude->setNull   (true);
$obTxtSegundoLongitude->setSize(5);
$obTxtSegundoLongitude->setMaxLength(5);

$obCmbUnidadeMedida = new ISelectUnidadeMedida('2,4');
$obCmbUnidadeMedida->setName('inCodUnidadeMedida');
$obCmbUnidadeMedida->setId  ('inCodUnidadeMedida');
$obCmbUnidadeMedida->setValue($inCodUnidade.'-'.$inCodGrandeza);

$obTxtQuantidade = new TextBox;
$obTxtQuantidade->setName   ("inQuantidade");
$obTxtQuantidade->setTitle  ("Informe a Quantidade da Unidade de Medida" );
$obTxtQuantidade->setValue  ($inQuantidade);
$obTxtQuantidade->setRotulo ("Quantidade");
$obTxtQuantidade->setInteiro(true);
$obTxtQuantidade->setNull   (true);
$obTxtQuantidade->setSize   (5);
$obTxtQuantidade->setMaxLength (5);

$obTxtEndereco = new TextBox;
$obTxtEndereco->setName   ( 'stEndereco' );
$obTxtEndereco->setId     ( 'stEndereco' );
$obTxtEndereco->setRotulo ( 'Endereço'   );
$obTxtEndereco->setValue  ( $stEndereco  );
$obTxtEndereco->setSize   ( 100 );
$obTxtEndereco->setMaxLength ( 100 );

$obTxtBairro = new TextBox;
$obTxtBairro->setName   ( 'stBairro' );
$obTxtBairro->setId     ( 'stBairro' );
$obTxtBairro->setRotulo ( 'Bairro'   );
$obTxtBairro->setValue  ( $stBairro  );
$obTxtBairro->setSize   ( 40 );
$obTxtBairro->setMaxLength ( 40 );

$obTxtFiscal = new TextBox;
$obTxtFiscal->setName   ( 'stFiscal' );
$obTxtFiscal->setId     ( 'stFiscal' );
$obTxtFiscal->setRotulo ( 'Fiscal'   );
$obTxtFiscal->setValue  ( $stFiscal  );
$obTxtFiscal->setSize   ( 50 );
$obTxtFiscal->setMaxLength ( 50 );

//$obExercicioEmpenho = new Exercicio;
//$obExercicioEmpenho->setObrigatorioBarra ( true );
//$obExercicioEmpenho->setName ( 'stExercicioEmpenho' );
//$obExercicioEmpenho->setId   ( 'stExercicioEmpenho' );
//$obExercicioEmpenho->setNull ( true );

// Define Objeto BuscaInner para Empenho
$obIPopUpEmpenho = new IPopUpEmpenho( $obForm );
$obIPopUpEmpenho->setObrigatorioBarra ( true         );
$obIPopUpEmpenho->setTipoBusca        ( 'obra_tcmgo' );

$arInclusao = array();
$arInclusao[] = $obIPopUpEmpenho;

$spnEmpenhos = new Span;
$spnEmpenhos->setId  ( 'spnEmpenho' );

$obFormulario = new Formulario();
$obFormulario->addForm              ( $obForm );

$obFormulario->addHidden            ( $obHdnAcao       );
if ($stAcao == 'alterar') {
    $obFormulario->addHidden ($obTxtCodObra) ;
    $obFormulario->addHidden ($stExercicio           );
    $obFormulario->addComponente ( $obLblCodObra     );
    $obFormulario->addComponente ( $obLblAnoObra     );
} else {
    $obFormulario->addComponente ( $obTxtCodObra          );
    $obFormulario->addComponente ( $stExercicio           );
}

//$obExercicioEmpenho->setNull ( true );
$obFormulario->addComponente ( $obTxtDescricao               );
$obFormulario->agrupaComponentes(array($obLblGrauLatitude, $obTxtGrauLatitude, $obLblMinuto, $obTxtMinutoLatitude, $obLblSegundo, $obTxtSegundoLatitude));
$obFormulario->agrupaComponentes(array($obLblGrauLongitude, $obTxtGrauLongitude, $obLblMinuto, $obTxtMinutoLongitude, $obLblSegundo, $obTxtSegundoLongitude));
$obFormulario->addComponente ($obCmbUnidadeMedida);
$obFormulario->addComponente ($obTxtQuantidade);
$obFormulario->addComponente ( $obTxtEndereco               );
$obFormulario->addComponente ( $obTxtBairro                 );
$obFormulario->addComponente ( $obTxtFiscal                 );
$obFormulario->addTitulo     ( 'Dados dos empenhos da obra.' );
//$obFormulario->addComponente ( $obExercicioEmpenho           );

$obIPopUpEmpenho->geraFormulario($obFormulario);
$obFormulario->Incluir( 'Empenho', $arInclusao,  true );
$obFormulario->addSpan ( $spnEmpenhos );

if ($stAcao == 'incluir') {
    $obFormulario->ok();
} else {
    $obFormulario->Cancelar($pgList.'?'.Sessao::getId().'&stAcao='.$stAcao );
}

$obFormulario->show();


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

$stJs .= listaEmpenhos  (  );
sistemaLegado::executaFrameOculto ( $stJs );
