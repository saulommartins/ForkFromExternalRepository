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
    * Arquivo de Filtro da Entrada por Ordem de Compra
    * Data de Criação: 12/07/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    $Id: FLMovimentacaoOrdemCompra.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php";
include_once CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "MovimentacaoOrdemCompra";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";

//Define a função do arquivo, ex: excluir ou alterar
$stAcao = $request->get('stAcao');

Sessao::write('link' , '');

//Instancia o formulário
$obForm = new Form;
$obForm->setAction   ( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

// campo exercício
$obTxtExercicio = new TextBox;
$obTxtExercicio->setRotulo   ( "Exercício"                  );
$obTxtExercicio->setTitle    ( "Informe o exercício." 		);
$obTxtExercicio->setName     ( "stExercicio"                );
$obTxtExercicio->setId       ( "stExercicio"                );
$obTxtExercicio->setValue    ( Sessao::getExercicio()       );
$obTxtExercicio->setSize     ( 4                            );
$obTxtExercicio->setMaxLength( 4                            );
$obTxtExercicio->setInteiro  ( true                         );

// instanciando o componente ISelectMultiploEntidadeUsuario
$obISelectEntidade = new ISelectMultiploEntidadeUsuario;
$obISelectEntidade->setNull( true );

$obISelectEntidade->SetNomeLista2 ( 'inCodEntidade' );
$obISelectEntidade->setCampoId2   ( 'cod_entidade'  );
$obISelectEntidade->setCampoDesc2 ( 'nom_cgm'       );

// campo ordem de compra
$obTxtOrdemCompra = new TextBox;
$obTxtOrdemCompra->setRotulo	( 'Ordem de Compra' );
$obTxtOrdemCompra->setTitle		( 'Informe o número da Ordem de Compra' );
$obTxtOrdemCompra->setName		( 'inOrdemCompra' );
$obTxtOrdemCompra->setId		( $obTxtOrdemCompra->getName() );
$obTxtOrdemCompra->setSize		( 6 );
$obTxtOrdemCompra->setInteiro	( true );

// campo fornecedor
$obIPopUpFornecedor = new IPopUpCGMVinculado( $obForm                 );
$obIPopUpFornecedor->setTabelaVinculo       ( 'compras.fornecedor'    );
$obIPopUpFornecedor->setCampoVinculo        ( 'cgm_fornecedor'        );
$obIPopUpFornecedor->setNomeVinculo         ( 'Fornecedor'            );
$obIPopUpFornecedor->setRotulo              ( 'Fornecedor'            );
$obIPopUpFornecedor->setTitle               ( 'Informe o fornecedor.' );
$obIPopUpFornecedor->setName                ( 'stNomCGM'              );
$obIPopUpFornecedor->setId                  ( 'stNomCGM'              );
$obIPopUpFornecedor->obCampoCod->setName    ( 'inCGM'                 );
$obIPopUpFornecedor->obCampoCod->setId      ( 'inCGM'                 );
$obIPopUpFornecedor->obCampoCod->setNull    ( true                    );
$obIPopUpFornecedor->setNull                ( true                    );

//Monta FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden        ( $obHdnCtrl                    );
$obFormulario->addHidden        ( $obHdnAcao                    );
$obFormulario->addTitulo        ( "Dados para filtro"           );
$obFormulario->addComponente	( $obTxtExercicio				);
$obFormulario->addComponente	( $obISelectEntidade 	 		);
$obFormulario->addComponente 	( $obTxtOrdemCompra				);
$obFormulario->addComponente    ( $obIPopUpFornecedor			);
$obFormulario->OK();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
