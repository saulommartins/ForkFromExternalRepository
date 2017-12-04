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
  * Página de Filtro para emissão do relatório de Classificação.
  * Data de criação : 23/07/2008

  * @author Desenvolvedor: Diogo Zarpelon

**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_COMPONENTES."IMontaClassificacao.class.php";
include_once CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeGeral.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioClassificacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OCGera".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

// Inclui os controles em JS da tela.
include($pgJS);

$obForm = new Form;
$obForm->setAction( $pgOcul );

$obITextBoxSelectEntidade = new ITextBoxSelectEntidadeGeral();
$obITextBoxSelectEntidade->setId('inCodEntidade');
$obITextBoxSelectEntidade->setName('inCodEntidade');
$obITextBoxSelectEntidade->setObrigatorio(true);

// Constroi o objeto de Classificação.
$obIMontaClassificacao = new IMontaClassificacao( $obForm );
$obIMontaClassificacao->setNull ( true );

// Define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm          );
$obFormulario->addTitulo     ( "Filtros para Relatório de Classificação");
$obFormulario->addComponente($obITextBoxSelectEntidade);
$obIMontaClassificacao->geraFormulario( $obFormulario );

$obFormulario->OK();
$obFormulario->show();
