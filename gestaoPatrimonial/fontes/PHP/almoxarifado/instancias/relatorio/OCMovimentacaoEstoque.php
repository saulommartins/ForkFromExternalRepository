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
    * Página de Filtro para Relatório de Ïtens
    * Data de Criação   : 24/01/2006

    * @author Gelson Wolowski Gonçalves

    * @ignore

    * Casos de uso : uc-03.03.24

    $Id: OCMovimentacaoEstoque.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stCtrl = $_REQUEST['stCtrl'];

switch ($stCtrl) {

    case 'montaQuebra' :
        if ($_REQUEST['stTipoRelatorio'] == 'S') {
            $obChkGrupoAlmoxarifado = new Checkbox();
            $obChkGrupoAlmoxarifado->setRotulo( "Quebrar por" );
            $obChkGrupoAlmoxarifado->setTitle ( "Selecione uma quebra de relatório."  );
            $obChkGrupoAlmoxarifado->setName  ( "stGrupoAlmoxarifado" );
            $obChkGrupoAlmoxarifado->setLabel ( "Almoxarifado" );
            $obChkGrupoAlmoxarifado->setValue ( "almoxarifado" );

            $obChkGrupoMarca = new Checkbox();
            $obChkGrupoMarca->setRotulo( "Quebrar por" );
            $obChkGrupoMarca->setTitle ( "Selecione uma quebra de relatório."  );
            $obChkGrupoMarca->setName  ( "stGrupoMarca" );
            $obChkGrupoMarca->setLabel ( "Marca" );
            $obChkGrupoMarca->setValue ( "marca" );

            $obChkGrupoCentroCusto = new Checkbox();
            $obChkGrupoCentroCusto->setRotulo( "Quebrar por" );
            $obChkGrupoCentroCusto->setTitle ( "Selecione uma quebra de relatório."  );
            $obChkGrupoCentroCusto->setName  ( "stGrupoCentroCusto" );
            $obChkGrupoCentroCusto->setLabel ( "Centro de Custo" );
            $obChkGrupoCentroCusto->setValue ( "centrocusto" );

            $obDtSaldo = new Data();
            $obDtSaldo->setRotulo( 'Situação até'                   );
            $obDtSaldo->setTitle ( 'Selecione a data para o saldo.' );
            $obDtSaldo->setName  ( 'stDataSaldo'                    );
            $obDtSaldo->setValue ( date('d/m/Y')                    );
            $obDtSaldo->setId    ( $obDtSaldo->getName()            );
            $obDtSaldo->setNull  ( false                            );

            $obForm = new Form();
            $obFormulario = new Formulario();
            $obFormulario->addForm( $obForm );
            $obFormulario->agrupaComponentes( array( $obChkGrupoAlmoxarifado,$obChkGrupoCentroCusto,$obChkGrupoMarca ) );
            $obFormulario->addComponente( $obDtSaldo );
            $obFormulario->montaInnerHTML();
            $stJs = "jQuery('#spnQuebra').html('".$obFormulario->getHTML()."'); ";
        } else
            $stJs = "jQuery('#spnQuebra').html(''); ";

    break;

    case 'preencheDataSaldo' :
        if ($_REQUEST['stTipoRelatorio'] == 'S')
            $stJs = "jQuery('#stDataSaldo').val('".$_REQUEST['stDataFinal']."');";
    break;

}

echo $stJs;
