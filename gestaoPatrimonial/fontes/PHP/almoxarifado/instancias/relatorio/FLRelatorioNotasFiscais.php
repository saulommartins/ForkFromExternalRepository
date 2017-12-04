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
/**
 * Página de Filtro de Relatorio de Notas Fiscais
 * Data de Criação   : 13/07/2015
 * @author Analista: Luciana Dellay
 * @author Desenvolvedor: Evandro Melos
 * $Id:$
 * $Name:$
 * $Revision:$
 * $Author:$
 * $Date:$
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoNotaFiscal.class.php";
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );

$stNomeArquivo = "RelatorioNotasFiscais";
$pgGera = 'OCGeraRelatorioNotasFiscais.php';

$obForm = new Form;
$obForm->setAction ( $pgGera );
$obForm->setTarget ( 'telaPrincipal' );

//Busca almoxarifados disponiveis
$obRAlmoxarifadoNotaFiscal = new RAlmoxarifadoNotaFiscal();
$obRAlmoxarifadoNotaFiscal->buscarAlmoxarifadosDisponiveis($rsDisponiveisAlmox,$rsPermitidosAlmox);

/* Define SELECT multiplo para Almoxarifado */
$obCmbAlmoxarifado = new SelectMultiplo();
$obCmbAlmoxarifado->setName       ( 'inCodAlmoxarifado' );
$obCmbAlmoxarifado->setRotulo     ( "Almoxarifados"     );
$obCmbAlmoxarifado->setTitle      ( "Selecione os almoxarifados."     );
$obCmbAlmoxarifado->setNull       ( false );

/* Lista de atributos disponiveis */
$obCmbAlmoxarifado->setNomeLista1 ( 'inCodAlmoxarifadoDisponivel' );
$obCmbAlmoxarifado->setCampoId1   ( 'codigo'            );
$obCmbAlmoxarifado->setCampoDesc1 ( '[codigo]-[nom_a]'  );
$obCmbAlmoxarifado->setRecord1    ( $rsDisponiveisAlmox );

/* lista de atributos selecionados */
$obCmbAlmoxarifado->setNomeLista2 ( 'inCodAlmoxarifadoSelecionado' );
$obCmbAlmoxarifado->setCampoId2   ( 'codigo'                       );
$obCmbAlmoxarifado->setCampoDesc2 ( '[codigo]-[nom_a]'             );
$obCmbAlmoxarifado->setRecord2    ( $rsPermitidosAlmox             );

//Fornecedor
$obIPopUpFornecedor = new IPopUpCGMVinculado( $obForm                 );
$obIPopUpFornecedor->setTabelaVinculo       ( 'compras.nota_fiscal_fornecedor'    );
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

/*************NOTA FISCAL****************/

//Data
$obDtPeriodicidade = new Periodicidade();
$obDtPeriodicidade->setRotulo   ( "Data da Nota Fiscal" );
$obDtPeriodicidade->setExercicio( Sessao::getExercicio() );
$obDtPeriodicidade->setNull     ( false );

//Numero da Nota
$obNF = new Inteiro;
$obNF->setRotulo   ( 'Número da Nota Fiscal'   );
$obNF->setName     ( 'inNF'                    );
$obNF->setId       ( 'inNF'                    );
$obNF->setSize     ( 10                        );
$obNF->setMaxLength( 9                         );
$obNF->setTitle    ( 'Informe o Número da Nota Fiscal' );
$obNF->setNegativo ( false                     );
$obNF->setValue    ( $inNotaFiscal             );

//Numero de Serie
$obSerieNF = new TextBox;
$obSerieNF->setRotulo   ( 'Número de Série'   );
$obSerieNF->setName     ( 'stSerieNF'         );
$obSerieNF->setId       ( 'stSerieNF'         );
$obSerieNF->setSize     ( 10                  );
$obSerieNF->setMaxLength( 9                   );
$obSerieNF->setTitle    ( 'Informe a Série da Nota Fiscal' );
$obSerieNF->setValue    ( $stSerieNF          );

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addTitulo    ( "Dados para o filtro" );
$obFormulario->addComponente( $obCmbAlmoxarifado    );
$obFormulario->addComponente( $obIPopUpFornecedor   );
$obFormulario->addComponente( $obDtPeriodicidade    );
$obFormulario->addComponente( $obNF                 );
$obFormulario->addComponente( $obSerieNF            );

$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>