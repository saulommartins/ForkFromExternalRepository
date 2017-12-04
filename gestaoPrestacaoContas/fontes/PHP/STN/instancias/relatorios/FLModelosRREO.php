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

    * Página de Filtro para Relatório de MODELOS
    * Data de Criação   : 24/01/2006

    * @author Fernando Zank Correa Evangelista

    * @ignore

    * Casos de uso : uc-06.02.11
                     uc-06.02.12
                     uc-06.02.13
                     uc-06.02.15
                     uc-06.02.17
                     uc-06.02.18

    $Id: FLModelosRREO.php 66677 2016-11-01 19:38:55Z carlos.silva $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_ORC_COMPONENTES  . 'ISelectMultiploEntidadeUsuario.class.php';
include_once CAM_GRH_PES_COMPONENTES . 'IFiltroCompetencia.class.php';
include_once CAM_FW_COMPONENTES      . 'HTML/Bimestre.class.php';
include_once CAM_GA_ADM_MAPEAMENTO   . 'TAdministracaoConfiguracao.class.php';
include_once CAM_GA_ADM_COMPONENTES  . 'IMontaAssinaturas.class.php';
include_once CAM_GF_ORC_COMPONENTES  . 'ISelectMultiploEntidadeGeral.class.php';

Sessao::remove('arValores');
Sessao::remove('filtroRelatorio');

$pgOcul = 'OCModelosRREO.php';

$jsOnLoad = isset($jsOnLoad) ? $jsOnLoad : '';
$stJs     = isset($stJs)     ? $stJs     : '';

$stAcao = $request->get('stAcao');

$obForm = new Form;

$obForm->setTarget ( 'telaPrincipal' );
switch ($stAcao) {
case 'anexo1':
case 'anexo1novo':
    $pgGera = 'OCGeraRREOAnexo1.php';
    break;
case 'anexo2':
case 'anexo2novo':
    $pgGera = 'OCGeraRREOAnexo2.php';
    break;
case 'anexo3':
case 'anexo3novo':
    $pgGera = 'OCGeraRREOAnexo3.php';
    break;
case 'anexo5':
case 'anexo4novo':
    $pgGera = 'OCGeraRREOAnexo5.php';
break;
case 'anexo6':
case 'anexo5novo':
    $pgGera = 'OCGeraRREOAnexo6.php';
    break;
case 'anexo7':
case 'anexo6novo':
    $pgGera = 'OCGeraRREOAnexo7.php';
    break;
case 'anexo9':
case 'anexo7novo':
    $pgGera = 'OCGeraRREOAnexo9.php';
    break;
case 'anexo10':
    $pgGera = 'OCGeraRREOAnexo10.php';
    break;
case 'anexo8novo':
    $pgGera = 'OCGeraRREOAnexo8.php';
    break;
case 'anexo11':
case 'anexo9novo':
    $pgGera = 'OCGeraRREOAnexo11.php';
    break;
case 'anexo14':
case 'anexo11novo':
    $pgGera = 'OCGeraRREOAnexo14.php';
    break;
case 'anexo16':
case 'anexo12novo':
    $pgGera = 'OCGeraRREOAnexo16.php';
    break;
case 'anexo18':
case 'anexo14novo':
    $pgGera = 'OCGeraRREOAnexo18.php';
    break;
default:
    $pgGera = CAM_FW_POPUPS . 'relatorio/OCRelatorio.php';
    $obForm->setTarget('oculto' );
}

$obForm->setAction($pgGera);

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnPgGera = new Hidden;
$obHdnPgGera->setName ( "pgGera" );
$obHdnPgGera->setValue( $pgGera );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue(CAM_GPC_STN_INSTANCIAS . 'relatorios/OCModelosRREO.php?pgGera=' . $pgGera . '&' . Sessao::getId());

$obCmbTipoRelatorio = new Select;
$obCmbTipoRelatorio->setRotulo('Tipo Relatorio');
$obCmbTipoRelatorio->setName  ('stTipoRelatorio');
$obCmbTipoRelatorio->addOption('', 'Selecione');

//Se estado for Minas Gerais
if((SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 11) && (Sessao::getExercicio() >= 2014)) {
    switch ($_REQUEST['stAcao']) {
        case 'anexo12novo':
        case 'anexo1novo':
        case 'anexo2novo':
        case 'anexo4novo':
        case 'anexo5novo':
        case 'anexo6novo':
        case 'anexo7novo':
        case 'anexo8novo':
        case 'anexo9novo':
            $obCmbTipoRelatorio->addOption('Mes', 'Mês');
        break;
    }
}

$obCmbTipoRelatorio->addOption('Bimestre', 'Bimestre');
$obCmbTipoRelatorio->setNull  (false);
$obCmbTipoRelatorio->setStyle ('width: 220px');
$obCmbTipoRelatorio->obEvento->setOnChange("montaParametrosGET( 'preencheSpan' );");

$obSpnTipoRelatorio = new Span();
$obSpnTipoRelatorio->setId('spnTipoRelatorio');

$obISelectEntidade = new ISelectMultiploEntidadeUsuario();
$obISelectEntidade->SetNomeLista2("inNumCGM");

include_once CAM_GF_ORC_COMPONENTES . 'IMontaRecursoDestinacao.class.php';
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro          (true);
$obIMontaRecursoDestinacao->setObrigatorioBarra(true);

// Define Objeto Button para Incluir Item na lista
$obBtnIncluir = new Button;
$obBtnIncluir->setValue            ("Incluir");
$obBtnIncluir->obEvento->setOnClick("montaParametrosGET('incluirLista','inCodRecurso,stDescricaoRecurso');frm.inCodRecurso.focus()" );

//Span da Listagem de itens
$obSpnLista = new Span;
$obSpnLista->setID("spnLista");

//Cria um campo valor para o quando for o anexo 6
//anexo6novo é o antigo anexo7
if ($stAcao == 'anexo5novo' || $stAcao == 'anexo6' || $stAcao == 'anexo7' || $stAcao == 'anexo6novo') {
    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
    $obTAdministracaoConfiguracao->setDado('exercicio',Sessao::getExercicio());
    $obTAdministracaoConfiguracao->setDado('cod_modulo',36);
    if ($stAcao == 'anexo5novo' || $stAcao == 'anexo6') {
      $obTAdministracaoConfiguracao->setDado('parametro','meta_resultado_nominal_fixada');
      //anexo6novo é o antigo anexo7
    } elseif ($stAcao == 'anexo7' || $stAcao == 'anexo6novo') {
      $obTAdministracaoConfiguracao->setDado('parametro','meta_resultado_primario_fixada');
    }

    $obTAdministracaoConfiguracao->recuperaPorChave($rsAdministracaoConfiguracao);

    $obValorMetaFixada =  new TextBox;
    $obValorMetaFixada->setName ( "inVlMetaFixada");

    if ($stAcao == 'anexo5novo' || $stAcao == 'anexo6') {
      $obValorMetaFixada->setRotulo( 'Meta de Resultado Nominal Fixada' );
      $obValorMetaFixada->setTitle( 'Informe a meta de resultado nominal fixada' );
      //anexo6novo é o antigo anexo7
    } elseif ($stAcao == 'anexo7' || $stAcao == 'anexo6novo') {
      $obValorMetaFixada->setRotulo( 'Meta de Resultado Primário Fixada' );
      $obValorMetaFixada->setTitle( 'Informe a meta de resultado primário fixada' );
    }
    $obValorMetaFixada->setNull ( false );
    $obValorMetaFixada->setSize ( 13 );
    $obValorMetaFixada->setMaxLength( 50 );
    $obValorMetaFixada->setId   ( "" );
    $obValorMetaFixada->obEvento->setOnKeyUp("mascaraMoeda(this,2,event,true);");
    $obValorMetaFixada->obEvento->setOnBlur ("floatDecimal(this, '2', event );");
    $obValorMetaFixada->setValue( $rsAdministracaoConfiguracao->getCampo('valor') );
}

// Instanciação do objeto Lista de Assinaturas
// Limpa papeis das Assinaturas na Sessão
$arAssinaturas = Sessao::read('assinaturas');
$arAssinaturas['papeis'] = array();
Sessao::write('assinaturas',$arAssinaturas);
Sessao::write('relatorio', $stAcao);

$obMontaAssinaturas = new IMontaAssinaturas;

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnPgGera );
if ($stAcao == 'anexo14' || $stAcao == 'anexo11novo') {
    $obFormulario->addHidden( $obHdnCaminho );
}
$obFormulario->addTitulo( "Dados para o filtro" );

$obFormulario->addComponente($obISelectEntidade );
$obFormulario->addComponente($obCmbTipoRelatorio);
$obFormulario->addSpan      ($obSpnTipoRelatorio);

$obMontaAssinaturas->setEventosCmbEntidades($obISelectEntidade);
$obISelectEntidade->setNomeLista2('inCodEntidade');

if ($stAcao == 'anexo5novo' || $stAcao == 'anexo6' || $stAcao == 'anexo7' || $stAcao == 'anexo6novo') {
    $obFormulario->addComponente( $obValorMetaFixada );
}

if (($stAcao == 'anexo10') OR ($stAcao == 'anexo8novo') OR ($stAcao == 'anexo16')) {
    if ($stAcao == 'anexo16') {
        $stDescricao = 'Saúde';
    } else {
        $stDescricao = 'Educação';
    }

    $obPct = new TextBox;
    $obPct->setName             ("flPct");
    $obPct->setRotulo           ('Porcentagem aplicada dos Recursos para a ' . $stDescricao);
    $obPct->setTitle            ('Informe a porcentagem aplicada dos recursos para a '.$stDescricao );
    $obPct->setNull             (false);
    $obPct->setSize             (10 );
    $obPct->setMaxLength        ( 5 );
    $obPct->setId               ("");
    $obPct->obEvento->setOnKeyUp("mascaraMoeda(this,2,event,true);");
    $obPct->obEvento->setOnBlur ("floatDecimal(this, '2', event );");
    if ($stAcao == 'anexo16') {
        $stValue = SistemaLegado::pegaDado('valor','administracao.configuracao',"WHERE cod_modulo = 36 AND exercicio = '".Sessao::getExercicio()."' AND parametro = 'stn_anexo16_porcentagem' ");
        $stValue = number_format( (float) $stValue,2,',','.');
    } else {
        $stValue = SistemaLegado::pegaDado('valor','administracao.configuracao',"WHERE cod_modulo = 36 AND exercicio = '".Sessao::getExercicio()."' AND parametro = 'stn_anexo10_porcentagem' ");
        $stValue = number_format( (float) $stValue,2,',','.');
    }
    $obPct->setValue            ($stValue);

    $obFormulario->addComponente($obPct);
}

$obMontaAssinaturas->geraFormulario($obFormulario);

if ($stAcao == 'anexo1' OR $stAcao == 'anexo18' OR $stAcao == 'anexo14novo') {

    $obOk  = new Ok;
    $obOk->setId ("Ok");
    $obOk->obEvento->setOnClick("montaParametrosGET('ValidaEntidade');");

    $obLimpar = new Button;
    $obLimpar->setValue( "Limpar" );
    $obLimpar->obEvento->setOnClick( "frm.reset();" );

    $obFormulario->defineBarra( array( $obOk, $obLimpar ) );

} elseif ($stAcao == 'anexo14' OR $stAcao == 'anexo11novo') {

    $obIMontaRecursoDestinacao->geraFormulario($obFormulario);
    $obFormulario->addComponente($obBtnIncluir);
    $obFormulario->addSpan      ($obSpnLista  );

    $obOk  = new Ok;
    $obOk->setId ("Ok");
    $obOk->obEvento->setOnClick("if ( Valida() ) {montaParametrosGET('Valida');}");

    $obLimpar = new Button;
    $obLimpar->setValue( "Limpar" );
    $obLimpar->obEvento->setOnClick( "frm.reset();" );

    $obFormulario->defineBarra( array( $obOk, $obLimpar ) );
    $jsOnLoad = "montaParametrosGET('montaLista');";
} else {
    $obFormulario->Ok();
}

$obFormulario->show();

$jsOnLoad .= $stJs;
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';