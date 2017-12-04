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

    $Id: FMManterApbaaaaTipoBem.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php" );
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioNatureza.class.php"    );

$stPrograma = "ManterApbaaaaTipoBem";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
//$pgJs   = "JS".$stPrograma.".js";

$jsOnload = "executaFuncaoAjax('consultaRegistros');";

Sessao::write('link', "");
$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Consulta das Naturezas
$obNaturezas = new TPatrimonioNatureza();
$obNaturezas->recuperaNatureza($rsNaturezas,'',"ORDER BY nom_natureza");

//Índice da lista
$obTxtCodigo = new TextBox;
$obTxtCodigo->setRotulo          ( "Código da Natureza");
$obTxtCodigo->setName            ( "inCodNatureza"     );
$obTxtCodigo->setId              ( "inCodNatureza"     );
$obTxtCodigo->setObrigatorio     ( true                );
$obTxtCodigo->setSize            ( 6           		   );
$obTxtCodigo->setMaxLength       ( 6           		   );
$obTxtCodigo->setMinLength       ( 6           		   );
$obTxtCodigo->setAlfaNumerico    ( false       		   );

//Definição da lista
$obLista = new Lista();
$obLista->setMostraPaginacao(false);
$obLista->setTitulo('Lista de Naturezas');
$obLista->setRecordSet($rsNaturezas);

//Cabeçalhos
$obLista->addCabecalho('', 5);
$obLista->addCabecalho('Natureza', 80);
$obLista->addCabecalho('Tipo do Bem');
$obLista->addCabecalho('Tipo do Bem Móvel');

//Natureza
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[nom_natureza]');
$obLista->commitDado();

//Tipo do Bem
$obCmbTipoBem = new Select();
$obCmbTipoBem->setName  ( 'inTipoBem' );
$obCmbTipoBem->addOption( '', 'Selecione' );
$obCmbTipoBem->addOption( '01', 'Móvel' );
$obCmbTipoBem->addOption( '02', 'Imóvel' );
$obCmbTipoBem->addOption( '02', 'Natureza Industrial' );
$obCmbTipoBem->setValue ('[valor]');
$obLista->addDadoComponente( $obCmbTipoBem, false );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->ultimoDado->setCampo( 'tipo_bem' );
$obLista->commitDadoComponente();

//Tipo Bem Móvel
$obCmbTipoBemMovel = new Select();
$obCmbTipoBemMovel->setName  (       'inTipoBem' 		  );
$obCmbTipoBemMovel->addOption( '  ', 'Selecione'		  );
$obCmbTipoBemMovel->addOption( '01', 'Aeronaves' 		  );
$obCmbTipoBemMovel->addOption( '02', 'Embarcações'        );
$obCmbTipoBemMovel->addOption( '03', 'Veículos Diversos'  );
$obCmbTipoBemMovel->addOption( '04', 'Maquinário Pesado'  );
$obCmbTipoBemMovel->addOption( '05', 'Outros Bens Móveis' );
$obCmbTipoBemMovel->setValue ('[valor]'					  );
$obCmbTipoBemMovel->setDisabled(true);
$obLista->addDadoComponente( $obCmbTipoBemMovel, false );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->ultimoDado->setCampo( 'tipo_bem' );
$obLista->commitDadoComponente();

//Span
$obSpnNaturezas = new Span();
$obSpnNaturezas->setId('spnNaturezas');
$obLista->montaHTML();
$obSpnNaturezas->setValue($obLista->getHTML());

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm              ($obForm);
$obFormulario->addHidden            ($obHdnAcao);
$obFormulario->addHidden            ($obHdnCtrl);
$obFormulario->addTitulo            ( "Parâmetros por Natureza" );
$obFormulario->addSpan              ($obSpnNaturezas);
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
