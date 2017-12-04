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
  * Página de Filtro para emissão do relatório Termo de Responsabilidade
  * Data de criação : 12/08/2008

  $Id: termoResponsabilidade.php 61409 2015-01-14 18:21:07Z diogo.zarpelon $

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrganograma.class.php";
include_once CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php";
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganogramaLocal.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "termoResponsabilidade";
$pgFilt     = $stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OCGera".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

// Inclui os controles em JS da tela.
include($pgJS);

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgOcul);

# Recupera o Organograma Ativo no sistema.
$obTOrganogramaOrganograma = new TOrganogramaOrganograma;
$obTOrganogramaOrganograma->setDado('ativo', true);
$obTOrganogramaOrganograma->recuperaOrganogramasAtivo($rsOrganogramaAtivo);

$inCodOrganogramaAtivo = $rsOrganogramaAtivo->getCampo('cod_organograma');

$obHdnOrganogramaAtivo = new Hidden;
$obHdnOrganogramaAtivo->setName ("inCodOrganogramaAtivo" );
$obHdnOrganogramaAtivo->setValue($inCodOrganogramaAtivo);

//instancia o componente IPopUpCGMVinculado para o responsavel
$obIPopUpCGMVinculadoResponsavel = new IPopUpCGMVinculado( $obForm );
$obIPopUpCGMVinculadoResponsavel->setTabelaVinculo    ( 'patrimonio.bem_responsavel'   );
$obIPopUpCGMVinculadoResponsavel->setCampoVinculo     ( 'numcgm'                 );
$obIPopUpCGMVinculadoResponsavel->setNomeVinculo      ( 'Responsavel'            );
$obIPopUpCGMVinculadoResponsavel->setRotulo           ( 'CGM Responsável'        );
$obIPopUpCGMVinculadoResponsavel->setTitle            ( 'Informe o responsável'  );
$obIPopUpCGMVinculadoResponsavel->setName             ( 'stNomResponsavel'       );
$obIPopUpCGMVinculadoResponsavel->setId               ( 'stNomResponsavel'       );
$obIPopUpCGMVinculadoResponsavel->obCampoCod->setName ( 'inNumResponsavel'       );
$obIPopUpCGMVinculadoResponsavel->obCampoCod->setId   ( 'inNumResponsavel'       );
$obIPopUpCGMVinculadoResponsavel->setNull             (  false                   );

$obChkValor = new Checkbox;
$obChkValor->setName    ( 'demo_valor'   );
$obChkValor->setId      ( 'demo_valor'   );
$obChkValor->setRotulo  ( 'Demonstrar Valor'       );

include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");
$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
$obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
$obTAdministracaoConfiguracao->setDado( 'cod_modulo', 2 );
$obTAdministracaoConfiguracao->pegaConfiguracao( $cnpjCNM, 'cnpj' );

if ($cnpjCNM == '00703157000183') {
    $obChkValor->setChecked ( false          );
    $obChkValor->setValue   (0);
} else {
    $obChkValor->setChecked ( true          );
    $obChkValor->setValue   (1);
}

# Filtros de Organograma / Localização
$obIMontaOrganograma = new IMontaOrganograma(false);
$obIMontaOrganograma->setStyle('width:250px');

$obIMontaOrganogramaLocal = new IMontaOrganogramaLocal;
$obIMontaOrganogramaLocal->setValue($codLocal);

// Define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm          );
$obFormulario->addTitulo     ( "Dados para o Filtro");
$obFormulario->addComponente ( $obIPopUpCGMVinculadoResponsavel );
$obFormulario->addComponente ( $obChkValor );
$obFormulario->addHidden    ( $obHdnOrganogramaAtivo );

$obFormulario->addTitulo    ( "Localização"   );
$obIMontaOrganograma->geraFormulario( $obFormulario );
$obIMontaOrganogramaLocal->geraFormulario( $obFormulario );

$obFormulario->OK();
$obFormulario->show();

?>
