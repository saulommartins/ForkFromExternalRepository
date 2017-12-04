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
  * Página de Filtro para emissão do relatório de Apólices de Seguros
  * Data de criação : 18/07/2008

  * @author Desenvolvedor: Diogo Zarpelon

**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );
include_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php";


//Define o nome dos arquivos PHP
$stPrograma = "ApoliceSeguros";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OCGeraRelatorio".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

// Inclui os controles em JS da tela.
include($pgJS);

$obForm = new Form;
$obForm->setAction( $pgOcul );

// Define Objeto TextBox para o código da apólice.
$obTxtCodApolice = new TextBox;
$obTxtCodApolice->setRotulo ( "Código da Apólice"            );
$obTxtCodApolice->setTitle  ( "Informe o código da apólice." );
$obTxtCodApolice->setName   ( "inCodApolice"                 );
$obTxtCodApolice->setSize   ( 20                             );

// Define Objeto TextBox para o número da apólice.
$obTxtNumApolice = new TextBox;
$obTxtNumApolice->setRotulo ( "Número da Apólice"            );
$obTxtNumApolice->setTitle  ( "Informe o número da apólice." );
$obTxtNumApolice->setName   ( "inNumApolice"                 );
$obTxtNumApolice->setSize   ( 20                             );

// Define o objeto Select para a escolha da seguradora.
$obIPopUpCGM = new IPopUpCGMVinculado( $obForm );
$obIPopUpCGM->setRotulo           ( 'Seguradora'             );
$obIPopUpCGM->setTitle            ( 'Informe a seguradora.'  );
$obIPopUpCGM->setTabelaVinculo    ( 'sw_cgm_pessoa_juridica' );
$obIPopUpCGM->setCampoVinculo     ( 'numcgm'                 );
$obIPopUpCGM->setNomeVinculo      ( 'seguradora'             );
$obIPopUpCGM->setName             ( 'stNomCGM'               );
$obIPopUpCGM->setId               ( 'stNomCGM'               );
$obIPopUpCGM->obCampoCod->setName ( 'inNumCGM'               );
$obIPopUpCGM->obCampoCod->setId   ( 'inNumCGM'               );
$obIPopUpCGM->setNull             ( true                     );

// Define o objeto Select para setar a ordenação do relatório.
$obCmOrderBy = new Select();
$obCmOrderBy->setName     ( "stOrdenacao"                            );
$obCmOrderBy->setRotulo   ( "Ordenar por"                            );
$obCmOrderBy->setStyle    ( "width: 150px;"                          );
$obCmOrderBy->setTitle    ( "Selecione a ordenação do relatório."    );
$obCmOrderBy->addOption   ( "a.num_apolice", "Apólice"               );
$obCmOrderBy->addOption   ( "a.cod_apolice", "Código da Apólice"     );
$obCmOrderBy->addOption   ( "ab.cod_bem", "Código do Bem"            );
$obCmOrderBy->addOption   ( "a.dt_vencimento", "Data de Vencimento"  );
$obCmOrderBy->addOption   ( "b.descricao", "Descrição"               );
$obCmOrderBy->addOption   ( "e.nom_especie", "Espécie"               );
$obCmOrderBy->addOption   ( "c.nom_cgm", "Seguradora"                );

$obISelectEntidade = new ITextBoxSelectEntidadeUsuario();
$obISelectEntidade->obTextBox->setNull(false);
$obISelectEntidade->setNull(false);

// Define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm             );
$obFormulario->addTitulo     ( "Filtros para Apólice de Seguros ");
$obFormulario->addComponente ( $obISelectEntidade  );
$obFormulario->addComponente ( $obTxtCodApolice    );
$obFormulario->addComponente ( $obTxtNumApolice    );
$obFormulario->addComponente ( $obIPopUpCGM        );
$obFormulario->addComponente ( $obCmOrderBy        );

$obFormulario->OK();
$obFormulario->show();
